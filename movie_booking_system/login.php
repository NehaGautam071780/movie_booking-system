<?php
require 'config.php';

$message = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action']) && $_POST['action'] === 'register') {
    // --- Registration ---
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    if ($password !== $confirm) {
      $message = 'Passwords do not match.';
    } else {
      // Check if email already exists
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
      $stmt->execute([$email]);
      if ($stmt->fetchColumn() > 0) {
        $message = 'Email already registered. Please login.';
      } else {
        // Create new user (basic password, same logic as your DB)
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'user')");
        $stmt->execute([$email, $password]);
        $success = 'Registration successful! You can now login.';
      }
    }
  } else {
    // --- Login ---
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['role'] = $user['role'];
      header($user['role'] == 'admin' ? 'Location: admin.php' : 'Location: user.php');
      exit;
    } else {
      $message = 'Invalid credentials.';
    }
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Login / Register</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container" style="max-width:420px;text-align:center;">
    <h2 id="form-title">Login</h2>

    <!-- LOGIN FORM -->
    <form id="login-form" method="post" autocomplete="off">
      <input type="hidden" name="action" value="login">
      <div class="form-row"><input type="email" name="email" placeholder="Email" required></div>
      <div class="form-row"><input type="password" name="password" placeholder="Password" required></div>
      <button type="submit">Login</button>
    </form>

    <!-- REGISTER FORM -->
    <form id="register-form" method="post" class="hidden" autocomplete="off">
      <input type="hidden" name="action" value="register">
      <div class="form-row"><input type="email" name="email" placeholder="Email" required></div>
      <div class="form-row"><input type="password" name="password" placeholder="Password" required></div>
      <div class="form-row"><input type="password" name="confirm_password" placeholder="Confirm Password" required></div>
      <button type="submit">Register</button>
    </form>

    <a class="toggle-link" id="toggle-link" style="cursor:pointer;color:#0f172a;text-decoration:underline;" onclick="toggleForms()">
      Don’t have an account? Register
    </a>

    <?php if ($message): ?>
      <p style="color:#b91c1c;font-weight:600;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
      <p style="color:#047857;font-weight:600;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
  </div>

  <script>
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const toggleLink = document.getElementById('toggle-link');
    const title = document.getElementById('form-title');

    function toggleForms() {
      const isLogin = !loginForm.classList.contains('hidden');
      loginForm.classList.toggle('hidden');
      registerForm.classList.toggle('hidden');
      title.textContent = isLogin ? 'Register' : 'Login';
      toggleLink.textContent = isLogin ? 'Already have an account? Login' : 'Don’t have an account? Register';
    }
  </script>
</body>

</html>