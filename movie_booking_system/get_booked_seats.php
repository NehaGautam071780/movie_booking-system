<?php
require 'config.php';
header('Content-Type: application/json');
$movie_id = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : 0;
if ($movie_id <= 0) {
    echo json_encode(['booked' => []]);
    exit;
}
$stmt = $pdo->prepare("SELECT seat_numbers FROM bookings WHERE movie_id=?");
$stmt->execute([$movie_id]);
$all = $stmt->fetchAll(PDO::FETCH_COLUMN);
$booked = [];
foreach ($all as $s) {
    if (trim($s) === '') continue;
    $parts = explode(',', $s);
    foreach ($parts as $p) $booked[] = trim($p);
}
$booked = array_values(array_unique($booked));
echo json_encode(['booked' => $booked]);
