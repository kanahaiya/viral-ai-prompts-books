<?php
// ─────────────────────────────────────────────────────────────────────────────
// book.php  —  Serve a protected book HTML file after access check
// ─────────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/auth.php';
requireLogin();

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

if (!file_exists($filePath)) {
    http_response_code(404);
    die('Book file not found on server. Please contact support.');
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
