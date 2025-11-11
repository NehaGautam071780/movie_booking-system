<?php
require 'config.php';
$movies = $pdo->query("SELECT * FROM movies ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Movie Booking - Home</title>
  <link rel="stylesheet" href="style.css">

</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container">
    <header>
      <h1>Now Showing</h1>
    </header>
    <div class="movie-grid">
      <?php foreach ($movies as $m): ?>
        <div class="movie-card">
          <img src="<?php echo htmlspecialchars($m['image']); ?>" alt="<?php echo htmlspecialchars($m['name']); ?>">
          <h3><?php echo htmlspecialchars($m['name']); ?></h3>
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
            <a href="user.php"><button>Book Tickets</button></a>
          <?php else: ?>
            <a href="login.php"><button>Login to Book</button></a>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>

</html>