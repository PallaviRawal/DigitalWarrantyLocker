<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include(__DIR__ . "/../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$readonly = false;

$edit_data = null;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt2 = $conn->prepare("SELECT * FROM products WHERE id=? AND user_id=?");
    $stmt2->bind_param("ii", $id, $user_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $edit_data = $result2->fetch_assoc();
    $stmt2->close();
}

$ocr_data = ($_SESSION['ocr_data'] ?? null);
if ($ocr_data && !$edit_data) {
    $edit_data = array_merge([
        "product_name"      => "",
        "category"          => "",
        "brand"             => "",
        "serial_number"     => "",
        "purchase_date"     => "",
        "price"             => "",
        "warranty_period"   => "",
        "warranty_unit"     => "months",
        "bill_file"         => "",
    ], $ocr_data);
    
}
?>

<div class="add-product-container">
    <div class="form-container">
        <h2><?= $edit_data ? "Edit Product Warranty" : "Add New Product Warranty" ?></h2>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="message success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php elseif (isset($_SESSION['error'])): ?>
            <p class="message error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form action="products/upload_product.php" method="POST" enctype="multipart/form-data">
            <?php if($edit_data && isset($edit_data['id'])): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <?php endif; ?>

            <label>Product Name *</label>
            <input type="text" name="product_name" required 
                   value="<?= htmlspecialchars($edit_data['product_name'] ?? '') ?>"
                   <?= $readonly ? "readonly" : "" ?>>

            <label>Category *</label>
            <select name="category" required <?= $readonly ? "disabled" : "" ?>>
                <option value="">Select Category</option>
                <?php
                $categories = ['Electronics', 'Appliances', 'Clothing', 'Furniture', 'Other'];
                foreach($categories as $cat){
                    $selected = ($edit_data['category'] ?? '') === $cat ? 'selected' : '';
                    echo "<option $selected>$cat</option>";
                }
                ?>
            </select>

            <label>Brand/Manufacturer *</label>
            <input type="text" name="brand" required 
                   value="<?= htmlspecialchars($edit_data['brand'] ?? '') ?>"
                   <?= $readonly ? "readonly" : "" ?>>

            <label>Serial Number (Optional)</label>
            <input type="text" name="serial_number" 
                   value="<?= htmlspecialchars($edit_data['serial_number'] ?? '') ?>"
                   <?= $readonly ? "readonly" : "" ?>>

            <label>Purchase Date *</label>
            <input type="date" name="purchase_date" required 
                   value="<?= htmlspecialchars($edit_data['purchase_date'] ?? '') ?>"
                   <?= $readonly ? "readonly" : "" ?>>

            <label>Purchase Price (Optional)</label>
            <input type="number" step="0.01" name="price" 
                   value="<?= htmlspecialchars($edit_data['price'] ?? '') ?>"
                   <?= $readonly ? "readonly" : "" ?>>

            <label>Warranty Period *</label>
            <div style="display:flex; gap:10px;">
                <input type="number" name="warranty_period" min="1" required 
                       value="<?= htmlspecialchars($edit_data['warranty_period'] ?? 1) ?>"
                       <?= $readonly ? "readonly" : "" ?>>
                <select name="warranty_unit" <?= $readonly ? "disabled" : "" ?>>
                    <option value="months" <?= ($edit_data['warranty_unit'] ?? '') === 'months' ? 'selected' : '' ?>>Months</option>
                    <option value="years" <?= ($edit_data['warranty_unit'] ?? '') === 'years' ? 'selected' : '' ?>>Years</option>
                </select>
            </div>

            <label>Upload Receipt (Optional)</label>
            <?php if(!$readonly): ?>
                <input type="file" name="bill_file" accept=".jpg,.jpeg,.png,.pdf">

                <?php if (isset($_SESSION['ocr_data']['bill_file'])): ?>
                <input type="hidden" name="ocr_bill_file" value="<?php echo htmlspecialchars($_SESSION['ocr_data']['bill_file']); ?>">
                <?php endif; ?>
                
            <?php endif; ?>

            <?php if(!empty($edit_data['bill_file'])): ?>
                <p>Current file: 
                    <a href="../uploads/<?= urlencode($edit_data['bill_file']) ?>" target="_blank">
                        <?= htmlspecialchars($edit_data['bill_file']) ?>
                    </a>
                </p>
            <?php endif; ?>

            <?php if(!$readonly): ?>
                <button type="submit" class="btn"><?= $edit_data && isset($edit_data['id']) ? "Update Product" : "Save Product" ?></button>
            <?php else: ?>
                <p style="color:#777; margin-top:10px;">You are viewing owner’s warranty. Editing is disabled.</p>
            <?php endif; ?>
        </form>
    </div>
</div>


<style>
.add-product-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 30px 0;
}
.form-container {
    width: 100%;
    max-width: 800px;
    background: #fff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    margin: 0 auto;
}
.form-container h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #1B3C53;
    font-size: 26px;
}
.form-container label {
    font-weight: 600;
    display: block;
    margin: 12px 0 6px;
    color: #1B3C53;
}
.form-container input,
.form-container select,
.form-container textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 14px;
    background: <?= $readonly ? "#f5f5f5" : "#fff" ?>;
}
.form-container .btn {
    margin-top: 20px;
    width: 100%;
    padding: 16px;
    border: none;
    border-radius: 10px;
    background: #1B3C53 ;
    color: white;
    font-size: 16px;
    cursor: pointer;
}
.form-container .btn:hover {
    background: #2e6084ff;
}
.message {
    text-align: center;
    margin-bottom: 15px;
    font-weight: bold;
}
.success { color: green; }
.error { color: red; }a
</style>
