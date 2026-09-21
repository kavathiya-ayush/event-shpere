<?php
// admin/export-csv.php - Secure CSV Data Exporter for Guest Bookings
require_once '../config/db.php';
require_once '../includes/functions.php';

require_admin('index.php');

$stmt = $pdo->query("SELECT b.booking_reference, u.fullname, u.email, u.phone, 
                            e.title AS event_title, e.event_date, e.venue,
                            b.ticket_quantity, b.total_amount, b.booking_status, b.payment_status, b.payment_method, b.booked_at 
                     FROM bookings b 
                     JOIN events e ON b.event_id = e.id 
                     JOIN users u ON b.user_id = u.id 
                     ORDER BY b.booked_at DESC");
$rows = $stmt->fetchAll();

$filename = "bhakti_events_bookings_" . date('Ymd_His') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Header Row
fputcsv($output, [
    'Booking Reference',
    'Guest Full Name',
    'Guest Email',
    'Guest Phone',
    'Celebration Title',
    'Event Date',
    'Venue Location',
    'Pass Quantity',
    'Total Amount (INR)',
    'Booking Status',
    'Payment Status',
    'Payment Method',
    'Booked Timestamp'
]);

foreach ($rows as $row) {
    fputcsv($output, [
        $row['booking_reference'],
        $row['fullname'],
        $row['email'],
        $row['phone'] ?? '',
        $row['event_title'],
        $row['event_date'],
        $row['venue'],
        $row['ticket_quantity'],
        $row['total_amount'],
        $row['booking_status'],
        $row['payment_status'],
        $row['payment_method'],
        $row['booked_at']
    ]);
}

fclose($output);
exit;
