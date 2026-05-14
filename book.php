<?php
// ─────────────────────────────────────────────────────────────────────────────
// book.php  —  Serve a protected book HTML file after access check
// ─────────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/auth.php';
requireLogin();

/**
 * Render a user-friendly unavailable page when content files are missing.
 */
function renderBookUnavailablePage(string $bookTitle): void {
    http_response_code(503);
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Frame-Options: SAMEORIGIN');
    header('Cache-Control: private, no-store');
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Temporarily Unavailable — AI Prompt Books</title>
<style>
body{font-family:'Segoe UI',Arial,sans-serif;background:#0a0a0a;color:#e8e4de;margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
.card{max-width:680px;width:100%;background:#141414;border:1px solid #2a2a2a;border-radius:10px;padding:28px}
.badge{font-family:'Courier New',monospace;font-size:12px;letter-spacing:2px;color:#d4a836;text-transform:uppercase;margin-bottom:10px}
h1{font-size:30px;line-height:1.2;margin:0 0 10px}
p{color:#a7a7a7;line-height:1.7;margin:0 0 10px}
.book{color:#fff;font-weight:700}
.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}
.btn{display:inline-block;padding:11px 16px;border-radius:6px;text-decoration:none;font-weight:700;font-size:14px}
.btn-primary{background:#d4a836;color:#111}
.btn-secondary{border:1px solid #3a3a3a;color:#ddd}
</style>
</head>
<body>
  <main class="card">
    <div class="badge">Content Sync Issue</div>
    <h1>This book is temporarily unavailable.</h1>
    <p>The file for <span class="book"><?= htmlspecialchars($bookTitle, ENT_QUOTES, 'UTF-8') ?></span> is not available on the server right now.</p>
    <p>Your purchase and access are safe. Please try again in a few minutes, or contact support if this continues.</p>
    <div class="actions">
      <a class="btn btn-primary" href="/dashboard.php">Back to Dashboard</a>
      <a class="btn btn-secondary" href="/">Go to Home</a>
    </div>
  </main>
</body>
</html>
<?php
    exit;
}

$bookId = intval($_GET['id'] ?? 0);
$downloadRequested = isset($_GET['download']) && $_GET['download'] === '1';
$books  = getBooks();

if (!isset($books[$bookId])) {
    http_response_code(404);
    die('Book not found.');
}

$user = getCurrentUser();
if (!userHasBookAccess($user, $bookId)) {
    http_response_code(403);
    header('Location: /dashboard.php');
    exit;
}

// Use custom filename if defined (e.g. bonus-guide.html), else default chapter pattern
$fileName = $books[$bookId]['file'] ?? sprintf('chapter-%02d-pdf.html', $bookId);
$primaryPath = BOOKS_DIR . $fileName;
$fallbackPath = dirname(__DIR__) . '/prompts-ebook/' . $fileName;
$filePath = file_exists($primaryPath) ? $primaryPath : $fallbackPath;
$previewFallbackPath = __DIR__ . '/preview.html';

if (!file_exists($filePath)) {
    if (file_exists($previewFallbackPath)) {
        $filePath = $previewFallbackPath;
    } else {
        renderBookUnavailablePage($books[$bookId]['title'] ?? 'Requested Book');
    }
}

if ($downloadRequested) {
    // Prefer a sibling PDF when available; otherwise download the source file.
    $fileInfo = pathinfo($filePath);
    $pdfPath = $fileInfo['dirname'] . '/' . $fileInfo['filename'] . '.pdf';
    $downloadPath = file_exists($pdfPath) ? $pdfPath : $filePath;

    $downloadInfo = pathinfo($downloadPath);
    $downloadName = $downloadInfo['basename'];
    $extension = strtolower($downloadInfo['extension'] ?? '');
    $contentType = $extension === 'pdf' ? 'application/pdf' : 'text/html; charset=UTF-8';

    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . $downloadName . '"');
    header('X-Frame-Options: SAMEORIGIN');
    header('Cache-Control: private, no-store');
    readfile($downloadPath);
    exit;
}

// Default behavior: open inside browser
header('Content-Type: text/html; charset=UTF-8');
header('X-Frame-Options: SAMEORIGIN');
header('Cache-Control: private, no-store');
readfile($filePath);
