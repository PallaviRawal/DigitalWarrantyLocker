<?php
session_start();
include(__DIR__ . "/../includes/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }

    $userId    = $_SESSION['user_id'];
    $productId = intval($_POST['product_id']);
    $newExpiry = $_POST['new_end_date'] ?? null;

    if (empty($productId) || empty($newExpiry)) {
        $_SESSION['msg'] = "Please select a product and enter a new expiry date.";
        header("Location: ../dashboard.php?page=renew_warranty");
        exit();
    }

    // Update warranty_expiry only
    $sql = "UPDATE products SET warranty_expiry = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $newExpiry, $productId, $userId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['msg'] = "Warranty renewed successfully! New expiry date set.";
        } else {
            $_SESSION['msg'] = "No warranty updated. (Check if the product belongs to you or expiry date is same as before)";
        }
    } else {
        $_SESSION['msg'] = "Database error: " . $stmt->error;
    }

    $stmt->close();
    header("Location: ../dashboard.php?page=product_list");
    exit();
}
