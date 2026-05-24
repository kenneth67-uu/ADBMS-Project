<?php
// ============================================================
// campaign.php — Receives POST from campaign.html,
// validates, saves, then redirects back with a status flag.
// ============================================================
require_once 'includes/config.php';

// Only handle POST — anything else goes back to the HTML page
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/campaign.html');
}

$title    = trim($_POST['campaign_title'] ?? '');
$category = trim($_POST['category']       ?? '');
$desc     = trim($_POST['description']    ?? '');
$target   =      $_POST['target_amount']  ?? '';
$end_date = trim($_POST['end_date']       ?? '');

// Validation
if (empty($title) || empty($category) || empty($desc) || empty($target) || empty($end_date)) {
    redirect(SITE_URL . '/campaign.html?status=error');
}
if (!is_numeric($target) || $target <= 0) {
    redirect(SITE_URL . '/campaign.html?status=error');
}

// ── TODO: Save to database ───────────────────────────────────
/*
$user_id = $_SESSION['user_id'] ?? null;
$stmt = $conn->prepare(
    "INSERT INTO campaigns (user_id, title, category, description, target_amount, end_date, status)
     VALUES (?, ?, ?, ?, ?, ?, 'pending')"
);
$stmt->bind_param("isssds", $user_id, $title, $category, $desc, (float)$target, $end_date);
if ($stmt->execute()) {
    redirect(SITE_URL . '/campaign.html?status=success');
} else {
    redirect(SITE_URL . '/campaign.html?status=error');
}
*/

// Demo — remove when DB is ready
redirect(SITE_URL . '/campaign.html?status=success');
?>