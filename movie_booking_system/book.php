<?php
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header('Location: login.php');
    exit;
}

function columnExists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns 
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return (bool)$stmt->fetchColumn();
}

$booking_success = false;
$booked_seats_display = '';
$booked_total_display = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $movie_id = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : 0;
    $seat_numbers = trim($_POST['seat_numbers'] ?? '');

    if (!$user_id || $movie_id <= 0 || $seat_numbers === '') {
        echo "<script>alert('Please select seats.'); window.location='user.php';</script>";
        exit;
    }

    $requested = array_values(array_filter(array_map('trim', explode(',', $seat_numbers))));

    $stmt = $pdo->prepare("SELECT seat_numbers FROM bookings WHERE movie_id = ?");
    $stmt->execute([$movie_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $booked = [];
    foreach ($rows as $r) {
        if (trim($r) === '') continue;
        foreach (explode(',', $r) as $p) {
            $p = trim($p);
            if ($p !== '') $booked[] = $p;
        }
    }
    $booked = array_values(array_unique($booked));

    if ($intersection = array_intersect($requested, $booked)) {
        $conflict = implode(', ', $intersection);
        echo "<script>alert('Seats ($conflict) are already booked. Please choose others.'); window.location='user.php';</script>";
        exit;
    }

    $tierPrices = ['A' => 150, 'B' => 120, 'C' => 80, 'D' => 60, 'E' => 50];
    $total = 0;
    foreach ($requested as $s) {
        $row = strtoupper(substr($s, 0, 1));
        $total += $tierPrices[$row] ?? 50;
    }

    try {
        $seats_count = count($requested);
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, movie_id, seats, seat_numbers) VALUES (?,?,?,?)");
        $stmt->execute([$user_id, $movie_id, $seats_count, implode(',', $requested)]);
        $bookingId = $pdo->lastInsertId();

        if (columnExists($pdo, 'bookings', 'total_amount')) {
            $stmt = $pdo->prepare("UPDATE bookings SET total_amount = ? WHERE id = ?");
            $stmt->execute([$total, $bookingId]);
        }

        $booking_success = true;
        $booked_seats_display = implode(', ', $requested);
        $booked_total_display = $total;
    } catch (Exception $e) {
        error_log("Booking insert failed: " . $e->getMessage());
        echo "<script>alert('Failed to book seats. Try again.'); window.location='user.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Booking Confirmation</title>
    <style>
        body {
            font-family: "Segoe UI", Roboto, Arial, sans-serif;
            background: rgba(0, 0, 0, 0.6);
            margin: 0;
        }

        .modal {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .modal-content {
            background: #fff;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            padding: 30px;
            text-align: center;
            position: relative;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        h2 {
            color: #28a745;
            margin-bottom: 10px;
        }

        p {
            color: #444;
            margin: 8px 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.2s;
        }

        .btn:hover {
            background: #0056b3;
        }

        .close-btn {
            position: absolute;
            right: 16px;
            top: 16px;
            background: linear-gradient(135deg, #ff4d4d, #b30000);
            border: none;
            color: #fff;
            padding: 8px 12px;
            border-radius: 50%;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
            transition: 0.2s ease;
        }

        .close-btn:hover {
            transform: scale(1.1);
            background: linear-gradient(135deg, #ff1a1a, #990000);
        }
    </style>
</head>

<body>
    <div class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="window.location='user.php'">&times;</button>
            <?php if ($booking_success): ?>
                <canvas id="confettiCanvas" style="position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;"></canvas>
                <h2>🎉 Booking Successful!</h2>
                <p><strong>Seats:</strong> <?php echo htmlspecialchars($booked_seats_display); ?></p>
                <p><strong>Total Price:</strong> Rs. <?php echo number_format($booked_total_display); ?></p>
                <a href="user.php" class="btn">Back to Dashboard</a>
            <?php else: ?>
                <h2 style="color:red;">Booking Failed</h2>
                <p>Something went wrong. Please try again.</p>
                <a href="user.php" class="btn">Try Again</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($booking_success): ?>
        <script>
            const canvas = document.getElementById('confettiCanvas');
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            const confetti = [];
            for (let i = 0; i < 150; i++) {
                confetti.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height - canvas.height,
                    r: Math.random() * 6 + 3,
                    dx: Math.random() * 2 - 1,
                    dy: Math.random() * 4 + 2,
                    color: `hsl(${Math.random() * 360}, 100%, 60%)`
                });
            }

            function draw() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                confetti.forEach(c => {
                    ctx.beginPath();
                    ctx.arc(c.x, c.y, c.r, 0, 2 * Math.PI);
                    ctx.fillStyle = c.color;
                    ctx.fill();
                });
            }

            function update() {
                confetti.forEach(c => {
                    c.x += c.dx;
                    c.y += c.dy;
                    if (c.y > canvas.height) c.y = -10;
                });
            }

            function animate() {
                draw();
                update();
                requestAnimationFrame(animate);
            }
            animate();
        </script>
    <?php endif; ?>
</body>

</html>