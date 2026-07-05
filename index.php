<?php
require_once __DIR__ . '/auth.php';
$loggedIn = isLoggedIn();
$books    = getBooks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" href="/assets/icons/logo-gold-quill-32.png">
<title>Viral AI Prompts System — 11 Books, 1,100 AI Image Templates</title>
<meta name="description" content="The Viral AI Prompts System — 11 books, 1,100 fill-in-the-blank templates. Create better AI images faster with Midjourney, ChatGPT, Firefly and more.">
<link rel="canonical" href="<?= htmlspecialchars(rtrim(SITE_URL, '/')) ?>/">
<meta property="og:type" content="website">
<meta property="og:site_name" content="AI Prompt System">
<meta property="og:title" content="Viral AI Prompts System — 11 Books, 1,100 AI Image Templates">
<meta property="og:description" content="Create stunning AI images in minutes with 11 prompt books and 1,100 fill-in-the-blank templates.">
<meta property="og:url" content="https://www.aipromptbooks.in/">
<meta property="og:image" content="https://www.aipromptbooks.in/assets/og/og-image.jpg">
<meta property="og:image:alt" content="AI Prompt System preview showing all 11 books and bonus resources">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Viral AI Prompts System — 11 Books, 1,100 AI Image Templates">
<meta name="twitter:description" content="Create stunning AI images in minutes with 11 prompt books and 1,100 fill-in-the-blank templates.">
<meta name="twitter:image" content="https://www.aipromptbooks.in/assets/og/og-image.jpg">
<meta name="facebook-domain-verification" content="80mehcxtq5lqugdfns9r4t854kzmeh">
<meta name="theme-color" content="#0a0a0a">
<link rel="dns-prefetch" href="//checkout.razorpay.com">
<link rel="dns-prefetch" href="//connect.facebook.net">
<link rel="preconnect" href="https://checkout.razorpay.com" crossorigin>
<link rel="preconnect" href="https://connect.facebook.net" crossorigin>
<!-- Preload video poster for instant hero display -->
<link rel="preload" as="image" href="/assets/video/hero-demo-poster.webp" fetchpriority="high">
<!-- Preload Responsive LCP Hero Image -->
<link rel="preload" as="image" href="assets/hero-mockup.webp" 
      imagesrcset="assets/hero-mockup-480w.webp 480w, assets/hero-mockup-768w.webp 768w, assets/hero-mockup.webp 900w" 
      imagesizes="(max-width: 960px) 92vw, 50vw" fetchpriority="high">
<!-- Preload Active Payment Gateway SDK Script for Instant Payment Modal Render -->
<?php
$activePaymentProvider = defined('PAYMENT_PROVIDER') ? PAYMENT_PROVIDER : 'razorpay';
if ($activePaymentProvider === 'cashfree'): ?>
  <link rel="preload" as="script" href="https://sdk.cashfree.com/js/v3/cashfree.js">
<?php else: ?>
  <link rel="preload" as="script" href="https://checkout.razorpay.com/v1/checkout.js">
<?php endif; ?>
<?php
$landingCriticalCssWebPath = '/assets/landing-critical.css';
$landingCriticalCssDiskPath = __DIR__ . $landingCriticalCssWebPath;
if (!file_exists($landingCriticalCssDiskPath)) {
  $landingCriticalCssWebPath = '/assets/landing-critical.min.css';
  $landingCriticalCssDiskPath = __DIR__ . $landingCriticalCssWebPath;
}
$landingCriticalCssVersion = file_exists($landingCriticalCssDiskPath) ? ('?v=' . filemtime($landingCriticalCssDiskPath)) : '';

$landingCssWebPath = '/assets/landing.css';
$landingCssDiskPath = __DIR__ . $landingCssWebPath;
if (!file_exists($landingCssDiskPath)) {
  $landingCssWebPath = '/assets/landing.min.css';
  $landingCssDiskPath = __DIR__ . $landingCssWebPath;
}
$landingCssVersion = file_exists($landingCssDiskPath) ? ('?v=' . filemtime($landingCssDiskPath)) : '';

$landingJsWebPath = '/assets/landing.js';
$landingJsDiskPath = __DIR__ . $landingJsWebPath;
if (!file_exists($landingJsDiskPath)) {
  $landingJsWebPath = '/assets/landing.min.js';
  $landingJsDiskPath = __DIR__ . $landingJsWebPath;
}
$landingJsVersion = file_exists($landingJsDiskPath) ? ('?v=' . filemtime($landingJsDiskPath)) : '';
?>
<link rel="preload" as="style" href="<?= htmlspecialchars($landingCssWebPath . $landingCssVersion, ENT_QUOTES, 'UTF-8') ?>">
<link rel="preload" as="script" href="<?= htmlspecialchars($landingJsWebPath . $landingJsVersion, ENT_QUOTES, 'UTF-8') ?>">
<?php if (file_exists($landingCriticalCssDiskPath)): ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Montserrat:wght@700;800;900&family=Oswald:wght@700&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Montserrat:wght@700;800;900&family=Oswald:wght@700&display=swap">
  <style><?= file_get_contents($landingCriticalCssDiskPath) ?></style>
<?php else: ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($landingCriticalCssWebPath . $landingCriticalCssVersion, ENT_QUOTES, 'UTF-8') ?>">
<?php endif; ?>
<link rel="stylesheet" href="<?= htmlspecialchars($landingCssWebPath . $landingCssVersion, ENT_QUOTES, 'UTF-8') ?>" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="<?= htmlspecialchars($landingCssWebPath . $landingCssVersion, ENT_QUOTES, 'UTF-8') ?>"></noscript>
<?php renderMetaPixelHead(); ?>
</head>
<body>
<?php renderMetaPixelNoScript(); ?>
<a href="#main" class="skip-link">Skip to main content</a>

<!-- NAV -->
<nav class="nav">
  <div class="nav-inner">
    <a href="/" class="nav-logo" aria-label="AI Prompt System home">
      <img src="/assets/icons/logo-gold-quill.webp" alt="" class="nav-logo__icon" width="28" height="28" loading="eager" decoding="async">
      <span class="nav-logo__text">
        <span class="nav-logo__name">AI PROMPT SYSTEM</span>
        <span class="nav-logo__sub">Interactive AI Prompt Generator</span>
      </span>
    </a>
    <div class="nav-center">
      <a href="#how-it-works" class="nav-link">How It Works</a>
      <a href="#offer-stack" class="nav-link">What's Inside</a>
      <a href="#reviews" class="nav-link">Reviews</a>
      <a href="#faq" class="nav-link">FAQ</a>
    </div>
    <div class="nav-links">
      <?php if ($loggedIn): ?>
        <a href="/dashboard.php" class="nav-cta">MY BOOKS →</a>
      <?php else: ?>
        <a href="#pricing" class="nav-cta">GET INSTANT ACCESS — ₹299 →</a>
        <a href="/login.php" class="nav-login">LOGIN</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main id="main">
<!-- HERO -->
<section class="hero" id="hero">
  <div class="hero-bg"></div>
  <div class="hero-container">

    <div class="hero-split">

      <!-- Left: copy -->
      <div class="hero-copy">

        <p class="hero-eyebrow"><span class="hero-eyebrow__star" aria-hidden="true">✨</span> No Prompt Engineering Required</p>

        <h1 class="hero-h1">
          <span class="hero-h1__stop">
            <span class="hero-h1__word">STOP WRITING</span>
            <span class="hero-h1__word hero-h1__word--gold">AI PROMPTS.</span>
          </span>
          <span class="hero-h1__sub">Fill a Few Fields.<br><span class="gold">Your Prompt Writes Itself.</span></span>
        </h1>

        <p class="hero-body">Answer a few short questions. Your prompt writes itself — paste it into ChatGPT and create professional AI images in minutes. No prompt engineering.</p>

        <div class="hero-proof-strip">
          <span class="hero-proof-pill">No Prompt Engineering</span>
          <span class="hero-proof-pill">Works with Top AI Tools</span>
          <span class="hero-proof-pill">1,100+ Tested Prompts</span>
          <span class="hero-proof-pill">Works Instantly in ChatGPT</span>
        </div>

        <div class="hero-ctas">
          <a href="#pricing" class="btn-primary btn-hero-cta" data-action="start-checkout" data-plan="bundle">
            ⚡ Get Full System Access — ₹299 <span class="btn-hero-orig">₹2,189</span>
          </a>
        </div>

        <p class="hero-urgency-line">⏳ Early-access price ends soon — rises to ₹499 after this window closes.</p>
        <p class="hero-founding-line">🚀 Founding Access — be among the first 20+ creators using this system.</p>

        <div class="hero-audience">
          <span class="hero-audience__label">Perfect for:</span>
          <span class="hero-audience__chip">Creators</span>
          <span class="hero-audience__chip">Freelancers</span>
          <span class="hero-audience__chip">Designers</span>
          <span class="hero-audience__chip">Marketers</span>
          <span class="hero-audience__chip">Small Businesses</span>
        </div>

        <p class="hero-payment-trust">
          Lifetime Access
          <span class="hpt-sep" aria-hidden="true">·</span>
          Instant Download
        </p>

        <p class="hero-payment-trust hero-payment-trust--secondary">
          🔒 Secure Razorpay Checkout
          <span class="hpt-sep" aria-hidden="true">·</span>
          ⚡ Instant Access After Payment
          <span class="hpt-sep" aria-hidden="true">·</span>
          7-Day Money-Back Guarantee
        </p>

      </div><!-- /hero-copy -->

      <!-- Right: demo video panel -->
      <div class="hero-visual">
        <div class="hero-demo-panel" id="demo-video">

          <div class="hero-demo-heading">
            <span class="hero-demo-kicker">▶ Watch Your Prompt Build Itself in 10 Seconds</span>
          </div>
          <p class="hero-demo-sub"></p>

          <!-- Fake browser chrome + video -->
          <div class="hero-video-wrap">
            <video
              class="hero-video"
              autoplay
              loop
              muted
              playsinline
              preload="auto"
              poster="/assets/video/hero-demo-poster.webp"
              aria-label="Demo showing how the AI prompt system works: fill a few fields and the prompt writes itself"
              width="1280"
              height="800"
            >
              <source src="/assets/video/hero-demo.webm" type="video/webm">
              <source src="/assets/video/hero-demo.mp4" type="video/mp4">
            </video>
            <button type="button" class="hero-video-fullscreen" aria-label="View demo fullscreen" title="Fullscreen">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 6V2h4M10 2h4v4M14 10v4h-4M6 14H2v-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
          </div>

          <!-- Step labels below video -->
          <div class="demo-steps-strip">
            <div class="dss-step">
              <span class="dss-num">1</span>
              <span>Fill a Few Fields</span>
            </div>
            <div class="dss-arrow" aria-hidden="true">→</div>
            <div class="dss-step">
              <span class="dss-num">2</span>
              <span>Prompt Writes Itself</span>
            </div>
            <div class="dss-arrow" aria-hidden="true">→</div>
            <div class="dss-step">
              <span class="dss-num">3</span>
              <span>Copy with One Click</span>
            </div>
            <div class="dss-arrow" aria-hidden="true">→</div>
            <div class="dss-step">
              <span class="dss-num">4</span>
              <span>Paste into ChatGPT</span>
            </div>
            <div class="dss-arrow" aria-hidden="true">→</div>
            <div class="dss-step">
              <span class="dss-num">5</span>
              <span>Get Stunning AI Images</span>
            </div>
          </div>

        </div><!-- /hero-demo-panel -->

        <!-- AI tools strip — below video panel, same column -->
        <div class="hero-ai-tools-section">
          <p class="hero-ai-tools__label">Compatible With Your Favorite AI Tools</p>
          <div class="hero-ai-tools__list">
            <div class="hero-ai-tool hero-ai-tool--invert">
              <img src="/assets/icons/ai-tools/chatgpt.svg" alt="" width="28" height="28" loading="lazy" decoding="async" aria-hidden="true">
              <span>ChatGPT</span>
            </div>
            <div class="hero-ai-tool hero-ai-tool--color">
              <img src="/assets/icons/ai-tools/gemini.svg" alt="" width="28" height="28" loading="lazy" decoding="async" aria-hidden="true">
              <span>Gemini</span>
            </div>
            <div class="hero-ai-tool hero-ai-tool--color">
              <img src="/assets/icons/ai-tools/claude.svg" alt="" width="28" height="28" loading="lazy" decoding="async" aria-hidden="true">
              <span>Claude</span>
            </div>
            <div class="hero-ai-tool hero-ai-tool--invert">
              <img src="/assets/icons/ai-tools/midjourney.svg" alt="" width="28" height="28" loading="lazy" decoding="async" aria-hidden="true">
              <span>Midjourney</span>
            </div>
            <div class="hero-ai-tool hero-ai-tool--invert">
              <img src="/assets/icons/ai-tools/flux.svg" alt="" width="28" height="28" loading="lazy" decoding="async" aria-hidden="true">
              <span>Flux</span>
            </div>
            <div class="hero-ai-tool hero-ai-tool--invert">
              <img src="/assets/icons/ai-tools/ideogram.svg" alt="" width="28" height="28" loading="lazy" decoding="async" aria-hidden="true">
              <span>Ideogram</span>
            </div>
            <div class="hero-ai-tool hero-ai-tool--color">
              <img src="/assets/icons/ai-tools/leonardo-ai.svg" alt="" width="28" height="28" loading="lazy" decoding="async" aria-hidden="true">
              <span>Leonardo AI</span>
            </div>
            <div class="hero-ai-tool hero-ai-tool--color">
              <img src="/assets/icons/ai-tools/microsoft-designer.svg" alt="" width="28" height="28" loading="lazy" decoding="async" aria-hidden="true">
              <span>Microsoft Designer</span>
            </div>
            <div class="hero-ai-tool hero-ai-tool--color">
              <img src="/assets/icons/ai-tools/adobe-firefly.svg" alt="" width="28" height="28" loading="lazy" decoding="async" aria-hidden="true">
              <span>Adobe Firefly</span>
            </div>
          </div>
        </div>

      </div><!-- /hero-visual -->

    </div><!-- /hero-split -->

    <!-- Stats bar -->
    <div class="hero-stats-wrap" id="systems">
      <div class="hero-stats">
        <div class="hero-stat">
          <svg class="hero-stat-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D4A836" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
          <div class="hero-stat-content">
            <div class="hero-stat-num">11</div>
            <div class="hero-stat-title">Interactive Prompt Systems</div>
            <div class="hero-stat-label">Covering every creative need</div>
          </div>
        </div>
        <div class="hero-stat">
          <svg class="hero-stat-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D4A836" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="4" rx="1"/><rect x="4" y="10" width="16" height="4" rx="1"/><rect x="4" y="16" width="16" height="4" rx="1"/></svg>
          <div class="hero-stat-content">
            <div class="hero-stat-num">1,100+</div>
            <div class="hero-stat-title">Fill-in-the-Blank Prompts</div>
            <div class="hero-stat-label">High quality &amp; tested</div>
          </div>
        </div>
        <div class="hero-stat">
          <svg class="hero-stat-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D4A836" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18.178 8c5.096 0 5.096 8 0 8-5.095 0-7.133-8-12.739-8-4.585 0-4.585 8 0 8 5.606 0 7.644-8 12.74-8z"/></svg>
          <div class="hero-stat-content">
            <div class="hero-stat-num hero-stat-num--unlimited">Unlimited</div>
            <div class="hero-stat-title">Prompt Combinations</div>
            <div class="hero-stat-label">Create endless possibilities</div>
          </div>
        </div>
        <div class="hero-stat">
          <svg class="hero-stat-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D4A836" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
          <div class="hero-stat-content">
            <div class="hero-stat-num">60s</div>
            <div class="hero-stat-title">To Your First AI Image</div>
            <div class="hero-stat-label">From idea to finished prompt in a minute</div>
          </div>
        </div>
        <div class="hero-stat">
          <svg class="hero-stat-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D4A836" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <div class="hero-stat-content">
            <div class="hero-stat-num">5+</div>
            <div class="hero-stat-title">AI Tools Supported</div>
            <div class="hero-stat-label">One system. Works everywhere.</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Trust strip -->
    <div class="hero-trust-strip">
      <div class="hero-trust-item">
        <img src="/assets/icons/shield-gold.svg" alt="" class="hero-trust-svg" width="20" height="20" loading="lazy" decoding="async">
        <div><strong>One-Time Payment</strong><span>No subscriptions. No recurring fees.</span></div>
      </div>
      <div class="hero-trust-item">
        <img src="/assets/icons/bolt-gold.svg" alt="" class="hero-trust-svg" width="20" height="20" loading="lazy" decoding="async">
        <div><strong>Instant Download</strong><span>Start creating immediately after purchase.</span></div>
      </div>
      <div class="hero-trust-item">
        <svg class="hero-trust-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D4A836" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18.178 8c5.096 0 5.096 8 0 8-5.095 0-7.133-8-12.739-8-4.585 0-4.585 8 0 8 5.606 0 7.644-8 12.74-8z"/></svg>
        <div><strong>Lifetime Access</strong><span>Own it forever with future updates included.</span></div>
      </div>
      <div class="hero-trust-item">
        <img src="/assets/icons/shield-gold.svg" alt="" class="hero-trust-svg" width="20" height="20" loading="lazy" decoding="async">
        <div><strong>Secure Checkout</strong><span>Protected payments powered by Razorpay.</span></div>
      </div>
      <div class="hero-trust-item">
        <img src="/assets/icons/headset-gold.svg" alt="" class="hero-trust-svg" width="20" height="20" loading="lazy" decoding="async">
        <div><strong>7-Day Money-Back Guarantee</strong><span>Try it risk-free.</span></div>
      </div>
    </div>

  </div><!-- /hero-container -->
</section>

<!-- PAIN AGITATION -->
<section class="pain-section">
  <div class="container">
    <h2 class="section-title">Everywhere You Look, People Are Posting<br><span class="text-gold">Incredible AI Art…</span></h2>
    <div class="pain-text">
      <p class="story pain-style-line">Anime portraits. Cinematic posters. Action figure toy boxes. Dreamy edits. Viral Instagram visuals.</p>
      <p class="story">And honestly? You want to create those too.</p>
      <p class="story pain-transition-line">But the moment AI tools open, the frustration starts.</p>
      <ul class="pain-bullets">
        <li><span class="x">✗</span>Random prompts giving random results</li>
        <li><span class="x">✗</span>No idea what to type</li>
        <li><span class="x">✗</span>Hours wasted tweaking words</li>
        <li><span class="x">✗</span>Saved AI art ideas never getting created</li>
        <li><span class="x">✗</span>AI tools feeling technical and overwhelming</li>
        <li><span class="x">✗</span>Final images looking average instead of impressive</li>
      </ul>
      <p class="story">Instead of feeling creative, the whole process starts feeling confusing.</p>
      <p class="pain-punch">Stop Struggling With Prompts</p>
    </div>
  </div>
</section>

<!-- SOLUTION BRIDGE -->
<section class="solution-section" id="how-it-works">
  <div class="container">
    <p class="big-intro">This Makes AI Image Creation Feel <span>Easy</span></p>
    <ul class="solution-no-list">
      <li>No prompt engineering.</li>
      <li>No complicated tutorials.</li>
      <li>No blank-screen frustration.</li>
    </ul>
    <div class="solution-grid">
      <div class="solution-card">
        <div class="solution-card-title">Just</div>
        <ul class="solution-card-list">
          <li class="solution-step"><span class="solution-step-badge">1</span><span>Pick a style</span></li>
          <li class="solution-step"><span class="solution-step-badge">2</span><span>Fill in a few blanks</span></li>
          <li class="solution-step"><span class="solution-step-badge">3</span><span>Paste the prompt</span></li>
          <li class="solution-step"><span class="solution-step-badge">4</span><span>Generate stunning AI visuals in minutes</span></li>
        </ul>
      </div>
      <div class="solution-card">
        <div class="solution-card-title">Create</div>
        <ul class="solution-card-list">
          <li>Anime portraits</li>
          <li>Cinematic posters</li>
          <li>Action figure toy boxes</li>
          <li>Pet transformations</li>
          <li>Professional headshots</li>
          <li>Viral-style AI art</li>
        </ul>
      </div>
    </div>
    <p class="solution-note">Everything is designed for complete beginners.<br>Even if this is the very first time using AI tools.</p>
    <a href="#pricing" class="btn-primary btn-inline">Get Full System Access — ₹299</a>
  </div>
</section>

<!-- BENEFITS -->
<section id="inside" class="benefits-section">
  <div class="container container-center">
    <h2 class="section-title">What You&rsquo;ll Be Able To Create</h2>
    <ul class="create-outcomes-list">
      <li>Create AI images that genuinely make friends and followers stop and react.</li>
      <li>Turn ordinary photos and ideas into visuals that look creative, cinematic, and impressive.</li>
      <li>Make viral-style AI art without spending weeks learning complicated prompting.</li>
      <li>Finally create the kind of AI images usually seen only on trending Instagram pages.</li>
      <li>Go from &ldquo;I have no idea what to type&rdquo; to creating stunning visuals in minutes.</li>
      <li>Make emotional gifts, nostalgic edits, and fun creations people actually remember.</li>
      <li>Create professional-looking visuals without hiring designers or expensive freelancers.</li>
      <li>Feel confident using AI tools even as a complete beginner.</li>
      <li>Save hours of frustration and start creating images that actually match the vision in your mind.</li>
      <li>Post AI visuals proudly instead of feeling embarrassed by average-looking results.</li>
    </ul>
    <a href="#pricing" class="btn-primary btn-inline">Get Full System Access — ₹299</a>
  </div>
</section>

<!-- COLLAGE: Visual Proof -->
<section class="gallery-section">
  <div class="container container-center">
    <h2 class="section-title">Real Outputs. Real Prompts. <span class="text-gold">From This System.</span></h2>
    <p class="section-sub section-sub--wide section-sub--compact">Action figures, Ghibli art, Mughal warriors, royal pet portraits, cinematic movie posters, and professional portraits — all from the same system, all for the same ₹299.</p>
    <div class="gallery-collage-wrap">
      <div class="gallery-img">
        <picture>
          <source type="image/avif" srcset="assets/collage-1-360w.avif 360w, assets/collage-1-520w.avif 520w, assets/collage-1.avif 682w" sizes="(max-width: 600px) 92vw, 520px">
          <source type="image/webp" srcset="assets/collage-1-360w.webp 360w, assets/collage-1-520w.webp 520w, assets/collage-1.webp 682w" sizes="(max-width: 600px) 92vw, 520px">
          <img src="assets/collage-1.jpg" srcset="assets/collage-1-360w.jpg 360w, assets/collage-1-520w.jpg 520w, assets/collage-1.jpg 682w" sizes="(max-width: 600px) 92vw, 520px" alt="AI image examples — action figure, Ghibli anime, Mughal warrior, royal dog portrait, movie poster, rooftop portrait" width="699" height="1024" loading="lazy" fetchpriority="low" decoding="async">
        </picture>
      </div>
    </div>
    <p class="section-footnote">6 styles shown. 1,100+ prompt templates across 11 books — many more styles inside.</p>
  </div>
</section>

<!-- SYSTEM INCLUDES -->
<section class="benefits-section">
  <div class="container container-center">
    <h2 class="section-title">What&rsquo;s Included Inside The System</h2>
    <div class="bundle-collection-visual">
      <picture>
        <source type="image/avif" srcset="assets/sections/bundle-collection-640w.avif 640w, assets/sections/bundle-collection-1024w.avif 1024w, assets/sections/bundle-collection.avif 1600w" sizes="(max-width: 900px) 95vw, 980px">
        <source type="image/webp" srcset="assets/sections/bundle-collection-640w.webp 640w, assets/sections/bundle-collection-1024w.webp 1024w, assets/sections/bundle-collection.webp 1600w" sizes="(max-width: 900px) 95vw, 980px">
        <img src="assets/sections/bundle-collection.jpg" srcset="assets/sections/bundle-collection-640w.jpg 640w, assets/sections/bundle-collection-1024w.jpg 1024w, assets/sections/bundle-collection.jpg 1600w" sizes="(max-width: 900px) 95vw, 980px" alt="Complete Viral AI Prompts System collection showing all 11 books and bonus resources" width="1800" height="900" loading="lazy" fetchpriority="low" decoding="async">
      </picture>
    </div>
    <div class="bundle-group-title">Core Books</div>
    <ul class="bundle-includes-list">
      <li class="bundle-highlight"><strong>11 AI Style Books</strong><span>Each book includes 100 ready-to-use prompts, so creating AI visuals never feels confusing or overwhelming.</span></li>
      <li><strong>All 11 styles shown above</strong><span>Action Figure, Ghibli, Nostalgia, Caricature, Headshots, Product, Cinematic, Scrapbook, Pet, Historical, and Trending Styles.</span></li>
    </ul>
    <div class="bundle-group-title">Templates + Bonus</div>
    <ul class="bundle-includes-list">
      <li class="bundle-highlight"><strong>1,100 Fill-in-the-Blank Prompt Templates</strong><span>Just customize, paste, and generate. No technical prompting skills needed.</span></li>
      <li><strong>AI Image Cheat Code Bonus Guide</strong><span>Simple tricks and beginner-friendly guidance to improve results faster.</span></li>
    </ul>
    <div class="bundle-group-title">Community + Support</div>
    <ul class="bundle-includes-list">
      <li><strong>Private Telegram Community Access</strong><span>Get new ideas, trending styles, support, and inspiration regularly.</span></li>
    </ul>
    <p class="bundle-bridge">Everything you need to go from beginner to confident creator.</p>
    <div class="bundle-pricing-line">
      <div class="bundle-value">Total Value ₹2,189+</div>
      <div class="bundle-price">Today&rsquo;s Price: Just ₹299</div>
    </div>
    <a href="#pricing" class="btn-primary btn-inline btn-inline--sm-top">Get Full System Access — ₹299</a>
  </div>
</section>

<!-- PERFECT FOR -->
<section class="benefits-section">
  <div class="container container-center">
    <h2 class="section-title">This Is Perfect For</h2>
    <ul class="perfect-for-list">
      <li>People who see stunning AI art online and want to create similar visuals themselves.</li>
      <li>Complete beginners who want amazing AI results without learning complicated prompting.</li>
      <li>Instagram creators who want scroll-stopping visuals people actually react to.</li>
      <li>Parents who want to create emotional and memorable AI images for their children and family.</li>
      <li>Freelancers and creators who want professional-looking visuals without hiring designers.</li>
      <li>Pet lovers who want to turn ordinary pet photos into creative AI artwork.</li>
      <li>Small business owners who want better-looking product photos and promotional visuals.</li>
      <li>Anyone who wants to create cool, impressive AI images for fun, gifts, content, or social media.</li>
    </ul>
    <a href="#pricing" class="btn-primary btn-inline">Get Full System Access — ₹299</a>
  </div>
</section>

<!-- OFFER STACK -->
<section id="offer-stack" class="offer-section">
  <div class="container container-center">
    <h2 class="section-title">Unlocks the Full Prompt System + Bonuses.</h2>
    <p class="section-sub section-sub--compact">You&rsquo;re getting a complete, proven system: every book, every bonus, and every future update.</p>
    <div class="offer-list offer-list--redesign">
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 1 — Action Figure &amp; Toy Box</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹199</span></div>
      </div>
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 2 — Ghibli &amp; Anime Style</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹199</span></div>
      </div>
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 3 — Childhood Nostalgia</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹199</span></div>
      </div>
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 4 — Caricature &amp; Chibi</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹199</span></div>
      </div>
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 5 — Professional Headshots</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹199</span></div>
      </div>
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 6 — Product Photography</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹199</span></div>
      </div>
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 7 — Cinematic Movie Poster</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹199</span></div>
      </div>
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 8 — Vintage Scrapbook</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹199</span></div>
      </div>
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 9 — Pet Transformation</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹199</span></div>
      </div>
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 10 — Historical Time Travel</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹199</span></div>
      </div>
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 11 — Trending Styles</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹199</span></div>
      </div>
      <div class="offer-total-row">
        <span class="offer-total-label">Value if bought separately</span>
        <div class="offer-total-price">
          <span class="offer-total-orig" id="offerOrig">₹2,189</span>
          <span class="offer-total-today" id="offerToday">₹299</span>
        </div>
      </div>
    </div>
    <p class="offer-saving-line" id="offerSavingLine">Save ₹1,890 vs. the individual value above — plus 3 bonuses included free. Early-access price: ₹299 → rises to ₹499 once this window closes.</p>
    <a href="#pricing" class="btn-primary btn-inline btn-inline--lg-top">Get Full System Access — ₹299</a>
    <div class="bonus-block">
      <div class="bonus-item">
        <div class="bonus-label">BONUS #1</div>
        <div class="bonus-title">AI Prompt Finder CustomGPT</div>
        <div class="bonus-desc">Describe the image you want, and the CustomGPT instantly recommends the best prompt book and style.<br>Perfect for beginners who are not sure where to start.</div>
      </div>
      <div class="bonus-item">
        <div class="bonus-label">BONUS #2</div>
        <div class="bonus-title">AI Image Cheat Code Ebook</div>
        <div class="bonus-desc">Simple prompt tricks and beginner-friendly guidance to help you create better AI visuals faster.<br>No technical jargon.<br>No overwhelm.</div>
      </div>
      <div class="bonus-item">
        <div class="bonus-label">BONUS #3</div>
        <div class="bonus-title">Private Telegram Community Access</div>
        <div class="bonus-desc">Get:</div>
        <ul class="bonus-points">
          <li>New prompt ideas</li>
          <li>Trending AI style updates</li>
          <li>Creative inspiration</li>
          <li>Beginner support</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- OFFER DETAILS (PRICING) -->
<section id="pricing" class="pricing-section">
  <div class="container container-center">
    <h2 class="section-title">One Price. No Subscription.<br><span class="text-gold">Use It For Years.</span></h2>
    <p class="section-sub section-sub--spaced">No subscription. No renewal. No price creep. Pay once — own it for life, including every new book added to the collection.</p>
    <div class="value-compare">
      <div class="value-card">
        <div class="label">Doing it manually</div>
        <div class="price">10-50 hours</div>
        <p>Researching styles, writing prompts, and fixing failed outputs from scratch.</p>
      </div>
      <div class="value-card">
        <div class="label">Hiring help</div>
        <div class="price">₹2,000+</div>
        <p>One designer session can cost far more than this full prompt library.</p>
      </div>
      <div class="value-card">
        <div class="label">This system</div>
        <div class="price">₹299 once</div>
        <p>11 books, 1,100 templates, bonus resources, and future updates included.</p>
      </div>
    </div>

    <div class="pricing-grid pricing-grid--single">
      <!-- Full System -->
      <div class="price-card popular">
        <div class="price-badge">BEST VALUE</div>
        <div class="price-plan">Full System Access</div>
        <div class="price-amount"><span class="currency">₹</span>299 <span class="original">₹2,189</span></div>
        <div class="price-billing">Save ₹1,890 · All 11 books + bonus resources · Lifetime access</div>
        <div class="price-card-social">🚀 Founding Access — be among the first <strong>20+ creators</strong> using this system</div>
        <ul class="price-features">
          <li>All 11 books — 1,100 prompt templates</li>
          <li>Every trending style covered (Ghibli, posters, pets, fashion…)</li>
          <li>6 personal variable slots per prompt</li>
          <li class="price-feature-tools">Works with Midjourney, ChatGPT, Firefly, and DALL·E</li>
          <li>Interactive online viewer — no downloads</li>
          <li>🎯 Free bonus: The AI Image Cheat Code guide</li>
          <li>🎁 Exclusive Telegram community access</li>
          <li>All future books automatically added</li>
          <li>✅ 7-Day Money-Back Guarantee — try it for 7 days. Not the right fit? Email us for a full refund. No questions asked.</li>
        </ul>
        <button class="btn-buy" data-action="start-checkout" data-plan="bundle">
          Get Full System Access — ₹299 →
        </button>
        <div class="price-risk-reversal">All 11 styles now, plus every future book included.</div>
      </div>
    </div>
    <div class="support-access">
      <div class="support-access-title">SUPPORT &amp; ACCESS</div>
      <ul class="support-access-list">
        <li>Instant Access After Payment</li>
        <li>Works on Mobile &amp; Laptop</li>
        <li>Works with ChatGPT, Midjourney, Firefly &amp; More</li>
        <li>Beginner-Friendly</li>
        <li>Lifetime Access</li>
        <li>Future Updates Included</li>
      </ul>
    </div>

    <p class="pricing-note pricing-note--top" id="pricingAnchor">💡 Early-access system pricing is currently ₹299. Price rises to ₹499 once this window closes.</p>
    <p class="pricing-note">🔒 Secure checkout via Razorpay (India)</p>
    <p class="pricing-note pricing-note--muted">✅ 7-Day Money-Back Guarantee — try it for 7 days. Not the right fit? Email us for a full refund. No questions asked.</p>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials-section" id="reviews">
  <div class="container">
    <h2 class="section-title section-title--center">What Buyers Are Saying</h2>

    <p class="testimonial-lead">Most buyers were not AI experts. They were creators, parents, freelancers, and side-hustlers who wanted better results without hours of trial and error. <strong class="text-muted-strong">These are their practical outcomes.</strong></p>

    <div class="testimonial-grid">

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"Posted my Ghibli portrait on Instagram. Got 146 likes in a day and a few DMs asking which app I used. I tell them I just filled in the template form. Most people don't believe it took under a minute."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar testimonial-avatar--rm">RM</div>
          <div>
            <div class="testimonial-name">Riya M.</div>
            <div class="testimonial-sub">Digital creator · Mumbai</div>
            <div class="testimonial-badge">VERIFIED BUYER</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"Made my son's action figure toy box in 10 mins. He cried happy tears. My wife shared it and three relatives messaged asking where to buy it. I made it myself. That reaction alone was worth 10x what I paid."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar testimonial-avatar--pk">PK</div>
          <div>
            <div class="testimonial-name">Prashant K.</div>
            <div class="testimonial-sub">Software engineer · Pune</div>
            <div class="testimonial-badge">VERIFIED BUYER</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"Using it for client work — headshots, movie posters, pet portraits. My clients think I have a full design team behind me. It's just me and these 11 books. Genuinely worth every rupee."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar testimonial-avatar--as">AS</div>
          <div>
            <div class="testimonial-name">Anika S.</div>
            <div class="testimonial-sub">Freelance designer · Bangalore</div>
            <div class="testimonial-badge">VERIFIED BUYER</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"Opened an Etsy store after buying the movie poster book. Sold a handful of AI movie poster prints in the first two weeks, with a few custom orders I could turn around fast. Recovered the system cost within my first few orders."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar testimonial-avatar--vt">VT</div>
          <div>
            <div class="testimonial-name">Vikram T.</div>
            <div class="testimonial-sub">Graphic designer · Chennai</div>
            <div class="testimonial-badge">VERIFIED BUYER</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★☆</div>
        <p class="testimonial-text">"Husband's 40th was in 3 days, no gift idea. Bought the Scrapbook book, spent maybe 20 minutes turning old photos into a vintage album cover. He got emotional. My sister-in-law still asks where I ordered it from. Would love more step-by-step help for total beginners."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar testimonial-avatar--dr">DR</div>
          <div>
            <div class="testimonial-name">Deepa R.</div>
            <div class="testimonial-sub">Teacher · Hyderabad</div>
            <div class="testimonial-badge">VERIFIED BUYER</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"I create Reels about AI tools. Used these prompts for a Midjourney demo Reel and gained 43 followers that week — better than my usual growth. Other creators asked which prompts I used. I just said I bought a prompt system. At ₹299 for all 11, it still felt like strong value."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar testimonial-avatar--sm">SM</div>
          <div>
            <div class="testimonial-name">Suresh M.</div>
            <div class="testimonial-sub">Content creator · Delhi</div>
            <div class="testimonial-badge">VERIFIED BUYER</div>
          </div>
        </div>
      </div>

    </div>

    <!-- Social proof numbers bar -->
    <div class="social-proof-bar">
      <div class="spb-item">
        <div class="spb-num">20+</div>
        <div class="spb-label">Early Creators Already In</div>
      </div>
      <div class="spb-item">
        <div class="spb-num">11</div>
        <div class="spb-label">AI Style Books</div>
      </div>
      <div class="spb-item">
        <div class="spb-num">60s</div>
        <div class="spb-label">To First Result</div>
      </div>
      <div class="spb-item">
        <div class="spb-num">₹299</div>
        <div class="spb-label">Early Access · System</div>
      </div>
    </div>
  </div>
</section>

<!-- EARLY ACCESS PRICING -->
<section class="benefits-section">
  <div class="container container-center">
    <h2 class="section-title">Early Access Pricing</h2>
    <div class="early-access-copy">
      <p class="early-access-lead">Right now, the complete 11-book system is available for ₹299.</p>
      <p>Value if bought separately: ₹2,189. Once this launch window ends, the price rises to ₹499.</p>
      <p class="early-access-vision">If AI art trends are already everywhere on Instagram and Reels, imagine where they&rsquo;ll be 3 months from now.</p>
      <p class="early-access-punch">Best time to start creating is while the trends, styles, and tools are still fresh.</p>
    </div>
    <div class="offer-bonus-callout offer-bonus-callout--secondary">
      <h3>Bonuses Available During This Offer</h3>
      <p>The AI Prompt Finder CustomGPT, bonus ebook, and private Telegram community are currently included free with the full system.</p>
      <p>These bonuses may not stay included permanently as new updates and books are added later.</p>
      <a href="#pricing" class="btn-secondary btn-inline">Claim My Bonuses</a>
    </div>
    <div class="momentum-copy">
      <h3>Don&rsquo;t Keep Watching Others Create Cool AI Art</h3>
      <p>Every day, more people are turning ordinary photos into cinematic visuals, anime portraits, and viral-style edits.</p>
      <p>Most are not designers. Most are not AI experts. They simply started.</p>
      <p>In a few minutes, this system can be in your inbox and your first image can be live.</p>
      <a href="#pricing" class="btn-primary btn-inline">Make My First AI Image</a>
    </div>
  </div>
</section>

<!-- COLLAGE 2: More Styles -->
<section class="gallery-section gallery-section--alt">
  <div class="container container-center">
    <h2 class="section-title">Still Not Sure? <span class="text-gold">See What Else Is Possible.</span></h2>
    <p class="section-sub section-sub--wide section-sub--compact">Pixar-style portraits, luxury product shots, Barbiecore fashion, childhood nostalgia, festive scrapbooks, and cyberpunk cities — all from this single ₹299 system.</p>
    <div class="gallery-collage-wrap">
      <div class="gallery-img">
        <picture>
          <source type="image/avif" srcset="assets/collage-2-360w.avif 360w, assets/collage-2-520w.avif 520w, assets/collage-2.avif 682w" sizes="(max-width: 600px) 92vw, 520px">
          <source type="image/webp" srcset="assets/collage-2-360w.webp 360w, assets/collage-2-520w.webp 520w, assets/collage-2.webp 682w" sizes="(max-width: 600px) 92vw, 520px">
          <img src="assets/collage-2.jpg" srcset="assets/collage-2-360w.jpg 360w, assets/collage-2-520w.jpg 520w, assets/collage-2.jpg 682w" sizes="(max-width: 600px) 92vw, 520px" alt="AI image examples — Pixar boy, product photography, Barbiecore portrait, firefly magic, Diwali scrapbook, cyberpunk Mumbai" width="699" height="1024" loading="lazy" fetchpriority="low" decoding="async">
        </picture>
      </div>
    </div>
    <p class="section-footnote">Still only 6 of 1,100+ styles. Every prompt works the same way — fill in the blanks, generate, done.</p>
  </div>
</section>

<!-- FAQ -->
<section id="faq">
  <div class="container">
    <h2 class="section-title section-title--center">Still on the Fence?<br><span class="text-gold">These Questions Are For You.</span></h2>
    <div class="faq-list">
      <?php
      $faqs = [
        ['I have never used AI tools before. Will this still work for me?',
         'Yes. The entire system is designed for complete beginners. Just pick a style, fill in a few details, and generate.'],
        ['Do I need to learn prompt engineering?',
         'No. The prompts are already structured for you. No technical AI knowledge needed.'],
        ['Which AI tools does this work with?',
         'It works with ChatGPT, Midjourney, Firefly, DALL·E, Ideogram, and most AI image generators.'],
        ['How much time does it take to create an image?',
         'Most images can be created within a few minutes once the prompt is selected.'],
        ['Will I get instant access after payment?',
         'Yes. Access details are delivered immediately after successful payment.'],
        ['Is this only for creators and designers?',
         'Not at all. It is made for ordinary people, beginners, students, parents, freelancers, and anyone who wants to create impressive AI visuals.'],
        ['What exactly will I receive inside?',
         'You get all 11 AI prompt books, 1,100 prompt templates, bonuses, community access, and future updates.'],
        ['Is the CustomGPT bonus included right now?',
         'Yes. The AI Prompt Finder CustomGPT is currently included free with the system.'],
        ['Do I get support if I get stuck?',
         'Yes. Buyers also get access to the Telegram community for guidance, updates, and support.'],
        ['Is there any refund policy?',
         'Yes. 7-Day Money-Back Guarantee — try it for 7 days. Not the right fit? Email us for a full refund. No questions asked.'],
      ];
      foreach ($faqs as $faqIndex => [$q, $a]):
        $faqAnswerId = 'faq-answer-' . $faqIndex;
      ?>
      <div class="faq-item">
        <button type="button" class="faq-q" aria-expanded="false" aria-controls="<?= htmlspecialchars($faqAnswerId, ENT_QUOTES, 'UTF-8') ?>">
          <?= htmlspecialchars($q) ?>
        </button>
        <div class="faq-a" id="<?= htmlspecialchars($faqAnswerId, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($a) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FINAL CTA -->
<section class="final-cta-section">
  <div class="container">
    <h2>Create Stunning AI Images Without Learning <span>Prompting</span></h2>
    <div class="final-cta-inner">
      <ul class="final-cta-list">
        <li>11 Premium AI Prompt System Books</li>
        <li>1,100 Fill-in-the-Blank Templates</li>
        <li>Bonuses Included</li>
        <li>Lifetime Access</li>
      </ul>
      <div class="final-cta-value">Total Value: ₹2,189+</div>
      <div class="final-cta-price">Today Only: ₹299</div>
      <a href="#pricing" class="btn-primary btn-inline btn-inline--final">
        Get Full System Access — ₹299
      </a>
      <div class="final-cta-micro">Instant email access after payment.</div>
    </div>
  </div>
</section>
</main>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-logo">AI Prompt System</div>
  <div class="footer-links">
    <a href="/contact-details.php">Contact</a>
    <a href="/terms-and-conditions.php">Terms</a>
    <a href="/privacy-policy.php">Privacy Policy</a>
    <a href="/refund-and-cancellation-policy.php">Refund Policy</a>
  </div>
  <div class="footer-copy">© <?= date('Y') ?> AI Prompt System. All rights reserved.</div>
</footer>

<!-- EXIT INTENT POPUP -->
<div class="exit-overlay" id="exitOverlay">
  <div class="exit-popup">
    <button type="button" class="exit-popup-close" data-action="dismiss-exit" aria-label="Close offer popup">×</button>
    <div class="exit-popup-badge">⚡ WAIT — ONE SECOND</div>
    <h3>₹299 closes when this early-access window ends.</h3>
    <p>Drop your email and we'll send you a free sample prompt from Book 2: Ghibli &amp; Anime — and remind you before the price goes to ₹499.</p>
    <div class="exit-popup-form">
      <input type="email" class="exit-popup-input" id="exitEmail" name="exit_email" autocomplete="email" spellcheck="false" aria-label="Email for free sample prompt" placeholder="your@email.com…">
      <button type="button" class="exit-popup-btn" data-action="submit-exit-email">Send Me The Sample</button>
    </div>
    <button type="button" class="exit-popup-dismiss" data-action="dismiss-exit">No thanks, I'll pass on the free sample →</button>
  </div>
</div>

<!-- STICKY MOBILE CTA -->
<div class="sticky-cta" id="stickyCta">
  <div class="sticky-cta-inner">
    <div class="sticky-cta-text">
      <strong>Prompt System — All 11 Books</strong>
      <span id="stickyPrice">₹299 · One-time payment</span>
    </div>
    <button class="sticky-cta-btn" data-action="start-checkout" data-plan="bundle">Get Access →</button>
  </div>
</div>

<!-- CHECKOUT MODAL -->
<div id="checkoutModal" class="app-modal app-modal--checkout">
  <div class="app-modal-card app-modal-card--checkout">
    <div class="app-modal-head app-modal-head--checkout">
      <div class="checkout-head-main">
        <div class="checkout-brand-icon" aria-hidden="true">
          <img src="/assets/icons/logo-gold-quill.webp" alt="" width="28" height="28" loading="eager" decoding="async">
        </div>
        <div class="checkout-head-copy">
          <div id="checkoutTitle" class="checkout-title">Unlock Your Full<br><span class="checkout-title-accent">AI Prompt System</span></div>
          <div id="checkoutSubtitle" class="checkout-subtitle">Get instant access to all 11 interactive prompt books + all bonuses.</div>
        </div>
      </div>
      <button type="button" class="checkout-close-btn" data-action="close-checkout" aria-label="Close checkout modal">×</button>
    </div>

    <div class="checkout-layout">
      <div class="checkout-left">
        <div id="checkoutSummary" class="checkout-summary">
          <div class="checkout-summary-head">
            <strong id="checkoutSummaryPlan">Full System Access</strong>
            <span id="checkoutSummaryPrice" class="checkout-summary-price">₹299</span>
          </div>
          <span id="checkoutSummarySelection">Includes:</span>
          <div id="checkoutSummaryHighlights" class="checkout-summary-highlights">
            <ul id="checkoutSummaryBooksList" class="checkout-summary-books-list">
              <li>Action Figure &amp; Toy Box</li>
              <li>Ghibli &amp; Anime Style</li>
              <li>Childhood Nostalgia</li>
            </ul>
            <div id="checkoutSummaryOverflow" class="checkout-summary-overflow" hidden>+0 more selected books</div>
            <div id="checkoutSummaryBonus" class="checkout-summary-bonus"><span class="checkout-summary-bonus-kicker">🎁 BONUS INCLUDED</span><span class="checkout-summary-bonus-title">AI Cheat Guide + CustomGPT + Telegram Community</span></div>
          </div>
          <div class="checkout-summary-chips" aria-label="System benefits">
            <span class="checkout-summary-chip">⚡ Real-Time Updates</span>
            <span class="checkout-summary-chip">📋 One-Click Copy</span>
            <span class="checkout-summary-chip">📄 PDF Export</span>
            <span class="checkout-summary-chip">♾ Lifetime Access</span>
          </div>
        </div>

        <div class="checkout-proof">
          <div id="checkoutProofTitle" class="checkout-proof-title">✨ Selected Styles Preview ✨</div>
          <div class="checkout-proof-strip" aria-label="Example outputs">
            <figure class="checkout-proof-item" id="checkoutProofItem1">
              <img id="checkoutProofImage1" src="/assets/checkout/ghibli-art-thumb.webp" alt="Ghibli style image example" width="88" height="88" loading="eager" decoding="async">
              <figcaption id="checkoutProofCaption1">Ghibli Art</figcaption>
            </figure>
            <figure class="checkout-proof-item" id="checkoutProofItem2">
              <img id="checkoutProofImage2" src="/assets/checkout/action-figures-thumb.webp" alt="Action figure style image example" width="88" height="88" loading="eager" decoding="async">
              <figcaption id="checkoutProofCaption2">Action Figures</figcaption>
            </figure>
            <figure class="checkout-proof-item" id="checkoutProofItem3">
              <img id="checkoutProofImage3" src="/assets/checkout/professional-headshots-thumb.webp" alt="Professional headshot image example" width="88" height="88" loading="eager" decoding="async">
              <figcaption id="checkoutProofCaption3">Professional Headshots</figcaption>
            </figure>
            <span id="checkoutProofMoreBadge" class="checkout-proof-more-badge" hidden>+0 More Styles</span>
          </div>
        </div>

        <div class="checkout-social-proof">
          <strong>⭐ Join our founding community of early creators</strong>
        </div>
      </div>

      <div class="checkout-right">

        <div class="checkout-field">
          <label for="buyerEmail" class="checkout-label">Email Address</label>
          <input type="email" id="buyerEmail" class="checkout-input" name="buyer_email" autocomplete="email" spellcheck="false" placeholder="Enter your email for instant access">
          <div class="checkout-help">Your login + access details are delivered instantly after payment.</div>
        </div>
        <div id="paymentButtons" class="checkout-payments">
          <button id="razorpayBtn" type="button" class="pay-btn-razorpay" data-action="pay-razorpay">
            <span class="pay-btn-razorpay__icon" aria-hidden="true">🔒</span>
            <span class="pay-btn-razorpay__text">Unlock Instant Access — ₹299</span>
          </button>
          <div id="checkoutCtaMicrocopy" class="checkout-cta-microcopy">Instant access to your full AI prompt system after payment.</div>
          <div id="paymentSecure" class="checkout-security">Secure Razorpay Payment</div>
          <div class="checkout-security checkout-security--methods">UPI • GPay • PhonePe • Cards Accepted</div>
          <div class="checkout-logos" aria-label="Payment methods">
            <span class="checkout-logo-pill"><img class="checkout-logo checkout-logo--upi" src="/assets/icons/payment-logo-upi.webp" alt="UPI" width="86" height="32" loading="eager" decoding="async"></span>
            <span class="checkout-logo-pill"><img class="checkout-logo checkout-logo--gpay" src="/assets/icons/payment-logo-gpay.webp" alt="GPay" width="86" height="32" loading="eager" decoding="async"></span>
            <span class="checkout-logo-pill"><img class="checkout-logo checkout-logo--phonepe" src="/assets/icons/payment-logo-phonepe.webp" alt="PhonePe" width="86" height="32" loading="eager" decoding="async"></span>
            <span class="checkout-logo-pill"><img class="checkout-logo checkout-logo--paytm" src="/assets/icons/payment-logo-paytm.webp" alt="Paytm" width="86" height="32" loading="eager" decoding="async"></span>
            <span class="checkout-logo-pill"><img class="checkout-logo checkout-logo--visa" src="/assets/icons/payment-logo-visa.webp" alt="Visa" width="86" height="32" loading="eager" decoding="async"></span>
          </div>

          <div class="checkout-trust-row" aria-label="Checkout trust assurances">
            <div class="checkout-trust-item"><span class="checkout-trust-icon checkout-trust-icon--bolt"></span><span>Instant Access<br>after payment</span></div>
            <div class="checkout-trust-item"><span class="checkout-trust-icon checkout-trust-icon--shield"></span><span>One-time payment<br>No subscription</span></div>
            <div class="checkout-trust-item"><span class="checkout-trust-icon checkout-trust-icon--mail"></span><span>Login delivered<br>instantly</span></div>
            <div class="checkout-trust-item"><span class="checkout-trust-icon checkout-trust-icon--headset"></span><span>7-Day Money-Back<br>Guarantee</span></div>
          </div>
          <p class="checkout-refund-note">✅ 7-Day Money-Back Guarantee — try it for 7 days. Not the right fit? Email us for a full refund. No questions asked.</p>

        </div>
      </div>
    </div>
    <div class="checkout-terms">
      Secure payment powered by Razorpay · 256-bit SSL Encrypted · 100% Safe &amp; Secure
    </div>
    <div id="checkoutError" class="checkout-error" role="alert" aria-live="polite"></div>
  </div>
</div>

<script src="/assets/js/pixel-tracking.js" defer></script>
<script>
/* Hero video fullscreen toggle */
(function(){
  var btn = document.querySelector('.hero-video-fullscreen');
  if (!btn) return;
  btn.addEventListener('click', function(){
    var video = btn.closest('.hero-video-wrap').querySelector('video');
    if (!video) return;
    if (video.requestFullscreen) video.requestFullscreen();
    else if (video.webkitRequestFullscreen) video.webkitRequestFullscreen();
    else if (video.msRequestFullscreen) video.msRequestFullscreen();
  });
})();

/* Pause video when out of view, play when visible */
(function(){
  var video = document.querySelector('.hero-video');
  if (!video || !('IntersectionObserver' in window)) return;
  var observer = new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
      if (entry.isIntersecting) {
        video.play();
      } else {
        video.pause();
      }
    });
  }, {threshold:0.25});
  observer.observe(video);
})();
</script>
<!-- Microsoft Clarity — deferred to avoid blocking page render -->
<script>
(function(c,l,a,r,i,t,y){
    c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
    t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
    y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
})(window, document, "clarity", "script", "x37q4ovu6j");
</script>
<?php
$checkoutImages = [];
foreach ($books as $id => $book) {
  if (!empty($book['bonus'])) continue;
  $modalCoverFile = sprintf('book-%02d.jpg', $id);
  $modalCoverWebPath = '/assets/covers/' . $modalCoverFile;
  $modalCoverDiskPath = __DIR__ . '/assets/covers/' . $modalCoverFile;
  if (file_exists($modalCoverDiskPath)) {
    $checkoutImages[] = $modalCoverWebPath . '?v=' . filemtime($modalCoverDiskPath);
  }
}
$bonusCoverWebPath = '/assets/covers/book-bonus.jpg';
$bonusCoverDiskPath = __DIR__ . $bonusCoverWebPath;
if (file_exists($bonusCoverDiskPath)) {
  $checkoutImages[] = $bonusCoverWebPath . '?v=' . filemtime($bonusCoverDiskPath);
}
// Add payment icons
$checkoutImages[] = '/assets/icons/payment-logo-upi.webp';
$checkoutImages[] = '/assets/icons/payment-logo-gpay.webp';
$checkoutImages[] = '/assets/icons/payment-logo-phonepe.webp';
$checkoutImages[] = '/assets/icons/payment-logo-paytm.webp';
$checkoutImages[] = '/assets/icons/payment-logo-visa.webp';
// Add trust icons
$checkoutImages[] = '/assets/icons/shield-gold.svg';
$checkoutImages[] = '/assets/icons/bolt-gold.svg';
$checkoutImages[] = '/assets/icons/mail-gold.svg';
$checkoutImages[] = '/assets/icons/headset-gold.svg';
// Add thumbnails
foreach (['action-figures', 'ghibli-art', 'childhood-nostalgia', 'caricature-chibi', 'professional-headshots', 'product-photography', 'cinematic-movie-poster', 'vintage-scrapbook', 'pet-transformation', 'historical-time-travel', 'trending-styles'] as $thumb) {
  $checkoutImages[] = "/assets/checkout/{$thumb}-thumb.webp";
}
?>
<script>
window.__AIPB_CONFIG = {
  paymentProvider: <?= json_encode(defined('PAYMENT_PROVIDER') ? PAYMENT_PROVIDER : 'razorpay') ?>,
  razorpayKeyId: <?= json_encode(defined('RAZORPAY_KEY_ID') ? RAZORPAY_KEY_ID : '') ?>,
  cashfreeEnv: <?= json_encode(defined('CASHFREE_ENV') ? CASHFREE_ENV : 'sandbox') ?>,
  siteName: <?= json_encode(defined('SITE_NAME') ? SITE_NAME : 'AI Prompt System') ?>,
  booksById: <?= json_encode(array_map(fn($b) => $b['title'], $books)) ?>,
  checkoutImages: <?= json_encode($checkoutImages) ?>
};
</script>
<script src="<?= htmlspecialchars($landingJsWebPath . $landingJsVersion, ENT_QUOTES, 'UTF-8') ?>" defer></script>
</body>
</html>
