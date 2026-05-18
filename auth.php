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

// ── Book access check ─────────────────────────────────────────────────────────
function userHasBookAccess(array $user, int $bookId): bool {
    $books = getBooks();
    // Bonus books (free for ALL buyers) — no plan check needed
    if (!empty($books[$bookId]['bonus'])) return true;
    if ($user['plan'] === 'bundle') return true;
    $access = json_decode($user['books_access'] ?? '[]', true);
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

// ── Meta Pixel helpers ────────────────────────────────────────────────────────
function getMetaPixelId(): string {
    return '1543072290570018';
}

function renderMetaPixelHead(): void {
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
    $pixelId = htmlspecialchars(getMetaPixelId(), ENT_QUOTES, 'UTF-8');
    echo <<<HTML
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={$pixelId}&ev=PageView&noscript=1"
/></noscript>
HTML;
}
