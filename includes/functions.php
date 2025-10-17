<?php
include 'db.php';

function get_notifications($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function count_unread($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
}

function send_email($to, $subject, $body) {
    // simple mail, you can replace with PHPMailer for better delivery
    mail($to, $subject, $body);
}
