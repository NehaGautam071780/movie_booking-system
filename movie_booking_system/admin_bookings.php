<?php
require 'config.php';
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){ header('Location: login.php'); exit; }
$stmt = $pdo->query("SELECT b.*, u.email AS user_email, m.name AS movie_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN movies m ON b.movie_id = m.id ORDER BY b.booking_time DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>All Bookings</title><link rel="stylesheet" href="style.css"></head><body>
<?php include 'navbar.php'; ?>
<div class="container">
  <h2>All Bookings</h2>
  <?php if(empty($rows)): ?>
    <p>No bookings yet.</p>
  <?php else: ?>
    <table class="table">
      <thead><tr><th>User</th><th>Movie</th><th>Seats</th><th>Seat Numbers</th><th>When</th></tr></thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['user_email']); ?></td>
            <td><?php echo htmlspecialchars($r['movie_name']); ?></td>
            <td><?php echo (int)$r['seats']; ?></td>
            <td><?php echo htmlspecialchars($r['seat_numbers']); ?></td>
            <td><?php echo htmlspecialchars($r['booking_time']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
</body></html>
