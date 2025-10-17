<?php
include "includes/db.php"; // dashboard already includes header/session

$owner_id = $_SESSION['user_id'];
$message = "";

// --- Handle removal of shared member ---
if (isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM warranty_shared_members WHERE id=? AND owner_id=?");
    $stmt->bind_param("ii", $delete_id, $owner_id);
    $stmt->execute();
    $message = "Shared access removed successfully!";
}

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_email'])) {
    $member_email = trim($_POST['member_email']);
    $member_pass  = password_hash(trim($_POST['member_pass']), PASSWORD_DEFAULT);
    $product_id   = (int)$_POST['product_id'];

    // Generate a dummy phone to avoid unique constraint error
    $dummy_phone = 'temp'.time();

    // Check if member already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $member_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
    $member_id = $result->fetch_assoc()['id'];
} else {
    // Create new member with dummy phone and username
    $dummy_phone = 'temp'.time();
    $generated_username = 'member_' . time(); // Unique username

    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role, phone) VALUES (?, ?, ?, 'member', ?)");
    $stmt->bind_param("ssss", $generated_username, $member_email, $member_pass, $dummy_phone);
    $stmt->execute();
    $member_id = $stmt->insert_id;
}


    // Check if already shared
    $stmt = $conn->prepare("SELECT * FROM warranty_shared_members WHERE owner_id=? AND member_id=? AND product_id=?");
    $stmt->bind_param("iii", $owner_id, $member_id, $product_id);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows == 0) {
        // Add to shared members
        $stmt = $conn->prepare("INSERT INTO warranty_shared_members (product_id, owner_id, member_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $product_id, $owner_id, $member_id);
        $stmt->execute();
        $message = "Access shared successfully!";
    } else {
        $message = "This member already has access to this product.";
    }
}

// --- Fetch shared members ---
$shared_members = $conn->query("
    SELECT wsm.id as share_id, u.email, p.product_name as product_name
    FROM warranty_shared_members wsm
    JOIN users u ON wsm.member_id = u.id
    JOIN products p ON wsm.product_id = p.id
    WHERE wsm.owner_id = $owner_id
");
?>

<div class="multi-access-wrapper">

    <!-- Form Column -->
    <div class="form-column">
        <h2>Share Access</h2>

        <?php if($message) echo "<p class='success-msg'>$message</p>"; ?>

        <form method="POST" class="form-container">
            <div class="form-group">
                <label>Member Email:</label>
                <input type="email" name="member_email" placeholder="Enter member email" required>
            </div>

            <div class="form-group">
                <label>Set Password:</label>
                <input type="password" name="member_pass" placeholder="Set a password" required>
            </div>

            <div class="form-group">
                <label>Select Product:</label>
                <select name="product_id" required>
                    <?php
                    $products = $conn->query("SELECT * FROM products WHERE user_id = $owner_id");
                    while ($row = $products->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['product_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit">Share Access</button>
        </form>
    </div>

    <!-- Shared Members Column -->
    <div class="list-column">
    <h2>Shared Members</h2>
    <div class="members-list">
        <?php if($shared_members->num_rows == 0): ?>
            <p>No members shared yet.</p>
        <?php else: ?>
            <?php while($row = $shared_members->fetch_assoc()): ?>
            <div class="member-card">
                <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
                <p><strong>Product:</strong> 
                    <span class="product-badge"><?php echo $row['product_name']; ?></span>
                </p>
                <form method="POST" style="margin-top:10px;">
                    <input type="hidden" name="delete_id" value="<?php echo $row['share_id']; ?>">
                    <button type="submit">Remove</button>
                </form>
            </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>


</div>

<style>
/* Wrapper */
.multi-access-wrapper {
    display: flex;
    gap: 30px;
    max-width: 1200px;
    margin: 30px auto;
    font-family: Arial, sans-serif;
}

/* Left Column - Form */
.form-column {
    flex: 1;
    padding: 25px;
    background: #f7f8fa;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.form-column h2 {
    text-align: center;
    color: #1B3C53;
    margin-bottom: 20px;
}

.success-msg {
    color: green;
    text-align: center;
    font-weight: bold;
    margin-bottom: 15px;
}

/* Form styles */
.form-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.form-group label {
    font-weight: bold;
    margin-bottom: 5px;
    color: #1B3C53;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 12px;
    border-radius: 7px;
    border: 1px solid #ccc;
    font-size: 14px;
}

/* Button theme colors */
.form-container button {
    width: 100%;
    padding: 14px;
    background: #E85C50;
    color: white;
    border: none;
    border-radius: 7px;
    cursor: pointer;
    font-size: 15px;
    font-weight: bold;
    transition: 0.3s;
}

.form-container button:hover {
    background: #c94d44;
}

/* Right Column - Shared Members */
.list-column {
    flex: 1;
    padding: 25px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    overflow-x: auto;
}

.list-column h2 {
    text-align: center;
    color: #1B3C53;
    margin-bottom: 20px;
}

.members-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.member-card {
    background-color: #FFEDE9; /* soft orange-red accent */
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.member-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.member-card p {
    margin: 6px 0;
    color: #333;
    font-size: 15px;
}

.product-badge {
    background-color: #1B3C53; /* dark blue accent */
    color: white;
    padding: 3px 10px;
    border-radius: 15px;
    font-size: 13px;
    font-weight: bold;
}

.member-card button {
    padding: 6px 12px;
    background-color: #E85C50;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
}

.member-card button:hover {
    background-color: #c94d44;
}

</style>