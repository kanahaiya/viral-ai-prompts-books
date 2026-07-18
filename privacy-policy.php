<?php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Privacy policy for AI Prompt Books. Learn what data we collect, why we collect it, and how we protect it.">
<title>Privacy Policy — <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></title>
<link rel="canonical" href="<?= htmlspecialchars(rtrim(SITE_URL, '/'), ENT_QUOTES, 'UTF-8') ?>/privacy-policy.php">
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
      <h1 class="section-title">Privacy Policy</h1>
      <p class="section-sub">This page explains what customer data we collect, how we use it, and how we keep it secure.</p>
    </div>
  </section>

  <main class="page-wrap">
    <article class="policy-card">
      <p class="policy-date">Last updated: <?= date('F j, Y') ?></p>

      <h2>1. Data We Collect</h2>
      <p>We collect only the information needed to process payments and provide product access, including your name, email address, and account credentials.</p>

      <h2>2. How We Use Data</h2>
      <p>Your data is used to create your account, deliver digital product access, support account recovery, and handle payment-related communication.</p>

      <h2>3. Payment Processing</h2>
      <p>Payments are handled by secure third-party gateways. We do not store your full card or UPI credentials on our servers.</p>

      <h2>4. Cookies and Session Data</h2>
      <p>We use secure session cookies for login and account access. These cookies help keep your account authenticated and protected.</p>

      <h2>5. Data Sharing</h2>
      <p>We do not sell your personal data. Data is shared only with essential service providers needed for payments, email delivery, and website operations.</p>

      <h2>6. Data Retention</h2>
      <p>We retain purchase and account records for support, compliance, and fraud prevention purposes, and only for as long as reasonably required.</p>

      <h2>7. Your Rights</h2>
      <p>You can request correction or deletion of your personal data by contacting support. Some records may be retained where required for legal or financial compliance.</p>

      <h2>8. Contact</h2>
      <p>For privacy-related requests, email <a class="policy-email" href="mailto:<?= htmlspecialchars(MAIL_FROM_EMAIL, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(MAIL_FROM_EMAIL, ENT_QUOTES, 'UTF-8') ?></a>.</p>
    </article>
  </main>

  <footer class="footer">
    <div class="footer-links">
      <a href="/contact-details.php">Contact</a>
      <a href="/terms-and-conditions.php">Terms</a>
      <a href="/privacy-policy.php">Privacy Policy</a>
      <a href="/refund-and-cancellation-policy.php">Refund Policy</a>
    </div>
    <div class="footer-copy">© <?= date('Y') ?> AI Prompt System. All rights reserved.</div>
  </footer>
</body>
</html>
