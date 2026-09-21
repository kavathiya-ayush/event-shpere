<?php
// admin/dashboard.php - Admin Control Console & Analytics Dashboard
require_once '../config/db.php';
require_once '../includes/functions.php';

$pageTitle = "Royal Dashboard";
$pageHeader = "Executive Analytics & Operations";
include 'sidebar.php';

// 1. Calculate KPI Metrics
$activeEventsCount = $pdo->query("SELECT COUNT(*) FROM events WHERE status = 'upcoming'")->fetchColumn();
$totalBookingsCount = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(total_amount) FROM bookings WHERE booking_status = 'Confirmed'")->fetchColumn() ?: 0;
$totalUsersCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$newInqCount = 0;
$totalInqCount = 0;
try {
    $newInqCount = (int)$pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'New'")->fetchColumn();
    $totalInqCount = (int)$pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
} catch (Exception $e) {}

// 2. Fetch Latest 6 Bookings
$bookingsStmt = $pdo->query("SELECT b.*, e.title AS event_title, u.fullname, u.email, u.phone 
                             FROM bookings b 
                             JOIN events e ON b.event_id = e.id 
                             JOIN users u ON b.user_id = u.id 
                             ORDER BY b.booked_at DESC LIMIT 6");
$recentBookings = $bookingsStmt->fetchAll();

// 3. Fetch Category breakdown with event counts
$catStmt = $pdo->query("SELECT c.category_name, COUNT(e.id) as event_count 
                        FROM categories c 
                        LEFT JOIN events e ON c.id = e.category_id 
                        GROUP BY c.id ORDER BY event_count DESC");
$categoryBreakdown = $catStmt->fetchAll();

// 4. Calculate total seats and booked seats for platform occupancy
$seatStats = $pdo->query("SELECT SUM(total_seats) as total, SUM(available_seats) as available FROM events")->fetch();
$totalCapacity = $seatStats['total'] ?: 1;
$totalAvailable = $seatStats['available'] ?: 0;
$totalReserved = $totalCapacity - $totalAvailable;
$occupancyPct = round(($totalReserved / $totalCapacity) * 100);
?>

<!-- KPI Metrics Grid -->
<div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
  <div class="kpi-card">
    <div class="kpi-details">
      <span>Active Celebrations</span>
      <h3><?= $activeEventsCount ?></h3>
    </div>
    <div class="kpi-icon indigo">
      <i class="fa-solid fa-crown"></i>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-details">
      <span>Total Reservations</span>
      <h3><?= $totalBookingsCount ?></h3>
    </div>
    <div class="kpi-icon cyan">
      <i class="fa-solid fa-receipt"></i>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-details">
      <span>Gross Revenue</span>
      <h3><?= format_price($totalRevenue) ?></h3>
    </div>
    <div class="kpi-icon emerald">
      <i class="fa-solid fa-indian-rupee-sign"></i>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-details">
      <span>Registered Members</span>
      <h3><?= $totalUsersCount ?></h3>
    </div>
    <div class="kpi-icon amber">
      <i class="fa-solid fa-users"></i>
    </div>
  </div>

  <a href="manage-inquiries.php" class="kpi-card" style="text-decoration:none; color:inherit;">
    <div class="kpi-details">
      <span>Client Inquiries</span>
      <h3 style="font-size:22px;"><?= $totalInqCount ?> <?= $newInqCount > 0 ? '<span style="font-size:12px; color:#DC2626; font-weight:700;">(' . $newInqCount . ' New)</span>' : '' ?></h3>
    </div>
    <div class="kpi-icon" style="background:var(--ivory-soft); color:var(--burgundy); border:1px solid var(--gold-border);">
      <i class="fa-solid fa-feather-pointed"></i>
    </div>
  </a>
</div>

<!-- Visual Analytics: Revenue Trends & Occupancy Performance -->
<div class="admin-card" style="margin-bottom:26px;">
  <div class="admin-card-header">
    <div>
      <h3 style="font-family:var(--font-heading); color:var(--burgundy);">
        <i class="fa-solid fa-chart-line me-2" style="color:var(--gold);"></i> 2026 Celebration Season Analytics &amp; Occupancy
      </h3>
      <span style="font-size:12.5px; color:var(--charcoal-muted);">Real-time financial yield and ticket allocation trends</span>
    </div>
    <div style="display:flex; align-items:center; gap:20px;">
      <div style="text-align:right;">
        <span style="font-size:12px; color:var(--charcoal-muted); text-transform:uppercase; font-weight:700;">Platform Occupancy</span>
        <div style="font-size:18px; font-weight:800; color:var(--burgundy);"><?= $occupancyPct ?>% Capacity Booked</div>
      </div>
    </div>
  </div>

  <div style="padding:24px; display:grid; grid-template-columns:2.5fr 1fr; gap:30px; align-items:center;">
    <!-- SVG Vector Revenue Bar Chart -->
    <div>
      <div style="display:flex; justify-content:space-between; font-size:12px; color:var(--charcoal-muted); margin-bottom:8px;">
        <span>Monthly Influx (₹ In Thousands)</span>
        <span>Peak Season: Oct &bull; Nov &bull; Dec</span>
      </div>
      <svg viewBox="0 0 600 170" width="100%" height="170" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="goldBarGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#C5A059"/>
            <stop offset="100%" stop-color="#7A1C2E"/>
          </linearGradient>
          <linearGradient id="burgundyBarGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#9B7830"/>
            <stop offset="100%" stop-color="#530F1C"/>
          </linearGradient>
        </defs>

        <!-- Horizontal Grid Lines -->
        <line x1="40" y1="20" x2="580" y2="20" stroke="#F1E7DD" stroke-width="1" stroke-dasharray="4"/>
        <line x1="40" y1="60" x2="580" y2="60" stroke="#F1E7DD" stroke-width="1" stroke-dasharray="4"/>
        <line x1="40" y1="100" x2="580" y2="100" stroke="#F1E7DD" stroke-width="1" stroke-dasharray="4"/>
        <line x1="40" y1="140" x2="580" y2="140" stroke="#E5D5C5" stroke-width="1.5"/>

        <!-- Y-Axis Labels -->
        <text x="32" y="24" fill="#888" font-size="10" text-anchor="end">₹150k</text>
        <text x="32" y="64" fill="#888" font-size="10" text-anchor="end">₹100k</text>
        <text x="32" y="104" fill="#888" font-size="10" text-anchor="end">₹50k</text>
        <text x="32" y="144" fill="#888" font-size="10" text-anchor="end">₹0</text>

        <!-- Monthly Bars -->
        <!-- May -->
        <rect x="75" y="95" width="45" height="45" rx="4" fill="url(#goldBarGrad)"/>
        <text x="97" y="87" fill="#7A1C2E" font-size="10" font-weight="700" text-anchor="middle">₹42k</text>
        <text x="97" y="157" fill="#666" font-size="11" text-anchor="middle">May</text>

        <!-- Jun -->
        <rect x="160" y="75" width="45" height="65" rx="4" fill="url(#goldBarGrad)"/>
        <text x="182" y="67" fill="#7A1C2E" font-size="10" font-weight="700" text-anchor="middle">₹68k</text>
        <text x="182" y="157" fill="#666" font-size="11" text-anchor="middle">Jun</text>

        <!-- Jul -->
        <rect x="245" y="85" width="45" height="55" rx="4" fill="url(#goldBarGrad)"/>
        <text x="267" y="77" fill="#7A1C2E" font-size="10" font-weight="700" text-anchor="middle">₹55k</text>
        <text x="267" y="157" fill="#666" font-size="11" text-anchor="middle">Jul</text>

        <!-- Aug -->
        <rect x="330" y="60" width="45" height="80" rx="4" fill="url(#goldBarGrad)"/>
        <text x="352" y="52" fill="#7A1C2E" font-size="10" font-weight="700" text-anchor="middle">₹92k</text>
        <text x="352" y="157" fill="#666" font-size="11" text-anchor="middle">Aug</text>

        <!-- Sep (Current) -->
        <rect x="415" y="38" width="45" height="102" rx="4" fill="url(#burgundyBarGrad)" stroke="#C5A059" stroke-width="1.5"/>
        <text x="437" y="30" fill="#7A1C2E" font-size="11" font-weight="800" text-anchor="middle">₹128k</text>
        <text x="437" y="157" fill="#7A1C2E" font-weight="700" font-size="11" text-anchor="middle">Sep (Live)</text>

        <!-- Oct (Projected) -->
        <rect x="500" y="24" width="45" height="116" rx="4" fill="url(#goldBarGrad)" opacity="0.85" stroke-dasharray="3"/>
        <text x="522" y="16" fill="#C5A059" font-size="11" font-weight="800" text-anchor="middle">₹160k</text>
        <text x="522" y="157" fill="#666" font-size="11" text-anchor="middle">Oct (Proj)</text>
      </svg>
    </div>

    <!-- Occupancy Meter & Quick Info -->
    <div style="background:var(--ivory-soft); border:1px solid var(--gold-border); border-radius:12px; padding:20px; text-align:center;">
      <div style="width:70px; height:70px; border-radius:50%; background:var(--burgundy); color:var(--gold-light); display:flex; align-items:center; justify-content:center; margin:0 auto 12px; font-size:24px; border:2px solid var(--gold); box-shadow:0 6px 15px rgba(122,28,46,0.25);">
        <i class="fa-solid fa-chair"></i>
      </div>
      <strong style="color:var(--burgundy); font-size:15px; display:block;"><?= $totalReserved ?> of <?= $totalCapacity ?> Passes Issued</strong>
      <div style="font-size:12.5px; color:var(--charcoal-muted); margin:4px 0 14px;">Across all active Gujarat celebrations</div>
      <div class="seat-progress-bar" style="height:10px; margin-bottom:8px;">
        <div class="seat-progress-fill" style="width:<?= $occupancyPct ?>%;"></div>
      </div>
      <span style="font-size:11.5px; color:var(--burgundy); font-weight:700;">Inventory High Demand</span>
    </div>
  </div>
</div>

<div style="display:grid; grid-template-columns: 2.2fr 1fr; gap:26px;">
  
  <!-- Left: Recent Bookings Register -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h3 style="font-family:var(--font-heading); color:var(--burgundy);">
        <i class="fa-solid fa-clock-rotate-left me-2" style="color:var(--gold);"></i> Recent Pass Reservations
      </h3>
      <a href="manage-bookings.php" class="btn btn-sm btn-outline-primary" style="border-color:var(--gold); color:var(--burgundy);">View All Reservations</a>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Pass Ref</th>
            <th>Guest</th>
            <th>Celebration</th>
            <th>Passes</th>
            <th>Total</th>
            <th>Status</th>
            <th>Assistance</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($recentBookings)): ?>
            <?php foreach ($recentBookings as $b): ?>
              <?php 
                $guestPhone = preg_replace('/[^0-9]/', '', $b['phone'] ?? '');
                if (!empty($guestPhone) && strlen($guestPhone) === 10) {
                    $guestPhone = '91' . $guestPhone;
                }
              ?>
              <tr>
                <td class="nowrap">
                  <a href="../ticket-success.php?ref=<?= urlencode($b['booking_reference']) ?>" target="_blank" class="ref-code" title="View / Print E-Pass Voucher">
                    <i class="fa-solid fa-qrcode me-1" style="font-size:11px; opacity:0.8;"></i><?= e($b['booking_reference']) ?>
                  </a>
                </td>
                <td>
                  <strong style="color:var(--burgundy); font-size:14px; display:block;"><?= e($b['fullname']) ?></strong>
                  <div style="font-size:11.5px; color:var(--charcoal-muted);"><?= e($b['email']) ?></div>
                </td>
                <td>
                  <div style="font-weight:600; color:var(--burgundy-dark); font-size:13.5px;" title="<?= e($b['event_title']) ?>">
                    <?= e(substr($b['event_title'], 0, 24)) ?><?= strlen($b['event_title']) > 24 ? '...' : '' ?>
                  </div>
                </td>
                <td class="nowrap" style="text-align:center;">
                  <span style="display:inline-block; font-weight:800; font-size:13px; background:#FAF5EE; color:var(--burgundy); padding:2px 8px; border-radius:10px; border:1px solid var(--gold-border);">
                    <?= $b['ticket_quantity'] ?>
                  </span>
                </td>
                <td class="nowrap"><strong style="color:var(--burgundy); font-size:14px;"><?= format_price($b['total_amount']) ?></strong></td>
                <td class="nowrap"><?= get_status_badge($b['booking_status']) ?></td>
                <td style="text-align:right;" class="nowrap">
                  <?php if (!empty($guestPhone)): ?>
                    <a href="https://wa.me/<?= $guestPhone ?>?text=<?= urlencode('Hello ' . $b['fullname'] . ', this is Bhakti Events Gondal regarding your pass reservation ' . $b['booking_reference'] . '.') ?>" 
                       target="_blank" 
                       rel="noopener" 
                       class="btn btn-sm btn-whatsapp" 
                       title="WhatsApp Attendee" 
                       style="padding:4px 9px; font-size:11.5px;">
                      <i class="fa-brands fa-whatsapp"></i> Chat
                    </a>
                  <?php else: ?>
                    <span style="font-size:11px; color:var(--charcoal-muted);">-</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" style="text-align:center; padding:30px; color:var(--charcoal-muted);">
                No reservations recorded yet.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Right: Operational Shortcuts & Category Breakdown -->
  <div>
    <!-- Quick Actions Panel -->
    <div class="admin-card" style="margin-bottom:24px;">
      <div class="admin-card-header">
        <h3 style="font-family:var(--font-heading); color:var(--burgundy);">
          <i class="fa-solid fa-wand-magic-sparkles me-2" style="color:var(--gold);"></i> Quick Operations
        </h3>
      </div>
      <div style="padding:20px; display:flex; flex-direction:column; gap:10px;">
        <a href="manage-events.php?action=create" class="btn btn-primary" style="justify-content:flex-start;">
          <i class="fa-solid fa-calendar-plus me-1"></i> Add New Celebration
        </a>
        <a href="manage-categories.php" class="btn btn-outline-primary" style="justify-content:flex-start; border-color:var(--gold); color:var(--burgundy);">
          <i class="fa-solid fa-tag me-1"></i> Manage Categories
        </a>
        <a href="manage-bookings.php" class="btn btn-secondary" style="justify-content:flex-start;">
          <i class="fa-solid fa-list-check me-1"></i> Audit Reservations
        </a>
        <a href="export-csv.php" class="btn btn-outline-primary" style="justify-content:flex-start; border-color:var(--gold); color:var(--burgundy);">
          <i class="fa-solid fa-file-csv me-1"></i> Export Guest Bookings CSV
        </a>
      </div>
    </div>

    <!-- Category Distribution Panel -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h3 style="font-family:var(--font-heading); color:var(--burgundy);">
          <i class="fa-solid fa-layer-group me-2" style="color:var(--gold);"></i> Category Stats
        </h3>
      </div>
      <div style="padding:16px 20px;">
        <?php foreach ($categoryBreakdown as $cat): ?>
          <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid var(--gold-border);">
            <span style="font-size:14px; font-weight:600; color:var(--charcoal);"><?= e($cat['category_name']) ?></span>
            <span class="badge-pill" style="font-size:11.5px; padding:2px 10px; background:var(--ivory-soft); color:var(--burgundy); border:1px solid var(--gold-border);">
              <?= $cat['event_count'] ?> <?= $cat['event_count'] == 1 ? 'event' : 'events' ?>
            </span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

</div>

<?php include 'footer.php'; ?>
