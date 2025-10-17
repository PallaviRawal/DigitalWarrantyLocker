<?php
// Layout for admin pages
// Ensure session and $conn are already available
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        .sidebar .menu a.active { background-color: #1B3C53; }
        .logout { background-color: #c94d44; }
        .main-content { background-color: #f4f6fa; padding: 20px; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <span class="brand-text">Admin Panel</span>
        </div>
        <nav class="menu">
            <a href="#" class="active" id="manage-complaints">📋 Manage Complaints</a>
        </nav>
        <a href="./admin_logout.php" class="logout">🚪 Logout</a>

    </aside>

    <main class="main-content">
        <header class="topbar">
            <h3>Welcome, Admin 👋</h3>
            <p class="date"><?= date("l, F j, Y") ?></p>
        </header>

        <div id="dashboard-page-content">
            <?php include 'admin_panel_content.php'; ?>
        </div>
    </main>
</div>

<script>
// Optional: if you want to load/manage complaints dynamically without redirect
document.getElementById('manage-complaints').addEventListener('click', function(e) {
    e.preventDefault();
    // Currently, content is already loaded in same page
    // If you want AJAX, you can replace #dashboard-page-content innerHTML
});
</script>
</body>
</html>
