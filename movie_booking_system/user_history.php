<?php
require 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
  header('Location: login.php');
  exit;
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT b.*, m.name AS movie_name, m.image AS movie_image FROM bookings b JOIN movies m ON b.movie_id = m.id WHERE b.user_id = ? ORDER BY b.booking_time DESC");
$stmt->execute([$user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>My Bookings</title>
  <link rel="stylesheet" href="style.css">
  <style>
    :root {
      --bg: #f6f8fb;
      --card: #ffffff;
      --muted: #6b7280;
      --accent: #0b5cff;
      --glass: rgba(255, 255, 255, 0.6);
      --radius: 12px;
      --shadow: 0 12px 40px rgba(2, 6, 23, 0.08);
    }

    body {
      margin: 0;
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: linear-gradient(180deg, var(--bg), #fff);
      color: #0b1220;
      -webkit-font-smoothing: antialiased;
    }

    .container {
      max-width: 1200px;
      margin: 34px auto;
      padding: 24px;
    }

    h2 {
      margin: 0 0 18px 0;
      font-size: 26px;
      letter-spacing: -0.2px;
    }

    .toolbar {
      display: flex;
      gap: 12px;
      align-items: center;
      margin-bottom: 18px;
      flex-wrap: wrap;
    }

    .search {
      display: flex;
      align-items: center;
      gap: 8px;
      background: var(--card);
      border-radius: 999px;
      padding: 8px 12px;
      box-shadow: 0 6px 18px rgba(2, 6, 23, 0.04);
      border: 1px solid rgba(11, 92, 255, 0.04);
    }

    .search input {
      border: 0;
      outline: 0;
      width: 320px;
      font-size: 14px;
      background: transparent;
      padding: 6px 4px;
    }

    .controls {
      margin-left: auto;
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .select,
    .btn {
      padding: 8px 12px;
      border-radius: 10px;
      border: 1px solid rgba(2, 6, 23, 0.06);
      background: var(--card);
      cursor: pointer;
      font-weight: 600;
      color: #0b1220;
      box-shadow: 0 6px 18px rgba(2, 6, 23, 0.04);
    }

    .select small {
      color: var(--muted);
      font-weight: 600;
      display: block;
      font-size: 12px
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
    }

    .booking-card {
      background: linear-gradient(180deg, var(--card), #fbfdff);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: var(--shadow);
      display: flex;
      flex-direction: column;
      transition: transform .14s ease, box-shadow .14s ease;
      border: 1px solid rgba(2, 6, 23, 0.03);
    }

    .booking-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 30px 60px rgba(2, 6, 23, 0.10)
    }

    .thumb {
      height: 150px;
      background: #000;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .thumb img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
      display: block;
      user-select: none;
      -webkit-user-drag: none;
    }

    .card-body {
      padding: 14px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .movie-title {
      font-size: 16px;
      font-weight: 800;
      color: #0b1220;
      display: flex;
      justify-content: space-between;
      gap: 8px;
      align-items: center;
    }

    .meta {
      display: flex;
      gap: 10px;
      align-items: center;
      color: var(--muted);
      font-weight: 600;
      font-size: 13px;
    }

    .tags {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 6px;
    }

    .pill {
      background: linear-gradient(180deg, #fff, #f3f6ff);
      padding: 6px 8px;
      border-radius: 999px;
      font-weight: 700;
      font-size: 13px;
      border: 1px solid rgba(11, 92, 255, 0.04);
    }

    .actions {
      display: flex;
      gap: 8px;
      margin-top: 8px;
    }

    .btn-action {
      flex: 1;
      padding: 10px 12px;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      font-weight: 800;
    }

    .btn-primary {
      background: var(--accent);
      color: #fff;
      box-shadow: 0 10px 28px rgba(11, 92, 255, 0.12);
    }

    .btn-ghost {
      background: transparent;
      color: var(--muted);
      border: 1px solid rgba(2, 6, 23, 0.06);
    }

    .empty {
      padding: 40px;
      background: linear-gradient(180deg, #fff, #fbfdff);
      border-radius: 12px;
      border: 1px dashed rgba(11, 92, 255, 0.06);
      text-align: center;
      color: var(--muted);
      font-weight: 700;
    }

    /* modal */
    #detailModal {
      display: none;
      position: fixed;
      left: 0;
      right: 0;
      top: 0;
      bottom: 0;
      background: linear-gradient(180deg, rgba(2, 6, 23, 0.5), rgba(2, 6, 23, 0.4));
      z-index: 1100;
      padding: 32px;
      box-sizing: border-box;
    }

    .modal-inner {
      max-width: 920px;
      margin: 40px auto;
      border-radius: 12px;
      overflow: hidden;
      background: linear-gradient(180deg, #fff, #fbfdff);
      box-shadow: 0 30px 80px rgba(2, 6, 23, 0.25);
      display: flex;
      gap: 0;
      flex-direction: column;
    }

    .modal-top {
      display: flex;
      gap: 20px;
    }

    .modal-thumb {
      flex: 0 0 350px;
      background: #000;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 220px;
    }

    .modal-thumb img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }

    .modal-details {
      padding: 18px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .modal-actions {
      display: flex;
      gap: 10px;
      margin-top: 12px;
    }

    .close-x {
      position: absolute;
      right: 14px;
      top: 14px;
      background: #ef4444;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 8px 10px;
      cursor: pointer;
      font-weight: 800;
    }

    .small {
      font-size: 13px;
      color: var(--muted);
      font-weight: 700;
    }

    .copy-success {
      color: #065f46;
      background: #ecfdf5;
      padding: 8px 10px;
      border-radius: 8px;
      border: 1px solid rgba(16, 185, 129, 0.08);
      display: inline-block;
      font-weight: 800;
    }

    @media (max-width:900px) {
      .modal-top {
        flex-direction: column;
      }

      .modal-thumb {
        flex: 0 0 auto;
        height: 240px;
      }
    }

    /* receipt template hidden area for pdf rendering */
    .receipt-template {
      width: 792px;
      background: #ffffff;
      padding: 28px;
      box-sizing: border-box;
      font-family: Arial, Helvetica, sans-serif;
    }

    .receipt-header {
      display: flex;
      gap: 12px;
      align-items: center;
      margin-bottom: 12px;
    }

    .receipt-header img {
      width: 120px;
      height: 160px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid #eee;
    }

    .receipt-title {
      font-weight: 900;
      font-size: 20px;
      color: #0b1220;
    }

    .receipt-block {
      margin-top: 12px;
      display: flex;
      gap: 12px;
      align-items: flex-start;
      flex-wrap: wrap;
    }

    .receipt-label {
      font-weight: 700;
      width: 140px;
      color: #374151;
    }

    .receipt-value {
      font-weight: 800;
      color: #0b1220;
    }
  </style>
  <!-- jsPDF and html2canvas -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body>
  <?php include 'navbar.php'; ?>
  <div class="container">
    <h2>My Bookings</h2>

    <div class="toolbar" role="region" aria-label="booking controls">
      <div class="search" role="search">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden>
          <path d="M21 21l-4.35-4.35" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <input id="q" type="text" placeholder="Search by movie name or seat" />
        <button class="select" id="clearSearch" title="Clear" style="display:none">Clear</button>
      </div>

      <div class="controls" aria-hidden>
        <div class="select" id="sortSelect">
          <small>Sort</small>
          <select id="sortBy" style="border:0;background:transparent;outline:0;font-weight:800;cursor:pointer">
            <option value="new">Newest first</option>
            <option value="old">Oldest first</option>
            <option value="seats_desc">Most seats</option>
            <option value="seats_asc">Least seats</option>
          </select>
        </div>

      </div>
    </div>

    <?php if (empty($rows)): ?>
      <div class="empty">
        You have no bookings yet. <a href="user.php" style="color:var(--accent);text-decoration:none;margin-left:6px">Book a ticket</a>.
      </div>
    <?php else: ?>
      <div class="grid" id="bookGrid" aria-live="polite">
        <?php foreach ($rows as $r): ?>
          <?php
          $movie_image = $r['movie_image'] ?: '';
          $thumb = htmlspecialchars($movie_image);
          $seat_numbers = htmlspecialchars($r['seat_numbers']);
          $movie_name = htmlspecialchars($r['movie_name']);
          $seats = (int)$r['seats'];
          $book_time = htmlspecialchars($r['booking_time']);
          $booking_id = (int)$r['id'];
          $total_amount = isset($r['total_amount']) && is_numeric($r['total_amount']) ? (int)$r['total_amount'] : 0;
          ?>
          <article class="booking-card" data-movie="<?php echo strtolower($movie_name); ?>" data-seats="<?php echo $seats; ?>" data-time="<?php echo $book_time; ?>" data-id="<?php echo $booking_id; ?>">
            <div class="thumb" role="img" aria-label="<?php echo $movie_name; ?>">
              <?php if ($thumb): ?>
                <img src="<?php echo $thumb; ?>" alt="<?php echo $movie_name; ?>">
              <?php else: ?>
                <div style="color:#fff;font-weight:800">No image</div>
              <?php endif; ?>
            </div>
            <div class="card-body">
              <div class="movie-title">
                <span><?php echo $movie_name; ?></span>
                <span class="small"><?php echo date('d M Y, H:i', strtotime($r['booking_time'])); ?></span>
              </div>

              <div class="meta">
                <div class="pill">Seats: <?php echo $seats; ?></div>
                <div class="pill">Seat Numbers: <strong style="margin-left:6px;"><?php echo $seat_numbers; ?></strong></div>
              </div>

              <div class="tags" aria-hidden>
                <div class="pill">Booking ID: <?php echo $booking_id; ?></div>
                <div class="pill">Paid: Rs.<?php echo $total_amount; ?></div>
              </div>

              <div class="actions">
                <button class="btn-action btn-primary" data-action="details" data-id="<?php echo $booking_id; ?>">Details</button>
                <button class="btn-action btn-ghost" data-action="download" data-id="<?php echo $booking_id; ?>" data-movie="<?php echo $movie_name; ?>" data-seats="<?php echo $seat_numbers; ?>" data-time="<?php echo $book_time; ?>" data-amount="<?php echo $total_amount; ?>" data-img="<?php echo $thumb; ?>">Download</button>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div id="detailModal" aria-hidden="true">
    <div class="modal-inner" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
      <button class="close-x" id="closeModal">Close</button>
      <div class="modal-top">
        <div class="modal-thumb" id="modalThumb"><img src="" alt=""></div>
        <div class="modal-details">
          <div id="modalTitle" style="font-size:20px;font-weight:900"></div>
          <div class="small" id="modalBookingTime"></div>
          <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:12px">
            <div class="pill" id="modalSeats"></div>
            <div class="pill" id="modalQty"></div>
            <div class="pill" id="modalAmount"></div>
            <div class="pill" id="modalBookingId"></div>
          </div>

          <div style="margin-top:12px" id="modalSeatList"></div>

          <div class="modal-actions">
            <button class="btn select" id="modalCopySeats">Copy seats</button>
            <button class="btn select" id="modalDownload">Download receipt</button>
            <span id="copyFeedback" style="display:none" class="copy-success">Copied!</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function() {
      const grid = document.getElementById('bookGrid');
      const q = document.getElementById('q');
      const clearSearch = document.getElementById('clearSearch');
      const sortBy = document.getElementById('sortBy');
      const modal = document.getElementById('detailModal');
      const modalThumb = document.getElementById('modalThumb').querySelector('img');
      const modalTitle = document.getElementById('modalTitle');
      const modalBookingTime = document.getElementById('modalBookingTime');
      const modalSeats = document.getElementById('modalSeats');
      const modalQty = document.getElementById('modalQty');
      const modalAmount = document.getElementById('modalAmount');
      const modalBookingId = document.getElementById('modalBookingId');
      const modalSeatList = document.getElementById('modalSeatList');
      const modalCopySeats = document.getElementById('modalCopySeats');
      const modalDownload = document.getElementById('modalDownload');
      const closeModal = document.getElementById('closeModal');
      const copyFeedback = document.getElementById('copyFeedback');

      function listCards() {
        return Array.from(document.querySelectorAll('.booking-card'));
      }

      function filterAndSort() {
        const term = q.value.trim().toLowerCase();
        let cards = listCards();

        cards.forEach(c => {
          const movie = c.dataset.movie || '';
          const seats = (c.querySelector('.pill strong') || {
            innerText: ''
          }).innerText || '';
          const match = movie.includes(term) || seats.toLowerCase().includes(term);
          c.style.display = match ? '' : 'none';
        });

        const visible = cards.filter(c => c.style.display !== 'none');
        const sortVal = sortBy.value;
        visible.sort((a, b) => {
          if (sortVal === 'new') {
            return new Date(b.dataset.time) - new Date(a.dataset.time);
          }
          if (sortVal === 'old') {
            return new Date(a.dataset.time) - new Date(b.dataset.time);
          }
          if (sortVal === 'seats_desc') {
            return (b.dataset.seats | 0) - (a.dataset.seats | 0);
          }
          if (sortVal === 'seats_asc') {
            return (a.dataset.seats | 0) - (b.dataset.seats | 0);
          }
          return 0;
        });

        visible.forEach(node => grid.appendChild(node));
      }

      q.addEventListener('input', () => {
        filterAndSort();
        clearSearch.style.display = q.value ? 'inline-block' : 'none';
      });

      clearSearch.addEventListener('click', () => {
        q.value = '';
        clearSearch.style.display = 'none';
        filterAndSort();
        q.focus();
      });

      sortBy.addEventListener('change', () => filterAndSort());

      // helper: build a receipt element (hidden) to render to canvas
      function buildReceiptElement(details) {
        const el = document.createElement('div');
        el.className = 'receipt-template';
        el.style.width = '792px';
        el.innerHTML = `
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
            <div style="font-weight:900;font-size:22px;color:#0b1220">Movie Ticket Receipt</div>
            <div style="text-align:right;color:#374151">Booking ID: ${details.id}</div>
          </div>
          <div class="receipt-header">
            <img src="${details.img}" crossorigin="anonymous" onerror="this.style.display='none'">
            <div>
              <div class="receipt-title">${details.movie}</div>
              <div style="margin-top:8px;color:#525f7f">Booked at: ${details.time}</div>
              <div style="margin-top:6px;color:#525f7f">Generated: ${new Date().toLocaleString()}</div>
            </div>
          </div>
          <div class="receipt-block">
            <div class="receipt-label">Seat Numbers</div>
            <div class="receipt-value">${details.seats}</div>
          </div>
          <div class="receipt-block">
            <div class="receipt-label">Quantity</div>
            <div class="receipt-value">${details.qty}</div>
          </div>
          <div class="receipt-block">
            <div class="receipt-label">Total Paid</div>
            <div class="receipt-value">Rs. ${details.amount}</div>
          </div>
          <div style="margin-top:18px;font-size:12px;color:#6b7280">Please carry a valid ID at the theatre. This receipt is for reference only.</div>
        `;
        // keep off screen
        el.style.position = 'fixed';
        el.style.left = '-9999px';
        el.style.top = '-9999px';
        document.body.appendChild(el);
        return el;
      }

      // generate pdf from details object: {id,movie,seats,qty,time,amount,img}
      async function generatePDF(details) {
        const receiptEl = buildReceiptElement(details);
        try {
          const canvas = await html2canvas(receiptEl, {
            scale: 2,
            useCORS: true,
            allowTaint: false,
            logging: false
          });
          const imgData = canvas.toDataURL('image/png');

          const {
            jsPDF
          } = window.jspdf;
          // A4 dimensions in mm
          const pdf = new jsPDF('p', 'mm', 'a4');
          const pageWidth = pdf.internal.pageSize.getWidth();
          const pageHeight = pdf.internal.pageSize.getHeight();

          // compute image dimensions (preserve aspect ratio)
          const imgProps = {
            widthPx: canvas.width,
            heightPx: canvas.height
          };
          const pxToMm = (px) => (px * 25.4) / 96; // approximate conversion using 96 DPI
          const imgWidthMm = pxToMm(imgProps.widthPx);
          const imgHeightMm = pxToMm(imgProps.heightPx);
          const maxWidth = pageWidth - 20; // margins 10mm
          const scale = Math.min(1, maxWidth / imgWidthMm);
          const finalWidth = imgWidthMm * scale;
          const finalHeight = imgHeightMm * scale;
          const x = (pageWidth - finalWidth) / 2;
          const y = 15;

          pdf.addImage(imgData, 'PNG', x, y, finalWidth, finalHeight);
          const fileName = `booking_${details.id}.pdf`;
          pdf.save(fileName);
        } catch (err) {
          console.error('PDF generation failed', err);
          alert('Failed to generate PDF. Try again.');
        } finally {
          if (receiptEl && receiptEl.parentNode) receiptEl.parentNode.removeChild(receiptEl);
        }
      }

      // delegated click handling
      document.addEventListener('click', (ev) => {
        const btn = ev.target.closest('button');
        if (!btn) return;
        const action = btn.dataset.action;
        if (!action) return;

        const card = btn.closest('.booking-card');
        if (!card) return;

        if (action === 'details') {
          const id = card.dataset.id;
          const movie = card.querySelector('.movie-title span').innerText;
          const time = card.querySelector('.movie-title .small') ? card.querySelector('.movie-title .small').innerText : '';
          const seatsText = card.querySelector('.pill strong') ? card.querySelector('.pill strong').innerText : '';
          const qty = card.dataset.seats || '0';
          const img = card.querySelector('.thumb img') ? card.querySelector('.thumb img').src : '';
          const amount = card.querySelector('.tags .pill:nth-child(2)') ? card.querySelector('.tags .pill:nth-child(2)').innerText.replace('Paid: Rs.', '') : '';

          modalThumb.src = img || '';
          modalTitle.innerText = movie;
          modalBookingTime.innerText = 'Booked at: ' + time;
          modalSeats.innerText = 'Seat Nos: ' + seatsText;
          modalQty.innerText = 'Qty: ' + qty;
          modalBookingId.innerText = 'Booking ID: ' + id;
          modalAmount.innerText = amount ? 'Rs.' + amount : 'Rs. 0';
          modalSeatList.innerHTML = '<div style="font-weight:800;margin-top:6px">Seats:</div><div style="margin-top:6px;font-weight:700">' + seatsText + '</div>';

          modalCopySeats.dataset.seats = seatsText;
          modalDownload.dataset.id = id;
          modalDownload.dataset.movie = movie;
          modalDownload.dataset.seats = seatsText;
          modalDownload.dataset.time = time;
          modalDownload.dataset.amount = amount || '0';
          modalDownload.dataset.img = img || '';
          modal.style.display = 'block';
          modal.setAttribute('aria-hidden', 'false');
        } else if (action === 'download') {
          const id = btn.dataset.id;
          const movie = btn.dataset.movie || '';
          const seats = btn.dataset.seats || '';
          const time = btn.dataset.time || '';
          const amount = btn.dataset.amount || '0';
          const img = btn.dataset.img || '';
          const qty = seats.split(',').filter(s => s.trim()).length || 0;
          generatePDF({
            id,
            movie,
            seats,
            qty,
            time,
            amount,
            img
          });
        }
      });

      // modal handlers
      closeModal.addEventListener('click', closeDetailModal);
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeDetailModal();
      });

      function closeDetailModal() {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        copyFeedback.style.display = 'none';
      }

      modalCopySeats.addEventListener('click', () => {
        const seats = modalCopySeats.dataset.seats || '';
        navigator.clipboard?.writeText(seats).then(() => {
          copyFeedback.style.display = 'inline-block';
          setTimeout(() => copyFeedback.style.display = 'none', 1600);
        }).catch(() => alert('Copy failed'));
      });

      modalDownload.addEventListener('click', () => {
        const id = modalDownload.dataset.id || 'receipt';
        const movie = modalDownload.dataset.movie || '';
        const seats = modalDownload.dataset.seats || '';
        const time = modalDownload.dataset.time || '';
        const amount = modalDownload.dataset.amount || '0';
        const img = modalDownload.dataset.img || '';
        const qty = seats.split(',').filter(s => s.trim()).length || 0;
        generatePDF({
          id,
          movie,
          seats,
          qty,
          time,
          amount,
          img
        });
      });

      filterAndSort();
    })();
  </script>
</body>

</html>