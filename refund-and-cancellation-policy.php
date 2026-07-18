<?php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Refund and cancellation policy for AI Prompt Books digital products and account access purchases.">
<title>Refund and Cancellation Policy — <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></title>
<link rel="canonical" href="<?= htmlspecialchars(rtrim(SITE_URL, '/'), ENT_QUOTES, 'UTF-8') ?>/refund-and-cancellation-policy.php">
<link rel="icon" type="image/png" href="/assets/icons/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Oswald:wght@700&display=swap">
<link rel="stylesheet" href="/assets/legal-pages.css">
</head>
<body>
  <header class="nav">
    <div class="nav-inner">
      <a class="nav-logo" href="/">
        <img src="/assets/icons/logo-gold-quill.webp" alt="" class="nav-logo__icon" width="36" height="36">
        <span class="nav-logo__text">
          <span class="nav-logo__name">AI PROMPT SYSTEM</span>
          <span class="nav-logo__sub">Interactive AI Prompt Generator</span>
        </span>
      </a>
      <nav class="nav-links" aria-label="Policy links">
        <a class="nav-link" href="/">Back to Home</a>
      </nav>
    </div>
  </header>

  <section class="hero">
    <div class="hero-inner">
      <div class="section-badge">Legal</div>
      <h1 class="section-title">Refund and Cancellation Policy</h1>
      <p class="section-sub">Please review this policy before purchasing digital products from AI Prompt Books.</p>
    </div>
  </section>

  <main class="page-wrap">
    <article class="policy-card">
      <p class="policy-date">Last updated: <?= date('F j, Y') ?></p>

      <h2>1. Digital Product Nature</h2>
      <p>Our products are delivered digitally with instant access after successful payment. Because delivery is instant, there's no separate cancellation window before delivery — instead, you're covered by the refund guarantee below regardless of how quickly access was issued.</p>

      <h2>2. Refund Eligibility</h2>
      <p>We offer a 7-day money-back guarantee on all purchases. If you're not satisfied for any reason, email us within 7 days of your purchase date and we'll issue a full refund — no questions asked. Approved refunds are returned to your original payment method within 5–7 business days via Razorpay.</p>

      <h2>3. How to Request a Refund</h2>
      <p>Email <a class="policy-email" href="mailto:<?= htmlspecialchars(MAIL_FROM_EMAIL, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(MAIL_FROM_EMAIL, ENT_QUOTES, 'UTF-8') ?></a> with your order email and purchase date. We'll confirm and process eligible refunds within 1 business day of your request.</p>

      <h2>4. Technical Access Issues</h2>
      <p>If your login or access setup fails due to a technical issue, contact us within 24 hours of purchase and we'll prioritize fixing it immediately. This is separate from, and in addition to, your 7-day refund right above.</p>

      <h2>5. Duplicate or Incorrect Charges</h2>
      <p>If you're charged more than once for the same order, contact support with your payment details and we'll review and resolve it.</p>

      <h2>6. Support Contact</h2>
      <p>For billing and access assistance, email <a class="policy-email" href="mailto:<?= htmlspecialchars(MAIL_FROM_EMAIL, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(MAIL_FROM_EMAIL, ENT_QUOTES, 'UTF-8') ?></a>.</p>
    </article>
  </main>

  <footer class="footer">
    <div class="footer-links">
      <a href="/contact-details.php">Contact</a>
      <a href="/terms-and-conditions.php">Terms</a>
      <a href="/privacy-policy.php">Privacy Policy</a>
      <a href="/refund-and-cancellation-policy.php">Refund Policy</a>
    </div>
    <div class="footer-copy">© <?= date('Y') ?> <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?>. All rights reserved.</div>
  </footer>
</body>
</html>
