<?php
// admin/manage-bookings.php - Royal Pass Register & Status Lifecycle Management
require_once '../config/db.php';
require_once '../includes/functions.php';

// Handle Booking Status Updates
$action = $_GET['action'] ?? '';
$bookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!empty($action) && $bookingId > 0) {
    if ($action === 'confirm') {
        $upStmt = $pdo->prepare("UPDATE bookings SET booking_status = 'Confirmed', payment_status = 'Paid' WHERE id = ?");
        $upStmt->execute([$bookingId]);
        set_flash('success', 'Pass reservation status updated to Confirmed & Paid.');
    } elseif ($action === 'cancel') {
        // Safe cancellation with seat restoration
        $pdo->beginTransaction();
        try {
            $bStmt = $pdo->prepare("SELECT event_id, ticket_quantity, booking_status FROM bookings WHERE id = ? FOR UPDATE");
            $bStmt->execute([$bookingId]);
            $b = $bStmt->fetch();

            if ($b && $b['booking_status'] !== 'Cancelled') {
                $upStmt = $pdo->prepare("UPDATE bookings SET booking_status = 'Cancelled' WHERE id = ?");
                $upStmt->execute([$bookingId]);

                // Restore seats to event
                $sStmt = $pdo->prepare("UPDATE events SET available_seats = available_seats + ? WHERE id = ?");
                $sStmt->execute([$b['ticket_quantity'], $b['event_id']]);

                $pdo->commit();
                set_flash('warning', 'Reservation marked as Cancelled. ' . $b['ticket_quantity'] . ' passes restored to celebration inventory.');
            } else {
                $pdo->rollBack();
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            set_flash('danger', 'Error updating reservation: ' . $e->getMessage());
        }
    } elseif ($action === 'mark_paid') {
        $upStmt = $pdo->prepare("UPDATE bookings SET payment_status = 'Paid' WHERE id = ?");
        $upStmt->execute([$bookingId]);
        set_flash('success', 'Payment status updated to Paid.');
    }
    header("Location: manage-bookings.php");
    exit;
}

// Fetch all bookings with event and user details
$sql = "SELECT b.*, e.title AS event_title, e.event_date, u.fullname, u.email, u.phone 
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        JOIN users u ON b.user_id = u.id 
        ORDER BY b.booked_at DESC";
$bookings = $pdo->query($sql)->fetchAll();

$pageTitle = "Manage Reservations";
$pageHeader = "Royal Pass Auditing & Status Register";
include 'sidebar.php';
?>

<div class="admin-card">
  <div class="admin-card-header">
    <div style="display:flex; align-items:center; gap:16px;">
      <h3 style="font-family:var(--font-heading); color:var(--burgundy);">
        <i class="fa-solid fa-ticket me-2" style="color:var(--gold);"></i> All Pass Reservations (<?= count($bookings) ?>)
      </h3>
    </div>
    <div class="admin-toolbar">
      <div class="search-wrapper">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="tableSearch" placeholder="Search reference, guest, phone..." class="search-input" style="width:250px;">
      </div>
      <select id="statusFilter" class="select-filter" style="width:130px;">
        <option value="">All Statuses</option>
        <option value="confirmed">Confirmed</option>
        <option value="pending">Pending</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <a href="export-csv.php" class="btn btn-sm btn-outline-primary toolbar-btn" style="border-color:var(--gold); color:var(--burgundy);">
        <i class="fa-solid fa-file-csv me-1" style="color:var(--gold);"></i> Export CSV
      </a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Pass Ref</th>
          <th>Guest Name</th>
          <th>Celebration</th>
          <th style="text-align:center;">Passes</th>
          <th>Amount</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Booked On</th>
          <th style="text-align:right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($bookings)): ?>
          <?php foreach ($bookings as $b): ?>
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
                <div style="font-size:12px; color:var(--charcoal-muted);"><?= e($b['email']) ?></div>
                <?php if (!empty($b['phone'])): ?>
                  <div style="font-size:11.5px; color:var(--charcoal-muted); margin-top:2px;">
                    <i class="fa-solid fa-phone me-1" style="color:var(--gold);"></i><?= e($b['phone']) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <div style="font-weight:600; color:var(--burgundy-dark); font-size:13.5px;" title="<?= e($b['event_title']) ?>">
                  <?= e(substr($b['event_title'], 0, 26)) ?><?= strlen($b['event_title']) > 26 ? '...' : '' ?>
                </div>
                <div style="font-size:11.5px; color:var(--charcoal-muted); margin-top:2px;">
                  <i class="fa-regular fa-calendar me-1" style="color:var(--gold);"></i> <?= format_date($b['event_date']) ?>
                </div>
              </td>
              <td class="nowrap" style="text-align:center;">
                <span style="display:inline-block; font-weight:800; font-size:13px; background:#FAF5EE; color:var(--burgundy); padding:3px 10px; border-radius:12px; border:1px solid var(--gold-border);">
                  <?= $b['ticket_quantity'] ?>
                </span>
              </td>
              <td class="nowrap">
                <strong style="font-size:14.5px; color:var(--burgundy);">
                  <?= format_price($b['total_amount']) ?>
                </strong>
              </td>
              <td class="nowrap">
                <span class="status-badge <?= $b['payment_status'] === 'Paid' ? 'badge-success' : 'badge-warning' ?>" style="font-size:11px;">
                  <i class="fa-solid <?= $b['payment_status'] === 'Paid' ? 'fa-check' : 'fa-hourglass-half' ?> me-1"></i>
                  <?= e($b['payment_status']) ?>
                </span>
                <div style="font-size:11px; color:var(--charcoal-muted); margin-top:3px;">
                  <?= e($b['payment_method']) ?>
                </div>
              </td>
              <td class="nowrap">
                <?= get_status_badge($b['booking_status']) ?>
              </td>
              <td class="nowrap" style="font-size:12px; color:var(--charcoal-muted);">
                <div><?= date('M d, Y', strtotime($b['booked_at'])) ?></div>
                <div style="font-size:11px; color:var(--charcoal-muted);"><?= date('h:i A', strtotime($b['booked_at'])) ?></div>
              </td>
              <td style="text-align:right;" class="nowrap">
                <div class="action-btns" style="justify-content:flex-end;">
                  <?php if (!empty($guestPhone)): ?>
                    <a href="https://wa.me/<?= $guestPhone ?>?text=<?= urlencode('Hello ' . $b['fullname'] . ', this is Bhakti Events Gondal regarding your pass reservation ' . $b['booking_reference'] . '.') ?>" 
                       target="_blank" 
                       rel="noopener" 
                       class="btn-icon whatsapp" 
                       title="WhatsApp Guest">
                      <i class="fa-brands fa-whatsapp"></i>
                    </a>
                  <?php endif; ?>
                  <a href="../ticket-success.php?ref=<?= urlencode($b['booking_reference']) ?>" target="_blank" class="btn-icon view" title="View / Print Royal E-Pass">
                    <i class="fa-solid fa-print"></i>
                  </a>
                  <?php if ($b['booking_status'] === 'Pending'): ?>
                    <a href="manage-bookings.php?action=confirm&id=<?= $b['id'] ?>" class="btn-icon edit" style="color:var(--success); border-color:#A7F3D0; background:#ECFDF5;" title="Confirm Reservation & Mark Paid" onclick="return confirmAction('Confirm reservation <?= e($b['booking_reference']) ?> and mark payment as Paid?')">
                      <i class="fa-solid fa-check"></i>
                    </a>
                  <?php endif; ?>
                  <?php if ($b['booking_status'] !== 'Cancelled'): ?>
                    <a href="manage-bookings.php?action=cancel&id=<?= $b['id'] ?>" class="btn-icon delete" title="Cancel & Release Passes" onclick="return confirmAction('Cancel reservation <?= e($b['booking_reference']) ?> and release <?= $b['ticket_quantity'] ?> passes back to celebration capacity?')">
                      <i class="fa-solid fa-ban"></i>
                    </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" style="text-align:center; padding:40px; color:var(--charcoal-muted);">
              No pass reservations found in the database.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'footer.php'; ?>
