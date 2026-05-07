<?php
// ============================================================
// signup.php — Shows signup form AND handles its own POST
// ============================================================
require_once 'includes/config.php';

if (isLoggedIn()) {
    redirect(SITE_URL . '/index.html');
}

$error   = '';
$success = '';
$old     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']         ?? '');
    $email    = trim($_POST['email']            ?? '');
    $password =      $_POST['password']         ?? '';
    $confirm  =      $_POST['confirm_password'] ?? '';
    $old      = ['username' => $username, 'email' => $email];

    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {

        // ── TODO: Replace with real DB insert ────────────────
        /*
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $error = 'An account with that email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt   = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed);
            if ($stmt->execute()) {
                $success = 'Account created! You can now sign in.';
                $old = [];
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
        */

        // Demo — remove when DB is ready
        $success = 'Account created successfully! You can now sign in.';
        $old = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign Up | DonateHub</title>
  <!-- FIXED: was js/script.js and css/style.css — all files are in root -->
  <link rel="stylesheet" href="style.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet"/>
</head>
<body>

<!-- ═══════════ NAVBAR ═══════════ -->
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
      <a href="login.php"  class="btn-outline">SIGN IN</a>
      <a href="signup.php" class="btn-green">SIGN UP</a>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<!-- ═══════════ SIGNUP CARD ═══════════ -->
<main class="auth-page">
  <div class="auth-card">
    <h1>Create Account</h1>
    <p class="auth-sub">Join DonateHub and start making a difference</p>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success">
        <?= htmlspecialchars($success) ?>
        <a href="login.php"> Sign in now →</a>
      </div>
    <?php endif; ?>

    <!--
      ALIGNED: removed inline onsubmit="checkPasswords()" — password
      validation now handled server-side in PHP above (more reliable),
      and script.js handles the pw-toggle buttons via delegation.
    -->
    <form method="POST" action="signup.php">

      <div class="form-group">
        <label for="username">Full Name</label>
        <input type="text" id="username" name="username"
               placeholder="Your full name"
               value="<?= htmlspecialchars($old['username'] ?? '') ?>"
               required/>
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email"
               placeholder="you@email.com"
               value="<?= htmlspecialchars($old['email'] ?? '') ?>"
               required/>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <!-- ALIGNED: .pw-wrap + .pw-toggle class — handled by script.js delegation -->
        <div class="pw-wrap">
          <input type="password" id="password" name="password"
                 placeholder="At least 6 characters" required/>
          <button type="button" class="pw-toggle">Show</button>
        </div>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <div class="pw-wrap">
          <input type="password" id="confirm_password" name="confirm_password"
                 placeholder="Re-enter your password" required/>
          <button type="button" class="pw-toggle">Show</button>
        </div>
      </div>

      <button type="submit" class="btn-auth">CREATE ACCOUNT</button>
    </form>

    <p class="auth-footer">
      Already have an account? <a href="login.php">Sign in here</a>
    </p>
  </div>
</main>

<!-- FIXED: was js/script.js — correct path is script.js -->
<script src="script.js"></script>
</body>
</html>