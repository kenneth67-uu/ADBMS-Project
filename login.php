<?php
// ============================================================
// login.php — Shows login form AND handles its own POST
// ============================================================
require_once 'includes/config.php';

// Already logged in → go home
if (isLoggedIn()) {
    redirect(SITE_URL . '/index.html');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both your email and password.';
    } else {

        // ── TODO: Replace demo block with real DB check ──────
        /*
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            redirect(SITE_URL . '/index.html');
        } else {
            $error = 'Incorrect email or password. Please try again.';
        }
        */

        // Demo login — remove when DB is ready
        if ($email === 'demo@donatehub.com' && $password === 'demo123') {
            $_SESSION['user_id']  = 1;
            $_SESSION['username'] = 'Demo User';
            redirect(SITE_URL . '/index.html');
        } else {
            $error = 'Incorrect email or password. (Demo: demo@donatehub.com / demo123)';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign In | DonateHub</title>
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

<!-- ═══════════ LOGIN CARD ═══════════ -->
<main class="auth-page">
  <div class="auth-card">
    <h1>Welcome Back</h1>
    <p class="auth-sub">Sign in to your DonateHub account</p>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- action="login.php" — posts to itself -->
    <form method="POST" action="login.php">

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email"
               placeholder="you@email.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               required/>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <!-- ALIGNED: .pw-wrap + .pw-toggle class — handled by script.js delegation -->
        <div class="pw-wrap">
          <input type="password" id="password" name="password"
                 placeholder="Enter your password" required/>
          <button type="button" class="pw-toggle">Show</button>
        </div>
      </div>

      <button type="submit" class="btn-auth">SIGN IN</button>
    </form>

    <p class="auth-footer">
      Don't have an account? <a href="signup.php">Create one here</a>
    </p>
  </div>
</main>

<!-- FIXED: was js/script.js — correct path is script.js -->
<script src="script.js"></script>
</body>
</html>