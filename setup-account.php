<?php
// ─────────────────────────────────────────────────────────────────────────────
// setup-account.php  —  Post-payment: set name + password, create user account
// ─────────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/auth.php';

$token   = trim($_GET['token'] ?? $_POST['token'] ?? '');
$error   = '';
$success = false;

if (!$token || strlen($token) < 32) {
    http_response_code(400);
    $error = 'Invalid or missing setup token.';
}

// Load payment by token
$payment = null;
if (!$error) {
    $db   = getDB();
    $stmt = $db->prepare('
        SELECT * FROM payments
        WHERE setup_token = ?
          AND setup_used = 0
          AND status = "completed"
          AND created_at >= (NOW() - INTERVAL 24 HOUR)
    ');
    $stmt->execute([$token]);
    $payment = $stmt->fetch();
    if (!$payment) {
        $error = 'This setup link is invalid, expired, or already used. Please <a href="/login.php">log in</a> if your account already exists, or <a href="/recover-access.php">resend setup link</a>.';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $csrfToken = trim($_POST['csrf_token'] ?? '');
    $name     = trim($_POST['name']     ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    if (!verifyCsrf($csrfToken))     { $error = 'Session expired. Please refresh and try again.'; }
    elseif (!$name)                  { $error = 'Please enter your name.'; }
    elseif (strlen($password) < 8)   { $error = 'Password must be at least 8 characters.'; }
    elseif ($password !== $confirm)  { $error = 'Passwords do not match.'; }
    else {
        try {
            $db = getDB();

            $selectedBookIds = [];
            if ($payment['plan'] === 'single') {
                if (!empty($payment['book_ids_json'])) {
                    $decodedBookIds = json_decode((string)$payment['book_ids_json'], true);
                    if (is_array($decodedBookIds)) {
                        $selectedBookIds = array_values(array_unique(array_filter(array_map('intval', $decodedBookIds), static function ($bookId) {
                            return $bookId >= 1 && $bookId <= 11;
                        })));
                    }
                } elseif (!empty($payment['book_id'])) {
                    $selectedBookIds = [intval($payment['book_id'])];
                }
            }

            $email = strtolower($payment['email']);
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $db->beginTransaction();

            $existingUserStmt = $db->prepare('SELECT * FROM users WHERE email = ?');
            $existingUserStmt->execute([$email]);
            $existingUser = $existingUserStmt->fetch();

            if ($existingUser) {
                $isBundlePlan = $payment['plan'] === 'bundle' || $existingUser['plan'] === 'bundle';
                $existingAccess = json_decode($existingUser['books_access'] ?? '[]', true);
                if (!is_array($existingAccess)) {
                    $existingAccess = [];
                }
                $mergedAccess = array_values(array_unique(array_map('intval', array_merge($existingAccess, $selectedBookIds))));
                sort($mergedAccess);

                $updateStmt = $db->prepare('
                    UPDATE users
                    SET name = ?, password_hash = ?, plan = ?, books_access = ?, currency = ?, payment_method = ?, payment_id = ?, status = "active"
                    WHERE email = ?
                ');
                $updateStmt->execute([
                    $name,
                    $passwordHash,
                    $isBundlePlan ? 'bundle' : 'single',
                    $isBundlePlan ? null : json_encode($mergedAccess),
                    $payment['currency'],
                    $payment['payment_method'],
                    $payment['payment_id'],
                    $email,
                ]);
            } else {
                $insertStmt = $db->prepare('
                    INSERT INTO users (email, name, password_hash, plan, books_access, currency, payment_method, payment_id, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, "active")
                ');
                $insertStmt->execute([
                    $email,
                    $name,
                    $passwordHash,
                    $payment['plan'],
                    $payment['plan'] === 'bundle' ? null : json_encode($selectedBookIds),
                    $payment['currency'],
                    $payment['payment_method'],
                    $payment['payment_id'],
                ]);
            }

            // Mark token as used
            $db->prepare('UPDATE payments SET setup_used = 1 WHERE setup_token = ?')->execute([$token]);
            $db->commit();

            header('Location: /login.php?setup=success');
            exit;
        } catch (\Exception $e) {
            if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
                $db->rollBack();
            }
            $error = 'Account creation failed. Please contact support.';
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
<title>Set Up Your Account — AI Prompt Books</title>
<?php renderMetaPixelHead(); ?>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Segoe UI',Arial,sans-serif;background:#0a0a0a;color:#e8e4de;min-height:100vh;}
.site-header{position:sticky;top:0;z-index:20;background:#111;border-bottom:1px solid #1a1a1a;padding:0 2rem;}
.site-header__inner{max-width:1100px;margin:0 auto;height:56px;display:flex;align-items:center;justify-content:space-between;}
.site-header__logo{font-family:'Courier New',monospace;font-size:0.75rem;letter-spacing:3px;text-transform:uppercase;color:#d4a836;text-decoration:none;}
.site-header__link{font-family:'Courier New',monospace;font-size:0.7rem;letter-spacing:1px;text-transform:uppercase;color:#a8a8a8;border:1px solid #3a3a3a;padding:5px 12px;border-radius:2px;text-decoration:none;}
.site-header__link:hover{color:#ddd;border-color:#555;}
.page-wrap{min-height:calc(100vh - 56px);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem;}
.page-wrap--invalid{justify-content:flex-start;padding-top:4rem;}
.logo{font-family:'Courier New',monospace;font-size:0.75rem;letter-spacing:3px;text-transform:uppercase;color:#d4a836;margin-bottom:2.5rem;text-align:center;}
.card{background:#141414;border:1px solid #2a2a2a;border-radius:6px;padding:2.5rem;width:100%;max-width:440px;}
.success-badge{background:#0d3a1a;border:1px solid #1a6b30;border-radius:4px;padding:1rem 1.2rem;margin-bottom:1.5rem;display:flex;gap:0.8rem;align-items:flex-start;}
.success-badge .icon{font-size:1.2rem;flex-shrink:0;}
.success-badge .text{font-size:0.85rem;color:#4ade80;line-height:1.5;}
h1{font-size:1.5rem;font-weight:900;color:#fff;margin-bottom:0.4rem;}
.sub{font-size:0.85rem;color:#888;margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:1px solid #2a2a2a;}
.field{margin-bottom:1.2rem;}
label{font-family:'Courier New',monospace;font-size:0.72rem;letter-spacing:1px;color:#888;display:block;margin-bottom:6px;}
input{width:100%;background:#0a0a0a;border:1.5px solid #333;color:#fff;padding:11px 14px;border-radius:3px;font-size:0.9rem;outline:none;transition:border-color 0.15s;}
input:focus{border-color:#d4a836;}
input[readonly]{color:#888;cursor:default;}
.hint{font-size:0.72rem;color:#555;margin-top:4px;}
.error{background:#3a1010;border:1px solid #7a2020;border-radius:3px;padding:10px 14px;font-size:0.82rem;color:#f87171;margin-bottom:1.2rem;}
.btn{width:100%;background:#d4a836;color:#000;font-family:'Courier New',monospace;font-size:0.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:13px;border:none;border-radius:3px;cursor:pointer;transition:background 0.15s;margin-top:0.5rem;}
.btn:hover{background:#e8b93a;}
.plan-tag{display:inline-block;font-family:'Courier New',monospace;font-size:0.65rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:3px 10px;border-radius:2px;margin-left:8px;vertical-align:middle;}
.plan-bundle{background:#d4a836;color:#000;}
.plan-single{background:#2a2a2a;color:#aaa;}
.invalid-card{text-align:center;border:1px solid #3a2a2a;background:linear-gradient(135deg,#1a1212 0%,#141414 100%);}
.invalid-text{color:#d6d6d6;margin-top:0.8rem;font-size:0.92rem;line-height:1.6;}
.invalid-text a{color:#d4a836;text-decoration:underline;text-underline-offset:2px;}
.invalid-text a:hover{color:#e8b93a;}
.footer{border-top:1px solid #1a1a1a;padding:1.6rem 1rem;text-align:center;}
.footer-logo{font-family:'Courier New',monospace;font-size:0.75rem;letter-spacing:3px;text-transform:uppercase;color:#d4a836;margin-bottom:1rem;}
.footer-links{display:flex;justify-content:center;gap:1rem;flex-wrap:wrap;margin-bottom:0.9rem;}
.footer-links a{font-size:0.75rem;color:#9a9a9a;text-decoration:none;}
.footer-links a:hover{color:#d4a836;}
.footer-copy{font-size:0.75rem;color:#777;}
</style>
</head>
<body>
<?php renderMetaPixelNoScript(); ?>
<header class="site-header">
  <div class="site-header__inner">
    <a href="/" class="site-header__logo">AI Prompt Books</a>
    <a href="/#pricing" class="site-header__link">Get Access</a>
  </div>
</header>
<main class="page-wrap <?= ($error && !$payment) ? 'page-wrap--invalid' : '' ?>">
<div class="logo">AI Prompt Books</div>
<div class="card <?= ($error && !$payment) ? 'invalid-card' : '' ?>">

  <?php if ($error && !$payment): ?>
    <div>
      <div style="font-size:2rem;margin-bottom:1rem;">⚠️</div>
      <h1>Invalid Link</h1>
      <p class="invalid-text"><?= $error ?></p>
    </div>

  <?php else: ?>

  <?php if ($payment): ?>
  <div class="success-badge">
    <div class="icon">✅</div>
    <div class="text">
      <strong>Payment successful!</strong> Set up your account below to access your books.
    </div>
  </div>
  <?php endif; ?>

  <h1>
    Set Up Your Account
    <?php if ($payment): ?>
      <?php
        $selectedBookCount = 0;
        if ($payment['plan'] === 'single') {
          if (!empty($payment['book_ids_json'])) {
            $decodedBookIds = json_decode((string)$payment['book_ids_json'], true);
            if (is_array($decodedBookIds)) {
              $selectedBookCount = count($decodedBookIds);
            }
          } elseif (!empty($payment['book_id'])) {
            $selectedBookCount = 1;
          }
        }
      ?>
      <span class="plan-tag <?= $payment['plan'] === 'bundle' ? 'plan-bundle' : 'plan-single' ?>">
        <?= $payment['plan'] === 'bundle' ? 'System' : (($selectedBookCount > 1) ? ($selectedBookCount . ' Books') : 'Single Book') ?>
      </span>
    <?php endif; ?>
  </h1>
  <p class="sub">Create your password to access your AI Prompt Books and Telegram community.</p>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

    <div class="field">
      <label>Email Address</label>
      <input type="email" value="<?= htmlspecialchars($payment['email'] ?? '') ?>" readonly>
    </div>

    <div class="field">
      <label for="name">Your Name</label>
      <input type="text" id="name" name="name" placeholder="e.g. Raj Sharma" required
             value="<?= htmlspecialchars($_POST['name'] ?? $payment['name'] ?? '') ?>">
    </div>

    <div class="field">
      <label for="password">Create Password</label>
      <input type="password" id="password" name="password" placeholder="At least 8 characters" required>
      <div class="hint">Minimum 8 characters</div>
    </div>

    <div class="field">
      <label for="confirm">Confirm Password</label>
      <input type="password" id="confirm" name="confirm" placeholder="Repeat your password" required>
    </div>

    <button type="submit" class="btn">Create Account & Access Books →</button>
  </form>

  <?php endif; ?>
</div>
</main>
<footer class="footer">
  <div class="footer-logo">AI Prompt Books</div>
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
pixelTrackCustom('AuthPageView', { page: 'setup-account' });
<?php if ($payment): ?>
pixelTrackCustom('SetupAccountLandingViewed', { plan: '<?= htmlspecialchars($payment['plan']) ?>' });
pixelTrackCustom('PostPaymentSetupViewed', {
  plan: '<?= htmlspecialchars($payment['plan']) ?>',
  page_type: 'post-payment-setup'
});
<?php endif; ?>
const setupAccountFormElement = document.querySelector('form[method="POST"]');
if (setupAccountFormElement) {
  setupAccountFormElement.addEventListener('submit', () => {
    pixelTrack('CompleteRegistration', { content_name: 'Account Setup Submit' });
    pixelTrackCustom('SetupAccountSubmitted', { page: 'setup-account' });
  });
}
</script>
</body>
</html>
