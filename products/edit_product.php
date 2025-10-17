<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

// Fetch product
$query = "SELECT * FROM products WHERE id = '$id' AND user_id = '{$_SESSION['user_id']}' LIMIT 1";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    $_SESSION['error'] = "Product not found.";
    header("Location: my_warranty.php");
    exit();
}

// Handle update form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name   = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category       = mysqli_real_escape_string($conn, $_POST['category']);
    $brand          = mysqli_real_escape_string($conn, $_POST['brand']);
    $purchase_date  = $_POST['purchase_date'];
    $price          = $_POST['price'] ?? NULL;
    $warranty_period= $_POST['warranty_period'];
    $warranty_unit  = $_POST['warranty_unit'];
    $notes          = mysqli_real_escape_string($conn, $_POST['notes']);

    // Update query
    $update = "UPDATE products SET 
                product_name='$product_name',
                category='$category',
                brand='$brand',
                purchase_date='$purchase_date',
                price=" . ($price ? "'$price'" : "NULL") . ",
                warranty_period='$warranty_period',
                warranty_unit='$warranty_unit',
                notes='$notes'
               WHERE id='$id' AND user_id='{$_SESSION['user_id']}'";

    if (mysqli_query($conn, $update)) {
        $_SESSION['success'] = "Product updated successfully!";
        header("Location: my_warranty.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
</head>
<body>
    <h2>Edit Product Warranty</h2>

    <form method="POST">
        <label>Product Name:</label><br>
        <input type="text" name="product_name" value="<?= $product['product_name'] ?>" required><br><br>

        <label>Category:</label><br>
        <input type="text" name="category" value="<?= $product['category'] ?>"><br><br>

        <label>Brand:</label><br>
        <input type="text" name="brand" value="<?= $product['brand'] ?>"><br><br>

        <label>Purchase Date:</label><br>
        <input type="date" name="purchase_date" value="<?= $product['purchase_date'] ?>"><br><br>

        <label>Price:</label><br>
        <input type="number" name="price" value="<?= $product['price'] ?>"><br><br>

        <label>Warranty Period:</label><br>
        <input type="number" name="warranty_period" value="<?= $product['warranty_period'] ?>" required>
        <select name="warranty_unit">
            <option value="months" <?= $product['warranty_unit']=="months"?"selected":"" ?>>Months</option>
            <option value="years" <?= $product['warranty_unit']=="years"?"selected":"" ?>>Years</option>
        </select><br><br>

        <label>Notes:</label><br>
        <textarea name="notes"><?= $product['notes'] ?></textarea><br><br>

        <button type="submit">Update Product</button>
    </form>
</body>
</html>
