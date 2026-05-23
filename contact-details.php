<?php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Contact details for AI Prompt Books customer support and business communication.">
<title>Contact Details — <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></title>
<link rel="canonical" href="<?= htmlspecialchars(rtrim(SITE_URL, '/'), ENT_QUOTES, 'UTF-8') ?>/contact-details.php">
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
      <div class="section-badge">Support & Compliance</div>
      <h1 class="section-title">Contact Details</h1>
      <p class="section-sub">Official support channels for customer queries, account access issues, and payment communication.</p>
    </div>
  </section>

  <main class="page-wrap">
    <article class="policy-card">
      <h2>Business Name</h2>
      <p><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></p>

      <h2>Support Email</h2>
      <p><a class="policy-email" href="mailto:<?= htmlspecialchars(MAIL_FROM_EMAIL, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(MAIL_FROM_EMAIL, ENT_QUOTES, 'UTF-8') ?></a></p>

      <h2>Website</h2>
      <p><a class="policy-email" href="https://www.aipromptbooks.in/">https://www.aipromptbooks.in/</a></p>

      <h2>Support Hours</h2>
      <p>Monday to Saturday, 10:00 AM to 7:00 PM IST</p>

      <h2>Support Channel</h2>
      <p>Email-only support is currently available for all customer, billing, and access requests.</p>

      <h2>Help Us Support You Faster</h2>
      <p>Please include your payment email and purchase date when contacting support.</p>
    </article>
  </main>

  <footer class="footer">
    <div class="footer-logo">AI Prompt Books</div>
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
