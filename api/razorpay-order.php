<?php
// ─────────────────────────────────────────────────────────────────────────────
// api/razorpay-order.php  —  Create a Razorpay order (INR)
// POST body: { plan, book_id, email, name }
// Returns: { id, amount, currency } or { error }
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

// Validate
if (!in_array($plan, ['single', 'bundle'], true)) jsonResponse(['error' => 'Invalid plan'], 400);
if ($plan === 'single' && ($bookId < 1 || $bookId > 11))  jsonResponse(['error' => 'Invalid book'], 400);
if (!filter_var($email, FILTER_VALIDATE_EMAIL))            jsonResponse(['error' => 'Invalid email'], 400);

$amountPaise = ($plan === 'bundle')
    ? PRICE_BUNDLE_INR * 100
    : PRICE_SINGLE_INR * 100;

// Create Razorpay order via API
$payload = json_encode([
    'amount'          => $amountPaise,
    'currency'        => 'INR',
    'receipt'         => 'order_' . time(),
    'notes'           => ['plan' => $plan, 'book_id' => $bookId, 'email' => $email, 'name' => $name],
]);

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_USERPWD        => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
    CURLOPT_TIMEOUT        => 30,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$order = json_decode($response, true);

if ($httpCode !== 200 || empty($order['id'])) {
    error_log('Razorpay order error: ' . $response);
    jsonResponse(['error' => 'Failed to create payment order. Please try again.'], 500);
}

// Store pending payment in DB
$db   = getDB();
$stmt = $db->prepare('
    INSERT INTO payments (email, name, plan, book_id, amount, currency, payment_method, order_id, status, setup_token)
    VALUES (?, ?, ?, ?, ?, "INR", "razorpay", ?, "created", ?)
');
$token = generateToken(32);
$stmt->execute([
    $email,
    $name,
    $plan,
    $plan === 'single' ? $bookId : null,
    PRICE_BUNDLE_INR * ($plan === 'bundle' ? 1 : 0) + PRICE_SINGLE_INR * ($plan === 'single' ? 1 : 0),
    $order['id'],
    $token,
]);

jsonResponse(['id' => $order['id'], 'amount' => $amountPaise, 'currency' => 'INR']);
