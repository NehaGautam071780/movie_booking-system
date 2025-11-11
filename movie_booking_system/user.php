<?php
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
  header('Location: login.php');
  exit;
}
$movies = $pdo->query("SELECT * FROM movies ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <style>
    :root {
      --card-bg: #ffffff;
      --muted: #6b7280;
      --accent: #0b5cff;
      --glass: rgba(255, 255, 255, 0.75);
      --shadow-1: 0 8px 30px rgba(2, 6, 23, 0.12);
      --radius: 12px;
    }

    body {
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: linear-gradient(180deg, #f6f8fb, #ffffff);
      color: #111827;
      margin: 0;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    .container {
      max-width: 1200px;
      margin: 34px auto;
      padding: 24px;
    }

    h2 {
      margin: 0 0 18px 0;
      font-size: 28px;
      letter-spacing: -0.3px;
    }

    .carousel-wrap {
      position: relative;
      margin-bottom: 28px;
      overflow: hidden;
      box-shadow: var(--shadow-1);
      background: linear-gradient(90deg, rgba(11, 92, 255, 0.04), rgba(59, 130, 246, 0.02));
      width: 100vw;
      left: 50%;
      transform: translateX(-50%);
      box-sizing: border-box;
    }

    .carousel-viewport {
      width: 100%;
      height: 56vh;
      min-height: 260px;
      max-height: 520px;
      position: relative;
    }

    .slider-track {
      display: flex;
      width: 100%;
      height: 100%;
      transition: transform .6s ease;
      will-change: transform;
    }

    .slide {
      flex: 0 0 100%;
      height: 100%;
      position: relative;
      overflow: hidden;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #000;
    }

    .slide img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
      display: block;
      transform-origin: center;
      transition: transform .4s ease;
      user-select: none;
      -webkit-user-drag: none;
    }

    .slide:hover img {
      transform: scale(1.02);
    }

    .slide-caption {
      position: absolute;
      left: 20px;
      bottom: 24px;
      right: 20px;
      background: rgba(0, 0, 0, 0.45);
      color: #fff;
      padding: 10px 14px;
      border-radius: 10px;
      font-weight: 800;
      font-size: 16px;
      display: inline-block;
      max-width: calc(100% - 40px);
      text-overflow: ellipsis;
      white-space: nowrap;
      overflow: hidden;
    }

    .carousel-button {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 44px;
      height: 44px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.9);
      border: none;
      box-shadow: 0 8px 20px rgba(2, 6, 23, 0.08);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      z-index: 40;
      color: #0b1220;
    }

    .carousel-prev {
      left: 18px;
    }

    .carousel-next {
      right: 18px;
    }

    .carousel-indicators {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      bottom: 14px;
      display: flex;
      gap: 8px;
      z-index: 40;
    }

    .indicator {
      width: 10px;
      height: 10px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.6);
      border: 1px solid rgba(11, 92, 255, 0.18);
      cursor: pointer;
    }

    .indicator.active {
      background: var(--accent);
      box-shadow: 0 6px 14px rgba(11, 92, 255, 0.14);
    }

    .movie-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
      margin-bottom: 24px;
    }

    .movie-card {
      padding: 14px;
      border-radius: 14px;
      background: linear-gradient(180deg, var(--card-bg), #fbfdff);
      box-shadow: var(--shadow-1);
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      transition: transform .14s ease, box-shadow .14s ease;
    }

    .movie-card:hover {
      transform: translateY(-6px)
    }

    .movie-card img {
      width: 100%;
      height: 320px;
      object-fit: cover;
      border-radius: 10px;
      display: block;
      margin-bottom: 12px;
      background: linear-gradient(180deg, #eef2f6, #ffffff);
    }

    .movie-card h3 {
      margin: 0 0 12px 0;
      font-size: 18px;
      line-height: 1.2;
      color: #0f172a;
      text-align: left;
      padding: 0 4px;
    }

    .movie-actions {
      display: flex;
      gap: 10px;
      margin-top: auto;
      align-items: center;
      justify-content: space-between;
      padding: 0 4px 4px 4px;
    }

    .select-btn {
      padding: 10px 16px;
      font-size: 15px;
      background: var(--accent);
      color: #fff;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 0 10px 20px rgba(11, 92, 255, 0.12);
      margin: 0 auto;
    }

    .select-btn.secondary {
      background: transparent;
      color: var(--muted);
      border: 1px solid rgba(0, 0, 0, 0.06);
      box-shadow: none;
    }

    #bookingModal {
      display: none;
      position: fixed;
      left: 0;
      top: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(180deg, rgba(2, 6, 23, 0.5), rgba(2, 6, 23, 0.38));
      z-index: 1000;
      padding: 28px;
      box-sizing: border-box;
      overflow: auto;
      backdrop-filter: blur(6px);
    }

    .modal-inner {
      position: relative;
      max-width: 980px;
      margin: 32px auto;
      padding: 18px;
      border-radius: 14px;
      background: linear-gradient(180deg, #ffffff, #fbfdff);
      max-height: calc(100vh - 120px);
      overflow-y: auto;
      box-shadow: 0 30px 80px rgba(2, 6, 23, 0.2);
    }

    .modal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 12px;
    }

    .modal-title {
      font-size: 20px;
      font-weight: 800;
      color: #0b1220;
      display: flex;
      gap: 12px;
      align-items: center;
    }

    .close-btn {
      padding: 10px 12px;
      border-radius: 10px;
      border: none;
      background: #ef4444;
      color: #fff;
      cursor: pointer;
      font-weight: 700;
      box-shadow: 0 8px 20px rgba(239, 68, 68, 0.12);
    }

    #tiersLegend {
      display: flex;
      gap: 10px;
      justify-content: center;
      margin-top: 6px;
      margin-bottom: 12px;
      flex-wrap: wrap;
    }

    .tier-pill {
      padding: 8px 12px;
      border-radius: 999px;
      font-weight: 700;
      font-size: 13px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: 1px solid rgba(0, 0, 0, 0.06);
      background: #fff;
      box-shadow: 0 6px 14px rgba(2, 6, 23, 0.04);
    }

    .tier-color {
      width: 12px;
      height: 12px;
      border-radius: 3px;
      display: inline-block;
    }

    .seats-area {
      display: flex;
      gap: 18px;
      align-items: flex-start;
      justify-content: center;
      flex-wrap: wrap;
    }

    /* ---------- SEAT MAP (unchanged layout) ---------- */
    .seat-map {
      display: grid;
      grid-template-columns: 92px repeat(8, 56px);
      gap: 12px;
      justify-content: center;
      margin-top: 8px;
      position: relative;
      align-items: center;
      background: linear-gradient(180deg, #fbfdff, #f6f9ff);
      padding: 18px;
      border-radius: 12px;
      border: 1px solid rgba(11, 92, 255, 0.06);
      box-shadow: 0 14px 40px rgba(11, 92, 255, 0.04);
    }

    /* ---------- SCREEN (redesigned appearance only) ---------- */
    .screen {
      grid-column: 1 / -1;
      text-align: center;
      margin-bottom: 10px;
      font-weight: 900;
      font-size: 14px;
      color: #0b1220;
      padding: 14px 18px;
      border-radius: 14px;
      background: linear-gradient(180deg, #eef6ff, #ffffff);
      box-shadow: 0 14px 40px rgba(11, 92, 255, 0.06), inset 0 -6px 20px rgba(255, 255, 255, 0.6);
      position: relative;
      overflow: visible;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    /* soft glossy reflection on the screen */
    .screen::before {
      content: "";
      position: absolute;
      left: 50%;
      top: 18%;
      transform: translateX(-50%);
      width: 70%;
      height: 36%;
      background: radial-gradient(ellipse at center, rgba(255, 255, 255, 0.45), rgba(255, 255, 255, 0.05));
      border-radius: 50%;
      pointer-events: none;
      filter: blur(6px);
      opacity: 0.9;
    }

    /* small decorative frame under the screen (stands) */
    .screen::after {
      content: "";
      display: block;
      position: absolute;
      left: 50%;
      bottom: -10px;
      transform: translateX(-50%);
      width: 220px;
      height: 10px;
      background: linear-gradient(90deg, rgba(11, 92, 255, 0.06), rgba(11, 92, 255, 0.02));
      border-radius: 8px;
      pointer-events: none;
      box-shadow: 0 6px 20px rgba(11, 92, 255, 0.03);
    }

    /* ---------- ROW LABEL (a bit more compact) ---------- */
    .row-label {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      justify-content: center;
      padding-left: 8px;
      font-weight: 700;
      color: #111;
      font-size: 13px;
    }

    .row-label .row-name {
      font-size: 14px;
      margin-bottom: 4px;
    }

    .row-label .row-price {
      font-size: 12px;
      color: #374151;
      font-weight: 600;
    }

    /* ---------- SEAT (redesigned visuals only) ---------- */
    .seat {
      width: 56px;
      height: 56px;
      cursor: pointer;
      user-select: none;
      transition: transform .14s ease, color .14s ease, background .18s ease, box-shadow .18s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(180deg, #ffffff, #f6f9ff);
      border: 1px solid rgba(14, 21, 47, 0.06);
      border-radius: 10px;
      padding: 6px;
      box-sizing: border-box;
      color: #1f2937;
      /* default icon color */
      box-shadow: 0 6px 18px rgba(2, 6, 23, 0.03);
      position: relative;
      overflow: hidden;
    }

    .seat:hover {
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 14px 30px rgba(2, 6, 23, 0.08);
    }

    /* Tier colors exposed as CSS variables on seat elements */
    .seat.tier-A {
      --tier: #f97316;
    }

    .seat.tier-B {
      --tier: #f59e0b;
    }

    .seat.tier-C {
      --tier: #10b981;
    }

    .seat.tier-D {
      --tier: #3b82f6;
    }

    .seat.tier-E {
      --tier: #6b7280;
    }

    /* when selected: fill the seat with the tier color and turn icon white */
    .seat.selected {
      transform: translateY(-6px) scale(1.03);
      color: #fff;
      background: linear-gradient(180deg, color-mix(in srgb, var(--tier) 90%, #000 2%), var(--tier));
      border: 1px solid rgba(0, 0, 0, 0.08);
      box-shadow: 0 18px 40px color-mix(in srgb, var(--tier) 25%, rgba(2, 6, 23, 0.18));
    }

    /* Specific visible accents for each tier when selected */
    .seat.tier-A.selected {
      box-shadow: 0 18px 40px rgba(249, 115, 22, 0.16);
    }

    .seat.tier-B.selected {
      box-shadow: 0 18px 40px rgba(245, 158, 11, 0.14);
    }

    .seat.tier-C.selected {
      box-shadow: 0 18px 40px rgba(16, 185, 129, 0.12);
    }

    .seat.tier-D.selected {
      box-shadow: 0 18px 40px rgba(59, 130, 246, 0.12);
    }

    .seat.tier-E.selected {
      box-shadow: 0 18px 40px rgba(107, 114, 128, 0.12);
    }

    /* taken seats: dim and show solid fill to indicate unavailability */
    .seat.taken {
      cursor: not-allowed;
      color: #fff;
      background: linear-gradient(180deg, rgba(30, 41, 59, 0.9), rgba(30, 41, 59, 0.78));
      border: 1px solid rgba(0, 0, 0, 0.08);
      opacity: 0.96;
      box-shadow: 0 10px 30px rgba(2, 6, 23, 0.12) inset;
    }

    /* icon behavior: use currentColor; when seat is selected or taken the svg gets filled */
    .seat svg {
      width: 28px;
      height: 28px;
      display: block;
      stroke: currentColor;
      fill: none;
      stroke-width: 1.4;
      stroke-linecap: round;
      stroke-linejoin: round;
      transition: all .12s ease;
    }

    .seat.selected svg {
      fill: currentColor;
      stroke: none;
    }

    .seat.taken svg {
      fill: currentColor;
      stroke: none;
      opacity: 0.95;
    }

    /* small seat label overlay for easier reading on small screens */
    .seat .sr-only {
      position: absolute;
      bottom: 4px;
      left: 6px;
      right: 6px;
      text-align: center;
      font-size: 11px;
      font-weight: 800;
      color: rgba(255, 255, 255, 0.9);
      pointer-events: none;
      transform: translateY(0);
    }

    /* maintain previous selected color fallbacks (for accessibility if CSS variables not loaded) */
    .seat.tier-A.selected {
      background: #f97316;
    }

    .seat.tier-B.selected {
      background: #f59e0b;
    }

    .seat.tier-C.selected {
      background: #10b981;
    }

    .seat.tier-D.selected {
      background: #3b82f6;
    }

    .seat.tier-E.selected {
      background: #6b7280;
    }

    #summaryBox {
      margin-top: 14px;
      padding: 12px;
      border-radius: 10px;
      background: linear-gradient(180deg, #ffffff, #fbfdff);
      border: 1px solid #e6eef6;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      box-shadow: 0 8px 24px rgba(2, 6, 23, 0.04);
    }

    #summaryBox .left {
      font-weight: 700;
      color: #111
    }

    #summaryBox .right {
      font-weight: 800;
      font-size: 18px;
      color: var(--accent)
    }

    /* ---------- PAYMENT & SUCCESS MODALS ---------- */
    #paymentModal,
    #successModal {
      display: none;
      position: fixed;
      left: 0;
      top: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(180deg, rgba(2, 6, 23, 0.6), rgba(2, 6, 23, 0.45));
      z-index: 1100;
      padding: 28px;
      box-sizing: border-box;
      overflow: auto;
      backdrop-filter: blur(6px);
    }

    .payment-inner,
    .success-inner {
      position: relative;
      max-width: 540px;
      margin: 80px auto;
      padding: 18px;
      border-radius: 12px;
      background: linear-gradient(180deg, #ffffff, #fbfdff);
      box-shadow: 0 30px 80px rgba(2, 6, 23, 0.2);
    }

    .payment-title {
      font-weight: 800;
      font-size: 18px;
      margin-bottom: 8px;
      color: #0b1220;
    }

    .payment-amount {
      font-weight: 900;
      font-size: 22px;
      color: var(--accent);
      margin-bottom: 12px;
    }

    .fake-card {
      border-radius: 10px;
      padding: 12px;
      border: 1px solid #e6eef6;
      background: linear-gradient(180deg, #f8fbff, #ffffff);
      box-shadow: 0 10px 30px rgba(2, 6, 23, 0.04);
      margin-bottom: 12px;
    }

    .fake-card label {
      display: block;
      font-size: 13px;
      margin-bottom: 6px;
      color: #374151;
      font-weight: 700;
    }

    .fake-card input {
      width: 100%;
      padding: 10px 12px;
      border-radius: 8px;
      border: 1px solid #e6eef6;
      font-size: 14px;
      margin-bottom: 8px;
      box-sizing: border-box;
      background: #fff;
    }

    .pay-actions {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      margin-top: 8px;
    }

    .btn-pay {
      padding: 10px 14px;
      border-radius: 10px;
      border: none;
      background: #10b981;
      color: #fff;
      font-weight: 800;
      cursor: pointer;
    }

    .btn-cancel {
      padding: 10px 14px;
      border-radius: 10px;
      border: 1px solid #e6eef6;
      background: #fff;
      color: #374151;
      font-weight: 800;
      cursor: pointer;
    }

    .processing {
      display: inline-flex;
      gap: 10px;
      align-items: center;
      font-weight: 700;
      color: #374151;
    }

    .spinner {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      border: 3px solid rgba(0, 0, 0, 0.08);
      border-top-color: #0b5cff;
      animation: spin 1s linear infinite;
      display: inline-block;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .success-inner .success-emoji {
      font-size: 48px;
      text-align: center;
      margin-bottom: 6px;
    }

    .success-inner .success-msg {
      text-align: center;
      font-weight: 900;
      font-size: 18px;
      margin-bottom: 8px;
      color: #0b1220;
    }

    .success-inner .success-detail {
      text-align: center;
      font-size: 14px;
      color: #374151;
      margin-bottom: 12px;
    }

    .success-actions {
      display: flex;
      gap: 10px;
      justify-content: center;
      margin-top: 6px;
    }

    .btn-primary {
      padding: 10px 14px;
      border-radius: 10px;
      border: none;
      background: var(--accent);
      color: #fff;
      font-weight: 800;
      cursor: pointer;
    }

    .btn-secondary {
      padding: 10px 14px;
      border-radius: 10px;
      border: 1px solid #e6eef6;
      background: #fff;
      color: #374151;
      font-weight: 800;
      cursor: pointer;
    }

    @media (max-width:920px) {
      .movie-card img {
        height: 240px
      }

      .carousel-viewport {
        height: 44vh;
      }

      .seat {
        width: 48px;
        height: 48px;
      }

      .seat svg {
        width: 22px;
        height: 22px;
      }

      .seat-map {
        grid-template-columns: 72px repeat(8, 44px)
      }
    }

    @media (max-width:680px) {
      .movie-card img {
        height: 200px
      }

      .carousel-viewport {
        height: 40vh;
        min-height: 180px;
      }

      .movie-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))
      }

      .modal-inner {
        margin: 18px 8px;
        padding: 12px
      }

      .seats-area {
        gap: 12px
      }

      .seat-map {
        padding: 12px
      }

      .carousel-prev,
      .carousel-next {
        width: 36px;
        height: 36px;
        font-size: 18px;
      }
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="carousel-wrap" id="carouselWrap">
    <div class="carousel-viewport">
      <div class="slider-track" id="sliderTrack">
        <?php
        $limit = min(5, count($movies));
        for ($i = 0; $i < $limit; $i++):
          $m = $movies[$i];
        ?>
          <div class="slide" data-movie-id="<?php echo $m['id']; ?>" data-movie-name="<?php echo addslashes($m['name']); ?>" onclick="openBooking(<?php echo $m['id']; ?>,'<?php echo addslashes($m['name']); ?>')">
            <img src="<?php echo htmlspecialchars($m['image']); ?>" alt="<?php echo htmlspecialchars($m['name']); ?>" draggable="false">
            <div class="slide-caption"><?php echo htmlspecialchars($m['name']); ?></div>
          </div>
        <?php endfor; ?>
      </div>
      <button class="carousel-button carousel-prev" id="carouselPrev" aria-label="Previous">&#9664;</button>
      <button class="carousel-button carousel-next" id="carouselNext" aria-label="Next">&#9654;</button>
      <div class="carousel-indicators" id="carouselIndicators"></div>
    </div>
  </div>
  <div class="container">
    <div class="movie-grid">
      <?php foreach ($movies as $m): ?>
        <div class="movie-card">
          <img src="<?php echo htmlspecialchars($m['image']); ?>" alt="<?php echo htmlspecialchars($m['name']); ?>">
          <h3 style="text-align: center;"><?php echo htmlspecialchars($m['name']); ?></h3>
          <div class="movie-actions">
            <button class="select-btn" onclick="openBooking(<?php echo $m['id']; ?>,'<?php echo addslashes($m['name']); ?>')">Select Seats</button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Booking modal (existing) -->
  <div id="bookingModal" onclick="if(event.target.id==='bookingModal')closeBooking()">
    <div class="modal-inner">
      <div class="modal-header">
        <div class="modal-title" id="movieTitle"></div>
        <div style="display:flex;gap:8px;align-items:center">
          <div style="font-weight:700;color:#0b1220;background:linear-gradient(180deg,#fff,#f3f4f6);padding:8px 12px;border-radius:10px;border:1px solid rgba(11,92,255,0.06)">Choose Seats</div>
          <button class="close-btn" onclick="closeBooking()">Close</button>
        </div>
      </div>
      <div id="tiersLegend">
        <div class="tier-pill"><span class="tier-color" style="background:#f97316"></span> VIP — Rs.700</div>
        <div class="tier-pill"><span class="tier-color" style="background:#f59e0b"></span> Premium — Rs.500</div>
        <div class="tier-pill"><span class="tier-color" style="background:#10b981"></span> Standard — Rs.350</div>
        <div class="tier-pill"><span class="tier-color" style="background:#3b82f6"></span> Economy — Rs.250</div>
        <div class="tier-pill"><span class="tier-color" style="background:#6b7280"></span> Budget — Rs.150</div>
      </div>
      <div class="seats-area">
        <div id="seatMap" class="seat-map"></div>
        <div style="min-width:220px;max-width:260px;">
          <div style="padding:14px;border-radius:12px;background:linear-gradient(180deg,#ffffff,#fbfdff);border:1px solid #e6eef6;box-shadow:0 10px 30px rgba(2,6,23,0.04);">
            <div style="font-weight:800;font-size:16px;margin-bottom:8px">Selection Summary</div>
            <div style="font-size:14px;color:#374151;margin-bottom:8px">Selected seats</div>
            <div id="selectedList" style="min-height:38px;padding:8px;border-radius:8px;background:#fbfdff;border:1px dashed rgba(11,92,255,0.04);font-weight:700;color:#0b1220">None</div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px">
              <div style="font-weight:700;color:#374151">Total</div>
              <div style="font-weight:900;font-size:20px;color:var(--accent)">Rs.<span id="totalPrice">0</span></div>
            </div>
            <form id="bookForm" method="post" action="book.php" style="margin-top:14px;">
              <input type="hidden" name="movie_id" id="movie_id">
              <input type="hidden" name="seat_numbers" id="seat_numbers">
              <input type="hidden" name="total_amount" id="total_amount">
              <button type="submit" id="confirmBookingBtn" style="width:100%;margin-top:12px;padding:12px 14px;border-radius:10px;border:none;background:var(--accent);color:#fff;font-weight:800;cursor:pointer">Confirm Booking</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Payment Modal (dummy) -->
  <div id="paymentModal" onclick="if(event.target.id==='paymentModal')closePaymentModal()">
    <div class="payment-inner" role="dialog" aria-modal="true" aria-labelledby="paymentTitle">
      <div class="payment-title" id="paymentTitle">Dummy Payment</div>
      <div class="payment-amount" id="paymentAmount">Rs. 0</div>

      <div class="fake-card" aria-hidden="false">
        <label>Card number</label>
        <input type="text" id="cardNumber" placeholder="4242 4242 4242 4242" autocomplete="off">
        <label>Name on card</label>
        <input type="text" id="cardName" placeholder="John Doe" autocomplete="off">
        <div style="display:flex;gap:8px">
          <div style="flex:1">
            <label>Expiry</label>
            <input type="text" id="cardExp" placeholder="MM/YY" autocomplete="off">
          </div>
          <div style="width:120px">
            <label>CVC</label>
            <input type="text" id="cardCvc" placeholder="123" autocomplete="off">
          </div>
        </div>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between">
        <div id="paymentStatus" style="font-weight:700;color:#374151"></div>
        <div class="pay-actions">
          <button class="btn-cancel" id="cancelPaymentBtn" type="button" onclick="closePaymentModal()">Cancel</button>
          <button class="btn-pay" id="payNowBtn" type="button" onclick="simulatePayment()">Pay Now</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" onclick="if(event.target.id==='successModal')closeSuccessModal()">
    <div class="success-inner" role="dialog" aria-modal="true" aria-labelledby="successTitle">
      <div class="success-emoji">🎉</div>
      <div class="success-msg" id="successTitle">Payment Successful</div>
      <div class="success-detail" id="successDetail">Your payment was processed successfully.</div>
      <div style="text-align:center;font-weight:700;margin-bottom:6px">Booking summary</div>
      <div style="text-align:center;color:#374151;margin-bottom:12px" id="successSummary">Seats: — | Amount: Rs. 0</div>
      <div class="success-actions">
        <button class="btn-primary" id="completeBookingBtn">Complete Booking</button>
        <button class="btn-secondary" onclick="closeSuccessModal()">Close</button>
      </div>
    </div>
  </div>

  <script>
    const tierPrices = {
      A: 700,
      B: 500,
      C: 350,
      D: 250,
      E: 150
    };
    const tierColors = {
      A: '#f97316',
      B: '#f59e0b',
      C: '#10b981',
      D: '#3b82f6',
      E: '#6b7280'
    };
    let selectedSeats = new Set();
    let takenSeats = new Set();

    function openBooking(movieId, movieName) {
      selectedSeats.clear();
      takenSeats.clear();
      document.getElementById('movieTitle').innerText = movieName;
      document.getElementById('movie_id').value = movieId;
      updateSelectedSeats();
      fetch('get_booked_seats.php?movie_id=' + movieId)
        .then(r => r.json())
        .then(data => {
          takenSeats = new Set((data.booked || []).map(String));
          renderSeats();
          document.getElementById('bookingModal').style.display = 'block';
          setTimeout(() => {
            document.getElementById('bookingModal').scrollTo({
              top: 0,
              behavior: 'smooth'
            })
          }, 20);
        })
        .catch(err => {
          console.error(err);
          takenSeats = new Set();
          renderSeats();
          document.getElementById('bookingModal').style.display = 'block';
        });
    }

    function closeBooking() {
      document.getElementById('bookingModal').style.display = 'none';
    }

    function renderSeats() {
      const seatMap = document.getElementById('seatMap');
      seatMap.innerHTML = '';
      const screenDiv = document.createElement('div');
      screenDiv.className = 'screen';
      screenDiv.innerText = 'SCREEN';
      seatMap.appendChild(screenDiv);
      const rows = ['A', 'B', 'C', 'D', 'E'];
      const cols = 8;
      rows.forEach(row => {
        const label = document.createElement('div');
        label.className = 'row-label';
        label.innerHTML = '<div class="row-name">Row ' + row + '</div><div class="row-price">Rs.' + tierPrices[row] + '</div>';
        seatMap.appendChild(label);
        for (let c = 1; c <= cols; c++) {
          const seatId = row + c;
          const div = document.createElement('div');
          div.className = 'seat tier-' + row;
          div.setAttribute('data-seat', seatId);
          div.setAttribute('data-tier', row);
          div.setAttribute('data-price', tierPrices[row]);
          div.title = seatId + ' — Rs.' + tierPrices[row];
          div.setAttribute('role', 'button');
          div.setAttribute('aria-pressed', 'false');
          div.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="6" y="3.5" width="12" height="6.5" rx="2"></rect><rect x="6" y="11" width="12" height="2.5" rx="1"></rect><path d="M8 18v-5"></path><path d="M16 18v-5"></path></svg><span class="sr-only">' + seatId + '</span>';
          if (takenSeats.has(seatId)) div.classList.add('taken');
          if (selectedSeats.has(seatId)) {
            div.classList.add('selected');
            div.setAttribute('aria-pressed', 'true');
          }
          div.onclick = function() {
            if (div.classList.contains('taken')) return;
            const id = div.getAttribute('data-seat');
            if (selectedSeats.has(id)) {
              selectedSeats.delete(id);
              div.classList.remove('selected');
              div.setAttribute('aria-pressed', 'false');
            } else {
              selectedSeats.add(id);
              div.classList.add('selected');
              div.setAttribute('aria-pressed', 'true');
            }
            updateSelectedSeats();
          };
          seatMap.appendChild(div);
        }
      });
    }

    function updateSelectedSeats() {
      const selected = Array.from(selectedSeats);
      document.getElementById('seat_numbers').value = selected.join(',');
      document.getElementById('selectedList').innerText = selected.length ? selected.join(', ') : 'None';
      let total = 0;
      selected.forEach(id => {
        const el = document.querySelector('.seat[data-seat="' + id + '"]');
        if (el) total += parseFloat(el.getAttribute('data-price') || 0);
      });
      document.getElementById('totalPrice').innerText = Number(total).toLocaleString('en-IN', {
        maximumFractionDigits: 0
      });
      document.getElementById('total_amount').value = total;
    }

    // ---------- Payment flow code (dummy) ----------
    const paymentModal = document.getElementById('paymentModal');
    const successModal = document.getElementById('successModal');
    const paymentAmountEl = document.getElementById('paymentAmount');
    const paymentStatusEl = document.getElementById('paymentStatus');
    const payNowBtn = document.getElementById('payNowBtn');
    const cancelPaymentBtn = document.getElementById('cancelPaymentBtn');
    const completeBookingBtn = document.getElementById('completeBookingBtn');

    function openPaymentModal() {
      // ensure latest seats & amount
      updateSelectedSeats();
      const amount = document.getElementById('total_amount').value || 0;
      const seats = document.getElementById('seat_numbers').value || '';
      paymentAmountEl.innerText = 'Rs. ' + Number(amount).toLocaleString('en-IN', {
        maximumFractionDigits: 0
      });
      paymentStatusEl.innerText = seats ? ('Seats: ' + seats) : 'No seats selected';
      paymentStatusEl.style.color = '#374151';
      payNowBtn.disabled = false;
      paymentModal.style.display = 'block';
    }

    function closePaymentModal() {
      paymentModal.style.display = 'none';
      paymentStatusEl.innerText = '';
    }

    function showSuccessModal(txnId, seats, amount) {
      document.getElementById('successDetail').innerText = 'Transaction ID: ' + txnId;
      document.getElementById('successSummary').innerText = 'Seats: ' + (seats || '—') + ' | Amount: Rs. ' + Number(amount || 0).toLocaleString('en-IN', {
        maximumFractionDigits: 0
      });
      successModal.style.display = 'block';
    }

    function closeSuccessModal() {
      successModal.style.display = 'none';
    }

    function simulatePayment() {
      // simulate a payment flow with UI feedback
      const amount = document.getElementById('total_amount').value || 0;
      const seats = document.getElementById('seat_numbers').value || '';
      if (!seats) {
        alert('Please select at least one seat.');
        closePaymentModal();
        return;
      }

      payNowBtn.disabled = true;
      cancelPaymentBtn.disabled = true;
      paymentStatusEl.innerHTML = '<span class="processing"><span class="spinner" aria-hidden="true"></span> Processing payment...</span>';

      // simulate processing time
      setTimeout(() => {
        // success
        const txnId = 'TXN' + Date.now().toString().slice(-8);
        paymentStatusEl.innerHTML = '<span style="color:#10b981;font-weight:800">Payment successful ✓</span>';
        // hide payment modal after short pause and show success UI
        setTimeout(() => {
          closePaymentModal();
          showSuccessModal(txnId, seats, amount);
        }, 700);
      }, 1600);
    }

    // When user clicks complete booking, submit the original form to book.php
    completeBookingBtn.addEventListener('click', function() {
      // Ensure the hidden fields are up to date
      updateSelectedSeats();
      // Optionally we can add txn reference to the form as an extra hidden field to mimic payment
      // create or update a hidden input named txn_id
      let txnInput = document.querySelector('input[name="txn_id"]');
      if (!txnInput) {
        txnInput = document.createElement('input');
        txnInput.type = 'hidden';
        txnInput.name = 'txn_id';
        document.getElementById('bookForm').appendChild(txnInput);
      }
      // put a dummy txn id (use timestamp)
      txnInput.value = 'TXN' + Date.now().toString().slice(-8);
      // finally submit the form (will follow original server behaviour)
      document.getElementById('bookForm').submit();
    });

    // Replace the previous simple submit handler with a payment-triggering handler.
    // (Preserves the check that seats must be selected)
    const bookForm = document.getElementById('bookForm');
    bookForm.addEventListener('submit', function(ev) {
      // intercept the normal submit to run dummy payment
      ev.preventDefault();
      updateSelectedSeats();
      const seatInput = document.getElementById('seat_numbers').value;
      if (!seatInput) {
        alert('Please select at least one seat.');
        return;
      }
      // open dummy payment modal
      openPaymentModal();
    });

    // allow cancel payment button to re-enable controls
    cancelPaymentBtn.addEventListener('click', function() {
      payNowBtn.disabled = false;
      cancelPaymentBtn.disabled = false;
    });

    // ---------- Carousel and other existing code below (unchanged) ----------
    const sliderTrack = document.getElementById('sliderTrack');
    const slides = Array.from(sliderTrack.querySelectorAll('.slide'));
    const prevBtn = document.getElementById('carouselPrev');
    const nextBtn = document.getElementById('carouselNext');
    const indicatorsWrap = document.getElementById('carouselIndicators');
    let currentIndex = 0;
    let autoSlideTimer = null;
    let isHovering = false;

    function buildIndicators() {
      indicatorsWrap.innerHTML = '';
      slides.forEach((s, i) => {
        const dot = document.createElement('div');
        dot.className = 'indicator' + (i === 0 ? ' active' : '');
        dot.setAttribute('data-index', i);
        dot.addEventListener('click', () => {
          goToSlide(i);
          resetAutoSlide();
        });
        indicatorsWrap.appendChild(dot);
      });
    }

    function updateIndicators() {
      const dots = Array.from(indicatorsWrap.children);
      dots.forEach((d, i) => {
        d.classList.toggle('active', i === currentIndex);
      });
    }

    function goToSlide(index) {
      if (index < 0) index = slides.length - 1;
      if (index >= slides.length) index = 0;
      currentIndex = index;
      sliderTrack.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
      updateIndicators();
    }
    prevBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      goToSlide(currentIndex - 1);
      resetAutoSlide();
    });
    nextBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      goToSlide(currentIndex + 1);
      resetAutoSlide();
    });
    sliderTrack.addEventListener('touchstart', touchStart, {
      passive: true
    });
    sliderTrack.addEventListener('touchmove', touchMove, {
      passive: true
    });
    sliderTrack.addEventListener('touchend', touchEnd);
    let touchStartX = 0;
    let touchDiff = 0;

    function touchStart(e) {
      touchStartX = e.touches[0].clientX;
      touchDiff = 0;
    }

    function touchMove(e) {
      if (e.touches && e.touches.length) {
        touchDiff = e.touches[0].clientX - touchStartX;
      }
    }

    function touchEnd() {
      if (touchDiff > 50) {
        goToSlide(currentIndex - 1);
        resetAutoSlide();
      } else if (touchDiff < -50) {
        goToSlide(currentIndex + 1);
        resetAutoSlide();
      }
      touchDiff = 0;
    }

    function startAutoSlide() {
      stopAutoSlide();
      autoSlideTimer = setInterval(() => {
        if (isHovering) return;
        goToSlide(currentIndex + 1);
      }, 4500);
    }

    function stopAutoSlide() {
      if (autoSlideTimer) {
        clearInterval(autoSlideTimer);
        autoSlideTimer = null;
      }
    }

    function resetAutoSlide() {
      startAutoSlide();
    }
    const viewport = document.querySelector('.carousel-viewport');
    viewport.addEventListener('mouseenter', () => {
      isHovering = true;
    });
    viewport.addEventListener('mouseleave', () => {
      isHovering = false;
    });
    buildIndicators();
    goToSlide(0);
    startAutoSlide();
    window.addEventListener('resize', () => {
      sliderTrack.style.transition = 'none';
      sliderTrack.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
      setTimeout(() => {
        sliderTrack.style.transition = '';
      }, 60);
    });
  </script>
</body>

</html>