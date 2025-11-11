<?php
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  header('Location: login.php');
  exit;
}
$movies = $pdo->query("SELECT * FROM movies ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container">
    <h2>Admin Dashboard</h2>
    <section>
      <h3>Add Movie</h3>
      <form action="add_movie.php" method="post" enctype="multipart/form-data">
        <div class="form-row"><input type="text" name="name" placeholder="Movie name" required></div>
        <div class="form-row"><input type="file" name="image" accept="image/*" required></div>
        <button type="submit">Add Movie</button>
      </form>
    </section>

    <section style="margin-top:20px;">
      <h3>Existing Movies</h3>
      <div class="movie-grid">
        <?php foreach ($movies as $m): ?>
          <div class="movie-card">
            <img src="<?php echo htmlspecialchars($m['image']); ?>" alt="">
            <h3><?php echo htmlspecialchars($m['name']); ?></h3>
            <form action="delete_movie.php" method="post" onsubmit="return confirm('Delete this movie?');">
              <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
              <button type="submit" class="secondary">Delete</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</body>

</html>