<?php
// ─────────────────────────────────────────────────────────────────────────────
// api/paypal-capture.php  —  Capture PayPal order after buyer approval
// POST body: { order_id, email, name, plan, book_id }
// Returns: { setup_url } on success or { error }
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

enforcePaymentRateLimit(
    'payment_verify_paypal',
    getPaymentRateLimitIdentifier($email, $orderId),
    12,
    300
);

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

$db = getDB();
$paymentStmt = $db->prepare('
    SELECT amount, currency, email, name, plan, setup_used, status, setup_token
    FROM payments
    WHERE payment_method = "paypal"
      AND order_id = ?
    LIMIT 1
');
$paymentStmt->execute([$orderId]);
$paymentRow = $paymentStmt->fetch();
if (!$paymentRow) {
    jsonResponse(['error' => 'Payment record not found. Please contact support.'], 404);
}
if ((int)$paymentRow['setup_used'] === 1) {
    jsonResponse(['error' => 'This order setup link has already been used.'], 409);
}
if ((string)$paymentRow['status'] === 'completed' && !empty($paymentRow['setup_token'])) {
    jsonResponse(['setup_url' => '/setup-account.php']);
}

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
    recordPaymentFailure('paypal', $orderId, 'paypal_capture_failed', (string)$captureStatus);
    error_log('PayPal capture error: ' . $response);
    jsonResponse(['error' => 'Payment capture failed. Please contact support.'], 500);
}

// Extract capture ID
$captureId = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? '';

// Update DB
$db->beginTransaction();
try {
    $gatewayAmount = (int)round(((float)($capture['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? 0)) * 100);
    $expectedAmount = (int)round(((float)$paymentRow['amount']) * 100);
    $gatewayCurrency = strtoupper((string)($capture['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'] ?? ''));
    if ($gatewayAmount <= 0 || $gatewayAmount !== $expectedAmount || $gatewayCurrency !== strtoupper((string)$paymentRow['currency'])) {
        $db->rollBack();
        error_log('PayPal capture mismatch for order ' . $orderId);
        jsonResponse(['error' => 'Payment record mismatch detected. Please contact support.'], 400);
    }

    $setupToken = generateToken(32);
    $setupTokenHash = hashSecurityToken($setupToken);
    $updateStmt = $db->prepare('
        UPDATE payments
        SET status = "completed", payment_id = ?, setup_token = ?, completed_at = CURRENT_TIMESTAMP
        WHERE payment_method = "paypal"
          AND order_id = ?
          AND setup_used = 0
          AND status = "created"
    ');
    $updateStmt->execute([$captureId, $setupTokenHash, $orderId]);
    if ($updateStmt->rowCount() === 0) {
        $db->rollBack();
        jsonResponse(['error' => 'Unable to finalize payment setup.'], 409);
    }
    $db->commit();

    sendSetupLinkEmail(
        (string)$paymentRow['email'],
        (string)($paymentRow['name'] ?? ''),
        $setupToken,
        (string)($paymentRow['plan'] ?? '')
    );
    setSetupClaimToken($setupToken);
    jsonResponse(['setup_url' => '/setup-account.php']);
} catch (Throwable $exception) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log('PayPal capture verify failed: ' . $exception->getMessage());
    jsonResponse(['error' => 'Payment verification failed. Please contact support.'], 500);
}
