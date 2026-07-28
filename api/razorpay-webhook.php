<?php
// ─────────────────────────────────────────────────────────────────────────────
// api/razorpay-webhook.php — Razorpay Webhook (server-to-server)
// Catches payment.captured events even if the browser callback failed.
// Configure this URL in Razorpay Dashboard → Webhooks:
//   https://www.aipromptbooks.in/api/razorpay-webhook.php
//   Event: payment.captured
// ─────────────────────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/auth.php';

// Razorpay sends JSON POST
$rawBody = file_get_contents('php://input');

// Verify webhook signature
$webhookSecret = defined('RAZORPAY_WEBHOOK_SECRET') ? RAZORPAY_WEBHOOK_SECRET : '';
if ($webhookSecret === '') {
    error_log('Razorpay webhook: RAZORPAY_WEBHOOK_SECRET not configured.');
    http_response_code(500);
    exit('Webhook secret not configured');
}

$receivedSignature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';
if ($receivedSignature === '') {
    error_log('Razorpay webhook: Missing X-Razorpay-Signature header.');
    http_response_code(400);
    exit('Missing signature');
}

$expectedSignature = hash_hmac('sha256', $rawBody, $webhookSecret);
if (!hash_equals($expectedSignature, $receivedSignature)) {
    error_log('Razorpay webhook: Signature verification failed.');
    http_response_code(400);
    exit('Invalid signature');
}

// Parse event
$event = json_decode($rawBody, true);
if (!$event || !is_array($event)) {
    http_response_code(400);
    exit('Invalid JSON');
}

$eventType = $event['event'] ?? '';

// We only handle payment.captured
if ($eventType !== 'payment.captured') {
    // Acknowledge other events without processing
    http_response_code(200);
    exit('Event ignored');
}

$payload = $event['payload']['payment']['entity'] ?? [];
$paymentId = $payload['id'] ?? '';
$orderId   = $payload['order_id'] ?? '';
$amount    = $payload['amount'] ?? 0;   // in paise
$email     = strtolower(trim($payload['email'] ?? ($payload['notes']['email'] ?? '')));

if (!$orderId || !$paymentId) {
    error_log('Razorpay webhook: Missing order_id or payment_id in payload.');
    http_response_code(400);
    exit('Missing order/payment id');
}

// Mark payment as completed (idempotent — skips if already completed)
try {
    $db = getDB();

    // Check if already completed (avoid duplicate processing)
    $checkStmt = $db->prepare('SELECT id, status, setup_token, email, name, plan FROM payments WHERE order_id = ?');
    $checkStmt->execute([$orderId]);
    $payment = $checkStmt->fetch();

    if (!$payment) {
        // Order not found in our DB — could be from a different integration
        error_log("Razorpay webhook: order_id={$orderId} not found in payments table.");
        http_response_code(200);
        exit('Order not in DB');
    }

    if ($payment['status'] === 'completed') {
        // Already processed (likely by razorpay-verify.php) — nothing to do
        http_response_code(200);
        exit('Already completed');
    }

    // Update status to completed
    $updateStmt = $db->prepare('
        UPDATE payments
        SET status = ?, payment_id = ?
        WHERE order_id = ? AND status = ?
    ');
    $updateStmt->execute(['completed', $paymentId, $orderId, 'created']);

    if ($updateStmt->rowCount() === 0) {
        // Race condition or unexpected state — log and exit gracefully
        error_log("Razorpay webhook: Could not update order_id={$orderId} (race condition or unexpected status).");
        http_response_code(200);
        exit('No update needed');
    }

    // Send setup email (same as razorpay-verify.php does)
    $setupToken = $payment['setup_token'] ?? '';
    $recipientEmail = $payment['email'] ?? $email;
    $recipientName  = $payment['name'] ?? '';
    $plan           = $payment['plan'] ?? '';

    if ($setupToken && $recipientEmail) {
        $emailSent = sendSetupLinkEmail($recipientEmail, $recipientName, $setupToken, $plan);
        if (!$emailSent) {
            error_log("Razorpay webhook: Email send failed for order_id={$orderId}, email={$recipientEmail}");
        }
    }

    // Trigger Meta CAPI event
    $fullPayment = $db->prepare('SELECT * FROM payments WHERE order_id = ?');
    $fullPayment->execute([$orderId]);
    $fullRow = $fullPayment->fetch();
    if ($fullRow) {
        sendMetaCapiPurchaseEvent($fullRow);
    }

    error_log("Razorpay webhook: Successfully processed payment.captured for order_id={$orderId}");

} catch (Throwable $e) {
    error_log('Razorpay webhook error: ' . $e->getMessage());
    http_response_code(500);
    exit('Internal error');
}

// Return 200 so Razorpay doesn't retry
http_response_code(200);
exit('OK');
