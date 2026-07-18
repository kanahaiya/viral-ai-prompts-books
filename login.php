<?php
require_once __DIR__ . '/auth.php';

if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

$error = '';
$success = '';
$next = '/dashboard.php';
$nextInput = filter_input(INPUT_GET, 'next', FILTER_SANITIZE_URL) ?: '';
if (is_string($nextInput) && str_starts_with($nextInput, '/') && !str_starts_with($nextInput, '//')) {
    $next = $nextInput;
}

if (($_GET['reset'] ?? '') === 'success') {
    $success = 'Password updated successfully. Please login with your new password.';
}
if (($_GET['setup'] ?? '') === 'success') {
    $success = 'Password set successfully. Please login to access your books.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = trim($_POST['csrf_token'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $normalizedEmail = strtolower($email);
    $rateIdentifier = ($normalizedEmail !== '' ? $normalizedEmail : 'empty-email') . '|' . getClientIpAddress();
    $rateLimit = rateLimitStatus('login-attempt', $rateIdentifier, 8, 900);

    if (!verifyCsrf($csrfToken)) {
        $error = 'Session expired. Please refresh and try again.';
    } elseif (!$rateLimit['allowed']) {
        $waitMinutes = max(1, (int)ceil(((int)$rateLimit['retry_after_seconds']) / 60));
        $error = 'Too many login attempts. Please wait about ' . $waitMinutes . ' minute(s) and try again.';
    } elseif (!$email || !$password) {
        $error = 'Please enter your email and password.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ? AND status = "active"');
        $stmt->execute([$normalizedEmail]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            rateLimitClear('login-attempt', $rateIdentifier);
            loginUser($user);
            header('Location: ' . $next);
            exit;
        } else {
            rateLimitHit('login-attempt', $rateIdentifier, 900);
            $error = 'Invalid email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" href="/assets/icons/favicon.png">
<title>Login — AI Prompt Books</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Oswald:wght@700&display=swap">
<?php renderMetaPixelHead(); ?>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Inter','Segoe UI',Arial,sans-serif;background:#0a0a0a;color:#e8e4de;min-height:100vh;}
.site-header{position:sticky;top:0;z-index:100;background:rgba(8,8,8,0.97);backdrop-filter:blur(16px);border-bottom:1px solid #1c1c1c;padding:0;height:64px;}
.site-header__inner{max-width:1440px;margin:0 auto;height:64px;display:flex;align-items:center;justify-content:space-between;gap:2rem;padding:0 1.5rem;}
.site-header__logo{display:flex;align-items:center;gap:8px;text-decoration:none;flex-shrink:0;}
.site-header__logo-icon{width:36px;height:36px;border-radius:7px;border:1.5px solid #d4a836;object-fit:contain;flex-shrink:0;padding:4px;background:#000;}
.site-header__logo-text{display:flex;flex-direction:column;gap:1px;}
.site-header__logo-name{font-family:'Oswald','Arial Narrow',sans-serif;font-size:1.15rem;letter-spacing:2px;text-transform:uppercase;color:#d4a836;line-height:1;font-weight:700;}
.site-header__logo-sub{font-size:0.68rem;letter-spacing:0.3px;color:rgba(255,255,255,0.6);line-height:1.1;font-weight:400;}
.site-header__link{background:#d4a836;color:#000;font-family:'Inter','Segoe UI',sans-serif;font-size:0.75rem;font-weight:500;letter-spacing:1px;text-transform:uppercase;padding:10px 22px;border-radius:4px;border:none;text-decoration:none;transition:background 0.15s;white-space:nowrap;}
.site-header__link:hover{background:#e8b93a;}
.page-wrap{min-height:calc(100vh - 64px);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem;}
.card{background:#141414;border:1px solid #2a2a2a;border-radius:6px;padding:2.5rem;width:100%;max-width:420px;}
h1{font-size:1.5rem;font-weight:900;color:#fff;margin-bottom:0.4rem;}
.sub{font-size:0.85rem;color:#888;margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:1px solid #2a2a2a;}
.field{margin-bottom:1.2rem;}
label{font-family:'Inter','Segoe UI',sans-serif;font-size:0.72rem;letter-spacing:0.5px;color:#888;display:block;margin-bottom:6px;text-transform:uppercase;font-weight:500;}
input[type=email],input[type=password]{width:100%;background:#0a0a0a;border:1.5px solid #333;color:#fff;padding:11px 14px;border-radius:3px;font-size:0.9rem;transition:border-color 0.15s,box-shadow 0.15s;}
input:focus{border-color:#d4a836;}
input:focus-visible{outline:2px solid rgba(212,168,54,0.55);outline-offset:1px;}
.error{background:#3a1010;border:1px solid #7a2020;border-radius:3px;padding:10px 14px;font-size:0.82rem;color:#f87171;margin-bottom:1.2rem;}
.success{background:#0e2f1a;border:1px solid #1f6f3c;border-radius:3px;padding:10px 14px;font-size:0.82rem;color:#86efac;margin-bottom:1.2rem;}
.btn{width:100%;background:#d4a836;color:#000;font-family:'Inter','Segoe UI',sans-serif;font-size:0.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:13px;border:none;border-radius:3px;cursor:pointer;transition:background 0.15s;margin-top:0.5rem;}
.btn:hover{background:#e8b93a;}
.footer-link{text-align:center;margin-top:1.5rem;font-size:0.8rem;color:#888;}
.footer-link a{color:#d4a836;}
.help-links{margin-top:1rem;padding-top:1rem;border-top:1px solid #2a2a2a;display:flex;flex-direction:column;gap:0.5rem;}
.help-links a{font-size:0.78rem;color:#c9a13b;text-decoration:none;}
.help-links a:hover{text-decoration:underline;}
.footer{border-top:1px solid #1a1a1a;padding:1.6rem 1rem;text-align:center;}
.footer-logo{font-family:'Oswald','Arial Narrow',sans-serif;font-size:0.85rem;letter-spacing:2px;text-transform:uppercase;color:#d4a836;margin-bottom:1rem;font-weight:700;}
.footer-links{display:flex;justify-content:center;gap:1rem;flex-wrap:wrap;margin-bottom:0.9rem;}
.footer-links a{font-size:0.75rem;color:#9a9a9a;text-decoration:none;}
.footer-links a:hover{color:#d4a836;}
.footer-copy{font-size:0.75rem;color:#777;}


/* Responsive safety layer */
html, body { overflow-x: hidden; }
img, video, canvas, svg { max-width: 100%; height: auto; }
table { display: block; max-width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

@media (max-width: 768px) {
  .page-wrap, .main { padding-left: 1rem !important; padding-right: 1rem !important; }
  .card, .policy-card, .prompt-card, .content-card, .intro-card, .setup-card { padding-left: 1rem !important; padding-right: 1rem !important; }
}

</style>
</head>
<body>
<?php renderMetaPixelNoScript(); ?>
<header class="site-header">
  <div class="site-header__inner">
    <a href="/" class="site-header__logo">
      <img src="/assets/icons/logo-gold-quill.webp" alt="" class="site-header__logo-icon" width="36" height="36">
      <span class="site-header__logo-text">
        <span class="site-header__logo-name">AI PROMPT SYSTEM</span>
        <span class="site-header__logo-sub">Interactive AI Prompt Generator</span>
      </span>
    </a>
    <a href="/#pricing" class="site-header__link">Get Access</a>
  </div>
</header>
<main class="page-wrap">
<div class="card">
  <h1>Welcome back</h1>
  <p class="sub">Log in to access your prompt books and Telegram community.</p>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST" action="/login.php?next=<?= urlencode($next) ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <div class="field">
      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" placeholder="you@example.com" required
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Your password" required>
    </div>
    <button type="submit" class="btn">Login →</button>
  </form>

  <div class="help-links">
    <a href="/recover-access.php?from=login">Paid but didn't set password? Resend setup link</a>
    <a href="/forgot-password.php">Forgot password? Reset it here</a>
  </div>

  <div class="footer-link">
    Don't have an account? <a href="/#pricing">Get access here</a>
  </div>
</div>
</main>
<footer class="footer">
  <div class="footer-logo">AI PROMPT SYSTEM</div>
  <div class="footer-links">
    <a href="/contact-details.php">Contact</a>
    <a href="/terms-and-conditions.php">Terms</a>
    <a href="/privacy-policy.php">Privacy Policy</a>
    <a href="/refund-and-cancellation-policy.php">Refund Policy</a>
  </div>
  <div class="footer-copy">© <?= date('Y') ?> AI Prompt Books. All rights reserved.</div>
</footer>
<script src="/assets/js/pixel-tracking.js"></script>
<script>
pixelTrackCustom('AuthPageView', { page: 'login' });
<?php if ($error): ?>
pixelTrackCustom('LoginFailed', { reason: 'invalid_credentials_or_validation' });
<?php endif; ?>
<?php if ($success): ?>
pixelTrackCustom('PasswordResetSuccessMessageViewed', { page: 'login' });
<?php endif; ?>
const loginFormElement = document.querySelector('form[action^="/login.php"]');
if (loginFormElement) {
  loginFormElement.addEventListener('submit', () => {
    pixelTrack('Login', { content_name: 'Login Form Submit' });
    pixelTrackCustom('LoginSubmitted', { page: 'login' });
  });
}
</script>
</body>
</html>
