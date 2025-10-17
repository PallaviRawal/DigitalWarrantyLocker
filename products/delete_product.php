<?php
session_start();
include(__DIR__ . "/../includes/db.php");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // First fetch the bill file (if exists) so we can delete it
    $stmt = $conn->prepare("SELECT bill_file FROM products WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if ($product) {
        // Delete DB record
        $stmt = $conn->prepare("DELETE FROM products WHERE id=? AND user_id=?");
        $stmt->bind_param("ii", $id, $user_id);

        if ($stmt->execute()) {
            // Delete file from uploads folder if it exists
            if (!empty($product['bill_file'])) {
                $file_path = __DIR__ . "/../uploads/" . $product['bill_file'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            $_SESSION['msg'] = "Warranty deleted successfully!";
        } else {
            $_SESSION['msg'] = "Error deleting warranty!";
        }
        $stmt->close();
    } else {
        $_SESSION['msg'] = " Warranty not found or unauthorized!";
    }
} else {
    $_SESSION['msg'] = " Invalid request!";
}

// Redirect back to My Warranties page
header("Location: ../dashboard.php?page=product_list");
exit();
