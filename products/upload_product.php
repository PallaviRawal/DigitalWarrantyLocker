<?php
session_start();
include(__DIR__ . "/../includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $product_name = trim($_POST['product_name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $serial_number = trim($_POST['serial_number'] ?? '');
    $purchase_date = $_POST['purchase_date'] ?? null;
    $price = $_POST['price'] !== '' ? (float)$_POST['price'] : null;
    $warranty_period = (int)($_POST['warranty_period'] ?? 0);
    $warranty_unit = $_POST['warranty_unit'] ?? 'months';
    $notes = trim($_POST['notes'] ?? '');

    $warranty_expiry = null;
    if ($purchase_date && $warranty_period > 0) {
        $endDate = new DateTime($purchase_date);
        $intervalUnit = ($warranty_unit === 'years') ? 'Y' : 'M';
        $endDate->add(new DateInterval('P' . $warranty_period . $intervalUnit));
        $warranty_expiry = $endDate->format('Y-m-d');
    }

   $bill_file = null;

// Case 1: A new file was uploaded 
if (!empty($_FILES['bill_file']['name'])) {
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $_FILES['bill_file']['tmp_name']);
    finfo_close($file_info);

    // Validate MIME type
    $allowed_mime_types = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!in_array($mime_type, $allowed_mime_types)) {
        $_SESSION['msg'] = "Error: Invalid file type. Only JPG, PNG, and PDF are allowed.";
        header("Location: ../dashboard.php?page=add_product");
        exit();
    }

    $target_dir = __DIR__ . "/../uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($_FILES["bill_file"]["name"]));
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($_FILES["bill_file"]["tmp_name"], $target_file)) {
        $bill_file = $filename;
    } else {
        $_SESSION['msg'] = "Error: Failed to move uploaded file.";
        header("Location: ../dashboard.php?page=add_product");
        exit();
    }
} 
// Case 2: OCR upload or edit without new file
else if (isset($_POST['ocr_bill_file'])) { 
    $bill_file = $_POST['ocr_bill_file'];
}
else if (!empty($_POST['id'])) {
    $stmt_old_file = $conn->prepare("SELECT bill_file FROM products WHERE id=? AND user_id=?");
    $stmt_old_file->bind_param("ii", $_POST['id'], $user_id);
    $stmt_old_file->execute();
    $old_file_result = $stmt_old_file->get_result()->fetch_assoc();
    $stmt_old_file->close();
    $bill_file = $old_file_result['bill_file'] ?? null;
}

    $is_edit = !empty($_POST['id']);
    $id = $is_edit ? (int)$_POST['id'] : 0;

    if ($is_edit) {
        if (!empty($bill_file) && isset($_FILES['bill_file']['name']) && !empty($_FILES['bill_file']['name'])) {
            $stmt = $conn->prepare("SELECT bill_file FROM products WHERE id=? AND user_id=?");
            $stmt->bind_param("ii", $id, $user_id);
            $stmt->execute();
            $old = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($old && !empty($old['bill_file'])) {
                $old_path = __DIR__ . "/../uploads/" . $old['bill_file'];
                if (file_exists($old_path)) unlink($old_path);
            }
        }

        $query = "UPDATE products 
                  SET product_name=?, category=?, brand=?, serial_number=?, purchase_date=?, price=?, warranty_period=?, warranty_unit=?, notes=?, warranty_expiry=?, updated_at=NOW()";
        if ($bill_file) $query .= ", bill_file=?";
        $query .= " WHERE id=? AND user_id=?";

        $stmt = $conn->prepare($query);

        if ($bill_file) {
            $stmt->bind_param(
                "sssssdissssii",
                $product_name, $category, $brand, $serial_number, $purchase_date, $price,
                $warranty_period, $warranty_unit, $notes, $warranty_expiry, $bill_file, $id, $user_id
            );
        } else {
            $stmt->bind_param(
                "sssssdisssii",
                $product_name, $category, $brand, $serial_number, $purchase_date, $price,
                $warranty_period, $warranty_unit, $notes, $warranty_expiry, $id, $user_id
            );
        }

        $success = $stmt->execute();
        $stmt->close();
        $msg = $success ? "Warranty updated successfully!" : "Error updating warranty!";

    } else {
        $stmt = $conn->prepare("INSERT INTO products 
            (user_id, product_name, category, brand, serial_number, purchase_date, price, warranty_period, warranty_unit, warranty_expiry, bill_file, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssisssss", 
            $user_id, $product_name, $category, $brand, $serial_number, $purchase_date, $price, 
            $warranty_period, $warranty_unit, $warranty_expiry, $bill_file, $notes
        );

        $success = $stmt->execute();
        $stmt->close();
        $msg = $success ? "Product added successfully!" : "Error adding product!";
    }

    
    unset($_SESSION['ocr_data']);

    $_SESSION['msg'] = $msg;
    header("Location: ../dashboard.php?page=product_list");
    exit();
}
?>
