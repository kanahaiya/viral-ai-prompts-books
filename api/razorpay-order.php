<?php
// ─────────────────────────────────────────────────────────────────────────────
// api/razorpay-order.php  —  Create a Razorpay order (INR)
// POST body: { plan, book_id, book_ids, email, name }
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
$bookIdsRaw = is_array($body['book_ids'] ?? null) ? $body['book_ids'] : [];
$email  = strtolower(trim($body['email'] ?? ''));
$name   = trim($body['name']  ?? '');
$bookIds = array_values(array_unique(array_filter(array_map('intval', $bookIdsRaw), static function ($id) {
    return $id >= 1 && $id <= 11;
})));

// Validate
if (!in_array($plan, ['single', 'bundle'], true)) jsonResponse(['error' => 'Invalid plan'], 400);
if ($plan === 'single') {
    if (empty($bookIds)) {
        if ($bookId >= 1 && $bookId <= 11) {
            $bookIds = [$bookId];
        } else {
            jsonResponse(['error' => 'Select at least one valid book'], 400);
        }
    }
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL))            jsonResponse(['error' => 'Invalid email'], 400);

$amountPaise = ($plan === 'bundle')
    ? PRICE_BUNDLE_INR * 100
    : (count($bookIds) * PRICE_SINGLE_INR * 100);

if (!function_exists('curl_init')) {
    jsonResponse(['error' => 'Server payment module is unavailable (cURL disabled). Enable cURL in hosting PHP settings.'], 500);
}

// Create Razorpay order via API
$payload = json_encode([
    'amount'          => $amountPaise,
    'currency'        => 'INR',
    'receipt'         => 'order_' . time(),
    'notes'           => [
        'plan' => $plan,
        'book_id' => $bookId,
        'book_ids' => implode(',', $bookIds),
        'email' => $email,
        'name' => $name,
    ],
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
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$order = json_decode($response, true);

if ($response === false || !empty($curlError)) {
    error_log('Razorpay order curl error: ' . $curlError);
    jsonResponse(['error' => 'Unable to connect to payment gateway from server. Please try again in a minute.'], 502);
}

if ($httpCode !== 200 || empty($order['id'])) {
    error_log('Razorpay order API error: ' . $response);
    $gatewayError = $order['error']['description'] ?? '';
    if (!empty($gatewayError)) {
        jsonResponse(['error' => $gatewayError], 500);
    }
    jsonResponse(['error' => 'Failed to create payment order. Please try again.'], 500);
}

// Store pending payment in DB
try {
    $db   = getDB();
    $token = generateToken(32);
    $bookIdsJson = $plan === 'single' ? json_encode($bookIds) : null;

    $hasBookIdsJsonColumn = false;
    $columnCheckStmt = $db->query("SHOW COLUMNS FROM payments LIKE 'book_ids_json'");
    if ($columnCheckStmt !== false && $columnCheckStmt->fetch()) {
        $hasBookIdsJsonColumn = true;
    }

    if (!$hasBookIdsJsonColumn && $plan === 'single' && count($bookIds) > 1) {
        jsonResponse(['error' => 'Multi-book checkout requires DB update. Add payments.book_ids_json column and retry.'], 500);
    }

    if ($hasBookIdsJsonColumn) {
        $stmt = $db->prepare('
            INSERT INTO payments (email, name, plan, book_id, book_ids_json, amount, currency, payment_method, order_id, status, setup_token)
            VALUES (?, ?, ?, ?, ?, ?, "INR", "razorpay", ?, "created", ?)
        ');
        $stmt->execute([
            $email,
            $name,
            $plan,
            $plan === 'single' && count($bookIds) === 1 ? $bookIds[0] : null,
            $bookIdsJson,
            $plan === 'bundle' ? PRICE_BUNDLE_INR : (count($bookIds) * PRICE_SINGLE_INR),
            $order['id'],
            $token,
        ]);
    } else {
        $stmt = $db->prepare('
            INSERT INTO payments (email, name, plan, book_id, amount, currency, payment_method, order_id, status, setup_token)
            VALUES (?, ?, ?, ?, ?, "INR", "razorpay", ?, "created", ?)
        ');
        $stmt->execute([
            $email,
            $name,
            $plan,
            $plan === 'single' && count($bookIds) === 1 ? $bookIds[0] : null,
            $plan === 'bundle' ? PRICE_BUNDLE_INR : PRICE_SINGLE_INR,
            $order['id'],
            $token,
        ]);
    }
} catch (Throwable $databaseError) {
    error_log('Razorpay payment DB write error: ' . $databaseError->getMessage());
    jsonResponse(['error' => 'Payment setup failed while saving order. Please verify DB config and try again.'], 500);
}

jsonResponse(['id' => $order['id'], 'amount' => $amountPaise, 'currency' => 'INR']);
