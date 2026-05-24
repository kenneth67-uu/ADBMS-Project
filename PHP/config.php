<?php
// ============================================================
// includes/config.php
// Start session, define site URL, DB constants, helpers.
// All PHP files require_once 'includes/config.php'
// ============================================================

// Change this to match your XAMPP folder name
define('SITE_URL', 'http://localhost/donatehub');

// XAMPP default DB credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'donatehub');

// Start session once on every PHP page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Uncomment when database is ready ──────────────────────
/*
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
*/

// Returns true if a user session is active
function isLoggedIn() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

// Safe redirect helper
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

// Returns the logged-in username, HTML-safe
function getUsername() {
    return htmlspecialchars($_SESSION['username'] ?? 'User');
}
?>