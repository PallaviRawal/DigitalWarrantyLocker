<?php
session_start();
include __DIR__ . "/../includes/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

if(!isset($_POST['complaint_id'], $_POST['status'])){
    header("Location: complaint_list.php?error=Invalid+request");
    exit;
}

$complaint_id = (int)$_POST['complaint_id'];
$status = $_POST['status'];
$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE complaints SET status=?, updated_at=NOW() WHERE id=? AND user_id=?");
$stmt->bind_param("sii", $status, $complaint_id, $userId);

if($stmt->execute()){
    header("Location: complaint_list.php?success=Status+updated");
} else {
    header("Location: complaint_list.php?error=".urlencode($stmt->error));
}
$stmt->close();
