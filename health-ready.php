<?php
require_once __DIR__ . '/auth.php';

try {
    $db = getDB();
    $statement = $db->query('SELECT 1');
    $isHealthy = $statement !== false;
} catch (Throwable $exception) {
    error_log('health-ready failed: ' . $exception->getMessage());
    jsonResponse([
        'status' => 'error',
        'service' => 'viral-ai-prompts-books',
    ], 503);
}

if (!$isHealthy) {
    jsonResponse([
        'status' => 'error',
        'service' => 'viral-ai-prompts-books',
    ], 503);
}

jsonResponse([
    'status' => 'ok',
    'service' => 'viral-ai-prompts-books',
]);
