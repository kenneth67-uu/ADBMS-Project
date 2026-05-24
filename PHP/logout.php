<?php
// ============================================================
// logout.php — Destroy session and redirect to homepage
// ============================================================
require_once 'includes/config.php';
session_destroy();
redirect(SITE_URL . '/index.html');
?>