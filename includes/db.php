<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session if not already started
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "warranty_locker";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
