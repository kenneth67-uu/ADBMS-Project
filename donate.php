<?php
// ============================================================
// donate.php — Login-protected. Shows form AND handles POST.
// ============================================================
require_once 'includes/config.php';

// Not logged in → redirect to login
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php');
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type  = trim($_POST['donation_type']     ?? '');
    $amt   =      $_POST['amount']            ?? '';
    $goods = trim($_POST['goods_description'] ?? '');
    $note  = trim($_POST['note']              ?? '');

    if ($type === 'money' && (!is_numeric($amt) || $amt <= 0)) {
        $error = 'Please enter a valid donation amount.';
    } elseif ($type === 'goods' && empty($goods)) {
        $error = 'Please describe the goods you are donating.';
    } else {

        // ── TODO: Replace with real DB insert ────────────────
        /*
        $stmt = $conn->prepare(
            "INSERT INTO donations (user_id, type, amount, goods_description, note)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("issss",
            $_SESSION['user_id'],
            $type,
            ($type === 'money' ? (float)$amt : null),
            $goods,
            $note
        );
        if ($stmt->execute()) {
            $success = 'Thank you for your donation! You are making a real difference.';
        } else {
            $error = 'Something went wrong. Please try again.';
        }
        */

        // Demo — remove when DB is ready
        $success = 'Thank you for your generous donation! You are making a real difference in Batangas.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Donate | DonateHub</title>
  <!-- FIXED: was css/style.css — all files are in root -->
  <link rel="stylesheet" href="style.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet"/>
</head>
<body>

<!-- ═══════════ NAVBAR — PHP outputs logged-in state ═══════════ -->
<header class="navbar" id="navbar">
  <div class="nav-inner">
    <a href="index.html" class="nav-logo">
      <span class="logo-top">UMDC</span>
      <span class="logo-sub">meaning of UMDC</span>
    </a>
    <nav class="nav-links" id="navLinks">
      <a href="goal.html">GOAL</a>
      <a href="about.html">About</a>
      <a href="projects.html">Projects</a>
      <a href="campaign.html">Campaign</a>
    </nav>
    <div class="nav-actions">
      <span style="font-size:0.82rem; font-weight:700; color:#555;">Hi, <?= getUsername() ?></span>
      <a href="logout.php" class="btn-outline">Log Out</a>
      <a href="donate.php" class="btn-green">DONATE</a>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<!-- ═══════════ DONATE FORM ═══════════ -->
<section class="sec page-wrap">
  <div class="container">

    <h1 class="campaign-title">Make a Donation</h1>
    <p style="text-align:center; color:#555; margin-bottom:28px; font-size:0.95rem;">
      Hi, <strong><?= getUsername() ?></strong>! Your generosity makes a real difference.
    </p>

    <?php if ($success): ?>
      <div class="alert alert-success" style="max-width:640px; margin:0 auto 20px;">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error" style="max-width:640px; margin:0 auto 20px;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="form-card">
      <p class="form-card-title">Choose how you want to give</p>

      <!-- action="donate.php" — posts to itself -->
      <form method="POST" action="donate.php" id="donatePhpForm">

        <div class="form-group">
          <label>Donation Type</label>
          <div class="type-wrap">
            <!--
              ALIGNED: removed inline onclick — script.js handles .type-btn
              via event delegation using data-type attribute.
              setDonateType() in script.js targets #donationType and
              #moneyFields / #goodsFields — IDs match below.
            -->
            <button type="button" class="type-btn active" data-type="money">💵 Money</button>
            <button type="button" class="type-btn"        data-type="goods">📦 Goods</button>
          </div>
          <input type="hidden" id="donationType" name="donation_type" value="money"/>
        </div>

        <!-- Money fields -->
        <div id="moneyFields">
          <div class="form-group">
            <label for="amountInput">Amount (₱)</label>
            <div class="quick-wrap">
              <!--
                ALIGNED: removed inline onclick — script.js handles .quick-btn
                via event delegation using data-val attribute.
                pickAmount() in script.js targets #amountInput — ID matches below.
              -->
              <button type="button" class="quick-btn" data-val="100">₱100</button>
              <button type="button" class="quick-btn" data-val="250">₱250</button>
              <button type="button" class="quick-btn" data-val="500">₱500</button>
              <button type="button" class="quick-btn" data-val="1000">₱1,000</button>
            </div>
            <input type="number" id="amountInput" name="amount" min="1"
                   placeholder="Or enter a custom amount"/>
          </div>
        </div>

        <!-- Goods fields (hidden by default) -->
        <div id="goodsFields" style="display:none;">
          <div class="form-group">
            <label for="goods_description">Describe the Goods</label>
            <input type="text" id="goods_description" name="goods_description"
                   placeholder="e.g. 10 kg rice, school supplies, canned goods"/>
          </div>
        </div>

        <div class="form-group">
          <label for="note">Note (optional)</label>
          <textarea id="note" name="note" rows="3"
                    placeholder="Leave a message of support..."></textarea>
        </div>

        <div class="form-actions">
          <a href="index.html" class="btn-cancel">Cancel</a>
          <button type="submit" class="btn-submit">Submit Donation</button>
        </div>

      </form>
    </div>
  </div>
</section>

<!-- ═══════════ CTA ═══════════ -->
<section class="cta-banner">
  <div class="cta-bg"><img src="images/cta-bg.jpg" alt="" onerror="this.style.display='none'"/></div>
  <div class="cta-overlay"></div>
  <div class="cta-body">
    <p>Be part of Bayanihan—give a gift to<br>help a fellow Batangueño.</p>
    <a href="donate.php" class="btn-cta">DONATE</a>
  </div>
</section>

<!-- ═══════════ FOOTER ═══════════ -->
<footer class="site-footer">
  <p>© 2026 <a href="index.html" class="footer-umdc">UMDC</a>. All rights reserved.</p>
</footer>

<!-- FIXED: was js/script.js — correct path is script.js -->
<script src="script.js"></script>
</body>
</html>