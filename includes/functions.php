<?php
// includes/functions.php - Global Helper Functions

if (!ob_get_level()) {
    ob_start();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * HTML Escaping helper for XSS defense
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Input sanitization
 */
function sanitize(string $data): string {
    return trim(htmlspecialchars(strip_tags($data)));
}

/**
 * Check if a user is currently authenticated
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if current user has administrative privileges
 */
function is_admin(): bool {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Ensure user is logged in, or redirect to login
 */
function require_login(string $redirect = 'login.php'): void {
    if (!is_logged_in()) {
        set_flash('warning', 'Please sign in to access that page.');
        header("Location: $redirect");
        exit;
    }
}

/**
 * Ensure administrator is logged in, or redirect
 */
function require_admin(string $redirect = '../admin/index.php'): void {
    if (!is_admin()) {
        set_flash('danger', 'Access restricted. Administrator credentials required.');
        header("Location: $redirect");
        exit;
    }
}

/**
 * Flash message helper
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type'    => $type, // success, danger, warning, info
        'message' => $message
    ];
}

/**
 * Retrieve and clear flash message
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Format currency with Indian Rupee symbol
 */
function format_price($amount): string {
    $val = floatval($amount);
    if ($val <= 0) {
        return 'Free';
    }
    return '₹' . number_format($val, 2);
}

/**
 * Format standard date
 */
function format_date(string $dateStr, string $format = 'M d, Y'): string {
    return date($format, strtotime($dateStr));
}

/**
 * Format standard 24h time to 12h AM/PM
 */
function format_time(string $timeStr, string $format = 'h:i A'): string {
    return date($format, strtotime($timeStr));
}

/**
 * Render visual badge for booking or event statuses
 */
function get_status_badge(string $status): string {
    $statusLower = strtolower($status);
    $colorClass = 'badge-secondary';
    
    switch ($statusLower) {
        case 'confirmed':
        case 'upcoming':
        case 'paid':
            $colorClass = 'badge-success';
            break;
        case 'pending':
        case 'unpaid':
            $colorClass = 'badge-warning';
            break;
        case 'cancelled':
        case 'completed':
            $colorClass = 'badge-danger';
            break;
    }
    
    return '<span class="status-badge ' . $colorClass . '">' . htmlspecialchars(ucfirst($status)) . '</span>';
}

/**
 * URL Slug generator
 */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'item-' . time() : $text;
}

/**
 * Generate unique booking reference code (e.g. EVT-9F3A1C4E)
 */
function generate_booking_ref(): string {
    return 'EVT-' . strtoupper(bin2hex(random_bytes(4)));
}
?>
