<?php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Terms and conditions for purchasing and using AI Prompt Books digital products.">
<title>Terms and Conditions — <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></title>
<link rel="canonical" href="<?= htmlspecialchars(rtrim(SITE_URL, '/'), ENT_QUOTES, 'UTF-8') ?>/terms-and-conditions.php">
<link rel="icon" type="image/png" href="/assets/icons/favicon.png">
<link rel="stylesheet" href="/assets/legal-pages.css">
</head>
<body>
  <header class="nav">
    <div class="nav-inner">
      <a class="nav-logo" href="/">AI Prompt Books</a>
      <nav class="nav-links" aria-label="Policy links">
        <a class="nav-link" href="/">Back to Home</a>
      </nav>
    </div>
  </header>

  <section class="hero">
    <div class="hero-inner">
      <div class="section-badge">Legal</div>
      <h1 class="section-title">Terms and Conditions</h1>
      <p class="section-sub">Please read these terms before purchasing and using AI Prompt Books digital products.</p>
    </div>
  </section>

  <main class="page-wrap">
    <article class="policy-card">
      <p class="policy-date">Last updated: <?= date('F j, Y') ?></p>

      <h2>1. Overview</h2>
      <p><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?> provides digital educational products, including prompt books and related resources for AI image generation.</p>

      <h2>2. Digital Product Access</h2>
      <p>All products sold on this website are digital. No physical goods are shipped. Access is delivered online after successful payment.</p>

      <h2>3. Account Responsibility</h2>
      <p>You are responsible for maintaining the confidentiality of your login credentials and for all activity under your account.</p>

      <h2>4. License and Use</h2>
      <p>Purchased content is licensed for your personal or internal business use. You may not resell, redistribute, or republish the product files as your own.</p>

      <h2>5. Payments</h2>
      <p>Payments are processed through secure third-party gateways. By completing payment, you agree to the listed price and product details at checkout.</p>

      <h2>6. Service Availability</h2>
      <p>We strive to provide uninterrupted access, but temporary downtime may occur due to maintenance, updates, or third-party service issues.</p>

      <h2>7. Limitation of Liability</h2>
      <p>We provide educational templates and guidance. Results can vary by tool, prompt variation, and user inputs. We do not guarantee specific business outcomes.</p>

      <h2>8. Contact</h2>
      <p>For support, contact <a class="policy-email" href="mailto:<?= htmlspecialchars(MAIL_FROM_EMAIL, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(MAIL_FROM_EMAIL, ENT_QUOTES, 'UTF-8') ?></a>.</p>
    </article>
  </main>

  <footer class="footer">
    <div class="footer-logo">AI Prompt Books</div>
    <div class="footer-links">
      <a href="/#pricing">Pricing</a>
      <a href="/#faq">FAQ</a>
      <a href="/contact-details.php">Contact</a>
      <a href="/terms-and-conditions.php">Terms</a>
      <a href="/privacy-policy.php">Privacy Policy</a>
      <a href="/refund-and-cancellation-policy.php">Refund Policy</a>
      <a href="/login.php">Login</a>
    </div>
    <div class="footer-copy">© <?= date('Y') ?> <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?>. All rights reserved.</div>
  </footer>
</body>
</html>
