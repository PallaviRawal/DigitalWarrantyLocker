<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "includes/db.php"; // make sure this points to your db connection file

// Get logged-in user info
$user_id  = $_SESSION['user_id'] ?? 0;
$username = $_SESSION['username'] ?? 'User';

// 1. Total Products
$sql = "SELECT COUNT(*) AS total_products FROM products WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_products = $stmt->get_result()->fetch_assoc()['total_products'] ?? 0;

// 2. Active Warranties (expiry date >= today)
$sql = "SELECT COUNT(*) AS active_warranties 
        FROM products 
        WHERE user_id = ? AND warranty_expiry >= CURDATE()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$active_warranties = $stmt->get_result()->fetch_assoc()['active_warranties'] ?? 0;
// Expiring Soon (within next 30 days)
$sql = "SELECT COUNT(*) AS expiring_soon 
        FROM products 
        WHERE user_id = ? 
        AND warranty_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$expiring_soon = $stmt->get_result()->fetch_assoc()['expiring_soon'] ?? 0;

// Expired
$sql = "SELECT COUNT(*) AS expired 
        FROM products 
        WHERE user_id = ? 
        AND warranty_expiry < CURDATE()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$expired = $stmt->get_result()->fetch_assoc()['expired'] ?? 0;

// 3. Pending Complaints
$sql = "SELECT COUNT(*) AS pending_complaints 
        FROM complaints 
        WHERE user_id = ? AND status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pending_complaints = $stmt->get_result()->fetch_assoc()['pending_complaints'] ?? 0;

// 4. Recent Activity (last 5 products/complaints)
$sql = "SELECT product_name, created_at 
        FROM products 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 3";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_products = $stmt->get_result();

// 5. My Warranties List
$sql = "SELECT product_name, purchase_date, warranty_expiry, price 
        FROM products 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$warranties = $stmt->get_result();
?>

<!-- Greeting Banner -->
<section class="greeting-banner">
  <h1><?php echo htmlspecialchars($username); ?> </h1>
  <p>Here’s an overview of your warranties today.</p>
</section>

<!-- Stats Cards -->
<section class="stats">
  <div class="card"><h3>Total Products</h3><p><?php echo $total_products; ?></p></div>
  <div class="card"><h3>Active Warranties</h3><p><?php echo $active_warranties; ?></p></div>
  <div class="card"><h3>Pending Complaints</h3><p><?php echo $pending_complaints; ?></p></div>
</section>

<!-- Two Column Layout -->
<section class="content-grid">
  <div class="big-card">
    <h3>Warranty Status</h3>
    <canvas id="warrantyChart" width="800" height="300"></canvas>
  </div>
  <div class="side-card">
    <h3>Recent Activity</h3>
    <ul>
      <?php while ($row = $recent_products->fetch_assoc()): ?>
        <li>Added warranty – <?php echo htmlspecialchars($row['product_name']); ?> (<?php echo $row['created_at']; ?>)</li>
      <?php endwhile; ?>
    </ul>
  </div>
</section>

<!-- Warranty List -->
<section class="table-section">
  <h3>My Warranties</h3>
  <table class="warranty-table">
    <thead>
      <tr>
        <th>Product</th>
        <th>Start Date</th>
        <th>Expiry Date</th>
        <th>Status</th>
        <th>Value</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $warranties->fetch_assoc()): 
        $status = (strtotime($row['warranty_expiry']) >= time()) ? "Active" : "Expired";
        $badge_class = ($status === "Active") ? "active" : "expired";
      ?>
      <tr>
        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
        <td><?php echo $row['purchase_date']; ?></td>
        <td><?php echo $row['warranty_expiry']; ?></td>
        <td><span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span></td>
        <td><?php echo $row['price'] ? "$".$row['price'] : "-"; ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</section>

<canvas id="warrantyChart" width="800" height="300"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // PHP values into JS
  const active = <?php echo $active_warranties; ?>;
  const soon   = <?php echo $expiring_soon; ?>;
  const expired = <?php echo $expired; ?>;

  // Grab CSS variables from :root
  const rootStyles = getComputedStyle(document.documentElement);
  const green  = rootStyles.getPropertyValue('--green').trim();
  const amber  = rootStyles.getPropertyValue('--amber').trim();
  const red    = rootStyles.getPropertyValue('--red').trim();

  const ctx = document.getElementById('warrantyChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Active', 'Expiring Soon', 'Expired'],
      datasets: [{
        data: [active, soon, expired],
        backgroundColor: [green, amber, red],
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1 }
        }
      }
    }
  });
</script>
