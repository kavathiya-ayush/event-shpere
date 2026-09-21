<?php
// event-details.php - Single Event Showcase & Interactive Booking Widget
require_once 'config/db.php';

$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($eventId <= 0) {
    header("Location: events.php");
    exit;
}

$stmt = $pdo->prepare("SELECT e.*, c.category_name 
                       FROM events e 
                       JOIN categories c ON e.category_id = c.id 
                       WHERE e.id = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch();

if (!$event) {
    set_flash('danger', 'Celebration not found or has been removed.');
    header("Location: events.php");
    exit;
}

$pageTitle = $event['title'];
include 'includes/header.php';

$isSoldOut = $event['available_seats'] <= 0;
$seatPercentage = ($event['total_seats'] > 0) ? round(($event['available_seats'] / $event['total_seats']) * 100) : 0;
$eventDate = new DateTime($event['event_date']);
$waText = "Hello Bhakti Events, I would like to inquire about passes/VIP tables for '{$event['title']}' on {$eventDate->format('d M, Y')}.";
?>

<!-- Event Hero Header -->
<section class="event-hero">
  <img src="assets/images/events/<?= e($event['image']) ?>" 
       onerror="this.src='assets/images/events/default-event.jpg'" 
       alt="<?= e($event['title']) ?>">
  
  <div class="event-hero-content">
    <div class="event-hero-container">
      <div style="display:flex; gap:10px; margin-bottom:14px; flex-wrap:wrap;">
        <span class="category-badge" style="position:static;">
          <i class="fa-solid fa-crown me-1"></i> <?= e($event['category_name']) ?>
        </span>
        <span class="status-badge <?= $event['status'] === 'upcoming' ? 'badge-success' : 'badge-secondary' ?>">
          <?= ucfirst($event['status']) ?>
        </span>
      </div>
      
      <h1><?= e($event['title']) ?></h1>
      
      <div style="display:flex; flex-wrap:wrap; gap:24px; color:#F5EBE6; font-size:15px; margin-top:12px;">
        <span><i class="fa-regular fa-calendar-days me-1" style="color:var(--gold-light);"></i> <?= format_date($event['event_date']) ?></span>
        <span><i class="fa-regular fa-clock me-1" style="color:var(--gold-light);"></i> <?= format_time($event['event_time']) ?></span>
        <span><i class="fa-solid fa-location-dot me-1" style="color:var(--gold-light);"></i> <?= e($event['venue']) ?></span>
      </div>
    </div>
  </div>
</section>

<!-- Event Details Layout -->
<div class="event-details-layout">
  
  <!-- Left Main Content Area -->
  <div class="event-main-content">
    
    <!-- Live Countdown Timer Banner -->
    <div class="countdown-banner" id="liveEventCountdown" data-target-date="<?= $event['event_date'] . ' ' . $event['event_time'] ?>">
      <div>
        <span style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:var(--gold-light); display:block; margin-bottom:2px;">
          <i class="fa-solid fa-hourglass-half me-1"></i> Celebration Commences In
        </span>
        <strong style="font-size:16px; font-family:var(--font-heading); color:#fff;"><?= format_date($event['event_date']) ?> &bull; <?= format_time($event['event_time']) ?></strong>
      </div>
      <div class="countdown-digits">
        <div class="countdown-segment">
          <div class="countdown-val" id="cdDays">00</div>
          <div class="countdown-lbl">Days</div>
        </div>
        <div class="countdown-segment">
          <div class="countdown-val" id="cdHours">00</div>
          <div class="countdown-lbl">Hours</div>
        </div>
        <div class="countdown-segment">
          <div class="countdown-val" id="cdMins">00</div>
          <div class="countdown-lbl">Mins</div>
        </div>
        <div class="countdown-segment">
          <div class="countdown-val" id="cdSecs">00</div>
          <div class="countdown-lbl">Secs</div>
        </div>
      </div>
    </div>

    <h3>Celebration Overview</h3>
    <div class="gold-divider" style="margin:10px 0 18px 0;"></div>
    <p style="font-size:15.5px; line-height:1.8; color:var(--charcoal);"><?= nl2br(e($event['description'])) ?></p>

    <!-- Celebration Itinerary & Schedule -->
    <h3 style="margin-top:38px;">Program Schedule &amp; Royal Itinerary</h3>
    <div class="gold-divider" style="margin:10px 0 20px 0;"></div>

    <div style="display:flex; flex-direction:column; gap:14px; margin-bottom:35px;">
      <div style="background:#fff; border:1px solid var(--gold-border); border-left:4px solid var(--gold); border-radius:10px; padding:16px 20px; box-shadow:var(--shadow-sm); display:flex; gap:16px; align-items:center;">
        <span style="font-weight:700; color:var(--burgundy); font-size:15px; min-width:85px;">Phase I</span>
        <div>
          <strong style="color:var(--burgundy); font-size:14.5px; display:block;">Shahi Welcome &amp; Traditional Aarti Reception</strong>
          <span style="font-size:13px; color:var(--charcoal-muted);">Gates open, red carpet entry, floral garlands, and royal rose sharbat bar.</span>
        </div>
      </div>

      <div style="background:#fff; border:1px solid var(--gold-border); border-left:4px solid var(--burgundy); border-radius:10px; padding:16px 20px; box-shadow:var(--shadow-sm); display:flex; gap:16px; align-items:center;">
        <span style="font-weight:700; color:var(--burgundy); font-size:15px; min-width:85px;">Phase II</span>
        <div>
          <strong style="color:var(--burgundy); font-size:14.5px; display:block;">Main Stage Performance &amp; Ceremonial Unveiling</strong>
          <span style="font-size:13px; color:var(--charcoal-muted);">Live orchestral Mehfil / Mandap Vedic mantras / High-energy Raas choreographed performance.</span>
        </div>
      </div>

      <div style="background:#fff; border:1px solid var(--gold-border); border-left:4px solid var(--gold); border-radius:10px; padding:16px 20px; box-shadow:var(--shadow-sm); display:flex; gap:16px; align-items:center;">
        <span style="font-weight:700; color:var(--burgundy); font-size:15px; min-width:85px;">Phase III</span>
        <div>
          <strong style="color:var(--burgundy); font-size:14.5px; display:block;">Shahi Rajwadi Multi-Course Banquet Feast</strong>
          <span style="font-size:13px; color:var(--charcoal-muted);">Royal dining pavilion open with authentic regional delicacies, live chaat stalls, and desserts.</span>
        </div>
      </div>
    </div>

    <h3>Royal Hospitality &amp; Experience Inclusions</h3>
    <div class="gold-divider" style="margin:10px 0 20px 0;"></div>
    
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:18px; margin:20px 0 35px;">
      <div style="display:flex; align-items:flex-start; gap:14px; background:#fff; padding:18px; border-radius:10px; border:1px solid var(--gold-border); box-shadow:var(--shadow-sm);">
        <i class="fa-solid fa-gem" style="color:var(--gold); font-size:20px; margin-top:3px;"></i>
        <div>
          <strong style="font-size:15px; color:var(--burgundy-dark);">Traditional Shahi Welcome</strong>
          <p style="font-size:13px; color:var(--charcoal-muted); margin:3px 0 0;">Floral garland, traditional tilak, and welcome rose sherbet reception.</p>
        </div>
      </div>
      
      <div style="display:flex; align-items:flex-start; gap:14px; background:#fff; padding:18px; border-radius:10px; border:1px solid var(--gold-border); box-shadow:var(--shadow-sm);">
        <i class="fa-solid fa-couch" style="color:var(--gold); font-size:20px; margin-top:3px;"></i>
        <div>
          <strong style="font-size:15px; color:var(--burgundy-dark);">Reserved Royal Seating</strong>
          <p style="font-size:13px; color:var(--charcoal-muted); margin:3px 0 0;">Guaranteed premium tiered seating with unobstructed stage acoustics.</p>
        </div>
      </div>

      <div style="display:flex; align-items:flex-start; gap:14px; background:#fff; padding:18px; border-radius:10px; border:1px solid var(--gold-border); box-shadow:var(--shadow-sm);">
        <i class="fa-solid fa-utensils" style="color:var(--gold); font-size:20px; margin-top:3px;"></i>
        <div>
          <strong style="font-size:15px; color:var(--burgundy-dark);">Shahi Rajwadi Feast</strong>
          <p style="font-size:13px; color:var(--charcoal-muted); margin:3px 0 0;">Access to royal culinary stalls, live delicacies, and dessert counters.</p>
        </div>
      </div>

      <div style="display:flex; align-items:flex-start; gap:14px; background:#fff; padding:18px; border-radius:10px; border:1px solid var(--gold-border); box-shadow:var(--shadow-sm);">
        <i class="fa-solid fa-car-side" style="color:var(--gold); font-size:20px; margin-top:3px;"></i>
        <div>
          <strong style="font-size:15px; color:var(--burgundy-dark);">Complimentary Valet Parking</strong>
          <p style="font-size:13px; color:var(--charcoal-muted); margin:3px 0 0;">Priority dedicated vehicle drop-off and security escort at the venue.</p>
        </div>
      </div>
    </div>

    <!-- Venue & Location Section -->
    <h3 style="margin-top:36px;">Royal Venue &amp; Route Guidance</h3>
    <div class="gold-divider" style="margin:10px 0 18px 0;"></div>
    
    <div style="background:#fff; border-radius:12px; border:1px solid var(--gold-border); overflow:hidden; margin-top:14px; box-shadow:var(--shadow-sm);">
      <div style="padding:20px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--gold-border); flex-wrap:wrap; gap:12px;">
        <div>
          <strong style="font-size:16px; color:var(--burgundy-dark); display:block;"><?= e($event['venue']) ?></strong>
          <span style="font-size:13.5px; color:var(--charcoal-muted);">Palace estate &amp; luxury banqueting premises</span>
        </div>
        <a href="https://maps.google.com/?q=<?= urlencode($event['venue']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary" style="border-color:var(--gold); color:var(--burgundy);">
          <i class="fa-solid fa-location-arrow me-1"></i> Open in Maps
        </a>
      </div>
      
      <!-- Simulated Royal Venue Display -->
      <div style="height:210px; background:linear-gradient(135deg, var(--burgundy-dark) 0%, #3B0A12 100%); display:flex; align-items:center; justify-content:center; color:#fff; flex-direction:column; gap:10px;">
        <div style="width:52px; height:52px; border-radius:50%; background:var(--gold); color:var(--burgundy-dark); display:flex; align-items:center; justify-content:center; font-size:22px; box-shadow:0 0 25px rgba(197,160,89,0.5);">
          <i class="fa-solid fa-location-dot"></i>
        </div>
        <span style="font-weight:700; font-size:16px; font-family:var(--font-heading); color:var(--gold-light);"><?= e($event['venue']) ?></span>
        <span style="font-size:13px; color:#F5EBE6;">Verified Royal Event Location &bull; Security &amp; Hospitality Managed by Bhakti Events</span>
      </div>
    </div>
  </div>

  <!-- Right Sticky Booking Card -->
  <aside>
    <div class="booking-card">
      <div class="booking-price-row">
        <div>
          <span class="label">Pass Contribution (Unit)</span>
          <div class="value" id="pricePerTicket" data-price="<?= floatval($event['ticket_price']) ?>" data-base-price="<?= floatval($event['ticket_price']) ?>">
            <?= format_price($event['ticket_price']) ?>
          </div>
        </div>
        <span class="badge-pill" style="font-size:11px; background:rgba(197,160,89,0.15); color:var(--burgundy); border-color:var(--gold);">
          <i class="fa-solid fa-crown me-1"></i> Verified Pass
        </span>
      </div>

      <!-- Live Seat Counter Bar -->
      <div class="seat-progress">
        <div style="display:flex; justify-content:space-between; font-size:13px; font-weight:600; margin-bottom:6px;">
          <span id="availableSeatsVal" data-max="<?= min(10, $event['available_seats']) ?>" style="color:<?= $event['available_seats'] < 35 ? 'var(--burgundy)' : 'var(--charcoal)' ?>;">
            <i class="fa-solid fa-chair me-1"></i> <?= $event['available_seats'] ?> Passes Remaining
          </span>
          <span style="color:var(--charcoal-muted);"><?= $event['total_seats'] ?> Total Capacity</span>
        </div>
        <div class="seat-progress-bar">
          <div class="seat-progress-fill" style="width:<?= $seatPercentage ?>%;"></div>
        </div>
      </div>

      <?php if ($isSoldOut): ?>
        <div style="background:var(--burgundy-soft); color:var(--burgundy); padding:18px; border-radius:8px; text-align:center; font-weight:700; border:1px solid var(--gold-border);">
          <i class="fa-solid fa-ban me-1"></i> RESERVATION CLOSED
          <p style="font-size:12.5px; margin:4px 0 0; font-weight:normal;">All passes have been allocated. Please contact our Gondal office for VIP waiting list inquiries.</p>
        </div>
      <?php else: ?>
        <form action="book-ticket.php" method="POST" class="booking-form">
          <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
          <input type="hidden" name="selected_tier" id="selectedTierInput" value="Royal Pass">

          <!-- Multi-Tier Pass Category Selection -->
          <div class="form-group">
            <label style="margin-bottom:8px; font-weight:600; display:block; font-size:13.5px; color:var(--charcoal);">
              Select Pass Category / Tier
            </label>
            <div class="pass-tier-grid">
              <div class="tier-option selected" data-multiplier="1" data-tier-name="Royal Pass">
                <div class="tier-left">
                  <input type="radio" name="pass_tier" value="Royal Pass" class="tier-radio" checked>
                  <div>
                    <span class="tier-title">Royal Pass</span>
                    <span class="tier-desc">Standard Tiered Seating &bull; E-Pass Entry</span>
                  </div>
                </div>
                <div class="tier-rate">1x</div>
              </div>

              <div class="tier-option" data-multiplier="1.6" data-tier-name="Gold Circle Pass">
                <div class="tier-left">
                  <input type="radio" name="pass_tier" value="Gold Circle Pass" class="tier-radio">
                  <div>
                    <span class="tier-title">Gold Circle Pass</span>
                    <span class="tier-desc">Front Row Seating &bull; Shahi Banquet Access</span>
                  </div>
                </div>
                <div class="tier-rate">1.6x</div>
              </div>

              <div class="tier-option" data-multiplier="2.8" data-tier-name="Shahi Diwan VIP Lounge">
                <div class="tier-left">
                  <input type="radio" name="pass_tier" value="Shahi Diwan VIP Lounge" class="tier-radio">
                  <div>
                    <span class="tier-title">Shahi Diwan VIP</span>
                    <span class="tier-desc">Velvet Sofa Lounge &bull; Valet &bull; Personal Butler</span>
                  </div>
                </div>
                <div class="tier-rate">2.8x</div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="ticketQuantity">Number of Passes (Max <?= min(10, $event['available_seats']) ?> passes)</label>
            <div class="quantity-control">
              <button type="button" class="quantity-btn" id="qtyMinus">&minus;</button>
              <input type="number" id="ticketQuantity" name="tickets" value="1" min="1" max="<?= min(10, $event['available_seats']) ?>" class="quantity-input" readonly>
              <button type="button" class="quantity-btn" id="qtyPlus">&plus;</button>
            </div>
          </div>

          <!-- Order Summary Box -->
          <div class="order-summary">
            <div class="summary-row">
              <span>Pass Subtotal:</span>
              <span id="summarySubtotal"><?= format_price($event['ticket_price']) ?></span>
            </div>
            <div class="summary-row">
              <span>Booking Fee:</span>
              <span style="color:var(--burgundy); font-weight:600;">₹0.00 (Waived)</span>
            </div>
            <div class="summary-row total">
              <span>Total Amount:</span>
              <span id="summaryTotal"><?= format_price($event['ticket_price']) ?></span>
            </div>
          </div>

          <?php if (is_logged_in()): ?>
            <button type="submit" class="btn btn-primary" style="width:100%; font-size:16px; padding:14px;">
              <i class="fa-solid fa-ticket me-1"></i> Confirm &amp; Reserve Passes
            </button>
          <?php else: ?>
            <a href="login.php?redirect=event-details.php?id=<?= $event['id'] ?>" class="btn btn-primary" style="width:100%; font-size:15px; padding:14px;">
              <i class="fa-solid fa-right-to-bracket me-1"></i> Sign In to Reserve Passes
            </a>
            <p style="font-size:12px; text-align:center; color:var(--charcoal-muted); margin-top:10px;">
              Quick 30-second sign up with instant confirmation.
            </p>
          <?php endif; ?>

          <!-- WhatsApp VIP Inquiry Link -->
          <div style="margin-top:16px; text-align:center;">
            <a href="https://wa.me/919825661046?text=<?= urlencode($waText) ?>" 
               target="_blank" 
               rel="noopener" 
               class="btn btn-sm btn-outline-primary" 
               style="width:100%; padding:10px; border-color:var(--gold); color:var(--burgundy);">
              <i class="fa-brands fa-whatsapp me-1" style="color:#25D366; font-size:16px;"></i> Inquire VIP Table on WhatsApp
            </a>
          </div>

          <div style="display:flex; align-items:center; justify-content:center; gap:8px; margin-top:16px; font-size:12px; color:var(--charcoal-muted);">
            <i class="fa-solid fa-shield-check" style="color:var(--burgundy);"></i>
            <span>Instant Digital E-Pass &bull; Concurrency Protected</span>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </aside>

</div>

<?php include 'includes/footer.php'; ?>
