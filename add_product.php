<?php
session_start();

// Get the pre-filled data from the session
$serialNumber = $_SESSION['serial_number'] ?? '';
$purchaseDate = $_SESSION['purchase_date'] ?? '';
$receiptImage = $_SESSION['receipt_image'] ?? '';

// You can add logic here to fetch user_id from a logged-in user session
$userId = 1; // Example user_id

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product - Verify Details</title>
</head>
<body>
    <h2>Verify Product Details</h2>
    <form action="save_product.php" method="post">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
        <input type="hidden" name="receipt_image" value="<?php echo htmlspecialchars($receiptImage); ?>">

        <label for="product_name">Product Name:</label><br>
        <input type="text" name="product_name" required><br><br>

        <label for="serial_number">Serial Number:</label><br>
        <input type="text" name="serial_number" value="<?php echo htmlspecialchars($serialNumber); ?>" required><br><br>

        <label for="purchase_date">Purchase Date:</label><br>
        <input type="date" name="purchase_date" value="<?php echo htmlspecialchars($purchaseDate); ?>" required><br><br>
        
        <input type="submit" value="Save Product">
    </form>
    
    <hr>
    <h3>Original Receipt Image</h3>
    <img src="<?php echo htmlspecialchars($receiptImage); ?>" alt="Receipt" style="max-width: 400px;">
    
</body>
</html>