<?php
// config/db.php - EventSphere Database Connection & Auto-Provisioning
// Tailored for XAMPP / MariaDB environment

$host     = "localhost";
$dbname   = "events_db";
$username = "root";
$password = "";

try {
    // Attempt standard connection to events_db
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // If database doesn't exist yet, attempt auto-initialization from database.sql
    if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
        try {
            $rootPdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Reconnect to the created database
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            // Execute database.sql seed script
            $sqlFile = __DIR__ . '/../database.sql';
            if (file_exists($sqlFile)) {
                $sqlContent = file_get_contents($sqlFile);
                $pdo->exec($sqlContent);
            }
        } catch (PDOException $initEx) {
            die("Database Auto-Setup Failed: " . htmlspecialchars($initEx->getMessage()) . "<br>Please import <code>database.sql</code> manually in phpMyAdmin.");
        }
    } else {
        // Server might not be running or credentials differ
        die("<div style='font-family:Segoe UI,sans-serif;max-width:600px;margin:50px auto;padding:30px;background:#FEF2F2;border:1px solid #FCA5A5;border-radius:12px;color:#991B1B;'>
            <h3 style='margin-top:0;'>⚠️ Database Connection Error</h3>
            <p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            <p>Please ensure that <strong>MySQL</strong> is running in your <strong>XAMPP Control Panel</strong>.</p>
            <p>Once MySQL is started, refresh this page or import <code>database.sql</code> into phpMyAdmin.</p>
        </div>");
    }
}
?>
