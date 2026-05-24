<?php
// ─────────────────────────────────────────────────────────────────────────────
// auth.php  —  Shared helpers: DB, session, auth checks
// ─────────────────────────────────────────────────────────────────────────────
$appEnv = getenv('APP_ENV') ?: ($_SERVER['APP_ENV'] ?? '');
$normalizedEnv = strtolower(trim((string)$appEnv));
$isProductionEnv = $normalizedEnv === 'production';
$productionConfigPath = __DIR__ . '/config.production.php';
$localConfigPath = __DIR__ . '/config.php';

if ($isProductionEnv) {
    if (!file_exists($productionConfigPath)) {
        error_log('Production bootstrap failed: config.production.php missing.');
        http_response_code(500);
        exit('Server configuration error.');
    }
    require_once $productionConfigPath;
    if (!defined('PRODUCTION_CONFIG_VALID') || PRODUCTION_CONFIG_VALID !== true) {
        error_log('Production bootstrap failed: production config validation did not pass.');
        http_response_code(500);
        exit('Server configuration error.');
    }
} else {
    require_once $localConfigPath;
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
        // Keep all runtime DB timestamps consistent in UTC.
        $pdo->exec("SET time_zone = '+00:00'");
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
    if (!$user) {
        return null;
    }
    if (array_key_exists('session_version', $user)) {
        $currentSessionVersion = (int)$user['session_version'];
        $storedSessionVersion = isset($_SESSION['user_session_version']) ? (int)$_SESSION['user_session_version'] : null;
        if ($storedSessionVersion !== null && $storedSessionVersion !== $currentSessionVersion) {
            return null;
        }
        if ($storedSessionVersion === null) {
            $_SESSION['user_session_version'] = $currentSessionVersion;
        }
    }
    return $user;
}

function loginUser(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_session_version'] = isset($user['session_version']) ? (int)$user['session_version'] : 1;
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

function usersTableHasColumn(PDO $db, string $columnName): bool {
    static $columnCache = [];
    if (isset($columnCache[$columnName])) {
        return $columnCache[$columnName];
    }
    try {
        $stmt = $db->query("SHOW COLUMNS FROM users LIKE " . $db->quote($columnName));
        $columnCache[$columnName] = $stmt !== false && (bool)$stmt->fetch();
    } catch (Throwable $exception) {
        $columnCache[$columnName] = false;
    }
    return $columnCache[$columnName];
}

function paymentsTableHasColumn(PDO $db, string $columnName): bool {
    static $columnCache = [];
    if (isset($columnCache[$columnName])) {
        return $columnCache[$columnName];
    }
    try {
        $stmt = $db->query("SHOW COLUMNS FROM payments LIKE " . $db->quote($columnName));
        $columnCache[$columnName] = $stmt !== false && (bool)$stmt->fetch();
    } catch (Throwable $exception) {
        $columnCache[$columnName] = false;
    }
    return $columnCache[$columnName];
}

/**
 * Record payment failure details for support/debug visibility.
 */
function recordPaymentFailure(string $paymentMethod, string $orderId, string $failureReason, string $gatewayStatus = ''): void {
    $normalizedPaymentMethod = trim($paymentMethod);
    $normalizedOrderId = trim($orderId);
    if ($normalizedPaymentMethod === '' || $normalizedOrderId === '') {
        return;
    }
    try {
        $db = getDB();
        if (!paymentsTableHasColumn($db, 'failed_at') || !paymentsTableHasColumn($db, 'failure_reason')) {
            return;
        }
        $safeFailureReason = substr(trim($failureReason), 0, 255);
        $safeGatewayStatus = paymentsTableHasColumn($db, 'gateway_status')
            ? substr(trim($gatewayStatus), 0, 64)
            : '';
        $sql = '
            UPDATE payments
            SET status = "failed",
                failed_at = CURRENT_TIMESTAMP,
                failure_reason = ?
        ';
        $params = [$safeFailureReason];
        if (paymentsTableHasColumn($db, 'gateway_status')) {
            $sql .= ', gateway_status = ?';
            $params[] = $safeGatewayStatus;
        }
        $sql .= '
            WHERE payment_method = ?
              AND order_id = ?
              AND status = "created"
        ';
        $params[] = $normalizedPaymentMethod;
        $params[] = $normalizedOrderId;
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    } catch (Throwable $exception) {
        error_log('recordPaymentFailure skipped: ' . $exception->getMessage());
    }
}

/**
 * Convert a UTC datetime string to India time for UI/admin display.
 */
function formatUtcToIst(?string $utcDateTime, string $format = 'Y-m-d H:i:s'): string {
    if (!is_string($utcDateTime) || trim($utcDateTime) === '') {
        return '';
    }
    try {
        $utc = new DateTimeZone('UTC');
        $ist = new DateTimeZone('Asia/Kolkata');
        $dateTime = new DateTime(trim($utcDateTime), $utc);
        $dateTime->setTimezone($ist);
        return $dateTime->format($format);
    } catch (Throwable $exception) {
        return '';
    }
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
 * Resolve token hash for storage/lookup.
 */
function hashSecurityToken(string $rawToken): string {
    return hash('sha256', $rawToken);
}

/**
 * Create a short-lived setup claim in the current session.
 */
function setSetupClaimToken(string $setupToken): void {
    $_SESSION['setup_claim_token'] = $setupToken;
    $_SESSION['setup_claim_created_at'] = time();
}

function consumeSetupClaimToken(): ?string {
    $token = $_SESSION['setup_claim_token'] ?? null;
    $createdAt = (int)($_SESSION['setup_claim_created_at'] ?? 0);
    unset($_SESSION['setup_claim_token'], $_SESSION['setup_claim_created_at']);
    if (!is_string($token) || $token === '') {
        return null;
    }
    if ($createdAt <= 0 || (time() - $createdAt) > 600) {
        return null;
    }
    return $token;
}

/**
 * DB-backed rate limiting for auth-sensitive actions.
 */
function getClientIpAddress(): string {
    $remoteAddress = $_SERVER['REMOTE_ADDR'] ?? '';
    return is_string($remoteAddress) && $remoteAddress !== '' ? $remoteAddress : 'unknown';
}

function ensureRateLimitTable(PDO $db): void {
    static $tableReady = false;
    if ($tableReady) {
        return;
    }
    $db->exec('
        CREATE TABLE IF NOT EXISTS auth_rate_limits (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            bucket_key CHAR(64) NOT NULL,
            action_key VARCHAR(64) NOT NULL,
            identifier_hash CHAR(64) NOT NULL,
            attempt_count INT UNSIGNED NOT NULL DEFAULT 0,
            window_start INT UNSIGNED NOT NULL,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_bucket_key (bucket_key),
            KEY idx_updated_at (updated_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ');
    $tableReady = true;
}

function rateLimitStatus(string $actionKey, string $identifier, int $maxAttempts, int $windowSeconds): array {
    $normalizedAction = trim($actionKey) !== '' ? trim($actionKey) : 'default';
    $normalizedIdentifier = trim($identifier) !== '' ? trim($identifier) : 'global';
    $bucketKey = hash('sha256', $normalizedAction . '|' . $normalizedIdentifier);
    $now = time();
    try {
        $db = getDB();
        ensureRateLimitTable($db);
        $stmt = $db->prepare('SELECT attempt_count, window_start FROM auth_rate_limits WHERE bucket_key = ? LIMIT 1');
        $stmt->execute([$bucketKey]);
        $bucket = $stmt->fetch();
        if (!$bucket) {
            return ['allowed' => true, 'retry_after_seconds' => 0];
        }
        $windowStart = (int)($bucket['window_start'] ?? 0);
        $attemptCount = (int)($bucket['attempt_count'] ?? 0);
        if ($windowStart <= 0 || ($now - $windowStart) >= $windowSeconds) {
            return ['allowed' => true, 'retry_after_seconds' => 0];
        }
        if ($attemptCount >= $maxAttempts) {
            $retryAfterSeconds = max(1, $windowSeconds - ($now - $windowStart));
            return ['allowed' => false, 'retry_after_seconds' => $retryAfterSeconds];
        }
    } catch (Throwable $rateLimitError) {
        error_log('rateLimitStatus fallback due to DB error: ' . $rateLimitError->getMessage());
        return ['allowed' => true, 'retry_after_seconds' => 0];
    }
    return ['allowed' => true, 'retry_after_seconds' => 0];
}

function rateLimitHit(string $actionKey, string $identifier, int $windowSeconds): void {
    $normalizedAction = trim($actionKey) !== '' ? trim($actionKey) : 'default';
    $normalizedIdentifier = trim($identifier) !== '' ? trim($identifier) : 'global';
    $bucketKey = hash('sha256', $normalizedAction . '|' . $normalizedIdentifier);
    $now = time();
    try {
        $db = getDB();
        ensureRateLimitTable($db);
        $stmt = $db->prepare('SELECT attempt_count, window_start FROM auth_rate_limits WHERE bucket_key = ? LIMIT 1');
        $stmt->execute([$bucketKey]);
        $bucket = $stmt->fetch();
        $identifierHash = hash('sha256', $normalizedIdentifier);
        if (!$bucket) {
            $insertStmt = $db->prepare('
                INSERT INTO auth_rate_limits (bucket_key, action_key, identifier_hash, attempt_count, window_start)
                VALUES (?, ?, ?, 1, ?)
            ');
            $insertStmt->execute([$bucketKey, $normalizedAction, $identifierHash, $now]);
            return;
        }
        $windowStart = (int)($bucket['window_start'] ?? 0);
        $attemptCount = (int)($bucket['attempt_count'] ?? 0);
        if ($windowStart <= 0 || ($now - $windowStart) >= $windowSeconds) {
            $resetStmt = $db->prepare('UPDATE auth_rate_limits SET attempt_count = 1, window_start = ? WHERE bucket_key = ?');
            $resetStmt->execute([$now, $bucketKey]);
            return;
        }
        $updateStmt = $db->prepare('UPDATE auth_rate_limits SET attempt_count = ? WHERE bucket_key = ?');
        $updateStmt->execute([$attemptCount + 1, $bucketKey]);
    } catch (Throwable $rateLimitError) {
        error_log('rateLimitHit ignored due to DB error: ' . $rateLimitError->getMessage());
    }
}

function rateLimitClear(string $actionKey, string $identifier): void {
    $normalizedAction = trim($actionKey) !== '' ? trim($actionKey) : 'default';
    $normalizedIdentifier = trim($identifier) !== '' ? trim($identifier) : 'global';
    $bucketKey = hash('sha256', $normalizedAction . '|' . $normalizedIdentifier);
    try {
        $db = getDB();
        ensureRateLimitTable($db);
        $deleteStmt = $db->prepare('DELETE FROM auth_rate_limits WHERE bucket_key = ?');
        $deleteStmt->execute([$bucketKey]);
    } catch (Throwable $rateLimitError) {
        error_log('rateLimitClear ignored due to DB error: ' . $rateLimitError->getMessage());
    }
}

/**
 * Build payment abuse-protection identifier from client and user context.
 */
function getPaymentRateLimitIdentifier(string $email = '', string $orderId = ''): string {
    $parts = ['ip:' . getClientIpAddress()];
    $normalizedEmail = strtolower(trim($email));
    if ($normalizedEmail !== '') {
        $parts[] = 'email:' . $normalizedEmail;
    }
    $normalizedOrderId = trim($orderId);
    if ($normalizedOrderId !== '') {
        $parts[] = 'order:' . $normalizedOrderId;
    }
    return implode('|', $parts);
}

/**
 * Enforce payment endpoint rate limit and return JSON response on abuse.
 */
function enforcePaymentRateLimit(string $actionKey, string $identifier, int $maxAttempts, int $windowSeconds): void {
    $status = rateLimitStatus($actionKey, $identifier, $maxAttempts, $windowSeconds);
    if (!($status['allowed'] ?? true)) {
        $retryAfterSeconds = (int)($status['retry_after_seconds'] ?? 60);
        header('Retry-After: ' . $retryAfterSeconds);
        jsonResponse([
            'error' => 'Too many payment attempts. Please wait a moment and try again.',
            'retry_after_seconds' => $retryAfterSeconds,
        ], 429);
    }
    rateLimitHit($actionKey, $identifier, $windowSeconds);
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
window.__aipbPageViewSent = true;
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
