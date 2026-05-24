<?php
require_once __DIR__ . '/auth.php';

if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

function ensurePasswordResetTable(PDO $db): void
{
    $db->exec('
        CREATE TABLE IF NOT EXISTS password_resets (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            email VARCHAR(255) NOT NULL,
            token CHAR(64) NOT NULL,
            used TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_email (email),
            KEY idx_token (token)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');
}

$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$tokenHash = $token !== '' ? hashSecurityToken($token) : '';
$statusMessage = '';
$isError = false;

if (!$token || strlen($token) < 32 || strlen($tokenHash) !== 64) {
    $statusMessage = 'Invalid or missing reset token.';
    $isError = true;
}

$resetRow = null;
if (!$isError) {
    try {
        $db = getDB();
        ensurePasswordResetTable($db);
        $stmt = $db->prepare('
            SELECT id, email
            FROM password_resets
            WHERE token = ?
              AND used = 0
              AND created_at >= (NOW() - INTERVAL 2 HOUR)
            ORDER BY id DESC
            LIMIT 1
        ');
        $stmt->execute([$tokenHash]);
        $resetRow = $stmt->fetch();
        if (!$resetRow) {
            $statusMessage = 'This reset link is invalid or expired. Please request a new one.';
            $isError = true;
        }
    } catch (Throwable $exception) {
        error_log('Reset password lookup failed: ' . $exception->getMessage());
        $statusMessage = 'Could not validate reset link right now.';
        $isError = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isError && $resetRow) {
    $csrfToken = trim($_POST['csrf_token'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm'] ?? '');
    $rateIdentifier = strtolower(trim((string)$resetRow['email'])) . '|' . getClientIpAddress();
    $rateLimit = rateLimitStatus('reset-password', $rateIdentifier, 6, 900);

    if (!verifyCsrf($csrfToken)) {
        $statusMessage = 'Session expired. Please refresh and try again.';
        $isError = true;
    } elseif (!$rateLimit['allowed']) {
        $waitMinutes = max(1, (int)ceil(((int)$rateLimit['retry_after_seconds']) / 60));
        $statusMessage = 'Too many password reset attempts. Please wait about ' . $waitMinutes . ' minute(s) and try again.';
        $isError = true;
    } elseif (strlen($password) < 8) {
        rateLimitHit('reset-password', $rateIdentifier, 900);
        $statusMessage = 'Password must be at least 8 characters.';
        $isError = true;
    } elseif ($password !== $confirm) {
        rateLimitHit('reset-password', $rateIdentifier, 900);
        $statusMessage = 'Passwords do not match.';
        $isError = true;
    } else {
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $db->beginTransaction();
            $passwordUpdateSql = 'UPDATE users SET password_hash = ?';
            if (usersTableHasColumn($db, 'password_changed_at')) {
                $passwordUpdateSql .= ', password_changed_at = CURRENT_TIMESTAMP';
            }
            if (usersTableHasColumn($db, 'session_version')) {
                $passwordUpdateSql .= ', session_version = session_version + 1';
            }
            $passwordUpdateSql .= ' WHERE email = ?';
            $updateUserStmt = $db->prepare($passwordUpdateSql);
            $updateUserStmt->execute([$passwordHash, $resetRow['email']]);
            $markUsedStmt = $db->prepare('UPDATE password_resets SET used = 1 WHERE email = ?');
            $markUsedStmt->execute([$resetRow['email']]);
            $db->commit();
            rateLimitClear('reset-password', $rateIdentifier);
            header('Location: /login.php?reset=success');
            exit;
        } catch (Throwable $exception) {
            if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
                $db->rollBack();
            }
            rateLimitHit('reset-password', $rateIdentifier, 900);
            error_log('Reset password save failed: ' . $exception->getMessage());
            $statusMessage = 'Could not reset password right now. Please try again.';
            $isError = true;
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
<title>Reset Password — AI Prompt Books</title>
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
.logo{font-family:'Courier New',monospace;font-size:0.75rem;letter-spacing:3px;text-transform:uppercase;color:#d4a836;margin-bottom:2.5rem;text-align:center;}
.logo a{color:inherit;text-decoration:none;}
.card{background:#141414;border:1px solid #2a2a2a;border-radius:6px;padding:2.2rem;width:100%;max-width:460px;}
h1{font-size:1.5rem;font-weight:900;color:#fff;margin-bottom:0.4rem;}
.sub{font-size:0.85rem;color:#888;margin-bottom:1.5rem;padding-bottom:1.2rem;border-bottom:1px solid #2a2a2a;line-height:1.6;}
.field{margin-bottom:1.1rem;}
label{font-family:'Courier New',monospace;font-size:0.72rem;letter-spacing:1px;color:#888;display:block;margin-bottom:6px;}
input[type=password]{width:100%;background:#0a0a0a;border:1.5px solid #333;color:#fff;padding:11px 14px;border-radius:3px;font-size:0.9rem;outline:none;transition:border-color 0.15s;}
input:focus{border-color:#d4a836;}
.btn{width:100%;background:#d4a836;color:#000;font-family:'Courier New',monospace;font-size:0.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:13px;border:none;border-radius:3px;cursor:pointer;transition:background 0.15s;margin-top:0.5rem;}
.btn:hover{background:#e8b93a;}
.message{border-radius:3px;padding:10px 14px;font-size:0.82rem;margin-bottom:1rem;line-height:1.5;}
.message.error{background:#3a1010;border:1px solid #7a2020;color:#f87171;}
.footer-link{text-align:center;margin-top:1.4rem;font-size:0.8rem;color:#888;}
.footer-link a{color:#d4a836;}
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
<main class="page-wrap">
<div class="logo"><a href="/">AI Prompt Books</a></div>
<div class="card">
  <h1>Reset Password</h1>
  <p class="sub">Set a new password for your account.</p>

  <?php if ($statusMessage): ?>
    <div class="message error"><?= htmlspecialchars($statusMessage) ?></div>
  <?php endif; ?>

  <?php if (!$isError && $resetRow): ?>
    <form method="POST" action="/reset-password.php">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
      <div class="field">
        <label for="password">New Password</label>
        <input type="password" id="password" name="password" required placeholder="At least 8 characters">
      </div>
      <div class="field">
        <label for="confirm">Confirm Password</label>
        <input type="password" id="confirm" name="confirm" required placeholder="Repeat your password">
      </div>
      <button type="submit" class="btn">Update Password →</button>
    </form>
  <?php endif; ?>

  <div class="footer-link">
    <a href="/login.php">Back to login</a>
  </div>
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
pixelTrackCustom('AuthPageView', { page: 'reset-password' });
const resetPasswordFormElement = document.querySelector('form[action="/reset-password.php"]');
if (resetPasswordFormElement) {
  resetPasswordFormElement.addEventListener('submit', () => {
    pixelTrackCustom('PasswordResetSubmitted', { page: 'reset-password' });
  });
}
</script>
</body>
</html>
