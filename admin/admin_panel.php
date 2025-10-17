<?php
session_start();
include "../includes/db.php";

// If the admin_id session variable is not set, redirect to the main login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Include the layout file, which in turn includes the content
include 'admin_layout.php';
?>