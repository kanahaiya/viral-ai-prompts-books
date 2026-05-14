<?php
// ─────────────────────────────────────────────────────────────────────────────
// api/paypal-order.php  —  Create a PayPal order (USD)
// POST body: { plan, book_id, email, name }
// Returns: { id } or { error }
// ─────────────────────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$body   = json_decode(file_get_contents('php://input'), true);
$plan   = $body['plan']    ?? '';
$bookId = intval($body['book_id'] ?? 0);
$email  = strtolower(trim($body['email'] ?? ''));
$name   = trim($body['name']  ?? '');

if (!in_array($plan, ['single', 'bundle'], true)) jsonResponse(['error' => 'Invalid plan'], 400);
if (!filter_var($email, FILTER_VALIDATE_EMAIL))    jsonResponse(['error' => 'Invalid email'], 400);

$amount = ($plan === 'bundle') ? number_format(PRICE_BUNDLE_USD, 2, '.', '') : number_format(PRICE_SINGLE_USD, 2, '.', '');
$desc   = ($plan === 'bundle') ? 'AI Prompt Books — Full Bundle (All 11 Books)' : 'AI Prompt Books — Single Book Access';

// Get PayPal access token
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

// Create order
$payload = json_encode([
    'intent'         => 'CAPTURE',
    'purchase_units' => [[
        'amount'      => ['currency_code' => 'USD', 'value' => $amount],
        'description' => $desc,
        'custom_id'   => json_encode(['plan' => $plan, 'book_id' => $bookId, 'email' => $email]),
    ]],
    'application_context' => [
        'brand_name'          => SITE_NAME,
        'user_action'         => 'PAY_NOW',
        'shipping_preference' => 'NO_SHIPPING',
    ],
]);

$ch = curl_init(PAYPAL_API_URL . '/v2/checkout/orders');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken,
        'PayPal-Request-Id: ' . uniqid('aipb_', true),
    ],
    CURLOPT_TIMEOUT        => 30,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$order = json_decode($response, true);

if ($httpCode !== 201 || empty($order['id'])) {
    error_log('PayPal order error: ' . $response);
    jsonResponse(['error' => 'Failed to create PayPal order. Please try again.'], 500);
}

// Store pending payment
$db    = getDB();
$token = generateToken(32);
$db->prepare('
    INSERT INTO payments (email, name, plan, book_id, amount, currency, payment_method, order_id, status, setup_token)
    VALUES (?, ?, ?, ?, ?, "USD", "paypal", ?, "created", ?)
')->execute([
    $email, $name, $plan,
    $plan === 'single' ? $bookId : null,
    $plan === 'bundle' ? PRICE_BUNDLE_USD : PRICE_SINGLE_USD,
    $order['id'],
    $token,
]);

jsonResponse(['id' => $order['id']]);
