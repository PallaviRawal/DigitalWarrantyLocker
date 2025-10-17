<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];

// Mark all notifications as read
$stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
?>
