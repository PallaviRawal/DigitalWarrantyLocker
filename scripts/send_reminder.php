<?php
session_start(); // optional if running from cron
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/email_functions.php';

// Number of days before expiry to send reminder
$daysBefore = 7;

// Fetch warranties expiring in X days
$sql = "SELECT u.id as user_id, u.email, u.first_name, p.id as product_id, p.product_name, p.warranty_expiry
        FROM products p
        JOIN users u ON p.user_id = u.id
        WHERE p.warranty_expiry = DATE_ADD(CURDATE(), INTERVAL ? DAY)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $daysBefore);
$stmt->execute();
$result = $stmt->get_result();

echo "Found " . $result->num_rows . " warranties to send reminders.\n";

while ($row = $result->fetch_assoc()) {
    $to = $row['email'];
    $name = $row['first_name'];
    $product = $row['product_name'];
    $expiry = $row['warranty_expiry'];

    $subject = "Warranty Expiry Reminder";
    $body = "Hi $name,<br>Your warranty for <b>$product</b> will expire on <b>$expiry</b>.<br>Please take action if needed.";

    if (sendEmail($to, $subject, $body)) {
        echo "Reminder sent to $to for $product\n";
        // Optional: insert into email_logs to avoid duplicates
    } else {
        echo "Failed to send reminder to $to\n";
    }
}
