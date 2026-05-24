<?php
require_once __DIR__ . '/auth.php';

header('Content-Type: application/json');
jsonResponse([
    'status' => 'ok',
    'service' => 'viral-ai-prompts-books',
]);
