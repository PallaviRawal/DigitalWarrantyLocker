<?php
session_start();
require_once __DIR__ . "/includes/db.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$owner_id = $_SESSION['owner_id'] ?? $user_id;

$notifications = [];
$unread_count = 0;
$today = date('Y-m-d');
$thirty_days_later = date('Y-m-d', strtotime('+30 days'));

$stmt = $conn->prepare("SELECT product_name, warranty_expiry FROM products WHERE user_id = ? AND warranty_expiry BETWEEN ? AND ? ORDER BY warranty_expiry ASC");
$stmt->bind_param("iss", $owner_id, $today, $thirty_days_later);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $expiry_date = new DateTime($row['warranty_expiry']);
    $today_date = new DateTime($today);
    $diff = $today_date->diff($expiry_date);
    $days_left = $diff->days;

    $notifications[] = [
        "id" => null,
        "title" => "Warranty Expiry Alert",
        "message" => "Your " . htmlspecialchars($row['product_name']) . " warranty is expiring in " . $days_left . " days.",
        "is_read" => 0,
        "created_at" => date('Y-m-d H:i:s')
    ];
    $unread_count++;
}

echo json_encode([
    "unread_count" => $unread_count,
    "notifications" => $notifications
]);
?>