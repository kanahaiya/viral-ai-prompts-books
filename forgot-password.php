<?php
require_once __DIR__ . '/auth.php';

if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

function sendBrevoEmail(string $recipientEmail, string $subject, string $body): bool
{
    if (!function_exists('curl_init')) return false;
    if (!defined('BREVO_API_KEY') || BREVO_API_KEY === '' || BREVO_API_KEY === 'brevo_api_key_placeholder') return false;

    $payload = [
        'sender' => ['name' => MAIL_FROM_NAME, 'email' => MAIL_FROM_EMAIL],
        'to' => [['email' => $recipientEmail]],
        'subject' => $subject,
        'textContent' => $body,
    ];

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => ['accept: application/json', 'api-key: ' . BREVO_API_KEY, 'content-type: application/json'],
        CURLOPT_TIMEOUT => 20,
    ]);
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curlError || $httpCode < 200 || $httpCode >= 300) {
        error_log('Forgot password Brevo send failed: ' . ($curlError ?: ('HTTP ' . $httpCode . ' body=' . (string)$response)));
        return false;
    }
    return true;
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

$statusMessage = '';
$isError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = trim($_POST['csrf_token'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));

    if (!verifyCsrf($csrfToken)) {
        $statusMessage = 'Session expired. Please refresh and try again.';
        $isError = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $statusMessage = 'Please enter a valid email address.';
        $isError = true;
    } else {
        $statusMessage = 'If an account exists for this email, a password reset link has been sent.';
        try {
            $db = getDB();
            $userStmt = $db->prepare('SELECT id FROM users WHERE email = ? AND status = "active" LIMIT 1');
            $userStmt->execute([$email]);
            $user = $userStmt->fetch();
            if ($user) {
                ensurePasswordResetTable($db);
                $token = generateToken(32);
                $insertStmt = $db->prepare('INSERT INTO password_resets (email, token, used) VALUES (?, ?, 0)');
                $insertStmt->execute([$email, $token]);
                $resetUrl = rtrim(SITE_URL, '/') . '/reset-password.php?token=' . urlencode($token);
                sendBrevoEmail($email, 'Reset your AI Prompt Books password', "Hi,\n\nUse this link to reset your password:\n{$resetUrl}\n\nThis link expires after 2 hours.\n\n- AI Prompt Books");
            }
        } catch (Throwable $exception) {
            error_log('Forgot password failed: ' . $exception->getMessage());
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
<title>Forgot Password — AI Prompt Books</title>
<?php renderMetaPixelHead(); ?>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Segoe UI',Arial,sans-serif;background:#0a0a0a;color:#e8e4de;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem;}
.logo{font-family:'Courier New',monospace;font-size:0.75rem;letter-spacing:3px;text-transform:uppercase;color:#d4a836;margin-bottom:2.5rem;text-align:center;}
.logo a{color:inherit;text-decoration:none;}
.card{background:#141414;border:1px solid #2a2a2a;border-radius:6px;padding:2.2rem;width:100%;max-width:460px;}
h1{font-size:1.5rem;font-weight:900;color:#fff;margin-bottom:0.4rem;}
.sub{font-size:0.85rem;color:#888;margin-bottom:1.5rem;padding-bottom:1.2rem;border-bottom:1px solid #2a2a2a;line-height:1.6;}
.field{margin-bottom:1.1rem;}
label{font-family:'Courier New',monospace;font-size:0.72rem;letter-spacing:1px;color:#888;display:block;margin-bottom:6px;}
input[type=email]{width:100%;background:#0a0a0a;border:1.5px solid #333;color:#fff;padding:11px 14px;border-radius:3px;font-size:0.9rem;outline:none;transition:border-color 0.15s;}
input:focus{border-color:#d4a836;}
.btn{width:100%;background:#d4a836;color:#000;font-family:'Courier New',monospace;font-size:0.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:13px;border:none;border-radius:3px;cursor:pointer;transition:background 0.15s;margin-top:0.5rem;}
.btn:hover{background:#e8b93a;}
.message{border-radius:3px;padding:10px 14px;font-size:0.82rem;margin-bottom:1rem;line-height:1.5;}
.message.error{background:#3a1010;border:1px solid #7a2020;color:#f87171;}
.message.info{background:#0e2f1a;border:1px solid #1f6f3c;color:#86efac;}
.footer-link{text-align:center;margin-top:1.4rem;font-size:0.8rem;color:#888;}
.footer-link a{color:#d4a836;}
</style>
</head>
<body>
<?php renderMetaPixelNoScript(); ?>
<div class="logo"><a href="/">AI Prompt Books</a></div>
<div class="card">
  <h1>Forgot Password</h1>
  <p class="sub">Enter your login email and we'll send a reset link.</p>

  <?php if ($statusMessage): ?>
    <div class="message <?= $isError ? 'error' : 'info' ?>"><?= htmlspecialchars($statusMessage) ?></div>
  <?php endif; ?>

  <form method="POST" action="/forgot-password.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <div class="field">
      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" placeholder="you@example.com" required>
    </div>
    <button type="submit" class="btn">Send Reset Link →</button>
  </form>

  <div class="footer-link">
    Remembered password? <a href="/login.php">Back to login</a>
  </div>
</div>
<script src="/assets/js/pixel-tracking.js"></script>
<script>
pixelTrackCustom('AuthPageView', { page: 'forgot-password' });
const forgotPasswordFormElement = document.querySelector('form[action="/forgot-password.php"]');
if (forgotPasswordFormElement) {
  forgotPasswordFormElement.addEventListener('submit', () => {
    pixelTrack('Lead', { source: 'forgot-password', content_name: 'Password Reset Request' });
    pixelTrackCustom('PasswordResetRequested', { page: 'forgot-password' });
  });
}
</script>
</body>
</html>
