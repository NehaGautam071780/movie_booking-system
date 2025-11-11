<?php
// navbar.php - include on pages after requiring config.php
?>
<nav style="background:#1f2937;color:#fff;padding:5px 20px;display:flex;align-items:center;gap:14px;">
  <img src="images/logo.jpg" alt="MovieLogo" style="height:70px; width: auto;">
  <?php if (!isset($_SESSION['role'])): ?>
    <a href="index.php" style="color:#fff;text-decoration:none;font-weight:600;">Home</a>
  <?php endif; ?>

  <?php if (isset($_SESSION['role'])): ?>
    <?php if ($_SESSION['role'] == 'admin'): ?>
      <a href="admin.php" style="color:#fff;text-decoration:none;">Dashboard</a>
      <a href="admin_bookings.php" style="color:#fff;text-decoration:none;">View Bookings</a>
    <?php else: ?>
      <a href="user.php" style="color:#fff;text-decoration:none;">Dashboard</a>
      <a href="user_history.php" style="color:#fff;text-decoration:none;">My Bookings</a>
    <?php endif; ?>
    <a href="logout.php" style="color:#fff;text-decoration:none;margin-left:auto;">Logout</a>
  <?php else: ?>
    <a href="login.php" style="margin-left:auto;color:#fff;text-decoration:none;">Login</a>
  <?php endif; ?>
</nav>