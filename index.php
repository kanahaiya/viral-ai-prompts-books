<?php
require_once __DIR__ . '/auth.php';
$loggedIn = isLoggedIn();
$books    = getBooks();

/**
 * Stable 50/50 hero assignment for landing experiments.
 * Priority:
 * 1) Explicit URL override (?hero_variant=a|b) for QA/ad-level testing.
 * 2) Existing cookie value for consistency across sessions.
 * 3) New random assignment with cookie persistence.
 */
$allowedHeroVariants = ['a', 'b'];
$requestedHeroVariant = isset($_GET['hero_variant']) ? strtolower((string) $_GET['hero_variant']) : '';
$cookieHeroVariant = isset($_COOKIE['aipb_hero_variant']) ? strtolower((string) $_COOKIE['aipb_hero_variant']) : '';
$heroVariant = 'a';
$heroAssignmentSource = 'random';

if (in_array($requestedHeroVariant, $allowedHeroVariants, true)) {
  $heroVariant = $requestedHeroVariant;
  $heroAssignmentSource = 'query';
} elseif (in_array($cookieHeroVariant, $allowedHeroVariants, true)) {
  $heroVariant = $cookieHeroVariant;
  $heroAssignmentSource = 'cookie';
} else {
  $heroVariant = random_int(0, 1) === 0 ? 'a' : 'b';
  $heroAssignmentSource = 'random';
}

$setHeroVariantCookie = !in_array($cookieHeroVariant, $allowedHeroVariants, true) || $cookieHeroVariant !== $heroVariant;
if ($setHeroVariantCookie) {
  setcookie(
    'aipb_hero_variant',
    $heroVariant,
    [
      'expires' => time() + (60 * 60 * 24 * 30),
      'path' => '/',
      'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
      'httponly' => false,
      'samesite' => 'Lax'
    ]
  );
}
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
<meta property="og:image:alt" content="AI Prompt Books system preview showing all 11 books and bonus resources">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Viral AI Prompts System — 11 Books, 1,100 AI Image Templates">
<meta name="twitter:description" content="Create stunning AI images in minutes with 11 prompt books and 1,100 fill-in-the-blank templates.">
<meta name="twitter:image" content="https://www.aipromptbooks.in/assets/og/og-image.jpg">
<meta name="facebook-domain-verification" content="80mehcxtq5lqugdfns9r4t854kzmeh">
<meta name="theme-color" content="#0a0a0a">
<link rel="dns-prefetch" href="//checkout.razorpay.com">
<link rel="preconnect" href="https://checkout.razorpay.com" crossorigin>
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
<a href="#main" class="skip-link">Skip to main content</a>

<!-- NAV -->
<nav class="nav">
  <div class="nav-inner">
    <div class="nav-logo">AI Prompt Books</div>
    <div class="nav-links">
      <?php if ($loggedIn): ?>
        <a href="/dashboard.php" class="nav-cta">My Books →</a>
      <?php else: ?>
        <a href="/login.php" class="nav-login">Login</a>
        <a href="#pricing" class="nav-cta">Get Instant Access</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main id="main">
<!-- HERO -->
<section class="hero" data-hero-variant="<?= htmlspecialchars($heroVariant, ENT_QUOTES, 'UTF-8') ?>" data-hero-assignment-source="<?= htmlspecialchars($heroAssignmentSource, ENT_QUOTES, 'UTF-8') ?>">
  <div class="hero-bg"></div>
  <div class="hero-container">

    <!-- Row 1: eyebrow + full-width headline -->
    <div class="hero-headline">
      <p class="hero-hook">Interactive AI Prompt System for complete beginners</p>
      <h1>
        <span class="hero-headline-line">Never Guess What To Type In ChatGPT Again.</span>
        <span class="hero-headline-line"><span class="gold">Fill a few details and get live personalized prompts</span> in seconds.</span>
      </h1>
    </div>

    <!-- Row 2: two-column split -->
    <div class="hero-split">

      <!-- Left: copy -->
      <div class="hero-copy">
        <p class="hero-sub">Pick a style, fill a few details, and instantly generate personalized AI prompts in real time, even if you have never used AI before.</p>
        <p class="hero-sub hero-sub--tight">
          No prompt engineering.<br>
          No guessing. No technical skills.<br>
          Copy with one click or save your personalized prompt book PDF.
        </p>
        <div class="hero-ctas">
          <a href="#pricing" class="btn-primary btn-primary--priced" data-cta-role="hero-primary">
            <span class="btn-main-text">Unlock Full AI Prompt System — ₹299</span>
            <span class="btn-price-line"><span class="btn-price-now">₹299</span> <span class="btn-price-orig">₹2,189+</span></span>
          </a>
          <a href="#how-it-works" class="btn-secondary btn-secondary--soft">See Exactly How It Works</a>
        </div>
        <div class="hero-proof-strip">
          <span class="hero-proof-pill">12 Interactive AI Prompt Systems</span>
          <span class="hero-proof-pill">Prompts Update Live As You Type</span>
          <span class="hero-proof-pill">Works with ChatGPT, Midjourney, Firefly, Ideogram</span>
        </div>
        <div class="hero-trust">
          Beginner-safe system used by creators, freelancers, and business owners across India<br>
          <span class="hero-trust-gold">One-time payment &nbsp;·&nbsp; Instant access &nbsp;·&nbsp; No subscription</span>
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

<!-- WHAT YOU GET -->
<section id="what-you-get" class="benefits-section">
  <div class="container container-center">
    <h2 class="section-title">What You Get Inside The Interactive AI Prompt System</h2>
    <p class="section-sub section-sub--compact">You are not buying static PDFs. You get a fill-in-the-blank AI image creation system designed for fast first results.</p>
    <ul class="create-outcomes-list">
      <li><strong>Style-by-style interactive books:</strong> Open any style, fill simple fields, and your prompt updates instantly in real time.</li>
      <li><strong>Done-for-you prompt structure:</strong> Stop guessing words and get personalized prompts automatically while you type.</li>
      <li><strong>One-click execution flow:</strong> Copy prompt instantly or save a personalized PDF book customized to your inputs.</li>
      <li><strong>Beginner-first certainty:</strong> Built to get your first strong AI image quickly without prompt engineering.</li>
    </ul>
    <a href="#pricing" class="btn-primary btn-inline">Unlock Full AI Prompt System — ₹299</a>
  </div>
</section>

<!-- COLLAGE: Visual Proof -->
<section class="gallery-section">
  <div class="container container-center">
    <h2 class="section-title">What You Can Create With This <span class="text-gold">(Even As a Beginner)</span></h2>
    <p class="section-sub section-sub--wide section-sub--compact">You do not need to guess what to type. Pick a style, fill a few blanks, and generate high-quality results in minutes.</p>
    <div class="gallery-collage-wrap">
      <div class="gallery-img">
        <picture>
          <source type="image/avif" srcset="assets/collage-1-360w.avif 360w, assets/collage-1-520w.avif 520w, assets/collage-1.avif 682w" sizes="(max-width: 600px) 92vw, 520px">
          <source type="image/webp" srcset="assets/collage-1-360w.webp 360w, assets/collage-1-520w.webp 520w, assets/collage-1.webp 682w" sizes="(max-width: 600px) 92vw, 520px">
          <img src="assets/collage-1.jpg" srcset="assets/collage-1-360w.jpg 360w, assets/collage-1-520w.jpg 520w, assets/collage-1.jpg 682w" sizes="(max-width: 600px) 92vw, 520px" alt="AI image examples — action figure, Ghibli anime, Mughal warrior, royal dog portrait, movie poster, rooftop portrait" width="699" height="1024" loading="lazy" fetchpriority="low" decoding="async">
        </picture>
      </div>
    </div>
    <ul class="proof-caption-list">
      <li><strong>Action Figure:</strong> selfie + role details → toy-box prompt → collectible-style result</li>
      <li><strong>Ghibli Portrait:</strong> photo + mood → anime prompt → cinematic nostalgic output</li>
      <li><strong>Movie Poster:</strong> concept + title → poster prompt → social-ready visual</li>
    </ul>
    <p class="section-footnote">6 styles shown. Same system for all styles. Works even if you're completely new.</p>
  </div>
</section>

<!-- SEE EXACTLY HOW IT WORKS -->
<section class="benefits-section ease-section" id="how-it-works">
  <div class="container container-center">
    <h2 class="section-title">See Exactly How It Works</h2>
    <p class="section-sub section-sub--compact">No Prompt Engineering. No Guessing. No Technical Skills.</p>
    <div class="ease-steps-grid">
      <article class="ease-step-card">
        <div class="ease-step-label">STEP 1</div>
        <h3 class="ease-step-title">Choose Your Style</h3>
        <img src="assets/hero-mockup-768w.jpg" alt="Members area showing all interactive AI prompt books" width="768" height="699" loading="lazy" decoding="async">
        <p class="ease-step-copy">Choose from Ghibli, Cinematic Posters, Product Photography, Headshots, Nostalgia, Pet Transformations and more.</p>
      </article>
      <article class="ease-step-card">
        <div class="ease-step-label">STEP 2</div>
        <h3 class="ease-step-title">Fill A Few Details</h3>
        <img src="assets/collage-1-520w.jpg" alt="Input form fields for character, outfit, world, setting, mood and companion" width="520" height="761" loading="lazy" decoding="async">
        <p class="ease-step-copy">Just fill simple fields like character, outfit, world, setting, mood and companion. No prompt writing required.</p>
      </article>
      <article class="ease-step-card">
        <div class="ease-step-label">STEP 3</div>
        <h3 class="ease-step-title">Watch Prompts Update LIVE</h3>
        <img src="assets/collage-1-520w.jpg" alt="Screenshot showing prompt pasted into ChatGPT" width="520" height="761" loading="lazy" decoding="async">
        <p class="ease-step-copy">Your prompts update automatically in real time while you type. No guessing, no trial and error, no technical skills.</p>
      </article>
      <article class="ease-step-card">
        <div class="ease-step-label">STEP 4</div>
        <h3 class="ease-step-title">Copy Or Save Your Personalized Book</h3>
        <img src="assets/collage-1-520w.jpg" alt="Screenshot showing final generated AI image output" width="520" height="761" loading="lazy" decoding="async">
        <p class="ease-step-copy">Copy prompts instantly with one click or export a personalized prompt book PDF customized to your details.</p>
      </article>
      <article class="ease-step-card">
        <div class="ease-step-label">STEP 5</div>
        <h3 class="ease-step-title">Generate Stunning Images</h3>
        <img src="assets/collage-1-520w.jpg" alt="AI image outputs generated after using personalized prompts" width="520" height="761" loading="lazy" decoding="async">
        <p class="ease-step-copy">Paste into ChatGPT, Midjourney, Firefly, Ideogram or your favorite image generator and create stunning visuals in minutes.</p>
      </article>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials-section" id="reviews">
  <div class="container">
    <h2 class="section-title section-title--center">Real Buyer Outcomes</h2>
    <p class="testimonial-lead">These buyers started as beginners. They used the same system to get practical results quickly.</p>
    <div class="testimonial-grid">
      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"146 likes in 24 hours from one Ghibli post. I just filled the template and generated."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar testimonial-avatar--rm">RM</div>
          <div>
            <div class="testimonial-name">Riya M.</div>
            <div class="testimonial-sub">Digital creator · Mumbai</div>
            <div class="testimonial-badge">VERIFIED BUYER · SYSTEM</div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"Recovered my ₹299 in first few client orders. It made delivery much faster for me."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar testimonial-avatar--vt">VT</div>
          <div>
            <div class="testimonial-name">Vikram T.</div>
            <div class="testimonial-sub">Graphic designer · Chennai</div>
            <div class="testimonial-badge">VERIFIED BUYER · SYSTEM</div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★☆</div>
        <p class="testimonial-text">"Created gift visuals in 20 minutes with zero prompt skills. My family thought I hired a designer."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar testimonial-avatar--dr">DR</div>
          <div>
            <div class="testimonial-name">Deepa R.</div>
            <div class="testimonial-sub">Teacher · Hyderabad</div>
            <div class="testimonial-badge">VERIFIED BUYER · BOOK 8</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- OBJECTION HANDLING -->
<section class="benefits-section">
  <div class="container container-center">
    <h2 class="section-title">Why This Beats Random Free Prompting</h2>
    <div class="solution-grid">
      <div class="solution-card">
        <div class="solution-card-title">Without a system</div>
        <ul class="solution-card-list">
          <li>Random prompt guessing</li>
          <li>Inconsistent AI results</li>
          <li>Hours wasted in trial-and-error</li>
          <li>Watching random tutorials without a repeatable workflow</li>
          <li>Bad outputs that feel hit-or-miss</li>
        </ul>
      </div>
      <div class="solution-card">
        <div class="solution-card-title">With this interactive system</div>
        <ul class="solution-card-list">
          <li>Personalized prompts generated instantly</li>
          <li>Prompts update live as you type</li>
          <li>One-click copy and personalized PDF export</li>
          <li>Beginner-friendly flow with consistent quality</li>
          <li>Faster path from idea to usable AI image</li>
        </ul>
      </div>
    </div>
    <p class="solution-note">If your goal is faster first results and better output consistency, a guided interactive system beats random prompting every time.</p>
  </div>
</section>

<!-- PERFECT FOR -->
<section class="benefits-section">
  <div class="container container-center">
    <h2 class="section-title">Built For People Who Want Results, Not Prompt Theory</h2>
    <ul class="perfect-for-list">
      <li>Beginners confused by AI tools and blank prompt screens.</li>
      <li>Creators who want better visuals without guessing prompts.</li>
      <li>Freelancers who want client-ready visuals without delays.</li>
      <li>Small business owners improving product and ad creatives.</li>
      <li>Anyone who wants fast, high-quality AI image results.</li>
    </ul>
    <p class="fit-exclusion-line">Not ideal for users looking for free, random trial-and-error prompting with inconsistent output quality.</p>
    <a href="#pricing" class="btn-primary btn-inline">Start Creating Better AI Images</a>
  </div>
</section>

<!-- OFFER STACK -->
<section id="offer-stack" class="offer-section">
  <div class="container container-center">
    <h2 class="section-title">Everything Included In Full AI Prompt System Access</h2>
    <p class="section-sub section-sub--compact">This bundle is built to remove guesswork and help beginners create quality AI visuals faster and more consistently.</p>
    <div class="offer-list offer-list--redesign">
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 1 — Action Figure &amp; Toy Box</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹99</span><span class="val-old">₹199</span></div>
      </div>
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 2 — Ghibli &amp; Anime Style</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹99</span><span class="val-old">₹199</span></div>
      </div>
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 3 — Childhood Nostalgia</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹99</span><span class="val-old">₹199</span></div>
      </div>
      <div class="offer-row">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 4 — Caricature &amp; Chibi</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹99</span><span class="val-old">₹199</span></div>
      </div>
      <div class="offer-row offer-row--optional">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 5 — Professional Headshots</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹99</span><span class="val-old">₹199</span></div>
      </div>
      <div class="offer-row offer-row--optional">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 6 — Product Photography</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹99</span><span class="val-old">₹199</span></div>
      </div>
      <div class="offer-row offer-row--optional">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 7 — Cinematic Movie Poster</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹99</span><span class="val-old">₹199</span></div>
      </div>
      <div class="offer-row offer-row--optional">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 8 — Vintage Scrapbook</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹99</span><span class="val-old">₹199</span></div>
      </div>
      <div class="offer-row offer-row--optional">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 9 — Pet Transformation</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹99</span><span class="val-old">₹199</span></div>
      </div>
      <div class="offer-row offer-row--optional">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 10 — Historical Time Travel</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹99</span><span class="val-old">₹199</span></div>
      </div>
      <div class="offer-row offer-row--optional">
        <div class="offer-row-left"><span class="offer-row-icon">✦</span><div class="offer-row-copy"><span class="offer-row-title">Book 11 — Trending Styles</span><span class="offer-row-meta">100 prompts</span></div></div>
        <div class="offer-row-price"><span class="val-now">₹99</span><span class="val-old">₹199</span></div>
      </div>
      <p class="offer-compressed-note">+ 7 more complete interactive style systems included instantly in the full access bundle.</p>
      <div class="offer-total-row">
        <span class="offer-total-label">Value if bought separately</span>
        <div class="offer-total-price">
          <span class="offer-total-orig" id="offerOrig">₹2,189</span>
          <span class="offer-total-today" id="offerToday">₹299</span>
        </div>
      </div>
    </div>
    <p class="offer-saving-line" id="offerSavingLine">Founder launch price: ₹299 one-time for full system access. Price increases after the next update cycle.</p>
    <a href="#pricing" class="btn-primary btn-inline btn-inline--lg-top">Unlock Full AI Prompt System — ₹299</a>
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

<!-- MOBILE TRUST TEASER -->
<section class="mobile-trust-teaser" aria-label="Mobile buyer proof">
  <div class="container container-center">
    <div class="mobile-trust-card">
      <div class="testimonial-stars">★★★★★</div>
      <p class="mobile-trust-quote">"Recovered my ₹299 in first few client orders. It made delivery much faster for me."</p>
      <p class="mobile-trust-meta">Vikram T. · Graphic designer · Chennai</p>
      <a href="#pricing" class="btn-primary btn-inline">Get Instant Access — ₹299</a>
    </div>
  </div>
</section>

<!-- OFFER DETAILS (PRICING) -->
<section id="pricing" class="pricing-section">
  <div class="container container-center">
    <h2 class="section-title">Choose Your Access Path</h2>
    <p class="section-sub section-sub--spaced">Most buyers choose full access because it removes guessing across every major style and gets faster first wins.</p>
    <div class="access-steps">
      <div class="access-step"><span class="access-step-num">1</span><span>Pay securely</span></div>
      <div class="access-step"><span class="access-step-num">2</span><span>Get instant email access</span></div>
      <div class="access-step"><span class="access-step-num">3</span><span>Start creating in minutes</span></div>
    </div>
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

    <div class="pricing-grid">
      <!-- Single Book -->
      <div class="price-card price-card--starter">
        <div class="price-plan">Starter (1 System)</div>
        <div class="price-amount"><span class="currency">₹</span>99 <span class="original">₹199</span></div>
        <div class="price-billing">One interactive system · 100 prompts · Lifetime access</div>
        <ul class="price-features">
          <li>Pick any 1 interactive style system</li>
          <li>100 fill-in-the-blank prompt templates</li>
          <li class="price-feature-tools">Works with Midjourney, ChatGPT, Firefly, and DALL·E</li>
          <li>Free bonus: AI Image Cheat Code guide</li>
          <li>Best for testing one style before full access</li>
        </ul>
        <button class="btn-buy-outline" data-action="start-checkout" data-plan="single">
          Start with 1 System - ₹99
        </button>
        <div class="price-risk-reversal">Starter option. Most buyers upgrade to full access for speed and consistency across all styles.</div>
      </div>

      <!-- Full System -->
      <div class="price-card popular">
        <div class="price-badge">BEST VALUE</div>
        <div class="price-plan">Complete Interactive System (11 Books)</div>
        <div class="price-amount"><span class="currency">₹</span>299 <span class="original">₹2,189</span></div>
        <div class="price-billing">Save ₹1,890 · All 11 books + bonus resources · Lifetime access</div>
        <div class="price-card-social">Chosen by <strong>200+ creators</strong> — from beginners to freelancers</div>
        <ul class="price-features">
          <li>All 11 interactive style systems + live prompt personalization</li>
          <li>Every trending style covered (Ghibli, posters, pets, fashion…)</li>
          <li>6 personal variable slots per prompt</li>
          <li class="price-feature-tools">Works with Midjourney, ChatGPT, Firefly, and DALL·E</li>
          <li>Interactive online viewer — no downloads</li>
          <li>🎯 Free bonus: The AI Image Cheat Code guide</li>
          <li>🎁 Exclusive Telegram community access</li>
          <li>All future books automatically added</li>
        </ul>
        <button class="btn-buy" data-action="start-checkout" data-plan="bundle">
          Unlock Full AI Prompt System — ₹299
        </button>
        <div class="price-risk-reversal">Best value path for beginners: all styles now, faster results, and future books included.</div>
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

    <p class="pricing-note pricing-note--top" id="pricingAnchor">Founder launch pricing: full system at ₹299. Price increases after upcoming system updates.</p>
    <p class="pricing-note">Secure checkout via Razorpay (India)</p>
    <p class="pricing-note pricing-note--muted">24-hour technical guarantee for failed access/login link issues.</p>
  </div>
</section>

<!-- FAQ -->
<section id="faq">
  <div class="container">
    <h2 class="section-title section-title--center">Buy With Confidence<br><span class="text-gold">Quick Answers Before You Checkout.</span></h2>
    <div class="faq-list">
      <?php
      $faqs = [
        ['Is this beginner-friendly if I am new to AI tools?',
         'Yes. This is made for complete beginners. You pick a style, fill a few blanks, and generate.'],
        ['How fast do I get access after payment?',
         'Immediately after successful payment. Your access details are sent to your email right away.'],
        ['Which AI tools does this work with?',
         'It works with ChatGPT, Midjourney, Firefly, DALL·E, Ideogram, and most AI image generators.'],
        ['What exactly will I receive inside?',
         'You get all 11 AI prompt books, 1,100 prompt templates, bonuses, community access, and future updates.'],
        ['What if my access link does not work?',
         'You get a 24-hour technical guarantee for failed access or login link issues. Support will fix it fast.'],
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
    <h2>Your First High-Quality AI Image Can Be Ready In <span>Minutes</span></h2>
    <div class="final-cta-inner">
      <ul class="final-cta-list">
        <li>No prompt engineering needed</li>
        <li>Prompts update live as you type</li>
        <li>Copy in one click or save personalized PDF books</li>
        <li>Instant full system access for ₹299 one-time</li>
      </ul>
      <div class="final-cta-value">Founder launch access is active now</div>
      <div class="final-cta-price">Unlock Full AI Prompt System — ₹299</div>
      <a href="#pricing" class="btn-primary btn-inline btn-inline--final">Unlock Full AI Prompt System — ₹299</a>
      <div class="final-assurance-row">
        <span>Secure checkout</span>
        <span>Instant access</span>
        <span>No subscription</span>
      </div>
      <div class="final-cta-micro">Founder pricing and current bonus bundle change after the next update cycle.</div>
    </div>
  </div>
</section>
</main>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-logo">AI Prompt Books</div>
  <div class="footer-links">
    <a href="/contact-details.php">Contact</a>
    <a href="/terms-and-conditions.php">Terms</a>
    <a href="/privacy-policy.php">Privacy Policy</a>
    <a href="/refund-and-cancellation-policy.php">Refund Policy</a>
  </div>
  <div class="footer-copy">© <?= date('Y') ?> AI Prompt Books. All rights reserved.</div>
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
      <strong>Full AI Prompt System + Bonuses</strong>
      <span id="stickyPrice">₹299 · Instant access after payment</span>
    </div>
    <button class="sticky-cta-btn" data-action="start-checkout" data-plan="bundle">Get Instant Access — ₹299</button>
  </div>
</div>

<!-- BOOK SELECTOR MODAL -->
<div id="bookModal" class="app-modal app-modal--books">
  <div class="app-modal-card app-modal-card--books">
    <!-- Modal header -->
    <div class="app-modal-head app-modal-head--books">
      <div>
        <div class="app-modal-kicker">Step 1 of 2 · Select Books</div>
        <div class="app-modal-title">Pick One or More Prompt Books</div>
      </div>
      <button type="button" class="modal-close-btn" data-action="close-modal" aria-label="Close book selection modal">×</button>
    </div>
    <p class="app-modal-sub">Tap to select books · ₹99 each · <span class="text-gold">🎁 Cheat Code guide free with every purchase</span></p>

    <div id="modalBookGrid" class="modal-book-grid">
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
        <div class="mbb-accent mbb-accent--bonus"></div>
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
<div id="checkoutModal" class="app-modal app-modal--checkout">
  <div class="app-modal-card app-modal-card--checkout">
    <div class="app-modal-head app-modal-head--checkout">
      <div id="checkoutTitle" class="checkout-title">SECURE CHECKOUT</div>
      <button type="button" class="checkout-close-btn" data-action="close-checkout" aria-label="Close checkout modal">×</button>
    </div>
    <div id="checkoutSummary" class="checkout-summary"></div>

    <div class="checkout-field">
      <label for="buyerName" class="checkout-label">YOUR NAME</label>
      <input type="text" id="buyerName" class="checkout-input" name="buyer_name" autocomplete="name" placeholder="e.g. Raj Sharma…">
    </div>
    <div class="checkout-field">
      <label for="buyerEmail" class="checkout-label">EMAIL ADDRESS</label>
      <input type="email" id="buyerEmail" class="checkout-input" name="buyer_email" autocomplete="email" spellcheck="false" placeholder="you@example.com…">
      <div class="checkout-help">Instant access details are sent to this email immediately after payment.</div>
    </div>
    <div id="paymentButtons" class="checkout-payments">
      <button id="razorpayBtn" type="button" class="pay-btn-razorpay" data-action="pay-razorpay">
        Pay Securely with UPI / Card (Razorpay)
      </button>
      <div id="paymentSecure" class="checkout-security">🔒 Secure checkout · Instant access after payment · No subscription</div>
      <div class="checkout-terms">
        24-hour technical guarantee for failed access/login link issues.
      </div>
    </div>
    <div id="checkoutError" class="checkout-error" role="alert" aria-live="polite"></div>
  </div>
</div>

<script src="/assets/js/pixel-tracking.js" defer></script>
<script>
if (window.location.hash === '#pricing') {
  const cleanPath = window.location.pathname + window.location.search;
  window.history.replaceState(null, '', cleanPath);
  window.scrollTo(0, 0);
}
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
