<?php
// user/my-bookings.php - User Royal Pass Wallet & Booking Management
require_once '../config/db.php';
require_once '../includes/functions.php';

require_login('../login.php');

$userId = $_SESSION['user_id'];

// Handle Booking Cancellation request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_booking') {
    $bookingId = intval($_POST['booking_id'] ?? 0);
    
    if ($bookingId > 0) {
        $pdo->beginTransaction();
        try {
            // Fetch booking belonging to this user
            $bStmt = $pdo->prepare("SELECT id, event_id, ticket_quantity, booking_status FROM bookings WHERE id = ? AND user_id = ? FOR UPDATE");
            $bStmt->execute([$bookingId, $userId]);
            $b = $bStmt->fetch();

            if ($b && $b['booking_status'] !== 'Cancelled') {
                // Update status to Cancelled
                $upStmt = $pdo->prepare("UPDATE bookings SET booking_status = 'Cancelled' WHERE id = ?");
                $upStmt->execute([$bookingId]);

                // Restore seats to the event inventory
                $seatStmt = $pdo->prepare("UPDATE events SET available_seats = available_seats + ? WHERE id = ?");
                $seatStmt->execute([$b['ticket_quantity'], $b['event_id']]);

                $pdo->commit();
                set_flash('success', 'Reservation cancelled successfully. ' . $b['ticket_quantity'] . ' passes restored to event inventory.');
            } else {
                $pdo->rollBack();
                set_flash('danger', 'Booking cannot be cancelled.');
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            set_flash('danger', 'Cancellation failed: ' . $e->getMessage());
        }
    }
    header("Location: my-bookings.php");
    exit;
}

// Fetch all bookings for this user
$stmt = $pdo->prepare("SELECT b.*, e.title AS event_title, e.event_date, e.event_time, e.venue, e.image, c.category_name 
                       FROM bookings b 
                       JOIN events e ON b.event_id = e.id 
                       JOIN categories c ON e.category_id = c.id 
                       WHERE b.user_id = ? 
                       ORDER BY b.booked_at DESC");
$stmt->execute([$userId]);
$bookings = $stmt->fetchAll();

$pageTitle = "My Royal Passes & Reservations";
include '../includes/header.php';
?>

<div style="background:linear-gradient(135deg, var(--burgundy-dark) 0%, var(--burgundy) 100%); color:#fff; padding:50px 20px; border-bottom:2px solid var(--gold);">
  <div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
      <div>
        <span class="badge-pill" style="background:rgba(197,160,89,0.2); color:var(--gold-light); border-color:var(--gold); margin-bottom:8px;">
          <i class="fa-solid fa-wallet"></i> Guest Pass Wallet &bull; Est. 2014
        </span>
        <h1 style="color:var(--gold-light); font-size:32px; margin:0; font-family:var(--font-heading);">My Passes &amp; Reservations</h1>
      </div>
      <div>
        <a href="../events.php" class="btn btn-primary">
          <i class="fa-solid fa-plus me-1"></i> Book Another Celebration
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container" style="padding:40px 20px 80px;">
  <?php if (!empty($bookings)): ?>
    <div style="background:#fff; border-radius:16px; border:1px solid var(--gold-border); box-shadow:var(--shadow-sm); overflow:hidden;">
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Pass Ref</th>
              <th>Celebration</th>
              <th>Date &amp; Venue</th>
              <th>Passes</th>
              <th>Amount</th>
              <th>Status</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($bookings as $b): ?>
              <tr>
                <td>
                  <strong style="color:var(--burgundy); font-family:monospace; font-size:14px;">
                    <?= e($b['booking_reference']) ?>
                  </strong>
                  <div style="font-size:11.5px; color:var(--charcoal-muted);">
                    <?= date('M d, Y', strtotime($b['booked_at'])) ?>
                  </div>
                </td>
                <td>
                  <div style="display:flex; align-items:center; gap:12px;">
                    <img src="../assets/images/events/<?= e($b['image']) ?>" 
                         onerror="this.src='../assets/images/events/default-event.jpg'" 
                         alt="" 
                         style="width:52px; height:52px; border-radius:8px; object-fit:cover; border:1px solid var(--gold-border);">
                    <div>
                      <strong style="color:var(--burgundy); font-size:14.5px; display:block;">
                        <a href="../event-details.php?id=<?= $b['event_id'] ?>"><?= e($b['event_title']) ?></a>
                      </strong>
                      <span class="category-badge" style="position:static; padding:2px 8px; font-size:10.5px; background:var(--ivory-soft); color:var(--burgundy); border:1px solid var(--gold-border);">
                        <?= e($b['category_name']) ?>
                      </span>
                    </div>
                  </div>
                </td>
                <td>
                  <div style="font-size:13.5px; font-weight:600; color:var(--charcoal);">
                    <i class="fa-regular fa-calendar me-1" style="color:var(--gold);"></i>
                    <?= format_date($b['event_date']) ?>
                  </div>
                  <div style="font-size:12px; color:var(--charcoal-muted);">
                    <i class="fa-solid fa-location-dot me-1" style="color:var(--burgundy);"></i>
                    <?= e(substr($b['venue'], 0, 25)) ?>...
                  </div>
                </td>
                <td>
                  <span style="font-weight:700; color:var(--charcoal);">
                    <?= $b['ticket_quantity'] ?> <?= $b['ticket_quantity'] > 1 ? 'Passes' : 'Pass' ?>
                  </span>
                </td>
                <td>
                  <strong style="font-size:15px; color:var(--burgundy);">
                    <?= format_price($b['total_amount']) ?>
                  </strong>
                  <div style="font-size:11.5px; color:#059669;">
                    <?= e($b['payment_status']) ?>
                  </div>
                </td>
                <td>
                  <?= get_status_badge($b['booking_status']) ?>
                </td>
                <td style="text-align:right;">
                  <div style="display:inline-flex; gap:8px;">
                    <a href="../ticket-success.php?ref=<?= urlencode($b['booking_reference']) ?>" class="btn btn-sm btn-outline-primary" style="border-color:var(--gold); color:var(--burgundy);" title="View & Print E-Ticket">
                      <i class="fa-solid fa-print"></i> Royal Pass
                    </a>
                    <?php if ($b['booking_status'] !== 'Cancelled'): ?>
                      <form action="my-bookings.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this reservation? Released passes will return to inventory.')">
                        <input type="hidden" name="action" value="cancel_booking">
                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger" title="Cancel Booking">
                          <i class="fa-solid fa-xmark"></i> Cancel
                        </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else: ?>
    <div style="text-align:center; padding:70px 20px; background:#fff; border-radius:16px; border:1px dashed var(--gold-border);">
      <div style="width:68px; height:68px; border-radius:50%; background:var(--ivory-soft); color:var(--burgundy); border:1px solid var(--gold-border); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:28px;">
        <i class="fa-solid fa-ticket"></i>
      </div>
      <h3 style="font-size:22px; margin-bottom:8px; font-family:var(--font-heading); color:var(--burgundy);">No Royal Passes Reserved Yet</h3>
      <p style="color:var(--charcoal-muted); max-width:460px; margin:0 auto 24px;">Explore our upcoming palace celebrations, musical mehfils, and cultural festivals in Gujarat to reserve your first passes.</p>
      <a href="../events.php" class="btn btn-primary">
        <i class="fa-solid fa-compass me-1"></i> Explore Upcoming Celebrations
      </a>
    </div>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
