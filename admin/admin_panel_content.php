<?php
// This file is now included in admin_layout.php
// The session and database connection are already handled.
if (!isset($conn)) {
    include "../includes/db.php";
}
$message = '';
$message_type = '';

if (isset($_POST['update_status'])) {
    $complaintId = intval($_POST['complaint_id']);
    $newStatus = $_POST['status'];
    
    // Validate the new status
    $allowedStatuses = ['Pending', 'In Progress', 'Resolved', 'Not Resolved'];
    if (in_array($newStatus, $allowedStatuses)) {
        $updateStmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
        $updateStmt->bind_param("si", $newStatus, $complaintId);
        
        if ($updateStmt->execute()) {
            $message = 'Status updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to update status.';
            $message_type = 'error';
        }
        $updateStmt->close();
    } else {
        $message = 'Invalid status.';
        $message_type = 'error';
    }
}

// Fetch all complaints (including attachments)
$stmt = $conn->prepare("
    SELECT c.id, c.title, c.description, c.priority, c.status, c.created_at, c.updated_at,
           c.attachment_image, c.attachment_audio,
           p.product_name, u.username 
    FROM complaints c 
    JOIN products p ON c.product_id = p.id 
    JOIN users u ON c.user_id = u.id 
    ORDER BY c.created_at DESC
");
$stmt->execute();
$complaints = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<style>
    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background: #1B3C53;
        color: #fff;
    }
    tr:hover { background: #f1f1f1; }
    select { padding: 5px; border-radius: 3px; }
    button { padding: 5px 10px; background: #22a06b; color: #fff; border: none; border-radius: 3px; cursor: pointer; }
    .message-container { text-align: center; margin-bottom: 20px; padding: 10px; border-radius: 5px; }
    .message-success { background: #d4edda; color: #155724; }
    .message-error { background: #f8d7da; color: #721c24; }
    img { border-radius: 5px; object-fit: cover; transition: 0.3s ease; }
    img:hover { transform: scale(1.6); }
    audio { outline: none; }
</style>

<h2>Manage Complaints</h2>
<?php if ($message): ?>
    <div class="message-container message-<?= $message_type ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Product</th>
            <th>Title</th>
            <th>Description</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Image</th>
            <th>Audio</th>
            <th>Created</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($complaints): foreach ($complaints as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['username']) ?></td>
            <td><?= htmlspecialchars($c['product_name']) ?></td>
            <td><?= htmlspecialchars($c['title']) ?></td>
            <td><?= htmlspecialchars($c['description']) ?></td>
            <td><?= htmlspecialchars($c['priority']) ?></td>
            <td><?= htmlspecialchars($c['status']) ?></td>

            <!-- Show Image -->
            <td>
                <?php if (!empty($c['attachment_image'])): ?>
                    <a href="../<?= htmlspecialchars($c['attachment_image']) ?>" target="_blank">
                        <img src="../<?= htmlspecialchars($c['attachment_image']) ?>" width="60" height="60">
                    </a>
                <?php else: ?>
                    <em>No image</em>
                <?php endif; ?>
            </td>

            <!-- Show Audio -->
            <td>
    <?php if (!empty($c['attachment_audio'])): ?>
        <?php 
            // adjust path depending on where this file is located
            $audioPath = (file_exists("../" . $c['attachment_audio'])) 
                ? "../" . $c['attachment_audio'] 
                : $c['attachment_audio']; 
        ?>
        <audio controls style="width:150px;">
            <source src="<?= htmlspecialchars($audioPath) ?>" type="audio/webm">
            Your browser does not support the audio element.
        </audio>
    <?php else: ?>
        <em>No audio</em>
    <?php endif; ?>
</td>


            <td><?= $c['created_at'] ?></td>
            <td>
                <form method="POST" action="admin_panel.php">
                    <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                    <input type="hidden" name="update_status" value="1">
                    <select name="status">
                        <option value="Pending" <?= $c['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="In Progress" <?= $c['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="Resolved" <?= $c['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                        <option value="Not Resolved" <?= $c['status'] == 'Not Resolved' ? 'selected' : '' ?>>Not Resolved</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="11" style="text-align:center;">No complaints found</td></tr>
        <?php endif; ?>
    </tbody>
</table>
