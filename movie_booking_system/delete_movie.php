<?php
require 'config.php';
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){ header('Location: login.php'); exit; }
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = (int)$_POST['id'];
    // Optionally delete image file
    $stmt = $pdo->prepare("SELECT image FROM movies WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row && file_exists($row['image']) && strpos($row['image'],'images/')===0){
        @unlink($row['image']);
    }
    $stmt = $pdo->prepare("DELETE FROM movies WHERE id=?");
    $stmt->execute([$id]);
    header('Location: admin.php'); exit;
}
?>