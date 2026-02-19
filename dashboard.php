<?php
session_start();
include "includes/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}

$user_id    = $_SESSION['user_id'];

include 'includes/header.php';
?>
<link rel="stylesheet" href="css/dashboard.css">

<header class="topbar">
    <div class="topbar-center">
        <h3>Welcome back, <?= htmlspecialchars($_SESSION['first_name'] ?? $_SESSION['username']) ?> 👋</h3>
        <p class="date"><?= date("l, F j, Y") ?></p>
        
    </div>
     <div class="topbar-actions" style="position:relative;">
        <!-- Notification Bell -->
        <div id="notificationBell" style="cursor:pointer; font-size:24px; position:relative;">
            🔔 <span id="unreadCount" style="color:#DC3C22; font-weight:bold;"></span>
        </div>

        <!-- Notification Dropdown -->
        <div id="notificationDropdown" style="
            display:none; 
            border:1px solid #ccc; 
            padding:10px; 
            width:300px; 
            position:absolute; 
            background:#fff; 
            top:30px; 
            right:0; 
            max-height:400px; 
            overflow-y:auto;
            box-shadow:0 4px 8px rgba(0,0,0,0.1);
        ">
            <ul id="notificationsList" style="list-style:none; padding:0; margin:0;"></ul>
        </div>
    </div>

    <script>

        // notifications.js
document.addEventListener("DOMContentLoaded", function() {
    const bell = document.getElementById('notificationBell');
    const dropdown = document.getElementById('notificationDropdown');
    const list = document.getElementById('notificationsList');
    const unreadCount = document.getElementById('unreadCount');

    // Toggle dropdown
    bell.addEventListener('click', () => {
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        if(dropdown.style.display === 'block') markAsRead();
    });

   function loadNotifications() {
    fetch('get_notifications.php')
    .then(res => res.json())
    .then(data => {
        if(data.error){
            list.innerHTML = '<li style="color:red;">' + data.error + '</li>';
            unreadCount.textContent = '';
            return;
        }

        unreadCount.textContent = data.unread_count > 0 ? `(${data.unread_count})` : '';
        list.innerHTML = '';

        data.notifications.forEach(n => {
    const li = document.createElement('li');
    li.style.padding = '5px 0';

    if (n.is_read == 0) {        
        li.style.color = '#26667F'; // Blue color for unread
    } else {
        // Read notification style
        li.style.color = '#6c757d'; // Gray color for read
    }

    li.innerHTML = `${n.title}: ${n.message}`;
    list.appendChild(li);
});
    })
    .catch(err => console.error(err));
}

    function markAsRead() {
        fetch('mark_notifications_as_read.php')
        .then(() => loadNotifications());
    }

    loadNotifications();
});

        </script>
</header>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="images/eprof_logo.png" alt="eProof Logo" class="logo">
            <span class="brand-text">eProof</span>
        </div>
        <nav class="menu">
            <a href="dashboard.php?page=home" class="<?= ($_GET['page'] ?? 'home') === 'home' ? 'active' : '' ?>">📊 Dashboard</a>
            <a href="dashboard.php?page=add_product" class="<?= ($_GET['page'] ?? '') === 'add_product' ? 'active' : '' ?>">➕ Add Product</a>
            <a href="dashboard.php?page=product_list" class="<?= ($_GET['page'] ?? '') === 'product_list' ? 'active' : '' ?>">📦 My Warranties</a>
            <a href="dashboard.php?page=complaint_form" class="<?= ($_GET['page'] ?? '') === 'complaint_form' ? 'active' : '' ?>">📝 Submit Complaint</a>
            <a href="dashboard.php?page=complaint_list" class="<?= ($_GET['page'] ?? '') === 'complaint_list' ? 'active' : '' ?>">📋 Complaints</a>
            <a href="dashboard.php?page=heatmap" class="<?= ($_GET['page'] ?? '') === 'heatmap' ? 'active' : '' ?>">📊 Brand Heatmap</a>
            <a href="dashboard.php?page=ocr" class="<?= ($_GET['page'] ?? '') === 'ocr' ? 'active' : '' ?>">🖼 OCR Bill Scanner</a>
            <a href="dashboard.php?page=multi_access" class="<?= ($_GET['page'] ?? '') === 'multi_access' ? 'active' : '' ?>">👪 Multi-Access</a>
            <a href="dashboard.php?page=renewals" class="<?= ($_GET['page'] ?? '') === 'renewals' ? 'active' : '' ?>">🔄 Renew Warranties</a>
        </nav>
        <a href="auth/logout.php" class="logout">🚪 Logout</a>
    </aside>

    <main class="main-content">
        <?php
        // Flash messages
        if (isset($_SESSION['form_message'])) {
            $msg_type = $_SESSION['message_type'] ?? 'success';
            echo '<div class="message-container message-' . $msg_type . '">' . htmlspecialchars($_SESSION['form_message']) . '</div>';
            unset($_SESSION['form_message'], $_SESSION['message_type']);
        }

        // Determine page
        $page = $_GET['page'] ?? 'home';
        switch ($page) {
            case 'add_product':
                include "products/add_product.php";
                break;
            case 'product_list':
                include "products/my_warranty.php";
                break;
            case 'complaint_form':
                include "complaints/complaint_form.php";
                break;
            case 'complaint_list':
                include "complaints/complaint_list.php";
                break;
            case 'heatmap':
                include "heatmap/brand_heatmap.php";
                break;
            case 'ocr':
                include "ocr/upload_receipt.php";
                break;
            case 'multi_access':
                include "multi_access/multi_access.php";
                break;
            case 'renewals':
                include "renewals/renewals.php";
                break;
            case 'home':
            default:
                include "dashboard_home.php";
                break;
        }
        ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/dashboard.js"></script>
<?php include 'includes/footer.php'; ?>
