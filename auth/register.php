<?php
session_start();
require_once '../includes/db.php';
include __DIR__ . '/../scripts/email_functions.php';

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first    = trim($_POST['first_name'] ?? '');
    $last     = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $pass     = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validation
    if ($first === '' || !preg_match("/^[A-Za-z]+$/", $first)) {
        $errors[] = "First name is required and must contain only letters.";
    }
    if ($last === '' || !preg_match("/^[A-Za-z]+$/", $last)) {
        $errors[] = "Last name is required and must contain only letters.";
    }
    if ($username === '' || !preg_match("/^[A-Za-z0-9_]{3,20}$/", $username)) {
        $errors[] = "Username is required (3–20 chars, only letters, numbers, underscores).";
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if ($phone === '' || !preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number is required and must be exactly 10 digits.";
    }
    if ($pass === '') {
        $errors[] = "Password is required.";
    } elseif (strlen($pass) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }
    if ($confirm === '') {
        $errors[] = "Confirm password is required.";
    } elseif ($pass !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Uniqueness check
    if (!$errors) {
        $check = $conn->prepare("SELECT id FROM users WHERE username=? OR email=? OR phone=? LIMIT 1");
        $check->bind_param("sss", $username, $email, $phone);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors[] = "Username, Email or Phone already exists.";
        }
        $check->close();
    }

    // Insert if no errors
    if (!$errors) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, phone, password_hash, created_at, updated_at)
            VALUES (?,?,?,?,?,?,NOW(),NOW())");
        $stmt->bind_param("ssssss", $first, $last, $username, $email, $phone, $hash);
        if ($stmt->execute()) {
            $success = "Account created! You can login now.";
            $subject = "Welcome to Warranty Locker!";
            $body    = "Hi $first,<br>Welcome to Warranty Locker!";
            sendEmail($email, $subject, $body);
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Create Account • eprof</title>
  <link rel="stylesheet" href="../css/register_style.css">
</head>
<body class="auth-bg">

  <main class="auth-shell">
    <!-- LEFT: info / banner -->
    <section class="panel-info">
    <!-- Hero Image -->
    <div class="info-hero">
        <img src="../images/logo.png" alt="Digital Warranty" class="hero-image">
    </div>

    <!-- Info List with Icons -->
    <ul class="info-list">
        <li>
            <span class="i i-shield"></span>
            <div>
                <h4>Bank-Level Security</h4>
                <p>Your documents are encrypted and protected</p>
            </div>
        </li>
        <li>
            <span class="i i-phone"></span>
            <div>
                <h4>Access Anywhere</h4>
                <p>Your warranties available on any device</p>
            </div>
        </li>
        <li>
            <span class="i i-bell"></span>
            <div>
                <h4>Smart Reminders</h4>
                <p>Get notified before warranties expire</p>
            </div>
        </li>
    </ul>
</section>

    <!-- RIGHT: form -->
    <section class="panel-form card">
      <header class="form-header">
        <h2><span class="brand">Digital Warranty</span> <span class="accent">Locker</span></h2>
        <p>Create your secure account</p>
      </header>

      <?php if ($success): ?>
        <div class="alert success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if ($errors): ?>
        <div class="alert error">
          <ul><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul>
        </div>
      <?php endif; ?>

      <form method="post" class="form">
        <input type="hidden" name="csrf" value="<?= isset($_SESSION['csrf']) ? htmlspecialchars($_SESSION['csrf']) : '' ?>">

        <div class="grid-2">
          <div class="field with-icon">
            <span class="i i-user"></span>
            <input type="text" name="first_name" placeholder="First Name"
                   pattern="[A-Za-z]+" title="Only letters allowed"
                   value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
          </div>
          <div class="field with-icon">
            <span class="i i-user"></span>
            <input type="text" name="last_name" placeholder="Last Name"
                   pattern="[A-Za-z]+" title="Only letters allowed"
                   value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required><br><br>
          </div>
          <div class="field with-icon">
            <span class="i i-user"></span>
            <input type="text" name="username" placeholder="Username"
                   pattern="[A-Za-z0-9_]{3,20}" title="3–20 chars, only letters, numbers, underscores"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required><br><br>
          </div>
        </div>

        <div class="field with-icon">
          <span class="i i-mail"></span>
          <input type="email" name="email" placeholder="Email Address"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required><br><br>
        </div>

        <div class="field with-icon">
          <span class="i i-phone"></span>
          <input type="tel" name="phone" placeholder="Phone Number"
                 pattern="[0-9]{10}" title="Must be exactly 10 digits"
                 value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required><br><br>
        </div>

        <div class="field with-icon">
          <span class="i i-lock"></span>
          <input type="password" name="password" placeholder="Password (min 8 characters)"
                 minlength="8" required><br><br>
        </div>

        <div class="field with-icon">
          <span class="i i-lock"></span>
          <input type="password" name="confirm_password" placeholder="Confirm Password"
                 minlength="8" required><br><br>
        </div>

        <button class="btn-cta" type="submit">
          <span class="i i-plus"></span> Create Account
        </button>

        <p class="muted center">Already have an account? <a href="login.php">Sign In</a></p>
        <p class="muted tiny center"><span class="i i-shield"></span> Your data is protected </p>
      </form>
    </section>
  </main>
</body>
</html>
