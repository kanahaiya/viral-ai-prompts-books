<?php
// ─────────────────────────────────────────────────────────────────────────────
// api/razorpay-verify.php  —  Verify Razorpay payment signature
// POST body: { razorpay_payment_id, razorpay_order_id, razorpay_signature, email, name, plan, book_id, book_ids }
// Returns: { setup_url } on success or { error }
// ─────────────────────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$body      = json_decode(file_get_contents('php://input'), true);
$paymentId = $body['razorpay_payment_id'] ?? '';
$orderId   = $body['razorpay_order_id']   ?? '';
$signature = $body['razorpay_signature']  ?? '';
$email     = strtolower(trim($body['email'] ?? ''));
$name      = trim($body['name']   ?? '');
$plan      = $body['plan']        ?? '';
$bookId    = intval($body['book_id'] ?? 0);

if (!$paymentId || !$orderId || !$signature) {
    jsonResponse(['error' => 'Missing payment details'], 400);
}

enforcePaymentRateLimit(
    'payment_verify_razorpay',
    getPaymentRateLimitIdentifier('', $orderId),
    12,
    300
);

// Verify HMAC-SHA256 signature
$expectedSig = hash_hmac('sha256', $orderId . '|' . $paymentId, RAZORPAY_KEY_SECRET);
if (!hash_equals($expectedSig, $signature)) {
    error_log("Razorpay signature mismatch: order=$orderId payment=$paymentId");
    jsonResponse(['error' => 'Payment signature verification failed. Please contact support.'], 400);
}

// Signature is valid — mark payment as completed and return setup token
$db = getDB();
$db->beginTransaction();
try {
    $paymentStmt = $db->prepare('
        SELECT id, amount, currency, email, name, plan, setup_used, status, setup_token
        FROM payments
        WHERE payment_method = "razorpay"
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

    $expectedAmountPaise = (int)round(((float)$paymentRow['amount']) * 100);
    if ($expectedAmountPaise <= 0 || strtoupper((string)$paymentRow['currency']) !== 'INR') {
        $db->rollBack();
        error_log('Razorpay verify mismatch for order ' . $orderId);
        jsonResponse(['error' => 'Payment record mismatch detected. Please contact support.'], 400);
    }
    if (!function_exists('curl_init')) {
        $db->rollBack();
        jsonResponse(['error' => 'Server payment module is unavailable (cURL disabled).'], 500);
    }
    $paymentDetailsCurl = curl_init('https://api.razorpay.com/v1/payments/' . rawurlencode($paymentId));
    curl_setopt_array($paymentDetailsCurl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPGET => true,
        CURLOPT_USERPWD => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
        CURLOPT_TIMEOUT => 20,
    ]);
    $paymentDetailsResponse = curl_exec($paymentDetailsCurl);
    $paymentDetailsHttpCode = curl_getinfo($paymentDetailsCurl, CURLINFO_HTTP_CODE);
    curl_close($paymentDetailsCurl);
    $gatewayPayment = json_decode((string)$paymentDetailsResponse, true);
    $gatewayAmount = (int)($gatewayPayment['amount'] ?? 0);
    $gatewayCurrency = strtoupper((string)($gatewayPayment['currency'] ?? ''));
    $gatewayOrderId = (string)($gatewayPayment['order_id'] ?? '');
    $gatewayStatus = strtoupper((string)($gatewayPayment['status'] ?? ''));
    $isCaptured = (bool)($gatewayPayment['captured'] ?? false);
    if (
        $paymentDetailsHttpCode !== 200
        || $gatewayAmount !== $expectedAmountPaise
        || $gatewayCurrency !== 'INR'
        || $gatewayOrderId !== $orderId
        || $gatewayStatus !== 'CAPTURED'
        || !$isCaptured
    ) {
        $db->rollBack();
        error_log('Razorpay gateway verification mismatch for order ' . $orderId);
        jsonResponse(['error' => 'Payment amount verification failed. Please contact support.'], 400);
    }

    $setupToken = generateToken(32);
    $setupTokenHash = hashSecurityToken($setupToken);
    $updateStmt = $db->prepare('
        UPDATE payments
        SET status = "completed", payment_id = ?, setup_token = ?, completed_at = CURRENT_TIMESTAMP
        WHERE payment_method = "razorpay"
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
    error_log('Razorpay verify failed: ' . $exception->getMessage());
    jsonResponse(['error' => 'Payment verification failed. Please contact support.'], 500);
}
