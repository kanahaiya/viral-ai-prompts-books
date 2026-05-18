<?php
// ─────────────────────────────────────────────────────────────────────────────
// api/paypal-capture.php  —  Capture PayPal order after buyer approval
// POST body: { order_id, email, name, plan, book_id }
// Returns: { token } on success or { error }
// ─────────────────────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$body    = json_decode(file_get_contents('php://input'), true);
$orderId = trim($body['order_id'] ?? '');
$email   = strtolower(trim($body['email'] ?? ''));
$name    = trim($body['name']   ?? '');
$plan    = $body['plan']        ?? '';
$bookId  = intval($body['book_id'] ?? 0);

if (!$orderId) jsonResponse(['error' => 'Missing order ID'], 400);

// Get access token
function getPayPalAccessToken(): string {
    $ch = curl_init(PAYPAL_API_URL . '/v1/oauth2/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
        CURLOPT_USERPWD        => PAYPAL_CLIENT_ID . ':' . PAYPAL_CLIENT_SECRET,
        CURLOPT_HTTPHEADER     => ['Accept: application/json', 'Accept-Language: en_US'],
        CURLOPT_TIMEOUT        => 30,
    ]);
    $resp = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $resp['access_token'] ?? '';
}

$accessToken = getPayPalAccessToken();
if (!$accessToken) jsonResponse(['error' => 'PayPal authentication failed'], 500);

// Capture the order
$ch = curl_init(PAYPAL_API_URL . '/v2/checkout/orders/' . $orderId . '/capture');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => '{}',
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken,
    ],
    CURLOPT_TIMEOUT        => 30,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$capture = json_decode($response, true);
$captureStatus = $capture['status'] ?? '';

if ($httpCode !== 201 || $captureStatus !== 'COMPLETED') {
    error_log('PayPal capture error: ' . $response);
    jsonResponse(['error' => 'Payment capture failed. Please contact support.'], 500);
}

// Extract capture ID
$captureId = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? '';

// Update DB
$db   = getDB();
$stmt = $db->prepare('
    UPDATE payments
    SET status = "completed", payment_id = ?
    WHERE order_id = ? AND status = "created"
');
$stmt->execute([$captureId, $orderId]);
$wasFreshlyCompleted = $stmt->rowCount() > 0;

// Fetch setup data.
$stmt = $db->prepare('SELECT setup_token, email, name, plan FROM payments WHERE order_id = ?');
$stmt->execute([$orderId]);
$row = $stmt->fetch();

if (!$row) {
    jsonResponse(['error' => 'Payment record not found. Please contact support.'], 404);
}

if ($wasFreshlyCompleted && !empty($row['setup_token'])) {
    // Email send failure should not block successful payment response.
    sendSetupLinkEmail(
        (string)$row['email'],
        (string)($row['name'] ?? ''),
        (string)$row['setup_token'],
        (string)($row['plan'] ?? '')
    );
}

jsonResponse(['token' => $row['setup_token']]);
