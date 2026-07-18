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
      <h1 class="section-title">Terms and Conditions</h1>
      <p class="section-sub">Please read these terms before purchasing and using AI Prompt Books digital products.</p>
    </div>
  </section>

  <main class="page-wrap">
    <article class="policy-card">
      <p class="policy-date">Last updated: July 18, 2025</p>

      <h2>1. Overview</h2>
      <p>AI Prompt Books provides digital educational products, including prompt books and related resources for AI image generation.</p>

      <h2>2. Digital Product Access</h2>
      <p>All products sold on this website are digital. No physical goods are shipped. Access is delivered online after successful payment.</p>

      <h2>3. Account Responsibility</h2>
      <p>You are responsible for maintaining the confidentiality of your login credentials and for all activity under your account.</p>

      <h2>4. License and Use</h2>
      <p>Purchased content is licensed for your personal or internal business use. You may not resell, redistribute, or republish the product files as your own.</p>

      <h2>5. Payments</h2>
      <p>Payments are processed through secure third-party gateways, including Razorpay. By completing payment, you agree to the listed price and product details at checkout.</p>

      <h2>6. Pricing Changes</h2>
      <p>We reserve the right to change prices for future purchases at any time, including at the end of any early-access or promotional pricing window. Price changes apply only to new purchases made after the change takes effect and do not affect or apply retroactively to purchases already completed.</p>

      <h2>7. Service Availability</h2>
      <p>We strive to provide uninterrupted access, but temporary downtime may occur due to maintenance, updates, or third-party service issues.</p>

      <h2>8. Limitation of Liability</h2>
      <p>We provide educational templates and guidance. Results can vary by tool, prompt variation, and user inputs. We do not guarantee specific business outcomes.</p>

      <h2>9. Governing Law and Jurisdiction</h2>
      <p>These Terms are governed by the laws of India. Any disputes arising out of or relating to these Terms or your use of AI Prompt Books shall be subject to the exclusive jurisdiction of the courts in Bangalore, Karnataka.</p>

      <h2>10. Contact</h2>
      <p>For support, contact <a class="policy-email" href="mailto:<?= htmlspecialchars(MAIL_FROM_EMAIL, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(MAIL_FROM_EMAIL, ENT_QUOTES, 'UTF-8') ?></a>.</p>
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
