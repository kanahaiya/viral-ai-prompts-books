<?php
// ─────────────────────────────────────────────────────────────────────────────
// api/cashfree-order.php  —  Create a Cashfree order (INR)
// POST body: { plan, book_id, book_ids, email, name, phone }
// Returns: { order_id, payment_session_id, amount, currency } or { error }
// ─────────────────────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$body = json_decode(file_get_contents('php://input'), true);
$plan = $body['plan'] ?? '';
$bookId = intval($body['book_id'] ?? 0);
$bookIdsRaw = is_array($body['book_ids'] ?? null) ? $body['book_ids'] : [];
$email = strtolower(trim($body['email'] ?? ''));
$name = trim($body['name'] ?? '');
$rawPhone = trim((string)($body['phone'] ?? ''));
$phoneDigits = preg_replace('/\D+/', '', $rawPhone);
$phone = '';
$phoneLength = strlen($phoneDigits);
if ($phoneLength === 10) {
    $phone = $phoneDigits;
} elseif ($phoneLength === 12 && str_starts_with($phoneDigits, '91')) {
    $phone = substr($phoneDigits, 2);
}
$bookIds = array_values(array_unique(array_filter(array_map('intval', $bookIdsRaw), static function ($id) {
    return $id >= 1 && $id <= 11;
})));

if (!in_array($plan, ['single', 'bundle'], true)) {
    jsonResponse(['error' => 'Invalid plan'], 400);
}
if ($plan === 'single' && empty($bookIds)) {
    if ($bookId >= 1 && $bookId <= 11) {
        $bookIds = [$bookId];
    } else {
        jsonResponse(['error' => 'Select at least one valid book'], 400);
    }
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['error' => 'Invalid email'], 400);
}
if ($phone === '') {
    jsonResponse(['error' => 'Please enter a valid 10-digit phone number'], 400);
}

$amountInr = $plan === 'bundle'
    ? PRICE_BUNDLE_INR
    : (count($bookIds) * PRICE_SINGLE_INR);

if (!function_exists('curl_init')) {
    jsonResponse(['error' => 'Server payment module is unavailable (cURL disabled).'], 500);
}

$cashfreeBaseUrl = (defined('CASHFREE_ENV') && CASHFREE_ENV === 'sandbox')
    ? 'https://sandbox.cashfree.com'
    : 'https://api.cashfree.com';
$orderId = 'cf_order_' . time() . '_' . bin2hex(random_bytes(4));

$payload = json_encode([
    'order_id' => $orderId,
    'order_amount' => $amountInr,
    'order_currency' => 'INR',
    'order_note' => $plan === 'bundle' ? 'Full bundle purchase' : 'Single plan purchase',
    'customer_details' => [
        'customer_id' => 'cust_' . substr(hash('sha256', $email), 0, 20),
        'customer_email' => $email,
        'customer_name' => $name !== '' ? $name : 'Customer',
        'customer_phone' => $phone,
    ],
    'order_meta' => [
        'return_url' => rtrim(SITE_URL, '/') . '/setup-account.php',
    ],
]);

$ch = curl_init($cashfreeBaseUrl . '/pg/orders');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'x-api-version: 2023-08-01',
        'x-client-id: ' . CASHFREE_APP_ID,
        'x-client-secret: ' . CASHFREE_SECRET_KEY,
    ],
    CURLOPT_TIMEOUT => 30,
]);
$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$order = json_decode((string)$response, true);
if ($response === false || $curlError !== '') {
    error_log('Cashfree order curl error: ' . $curlError);
    jsonResponse(['error' => 'Unable to connect to payment gateway from server. Please try again shortly.'], 502);
}
if (($httpCode !== 200 && $httpCode !== 201) || empty($order['payment_session_id']) || empty($order['order_id'])) {
    error_log('Cashfree order API error: ' . (string)$response);
    $gatewayError = $order['message'] ?? ($order['error']['description'] ?? '');
    jsonResponse(['error' => $gatewayError !== '' ? $gatewayError : 'Failed to create payment order. Please try again.'], 500);
}

try {
    $db = getDB();
    $token = generateToken(32);
    $bookIdsJson = $plan === 'single' ? json_encode($bookIds) : null;

    $paymentMethod = 'cashfree';
    try {
        $paymentMethodCheckStmt = $db->query("SHOW COLUMNS FROM payments LIKE 'payment_method'");
        $paymentMethodColumn = $paymentMethodCheckStmt ? $paymentMethodCheckStmt->fetch() : null;
        $columnType = strtolower((string)($paymentMethodColumn['Type'] ?? ''));
        if ($columnType !== '' && strpos($columnType, "'cashfree'") === false) {
            $paymentMethod = 'razorpay';
        }
    } catch (Throwable $schemaCheckError) {
        // SQLite or unsupported SHOW COLUMNS, keep cashfree.
    }

    $hasBookIdsJsonColumn = false;
    try {
        $columnCheckStmt = $db->query("SHOW COLUMNS FROM payments LIKE 'book_ids_json'");
        $hasBookIdsJsonColumn = $columnCheckStmt !== false && (bool)$columnCheckStmt->fetch();
    } catch (Throwable $schemaCheckError) {
        $hasBookIdsJsonColumn = true;
    }

    if (!$hasBookIdsJsonColumn && $plan === 'single' && count($bookIds) > 1) {
        jsonResponse(['error' => 'Multi-book checkout requires DB update. Add payments.book_ids_json column and retry.'], 500);
    }

    if ($hasBookIdsJsonColumn) {
        $stmt = $db->prepare('
            INSERT INTO payments (email, name, plan, book_id, book_ids_json, amount, currency, payment_method, order_id, status, setup_token)
            VALUES (?, ?, ?, ?, ?, ?, "INR", ?, ?, "created", ?)
        ');
        $stmt->execute([
            $email,
            $name,
            $plan,
            $plan === 'single' && count($bookIds) === 1 ? $bookIds[0] : null,
            $bookIdsJson,
            $amountInr,
            $paymentMethod,
            $order['order_id'],
            $token,
        ]);
    } else {
        $stmt = $db->prepare('
            INSERT INTO payments (email, name, plan, book_id, amount, currency, payment_method, order_id, status, setup_token)
            VALUES (?, ?, ?, ?, ?, "INR", ?, ?, "created", ?)
        ');
        $stmt->execute([
            $email,
            $name,
            $plan,
            $plan === 'single' && count($bookIds) === 1 ? $bookIds[0] : null,
            $amountInr,
            $paymentMethod,
            $order['order_id'],
            $token,
        ]);
    }
} catch (Throwable $databaseError) {
    error_log('Cashfree payment DB write error: ' . $databaseError->getMessage());
    jsonResponse(['error' => 'Payment setup failed while saving order. Please verify DB config and try again.'], 500);
}

jsonResponse([
    'order_id' => $order['order_id'],
    'payment_session_id' => $order['payment_session_id'],
    'amount' => $amountInr,
    'currency' => 'INR',
]);
