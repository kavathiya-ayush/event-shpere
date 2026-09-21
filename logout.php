<?php
// logout.php - Session Termination
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

// Start clean session for the farewell message
session_start();
require_once 'includes/functions.php';
set_flash('info', 'You have been safely signed out. See you again soon!');
header("Location: login.php");
exit;
?>
