<?php
session_start();
include "../includes/db.php";

// Initialize error message
$errors = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);       // Username / Email / Phone field
    $password = trim($_POST['password']);

    if (empty($login) || empty($password)) {
        $errors = "Please enter both username/email/phone and password.";
    } else {
        // Fetch user using username OR email OR phone
        $stmt = $conn->prepare("SELECT id, username, email, phone, password_hash, is_admin FROM users WHERE username = ? OR email = ? OR phone = ?");
        $stmt->bind_param("sss", $login, $login, $login);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['is_admin'] == 1) {
                // Admin login
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                header("Location: ../admin/admin_panel.php");
                exit;
            } else {
                // Normal user login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['phone'] = $user['phone'];
                header("Location: ../dashboard.php");
                exit;
            }
        } else {
            $errors = "Invalid login credentials.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="../css/loginstyle.css">
</head>
<body>
<div class="container">
    <div class="form-container">
        <h2>eProof Login</h2>

        <?php if ($errors): ?>
            <div class="error">
                <p><?= htmlspecialchars($errors) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="login" placeholder="Username / Email / Phone" required>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <button type="submit">Login</button>
        </form>
        <p class="alt-text">Don’t have an account? <a href="register.php">Sign up</a></p>
    </div>
</div>
</body>
</html>
