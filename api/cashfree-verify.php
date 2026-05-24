<?php
// ─────────────────────────────────────────────────────────────────────────────
// api/cashfree-verify.php  —  Verify Cashfree order payment status
// POST body: { order_id }
// Returns: { setup_url } on success or { error }
// ─────────────────────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$body = json_decode(file_get_contents('php://input'), true);
$orderId = trim((string)($body['order_id'] ?? ''));
if ($orderId === '') {
    jsonResponse(['error' => 'Missing order ID'], 400);
}

enforcePaymentRateLimit(
    'payment_verify_cashfree',
    getPaymentRateLimitIdentifier('', $orderId),
    12,
    300
);
if (!function_exists('curl_init')) {
    jsonResponse(['error' => 'Server payment module is unavailable (cURL disabled).'], 500);
}

$cashfreeBaseUrl = (defined('CASHFREE_ENV') && CASHFREE_ENV === 'sandbox')
    ? 'https://sandbox.cashfree.com'
    : 'https://api.cashfree.com';

$ch = curl_init($cashfreeBaseUrl . '/pg/orders/' . rawurlencode($orderId));
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPGET => true,
    CURLOPT_HTTPHEADER => [
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
    recordPaymentFailure('cashfree', $orderId, 'cashfree_verify_curl_error');
    error_log('Cashfree verify curl error: ' . $curlError);
    jsonResponse(['error' => 'Unable to verify payment right now. Please retry in a minute.'], 502);
}
if ($httpCode !== 200 || empty($order['order_status'])) {
    recordPaymentFailure('cashfree', $orderId, 'cashfree_verify_http_error', 'HTTP_' . (string)$httpCode);
    error_log('Cashfree verify API error: ' . (string)$response);
    $gatewayError = $order['message'] ?? '';
    jsonResponse(['error' => $gatewayError !== '' ? $gatewayError : 'Unable to verify payment.'], 500);
}
if ($order['order_status'] !== 'PAID') {
    recordPaymentFailure('cashfree', $orderId, 'cashfree_order_not_paid', (string)$order['order_status']);
    jsonResponse(['error' => 'Payment is not completed yet. Please finish payment and try again.'], 400);
}

$paymentId = '';
if (!empty($order['cf_order_id'])) {
    $paymentId = (string)$order['cf_order_id'];
} elseif (!empty($order['order_id'])) {
    $paymentId = (string)$order['order_id'];
}

$db = getDB();
$db->beginTransaction();
try {
    $paymentStmt = $db->prepare('
        SELECT amount, currency, email, name, plan, setup_used, status, setup_token
        FROM payments
        WHERE payment_method = "cashfree"
          AND order_id = ?
        LIMIT 1
    ');
    $paymentStmt->execute([$orderId]);
    $paymentRow = $paymentStmt->fetch();
    if (!$paymentRow) {
        $db->rollBack();
        jsonResponse(['error' => 'Order not found'], 404);
    }
    if ((int)$paymentRow['setup_used'] === 1) {
        $db->rollBack();
        jsonResponse(['error' => 'This order setup link has already been used.'], 409);
    }
    if ((string)$paymentRow['status'] === 'completed' && !empty($paymentRow['setup_token'])) {
        $db->rollBack();
        jsonResponse(['setup_url' => '/setup-account.php']);
    }
    $gatewayAmount = (int)round(((float)($order['order_amount'] ?? 0)) * 100);
    $expectedAmount = (int)round(((float)$paymentRow['amount']) * 100);
    $gatewayCurrency = strtoupper((string)($order['order_currency'] ?? ''));
    if ($gatewayAmount <= 0 || $gatewayAmount !== $expectedAmount || $gatewayCurrency !== strtoupper((string)$paymentRow['currency'])) {
        $db->rollBack();
        error_log('Cashfree verify mismatch for order ' . $orderId);
        jsonResponse(['error' => 'Payment record mismatch detected. Please contact support.'], 400);
    }

    $setupToken = generateToken(32);
    $setupTokenHash = hashSecurityToken($setupToken);
    $updateStmt = $db->prepare('
        UPDATE payments
        SET status = "completed", payment_id = ?, setup_token = ?, completed_at = CURRENT_TIMESTAMP
        WHERE payment_method = "cashfree"
          AND order_id = ?
          AND setup_used = 0
          AND status = "created"
    ');
    $updateStmt->execute([$paymentId, $setupTokenHash, $orderId]);
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
    error_log('Cashfree verify failed: ' . $exception->getMessage());
    jsonResponse(['error' => 'Payment verification failed. Please contact support.'], 500);
}
