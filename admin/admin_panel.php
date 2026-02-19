<?php
session_start();
include "../includes/db.php";


if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

include 'admin_layout.php';
?>
