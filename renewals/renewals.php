<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include(__DIR__ . "/../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

$sql = "SELECT id, product_name, brand FROM products WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>


<?php if (isset($_SESSION['msg'])): ?>
    <div style="color:green"><?= htmlspecialchars($_SESSION['msg']) ?></div>
    <?php unset($_SESSION['msg']); ?>
<?php endif; ?>

<form method="POST" action="renewals/process_renewal.php">

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="flash-msg"><?= htmlspecialchars($_SESSION['msg']) ?></div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>

    <div class="renew-wrapper">
        <!-- Left Form -->
        <div class="renew-card">
            <h2 class="renew-title">Renew Warranty</h2>
            <label for="product_id">Select Product</label>
            <select name="product_id" id="product_id" required>
                <option value="">-- Choose Product --</option>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>">
                        <?= htmlspecialchars($row['product_name']) ?> (<?= htmlspecialchars($row['brand']) ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="new_end_date">New Expiry Date</label>
            <input type="date" name="new_end_date" id="new_end_date" required>

            <button type="submit" class="renew-btn">Renew Warranty</button>
        </div>

        <!-- Right Image -->
        <div class="renew-image">
            <img src="images/renew.png" alt="Renew Warranty" />
        </div>
    </div>
</form>

<style>
.renew-title {
    text-align: center;
    margin-bottom: 20px;
    font-size: 28px;
    color: #1B3C53;
}

.flash-msg {
    text-align: center;
    background: #e6f9e6;
    border: 1px solid #22a06b;
    color: #137a4d;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-weight: 600;
}

.renew-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 80px;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

/* Card style */
.renew-card {
    flex: 1;
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

/* Right Image */
.renew-image img {
    max-width: 450px;
    height: auto;
    border-radius: 12px;
}

/* Form fields */
.renew-card label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #1B3C53;
}

.renew-card select,
.renew-card input[type="date"] {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 15px;
}

/* Button */
.renew-btn {
    width: 100%;
    background: #E85C50;
    color: white;
    border: none;
    padding: 14px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s ease;
}

.renew-btn:hover {
    background: #c94d44;
}

/* Responsive: Stack vertically */
@media(max-width: 768px) {
    .renew-wrapper {
        flex-direction: column;
        text-align: center;
    }
    .renew-image img {
        max-width: 250px;
    }
}
</style>

