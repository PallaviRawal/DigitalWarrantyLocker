<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include(__DIR__ . "/../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

$sql = "SELECT * FROM products WHERE user_id = ? ORDER BY purchase_date DESC";
$stmt = $conn->prepare($sql);
if (!$stmt) die("Prepare failed: " . $conn->error);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<style>
/*Table Styles */
.main-content-inner {
    max-width: 1200px;
    margin: 0 auto
}

.main-content-inner h2 {
    margin-bottom: 20px;
    font-size: 28px;
    font-weight: 700;
    color: #1B3C53;
}

/* Search bar */
.search-filter-bar {
    margin-bottom: 20px;
    display: flex;
    justify-content: flex-start;
    gap: 15px;
    flex-wrap: wrap;
}

.search-filter-bar input {
    padding: 14px 10px;
    border-radius: 6px;
    border: 1px solid #ddd;
    width: 450px;
    font-size: 16.5px;
}

.search-filter-bar select {
    padding: 14px 10px;
    border-radius: 6px;
    border: 1px solid #ddd;
    cursor: pointer;
    font-size: 16.5px;
}

/* Table */
.warranty-table {
    width: 110%;
    min-width: 900px;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.warranty-table th, .warranty-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #f1f1f1;
    font-size: 16px;
}

.warranty-table th {
    background: #1B3C53;
    color: #fff;
    font-weight: 600;
}

.warranty-table tr:hover {
    background: #f5f7fa;
}

.badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    text-transform: capitalize;
}
.badge.active { background: #22a06b; }
.badge.warning { background: #f59e0b; }
.badge.expired { background: #e45858; }

/* Actions */
.actions a {
    margin-right: 8px;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    text-decoration: none;
    transition: background 0.2s ease;
}

.view-btn { background: #14b8a6; }
.view-btn:hover { background: #0d9488; }

.edit-btn { background: #fbbf24; }
.edit-btn:hover { background: #f59e0b; }

.delete-btn { background: #e45858; }
.delete-btn:hover { background: #c53030; }

/* Responsive */
@media(max-width: 1024px) {
    .warranty-table th, .warranty-table td {
        padding: 10px;
        font-size: 13px;
    }
}

@media(max-width: 768px) {
    .warranty-table, .warranty-table thead, .warranty-table tbody, .warranty-table th, .warranty-table td, .warranty-table tr {
        display: block;
    }
    .warranty-table tr {
        margin-bottom: 15px;
        border-bottom: 2px solid #f1f1f1;
    }
    .warranty-table td {
        text-align: right;
        padding-left: 50%;
        position: relative;
    }
    .warranty-table td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        width: calc(50% - 30px);
        text-align: left;
        font-weight: 600;
    }
}
</style>

<div class="main-content-inner">
    <?php
    // Show flash message if exists
    if(isset($_SESSION['msg'])) {
        echo '<div class="flash-msg">'.htmlspecialchars($_SESSION['msg']).'</div>';
        unset($_SESSION['msg']);
    }
    ?>

    <h2>My Warranties</h2>

    <div class="search-filter-bar">
        <input type="text" id="searchInput" placeholder="🔍 Search by product, brand, or retailer...">
        <select id="statusFilter">
            <option value="">All</option>
            <option value="active">Active</option>
            <option value="warning">Expiring Soon</option>
            <option value="expired">Expired</option>
        </select>
    </div>

    <?php if($result && $result->num_rows > 0): ?>
    <table class="warranty-table" id="warrantyTable">
        <thead>
            <tr>
                <th>Product</th>
                <th>Brand</th>
                <th>Purchase Date</th>
                <th>Expiry Date</th>
                <th>Price</th>
                
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
       <?php while($row = $result->fetch_assoc()):
    
    $warranty_expiry = !empty($row['warranty_expiry']) ? $row['warranty_expiry'] : null;

    $today_ts = strtotime(date('Y-m-d'));
    $status_class = "expired"; 
    $status_text = "Expired";

    if($warranty_expiry){
        $expiry_ts = strtotime($warranty_expiry);
        $in_30_ts = strtotime('+30 days', $today_ts);
        if($expiry_ts < $today_ts){ 
            $status_class="expired"; 
            $status_text="Expired"; 
        }
        elseif($expiry_ts <= $in_30_ts){ 
            $status_class="warning"; 
            $status_text="Expiring Soon"; 
        }
        else { 
            $status_class="active"; 
            $status_text="Active"; 
        }
    }
?>

            <tr data-status="<?= $status_class ?>" data-search="<?= strtolower($row['product_name'].' '.$row['brand']) ?>">
                <td data-label="Product"><?= htmlspecialchars($row['product_name']) ?></td>
                <td data-label="Brand"><?= htmlspecialchars($row['brand']) ?></td>
                <td data-label="Purchase Date"><?= htmlspecialchars($row['purchase_date']) ?></td>
                <td data-label="Expiry Date"><?= $warranty_expiry ?? 'N/A' ?></td>
                <td data-label="Price"><?= !empty($row['price']) ? '₹'.htmlspecialchars($row['price']) : '-' ?></td>
                

                <td data-label="Status"><span class="badge <?= $status_class ?>"><?= $status_text ?></span></td>
                <td data-label="Actions" class="actions">
                    <?php if(!empty($row['bill_file'])): ?>
                    <a class="view-btn" href="uploads/<?= urlencode($row['bill_file']) ?>">View</a>
                    <?php endif; ?>
                    <a class="edit-btn" href="dashboard.php?page=add_product&id=<?= (int)$row['id'] ?>">Edit</a>
                    <a class="delete-btn" href="products/delete_product.php?id=<?= (int)$row['id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p style="color:#777;">No warranties found. <a href="dashboard.php?page=add_product">Add one now</a>.</p>
    <?php endif; ?>
</div>

<div id="receiptModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <img id="receiptImg" src="" alt="Receipt" style="width:100%; max-height:80vh; object-fit:contain; display:none;">
    <iframe id="receiptFrame" style="width:100%; height:80vh; border:none; display:none;"></iframe>
    <a id="downloadBtn" href="#" download class="download-btn">⬇ Download Receipt</a>
  </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left:0; top:0;
    width:100%; height:100%;
    overflow:auto;
    background: rgba(0,0,0,0.6);
}
.modal-content {
    background: #fff;
    margin: 60px auto;
    padding: 10px;
    border-radius: 12px;
    width: 80%;
    max-width: 700px;
    position: relative;
}
.modal .close {
    position: absolute;
    top:10px; right:15px;
    font-size:28px;
    font-weight:bold;
    cursor:pointer;
}
.download-btn {
    display: inline-block;
    margin-top: 12px;
    padding: 8px 14px;
    background: #E85C50;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.2s ease;
}
.download-btn:hover {
    background: #c94d44;
}
</style>

<script>
// Search input
document.getElementById("searchInput").addEventListener("input", function(){
    let query = this.value.toLowerCase();
    document.querySelectorAll("#warrantyTable tbody tr").forEach(row=>{
        let searchData = row.dataset.search;
        row.style.display = searchData.includes(query) ? "table-row" : "none";
    });
});

// Status filter
document.getElementById("statusFilter").addEventListener("change", function(){
    let filter = this.value;
    document.querySelectorAll("#warrantyTable tbody tr").forEach(row=>{
        let status = row.dataset.status;
        row.style.display = (!filter || status === filter) ? "table-row" : "none";
    });
});

// Modal for view receipt
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function(e){
        e.preventDefault();
        const fileUrl = this.getAttribute("href");
        const modal = document.getElementById('receiptModal');
        const img = document.getElementById('receiptImg');
        const frame = document.getElementById('receiptFrame');
        const downloadBtn = document.getElementById('downloadBtn');

        // Reset views
        img.style.display = 'none';
        frame.style.display = 'none';

        if (fileUrl.match(/\.(jpg|jpeg|png|gif)$/i)) {
            img.src = fileUrl;
            img.style.display = 'block';
        } 
        else if (fileUrl.match(/\.pdf$/i)) {
            frame.src = fileUrl;
            frame.style.display = 'block';
        } 
        else {
            alert("This file type is not supported for preview, but you can download it.");
        }

        downloadBtn.href = fileUrl;
        downloadBtn.setAttribute("download", fileUrl.split('/').pop());

        modal.style.display = 'block';
    });
});

// Close modal
document.querySelector('.modal .close').addEventListener('click', function(){
    document.getElementById('receiptModal').style.display = 'none';
});

// Close on outside click
window.onclick = function(event) {
    const modal = document.getElementById('receiptModal');
    if(event.target == modal) modal.style.display = "none";
};
</script>
