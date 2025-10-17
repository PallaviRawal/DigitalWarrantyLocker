<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . "/../includes/db.php";

$successMsg = $errorMsg = "";

// --- Handle form submit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId     = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    $title      = trim($_POST['title']);
    $description= trim($_POST['description']);
    $priority   = $_POST['priority'] ?? 'Medium';

    // Upload dir
    $uploadDir = __DIR__ . "/../uploads/complaints/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $attachment_image = null;
    if (!empty($_FILES['attachment_image']['name'])) {
        $fileExtension = pathinfo($_FILES['attachment_image']['name'], PATHINFO_EXTENSION);
        $newFileName   = uniqid() . '.' . $fileExtension;
        if (move_uploaded_file($_FILES['attachment_image']['tmp_name'], $uploadDir . $newFileName)) {
            $attachment_image = "uploads/complaints/" . $newFileName;
        }
    }

    $attachment_audio = null;
   if (!empty($_POST['recorded_audio'])) {
    $audioData = $_POST['recorded_audio'];
    $audioData = str_replace('data:audio/webm;base64,', '', $audioData);
    $audioData = str_replace(' ', '+', $audioData);
    $decodedData = base64_decode($audioData);

    $newFileName = uniqid() . ".webm";
    $filePath = $uploadDir . $newFileName;
    file_put_contents($filePath, $decodedData);

    $attachment_audio = "uploads/complaints/" . $newFileName;
}

    // Insert complaint
    $sql = "INSERT INTO complaints (user_id, product_id, title, description, priority, attachment_image, attachment_audio) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("iisssss", $userId, $product_id, $title, $description, $priority, $attachment_image, $attachment_audio);
        if ($stmt->execute()) {
            $successMsg = "Complaint submitted successfully!";
        } else {
            $errorMsg = "Error submitting complaint: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMsg = "Database error: " . $conn->error;
    }
}

// Fetch products for dropdown
$userId = $_SESSION['user_id'];
$query = $conn->prepare("SELECT id, product_name FROM products WHERE user_id = ?");
$query->bind_param("i", $userId);
$query->execute();
$products = $query->get_result()->fetch_all(MYSQLI_ASSOC);
$query->close();
?>
<style>
.complaint-container { max-width: 800px; margin: 40px auto; padding: 40px; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
.complaint-container h2 { text-align: center; color: #1B3C53; margin-bottom: 20px; }
.complaint-container form label { display: block; margin-top: 15px; font-weight: bold; color: #555; }
#recorder-controls button:not(:last-child) {
    margin-right: 10px;
}
.complaint-container form input, .complaint-container form select, .complaint-container form textarea { 
  width: 100%; padding: 12px; margin: 5px 0 15px; 
  border: 1px solid #ccc; border-radius: 6px; 
  font-size: 14px; box-sizing: border-box; 
}

#recorder-controls {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-top: 10px;
  margin-bottom: 10px;
  padding: 15px;
  background-color: #f8f9fb;
  border: 1px solid #ddd;
  border-radius: 8px;
  flex-wrap: wrap; /* allows elements to go next line if needed */
}

#recorder-controls button {
  padding: 10px 18px;
  font-size: 15px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  transition: all 0.3s ease;
}

/* 🎙️ Start Button */
#startRecord {
  background-color: #28a745;
  color: white;
}
#startRecord:hover {
  background-color: #218838;
}

/* ⏹️ Stop Button */
#stopRecord {
  background-color: #dc3545;
  color: white;
}
#stopRecord:hover {
  background-color: #b52a37;
}

#audioPreview {
  display: block;
  margin-top: 12px;
  width: 100%;
  max-width: 100%;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.complaint-container form button { background: #1B3C53; color: #fff; padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; transition: background-color 0.3s; }
.complaint-container form button:hover { background: #2e6084; }
.message { text-align:center; font-weight:bold; margin-bottom:15px; }
.success { color: green; }
.error { color: red; }
</style>

<div class="complaint-container">
    <h2>Submit a Complaint</h2>

    <!-- Show success/error message -->
    <?php if ($successMsg): ?>
        <p class="message success"><?= htmlspecialchars($successMsg) ?></p>
    <?php elseif ($errorMsg): ?>
        <p class="message error"><?= htmlspecialchars($errorMsg) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="product_id">Select Product</label>
        <select name="product_id" id="product_id" required>
            <option value="">-- Select Product --</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= htmlspecialchars($p['id']) ?>"><?= htmlspecialchars($p['product_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="title">Complaint Title</label>
        <input type="text" name="title" id="title" placeholder="Brief title" required>

        <label for="description">Complaint Description</label>
        <textarea name="description" id="description" rows="5" placeholder="Describe your issue..." required></textarea>

        <label for="priority">Priority</label>
        <select name="priority" id="priority" required>
            <option value="Low">Low</option>
            <option value="Medium" selected>Medium</option>
            <option value="High">High</option>
        </select>

        <label for="attachment_image">Attach Image (optional)</label>
        <input type="file" name="attachment_image" id="attachment_image" accept="image/*">

        <label for="attachment_audio">Record Voice (optional)</label>
<div id="recorder-controls">
  <button type="button" id="startRecord"> Start Recording</button>
  <button type="button" id="stopRecord" disabled>Stop Recording</button>
</div>

<!-- Preview will stay below -->
<audio id="audioPreview" controls style="display:none;"></audio><br>
<input type="hidden" name="recorded_audio" id="recorded_audio">
        <button type="submit">Submit Complaint</button>
    </form>
</div>

<!-- Audio recording script-->
<script>
(() => {
  const startBtn = document.getElementById('startRecord');
  const stopBtn  = document.getElementById('stopRecord');
  const preview   = document.getElementById('audioPreview');
  const hiddenInp = document.getElementById('recorded_audio');
  const formEl    = document.querySelector('form');

  let mediaRecorder = null;
  let audioChunks = [];
  let currentStream = null;

  // Prevent form submission while recording
  formEl.addEventListener('submit', (e) => {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
      e.preventDefault();
      alert('Please stop recording before submitting the form.');
    }
  });

  async function startRecording() {
    // Clear previous
    hiddenInp.value = "";
    preview.src = "";
    preview.style.display = 'none';

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      alert('Recording is not supported by your browser.');
      return;
    }

    try {
      const devices = await navigator.mediaDevices.enumerateDevices();
console.log("Available Devices:", devices);

const mic = devices.find(d => d.kind === "audioinput");
if (!mic) {
    alert("No microphone detected!");
    return;
}

currentStream = await navigator.mediaDevices.getUserMedia({
    audio: {
        deviceId: mic.deviceId, // Force first mic
        channelCount: 2,        // Stereo for safety
        echoCancellation: true,
        noiseSuppression: true
    }
});

    } catch (err) {
      console.error('Microphone access denied or error:', err);
      alert('Cannot access microphone. Please allow microphone permission.');
      return;
    }

    try {
      audioChunks = [];
      mediaRecorder = new MediaRecorder(currentStream);

      mediaRecorder.ondataavailable = e => {
        if (e.data && e.data.size > 0) audioChunks.push(e.data);
      };

      mediaRecorder.onstop = () => {
        // AFTER: Use a simpler WebM MIME type
        const mimeType = 'audio/webm';
        const audioBlob = new Blob(audioChunks, { type: mimeType });
        const audioUrl = URL.createObjectURL(audioBlob);
        preview.src = audioUrl;
        preview.style.display = 'block';

        // convert to base64
        const reader = new FileReader();
        reader.onloadend = () => {
          hiddenInp.value = reader.result;
        };
        reader.readAsDataURL(audioBlob);

        // stop and release tracks
        if (currentStream) {
          currentStream.getTracks().forEach(t => t.stop());
          currentStream = null;
        }
      };

      mediaRecorder.start();
      startBtn.disabled = true;
      stopBtn.disabled = false;
    } catch (err) {
      console.error('MediaRecorder error:', err);
      alert('Recording failed: ' + (err.message || err));
    }
  }

  function stopRecording() {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
      mediaRecorder.stop();
    }
    startBtn.disabled = false;
    stopBtn.disabled = true;
  }

  startBtn.addEventListener('click', () => startRecording());
  stopBtn.addEventListener('click', () => stopRecording());
})();
</script>

