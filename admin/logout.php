<?php
// admin/logout.php - Admin Session Termination
session_start();
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

session_start();
require_once '../includes/functions.php';
set_flash('info', 'Administrator signed out safely.');
header("Location: index.php");
exit;
?>
