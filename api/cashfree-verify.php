<?php
// ─────────────────────────────────────────────────────────────────────────────
// api/cashfree-verify.php  —  Verify Cashfree order payment status
// POST body: { order_id }
// Returns: { token } on success or { error }
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
    error_log('Cashfree verify curl error: ' . $curlError);
    jsonResponse(['error' => 'Unable to verify payment right now. Please retry in a minute.'], 502);
}
if ($httpCode !== 200 || empty($order['order_status'])) {
    error_log('Cashfree verify API error: ' . (string)$response);
    $gatewayError = $order['message'] ?? '';
    jsonResponse(['error' => $gatewayError !== '' ? $gatewayError : 'Unable to verify payment.'], 500);
}
if ($order['order_status'] !== 'PAID') {
    jsonResponse(['error' => 'Payment is not completed yet. Please finish payment and try again.'], 400);
}

$paymentId = '';
if (!empty($order['cf_order_id'])) {
    $paymentId = (string)$order['cf_order_id'];
} elseif (!empty($order['order_id'])) {
    $paymentId = (string)$order['order_id'];
}

$db = getDB();
$stmt = $db->prepare('
    UPDATE payments
    SET status = "completed", payment_id = ?
    WHERE order_id = ? AND status = "created"
');
$stmt->execute([$paymentId, $orderId]);

if ($stmt->rowCount() === 0) {
    $stmt = $db->prepare('SELECT setup_token FROM payments WHERE order_id = ? AND status = "completed"');
    $stmt->execute([$orderId]);
    $existing = $stmt->fetch();
    if ($existing && !empty($existing['setup_token'])) {
        jsonResponse(['token' => $existing['setup_token']]);
    }
    jsonResponse(['error' => 'Order not found'], 404);
}

$stmt = $db->prepare('SELECT setup_token, email, name, plan FROM payments WHERE order_id = ?');
$stmt->execute([$orderId]);
$row = $stmt->fetch();

if ($row && !empty($row['setup_token'])) {
    sendSetupLinkEmail(
        (string)$row['email'],
        (string)($row['name'] ?? ''),
        (string)$row['setup_token'],
        (string)($row['plan'] ?? '')
    );
}

jsonResponse(['token' => (string)($row['setup_token'] ?? '')]);
