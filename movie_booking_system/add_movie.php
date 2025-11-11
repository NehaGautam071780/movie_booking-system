<?php
require 'config.php';
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){ header('Location: login.php'); exit; }

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    if(!empty($_FILES['image']['name'])){
        $target_dir = 'images/';
        if(!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $filename;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    } else {
        $target_file = 'images/default.jpg';
    }
    $stmt = $pdo->prepare("INSERT INTO movies (name, image) VALUES (?, ?)");
    $stmt->execute([$name, $target_file]);
    header('Location: admin.php'); exit;
}
?>