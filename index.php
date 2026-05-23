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
<link rel="icon" type="image/png" href="/assets/icons/favicon.png">
<title>Viral AI Prompts System — 11 Books, 1,100 AI Image Templates</title>
<meta name="description" content="The Viral AI Prompts System — 11 books, 1,100 fill-in-the-blank templates. Create better AI images faster with Midjourney, ChatGPT, Firefly and more.">
<link rel="canonical" href="<?= htmlspecialchars(rtrim(SITE_URL, '/')) ?>/">
<meta property="og:type" content="website">
<meta property="og:site_name" content="AI Prompt Books">
<meta property="og:title" content="Viral AI Prompts System — 11 Books, 1,100 AI Image Templates">
<meta property="og:description" content="Create stunning AI images in minutes with 11 prompt books and 1,100 fill-in-the-blank templates.">
<meta property="og:url" content="https://www.aipromptbooks.in/">
<meta property="og:image" content="https://www.aipromptbooks.in/assets/og/og-image.jpg">
<meta property="og:image:alt" content="AI Prompt Books system preview showing all 11 books and bonus guide">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Viral AI Prompts System — 11 Books, 1,100 AI Image Templates">
<meta name="twitter:description" content="Create stunning AI images in minutes with 11 prompt books and 1,100 fill-in-the-blank templates.">
<meta name="twitter:image" content="https://www.aipromptbooks.in/assets/og/og-image.jpg">
<link rel="dns-prefetch" href="//checkout.razorpay.com">
<link rel="preconnect" href="https://checkout.razorpay.com" crossorigin>
<?php
$landingCriticalCssWebPath = '/assets/landing-critical.min.css';
$landingCriticalCssDiskPath = __DIR__ . $landingCriticalCssWebPath;
if (!file_exists($landingCriticalCssDiskPath)) {
  $landingCriticalCssWebPath = '/assets/landing-critical.css';
  $landingCriticalCssDiskPath = __DIR__ . $landingCriticalCssWebPath;
}
$landingCriticalCssVersion = file_exists($landingCriticalCssDiskPath) ? ('?v=' . filemtime($landingCriticalCssDiskPath)) : '';

$landingCssWebPath = '/assets/landing.min.css';
$landingCssDiskPath = __DIR__ . $landingCssWebPath;
if (!file_exists($landingCssDiskPath)) {
  $landingCssWebPath = '/assets/landing.css';
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
<link rel="preload" as="style" href="<?= htmlspecialchars($landingCriticalCssWebPath . $landingCriticalCssVersion, ENT_QUOTES, 'UTF-8') ?>">
<link rel="preload" as="style" href="<?= htmlspecialchars($landingCssWebPath . $landingCssVersion, ENT_QUOTES, 'UTF-8') ?>">
<link rel="preload" as="script" href="<?= htmlspecialchars($landingJsWebPath . $landingJsVersion, ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars($landingCriticalCssWebPath . $landingCriticalCssVersion, ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars($landingCssWebPath . $landingCssVersion, ENT_QUOTES, 'UTF-8') ?>" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="<?= htmlspecialchars($landingCssWebPath . $landingCssVersion, ENT_QUOTES, 'UTF-8') ?>"></noscript>
<?php renderMetaPixelHead(); ?>
</head>
<body>
<?php renderMetaPixelNoScript(); ?>

<!-- NAV -->
<nav class="nav">
  <div class="nav-inner">
    <div class="nav-logo">AI Prompt Books</div>
    <div class="nav-links">
      <?php if ($loggedIn): ?>
        <a href="/dashboard.php" class="nav-cta">My Books →</a>
      <?php else: ?>
        <a href="/login.php" class="nav-login">Login</a>
        <a href="#pricing" class="nav-cta">Get Access</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main id="main">
<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-container">

    <!-- Row 1: eyebrow + full-width headline -->
    <div class="hero-headline">
      <h1>
        <span class="hero-headline-line"><span class="gold">Create Stunning</span> AI Images in Minutes,</span>
        <span class="hero-headline-line">Even If You&rsquo;ve Never Used <span class="gold">AI Before</span></span>
      </h1>
    </div>

    <!-- Row 2: two-column split -->
    <div class="hero-split">

      <!-- Left: copy -->
      <div class="hero-copy">
        <p class="hero-sub">Turn selfies, family photos, ideas, and simple concepts into scroll-stopping AI visuals using ready-made fill-in-the-blank templates.</p>
        <p class="hero-sub" style="margin-top:-1rem;">
          No design skills.<br>
          No prompt writing.<br>
          No technical knowledge needed.
        </p>
        <div class="hero-ctas">
          <a href="#pricing" class="btn-primary btn-primary--priced">
            <span class="btn-main-text">Create My AI Art</span>
            <span class="btn-price-line"><span class="btn-price-now">₹299</span> <span class="btn-price-orig">₹2,189</span></span>
          </a>
          <a href="#pricing" class="btn-secondary">See What's Inside</a>
        </div>
        <div class="hero-proof-strip">
          <span class="hero-proof-pill">No prompt-writing required</span>
          <span class="hero-proof-pill">Works with ChatGPT, Midjourney, Firefly</span>
          <span class="hero-proof-pill">First result in your first session</span>
        </div>
        <div class="hero-trust">
          🔒 One-time payment &nbsp;·&nbsp; Instant access &nbsp;·&nbsp; No subscription<br>
          <span class="hero-trust-gold">⭐ Trusted by 200+ paying customers · 4.6★ average rating</span>
        </div>
      </div>

      <!-- Right: product mockup -->
      <div class="hero-visual">
        <a href="#pricing" class="hero-mockup-link" aria-label="Get the Viral AI Prompts System">
          <picture>
            <source type="image/avif" srcset="assets/hero-mockup-480w.avif 480w, assets/hero-mockup-768w.avif 768w, assets/hero-mockup.avif 900w" sizes="(max-width: 960px) 92vw, 50vw">
            <source type="image/webp" srcset="assets/hero-mockup-480w.webp 480w, assets/hero-mockup-768w.webp 768w, assets/hero-mockup.webp 900w" sizes="(max-width: 960px) 92vw, 50vw">
            <img src="assets/hero-mockup.jpg" srcset="assets/hero-mockup-480w.jpg 480w, assets/hero-mockup-768w.jpg 768w, assets/hero-mockup.jpg 900w" sizes="(max-width: 960px) 92vw, 50vw" alt="Viral AI Prompts System — product preview showing 11 books and 1,100 prompt templates" class="hero-mockup-img" width="580" height="529" loading="eager" fetchpriority="high" decoding="async">
          </picture>
        </a>
      </div>

    </div>

    <!-- Stats bar -->
    <div class="hero-stats-wrap">
      <div class="hero-stats">
        <div>
          <div class="hero-stat-num">1,100</div>
          <div class="hero-stat-label">Prompt Templates</div>
        </div>
        <div>
          <div class="hero-stat-num">11</div>
          <div class="hero-stat-label">Style Books</div>
        </div>
        <div>
          <div class="hero-stat-num">60s</div>
          <div class="hero-stat-label">To First Result</div>
        </div>
        <div>
          <div class="hero-stat-num">5+</div>
          <div class="hero-stat-label">AI Tools Supported</div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- PAIN AGITATION -->
<section class="pain-section">
  <div class="container">
    <h2 class="section-title">Everywhere You Look, People Are Posting<br><span style="color:var(--gold);">Incredible AI Art…</span></h2>
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
<section class="solution-section">
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
    <a href="#pricing" class="btn-primary" style="display:inline-block;">Start Creating Now</a>
  </div>
</section>

<!-- BENEFITS -->
<section class="benefits-section">
  <div class="container" style="text-align:center;">
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
    <a href="#pricing" class="btn-primary" style="display:inline-block;">Create Stunning AI Art</a>
  </div>
</section>

<!-- COLLAGE: Visual Proof -->
<section class="gallery-section">
  <div class="container" style="text-align:center;">
    <h2 class="section-title">Real Outputs. Real Prompts. <span style="color:var(--gold);">From This System.</span></h2>
    <p class="section-sub section-sub--wide" style="margin:0 auto 0.5rem;">Action figures, Ghibli art, Mughal warriors, royal pet portraits, cinematic movie posters, and professional portraits — all from the same system, all for the same ₹299.</p>
    <div class="gallery-collage-wrap">
      <div class="gallery-img">
        <picture>
          <source type="image/avif" srcset="assets/collage-1-360w.avif 360w, assets/collage-1-520w.avif 520w, assets/collage-1.avif 682w" sizes="(max-width: 600px) 92vw, 520px">
          <source type="image/webp" srcset="assets/collage-1-360w.webp 360w, assets/collage-1-520w.webp 520w, assets/collage-1.webp 682w" sizes="(max-width: 600px) 92vw, 520px">
          <img src="assets/collage-1.jpg" srcset="assets/collage-1-360w.jpg 360w, assets/collage-1-520w.jpg 520w, assets/collage-1.jpg 682w" sizes="(max-width: 600px) 92vw, 520px" alt="AI image examples — action figure, Ghibli anime, Mughal warrior, royal dog portrait, movie poster, rooftop portrait" width="699" height="1024" loading="lazy" fetchpriority="low" decoding="async">
        </picture>
      </div>
    </div>
    <p style="font-size:0.85rem;color:var(--muted);margin-top:1rem;">6 styles shown. 1,100+ prompt templates across 11 books — many more styles inside.</p>
  </div>
</section>

<!-- SYSTEM INCLUDES -->
<section class="benefits-section">
  <div class="container" style="text-align:center;">
    <h2 class="section-title">What&rsquo;s Included Inside The System</h2>
    <div class="bundle-collection-visual">
      <picture>
        <source type="image/avif" srcset="assets/sections/bundle-collection-640w.avif 640w, assets/sections/bundle-collection-1024w.avif 1024w, assets/sections/bundle-collection.avif 1600w" sizes="(max-width: 900px) 95vw, 980px">
        <source type="image/webp" srcset="assets/sections/bundle-collection-640w.webp 640w, assets/sections/bundle-collection-1024w.webp 1024w, assets/sections/bundle-collection.webp 1600w" sizes="(max-width: 900px) 95vw, 980px">
        <img src="assets/sections/bundle-collection.jpg" srcset="assets/sections/bundle-collection-640w.jpg 640w, assets/sections/bundle-collection-1024w.jpg 1024w, assets/sections/bundle-collection.jpg 1600w" sizes="(max-width: 900px) 95vw, 980px" alt="Complete Viral AI Prompts System collection showing all 11 books and the bonus guide" width="1800" height="900" loading="lazy" fetchpriority="low" decoding="async">
      </picture>
    </div>
    <div class="bundle-group-title">Core Books</div>
    <ul class="bundle-includes-list">
      <li class="bundle-highlight"><strong>11 AI Style Books</strong><span>Each book includes 100 ready-to-use prompts, so creating AI visuals never feels confusing or overwhelming.</span></li>
      <li><strong>All 11 styles shown above</strong><span>Action Figure, Ghibli, Nostalgia, Caricature, Headshots, Product, Cinematic, Scrapbook, Pet, Historical, and Bonus Trending.</span></li>
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
    <a href="#pricing" class="btn-primary" style="display:inline-block;margin-top:0.45rem;">Unlock The System</a>
  </div>
</section>

<!-- PERFECT FOR -->
<section class="benefits-section">
  <div class="container" style="text-align:center;">
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
    <a href="#pricing" class="btn-primary" style="display:inline-block;">Yes, I Want This</a>
  </div>
</section>

<!-- OFFER STACK -->
<section class="offer-section">
  <div class="container" style="text-align:center;">
    <h2 class="section-title">Unlocks the Full Prompt System + Bonuses.</h2>
    <p class="section-sub" style="margin:0 auto;">You&rsquo;re getting a complete, proven system: every book, every bonus, and every future update.</p>
    <div class="offer-list">
      <div class="offer-item"><span class="chk">✦</span><span class="name">🧸 Book 1 — Action Figure &amp; Toy Box (100 prompts)</span><span class="val"><span class="val-now">₹99</span><span class="val-old">₹199</span></span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">🌸 Book 2 — Ghibli &amp; Anime Style (100 prompts)</span><span class="val"><span class="val-now">₹99</span><span class="val-old">₹199</span></span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">📷 Book 3 — Childhood Nostalgia (100 prompts)</span><span class="val"><span class="val-now">₹99</span><span class="val-old">₹199</span></span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">🎨 Book 4 — Caricature &amp; Chibi (100 prompts)</span><span class="val"><span class="val-now">₹99</span><span class="val-old">₹199</span></span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">💼 Book 5 — Professional Headshots (100 prompts)</span><span class="val"><span class="val-now">₹99</span><span class="val-old">₹199</span></span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">📦 Book 6 — Product Photography (100 prompts)</span><span class="val"><span class="val-now">₹99</span><span class="val-old">₹199</span></span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">🎬 Book 7 — Cinematic Movie Poster (100 prompts)</span><span class="val"><span class="val-now">₹99</span><span class="val-old">₹199</span></span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">📜 Book 8 — Vintage Scrapbook (100 prompts)</span><span class="val"><span class="val-now">₹99</span><span class="val-old">₹199</span></span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">🐾 Book 9 — Pet Transformation (100 prompts)</span><span class="val"><span class="val-now">₹99</span><span class="val-old">₹199</span></span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">🕰️ Book 10 — Historical Time Travel (100 prompts)</span><span class="val"><span class="val-now">₹99</span><span class="val-old">₹199</span></span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">✨ Book 11 — Bonus Trending Styles (100 prompts)</span><span class="val"><span class="val-now">₹99</span><span class="val-old">₹199</span></span></div>
      <div class="offer-total-row">
        <span class="offer-total-label">Value if bought separately</span>
        <div class="offer-total-price">
          <span class="offer-total-orig" id="offerOrig">₹2,189</span>
          <span class="offer-total-today" id="offerToday">₹299</span>
        </div>
      </div>
    </div>
    <p style="margin-top:1rem;font-size:0.8rem;color:#555;" id="offerSavingLine">Save ₹1,890 vs buying each book separately — plus 2 free bonuses. Early-access price: ₹299 → ₹499 when this window closes.</p>
    <a href="#pricing" class="btn-primary" style="display:inline-block;margin-top:1.5rem;">Unlock Full System Access</a>
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
  <div class="container" style="text-align:center;">
    <h2 class="section-title">One Price. No Subscription.<br><span style="color:var(--gold);">Use It For Years.</span></h2>
    <p class="section-sub" style="margin:0 auto 1.5rem;">No subscription. No renewal. No price creep. Pay once — own it for life, including every new book added to the collection.</p>
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
        <p>11 books, 1,100 templates, bonus guide, and future updates included.</p>
      </div>
    </div>

    <div class="pricing-grid">
      <!-- Single Book -->
      <div class="price-card">
        <div class="price-plan">Single Book</div>
        <div class="price-amount"><span class="currency">₹</span>99 <span class="original">₹199</span></div>
        <div class="price-billing">One book · 100 prompts · Lifetime access</div>
        <ul class="price-features">
          <li>1 book of your choice (you pick)</li>
          <li>100 fill-in-the-blank prompt templates</li>
          <li>6 personal variable slots per prompt</li>
          <li class="price-feature-tools">Works with Midjourney, ChatGPT, Firefly, and DALL·E</li>
          <li>Interactive online viewer — no downloads</li>
          <li>🎯 Free bonus: The AI Image Cheat Code guide</li>
          <li>Telegram community access</li>
          <li>All future updates to your book</li>
        </ul>
        <button class="btn-buy-outline" data-action="start-checkout" data-plan="single">
          Start with One Book - ₹99 (was ₹199)
        </button>
        <div class="price-risk-reversal">Good if you want to test one style first before committing.</div>
      </div>

      <!-- Full System -->
      <div class="price-card popular">
        <div class="price-badge">BEST VALUE</div>
        <div class="price-plan">Full System Access</div>
        <div class="price-amount"><span class="currency">₹</span>299 <span class="original">₹2,189</span></div>
        <div class="price-billing">Save ₹1,890 · All 11 books + bonus guide · Lifetime access</div>
        <div class="price-card-social">Chosen by <strong>200+ creators</strong> — from beginners to freelancers</div>
        <ul class="price-features">
          <li>All 11 books — 1,100 prompt templates</li>
          <li>Every trending style covered (Ghibli, posters, pets, fashion…)</li>
          <li>6 personal variable slots per prompt</li>
          <li class="price-feature-tools">Works with Midjourney, ChatGPT, Firefly, and DALL·E</li>
          <li>Interactive online viewer — no downloads</li>
          <li>🎯 Free bonus: The AI Image Cheat Code guide</li>
          <li>🎁 Exclusive Telegram community access</li>
          <li>All future books automatically added</li>
        </ul>
        <button class="btn-buy" data-action="start-checkout" data-plan="bundle">
          Get Full System Access — ₹299 →
        </button>
        <div class="price-risk-reversal">Best for serious use: all styles now, plus future books included.</div>
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

    <p style="margin-top:1.5rem;font-size:0.78rem;color:#555;" id="pricingAnchor">💡 Early-access system pricing is currently ₹299. Individual books are listed separately at ₹199 each.</p>
    <p style="margin-top:0.5rem;font-size:0.75rem;color:#444;">🔒 Secure checkout via Razorpay (India)</p>
    <p style="margin-top:0.5rem;font-size:0.75rem;color:#666;">✅ 24-hour technical guarantee: if your access/login link does not work, we'll fix it fast or refund you. No refunds after successful access.</p>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials-section" id="reviews">
  <div class="container">
    <h2 class="section-title" style="text-align:center;">What Buyers Are Saying</h2>

    <p class="testimonial-lead">Most of them were not "AI experts." They were creators, parents, freelancers, and side-hustlers trying to get better output without spending hours experimenting. <strong style="color:#ccc;">These are their practical outcomes.</strong></p>

    <div class="testimonial-grid">

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"Posted my Ghibli portrait on Instagram. Got 146 likes in a day and a few DMs asking which app I used. I tell them I just filled in the template form. Most people don't believe it took under a minute."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background:#7c3aed;">RM</div>
          <div>
            <div class="testimonial-name">Riya M.</div>
            <div class="testimonial-sub">Digital creator · Mumbai</div>
            <div class="testimonial-badge">VERIFIED BUYER · SYSTEM</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"Made my son's action figure toy box in 10 mins. He cried happy tears. My wife shared it and three relatives messaged asking where to buy it. I made it myself. That reaction alone was worth 10x what I paid."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background:#1d4ed8;">PK</div>
          <div>
            <div class="testimonial-name">Prashant K.</div>
            <div class="testimonial-sub">Software engineer · Pune</div>
            <div class="testimonial-badge">VERIFIED BUYER · SYSTEM</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"Using it for client work — headshots, movie posters, pet portraits. My clients think I have a full design team behind me. It's just me and these 11 books. Genuinely worth every rupee."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background:#0f766e;">AS</div>
          <div>
            <div class="testimonial-name">Anika S.</div>
            <div class="testimonial-sub">Freelance designer · Bangalore</div>
            <div class="testimonial-badge">VERIFIED BUYER · SYSTEM</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"Opened an Etsy store after buying Book 7. In the first two weeks, I made around ₹1,150 from AI movie poster prints. A few buyers asked for custom versions, and I could deliver quickly. I recovered the system cost in the first few orders."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background:#b45309;">VT</div>
          <div>
            <div class="testimonial-name">Vikram T.</div>
            <div class="testimonial-sub">Graphic designer · Chennai</div>
            <div class="testimonial-badge">VERIFIED BUYER · SYSTEM</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★☆</div>
        <p class="testimonial-text">"Husband's 40th was in 3 days, no gift idea. Bought the Scrapbook book, spent maybe 20 minutes turning old photos into a vintage album cover. He got emotional. My sister-in-law still asks where I ordered it from. Would love more step-by-step help for total beginners."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background:#be185d;">DR</div>
          <div>
            <div class="testimonial-name">Deepa R.</div>
            <div class="testimonial-sub">Teacher · Hyderabad</div>
            <div class="testimonial-badge">VERIFIED BUYER · BOOK 8</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"I create Reels about AI tools. Used these prompts for a Midjourney demo Reel and gained 43 followers that week — better than my usual growth. Other creators asked which prompts I used. I just said I bought a prompt system. At ₹299 for all 11, it still felt like strong value."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background:#15803d;">SM</div>
          <div>
            <div class="testimonial-name">Suresh M.</div>
            <div class="testimonial-sub">Content creator · Delhi</div>
            <div class="testimonial-badge">VERIFIED BUYER · SYSTEM</div>
          </div>
        </div>
      </div>

    </div>

    <!-- Social proof numbers bar -->
    <div class="social-proof-bar">
      <div class="spb-item">
        <div class="spb-num">200+</div>
        <div class="spb-label">Creators Inside</div>
      </div>
      <div class="spb-item">
        <div class="spb-num">4.6★</div>
        <div class="spb-label">Verified Rating</div>
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
  <div class="container" style="text-align:center;">
    <h2 class="section-title">Early Access Pricing</h2>
    <div class="early-access-copy">
      <p class="early-access-lead">Right now, the complete 11-book system is available for just ₹299.</p>
      <p>Each book individually costs ₹199.</p>
      <p>Once the launch offer ends, the price increases.</p>
      <p class="early-access-vision">If AI art trends are already everywhere on Instagram and Reels, imagine where they&rsquo;ll be 3 months from now.</p>
      <p class="early-access-punch">Best time to start creating is while the trends, styles, and tools are still fresh.</p>
    </div>
    <div class="offer-bonus-callout offer-bonus-callout--secondary">
      <h3>Bonuses Available During This Offer</h3>
      <p>The AI Prompt Finder CustomGPT, bonus ebook, and private Telegram community are currently included free with the full system.</p>
      <p>These bonuses may not stay included permanently as new updates and books are added later.</p>
      <a href="#pricing" class="btn-secondary" style="display:inline-block;">Claim My Bonuses</a>
    </div>
    <div class="momentum-copy">
      <h3>Don&rsquo;t Keep Watching Others Create Cool AI Art</h3>
      <p>Every day, more people are turning ordinary photos into stunning AI visuals, cinematic posters, anime portraits, and viral-style edits.</p>
      <p>Most are not designers.</p>
      <p>Most are not AI experts.</p>
      <p>They simply started.</p>
      <p>A few minutes from now, the system could already be inside the inbox and the first AI image could already be created.</p>
      <a href="#pricing" class="btn-primary" style="display:inline-block;">Make My First AI Image</a>
    </div>
  </div>
</section>

<!-- COLLAGE 2: More Styles -->
<section class="gallery-section gallery-section--alt">
  <div class="container" style="text-align:center;">
    <h2 class="section-title">Still Not Sure? <span style="color:var(--gold);">See What Else Is Possible.</span></h2>
    <p class="section-sub section-sub--wide" style="margin:0 auto 0.5rem;">Pixar-style portraits, luxury product shots, Barbiecore fashion, childhood nostalgia, festive scrapbooks, cyberpunk cities — all using prompts from this single ₹299 system.</p>
    <div class="gallery-collage-wrap">
      <div class="gallery-img">
        <picture>
          <source type="image/avif" srcset="assets/collage-2-360w.avif 360w, assets/collage-2-520w.avif 520w, assets/collage-2.avif 682w" sizes="(max-width: 600px) 92vw, 520px">
          <source type="image/webp" srcset="assets/collage-2-360w.webp 360w, assets/collage-2-520w.webp 520w, assets/collage-2.webp 682w" sizes="(max-width: 600px) 92vw, 520px">
          <img src="assets/collage-2.jpg" srcset="assets/collage-2-360w.jpg 360w, assets/collage-2-520w.jpg 520w, assets/collage-2.jpg 682w" sizes="(max-width: 600px) 92vw, 520px" alt="AI image examples — Pixar boy, product photography, Barbiecore portrait, firefly magic, Diwali scrapbook, cyberpunk Mumbai" width="699" height="1024" loading="lazy" fetchpriority="low" decoding="async">
        </picture>
      </div>
    </div>
    <p style="font-size:0.85rem;color:var(--muted);margin-top:1rem;">Still only 6 of 1,100+ styles. Every prompt works the same way — fill in the blanks, generate, done.</p>
  </div>
</section>

<!-- FAQ -->
<section id="faq">
  <div class="container">
    <h2 class="section-title" style="text-align:center;">Still on the Fence?<br><span style="color:var(--gold);">These Questions Are For You.</span></h2>
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
         'Because this is a digital product with instant access, refunds are not available after access is delivered. However, if there is any technical issue with access, support will help resolve it quickly.'],
      ];
      foreach ($faqs as [$q, $a]):
      ?>
      <div class="faq-item">
        <div class="faq-q"><?= htmlspecialchars($q) ?></div>
        <div class="faq-a"><?= htmlspecialchars($a) ?></div>
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
        <li>11 Premium AI Prompt Books</li>
        <li>1,100 Fill-in-the-Blank Templates</li>
        <li>Bonuses Included</li>
        <li>Lifetime Access</li>
      </ul>
      <div class="final-cta-value">Total Value: ₹2,189+</div>
      <div class="final-cta-price">Today Only: ₹299</div>
      <a href="#pricing" class="btn-primary" style="display:inline-block;font-size:1rem;padding:16px 40px;">
        Get Instant Access
      </a>
      <div class="final-cta-micro">Instant email access after payment.</div>
    </div>
  </div>
</section>
</main>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-logo">AI Prompt Books</div>
  <div class="footer-links">
    <a href="#pricing">Pricing</a>
    <a href="#faq">FAQ</a>
    <a href="/contact-details.php">Contact</a>
    <a href="/terms-and-conditions.php">Terms</a>
    <a href="/privacy-policy.php">Privacy Policy</a>
    <a href="/refund-and-cancellation-policy.php">Refund Policy</a>
    <a href="/login.php">Login</a>
  </div>
  <div class="footer-copy">© <?= date('Y') ?> AI Prompt Books. All rights reserved.</div>
</footer>

<!-- EXIT INTENT POPUP -->
<div class="exit-overlay" id="exitOverlay">
  <div class="exit-popup">
    <button type="button" class="exit-popup-close" data-action="dismiss-exit">×</button>
    <div class="exit-popup-badge">⚡ WAIT — ONE SECOND</div>
    <h3>₹299 closes when this early-access window ends.</h3>
    <p>Drop your email and we'll send you a free sample prompt from Book 2: Ghibli &amp; Anime — and remind you before the price goes to ₹499.</p>
    <div class="exit-popup-form">
      <input type="email" class="exit-popup-input" id="exitEmail" placeholder="your@email.com">
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

<!-- BOOK SELECTOR MODAL -->
<div id="bookModal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(0,0,0,0.88);backdrop-filter:blur(6px);overflow-y:auto;padding:2rem 1rem;">
  <div style="max-width:var(--content-modal-lg);margin:2rem auto;background:#141414;border:1px solid #2a2a2a;border-radius:12px;padding:2rem;box-shadow:0 24px 64px rgba(0,0,0,0.7);">
    <!-- Modal header -->
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.5rem;">
      <div>
        <div style="font-family:'Courier New',monospace;font-size:0.6rem;letter-spacing:2.5px;text-transform:uppercase;color:var(--gold);opacity:0.75;margin-bottom:0.3rem;">Step 1 of 2 · Select Books</div>
        <div style="font-size:1.2rem;font-weight:800;color:#fff;letter-spacing:-0.3px;">Pick One or More Prompt Books</div>
      </div>
      <button type="button" class="modal-close-btn" data-action="close-modal">×</button>
    </div>
    <p style="font-size:0.78rem;color:#555;margin-bottom:1.2rem;padding-bottom:1rem;border-bottom:1px solid #1e1e1e;">Tap to select books · ₹99 each · <span style="color:var(--gold);">🎁 Cheat Code guide free with every purchase</span></p>

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.75rem;" id="modalBookGrid">
      <?php foreach ($books as $id => $book): if (!empty($book['bonus'])) continue; ?>
      <?php
        $modalCoverFile = sprintf('book-%02d.jpg', $id);
        $modalCoverWebPath = '/assets/covers/' . $modalCoverFile;
        $modalCoverDiskPath = __DIR__ . '/assets/covers/' . $modalCoverFile;
        $hasModalCover = file_exists($modalCoverDiskPath);
        $modalCoverVersion = $hasModalCover ? ('?v=' . filemtime($modalCoverDiskPath)) : '';
      ?>
      <button type="button" data-action="toggle-book-selection" data-book-id="<?= $id ?>"
              class="modal-book-btn"
              style="--mbb-color:<?= htmlspecialchars($book['accent']) ?>;">
        <div class="mbb-accent"></div>
        <?php if ($hasModalCover): ?>
          <div class="mbb-cover" style="background-image:url('<?= htmlspecialchars($modalCoverWebPath . $modalCoverVersion, ENT_QUOTES, 'UTF-8') ?>')"></div>
        <?php else: ?>
          <span class="mbb-emoji"><?= $book['emoji'] ?></span>
        <?php endif; ?>
        <div class="mbb-title"><?= htmlspecialchars($book['title']) ?></div>
        <div class="mbb-meta">100 PROMPTS</div>
        <div class="mbb-price" id="modalPrice<?= $id ?>"><span class="mbb-price-now">₹99</span><span class="mbb-price-old">₹199</span></div>
      </button>
      <?php endforeach; ?>
      <!-- Bonus card — included free with any purchase -->
      <?php
        $bonusCoverWebPath = '/assets/covers/book-bonus.jpg';
        $bonusCoverDiskPath = __DIR__ . '/assets/covers/book-bonus.jpg';
        $hasBonusCover = file_exists($bonusCoverDiskPath);
        $bonusCoverVersion = $hasBonusCover ? ('?v=' . filemtime($bonusCoverDiskPath)) : '';
      ?>
      <div class="modal-bonus-card">
        <div class="mbb-accent" style="background:linear-gradient(90deg,#d4a836,#f59e0b);"></div>
        <?php if ($hasBonusCover): ?>
          <div class="mbb-cover" style="background-image:url('<?= htmlspecialchars($bonusCoverWebPath . $bonusCoverVersion, ENT_QUOTES, 'UTF-8') ?>')"></div>
        <?php else: ?>
          <span class="mbb-emoji">🎯</span>
        <?php endif; ?>
        <div class="mbb-title">The AI Image Cheat Code</div>
        <div class="mbb-meta">STYLE GUIDE</div>
        <div class="mbb-free-badge"><span>🎁 FREE BONUS</span></div>
      </div>
    </div>
    <div class="modal-book-footer">
      <div class="modal-selection-meta">
        <div id="selectedBooksCount">0 BOOKS SELECTED</div>
        <div id="selectedBooksAmount"><strong>₹0</strong></div>
      </div>
      <button type="button" class="modal-continue-btn" id="continueSelectedBooksBtn" data-action="continue-selected-books" disabled>
        Continue to Checkout →
      </button>
    </div>
  </div>
</div>

<!-- CHECKOUT MODAL -->
<div id="checkoutModal" style="display:none;position:fixed;inset:0;z-index:300;background:rgba(0,0,0,0.9);backdrop-filter:blur(4px);overflow-y:auto;padding:2rem;">
  <div style="max-width:var(--content-modal-sm);margin:auto;background:#141414;border:1px solid #2a2a2a;border-radius:8px;padding:2.5rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
      <div style="font-family:'Courier New',monospace;font-size:0.75rem;letter-spacing:2px;color:var(--gold);" id="checkoutTitle">CHECKOUT</div>
      <button type="button" class="checkout-close-btn" data-action="close-checkout">×</button>
    </div>
    <div id="checkoutSummary" style="background:#1a1a1a;border:1px solid #2a2a2a;border-radius:4px;padding:1rem;margin-bottom:1.5rem;font-size:0.85rem;color:#aaa;"></div>

    <div style="margin-bottom:1rem;">
      <label style="font-family:'Courier New',monospace;font-size:0.72rem;letter-spacing:1px;color:#888;display:block;margin-bottom:6px;">YOUR NAME</label>
      <input type="text" id="buyerName" placeholder="e.g. Raj Sharma" style="width:100%;background:#0a0a0a;border:1.5px solid #333;color:#fff;padding:10px 14px;border-radius:3px;font-size:0.88rem;outline:none;">
    </div>
    <div style="margin-bottom:1rem;">
      <label style="font-family:'Courier New',monospace;font-size:0.72rem;letter-spacing:1px;color:#888;display:block;margin-bottom:6px;">EMAIL ADDRESS</label>
      <input type="email" id="buyerEmail" placeholder="you@example.com" style="width:100%;background:#0a0a0a;border:1.5px solid #333;color:#fff;padding:10px 14px;border-radius:3px;font-size:0.88rem;outline:none;">
      <div style="font-size:0.75rem;color:#555;margin-top:4px;">Your login credentials will be sent to this email right after payment.</div>
    </div>
    <div id="paymentButtons" style="display:flex;flex-direction:column;gap:0.8rem;">
      <button id="razorpayBtn" type="button" class="pay-btn-razorpay" data-action="pay-razorpay">
        Pay with UPI / Card (Razorpay)
      </button>
      <div style="text-align:center;font-size:0.72rem;color:#555;padding:4px 0;" id="paymentSecure">🔒 Secure · One-time payment · No subscription</div>
      <div style="text-align:center;font-size:0.7rem;color:#666;line-height:1.5;padding:2px 0 0;">
        24-hour technical guarantee for failed access/login link. No refunds after successful access.
      </div>
    </div>
    <div id="checkoutError" style="display:none;background:#3a1010;border:1px solid #7a2020;border-radius:3px;padding:10px 14px;font-size:0.82rem;color:#f87171;margin-top:1rem;"></div>
  </div>
</div>

<script src="/assets/js/pixel-tracking.js" defer></script>
<script>
window.__AIPB_CONFIG = {
  paymentProvider: <?= json_encode(defined('PAYMENT_PROVIDER') ? PAYMENT_PROVIDER : 'razorpay') ?>,
  razorpayKeyId: <?= json_encode(defined('RAZORPAY_KEY_ID') ? RAZORPAY_KEY_ID : '') ?>,
  cashfreeEnv: <?= json_encode(defined('CASHFREE_ENV') ? CASHFREE_ENV : 'sandbox') ?>,
  siteName: <?= json_encode(defined('SITE_NAME') ? SITE_NAME : 'AI Prompt Books') ?>,
  booksById: <?= json_encode(array_map(fn($b) => $b['title'], $books)) ?>
};
</script>
<script src="<?= htmlspecialchars($landingJsWebPath . $landingJsVersion, ENT_QUOTES, 'UTF-8') ?>" defer></script>
</body>
</html>
