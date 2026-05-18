<?php
// ─────────────────────────────────────────────────────────────────────────────
// api/razorpay-verify.php  —  Verify Razorpay payment signature
// POST body: { razorpay_payment_id, razorpay_order_id, razorpay_signature, email, name, plan, book_id, book_ids }
// Returns: { token } on success or { error }
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

// Verify HMAC-SHA256 signature
$expectedSig = hash_hmac('sha256', $orderId . '|' . $paymentId, RAZORPAY_KEY_SECRET);
if (!hash_equals($expectedSig, $signature)) {
    error_log("Razorpay signature mismatch: order=$orderId payment=$paymentId");
    jsonResponse(['error' => 'Payment signature verification failed. Please contact support.'], 400);
}

// Signature is valid — mark payment as completed and return setup token
$db   = getDB();
$stmt = $db->prepare('
    UPDATE payments
    SET status = "completed", payment_id = ?
    WHERE order_id = ? AND status = "created"
');
$stmt->execute([$paymentId, $orderId]);

if ($stmt->rowCount() === 0) {
    // Already processed — fetch existing token
    $stmt = $db->prepare('SELECT setup_token FROM payments WHERE order_id = ? AND status = "completed"');
    $stmt->execute([$orderId]);
    $row = $stmt->fetch();
    if ($row) {
        jsonResponse(['token' => $row['setup_token']]);
    }
    jsonResponse(['error' => 'Order not found'], 404);
}

// Get the setup token
$stmt = $db->prepare('SELECT setup_token FROM payments WHERE order_id = ?');
$stmt->execute([$orderId]);
$row = $stmt->fetch();

jsonResponse(['token' => $row['setup_token']]);
