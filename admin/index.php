<?php
// ─────────────────────────────────────────────────────────────────────────────
// admin/index.php  —  Protected admin dashboard
// Protected by /admin/.htaccess (HTTP Basic Auth)
// ─────────────────────────────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/auth.php';

$db = getDB();

// Stats
$stats = $db->query('
    SELECT
      COUNT(*) AS total_users,
      SUM(plan = "bundle") AS bundle_users,
      SUM(plan = "single") AS single_users,
      SUM(currency = "INR" AND plan = "bundle") * 299 +
      SUM(currency = "INR" AND plan = "single") * 99 AS inr_revenue,
      SUM(currency = "USD" AND plan = "bundle") * 9 +
      SUM(currency = "USD" AND plan = "single") * 2.99 AS usd_revenue
    FROM users
    WHERE status = "active"
')->fetch();

$recentUsers = $db->query('
    SELECT id, email, name, plan, currency, payment_method, status, created_at
    FROM users ORDER BY created_at DESC LIMIT 50
')->fetchAll();

$recentPayments = $db->query('
    SELECT id, email, plan, book_id, amount, currency, payment_method, status, created_at
    FROM payments ORDER BY created_at DESC LIMIT 50
')->fetchAll();

// Filter
$filterStatus = $_GET['status'] ?? '';
$filterPlan   = $_GET['plan']   ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" href="/assets/icons/favicon.png">
<title>Admin — AI Prompt Books</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Segoe UI',Arial,sans-serif;background:#0a0a0a;color:#e8e4de;padding:2rem;font-size:14px;}
h1{font-size:1.4rem;font-weight:900;color:#fff;margin-bottom:0.3rem;}
.sub{font-size:0.8rem;color:#666;margin-bottom:2rem;}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:2.5rem;}
.stat{background:#141414;border:1px solid #2a2a2a;border-radius:4px;padding:1.2rem 1.5rem;}
.stat-num{font-family:'Courier New',monospace;font-size:1.8rem;font-weight:900;color:#d4a836;line-height:1;}
.stat-label{font-size:0.72rem;color:#666;letter-spacing:1px;margin-top:4px;text-transform:uppercase;}
.section-title{font-family:'Courier New',monospace;font-size:0.7rem;letter-spacing:3px;text-transform:uppercase;color:#d4a836;margin-bottom:0.8rem;margin-top:2rem;padding-top:2rem;border-top:1px solid #1a1a1a;}
table{width:100%;border-collapse:collapse;background:#111;border:1px solid #2a2a2a;border-radius:4px;overflow:hidden;}
th{font-family:'Courier New',monospace;font-size:0.65rem;letter-spacing:1px;text-transform:uppercase;color:#666;padding:10px 14px;text-align:left;background:#141414;border-bottom:1px solid #2a2a2a;}
td{padding:9px 14px;border-bottom:1px solid #1a1a1a;font-size:0.82rem;color:#ccc;vertical-align:middle;}
tr:last-child td{border-bottom:none;}
tr:hover td{background:#151515;}
.badge{font-family:'Courier New',monospace;font-size:0.62rem;font-weight:700;letter-spacing:1px;padding:2px 8px;border-radius:2px;text-transform:uppercase;}
.badge-bundle{background:#d4a836;color:#000;}
.badge-single{background:#2a2a2a;color:#d4a836;}
.badge-active{background:#064e3b;color:#6ee7b7;}
.badge-razorpay{background:#12324a;color:#60a5fa;}
.badge-paypal{background:#0c2454;color:#93c5fd;}
.badge-inr{background:#1c1005;color:#d4a836;}
.badge-usd{background:#05100a;color:#4ade80;}
.text-dim{color:#555;}
</style>
</head>
<body>
<h1>Admin Dashboard</h1>
<div class="sub">Last refreshed: <?= date('d M Y H:i:s') ?> — <a href="" style="color:#d4a836;">Refresh</a></div>

<!-- STATS -->
<div class="stats-grid">
  <div class="stat">
    <div class="stat-num"><?= intval($stats['total_users']) ?></div>
    <div class="stat-label">Total Users</div>
  </div>
  <div class="stat">
    <div class="stat-num"><?= intval($stats['bundle_users']) ?></div>
    <div class="stat-label">Bundle Buyers</div>
  </div>
  <div class="stat">
    <div class="stat-num"><?= intval($stats['single_users']) ?></div>
    <div class="stat-label">Single Buyers</div>
  </div>
  <div class="stat">
    <div class="stat-num">₹<?= number_format(floatval($stats['inr_revenue'])) ?></div>
    <div class="stat-label">INR Revenue</div>
  </div>
  <div class="stat">
    <div class="stat-num">$<?= number_format(floatval($stats['usd_revenue']), 2) ?></div>
    <div class="stat-label">USD Revenue</div>
  </div>
</div>

<!-- USERS -->
<div class="section-title">Recent Users (last 50)</div>
<table>
  <thead>
    <tr>
      <th>#</th><th>Email</th><th>Name</th><th>Plan</th>
      <th>Currency</th><th>Payment</th><th>Status</th><th>Joined</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($recentUsers as $u): ?>
    <tr>
      <td class="text-dim"><?= $u['id'] ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= htmlspecialchars($u['name'] ?? '—') ?></td>
      <td><span class="badge badge-<?= $u['plan'] ?>"><?= strtoupper($u['plan']) ?></span></td>
      <td><span class="badge badge-<?= strtolower($u['currency']) ?>"><?= $u['currency'] ?></span></td>
      <td><span class="badge badge-<?= $u['payment_method'] ?>"><?= ucfirst($u['payment_method']) ?></span></td>
      <td><span class="badge badge-active"><?= $u['status'] ?></span></td>
      <td class="text-dim"><?= date('d M y H:i', strtotime($u['created_at'])) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$recentUsers): ?>
    <tr><td colspan="8" style="text-align:center;color:#555;padding:2rem;">No users yet.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<!-- PAYMENTS -->
<div class="section-title">Recent Payments (last 50)</div>
<table>
  <thead>
    <tr>
      <th>#</th><th>Email</th><th>Plan</th><th>Book</th>
      <th>Amount</th><th>Method</th><th>Status</th><th>Date</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($recentPayments as $p): ?>
    <tr>
      <td class="text-dim"><?= $p['id'] ?></td>
      <td><?= htmlspecialchars($p['email']) ?></td>
      <td><span class="badge badge-<?= $p['plan'] ?>"><?= strtoupper($p['plan']) ?></span></td>
      <td><?= $p['book_id'] ? 'Book ' . $p['book_id'] : '—' ?></td>
      <td style="color:#d4a836;font-family:'Courier New',monospace;">
        <?= $p['currency'] === 'INR' ? '₹' : '$' ?><?= number_format(floatval($p['amount']), 2) ?>
      </td>
      <td><span class="badge badge-<?= $p['payment_method'] ?>"><?= ucfirst($p['payment_method']) ?></span></td>
      <td style="color:<?= $p['status'] === 'completed' ? '#4ade80' : ($p['status'] === 'failed' ? '#f87171' : '#888') ?>">
        <?= $p['status'] ?>
      </td>
      <td class="text-dim"><?= date('d M y H:i', strtotime($p['created_at'])) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$recentPayments): ?>
    <tr><td colspan="8" style="text-align:center;color:#555;padding:2rem;">No payments yet.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

</body>
</html>
