<?php
// book-ticket.php - Concurrency-Safe Royal Pass Booking Engine
// ACID Transaction with SELECT ... FOR UPDATE row-level lock
require_once 'config/db.php';
require_once 'includes/functions.php';

// Verify user is authenticated
if (!is_logged_in()) {
    set_flash('warning', 'Please sign in to complete your royal pass reservation.');
    $redirectId = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
    header("Location: login.php" . ($redirectId > 0 ? "?redirect=event-details.php?id=$redirectId" : ""));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: events.php");
    exit;
}

$userId   = $_SESSION['user_id'];
$eventId  = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
$tickets  = isset($_POST['tickets']) ? intval($_POST['tickets']) : 0;
$tierName = trim($_POST['selected_tier'] ?? $_POST['pass_tier'] ?? 'Royal Pass');

// Determine tier multiplier
$tierMultiplier = 1.0;
if ($tierName === 'Gold Circle Pass') {
    $tierMultiplier = 1.6;
} elseif ($tierName === 'Shahi Diwan VIP Lounge') {
    $tierMultiplier = 2.8;
}

if ($eventId <= 0 || $tickets <= 0) {
    set_flash('danger', 'Invalid reservation parameters provided.');
    header("Location: events.php");
    exit;
}

// Limit single booking to 10 passes for safety
if ($tickets > 10) {
    set_flash('warning', 'A maximum of 10 passes can be reserved per transaction.');
    header("Location: event-details.php?id=$eventId");
    exit;
}

// ==========================================================
// Begin ACID Database Transaction with Concurrency Lock
// ==========================================================
$pdo->beginTransaction();

try {
    // 1. Fetch current event state with row-level lock (FOR UPDATE)
    // This prevents race conditions and overbooking when multiple guests book at the same second
    $stmt = $pdo->prepare("SELECT ticket_price, available_seats, title FROM events WHERE id = ? FOR UPDATE");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();

    if (!$event) {
        $pdo->rollBack();
        set_flash('danger', 'Celebration not found or has been cancelled.');
        header("Location: events.php");
        exit;
    }

    if ($event['available_seats'] < $tickets) {
        $pdo->rollBack();
        set_flash('danger', 'Insufficient passes remaining! Only ' . $event['available_seats'] . ' passes available.');
        header("Location: event-details.php?id=$eventId");
        exit;
    }

    // Calculate total price based on selected tier
    $unitPrice = round(floatval($event['ticket_price']) * $tierMultiplier, 2);
    $totalAmount = $unitPrice * $tickets;
    
    // Generate unique verifiable booking reference
    $bookingRef = generate_booking_ref();

    $paymentMethod = "Online Simulated (" . htmlspecialchars($tierName) . ")";

    // 2. Insert booking record
    $insertStmt = $pdo->prepare("INSERT INTO bookings 
        (booking_reference, user_id, event_id, ticket_quantity, total_amount, booking_status, payment_status, payment_method) 
        VALUES (?, ?, ?, ?, ?, 'Confirmed', 'Paid', ?)");
    $insertStmt->execute([$bookingRef, $userId, $eventId, $tickets, $totalAmount, $paymentMethod]);

    // 3. Decrement available seats in the events table
    $updateStmt = $pdo->prepare("UPDATE events SET available_seats = available_seats - ? WHERE id = ?");
    $updateStmt->execute([$tickets, $eventId]);

    // Commit ACID transaction
    $pdo->commit();

    set_flash('success', 'Congratulations! Your ' . htmlspecialchars($tierName) . ' for ' . $event['title'] . ' has been confirmed.');
    header("Location: ticket-success.php?ref=" . urlencode($bookingRef));
    exit;

} catch (Exception $e) {
    // Rollback any modifications if an error occurs
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    set_flash('danger', 'Pass reservation failed: ' . $e->getMessage());
    header("Location: event-details.php?id=$eventId");
    exit;
}
?>
