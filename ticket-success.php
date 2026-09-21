<?php
// ticket-success.php - Royal E-Ticket Confirmation & Printable Voucher
require_once 'config/db.php';
$pageTitle = "Royal E-Pass Confirmation & Entry Voucher";
include 'includes/header.php';

$bookingRef = isset($_GET['ref']) ? trim($_GET['ref']) : '';

if (empty($bookingRef)) {
    set_flash('danger', 'No booking reference specified.');
    header("Location: events.php");
    exit;
}

$stmt = $pdo->prepare("SELECT b.*, e.title AS event_title, e.event_date, e.event_time, e.venue, e.image, 
                              c.category_name, u.fullname, u.email, u.phone 
                       FROM bookings b 
                       JOIN events e ON b.event_id = e.id 
                       JOIN categories c ON e.category_id = c.id 
                       JOIN users u ON b.user_id = u.id 
                       WHERE b.booking_reference = ?");
$stmt->execute([$bookingRef]);
$booking = $stmt->fetch();

if (!$booking) {
    set_flash('danger', 'Booking reference not found in the database.');
    header("Location: events.php");
    exit;
}

$waShareText = "Hello Bhakti Events, I have confirmed my booking for '{$booking['event_title']}' with reference {$booking['booking_reference']}. Please confirm venue concierge details.";
?>

<div class="ticket-wrapper">
  
  <!-- Success Alert Header -->
  <div style="text-align:center; margin-bottom:32px;" class="no-print">
    <div style="width:72px; height:72px; border-radius:50%; background:var(--ivory-soft); color:var(--gold); border:2px solid var(--gold); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:32px; box-shadow:0 10px 25px rgba(197,160,89,0.25);">
      <i class="fa-solid fa-crown"></i>
    </div>
    <h2 style="font-size:30px; margin-bottom:6px; font-family:var(--font-heading); color:var(--burgundy);">Royal Pass Confirmed!</h2>
    <p style="color:var(--charcoal-muted); font-size:15px;">Your entry passes have been securely issued and reserved under your name.</p>
  </div>

  <!-- Printable E-Ticket Card -->
  <div class="ticket-card" id="printableTicket" style="border:2px solid var(--gold); box-shadow:0 15px 35px rgba(122,28,46,0.12);">
    
    <!-- Ticket Top Header -->
    <div class="ticket-top" style="background:linear-gradient(135deg, var(--burgundy-dark) 0%, var(--burgundy) 100%);">
      <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
          <div style="width:28px; height:28px; border-radius:50%; background:var(--gold); color:var(--burgundy-dark); display:flex; align-items:center; justify-content:center; font-size:13px;">
            <i class="fa-solid fa-crown"></i>
          </div>
          <span style="font-weight:800; font-size:18px; letter-spacing:0.5px; font-family:var(--font-heading); color:#fff;">
            BHAKTI EVENTS <span style="color:var(--gold-light);">&bull; ROYAL PASS</span>
          </span>
        </div>
        <span style="font-size:12px; color:#F5EBE6;">Official Admission Voucher &bull; Est. 2014 &bull; Gondal, Gujarat</span>
      </div>
      <div>
        <span class="ticket-status-stamp" style="background:rgba(197,160,89,0.2); color:var(--gold-light); border-color:var(--gold);">
          CONFIRMED &bull; ISSUED
        </span>
      </div>
    </div>

    <!-- Ticket Body Details -->
    <div class="ticket-body">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
        <div>
          <span class="category-badge" style="position:static; margin-bottom:8px; display:inline-block; background:var(--burgundy); color:var(--gold-light); border:1px solid var(--gold);">
            <i class="fa-solid fa-crown me-1" style="font-size:10px;"></i>
            <?= e($booking['category_name']) ?>
          </span>
          <h2 style="font-size:24px; color:var(--burgundy); margin:4px 0 6px; font-family:var(--font-heading);"><?= e($booking['event_title']) ?></h2>
          <p style="color:var(--charcoal-muted); font-size:14px; margin:0;">
            <i class="fa-solid fa-location-dot me-1" style="color:var(--gold);"></i> <?= e($booking['venue']) ?>
          </p>
        </div>
      </div>

      <div class="ticket-grid">
        <div class="ticket-field">
          <span class="label">Pass Reference</span>
          <span class="value" style="color:var(--burgundy); font-family:monospace; font-size:16px; font-weight:700;">
            <?= e($booking['booking_reference']) ?>
          </span>
        </div>

        <div class="ticket-field">
          <span class="label">Celebration Date &amp; Time</span>
          <span class="value" style="color:var(--charcoal); font-weight:600;">
            <?= format_date($booking['event_date']) ?> at <?= format_time($booking['event_time']) ?>
          </span>
        </div>

        <div class="ticket-field">
          <span class="label">Primary Guest</span>
          <span class="value" style="color:var(--burgundy); font-weight:600;"><?= e($booking['fullname']) ?></span>
          <div style="font-size:12px; color:var(--charcoal-muted);"><?= e($booking['email']) ?></div>
        </div>

        <div class="ticket-field">
          <span class="label">Reserved Passes</span>
          <span class="value" style="color:var(--charcoal); font-weight:600;"><?= $booking['ticket_quantity'] ?> <?= $booking['ticket_quantity'] > 1 ? 'Admit Passes' : 'Admit Pass' ?></span>
        </div>

        <div class="ticket-field">
          <span class="label">Total Amount</span>
          <span class="value" style="color:var(--burgundy); font-size:19px; font-weight:700;">
            <?= format_price($booking['total_amount']) ?>
          </span>
        </div>

        <div class="ticket-field">
          <span class="label">Payment Status</span>
          <span class="value" style="color:#059669; font-weight:600;">
            <i class="fa-solid fa-circle-check me-1" style="color:#059669;"></i> <?= e($booking['payment_method']) ?>
          </span>
        </div>
      </div>

      <!-- Barcode & QR Code Section -->
      <div class="ticket-barcode-box" style="border-top:1px dashed var(--gold-border); margin-top:24px; padding-top:20px;">
        <div style="display:flex; flex-direction:column; gap:6px;">
          <!-- Realistic Barcode Simulation -->
          <svg width="220" height="42" xmlns="http://www.w3.org/2000/svg" style="opacity:0.9;">
            <g fill="#7A1C2E">
              <rect x="0" y="0" width="3" height="42"/>
              <rect x="5" y="0" width="1.5" height="42"/>
              <rect x="9" y="0" width="4" height="42"/>
              <rect x="16" y="0" width="2" height="42"/>
              <rect x="21" y="0" width="1" height="42"/>
              <rect x="25" y="0" width="5" height="42"/>
              <rect x="33" y="0" width="2" height="42"/>
              <rect x="38" y="0" width="3.5" height="42"/>
              <rect x="44" y="0" width="1.5" height="42"/>
              <rect x="48" y="0" width="4" height="42"/>
              <rect x="55" y="0" width="2" height="42"/>
              <rect x="60" y="0" width="3" height="42"/>
              <rect x="66" y="0" width="1" height="42"/>
              <rect x="70" y="0" width="4" height="42"/>
              <rect x="77" y="0" width="2.5" height="42"/>
              <rect x="83" y="0" width="3" height="42"/>
              <rect x="89" y="0" width="1.5" height="42"/>
              <rect x="94" y="0" width="5" height="42"/>
              <rect x="102" y="0" width="2" height="42"/>
              <rect x="107" y="0" width="3" height="42"/>
              <rect x="113" y="0" width="1" height="42"/>
              <rect x="117" y="0" width="4" height="42"/>
              <rect x="124" y="0" width="2" height="42"/>
              <rect x="129" y="0" width="3" height="42"/>
              <rect x="135" y="0" width="1.5" height="42"/>
              <rect x="140" y="0" width="4" height="42"/>
              <rect x="147" y="0" width="2" height="42"/>
              <rect x="152" y="0" width="5" height="42"/>
              <rect x="160" y="0" width="1.5" height="42"/>
              <rect x="164" y="0" width="3" height="42"/>
              <rect x="170" y="0" width="2" height="42"/>
              <rect x="175" y="0" width="4" height="42"/>
              <rect x="182" y="0" width="2.5" height="42"/>
              <rect x="187" y="0" width="1.5" height="42"/>
              <rect x="191" y="0" width="3" height="42"/>
              <rect x="197" y="0" width="4" height="42"/>
              <rect x="204" y="0" width="2" height="42"/>
              <rect x="209" y="0" width="3" height="42"/>
              <rect x="215" y="0" width="2" height="42"/>
            </g>
          </svg>
          <span style="font-family:monospace; font-size:11.5px; color:var(--burgundy); letter-spacing:2px; font-weight:700;">
            * <?= e($booking['booking_reference']) ?> *
          </span>
        </div>

        <!-- QR Code Simulation -->
        <div style="border:2px solid var(--gold); padding:8px; border-radius:8px; background:#fff; text-align:center;">
          <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--burgundy);">
            <rect width="5" height="5" x="3" y="3" rx="1"/>
            <rect width="5" height="5" x="16" y="3" rx="1"/>
            <rect width="5" height="5" x="3" y="16" rx="1"/>
            <path d="M21 16h-3a2 2 0 0 0-2 2v3"/>
            <path d="M21 21v.01"/>
            <path d="M12 7v3a2 2 0 0 1-2 2H7"/>
            <path d="M3 12h.01"/>
            <path d="M12 3h.01"/>
            <path d="M12 16v.01"/>
            <path d="M16 12h1"/>
            <path d="M21 12v.01"/>
            <path d="M12 21v-1"/>
          </svg>
          <span style="display:block; font-size:9px; color:var(--gold); font-weight:700; letter-spacing:0.5px;">SCAN AT ENTRY</span>
        </div>
      </div>
    </div>
  </div>

  <!-- WhatsApp Concierge Assistance Bar -->
  <div style="margin-top:24px; text-align:center; background:#fff; border:1px solid var(--gold-border); border-radius:12px; padding:18px;" class="no-print">
    <span style="color:var(--charcoal-muted); font-size:14px; display:block; margin-bottom:8px;">
      Need special banquet accommodations, valet assistance, or VIP table upgrades?
    </span>
    <a href="https://wa.me/919825661046?text=<?= urlencode($waShareText) ?>" 
       target="_blank" 
       rel="noopener" 
       class="btn btn-sm btn-outline-primary" 
       style="border-color:var(--gold); color:var(--burgundy); padding:8px 18px;">
      <i class="fa-brands fa-whatsapp me-2" style="color:#25D366; font-size:16px;"></i> Chat with Gondal Concierge: +91 9825661046
    </a>
  </div>

  <!-- Ticket Actions Bar (Hidden when printing) -->
  <div class="ticket-actions no-print" style="margin-top:24px;">
    <button type="button" class="btn btn-primary" onclick="window.print()">
      <i class="fa-solid fa-print"></i> Print / Save Royal E-Pass
    </button>
    <a href="user/my-bookings.php" class="btn btn-outline-primary" style="border-color:var(--gold); color:var(--burgundy);">
      <i class="fa-solid fa-receipt"></i> View In My Bookings
    </a>
    <a href="events.php" class="btn btn-secondary">
      <i class="fa-solid fa-compass"></i> Discover More Celebrations
    </a>
  </div>

</div>

<?php include 'includes/footer.php'; ?>
