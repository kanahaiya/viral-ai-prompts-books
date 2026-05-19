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
<meta property="og:image" content="https://www.aipromptbooks.in/assets/og/og-image.png">
<meta property="og:image:alt" content="AI Prompt Books bundle preview showing all 11 books and bonus guide">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Viral AI Prompts System — 11 Books, 1,100 AI Image Templates">
<meta name="twitter:description" content="Create stunning AI images in minutes with 11 prompt books and 1,100 fill-in-the-blank templates.">
<meta name="twitter:image" content="https://www.aipromptbooks.in/assets/og/og-image.png">
<?php renderMetaPixelHead(); ?>
<style>
/* ── RESET ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;font-size:16px;}
body{font-family:'Segoe UI',Arial,sans-serif;background:#0a0a0a;color:#e8e4de;line-height:1.7;overflow-x:hidden;}
a{color:inherit;text-decoration:none;}
img{max-width:100%;display:block;}

/* ── VARIABLES ── */
:root{
  --gold:#d4a836;
  --gold-dim:#a07820;
  --surface:#141414;
  --surface2:#1a1a1a;
  --border:#2a2a2a;
  --text-dim:#888;
  --radius:6px;
}

/* ── NAV ── */
.nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(10,10,10,0.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);padding:0 2rem;}
.nav-inner{max-width:1280px;margin:0 auto;height:60px;display:flex;align-items:center;justify-content:space-between;}
.nav-logo{font-family:'Courier New',monospace;font-size:0.8rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);}
.nav-links{display:flex;align-items:center;gap:1.2rem;}
.nav-login{font-family:'Courier New',monospace;font-size:0.75rem;letter-spacing:1px;text-transform:uppercase;color:var(--text-dim);border:1px solid var(--border);padding:7px 18px;border-radius:3px;transition:all 0.15s;}
.nav-login:hover{color:#fff;border-color:#555;}
.nav-cta{background:var(--gold);color:#000;font-family:'Courier New',monospace;font-size:0.75rem;letter-spacing:1px;text-transform:uppercase;padding:8px 20px;border-radius:3px;font-weight:700;transition:background 0.15s;}
.nav-cta:hover{background:#e8b93a;}

/* ── HERO ── */
.hero{position:relative;padding:64px 2rem 0;}
.hero-bg{position:absolute;inset:0;background:radial-gradient(ellipse 90% 55% at 50% -10%, rgba(212,168,54,0.13) 0%, transparent 65%);pointer-events:none;}
.hero-container{max-width:1280px;margin:0 auto;position:relative;}

/* Row 1 — full-width headline, left-aligned to match copy column */
.hero-headline{text-align:left;margin-bottom:1.6rem;}
.hero h1{font-size:clamp(2.4rem,4.6vw,5.4rem);font-weight:900;line-height:1.07;letter-spacing:-3px;margin:0;color:#fff;}
.hero h1 .gold{color:var(--gold);}
.hero-headline-line{display:block;white-space:nowrap;}

/* Row 2 — columns stretch to match each other's height */
.hero-split{display:flex;align-items:stretch;gap:4rem;border-top:none;padding-top:0;}

/* Left column — fills from top, same start point as image */
.hero-copy{flex:0 0 46%;min-width:0;display:flex;flex-direction:column;justify-content:space-between;}
.hero-hook{font-size:0.85rem;font-family:'Courier New',monospace;letter-spacing:2px;text-transform:uppercase;color:var(--gold);margin:0 0 1.4rem;opacity:0.85;}
.hero-sub{font-size:clamp(1.1rem,1.5vw,1.25rem);color:#bbb;margin:0 0 2.4rem;line-height:2;}
.hero-ctas{display:flex;gap:0.85rem;flex-wrap:nowrap;margin-bottom:1.6rem;align-items:center;}
.hero-proof-strip{display:flex;flex-wrap:wrap;gap:0.55rem;margin:0 0 1.8rem;}
.hero-trust{font-size:0.78rem;color:#666;line-height:2.3;text-align:left;margin-bottom:1.8rem;}
.hero-trust-gold{color:var(--gold) !important;opacity:0.85;}
.hero-mini-testimonial{margin:0;font-size:0.92rem;color:#888;line-height:1.85;border-left:2px solid rgba(212,168,54,0.45);padding-left:1rem;}

/* Right column — top-aligned, fills remaining width */
.hero-visual{flex:1 1 0;display:flex;align-items:flex-start;justify-content:flex-end;}
.hero-mockup-link{display:block;cursor:pointer;}
.hero-mockup-link:focus{outline:2px solid var(--gold);outline-offset:4px;border-radius:10px;}
.hero-mockup-img{width:100%;height:auto;display:block;filter:drop-shadow(0 32px 80px rgba(212,168,54,0.3));border-radius:10px;transition:filter 0.25s ease,transform 0.25s ease;}
.hero-mockup-link:hover .hero-mockup-img{filter:drop-shadow(0 36px 90px rgba(212,168,54,0.5));transform:scale(1.015);}

/* CTA buttons */
.btn-primary--priced{display:inline-flex;flex-direction:column;align-items:center;gap:3px;padding:15px 32px 12px;white-space:nowrap;}
.btn-main-text{font-size:0.85rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;line-height:1;}
.btn-price-line{display:flex;align-items:baseline;gap:5px;line-height:1;}
.btn-price-now{font-size:1.15rem;font-weight:900;letter-spacing:-0.5px;}
.btn-price-orig{font-size:0.72rem;text-decoration:line-through;opacity:0.5;letter-spacing:0;}
.btn-price-label{font-size:0.7rem;opacity:0.7;letter-spacing:0;text-transform:none;font-weight:400;}
.hero-proof-pill{font-size:0.86rem;color:#bbb;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.1);border-radius:20px;padding:8px 15px;display:flex;align-items:center;gap:5px;}
.hero-proof-pill::before{content:'✓';color:var(--gold);font-size:0.72rem;font-weight:700;}
.hero-mini-testimonial strong{color:#ddd;}

/* Buttons */
.btn-primary{background:var(--gold);color:#000;font-weight:700;font-family:'Courier New',monospace;font-size:0.82rem;letter-spacing:1px;text-transform:uppercase;padding:14px 28px;border-radius:3px;border:none;cursor:pointer;transition:all 0.15s;animation:ctaGlow 3.5s ease-in-out infinite;}
.btn-primary:hover{background:#e8b93a;transform:translateY(-2px);box-shadow:0 6px 24px rgba(212,168,54,0.4);}
@keyframes ctaGlow{0%,100%{box-shadow:0 0 0 0 rgba(212,168,54,0.0),0 2px 8px rgba(0,0,0,0.3);}55%{box-shadow:0 0 0 8px rgba(212,168,54,0.15),0 4px 20px rgba(212,168,54,0.25);}}
.btn-secondary{background:transparent;color:#ccc;font-family:'Courier New',monospace;font-size:0.78rem;letter-spacing:1px;text-transform:uppercase;padding:13px 22px;border-radius:3px;border:1.5px solid rgba(255,255,255,0.2);cursor:pointer;transition:all 0.15s;white-space:nowrap;}
.btn-secondary:hover{border-color:#888;color:#fff;transform:translateY(-1px);}

/* Stats bar */
.hero-stats-wrap{padding:0 0 52px;}
.hero-stats{display:flex;gap:3rem;flex-wrap:wrap;justify-content:space-between;padding:1.2rem 0 0.4rem;margin-top:2rem;border-top:1px solid rgba(212,168,54,0.15);}
.hero-stat-num{font-family:'Courier New',monospace;font-size:1.5rem;font-weight:900;color:var(--gold);line-height:1;}
.hero-stat-label{font-size:0.67rem;color:#555;letter-spacing:1.5px;text-transform:uppercase;margin-top:4px;}
/* Gallery section */
.gallery-section{border-top:1px solid var(--border);padding:60px 2rem 52px;background:linear-gradient(180deg,#0d0d0d 0%,#0a0a0a 100%);}
.gallery-section--alt{background:linear-gradient(180deg,#0a0a0a 0%,#0d0d0d 100%);}
.gallery-collage-wrap{display:flex;justify-content:center;margin-top:2rem;}
.gallery-img{border-radius:14px;overflow:hidden;border:1px solid var(--border);display:block;box-shadow:0 8px 48px rgba(0,0,0,0.6);max-width:520px;width:100%;}
.gallery-img img{display:block;width:100%;height:auto;transition:transform 0.4s ease;}
.gallery-img:hover img{transform:scale(1.015);}
/* Gallery style tag strip */
.gallery-styles{display:flex;flex-wrap:wrap;justify-content:center;gap:0.5rem;margin:1.2rem auto 0;max-width:900px;}
.gallery-style-tag{font-family:'Courier New',monospace;font-size:0.62rem;letter-spacing:1px;text-transform:uppercase;background:rgba(212,168,54,0.06);color:var(--gold);border:1px solid rgba(212,168,54,0.25);padding:4px 12px;border-radius:20px;}
.bundle-includes-list{list-style:none;max-width:900px;margin:1.8rem auto 1.8rem;padding:0;display:grid;gap:0.65rem;text-align:left;}
.bundle-collection-visual{max-width:980px;margin:1.4rem auto 1.2rem;border:1px solid var(--border);border-radius:10px;overflow:hidden;box-shadow:0 10px 36px rgba(0,0,0,0.5);}
.bundle-collection-visual img{width:100%;height:auto;display:block;}
.bundle-includes-list li{font-size:1rem;color:#c2c2c2;line-height:1.65;padding:0.85rem 1rem;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);border-radius:8px;display:grid;gap:0.3rem;}
.bundle-includes-list li strong{color:#fff;font-size:1rem;}
.bundle-group-title{font-family:'Courier New',monospace;font-size:0.68rem;letter-spacing:2px;text-transform:uppercase;color:var(--gold);margin:1.3rem auto 0.65rem;max-width:900px;text-align:left;}
.bundle-pricing-line{display:flex;justify-content:center;gap:1.2rem;align-items:baseline;flex-wrap:wrap;margin:0.6rem 0 1.3rem;}
.bundle-value{font-size:1rem;color:#8f8f8f;}
.bundle-price{font-size:1.35rem;color:var(--gold);font-weight:900;}
.bundle-bridge{font-size:0.95rem;color:#a9a9a9;margin:0.8rem auto 0.4rem;max-width:620px;line-height:1.7;}
.perfect-for-list{list-style:none;max-width:900px;margin:1.8rem auto 1.8rem;padding:0;display:grid;gap:0.65rem;text-align:left;}
.perfect-for-list li{font-size:0.95rem;color:#c2c2c2;line-height:1.68;padding:0.8rem 1rem;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:8px;display:flex;gap:10px;align-items:flex-start;}
.perfect-for-list li::before{content:'✓';color:var(--gold);font-weight:800;flex-shrink:0;margin-top:1px;}
.bundle-detail-label{font-family:'Courier New',monospace;font-size:0.7rem;letter-spacing:2px;text-transform:uppercase;color:var(--gold);margin:0.8rem auto 0.8rem;}
.bundle-detail-list{list-style:none;max-width:860px;margin:0 auto 1.4rem;padding:0;display:grid;gap:0.55rem;text-align:left;}
.bundle-detail-list li{font-size:0.94rem;color:#c0c0c0;line-height:1.55;padding:0.72rem 0.9rem;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);border-radius:7px;}
.bundle-detail-value{font-size:0.97rem;color:#a8a8a8;line-height:1.8;}
.bonus-block{max-width:900px;margin:1.8rem auto 0;text-align:left;}
.bonus-item{background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:1.3rem 1.2rem;margin-bottom:1rem;}
.bonus-label{font-family:'Courier New',monospace;font-size:0.66rem;letter-spacing:1.8px;color:var(--gold);text-transform:uppercase;margin-bottom:0.35rem;}
.bonus-title{font-size:1.06rem;font-weight:800;color:#fff;margin-bottom:0.55rem;}
.bonus-desc{font-size:1rem;color:#d7d7d7;line-height:1.8;}
.bonus-points{list-style:none;margin:0.55rem 0 0;padding:0;display:grid;gap:0.35rem;}
.bonus-points li{font-size:0.98rem;color:#e0e0e0;display:flex;gap:8px;align-items:flex-start;}
.bonus-points li::before{content:'*';color:var(--gold);font-weight:800;flex-shrink:0;}

/* ── TESTIMONIALS ── */
.testimonials-section{border-top:1px solid var(--border);background:var(--surface);}
.testimonial-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.2rem;margin-top:2.5rem;}
.testimonial-card{background:#0f0f0f;border:1px solid var(--border);border-radius:8px;padding:1.6rem;position:relative;transition:border-color 0.2s,transform 0.15s;}
.testimonial-card:hover{border-color:rgba(212,168,54,0.4);transform:translateY(-2px);}
.testimonial-card::before{content:'❝';position:absolute;top:12px;right:16px;font-size:2.5rem;color:rgba(212,168,54,0.12);line-height:1;}
.testimonial-stars{color:#f59e0b;font-size:0.85rem;letter-spacing:2px;margin-bottom:0.8rem;}
.testimonial-text{font-size:0.97rem;color:#ccc;line-height:1.8;margin-bottom:1.2rem;font-style:italic;}
.testimonial-author{display:flex;align-items:center;gap:0.8rem;}
.testimonial-avatar{width:40px;height:40px;border-radius:50%;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:800;font-family:'Courier New',monospace;flex-shrink:0;color:#fff;letter-spacing:0;}
.testimonial-name{font-size:0.88rem;font-weight:700;color:#f0ece6;}
.testimonial-sub{font-size:0.76rem;color:#666;margin-top:2px;}
.testimonial-badge{display:inline-flex;align-items:center;gap:4px;font-family:'Courier New',monospace;font-size:0.55rem;letter-spacing:1px;background:rgba(212,168,54,0.08);color:rgba(212,168,54,0.7);border:1px solid rgba(212,168,54,0.2);padding:2px 8px;border-radius:2px;margin-top:4px;}
.testimonial-badge::before{content:'✓';font-size:0.6rem;color:var(--gold);font-weight:900;}
/* Social proof bar — card treatment */
.social-proof-bar{display:flex;justify-content:center;gap:1rem;flex-wrap:wrap;margin-top:2.5rem;padding-top:2rem;border-top:1px solid var(--border);}
.spb-item{text-align:center;background:var(--surface2);border:1px solid var(--border);border-radius:6px;padding:1rem 1.8rem;min-width:110px;transition:border-color 0.2s;}
.spb-item:hover{border-color:rgba(212,168,54,0.35);}
.spb-num{font-family:'Courier New',monospace;font-size:1.8rem;font-weight:900;color:var(--gold);}
.spb-label{font-size:0.7rem;color:#666;margin-top:4px;letter-spacing:1px;text-transform:uppercase;}

/* ── COUNTDOWN TIMER ── */
.countdown-row{display:flex;justify-content:center;gap:0.8rem;margin:1.2rem 0;}
.cd-block{background:rgba(0,0,0,0.5);border:1px solid #3a1e00;border-radius:4px;padding:8px 14px;min-width:60px;text-align:center;}
.cd-num{font-family:'Courier New',monospace;font-size:1.6rem;font-weight:900;color:#f59e0b;line-height:1;animation:cdPulse 2s ease-in-out infinite;}
@keyframes cdPulse{0%,100%{text-shadow:0 0 6px rgba(245,158,11,0.4);}50%{text-shadow:0 0 18px rgba(245,158,11,0.85),0 0 30px rgba(245,158,11,0.35);}}
.cd-label{font-size:0.58rem;color:#888;letter-spacing:1px;margin-top:3px;text-transform:uppercase;}
.cd-sep{font-family:'Courier New',monospace;font-size:1.4rem;color:#f59e0b;align-self:center;margin-top:-8px;animation:cdPulse 2s ease-in-out infinite;}

/* ── EXIT INTENT POPUP ── */
.exit-overlay{display:none !important;}
.exit-overlay.show{display:none !important;}
.exit-popup{background:#141414;border:1px solid var(--gold);border-radius:8px 8px 0 0;padding:2rem;max-width:480px;width:100%;position:relative;animation:slideUp 0.3s ease;}
@keyframes slideUp{from{transform:translateY(60px);opacity:0;}to{transform:translateY(0);opacity:1;}}
.exit-popup-close{position:absolute;top:12px;right:16px;background:none;border:none;color:#555;font-size:1.4rem;cursor:pointer;line-height:1;}
.exit-popup-close:hover{color:#fff;}
.exit-popup-badge{font-family:'Courier New',monospace;font-size:0.6rem;letter-spacing:2px;color:var(--gold);margin-bottom:0.6rem;}
.exit-popup h3{font-size:1.15rem;font-weight:900;color:#fff;margin-bottom:0.4rem;}
.exit-popup p{font-size:0.82rem;color:#888;margin-bottom:1.2rem;line-height:1.6;}
.exit-popup-form{display:flex;gap:0.5rem;}
.exit-popup-input{flex:1;background:#0a0a0a;border:1.5px solid #333;color:#fff;padding:10px 14px;border-radius:3px;font-size:0.85rem;outline:none;}
.exit-popup-input:focus{border-color:var(--gold);}
.exit-popup-btn{background:var(--gold);color:#000;font-family:'Courier New',monospace;font-size:0.72rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:10px 16px;border:none;border-radius:3px;cursor:pointer;white-space:nowrap;}
.exit-popup-dismiss{display:block;text-align:center;margin-top:0.8rem;font-size:0.72rem;color:#444;cursor:pointer;background:none;border:none;width:100%;}
.exit-popup-dismiss:hover{color:#888;}
.modal-close-btn{background:rgba(255,255,255,0.05);border:1px solid #333;color:#888;font-size:1.1rem;cursor:pointer;width:34px;height:34px;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;line-height:1;transition:color 0.15s;}
.modal-close-btn:hover{color:#fff;}
.checkout-close-btn{background:none;border:none;color:#888;font-size:1.5rem;cursor:pointer;}
.checkout-close-btn:hover{color:#fff;}
.pay-btn-razorpay{display:block;background:#2e86c1;color:#fff;font-family:'Courier New',monospace;font-size:0.8rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:13px;border:none;border-radius:3px;cursor:pointer;transition:background 0.15s;}
.pay-btn-razorpay:hover{background:#236da0;}

/* ── STICKY MOBILE CTA ── */
.sticky-cta{display:none;position:fixed;bottom:0;left:0;right:0;z-index:150;background:#0a0a0a;border-top:1px solid var(--gold);padding:12px 1.2rem;box-shadow:0 -4px 20px rgba(0,0,0,0.6);transform:translateY(100%);transition:transform 0.3s ease;}
.sticky-cta.visible{transform:translateY(0);}
.sticky-cta-inner{display:flex;align-items:center;justify-content:space-between;gap:1rem;max-width:600px;margin:0 auto;}
.sticky-cta-text{font-size:0.78rem;color:#aaa;line-height:1.4;}
.sticky-cta-text strong{color:#fff;display:block;font-size:0.88rem;}
.sticky-cta-btn{background:var(--gold);color:#000;font-family:'Courier New',monospace;font-size:0.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:10px 18px;border-radius:3px;border:none;cursor:pointer;white-space:nowrap;flex-shrink:0;}
@media(max-width:900px){.sticky-cta{display:block;}}

/* ── SECTION COMMON ── */
section{padding:64px 2rem;}
.container{max-width:1280px;margin:0 auto;}
.section-badge{font-family:'Courier New',monospace;font-size:0.65rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:0.8rem;}
.section-title{font-size:clamp(1.8rem,4vw,2.8rem);font-weight:900;letter-spacing:-1px;color:#fff;margin-bottom:1rem;}
.section-sub{font-size:1.05rem;color:#999;max-width:560px;line-height:1.8;}

/* ── BOOKS GRID ── */
.books-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.2rem;margin-top:3rem;}
@media(max-width:900px){.books-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:600px){.books-grid{grid-template-columns:repeat(2,1fr);}}
.book-card{background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem 1.2rem;transition:transform 0.18s,border-color 0.18s,box-shadow 0.18s;cursor:default;position:relative;overflow:hidden;}
.book-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--card-accent,var(--gold));}
.book-card:hover{transform:translateY(-4px);border-color:var(--card-accent,var(--gold));box-shadow:0 0 0 1px var(--card-accent,var(--gold)),0 12px 28px rgba(0,0,0,0.5);}
.book-card .book-num{font-family:'Courier New',monospace;font-size:0.65rem;letter-spacing:2px;color:var(--text-dim);margin-bottom:0.6rem;}
.book-card .book-emoji{font-size:1.8rem;margin-bottom:0.8rem;}
.book-card .book-title{font-size:0.92rem;font-weight:700;color:#fff;line-height:1.3;margin-bottom:0.5rem;}
.book-card .book-prompts{font-family:'Courier New',monospace;font-size:0.65rem;color:var(--text-dim);letter-spacing:1px;}
.book-card .book-price-tag{position:absolute;top:14px;right:14px;font-family:'Courier New',monospace;font-size:0.65rem;font-weight:700;color:#000;background:var(--card-accent,var(--gold));padding:2px 8px;border-radius:2px;}

/* ── HOW IT WORKS ── */
#how{border-top:1px solid var(--border);padding-top:44px;}
.how-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;margin-top:3rem;position:relative;}
.how-grid::before{content:'';position:absolute;top:23px;left:calc(16.66% + 24px);right:calc(16.66% + 24px);height:1.5px;background:linear-gradient(90deg,var(--gold),rgba(212,168,54,0.3),var(--gold));z-index:0;}
.how-step{text-align:center;position:relative;z-index:1;}
.how-step-num{width:48px;height:48px;background:var(--surface);border:2px solid var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Courier New',monospace;font-size:0.85rem;font-weight:700;color:var(--gold);margin:0 auto 1rem;box-shadow:0 0 0 4px var(--surface);}
.how-step h3{font-size:1rem;font-weight:700;color:#fff;margin-bottom:0.5rem;}
.how-step p{font-size:0.85rem;color:#888;line-height:1.6;}
@media(max-width:640px){.how-grid{grid-template-columns:1fr;gap:1.5rem;}.how-grid::before{display:none;}}

/* ── PRICING ── */
.pricing-section{background:var(--surface);}
.pricing-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;max-width:780px;margin:3rem auto 0;}
@media(max-width:600px){.pricing-grid{grid-template-columns:1fr;}}
.price-card{background:var(--surface2);border:1.5px solid var(--border);border-radius:var(--radius);padding:2.5rem 2rem;position:relative;transition:border-color 0.2s;}
.price-card.popular{border-color:var(--gold);box-shadow:0 0 0 1px var(--gold),0 8px 40px rgba(212,168,54,0.18);}
.price-badge{position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:var(--gold);color:#000;font-family:'Courier New',monospace;font-size:0.65rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:4px 16px;border-radius:2px;white-space:nowrap;}
.price-plan{font-family:'Courier New',monospace;font-size:0.7rem;letter-spacing:3px;text-transform:uppercase;color:var(--text-dim);margin-bottom:0.8rem;}
.price-amount{font-size:3rem;font-weight:900;color:#fff;line-height:1;margin-bottom:0.3rem;}
.price-amount .currency{font-size:1.5rem;vertical-align:top;margin-top:0.5rem;display:inline-block;color:var(--gold);}
.price-amount .original{font-size:1.2rem;color:var(--text-dim);text-decoration:line-through;margin-left:0.5rem;}
.price-billing{font-size:0.85rem;color:#aaa;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--border);}
.price-features{list-style:none;margin-bottom:2rem;text-align:left;}
.price-features li{font-size:0.9rem;color:#bbb;padding:6px 0;display:flex;gap:8px;align-items:flex-start;text-align:left;}
.price-features li::before{content:'✓';color:var(--gold);font-weight:700;flex-shrink:0;margin-top:2px;}
.price-feature-tools{line-height:1.6;}
.btn-buy{width:100%;background:var(--gold);color:#000;font-family:'Courier New',monospace;font-size:0.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:13px;border:none;border-radius:3px;cursor:pointer;transition:background 0.15s;}
.btn-buy:hover{background:#e8b93a;}
.btn-buy-outline{width:100%;background:transparent;color:var(--gold);font-family:'Courier New',monospace;font-size:0.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:12px;border:1.5px solid var(--gold);border-radius:3px;cursor:pointer;transition:all 0.15s;}
.btn-buy-outline:hover{background:rgba(212,168,54,0.08);}
.support-access{max-width:780px;margin:1.6rem auto 0;padding:1rem 1.1rem;background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:8px;text-align:left;}
.support-access-title{font-family:'Courier New',monospace;font-size:0.72rem;letter-spacing:2px;text-transform:uppercase;color:var(--gold);margin-bottom:0.75rem;}
.support-access-list{list-style:none;margin:0;padding:0;display:grid;gap:0.42rem;}
.support-access-list li{font-size:0.9rem;color:#c4c4c4;line-height:1.55;display:flex;gap:8px;align-items:flex-start;}
.support-access-list li::before{content:'✓';color:var(--gold);font-weight:800;flex-shrink:0;}
.early-access-copy{max-width:820px;margin:1.2rem auto 0;text-align:center;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:1.25rem 1.35rem;}
.early-access-copy p{font-size:1rem;color:#c9c9c9;line-height:1.8;margin-bottom:0.8rem;}
.early-access-copy p:last-child{margin-bottom:0;}
.early-access-copy .early-access-lead{font-size:1.06rem;color:#f1f1f1;font-weight:700;}
.early-access-copy .early-access-vision{color:#d6d6d6;}
.early-access-copy .early-access-punch{color:#e8e4de;font-weight:700;}
.offer-bonus-callout{max-width:820px;margin:1rem auto 0;text-align:center;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:1.1rem 1.25rem;}
.offer-bonus-callout h3{font-size:1.25rem;color:#fff;font-weight:800;margin-bottom:0.55rem;letter-spacing:-0.2px;}
.offer-bonus-callout p{font-size:0.97rem;color:#c8c8c8;line-height:1.75;margin-bottom:0.55rem;}
.offer-bonus-callout p:last-of-type{margin-bottom:0.95rem;}
.offer-bonus-callout--secondary{background:rgba(255,255,255,0.015);border-color:rgba(255,255,255,0.06);}
.offer-bonus-callout p,.momentum-copy p{max-width:680px;margin-left:auto;margin-right:auto;}
.momentum-copy{max-width:820px;margin:1.4rem auto 0;text-align:center;background:rgba(255,255,255,0.015);border:1px solid rgba(255,255,255,0.06);border-radius:10px;padding:1.25rem 1.35rem;}
.momentum-copy h3{font-size:1.4rem;color:#fff;font-weight:900;letter-spacing:-0.3px;line-height:1.3;margin-bottom:0.85rem;}
.momentum-copy p{font-size:0.98rem;color:#c8c8c8;line-height:1.8;margin-bottom:0.6rem;}
.momentum-copy p:last-of-type{margin-bottom:1rem;}

/* ── COMMUNITY ── */
.community-section{background:linear-gradient(135deg,#0e0c08 0%,#0a0a0a 100%);border-top:1px solid var(--border);border-bottom:1px solid var(--border);}
.community-inner{display:flex;gap:4rem;align-items:center;flex-wrap:wrap;}
.community-text{flex:1;min-width:280px;}
.community-visual{flex:0 0 300px;background:linear-gradient(145deg,#191408,#111);border:1px solid rgba(212,168,54,0.2);border-radius:12px;padding:2rem;text-align:center;box-shadow:0 0 0 1px rgba(212,168,54,0.06),0 8px 32px rgba(0,0,0,0.4);}
.wa-icon{font-size:3.5rem;margin-bottom:1rem;}
.wa-count{font-family:'Courier New',monospace;font-size:0.7rem;letter-spacing:2px;color:var(--gold);opacity:0.75;margin-bottom:0.5rem;}
.wa-title{font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:0.5rem;}
.wa-sub{font-size:0.82rem;color:#888;margin-bottom:1.5rem;}
.btn-wa{background:var(--gold);color:#000;font-family:'Courier New',monospace;font-size:0.78rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:12px 24px;border:none;border-radius:3px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:background 0.15s,box-shadow 0.15s;}
.btn-wa:hover{background:#e8b93a;box-shadow:0 4px 16px rgba(212,168,54,0.35);}
.community-perks{list-style:none;margin-top:1.5rem;}
.community-perks li{font-size:0.88rem;color:#aaa;padding:8px 0;display:flex;gap:10px;align-items:flex-start;border-bottom:1px solid var(--border);transition:color 0.15s;}
.community-perks li:hover{color:#ccc;}
.community-perks li:last-child{border-bottom:none;}
.community-perks li .perk-icon{font-size:1rem;flex-shrink:0;}

/* ── FAQ ── */
.faq-list{max-width:720px;margin:3rem auto 0;}
.faq-item{border-bottom:1px solid var(--border);padding:0;border-radius:4px;margin-bottom:2px;transition:background 0.15s;}
.faq-item:hover{background:rgba(255,255,255,0.025);}
.faq-q{font-size:0.95rem;font-weight:700;color:#fff;cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:1rem;padding:1.2rem 0.8rem;}
.faq-q::after{content:'+';font-size:1.4rem;color:var(--gold);flex-shrink:0;transition:transform 0.2s;line-height:1;}
.faq-item.open .faq-q::after{transform:rotate(45deg);}
.faq-item.open .faq-q{color:var(--gold);}
.faq-a{font-size:0.85rem;color:#888;line-height:1.8;max-height:0;overflow:hidden;transition:max-height 0.35s ease,padding-top 0.2s;padding:0 0.8rem;}
.faq-item.open .faq-a{max-height:220px;padding:0 0.8rem 1.2rem;}

/* ── FOOTER ── */
.footer{background:var(--surface);border-top:1px solid var(--border);padding:3rem 2rem;text-align:center;}
.footer-logo{font-family:'Courier New',monospace;font-size:0.75rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:1rem;}
.footer-links{display:flex;gap:1.5rem;justify-content:center;flex-wrap:wrap;font-size:0.78rem;color:var(--text-dim);margin-bottom:1rem;}
.footer-links a:hover{color:#fff;}
.footer-copy{font-size:0.72rem;color:#555;}

/* ── PAIN SECTION ── */
.pain-section{background:linear-gradient(180deg,#131313 0%,#0e0e0e 100%);padding-bottom:56px;text-align:center;border-top:1px solid var(--border);border-bottom:1px solid var(--border);}
.pain-text{max-width:760px;margin:0 auto;text-align:left;padding:0.2rem 0;}
.pain-text .story{font-size:1.01rem;color:#b5b5b5;line-height:1.85;margin-bottom:0.85rem;}
.pain-text .story strong{color:#fff;}
.pain-style-line{font-size:1.08rem;color:#dedede;font-weight:600;line-height:1.75;}
.pain-transition-line{font-size:1.08rem;color:#f0ece6;font-weight:700;margin-top:0.4rem;}
.pain-bullets{list-style:none;margin:1.35rem 0 1.45rem;display:grid;gap:0.55rem;}
.pain-bullets li{display:flex;gap:10px;align-items:flex-start;padding:10px 12px;font-size:0.94rem;color:#c0c0c0;background:rgba(0,0,0,0.18);border:1px solid rgba(255,255,255,0.06);border-radius:7px;transition:border-color 0.2s,transform 0.15s;}
.pain-bullets li:hover{border-color:rgba(212,168,54,0.35);transform:translateX(2px);}
.pain-bullets li .x{color:#f87171;font-weight:900;flex-shrink:0;font-size:1.02rem;line-height:1.3;}
.pain-punch{font-size:1.03rem;font-weight:800;color:#fff;margin-top:1.25rem;padding:1.15rem 1.3rem;background:#0a0a0a;border-left:3px solid var(--gold);border-radius:0 6px 6px 0;font-style:italic;letter-spacing:0.2px;box-shadow:0 0 0 1px rgba(212,168,54,0.14);}

/* ── SOLUTION BRIDGE ── */
.solution-section{text-align:center;border-top:1px solid var(--border);padding-top:48px;}
.solution-section .big-intro{font-size:clamp(1.5rem,3.5vw,2.2rem);font-weight:900;color:#fff;max-width:760px;margin:1.5rem auto 1rem;line-height:1.25;letter-spacing:-0.5px;}
.solution-section .big-intro span{color:var(--gold);}
.solution-desc{font-size:0.98rem;color:#888;max-width:580px;margin:0 auto 2rem;line-height:1.8;}
.solution-no-list{list-style:none;display:grid;gap:0.45rem;max-width:520px;margin:0.4rem auto 1.4rem;padding:0;}
.solution-no-list li{font-size:0.98rem;color:#bcbcbc;}
.solution-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;max-width:760px;margin:0.6rem auto 1.2rem;text-align:left;}
.solution-card{background:var(--surface2);border:1px solid var(--border);border-left:3px solid var(--gold);border-radius:8px;padding:1.1rem 1rem 1rem;transition:border-color 0.2s,transform 0.15s;}
.solution-card:hover{border-color:rgba(212,168,54,0.35);transform:translateY(-1px);}
.solution-card-title{font-size:0.72rem;color:var(--gold);font-family:'Courier New',monospace;letter-spacing:2px;text-transform:uppercase;margin-bottom:0.65rem;}
.solution-card-list{list-style:none;margin:0;padding:0;display:grid;gap:0.52rem;}
.solution-card-list li{font-size:0.92rem;color:#bebebe;line-height:1.55;}
.solution-step{display:flex;align-items:center;gap:8px;}
.solution-step-badge{width:20px;height:20px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:rgba(212,168,54,0.14);border:1px solid rgba(212,168,54,0.45);color:var(--gold);font-family:'Courier New',monospace;font-size:0.68rem;font-weight:700;flex-shrink:0;}
.solution-note{font-size:0.96rem;color:#aaa;line-height:1.85;max-width:640px;margin:0.7rem auto 1.5rem;}
@media(max-width:760px){.solution-grid{grid-template-columns:1fr;}}

/* ── BENEFITS ── */
.benefits-section{border-top:1px solid var(--border);background:linear-gradient(180deg,#0a0a0a 0%,#0d0d0d 100%);padding-bottom:44px;}
.benefits-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.2rem;margin-top:2.5rem;}
@media(max-width:780px){.benefits-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:480px){.benefits-grid{grid-template-columns:1fr;}}
.benefit-item{background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;text-align:left;border-left:3px solid var(--gold);transition:border-color 0.2s,transform 0.15s;}
.benefit-item:hover{transform:translateY(-2px);}
.benefit-icon{font-size:2rem;margin-bottom:0.8rem;display:block;}
.benefit-title{font-size:0.95rem;font-weight:700;color:#fff;margin-bottom:0.4rem;}
.benefit-desc{font-size:0.82rem;color:#aaa;line-height:1.6;}
.create-outcomes-list{list-style:none;max-width:860px;margin:1.8rem auto 1.8rem;padding:0;display:grid;gap:0.65rem;text-align:left;}
.create-outcomes-list li{font-size:0.95rem;color:#c0c0c0;line-height:1.7;padding:0.75rem 0.9rem;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:7px;display:flex;gap:10px;align-items:flex-start;}
.create-outcomes-list li::before{content:'✓';color:var(--gold);font-weight:800;flex-shrink:0;margin-top:1px;}

/* ── OFFER STACK ── */
.offer-section{background:var(--surface2);border-top:1px solid var(--border);border-bottom:1px solid var(--border);}
.offer-list{max-width:760px;margin:2rem auto 0;}
.offer-item{display:flex;align-items:center;gap:1rem;padding:14px 0;border-bottom:1px solid var(--border);font-size:1rem;line-height:1.7;color:#e1e1e1;}
.offer-item:last-child{border-bottom:none;}
.offer-item .chk{color:var(--gold);font-weight:700;flex-shrink:0;}
.offer-item .name{flex:1;}
.offer-item .val{font-family:'Courier New',monospace;display:inline-flex;align-items:baseline;gap:0.28rem;flex-shrink:0;}
.offer-item .val-now{font-size:0.9rem;font-weight:800;color:var(--gold);}
.offer-item .val-old{font-size:0.84rem;font-weight:700;color:rgba(232,228,222,0.52);text-decoration:line-through;text-decoration-color:rgba(232,228,222,0.42);}
.offer-bonus{display:flex;align-items:center;gap:1rem;padding:14px 0;border-bottom:1px solid var(--border);font-size:1rem;line-height:1.7;color:var(--gold);}
.offer-bonus .chk{font-weight:700;flex-shrink:0;}
.offer-bonus .name{flex:1;}
.offer-bonus .val{font-family:'Courier New',monospace;font-size:0.72rem;color:var(--gold);flex-shrink:0;}
.offer-total-row{display:flex;justify-content:space-between;align-items:center;padding:1.2rem 0 0;margin-top:0.5rem;border-top:2px solid var(--gold);}
.offer-total-label{font-family:'Courier New',monospace;font-size:0.7rem;letter-spacing:2px;text-transform:uppercase;color:#9a9a9a;}
.offer-total-price{display:flex;align-items:baseline;gap:0.8rem;}
.offer-total-orig{font-size:1.05rem;color:#818181;text-decoration:line-through;}
.offer-total-today{font-size:2.05rem;font-weight:900;color:var(--gold);font-family:'Courier New',monospace;}

/* ── GUARANTEE ── */
.guarantee-section{background:var(--surface);border-top:1px solid var(--border);}
.guarantee-inner{display:flex;gap:2rem;align-items:flex-start;flex-wrap:wrap;max-width:820px;margin:2rem auto 0;background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:2rem;}
.guarantee-badge{font-size:1.5rem;font-weight:900;color:var(--gold);flex-shrink:0;text-align:center;line-height:1;padding-top:0.2rem;}
.guarantee-body h3{font-size:1.2rem;font-weight:900;color:#fff;margin-bottom:0.8rem;}
.guarantee-body p{font-size:0.88rem;color:#888;line-height:1.8;margin-bottom:0.8rem;}
.guarantee-points{list-style:none;}
.guarantee-points li{font-size:0.85rem;color:#aaa;padding:5px 0;display:flex;gap:8px;}
.guarantee-points li::before{content:'✓';color:var(--gold);font-weight:700;flex-shrink:0;}

/* ── URGENCY ── */
.urgency-section{background:linear-gradient(135deg,#1c0e00 0%,#0c0c0c 100%);border-top:1px solid rgba(212,168,54,0.22);border-bottom:1px solid rgba(212,168,54,0.22);padding:48px 2rem;text-align:center;box-shadow:inset 0 1px 0 rgba(212,168,54,0.06),inset 0 -1px 0 rgba(212,168,54,0.06);}
.urgency-inner{max-width:620px;margin:0 auto;}
.urgency-icon{font-size:2.4rem;margin-bottom:0.8rem;}
.urgency-live-badge{display:inline-flex;align-items:center;gap:6px;font-family:'Courier New',monospace;font-size:0.6rem;letter-spacing:2px;text-transform:uppercase;color:#f87171;margin-bottom:0.8rem;}
.urgency-live-dot{width:7px;height:7px;border-radius:50%;background:#f87171;animation:livePulse 1.2s ease-in-out infinite;}
@keyframes livePulse{0%,100%{opacity:1;box-shadow:0 0 0 0 rgba(248,113,113,0.6);}50%{opacity:0.7;box-shadow:0 0 0 5px rgba(248,113,113,0);}}
.urgency-title{font-size:1.65rem;font-weight:900;color:#fff;margin-bottom:0.6rem;letter-spacing:-0.5px;line-height:1.2;}
.urgency-sub{font-size:0.95rem;color:#ccc;line-height:1.85;}
.urgency-sub strong{color:#f59e0b;}

/* ── FINAL CTA ── */
.final-cta-section{border-top:1px solid var(--border);text-align:center;background:radial-gradient(ellipse 80% 100% at 50% 100%, rgba(212,168,54,0.07) 0%, transparent 70%);}
.final-cta-section h2{font-size:clamp(1.8rem,4vw,3rem);font-weight:900;letter-spacing:-1px;color:#fff;margin:0 auto 1rem;max-width:980px;line-height:1.22;}
.final-cta-section h2 span{color:var(--gold);}
.final-cta-section .sub{font-size:1rem;color:#888;max-width:480px;margin:0 auto 2.5rem;line-height:1.8;}
.final-cta-inner{max-width:760px;margin:0 auto;background:rgba(255,255,255,0.015);border:1px solid rgba(255,255,255,0.07);border-radius:10px;padding:1.2rem 1.2rem 1.35rem;}
.final-cta-list{list-style:none;max-width:390px;margin:1rem auto 1.05rem;padding:0;display:grid;gap:0.52rem;text-align:center;}
.final-cta-list li{font-size:0.98rem;color:#cecece;line-height:1.6;display:flex;gap:8px;align-items:center;justify-content:center;}
.final-cta-list li::before{content:'✓';color:var(--gold);font-weight:800;flex-shrink:0;margin-top:1px;}
.final-cta-value{font-size:1rem;color:#8f8f8f;margin-top:0.25rem;}
.final-cta-price{font-size:1.55rem;font-weight:900;color:var(--gold);margin:0.2rem 0 1rem;font-family:'Courier New',monospace;}
.final-cta-micro{margin-top:0.75rem;font-size:0.8rem;color:#777;line-height:1.7;}
.final-cta-pills{display:flex;flex-wrap:wrap;justify-content:center;gap:0.5rem;margin-top:1.2rem;}
.final-cta-pill{font-size:0.72rem;color:#aaa;background:rgba(255,255,255,0.04);border:1px solid var(--border);border-radius:20px;padding:5px 12px;display:inline-flex;align-items:center;gap:5px;}
.final-cta-meta{margin-top:1rem;font-size:0.75rem;color:#555;line-height:2;}

/* ── MARKET PSYCHOLOGY ── */
.psychology-section{border-top:1px solid var(--border);background:var(--surface2);}
.psychology-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:2rem;}
@media(max-width:860px){.psychology-grid{grid-template-columns:1fr;}}
.psychology-card{background:#121212;border:1px solid var(--border);border-radius:8px;padding:1.2rem 1.1rem;}
.psychology-card h3{font-size:0.95rem;color:#fff;margin-bottom:0.5rem;}
.psychology-card p{font-size:0.82rem;color:#9a9a9a;line-height:1.7;}
.trigger-strip{display:flex;flex-wrap:wrap;gap:0.5rem;margin-top:1.2rem;}
.trigger-pill{font-family:'Courier New',monospace;font-size:0.62rem;letter-spacing:1px;color:var(--gold);border:1px solid rgba(212,168,54,0.3);background:rgba(212,168,54,0.07);border-radius:20px;padding:5px 11px;}

/* ── FIT SECTION ── */
.fit-section{border-top:1px solid var(--border);background:#0d0d0d;}
.fit-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:2rem;}
@media(max-width:860px){.fit-grid{grid-template-columns:1fr;}}
.fit-card{background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:1.3rem;}
.fit-card h3{font-size:1rem;color:#fff;margin-bottom:0.8rem;}
.fit-card ul{list-style:none;}
.fit-card li{font-size:0.84rem;color:#aaa;padding:7px 0;display:flex;gap:8px;}
.fit-card li::before{font-weight:700;flex-shrink:0;}
.fit-card.fit-yes li::before{content:'✓';color:var(--gold);}
.fit-card.fit-no li::before{content:'–';color:#f87171;}

/* ── PRICING JUSTIFICATION ── */
.value-compare{display:grid;grid-template-columns:repeat(3,1fr);gap:0.9rem;max-width:900px;margin:2rem auto 0;}
@media(max-width:860px){.value-compare{grid-template-columns:1fr;}}
.value-card{background:#101010;border:1px solid var(--border);border-radius:8px;padding:1rem;}
.value-card .label{font-family:'Courier New',monospace;font-size:0.62rem;letter-spacing:1.5px;color:#777;text-transform:uppercase;}
.value-card .price{font-size:1.2rem;font-weight:800;color:#fff;margin:0.4rem 0;}
.value-card p{font-size:0.8rem;color:#8f8f8f;line-height:1.6;}

/* ── RESPONSIVE ── */
@media(max-width:960px){
  .hero h1{font-size:clamp(2rem,5.5vw,3.4rem);letter-spacing:-2px;}
  .hero-split{flex-direction:column;gap:2.5rem;padding-top:2rem;}
  .hero-copy{flex:none;width:100%;}
  .hero-visual{flex:none;width:100%;justify-content:center;}
  .hero-ctas{flex-wrap:wrap;}
}
@media(max-width:600px){
  .hero{padding:48px 1.25rem 0;}
  .hero h1{font-size:clamp(1.8rem,7vw,2.6rem);letter-spacing:-1.5px;}
  .hero-headline-line{white-space:normal;}
  .hero-headline{margin-bottom:1.8rem;}
  .hero-eyebrow{font-size:0.62rem;letter-spacing:2px;}
  .hero-ctas{flex-direction:column;align-items:flex-start;}
  .btn-primary--priced,.btn-secondary{width:100%;justify-content:center;text-align:center;}
}
@media(max-width:768px){
  .nav{padding:0 1rem;}
  .hero{padding:80px 1.5rem 50px;}
  .hero-stats{gap:1.5rem;}
  .community-inner{flex-direction:column;}
  .community-visual{flex:none;width:100%;}
  section{padding:48px 1.5rem;}
  .books-grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr));}
  .guarantee-inner{flex-direction:column;}
  .guarantee-badge{width:100%;}
  .offer-item .val,.offer-bonus .val{display:none;}
  .pain-section .section-title{font-size:clamp(1.6rem,7.5vw,2.15rem);line-height:1.2;}
  .pain-text .story{font-size:0.98rem;line-height:1.85;}
  .pain-bullets li{font-size:0.9rem;padding:10px;}
  .pain-punch{font-size:0.95rem;padding:1.1rem 1.2rem;}
}

/* ── BOOK SELECTOR MODAL ────────────────────────────────── */
.modal-book-btn{position:relative;background:#1a1a1a;border:1.5px solid #252525;border-radius:8px;padding:1.1rem 0.9rem 0.9rem;text-align:left;cursor:pointer;transition:border-color 0.18s,box-shadow 0.18s,transform 0.15s;overflow:hidden;width:100%;outline:none;}
.modal-book-btn:hover,.modal-book-btn:focus-visible{border-color:var(--gold);box-shadow:0 0 0 1px rgba(212,168,54,0.2),0 8px 22px rgba(0,0,0,0.5);transform:translateY(-2px);}
.modal-book-btn.is-selected{border-color:var(--gold);box-shadow:0 0 0 1px rgba(212,168,54,0.38),0 10px 24px rgba(0,0,0,0.55);}
.mbb-accent{position:absolute;top:0;left:0;right:0;height:3px;background:var(--mbb-color,var(--gold));}
.mbb-cover{width:100%;aspect-ratio:2/3;border:1px solid rgba(255,255,255,0.08);border-radius:5px;background-size:contain;background-position:center;background-repeat:no-repeat;background-color:#0f0f0f;margin-bottom:0.6rem;}
.mbb-emoji{font-size:1.5rem;margin-bottom:0.5rem;display:block;line-height:1;}
.mbb-title{font-size:0.8rem;font-weight:700;color:#fff;line-height:1.3;margin-bottom:0.25rem;}
.mbb-meta{font-family:'Courier New',monospace;font-size:0.57rem;letter-spacing:1.5px;text-transform:uppercase;color:#666;margin-bottom:0.35rem;}
.mbb-price{font-family:'Courier New',monospace;display:inline-flex;align-items:baseline;gap:0.28rem;}
.mbb-price-now{font-size:0.78rem;font-weight:800;color:var(--gold);}
.mbb-price-old{font-size:0.7rem;font-weight:700;color:rgba(232,228,222,0.5);text-decoration:line-through;text-decoration-color:rgba(232,228,222,0.4);}
.modal-bonus-card{position:relative;background:rgba(212,168,54,0.04);border:1.5px solid rgba(212,168,54,0.2);border-radius:8px;padding:1.1rem 0.9rem 0.9rem;text-align:left;overflow:hidden;}
.mbb-free-badge{display:inline-flex;align-items:center;gap:4px;background:rgba(212,168,54,0.12);border:1px solid rgba(212,168,54,0.3);border-radius:20px;padding:3px 10px;margin-top:0.45rem;}
.mbb-free-badge span{font-size:0.6rem;font-family:'Courier New',monospace;letter-spacing:1px;color:var(--gold);font-weight:700;}
.modal-book-footer{display:flex;align-items:center;justify-content:space-between;gap:0.8rem;margin-top:1rem;padding-top:1rem;border-top:1px solid #1f1f1f;}
.modal-selection-meta{font-family:'Courier New',monospace;font-size:0.68rem;letter-spacing:1px;color:#999;line-height:1.6;}
.modal-selection-meta strong{color:var(--gold);}
.modal-continue-btn{background:var(--gold);color:#000;font-family:'Courier New',monospace;font-size:0.72rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:10px 16px;border:none;border-radius:3px;cursor:pointer;}
.modal-continue-btn:disabled{opacity:0.45;cursor:not-allowed;}
@media(max-width:900px){#modalBookGrid{grid-template-columns:repeat(3,1fr)!important;}}
@media(max-width:520px){#modalBookGrid{grid-template-columns:repeat(2,1fr)!important;}}

/* ── LIVE ACTIVITY TICKER ── */
.live-activity{display:inline-flex;align-items:center;gap:7px;font-family:'Courier New',monospace;font-size:0.67rem;letter-spacing:0.5px;color:#777;background:rgba(212,168,54,0.04);border:1px solid rgba(212,168,54,0.13);padding:5px 14px;border-radius:20px;margin-top:0.85rem;}
.live-activity #liveActivityText{transition:opacity 0.3s ease;}
.live-pulse-dot{width:6px;height:6px;border-radius:50%;background:#22c55e;flex-shrink:0;animation:livePulse 1.2s ease-in-out infinite;}

/* ── TESTIMONIAL LEAD ── */
.testimonial-lead{font-size:0.93rem;color:#888;max-width:660px;margin:0 auto 2.2rem;line-height:1.85;padding:0.9rem 1.2rem;background:rgba(212,168,54,0.03);border-left:2px solid rgba(212,168,54,0.35);border-radius:0 4px 4px 0;}

/* ── BENEFIT OUTCOME LINE ── */
.benefit-stat{font-family:'Courier New',monospace;font-size:0.64rem;letter-spacing:1.5px;color:var(--gold);opacity:0.8;margin-top:0.7rem;padding-top:0.6rem;border-top:1px solid rgba(212,168,54,0.15);}

/* ── PRICE CARD SOCIAL LINE ── */
.price-card-social{font-size:0.73rem;color:#777;text-align:center;margin-bottom:1.2rem;padding-bottom:1.2rem;border-bottom:1px solid var(--border);line-height:1.6;}
.price-card-social strong{color:#aaa;}
.price-risk-reversal{font-size:0.74rem;color:#8f8f8f;text-align:center;margin-top:0.8rem;line-height:1.6;}

/* ── WHAT HAPPENS NEXT ── */
.whats-next{display:flex;flex-wrap:wrap;justify-content:center;gap:0.5rem;margin:1.5rem auto 0;max-width:560px;}
.wn-step{display:flex;align-items:center;gap:8px;font-size:0.77rem;color:#888;background:rgba(255,255,255,0.025);border:1px solid var(--border);border-radius:6px;padding:7px 14px;}
.wn-step .wn-n{font-family:'Courier New',monospace;font-size:0.62rem;font-weight:700;color:#000;background:var(--gold);border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
</style>
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
          <img src="assets/hero-mockup.png" alt="Viral AI Prompts System — product preview showing 11 books and 1,100 prompt templates" class="hero-mockup-img" width="580" height="529">
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
    <div class="section-badge">Problem Section</div>
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
    <div class="section-badge">Solution Section</div>
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
    <div class="section-badge">Benefits Section</div>
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
    <div class="section-badge">What You Can Create</div>
    <h2 class="section-title">Real Outputs. Real Prompts. <span style="color:var(--gold);">From This System.</span></h2>
    <p class="section-sub" style="max-width:640px;margin:0 auto 0.5rem;">Action figures, Ghibli art, Mughal warriors, royal pet portraits, cinematic movie posters, and professional portraits — all from the same system, all for the same ₹299.</p>
    <div class="gallery-collage-wrap">
      <div class="gallery-img">
        <img src="assets/collage-1.jpg" alt="AI image examples — action figure, Ghibli anime, Mughal warrior, royal dog portrait, movie poster, rooftop portrait" width="699" height="1024" loading="lazy">
      </div>
    </div>
    <p style="font-size:0.85rem;color:var(--muted);margin-top:1rem;">6 styles shown. 1,100+ prompt templates across 11 books — many more styles inside.</p>
  </div>
</section>

<!-- BUNDLE INCLUDES -->
<section class="benefits-section">
  <div class="container" style="text-align:center;">
    <div class="section-badge">Bundle Includes</div>
    <h2 class="section-title">What&rsquo;s Included Inside The Bundle</h2>
    <div class="bundle-collection-visual">
      <img src="assets/sections/bundle-collection.jpg" alt="Complete Viral AI Prompts System collection showing all 11 books and the bonus guide" width="1800" height="900" loading="lazy">
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
      <li><strong>Private WhatsApp Community Access</strong><span>Get new ideas, trending styles, support, and inspiration regularly.</span></li>
    </ul>
    <p class="bundle-bridge">Everything you need to go from beginner to confident creator.</p>
    <div class="bundle-pricing-line">
      <div class="bundle-value">Total Value ₹2,189+</div>
      <div class="bundle-price">Today&rsquo;s Price: Just ₹299</div>
    </div>
    <a href="#pricing" class="btn-primary" style="display:inline-block;margin-top:0.45rem;">Unlock The Bundle</a>
  </div>
</section>

<!-- PERFECT FOR -->
<section class="benefits-section">
  <div class="container" style="text-align:center;">
    <div class="section-badge">Audience Fit</div>
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
    <div class="section-badge">System Delivery + Bonuses</div>
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
        <div class="bonus-title">Private WhatsApp Community Access</div>
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
    <div class="section-badge">Offer Details</div>
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
          <li>WhatsApp community access</li>
          <li>All future updates to your book</li>
        </ul>
        <button class="btn-buy-outline" data-action="start-checkout" data-plan="single">
          Start with One Book - ₹99 (was ₹199)
        </button>
        <div class="price-risk-reversal">Good if you want to test one style first before committing.</div>
      </div>

      <!-- Full Bundle -->
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
          <li>🎁 Exclusive WhatsApp community access</li>
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

    <p style="margin-top:1.5rem;font-size:0.78rem;color:#555;" id="pricingAnchor">💡 At ₹299, that's about ₹27 per book — still lower than one cafe snack for a reusable creation system.</p>
    <p style="margin-top:0.5rem;font-size:0.75rem;color:#444;">🔒 Secure checkout via Razorpay (India)</p>
    <p style="margin-top:0.5rem;font-size:0.75rem;color:#666;">✅ 24-hour technical guarantee: if your access/login link does not work, we'll fix it fast or refund you. No refunds after successful access.</p>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials-section" id="reviews">
  <div class="container">
    <div class="section-badge" style="text-align:center;">Real Buyers · Real Results</div>
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
            <div class="testimonial-badge">VERIFIED BUYER · BUNDLE</div>
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
            <div class="testimonial-badge">VERIFIED BUYER · BUNDLE</div>
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
            <div class="testimonial-badge">VERIFIED BUYER · BUNDLE</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"Opened an Etsy store after buying Book 7. In the first two weeks, I made around ₹1,150 from AI movie poster prints. A few buyers asked for custom versions, and I could deliver quickly. I recovered the bundle cost in the first few orders."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background:#b45309;">VT</div>
          <div>
            <div class="testimonial-name">Vikram T.</div>
            <div class="testimonial-sub">Graphic designer · Chennai</div>
            <div class="testimonial-badge">VERIFIED BUYER · BUNDLE</div>
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
            <div class="testimonial-badge">VERIFIED BUYER · BUNDLE</div>
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
        <div class="spb-num">₹27</div>
        <div class="spb-label">Per Book · Lifetime</div>
      </div>
    </div>
  </div>
</section>

<!-- EARLY ACCESS PRICING -->
<section class="benefits-section">
  <div class="container" style="text-align:center;">
    <div class="section-badge">Pricing Window</div>
    <h2 class="section-title">Early Access Pricing</h2>
    <div class="early-access-copy">
      <p class="early-access-lead">Right now, the complete 11-book bundle is available for just ₹299.</p>
      <p>Each book individually costs ₹199.</p>
      <p>Once the launch offer ends, the price increases.</p>
      <p class="early-access-vision">If AI art trends are already everywhere on Instagram and Reels, imagine where they&rsquo;ll be 3 months from now.</p>
      <p class="early-access-punch">Best time to start creating is while the trends, styles, and tools are still fresh.</p>
    </div>
    <div class="offer-bonus-callout offer-bonus-callout--secondary">
      <h3>Bonuses Available During This Offer</h3>
      <p>The AI Prompt Finder CustomGPT, bonus ebook, and private WhatsApp community are currently included free with the full bundle.</p>
      <p>These bonuses may not stay included permanently as new updates and books are added later.</p>
      <a href="#pricing" class="btn-secondary" style="display:inline-block;">Claim My Bonuses</a>
    </div>
    <div class="momentum-copy">
      <h3>Don&rsquo;t Keep Watching Others Create Cool AI Art</h3>
      <p>Every day, more people are turning ordinary photos into stunning AI visuals, cinematic posters, anime portraits, and viral-style edits.</p>
      <p>Most are not designers.</p>
      <p>Most are not AI experts.</p>
      <p>They simply started.</p>
      <p>A few minutes from now, the bundle could already be inside the inbox and the first AI image could already be created.</p>
      <a href="#pricing" class="btn-primary" style="display:inline-block;">Make My First AI Image</a>
    </div>
  </div>
</section>

<!-- COLLAGE 2: More Styles -->
<section class="gallery-section gallery-section--alt">
  <div class="container" style="text-align:center;">
    <div class="section-badge">More Styles Inside</div>
    <h2 class="section-title">Still Not Sure? <span style="color:var(--gold);">See What Else Is Possible.</span></h2>
    <p class="section-sub" style="max-width:640px;margin:0 auto 0.5rem;">Pixar-style portraits, luxury product shots, Barbiecore fashion, childhood nostalgia, festive scrapbooks, cyberpunk cities — all using prompts from this single ₹299 bundle.</p>
    <div class="gallery-collage-wrap">
      <div class="gallery-img">
        <img src="assets/collage-2.jpg" alt="AI image examples — Pixar boy, product photography, Barbiecore portrait, firefly magic, Diwali scrapbook, cyberpunk Mumbai" width="699" height="1024" loading="lazy">
      </div>
    </div>
    <p style="font-size:0.85rem;color:var(--muted);margin-top:1rem;">Still only 6 of 1,100+ styles. Every prompt works the same way — fill in the blanks, generate, done.</p>
  </div>
</section>

<!-- FAQ -->
<section id="faq">
  <div class="container">
    <div class="section-badge" style="text-align:center;">FAQ</div>
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
         'Yes. The AI Prompt Finder CustomGPT is currently included free with the bundle.'],
        ['Do I get support if I get stuck?',
         'Yes. Buyers also get access to the WhatsApp community for guidance, updates, and support.'],
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

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-logo">AI Prompt Books</div>
  <div class="footer-links">
    <a href="#reviews">Proof</a>
    <a href="#pricing">Pricing</a>
    <a href="#faq">FAQ</a>
    <a href="/contact-details.php">Contact</a>
    <a href="/terms-and-conditions.php">Terms</a>
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
  <div style="max-width:700px;margin:2rem auto;background:#141414;border:1px solid #2a2a2a;border-radius:12px;padding:2rem;box-shadow:0 24px 64px rgba(0,0,0,0.7);">
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
      ?>
      <button type="button" data-action="toggle-book-selection" data-book-id="<?= $id ?>"
              class="modal-book-btn"
              style="--mbb-color:<?= htmlspecialchars($book['accent']) ?>;">
        <div class="mbb-accent"></div>
        <?php if ($hasModalCover): ?>
          <div class="mbb-cover" style="background-image:url('<?= htmlspecialchars($modalCoverWebPath, ENT_QUOTES, 'UTF-8') ?>')"></div>
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
      ?>
      <div class="modal-bonus-card">
        <div class="mbb-accent" style="background:linear-gradient(90deg,#d4a836,#f59e0b);"></div>
        <?php if ($hasBonusCover): ?>
          <div class="mbb-cover" style="background-image:url('<?= htmlspecialchars($bonusCoverWebPath, ENT_QUOTES, 'UTF-8') ?>')"></div>
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
  <div style="max-width:460px;margin:auto;background:#141414;border:1px solid #2a2a2a;border-radius:8px;padding:2.5rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
      <div style="font-family:'Courier New',monospace;font-size:0.75rem;letter-spacing:2px;color:var(--gold);" id="checkoutTitle">CHECKOUT</div>
      <button type="button" class="checkout-close-btn" data-action="close-checkout">×</button>
    </div>
    <div id="checkoutSummary" style="background:#1a1a1a;border:1px solid #2a2a2a;border-radius:4px;padding:1rem;margin-bottom:1.5rem;font-size:0.85rem;color:#aaa;"></div>

    <div style="margin-bottom:1rem;">
      <label style="font-family:'Courier New',monospace;font-size:0.72rem;letter-spacing:1px;color:#888;display:block;margin-bottom:6px;">YOUR NAME</label>
      <input type="text" id="buyerName" placeholder="e.g. Raj Sharma" style="width:100%;background:#0a0a0a;border:1.5px solid #333;color:#fff;padding:10px 14px;border-radius:3px;font-size:0.88rem;outline:none;">
    </div>
    <div style="margin-bottom:1.5rem;">
      <label style="font-family:'Courier New',monospace;font-size:0.72rem;letter-spacing:1px;color:#888;display:block;margin-bottom:6px;">EMAIL ADDRESS</label>
      <input type="email" id="buyerEmail" placeholder="you@example.com" style="width:100%;background:#0a0a0a;border:1.5px solid #333;color:#fff;padding:10px 14px;border-radius:3px;font-size:0.88rem;outline:none;">
      <div style="font-size:0.75rem;color:#555;margin-top:4px;">Your login credentials will be sent to this email right after payment.</div>
    </div>
    <div id="paymentButtons" style="display:flex;flex-direction:column;gap:0.8rem;">
      <!-- Razorpay button (India) -->
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

<script src="/assets/js/pixel-tracking.js"></script>
<script>
const currency = 'INR';
const BUNDLE_PRICE_INR = 299;
const RAZORPAY_CHECKOUT_SRC = 'https://checkout.razorpay.com/v1/checkout.js';
let razorpayLoaderPromise = null;

function trackEvent(eventName, params = {}) {
  if (typeof window.pixelTrack === 'function') {
    window.pixelTrack(eventName, params);
    return;
  }
  if (typeof window.fbq !== 'function') return;
  window.fbq('track', eventName, params);
}

function trackCustomEvent(eventName, params = {}) {
  if (typeof window.pixelTrackCustom === 'function') {
    window.pixelTrackCustom(eventName, params);
    return;
  }
  if (typeof window.fbq !== 'function') return;
  window.fbq('trackCustom', eventName, params);
}

function getCheckoutMetrics(plan, selectedCount = 0) {
  if (plan === 'bundle') {
    return { value: BUNDLE_PRICE_INR, numItems: 11, contentName: 'Full Bundle' };
  }
  const safeCount = selectedCount > 0 ? selectedCount : 1;
  return {
    value: safeCount * SINGLE_BOOK_PRICE_INR,
    numItems: safeCount,
    contentName: `Single Plan (${safeCount} book${safeCount > 1 ? 's' : ''})`
  };
}

function trackCheckoutEvent(eventName, plan, selectedCount = 0, extras = {}) {
  const metrics = getCheckoutMetrics(plan, selectedCount);
  trackEvent(eventName, {
    currency,
    value: metrics.value,
    num_items: metrics.numItems,
    content_name: metrics.contentName,
    ...extras
  });
}

function ensureRazorpayLoaded() {
  if (typeof window.Razorpay === 'function') {
    return Promise.resolve();
  }
  if (razorpayLoaderPromise) {
    return razorpayLoaderPromise;
  }

  razorpayLoaderPromise = new Promise((resolve, reject) => {
    const scriptElement = document.createElement('script');
    scriptElement.src = RAZORPAY_CHECKOUT_SRC;
    scriptElement.async = true;
    scriptElement.onload = () => resolve();
    scriptElement.onerror = () => reject(new Error('Razorpay SDK failed to load.'));
    document.head.appendChild(scriptElement);
  });

  return razorpayLoaderPromise;
}

// ── Checkout flow ─────────────────────────────────────────────────────────────
let currentPlan   = null;
let currentBookId = null;
let currentBookIds = [];
let selectedBookIds = [];
const SINGLE_BOOK_PRICE_INR = 99;

function startCheckout(plan) {
  trackCustomEvent('CheckoutStarted', { plan });
  if (plan === 'single') {
    selectedBookIds = [];
    updateSelectedBooksUi();
    document.getElementById('bookModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    trackCustomEvent('BookModalOpened', { source: 'start-checkout' });
  } else {
    trackCheckoutEvent('InitiateCheckout', 'bundle', 11, { source: 'bundle-cta' });
    proceedCheckout('bundle', null);
  }
}

function closeModal() {
  document.getElementById('bookModal').style.display = 'none';
  document.body.style.overflow = '';
  trackCustomEvent('BookModalClosed', { selected_count: selectedBookIds.length });
}

function toggleBookSelection(bookId) {
  if (!bookId || bookId < 1 || bookId > 11) return;
  if (selectedBookIds.includes(bookId)) {
    selectedBookIds = selectedBookIds.filter((id) => id !== bookId);
  } else {
    selectedBookIds.push(bookId);
  }
  selectedBookIds.sort((a, b) => a - b);
  updateSelectedBooksUi();
  trackCustomEvent('BookSelectionUpdated', {
    selected_count: selectedBookIds.length,
    selected_books: selectedBookIds.join(',')
  });
}

function updateSelectedBooksUi() {
  const selectedSet = new Set(selectedBookIds);
  document.querySelectorAll('[data-action="toggle-book-selection"][data-book-id]').forEach((buttonElement) => {
    const bookId = parseInt(buttonElement.dataset.bookId || '0', 10);
    const isSelected = selectedSet.has(bookId);
    buttonElement.classList.toggle('is-selected', isSelected);
  });

  const countElement = document.getElementById('selectedBooksCount');
  const amountElement = document.getElementById('selectedBooksAmount');
  const continueButtonElement = document.getElementById('continueSelectedBooksBtn');
  const selectedCount = selectedBookIds.length;
  const totalAmount = selectedCount * SINGLE_BOOK_PRICE_INR;

  if (countElement) {
    countElement.textContent = `${selectedCount} BOOK${selectedCount === 1 ? '' : 'S'} SELECTED`;
  }
  if (amountElement) {
    amountElement.innerHTML = `<strong>₹${totalAmount}</strong>`;
  }
  if (continueButtonElement) {
    continueButtonElement.disabled = selectedCount === 0;
  }
}

function proceedCheckout(plan, bookId, bookIds = null) {
  closeModal();
  currentPlan   = plan;
  currentBookId = bookId;
  currentBookIds = Array.isArray(bookIds) ? bookIds : (bookId ? [bookId] : []);
  const selectedCount = currentBookIds.length;
  const totalInr = plan === 'bundle' ? 299 : (selectedCount * SINGLE_BOOK_PRICE_INR);
  const price = `₹${totalInr}`;

  // Summary text
  const booksById = <?= json_encode(array_map(fn($b) => $b['title'], $books)) ?>;
  const selectedBookNames = currentBookIds.map((id) => booksById[id]).filter(Boolean);
  const bookName = plan === 'bundle'
    ? 'All 11 Books + Bonus Guide (Full Bundle)'
    : selectedBookNames.join(', ');
  const planLabel = plan === 'bundle'
    ? '📦 Full Bundle'
    : `📚 Selected Books (${selectedCount})`;
  document.getElementById('checkoutSummary').innerHTML =
    `<strong style="color:#fff">${planLabel}</strong><br>
     <span style="color:#888">${bookName}</span><br>
     <span style="color:#d4a836;font-weight:700;font-size:1.1rem;font-family:'Courier New',monospace;">${price}</span>`;

  document.getElementById('checkoutModal').style.display = 'block';
  document.body.style.overflow = 'hidden';

  if (plan === 'single') {
    trackCheckoutEvent('InitiateCheckout', 'single', selectedCount, {
      selected_books: currentBookIds.join(','),
      source: 'book-modal'
    });
  }
  trackCustomEvent('CheckoutModalOpened', {
    plan,
    selected_count: selectedCount,
    value: totalInr
  });
  trackCustomEvent('PaymentPageViewed', {
    plan,
    selected_count: selectedCount,
    value: totalInr,
    page_type: 'checkout-modal'
  });
}

function closeCheckout() {
  document.getElementById('checkoutModal').style.display = 'none';
  document.body.style.overflow = '';
  document.getElementById('checkoutError').style.display = 'none';
  trackCustomEvent('CheckoutModalClosed', {
    plan: currentPlan || 'unknown',
    selected_count: currentBookIds.length
  });
}

function getCheckoutData() {
  const name  = document.getElementById('buyerName').value.trim();
  const email = document.getElementById('buyerEmail').value.trim();
  if (!name)  { showError('Please enter your name.'); return null; }
  if (!email || !email.includes('@')) { showError('Please enter a valid email address.'); return null; }
  return { name, email };
}

function showError(msg) {
  const el = document.getElementById('checkoutError');
  el.textContent = msg;
  el.style.display = 'block';
  trackCustomEvent('CheckoutErrorShown', {
    message: msg.slice(0, 120),
    plan: currentPlan || 'unknown'
  });
}

// ── Razorpay ──────────────────────────────────────────────────────────────────
async function payWithRazorpay() {
  const data = getCheckoutData();
  if (!data) return;

  trackCheckoutEvent('AddPaymentInfo', currentPlan || 'bundle', currentBookIds.length || (currentPlan === 'bundle' ? 11 : 1), {
    payment_gateway: 'razorpay'
  });
  trackCustomEvent('RazorpayOrderStarted', {
    plan: currentPlan || 'unknown',
    selected_count: currentBookIds.length
  });

  document.getElementById('razorpayBtn').textContent = 'Creating order…';
  document.getElementById('razorpayBtn').disabled    = true;

  try {
    await ensureRazorpayLoaded();
    if (typeof window.Razorpay !== 'function') {
      showError('Payment window could not be initialized. Please refresh and try again.');
      return;
    }

    const res = await fetch('/api/razorpay-order.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({
        plan:    currentPlan,
        book_id: currentBookId,
        book_ids: currentBookIds,
        email:   data.email,
        name:    data.name
      })
    });
    const responseText = await res.text();
    let order = null;
    try {
      order = JSON.parse(responseText);
    } catch (parseError) {
      showError('Checkout setup failed on server. Please refresh and try again.');
      return;
    }
    if (!res.ok || !order.id) {
      showError(order.error || 'Failed to create order. Please try again.');
      return;
    }

    const options = {
      key:         '<?= RAZORPAY_KEY_ID ?>',
      amount:      order.amount,
      currency:    'INR',
      name:        '<?= SITE_NAME ?>',
      description: currentPlan === 'bundle' ? 'Full Bundle — All 11 Books + Bonus Guide' : `${currentBookIds.length} Book Access`,
      order_id:    order.id,
      prefill:     { name: data.name, email: data.email },
      theme:       { color: '#d4a836' },
      handler: async function(response) {
        await verifyRazorpay(response, data);
      },
      modal: {
        ondismiss: function() {
          trackCustomEvent('PaymentPopupDismissed', {
            plan: currentPlan || 'unknown',
            selected_count: currentBookIds.length
          });
          document.getElementById('razorpayBtn').textContent = 'Pay with UPI / Card (Razorpay)';
          document.getElementById('razorpayBtn').disabled    = false;
        }
      }
    };
    new Razorpay(options).open();
  } catch(e) {
    showError('Could not reach payment service. Check internet and try again.');
  } finally {
    document.getElementById('razorpayBtn').textContent = 'Pay with UPI / Card (Razorpay)';
    document.getElementById('razorpayBtn').disabled    = false;
  }
}

async function verifyRazorpay(response, data) {
  const res = await fetch('/api/razorpay-verify.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({ ...response, email: data.email, name: data.name, plan: currentPlan, book_id: currentBookId, book_ids: currentBookIds })
  });
  const result = await res.json();
  if (result.token) {
    const metrics = getCheckoutMetrics(currentPlan || 'bundle', currentBookIds.length || (currentPlan === 'bundle' ? 11 : 1));
    trackEvent('Purchase', {
      currency,
      value: metrics.value,
      content_name: metrics.contentName,
      num_items: metrics.numItems,
      payment_method: 'razorpay'
    });
    trackCustomEvent('PaymentVerified', {
      plan: currentPlan || 'unknown',
      selected_count: currentBookIds.length,
      payment_id: response.razorpay_payment_id || ''
    });
    window.location.href = '/setup-account.php?token=' + result.token;
  } else {
    trackCustomEvent('PaymentVerificationFailed', {
      plan: currentPlan || 'unknown',
      selected_count: currentBookIds.length
    });
    showError(result.error || 'Payment verification failed. Please contact support.');
  }
}

// ── FAQ toggles ───────────────────────────────────────────────────────────────
document.querySelectorAll('.faq-q').forEach(q => {
  q.addEventListener('click', () => {
    q.closest('.faq-item').classList.toggle('open');
  });
});

// ── Exit Intent — DISABLED ───────────────────────────────────────────────────
// (function initExitIntent() { ... })(); // disabled

function dismissExit() {
  document.getElementById('exitOverlay').classList.remove('show');
}

function submitExitEmail() {
  const email = document.getElementById('exitEmail').value.trim();
  if (!email || !email.includes('@')) {
    document.getElementById('exitEmail').style.borderColor = '#f87171';
    return;
  }
  // Store locally — wire to your email service (Mailchimp / ConvertKit etc) when live
  localStorage.setItem('aipb_waitlist_email', email);
  const popup = document.querySelector('.exit-popup');
  popup.innerHTML = '<div style="text-align:center;padding:1rem 0;"><div style="font-size:2rem;margin-bottom:1rem;">✅</div><h3 style="color:#fff;margin-bottom:0.5rem;">You are on the list!</h3><p style="color:#888;font-size:0.85rem;">Check your inbox — your free Ghibli sample prompt is on its way.</p><button type="button" class="exit-popup-dismiss" data-action="dismiss-exit" style="display:block;margin:1rem auto 0;">Close this →</button></div>';
  setTimeout(() => dismissExit(), 4000);
}

// ── Declarative button actions (no inline handlers) ──────────────────────────
document.addEventListener('click', (event) => {
  const actionElement = event.target.closest('[data-action]');
  if (!actionElement) return;

  const action = actionElement.dataset.action;
  const actionPlan = actionElement.dataset.plan || 'unknown';
  if (action === 'start-checkout') {
    trackCustomEvent('CtaClicked', { action, plan: actionPlan });
    startCheckout(actionElement.dataset.plan || 'bundle');
    return;
  }
  if (action === 'dismiss-exit') {
    dismissExit();
    return;
  }
  if (action === 'submit-exit-email') {
    trackEvent('Lead', { source: 'exit-intent', content_name: 'Sample Prompt Lead' });
    submitExitEmail();
    return;
  }
  if (action === 'close-modal') {
    closeModal();
    return;
  }
  if (action === 'toggle-book-selection') {
    const bookId = parseInt(actionElement.dataset.bookId || '0', 10);
    toggleBookSelection(Number.isNaN(bookId) ? 0 : bookId);
    return;
  }
  if (action === 'continue-selected-books') {
    trackCustomEvent('BookSelectionContinued', { selected_count: selectedBookIds.length });
    proceedCheckout('single', selectedBookIds[0] || null, [...selectedBookIds]);
    return;
  }
  if (action === 'close-checkout') {
    closeCheckout();
    return;
  }
  if (action === 'pay-razorpay') {
    trackCustomEvent('PayButtonClicked', { plan: currentPlan || 'unknown' });
    payWithRazorpay();
  }
});

// ── Funnel analytics ───────────────────────────────────────────────────────────
(function initFunnelTracking() {
  trackEvent('ViewContent', {
    content_name: 'Landing Page',
    content_category: 'Sales Page'
  });

  const trackedSections = [
    { id: 'pricing', event: 'PricingViewed', label: 'Pricing Section' },
    { id: 'reviews', event: 'TestimonialsViewed', label: 'Testimonials Section' },
    { id: 'faq', event: 'FaqViewed', label: 'FAQ Section' }
  ];

  if (!('IntersectionObserver' in window)) return;
  const fired = new Set();
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      const targetId = entry.target.id;
      if (!targetId || fired.has(targetId)) return;
      const meta = trackedSections.find((section) => section.id === targetId);
      if (!meta) return;
      fired.add(targetId);
      trackCustomEvent(meta.event, { section: meta.label });
    });
  }, { threshold: 0.35 });

  trackedSections.forEach((section) => {
    const sectionElement = document.getElementById(section.id);
    if (sectionElement) observer.observe(sectionElement);
  });
})();

// ── Countdown Timer ───────────────────────────────────────────────────────────
(function initCountdown() {
  const KEY = 'aipb_cd_end';
  let end = parseInt(localStorage.getItem(KEY) || '0', 10);
  if (!end || end < Date.now()) {
    end = Date.now() + 48 * 60 * 60 * 1000; // 48 hours from first visit
    localStorage.setItem(KEY, end);
  }
  const cdH = document.getElementById('cdH');
  const cdM = document.getElementById('cdM');
  const cdS = document.getElementById('cdS');
  function tick() {
    const diff = Math.max(0, end - Date.now());
    const h = Math.floor(diff / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    if (cdH) cdH.textContent = String(h).padStart(2,'0');
    if (cdM) cdM.textContent = String(m).padStart(2,'0');
    if (cdS) cdS.textContent = String(s).padStart(2,'0');
    if (diff === 0) clearInterval(timer);
  }
  tick();
  const timer = setInterval(tick, 1000);
})();

// ── Live Activity Ticker ──────────────────────────────────────────────────────
(function initActivityTicker() {
  const feed = [
    'Meera from Bengaluru generated her first poster style result · 2 min ago',
    'Rajiv from Kolkata made his first action figure image · 4 min ago',
    'Sneha from Ahmedabad finished her Ghibli portrait set · 7 min ago',
    'Arjun from Jaipur tested Book 7 — Movie Poster prompts · 11 min ago',
    'Kavya from Kochi joined the buyers community · 15 min ago',
    'Nikhil from Hyderabad created product visuals for his side project · 19 min ago',
    'Priya from Chennai turned her cat photo into a print she framed · 23 min ago',
    'Siddharth from Mumbai created his daughter\'s birthday portrait · 28 min ago',
  ];
  const el = document.getElementById('liveActivityText');
  if (!el) return;
  let idx = 0;
  setInterval(() => {
    idx = (idx + 1) % feed.length;
    el.style.opacity = '0';
    setTimeout(() => { el.textContent = feed[idx]; el.style.opacity = '1'; }, 300);
  }, 5000);
})();

// ── Sticky CTA (appears when hero scrolls out of view) ───────────────────────
(function initStickyCta() {
  const stickyCta = document.getElementById('stickyCta');
  if (!stickyCta) return;
  // Update sticky price text on currency change
  function updateStickyPrice() {
    const sp = document.getElementById('stickyPrice');
    if (sp) sp.textContent = '₹299 · One-time payment';
  }
  updateStickyPrice();

  const hero = document.querySelector('.hero');
  if (!hero || !('IntersectionObserver' in window)) return;
  const obs = new IntersectionObserver(([entry]) => {
    if (entry.isIntersecting) {
      stickyCta.classList.remove('visible');
    } else {
      stickyCta.classList.add('visible');
    }
  }, { threshold: 0 });
  obs.observe(hero);
})();
</script>
</body>
</html>
