<?php
// ─────────────────────────────────────────────────────────────────────────────
// auth.php  —  Shared helpers: DB, session, auth checks
// ─────────────────────────────────────────────────────────────────────────────
$appEnv = getenv('APP_ENV') ?: ($_SERVER['APP_ENV'] ?? '');
$isProductionEnv = strtolower((string)$appEnv) === 'production';
$productionConfigPath = __DIR__ . '/config.production.php';

if ($isProductionEnv && file_exists($productionConfigPath)) {
    require_once $productionConfigPath;
} else {
    require_once __DIR__ . '/config.php';
}

// ── Start session once ────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => COOKIE_LIFETIME,
        'path'     => '/',
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ── Database connection (singleton) ──────────────────────────────────────────
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        // DB_DSN allows SQLite for local dev; fallback builds MySQL DSN for production
        if (defined('DB_DSN')) {
            $dsn  = DB_DSN;
            $user = null;
            $pass = null;
        } else {
            $dsn  = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $user = DB_USER;
            $pass = DB_PASS;
        }
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

// ── Auth helpers ──────────────────────────────────────────────────────────────
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/login.php?next=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    $db   = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ? AND status = "active"');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function loginUser(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    // update last_login
    getDB()->prepare('UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?')
           ->execute([$user['id']]);
}

function logoutUser(): void {
    $_SESSION = [];
    session_destroy();
    session_start();
    session_regenerate_id(true);
}

/**
 * Resolve single-plan book access with fallback recovery from payments history.
 */
function getResolvedBooksAccessIds(array $user): array {
    if (($user['plan'] ?? '') === 'bundle') {
        $allBooks = getBooks();
        $paidBookIds = [];
        foreach ($allBooks as $bookId => $bookMeta) {
            if (empty($bookMeta['bonus'])) {
                $paidBookIds[] = (int)$bookId;
            }
        }
        return $paidBookIds;
    }

    $existingAccess = json_decode($user['books_access'] ?? '[]', true);
    if (is_array($existingAccess)) {
        $normalizedExistingAccess = array_values(array_unique(array_filter(array_map('intval', $existingAccess), static function ($bookId) {
            return $bookId >= 1 && $bookId <= 11;
        })));
        sort($normalizedExistingAccess);
        if (!empty($normalizedExistingAccess)) {
            return $normalizedExistingAccess;
        }
    }

    // Fallback for legacy rows where books_access is missing.
    $email = strtolower(trim((string)($user['email'] ?? '')));
    if ($email === '') {
        return [];
    }

    try {
        $db = getDB();
        $stmt = $db->prepare('
            SELECT book_id, book_ids_json
            FROM payments
            WHERE email = ?
              AND status = "completed"
              AND plan = "single"
            ORDER BY id ASC
        ');
        $stmt->execute([$email]);
        $paymentRows = $stmt->fetchAll();

        $recoveredBookIds = [];
        foreach ($paymentRows as $paymentRow) {
            if (!empty($paymentRow['book_ids_json'])) {
                $decodedBookIds = json_decode((string)$paymentRow['book_ids_json'], true);
                if (is_array($decodedBookIds)) {
                    foreach ($decodedBookIds as $bookId) {
                        $normalizedBookId = (int)$bookId;
                        if ($normalizedBookId >= 1 && $normalizedBookId <= 11) {
                            $recoveredBookIds[] = $normalizedBookId;
                        }
                    }
                }
            } elseif (!empty($paymentRow['book_id'])) {
                $normalizedBookId = (int)$paymentRow['book_id'];
                if ($normalizedBookId >= 1 && $normalizedBookId <= 11) {
                    $recoveredBookIds[] = $normalizedBookId;
                }
            }
        }

        $recoveredBookIds = array_values(array_unique($recoveredBookIds));
        sort($recoveredBookIds);

        // Self-heal user row so future reads don't need fallback.
        if (!empty($recoveredBookIds) && !empty($user['id'])) {
            $updateStmt = $db->prepare('UPDATE users SET books_access = ? WHERE id = ? AND plan = "single"');
            $updateStmt->execute([json_encode($recoveredBookIds), (int)$user['id']]);
        }

        return $recoveredBookIds;
    } catch (Throwable $exception) {
        error_log('Book access recovery failed: ' . $exception->getMessage());
        return [];
    }
}

// ── Book access check ─────────────────────────────────────────────────────────
function userHasBookAccess(array $user, int $bookId): bool {
    $books = getBooks();
    // Bonus books (free for ALL buyers) — no plan check needed
    if (!empty($books[$bookId]['bonus'])) return true;
    if ($user['plan'] === 'bundle') return true;
    $access = getResolvedBooksAccessIds($user);
    return in_array($bookId, (array)$access, true);
}

// ── Book metadata ─────────────────────────────────────────────────────────────
function getBooks(): array {
    return [
        1  => ['title' => 'Action Figure & Toy Box',   'accent' => '#d4a836', 'emoji' => '🧸'],
        2  => ['title' => 'Ghibli & Anime',             'accent' => '#ec4899', 'emoji' => '🌸'],
        3  => ['title' => 'Childhood Nostalgia',        'accent' => '#f97316', 'emoji' => '📷'],
        4  => ['title' => 'Caricature & Chibi',         'accent' => '#84cc16', 'emoji' => '🎨'],
        5  => ['title' => 'Professional Headshots',     'accent' => '#3b82f6', 'emoji' => '💼'],
        6  => ['title' => 'Product Photography',        'accent' => '#059669', 'emoji' => '📦'],
        7  => ['title' => 'Cinematic Movie Poster',     'accent' => '#e11d48', 'emoji' => '🎬'],
        8  => ['title' => 'Vintage Scrapbook',          'accent' => '#b45309', 'emoji' => '📜'],
        9  => ['title' => 'Pet Transformation',         'accent' => '#eab308', 'emoji' => '🐾'],
        10 => ['title' => 'Historical Time Travel',     'accent' => '#4f46e5', 'emoji' => '🕰️'],
        11 => ['title' => 'Bonus Trending Styles',      'accent' => '#d946ef', 'emoji' => '✨'],
        12 => [
            'title'  => 'The AI Image Cheat Code',
            'accent' => '#d4a836',
            'emoji'  => '🎯',
            'file'   => 'bonus-guide.html',   // custom filename (not chapter-XX-pdf.html)
            'bonus'  => true,                 // free for ALL buyers — no plan check
            'label'  => 'BONUS GUIDE',        // shown instead of "100 PROMPTS"
        ],
    ];
}

// ── Generate a cryptographically secure token ─────────────────────────────────
function generateToken(int $bytes = 32): string {
    return bin2hex(random_bytes($bytes));
}

// ── JSON response helper ──────────────────────────────────────────────────────
function jsonResponse(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// ── CSRF helpers ──────────────────────────────────────────────────────────────
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateToken(16);
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Basic session-backed rate limiting for auth-sensitive actions.
 * This is intentionally lightweight and avoids extra DB tables.
 */
function getClientIpAddress(): string {
    $remoteAddress = $_SERVER['REMOTE_ADDR'] ?? '';
    return is_string($remoteAddress) && $remoteAddress !== '' ? $remoteAddress : 'unknown';
}

function rateLimitStatus(string $actionKey, string $identifier, int $maxAttempts, int $windowSeconds): array {
    $normalizedAction = trim($actionKey) !== '' ? trim($actionKey) : 'default';
    $normalizedIdentifier = trim($identifier) !== '' ? trim($identifier) : 'global';
    $bucketKey = hash('sha256', $normalizedAction . '|' . $normalizedIdentifier);
    $now = time();

    if (!isset($_SESSION['rate_limits']) || !is_array($_SESSION['rate_limits'])) {
        $_SESSION['rate_limits'] = [];
    }

    $bucket = $_SESSION['rate_limits'][$bucketKey] ?? null;
    if (!is_array($bucket)) {
        return ['allowed' => true, 'retry_after_seconds' => 0];
    }

    $windowStart = (int)($bucket['window_start'] ?? 0);
    $attemptCount = (int)($bucket['count'] ?? 0);
    if ($windowStart <= 0 || ($now - $windowStart) >= $windowSeconds) {
        return ['allowed' => true, 'retry_after_seconds' => 0];
    }

    if ($attemptCount >= $maxAttempts) {
        $retryAfterSeconds = max(1, $windowSeconds - ($now - $windowStart));
        return ['allowed' => false, 'retry_after_seconds' => $retryAfterSeconds];
    }

    return ['allowed' => true, 'retry_after_seconds' => 0];
}

function rateLimitHit(string $actionKey, string $identifier, int $windowSeconds): void {
    $normalizedAction = trim($actionKey) !== '' ? trim($actionKey) : 'default';
    $normalizedIdentifier = trim($identifier) !== '' ? trim($identifier) : 'global';
    $bucketKey = hash('sha256', $normalizedAction . '|' . $normalizedIdentifier);
    $now = time();

    if (!isset($_SESSION['rate_limits']) || !is_array($_SESSION['rate_limits'])) {
        $_SESSION['rate_limits'] = [];
    }

    $bucket = $_SESSION['rate_limits'][$bucketKey] ?? null;
    if (!is_array($bucket)) {
        $_SESSION['rate_limits'][$bucketKey] = [
            'count' => 1,
            'window_start' => $now,
        ];
        return;
    }

    $windowStart = (int)($bucket['window_start'] ?? 0);
    if ($windowStart <= 0 || ($now - $windowStart) >= $windowSeconds) {
        $_SESSION['rate_limits'][$bucketKey] = [
            'count' => 1,
            'window_start' => $now,
        ];
        return;
    }

    $_SESSION['rate_limits'][$bucketKey] = [
        'count' => ((int)($bucket['count'] ?? 0)) + 1,
        'window_start' => $windowStart,
    ];
}

function rateLimitClear(string $actionKey, string $identifier): void {
    $normalizedAction = trim($actionKey) !== '' ? trim($actionKey) : 'default';
    $normalizedIdentifier = trim($identifier) !== '' ? trim($identifier) : 'global';
    $bucketKey = hash('sha256', $normalizedAction . '|' . $normalizedIdentifier);
    if (isset($_SESSION['rate_limits']) && is_array($_SESSION['rate_limits'])) {
        unset($_SESSION['rate_limits'][$bucketKey]);
    }
}

// ── Meta Pixel helpers ────────────────────────────────────────────────────────
function isMetaPixelEnabled(): bool {
    if (!defined('ENABLE_META_PIXEL') || ENABLE_META_PIXEL !== true) {
        return false;
    }
    if (!defined('META_PIXEL_ID')) {
        return false;
    }
    $pixelId = trim((string)META_PIXEL_ID);
    return $pixelId !== '';
}

function getMetaPixelId(): string {
    if (!defined('META_PIXEL_ID')) {
        return '';
    }
    return trim((string)META_PIXEL_ID);
}

function renderMetaPixelHead(): void {
    if (!isMetaPixelEnabled()) {
        return;
    }
    $pixelId = htmlspecialchars(getMetaPixelId(), ENT_QUOTES, 'UTF-8');
    echo <<<HTML
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{$pixelId}');
fbq('track', 'PageView');
</script>
<!-- End Meta Pixel Code -->
HTML;
}

function renderMetaPixelNoScript(): void {
    if (!isMetaPixelEnabled()) {
        return;
    }
    $pixelId = htmlspecialchars(getMetaPixelId(), ENT_QUOTES, 'UTF-8');
    echo <<<HTML
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={$pixelId}&ev=PageView&noscript=1"
/></noscript>
HTML;
}

// ── Payment setup email helper ────────────────────────────────────────────────
function sendSetupLinkEmail(string $recipientEmail, string $recipientName, string $setupToken, string $plan): bool {
    if (!function_exists('curl_init')) {
        error_log('Setup email skipped: cURL not available.');
        return false;
    }
    if (!defined('BREVO_API_KEY') || BREVO_API_KEY === '' || BREVO_API_KEY === 'brevo_api_key_placeholder') {
        error_log('Setup email skipped: BREVO_API_KEY missing.');
        return false;
    }
    if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
        error_log('Setup email skipped: invalid recipient email.');
        return false;
    }
    if ($setupToken === '') {
        error_log('Setup email skipped: empty setup token.');
        return false;
    }

    $safeName = trim($recipientName) !== '' ? trim($recipientName) : 'there';
    $setupUrl = rtrim(SITE_URL, '/') . '/setup-account.php?token=' . urlencode($setupToken);
    $planText = $plan === 'bundle' ? 'full system' : 'selected book access';
    $subject = 'Your AI Prompt Books access link';
    $body = "Hi {$safeName},\n\n"
        . "Your payment is confirmed. Use this link to create your account and unlock your {$planText}:\n"
        . "{$setupUrl}\n\n"
        . "If you cannot set up right now, no worries — you can use the same link later.\n\n"
        . "- AI Prompt Books";

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
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'api-key: ' . BREVO_API_KEY,
            'content-type: application/json',
        ],
        CURLOPT_TIMEOUT => 20,
    ]);
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curlError || $httpCode < 200 || $httpCode >= 300) {
        error_log('Setup email send failed: ' . ($curlError ?: ('HTTP ' . $httpCode . ' body=' . (string)$response)));
        return false;
    }

    return true;
}
