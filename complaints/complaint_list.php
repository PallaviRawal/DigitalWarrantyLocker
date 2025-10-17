<?php
// complaints/complaint_list.php

include __DIR__ . "/../includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Fetch complaints for this user (without image)
$stmt = $conn->prepare("
    SELECT c.id, c.title, c.description, c.priority, c.status, 
           c.created_at, c.updated_at, c.attachment_audio,
           p.product_name
    FROM complaints c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ? 
    ORDER BY c.created_at DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$complaints = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Complaints</title>
<style>
body { font-family: Arial, sans-serif; background: #f4f6fa; padding: 20px; }
h2 { text-align: center; color: #1B3C53; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
th, td { padding: 14.5px; text-align: left; border-bottom: 1px solid #ddd; vertical-align: middle; }
th { background: #1B3C53; color: #fff; }
tr:hover { background: #f1f1f1; }
.status-Pending { color: #e45858; font-weight: bold; }
.status-InProgress { color: #f59e0b; font-weight: bold; }
.status-Resolved { color: #22a06b; font-weight: bold; }
.status-NotResolved { color: #e45858; font-weight: bold; }

/* Buttons */
.btn {
  background-color: #14b8a6;
  color: #fff;
  border: none;
  padding: 6px 10px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 13px;
}
.btn:hover { background-color: #0d9488; }

/* Modal */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.6);
}
.modal-content {
  background-color: #fff;
  margin: 8% auto;
  padding: 15px;
  border-radius: 10px;
  width: 320px;
  text-align: center;
}
.modal-content audio { width: 100%; }
.close {
  float: right;
  font-size: 22px;
  cursor: pointer;
  color: #666;
}
.close:hover { color: #000; }
</style>
</head>
<body>
<h2>My Complaints</h2>

<?php if (!empty($message)): ?>
<div style="background: <?= $message_type === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $message_type === 'success' ? '#155724' : '#721c24' ?>; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 20px;">
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<table>
<thead>
<tr>
<th>ID</th>
<th>Product</th>
<th>Title</th>
<th>Description</th>
<th>Priority</th>
<th>Status</th>
<th>Audio</th>
<th>Created</th>
<th>Last Update</th>
</tr>
</thead>
<tbody>
<?php if ($complaints): foreach ($complaints as $c): ?>
<tr>
<td><?= $c['id'] ?></td>
<td><?= htmlspecialchars($c['product_name']) ?></td>
<td><?= htmlspecialchars($c['title']) ?></td>
<td><?= htmlspecialchars($c['description']) ?></td>
<td><?= htmlspecialchars($c['priority']) ?></td>
<td class="status-<?= str_replace(' ', '', $c['status']) ?>"><?= htmlspecialchars($c['status']) ?></td>

<!-- Audio Button -->
<td>
  <?php if (!empty($c['attachment_audio'])): ?>
   <button class="btn" onclick="openAudioModal('<?= htmlspecialchars($c['attachment_audio']) ?>')">🎧 Play</button>
  <?php else: ?>
    -
  <?php endif; ?>
</td>

<td><?= $c['created_at'] ?></td>
<td><?= $c['updated_at'] ?? '-' ?></td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="9" style="text-align:center;">No complaints found</td></tr>
<?php endif; ?>
</tbody>
</table>

<!-- 🔹 Audio Modal -->
<div id="audioModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('audioModal')">&times;</span>
    <h3>Complaint Audio</h3>
    <audio id="modalAudio" controls></audio>
  </div>
</div>

<script>
function openAudioModal(src) {
  const player = document.getElementById("modalAudio");
  player.src = src;
  player.play();
  document.getElementById("audioModal").style.display = "block";
}

function closeModal(id) {
  const modal = document.getElementById(id);
  modal.style.display = "none";

  if (id === 'audioModal') {
    document.getElementById('modalAudio').pause();
  }
}

window.onclick = function(event) {
  if (event.target.classList.contains('modal')) {
    event.target.style.display = "none";
    document.getElementById('modalAudio').pause();
  }
};
</script>

</body>
</html>
