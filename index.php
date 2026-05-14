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
<title>AI Prompt Books — 1,100 Fill-in-the-Blank AI Prompt Templates</title>
<meta name="description" content="11 books · 1,100 proven AI image prompt templates. Every trending style — Ghibli, action figures, movie posters, pet art. Fill in your details, copy, paste, create.">
<link rel="canonical" href="<?= htmlspecialchars(rtrim(SITE_URL, '/')) ?>/">
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
.nav-inner{max-width:1100px;margin:0 auto;height:60px;display:flex;align-items:center;justify-content:space-between;}
.nav-logo{font-family:'Courier New',monospace;font-size:0.8rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);}
.nav-links{display:flex;align-items:center;gap:1.2rem;}
.nav-login{font-family:'Courier New',monospace;font-size:0.75rem;letter-spacing:1px;text-transform:uppercase;color:var(--text-dim);border:1px solid var(--border);padding:7px 18px;border-radius:3px;transition:all 0.15s;}
.nav-login:hover{color:#fff;border-color:#555;}
.nav-cta{background:var(--gold);color:#000;font-family:'Courier New',monospace;font-size:0.75rem;letter-spacing:1px;text-transform:uppercase;padding:8px 20px;border-radius:3px;font-weight:700;transition:background 0.15s;}
.nav-cta:hover{background:#e8b93a;}

/* ── HERO ── */
.hero{display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding:100px 2rem 60px;position:relative;overflow:hidden;}
.hero-bg{position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% 0%, rgba(212,168,54,0.1) 0%, transparent 70%);pointer-events:none;}
/* Split layout */
.hero-split{display:flex;align-items:center;gap:3.2rem;width:100%;max-width:1240px;margin-bottom:3rem;}
.hero-text{flex:1;min-width:0;}
.hero-visual{flex:0 0 540px;margin-top:-12px;}
.hero-badge{font-family:'Courier New',monospace;font-size:0.7rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);border:1px solid rgba(212,168,54,0.7);background:rgba(212,168,54,0.07);padding:5px 16px;border-radius:2px;margin-bottom:1.8rem;display:inline-block;}
.hero h1{font-size:clamp(2.5rem,5.5vw,4.8rem);font-weight:900;line-height:1.05;letter-spacing:-2px;margin-bottom:1.4rem;color:#fff;}
.hero h1 span{color:var(--gold);}
.hero-sub{font-size:clamp(0.95rem,1.8vw,1.15rem);color:#aaa;max-width:520px;margin:0 0 2rem;}
.hero-ctas{display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1rem;}
.hero-trust{font-size:0.75rem;color:#555;line-height:2;margin-bottom:0;}
.hero-trust span{color:#444;}
.hero-proof-strip{display:flex;flex-wrap:wrap;gap:0.5rem;margin:1rem 0 1.2rem;}
.hero-proof-pill{font-size:0.72rem;color:#aaa;background:rgba(255,255,255,0.04);border:1px solid var(--border);border-radius:16px;padding:6px 12px;}
.hero-mini-testimonial{margin-top:0.9rem;font-size:0.8rem;color:#888;max-width:560px;line-height:1.7;border-left:2px solid rgba(212,168,54,0.35);padding-left:0.9rem;}
.hero-mini-testimonial strong{color:#ccc;}
.btn-primary{background:var(--gold);color:#000;font-weight:700;font-family:'Courier New',monospace;font-size:0.85rem;letter-spacing:1px;text-transform:uppercase;padding:14px 32px;border-radius:3px;border:none;cursor:pointer;transition:all 0.15s;animation:ctaGlow 3.5s ease-in-out infinite;}
.btn-primary:hover{background:#e8b93a;transform:translateY(-2px);box-shadow:0 6px 24px rgba(212,168,54,0.4);}
@keyframes ctaGlow{0%,100%{box-shadow:0 0 0 0 rgba(212,168,54,0.0),0 2px 8px rgba(0,0,0,0.3);}55%{box-shadow:0 0 0 7px rgba(212,168,54,0.14),0 4px 20px rgba(212,168,54,0.22);}}
.btn-secondary{background:transparent;color:#fff;font-family:'Courier New',monospace;font-size:0.85rem;letter-spacing:1px;text-transform:uppercase;padding:13px 32px;border-radius:3px;border:1.5px solid #444;cursor:pointer;transition:all 0.15s;}
.btn-secondary:hover{border-color:#888;transform:translateY(-1px);}
.hero-stats{display:flex;gap:3rem;flex-wrap:wrap;justify-content:center;padding-top:2rem;border-top:1px solid var(--border);width:100%;max-width:1200px;}
.hero-stat-num{font-family:'Courier New',monospace;font-size:2.5rem;font-weight:900;color:var(--gold);line-height:1;}
.hero-stat-label{font-size:0.78rem;color:var(--text-dim);letter-spacing:1px;margin-top:4px;}
/* Style preview cards */
.style-preview-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0.65rem;}
.sp-card{border-radius:8px;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,0.08);transition:transform 0.2s;aspect-ratio:4/5;}
.sp-card img{width:100%;height:100%;object-fit:cover;display:block;transition:transform 0.35s;}
.sp-card:hover{transform:translateY(-3px);}
.sp-card:hover img{transform:scale(1.07);}
.sp-overlay{position:absolute;bottom:0;left:0;right:0;padding:0.65rem 0.7rem;background:linear-gradient(0deg,rgba(0,0,0,0.92) 0%,transparent 100%);}
.sp-name{font-size:0.72rem;font-weight:700;color:#fff;line-height:1.3;}
.sp-count{font-family:'Courier New',monospace;font-size:0.55rem;color:rgba(255,255,255,0.45);margin-top:0.15rem;letter-spacing:1px;}
.sp-tag{position:absolute;top:7px;right:7px;font-family:'Courier New',monospace;font-size:0.5rem;font-weight:700;padding:2px 6px;border-radius:2px;letter-spacing:0.5px;z-index:1;}
/* Gallery section */
.gallery-section{border-top:1px solid var(--border);padding:52px 2rem 44px;}
.gallery-img{width:100%;max-width:900px;margin:2rem auto 0;border-radius:10px;overflow:hidden;border:1px solid var(--border);display:block;}
.gallery-img img{width:100%;display:block;}
/* Gallery style tag strip */
.gallery-styles{display:flex;flex-wrap:wrap;justify-content:center;gap:0.5rem;margin:1.2rem auto 0;max-width:900px;}
.gallery-style-tag{font-family:'Courier New',monospace;font-size:0.62rem;letter-spacing:1px;text-transform:uppercase;background:rgba(212,168,54,0.06);color:var(--gold);border:1px solid rgba(212,168,54,0.25);padding:4px 12px;border-radius:20px;}

/* ── TESTIMONIALS ── */
.testimonials-section{border-top:1px solid var(--border);background:var(--surface);}
.testimonial-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.2rem;margin-top:2.5rem;}
.testimonial-card{background:#0f0f0f;border:1px solid var(--border);border-radius:8px;padding:1.6rem;position:relative;transition:border-color 0.2s,transform 0.15s;}
.testimonial-card:hover{border-color:rgba(212,168,54,0.4);transform:translateY(-2px);}
.testimonial-card::before{content:'❝';position:absolute;top:12px;right:16px;font-size:2.5rem;color:rgba(212,168,54,0.12);line-height:1;}
.testimonial-stars{color:#f59e0b;font-size:0.85rem;letter-spacing:2px;margin-bottom:0.8rem;}
.testimonial-text{font-size:0.9rem;color:#bbb;line-height:1.75;margin-bottom:1.2rem;font-style:italic;}
.testimonial-author{display:flex;align-items:center;gap:0.8rem;}
.testimonial-avatar{width:40px;height:40px;border-radius:50%;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:800;font-family:'Courier New',monospace;flex-shrink:0;color:#fff;letter-spacing:0;}
.testimonial-name{font-size:0.82rem;font-weight:700;color:#e8e4de;}
.testimonial-sub{font-size:0.72rem;color:#555;margin-top:2px;}
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
.exit-overlay{display:none;position:fixed;inset:0;z-index:500;background:rgba(0,0,0,0.7);backdrop-filter:blur(3px);align-items:flex-end;justify-content:center;}
.exit-overlay.show{display:flex;}
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
.pay-btn-razorpay{display:none;background:#2e86c1;color:#fff;font-family:'Courier New',monospace;font-size:0.8rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:13px;border:none;border-radius:3px;cursor:pointer;transition:background 0.15s;}
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
.container{max-width:1100px;margin:0 auto;}
.section-badge{font-family:'Courier New',monospace;font-size:0.65rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:0.8rem;}
.section-title{font-size:clamp(1.8rem,4vw,2.8rem);font-weight:900;letter-spacing:-1px;color:#fff;margin-bottom:1rem;}
.section-sub{font-size:1rem;color:#888;max-width:540px;}

/* ── CURRENCY DETECTOR BADGE ── */
.currency-bar{background:var(--surface2);border:1px solid var(--border);border-radius:4px;padding:10px 20px;display:inline-flex;align-items:center;gap:12px;margin-bottom:2rem;font-size:0.82rem;}
.currency-bar .detected{color:#888;}
.currency-bar .flag{font-size:1.2rem;}
.currency-switch{font-family:'Courier New',monospace;font-size:0.7rem;letter-spacing:1px;text-transform:uppercase;background:transparent;border:1px solid var(--border);color:var(--text-dim);padding:4px 10px;border-radius:2px;cursor:pointer;transition:all 0.1s;}
.currency-switch:hover{border-color:var(--gold);color:var(--gold);}

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
.price-billing{font-size:0.78rem;color:#888;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--border);}
.price-features{list-style:none;margin-bottom:2rem;text-align:left;}
.price-features li{font-size:0.85rem;color:#aaa;padding:5px 0;display:flex;gap:8px;align-items:flex-start;text-align:left;}
.price-features li::before{content:'✓';color:var(--gold);font-weight:700;flex-shrink:0;margin-top:2px;}
.price-feature-tools{line-height:1.6;}
.btn-buy{width:100%;background:var(--gold);color:#000;font-family:'Courier New',monospace;font-size:0.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:13px;border:none;border-radius:3px;cursor:pointer;transition:background 0.15s;}
.btn-buy:hover{background:#e8b93a;}
.btn-buy-outline{width:100%;background:transparent;color:var(--gold);font-family:'Courier New',monospace;font-size:0.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:12px;border:1.5px solid var(--gold);border-radius:3px;cursor:pointer;transition:all 0.15s;}
.btn-buy-outline:hover{background:rgba(212,168,54,0.08);}

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
.pain-section{background:var(--surface);padding-bottom:48px;}
.pain-text{max-width:740px;margin:0 auto;}
.pain-text .story{font-size:1.05rem;color:#aaa;line-height:1.9;margin-bottom:1rem;}
.pain-text .story strong{color:#fff;}
.pain-bullets{list-style:none;margin:1.8rem 0;}
.pain-bullets li{display:flex;gap:12px;align-items:flex-start;padding:9px 0;font-size:0.92rem;color:#aaa;border-bottom:1px solid var(--border);}
.pain-bullets li:last-child{border-bottom:none;}
.pain-bullets li .x{color:#f87171;font-weight:900;flex-shrink:0;font-size:1rem;}
.pain-punch{font-size:1rem;font-weight:700;color:#fff;margin-top:1.8rem;padding:1.2rem 1.5rem;background:#0a0a0a;border-left:3px solid var(--gold);border-radius:0 4px 4px 0;font-style:italic;}

/* ── SOLUTION BRIDGE ── */
.solution-section{text-align:center;border-top:1px solid var(--border);padding-top:48px;}
.solution-section .big-intro{font-size:clamp(1.5rem,3.5vw,2.2rem);font-weight:900;color:#fff;max-width:760px;margin:1.5rem auto 1rem;line-height:1.25;letter-spacing:-0.5px;}
.solution-section .big-intro span{color:var(--gold);}
.solution-desc{font-size:0.98rem;color:#888;max-width:580px;margin:0 auto 2rem;line-height:1.8;}

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

/* ── OFFER STACK ── */
.offer-section{background:var(--surface2);border-top:1px solid var(--border);border-bottom:1px solid var(--border);}
.offer-list{max-width:660px;margin:2rem auto 0;}
.offer-item{display:flex;align-items:center;gap:1rem;padding:10px 0;border-bottom:1px solid var(--border);font-size:0.88rem;color:#ccc;}
.offer-item:last-child{border-bottom:none;}
.offer-item .chk{color:var(--gold);font-weight:700;flex-shrink:0;}
.offer-item .name{flex:1;}
.offer-item .val{font-family:'Courier New',monospace;font-size:0.72rem;color:var(--text-dim);text-decoration:line-through;flex-shrink:0;}
.offer-bonus{display:flex;align-items:center;gap:1rem;padding:10px 0;border-bottom:1px solid var(--border);font-size:0.88rem;color:var(--gold);}
.offer-bonus .chk{font-weight:700;flex-shrink:0;}
.offer-bonus .name{flex:1;}
.offer-bonus .val{font-family:'Courier New',monospace;font-size:0.72rem;color:var(--gold);flex-shrink:0;}
.offer-total-row{display:flex;justify-content:space-between;align-items:center;padding:1.2rem 0 0;margin-top:0.5rem;border-top:2px solid var(--gold);}
.offer-total-label{font-family:'Courier New',monospace;font-size:0.68rem;letter-spacing:2px;text-transform:uppercase;color:#888;}
.offer-total-price{display:flex;align-items:baseline;gap:0.8rem;}
.offer-total-orig{font-size:1rem;color:var(--text-dim);text-decoration:line-through;}
.offer-total-today{font-size:1.8rem;font-weight:900;color:var(--gold);font-family:'Courier New',monospace;}

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
.urgency-title{font-size:1.4rem;font-weight:900;color:#fff;margin-bottom:0.6rem;letter-spacing:-0.5px;}
.urgency-sub{font-size:0.9rem;color:#bbb;line-height:1.8;}
.urgency-sub strong{color:#f59e0b;}

/* ── FINAL CTA ── */
.final-cta-section{border-top:1px solid var(--border);text-align:center;background:radial-gradient(ellipse 80% 100% at 50% 100%, rgba(212,168,54,0.07) 0%, transparent 70%);}
.final-cta-section h2{font-size:clamp(1.8rem,4vw,3rem);font-weight:900;letter-spacing:-1px;color:#fff;margin-bottom:1rem;}
.final-cta-section h2 span{color:var(--gold);}
.final-cta-section .sub{font-size:1rem;color:#888;max-width:480px;margin:0 auto 2.5rem;line-height:1.8;}
.final-cta-pills{display:flex;flex-wrap:wrap;justify-content:center;gap:0.5rem;margin-top:1.2rem;}
.final-cta-pill{font-size:0.72rem;color:#aaa;background:rgba(255,255,255,0.04);border:1px solid var(--border);border-radius:20px;padding:5px 12px;display:inline-flex;align-items:center;gap:5px;}
.final-cta-meta{margin-top:1rem;font-size:0.75rem;color:#555;line-height:2;}

/* ── RESPONSIVE ── */
@media(max-width:1000px){
  .hero-split{flex-direction:column;gap:2rem;}
  .hero-visual{flex:none;width:100%;max-width:520px;margin:0 auto;}
  .hero-text{text-align:center;}
  .hero-sub{margin:0 auto 2rem;}
  .hero-ctas{justify-content:center;}
  .hero-trust{text-align:center;}
}
@media(min-width:1001px) and (max-width:1360px){
  .hero h1{
    font-size:clamp(2.9rem,4.5vw,4.2rem);
    line-height:1.08;
    letter-spacing:-1.5px;
    max-width:13ch;
  }
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
}

/* ── BOOK SELECTOR MODAL ────────────────────────────────── */
.modal-book-btn{position:relative;background:#1a1a1a;border:1.5px solid #252525;border-radius:8px;padding:1.1rem 0.9rem 0.9rem;text-align:left;cursor:pointer;transition:border-color 0.18s,box-shadow 0.18s,transform 0.15s;overflow:hidden;width:100%;outline:none;}
.modal-book-btn:hover,.modal-book-btn:focus-visible{border-color:var(--gold);box-shadow:0 0 0 1px rgba(212,168,54,0.2),0 8px 22px rgba(0,0,0,0.5);transform:translateY(-2px);}
.mbb-accent{position:absolute;top:0;left:0;right:0;height:3px;background:var(--mbb-color,var(--gold));}
.mbb-emoji{font-size:1.5rem;margin-bottom:0.5rem;display:block;line-height:1;}
.mbb-title{font-size:0.8rem;font-weight:700;color:#fff;line-height:1.3;margin-bottom:0.25rem;}
.mbb-meta{font-family:'Courier New',monospace;font-size:0.57rem;letter-spacing:1.5px;text-transform:uppercase;color:#666;margin-bottom:0.35rem;}
.mbb-price{font-family:'Courier New',monospace;font-size:0.78rem;font-weight:700;color:var(--gold);}
.modal-bonus-card{position:relative;background:rgba(212,168,54,0.04);border:1.5px solid rgba(212,168,54,0.2);border-radius:8px;padding:1.1rem 0.9rem 0.9rem;text-align:left;overflow:hidden;}
.mbb-free-badge{display:inline-flex;align-items:center;gap:4px;background:rgba(212,168,54,0.12);border:1px solid rgba(212,168,54,0.3);border-radius:20px;padding:3px 10px;margin-top:0.45rem;}
.mbb-free-badge span{font-size:0.6rem;font-family:'Courier New',monospace;letter-spacing:1px;color:var(--gold);font-weight:700;}
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

  <!-- Split: Text (left) + Visual grid (right) -->
  <div class="hero-split">

    <!-- LEFT: Copy -->
    <div class="hero-text">
      <div class="hero-badge">🔥 1,100 Templates · Loved by 200+ Buyers · First Result in Minutes</div>
      <h1>Turn "I Don't Know<br>What to Prompt"<br>into <span>Images You're Proud to Post</span></h1>
      <p class="hero-sub">Riya used to open Midjourney, type random words, and close it frustrated. Then she filled 6 blanks from one template, posted the result, and got 146 likes with real DMs asking how she made it. That's the shift these books are built for.</p>
      <div class="hero-ctas">
        <a href="#pricing" class="btn-primary">Start Now — ₹199 →</a>
        <a href="#books" class="btn-secondary">See What's Inside</a>
      </div>
      <div class="hero-proof-strip">
        <span class="hero-proof-pill">No prompt-writing required</span>
        <span class="hero-proof-pill">Works with ChatGPT, Midjourney, Firefly</span>
        <span class="hero-proof-pill">Most buyers get first usable result in first session</span>
      </div>
      <div style="font-size:0.76rem;color:#777;margin-top:-0.2rem;margin-bottom:0.9rem;">
        First 10 minutes: Pick a style → fill a few blanks → generate your image.
      </div>
      <div class="hero-trust">
        🔒 One-time payment &nbsp;·&nbsp; Instant access &nbsp;·&nbsp; No subscription<br>
        <span>Works with ChatGPT · Midjourney · Firefly · Ideogram · DALL·E &amp; more</span><br>
        <span style="color:var(--gold);opacity:0.8;">⭐ Based on buyer feedback from 200+ paying customers</span>
      </div>
      <div class="hero-mini-testimonial">
        <strong>"I stopped guessing prompts."</strong> I picked a template, filled details, and finally got an image I was happy to post the same evening.
      </div>
      <div class="live-activity" id="liveActivity">
        <span class="live-pulse-dot"></span>
        <span id="liveActivityText">Meera from Bengaluru just bought the bundle · 2 min ago</span>
      </div>
    </div>

    <!-- RIGHT: Style preview grid (real AI-generated images) -->
    <div class="hero-visual">
      <div class="style-preview-grid">

        <div class="sp-card">
          <div class="sp-tag" style="background:#ec4899;color:#fff;">HOT</div>
          <img src="/assets/style-ghibli.jpg" alt="Ghibli & Anime style AI art" loading="eager">
          <div class="sp-overlay">
            <div class="sp-name">Ghibli &amp; Anime</div>
            <div class="sp-count">100 PROMPTS</div>
          </div>
        </div>

        <div class="sp-card">
          <div class="sp-tag" style="background:#d4a836;color:#000;">VIRAL</div>
          <img src="/assets/style-action-figure.jpg" alt="Action Figure toy box AI art" loading="eager">
          <div class="sp-overlay">
            <div class="sp-name">Action Figure</div>
            <div class="sp-count">100 PROMPTS</div>
          </div>
        </div>

        <div class="sp-card">
          <div class="sp-tag" style="background:#e11d48;color:#fff;">🔥</div>
          <img src="/assets/style-movie-poster.jpg" alt="Cinematic Movie Poster AI art" loading="lazy" style="object-position:top;">
          <div class="sp-overlay">
            <div class="sp-name">Movie Poster</div>
            <div class="sp-count">100 PROMPTS</div>
          </div>
        </div>

        <div class="sp-card">
          <img src="/assets/style-pet.jpg" alt="Pet Transformation AI art" loading="lazy">
          <div class="sp-overlay">
            <div class="sp-name">Pet Art</div>
            <div class="sp-count">100 PROMPTS</div>
          </div>
        </div>

        <div class="sp-card">
          <img src="/assets/style-headshot.jpg" alt="Professional Headshot AI art" loading="lazy" style="object-position:top;">
          <div class="sp-overlay">
            <div class="sp-name">Headshots</div>
            <div class="sp-count">100 PROMPTS</div>
          </div>
        </div>

        <div class="sp-card">
          <img src="/assets/style-historical.jpg" alt="Historical Time Travel AI art" loading="lazy">
          <div class="sp-overlay" style="background:linear-gradient(0deg,rgba(0,0,0,0.95) 0%,rgba(0,0,0,0.4) 100%);">
            <div class="sp-name" style="color:var(--gold);font-size:1rem;font-weight:900;">+6 More</div>
            <div class="sp-count">600 PROMPTS</div>
          </div>
        </div>

      </div>
    </div>

  </div><!-- /hero-split -->

  <!-- Stats bar -->
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

</section>

<!-- PAIN AGITATION -->
<section class="pain-section">
  <div class="container">
    <div class="section-badge">If This Feels Familiar</div>
    <h2 class="section-title">You're Not Bad at AI.<br>You're Missing a Repeatable Prompt System.</h2>
    <div class="pain-text">
      <p class="story">You open Midjourney. You stare at the blank prompt box. You type something. You hit enter.</p>
      <p class="story">And you get… <strong>fine</strong>. Not bad. Just not the result you had in your head.</p>
      <p class="story">Meanwhile, someone on Instagram just posted a Ghibli-style portrait that looks genuinely polished. A small Etsy shop is quietly selling AI movie poster prints. Your colleague turned a childhood photo into a 90s action figure and everyone in your group asked how they did it.</p>
      <p class="story">You <strong>know</strong> AI can do this. You're paying for the tool. You've watched the tutorials. But nobody gives you a reliable prompt structure you can reuse when you need results fast.</p>
      <ul class="pain-bullets">
        <li><span class="x">✗</span>You've spent hours tweaking prompts and still get mediocre results</li>
        <li><span class="x">✗</span>You've seen the trending styles and had no idea how to recreate them</li>
        <li><span class="x">✗</span>You feel that blank-box paralysis — <em>where do I even start?</em></li>
        <li><span class="x">✗</span>You're paying for AI tools every month but not getting professional output</li>
        <li><span class="x">✗</span>You've copied prompts from Reddit and they never seem to work right</li>
      </ul>
      <p class="pain-punch">"The gap between their results and yours isn't talent or luck. It's having reusable prompt patterns instead of starting from scratch each time."</p>
    </div>
  </div>
</section>

<!-- SOLUTION BRIDGE -->
<section class="solution-section">
  <div class="container">
    <div class="section-badge">The Shift</div>
    <p class="big-intro">Instead of guessing every prompt from zero,<br><span>start from proven templates and personalize in minutes.</span></p>
    <p class="solution-desc">You get 1,100 fill-in-the-blank templates covering real use cases: portraits, posters, products, pets, and more. Fill in a few details. Copy. Paste. Generate. It's the fastest path from idea to a result you're happy to share.</p>
    <a href="#pricing" class="btn-primary" style="display:inline-block;">Yes — I Want the Templates →</a>
  </div>
</section>

<!-- WHY THIS WORKS -->
<section style="border-top:1px solid var(--border);background:var(--surface);">
  <div class="container" style="text-align:center;">
    <div class="section-badge">Why Most People Finally Get Results</div>
    <h2 class="section-title">The 3-Part Shift From Random Outputs<br>to Consistent Images</h2>
    <div class="how-grid" style="margin-top:2.2rem;">
      <div class="how-step">
        <div class="how-step-num">1</div>
        <h3>What people try</h3>
        <p>Typing from scratch every time. New prompt, new guess, new disappointment.</p>
      </div>
      <div class="how-step">
        <div class="how-step-num">2</div>
        <h3>Why it fails</h3>
        <p>No reusable structure. You tweak random words instead of a proven framework.</p>
      </div>
      <div class="how-step">
        <div class="how-step-num">3</div>
        <h3>What changes here</h3>
        <p>Start from tested templates, personalize in minutes, and produce repeatable results.</p>
      </div>
    </div>
  </div>
</section>

<!-- GALLERY — Results collage -->
<section class="gallery-section">
  <div class="container" style="text-align:center;">
    <div class="section-badge">Real Results</div>
    <h2 class="section-title" style="font-size:2rem;">Every One of These Was Made<br>in Under 2 Minutes.</h2>
    <p class="section-sub" style="margin:0 auto 0;">Real images. Real prompts from these books. Swap in your name — get the same result. Tonight.</p>
    <div class="gallery-img">
      <img src="/assets/hero-collage.jpg" alt="AI-generated images made with prompt templates — Ghibli, action figure, movie poster, pet art, headshot, vintage" loading="lazy">
    </div>
    <div class="gallery-styles">
      <span class="gallery-style-tag">🌸 Ghibli &amp; Anime</span>
      <span class="gallery-style-tag">🧸 Action Figure</span>
      <span class="gallery-style-tag">📷 Childhood Nostalgia</span>
      <span class="gallery-style-tag">🎬 Movie Poster</span>
      <span class="gallery-style-tag">💼 Headshots</span>
      <span class="gallery-style-tag">🐾 Pet Art</span>
      <span class="gallery-style-tag">📜 Vintage Scrapbook</span>
      <span class="gallery-style-tag">✨ Trending Styles</span>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials-section" id="reviews">
  <div class="container">
    <div class="section-badge" style="text-align:center;">Real Buyers · Real Results</div>
    <h2 class="section-title" style="text-align:center;">Real Buyers. Real Use Cases.<br><span style="color:var(--gold);">No Hype.</span></h2>

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
        <p class="testimonial-text">"I create Reels about AI tools. Used these prompts for a Midjourney demo Reel and gained 43 followers that week — better than my usual growth. Other creators asked which prompts I used. I just said I bought a prompt bundle. At ₹199 for all 11, it felt like good value."</p>
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
        <div class="spb-num">₹18</div>
        <div class="spb-label">Per Book · Lifetime</div>
      </div>
    </div>
  </div>
</section>

<!-- BENEFITS -->
<section class="benefits-section">
  <div class="container" style="text-align:center;">
    <div class="section-badge">What You Actually Get</div>
    <h2 class="section-title">What Riya, Prashant, Vikram<br>and Other Buyers Got.</h2>
    <p class="section-sub" style="margin:0 auto 0.5rem;">Not just templates — a complete system. Here's exactly why it works when random prompts from Reddit don't.</p>
    <div class="benefits-grid">
      <div class="benefit-item">
        <div class="benefit-icon">⚡</div>
        <div class="benefit-title">Save 10–50 Hours</div>
        <div class="benefit-desc">Every template is pre-engineered for maximum quality output. Skip all the trial-and-error — get results from your first paste.</div>
        <div class="benefit-stat">AVG. FIRST SHARE-WORTHY IMAGE: 47 SEC</div>
      </div>
      <div class="benefit-item">
        <div class="benefit-icon">🎨</div>
        <div class="benefit-title">Look Like a Pro Artist</div>
        <div class="benefit-desc">Even if you've never written a prompt before. If you can fill out a form, you can use this. Zero technical skill needed.</div>
      </div>
      <div class="benefit-item">
        <div class="benefit-icon">📱</div>
        <div class="benefit-title">Works on Your Phone</div>
        <div class="benefit-desc">No app, no download, no installation. Open your browser on any device — all 11 books are instantly available in your dashboard.</div>
      </div>
      <div class="benefit-item">
        <div class="benefit-icon">💸</div>
        <div class="benefit-title">Monetize When You're Ready</div>
        <div class="benefit-desc">Use these for Etsy prints, client content, or your own social brand. Start with personal projects, then turn winning styles into paid work.</div>
        <div class="benefit-stat">VIKRAM: APPROX. ₹1,150 IN FIRST 2 WEEKS ON ETSY</div>
      </div>
      <div class="benefit-item">
        <div class="benefit-icon">🔁</div>
        <div class="benefit-title">Endlessly Reusable</div>
        <div class="benefit-desc">Change one variable — name, style, setting — and get a completely different image every time. 1,100 templates become unlimited outputs.</div>
        <div class="benefit-stat">RIYA: 146 LIKES FROM ONE TEMPLATE POST</div>
      </div>
      <div class="benefit-item">
        <div class="benefit-icon">🌐</div>
        <div class="benefit-title">Works Everywhere</div>
        <div class="benefit-desc">Midjourney, ChatGPT, DALL·E 3, Ideogram, Stable Diffusion, Adobe Firefly. Wherever you paste a text prompt, these work.</div>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section id="how">
  <div class="container" style="text-align:center;">
    <div class="section-badge">How It Works</div>
    <h2 class="section-title">3 Steps to Your Perfect AI Image</h2>
    <div class="how-grid">
      <div class="how-step">
        <div class="how-step-num">1</div>
        <h3>Pick Your Book</h3>
        <p>Choose from 11 categories — action figures, headshots, pet portraits, movie posters, and more.</p>
        <p style="margin-top:0.6rem;font-size:0.78rem;color:var(--gold);font-style:italic;">Takes 30 seconds. You'll know exactly which one the moment you see it.</p>
      </div>
      <div class="how-step">
        <div class="how-step-num">2</div>
        <h3>Fill 6 Blanks</h3>
        <p>Type your name, look, style, and setting. Every one of the 100 prompts in your book updates instantly.</p>
        <p style="margin-top:0.6rem;font-size:0.78rem;color:var(--gold);font-style:italic;">No writing. No guessing. Just fill a form — the prompts write themselves.</p>
      </div>
      <div class="how-step">
        <div class="how-step-num">3</div>
        <h3>Paste & See It</h3>
        <p>Copy any prompt. Paste into ChatGPT, Midjourney, Firefly, or DALL·E. Your image appears in seconds.</p>
        <p style="margin-top:0.6rem;font-size:0.78rem;color:var(--gold);font-style:italic;">That reaction — "wait, I made this?" — happens on the first try.</p>
      </div>
    </div>
    <a href="#pricing" class="btn-primary" style="display:inline-block;margin-top:2.5rem;">Start in the Next 60 Seconds →</a>
  </div>
</section>

<!-- BOOKS -->
<section id="books" style="background:var(--surface);border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
  <div class="container" style="text-align:center;">
    <div class="section-badge">The Collection</div>
    <h2 class="section-title">11 Books · 1,100 Prompts</h2>
    <p class="section-sub" style="margin:0 auto;">Every prompt has 6 personal variable slots. Fill once — get 100 personalised prompts. <span style="color:var(--gold);">+&nbsp;1&nbsp;free bonus guide included with every purchase.</span></p>

    <div class="books-grid" id="booksGrid">
      <?php foreach ($books as $id => $book):
        $isBonus = !empty($book['bonus']);
      ?>
      <div class="book-card" style="--card-accent:<?= htmlspecialchars($book['accent']) ?>">
        <div class="book-num"><?= $isBonus ? '🎁 FREE BONUS' : 'BOOK ' . str_pad($id, 2, '0', STR_PAD_LEFT) ?></div>
        <div class="book-emoji"><?= $book['emoji'] ?></div>
        <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
        <div class="book-prompts"><?= $isBonus ? 'CHEAT CODE GUIDE · FREE' : '100 PROMPTS · 6 VARIABLES' ?></div>
        <?php if (!$isBonus): ?>
        <div class="book-price-tag" data-single-inr="₹99" data-single-usd="$2.99">₹99</div>
        <?php else: ?>
        <div class="book-price-tag" style="background:<?= htmlspecialchars($book['accent']) ?>;">FREE</div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- OFFER STACK -->
<section class="offer-section">
  <div class="container" style="text-align:center;">
    <div class="section-badge">Everything You're Getting</div>
    <h2 class="section-title">₹199 Unlocks All of This.</h2>
    <p class="section-sub" style="margin:0 auto;">Every book. Every bonus. Every future update. Here's the full list — and what it would cost if bought separately.</p>
    <div class="offer-list">
      <div class="offer-item"><span class="chk">✦</span><span class="name">🧸 Book 1 — Action Figure &amp; Toy Box (100 prompts)</span><span class="val offer-inr">₹99</span><span class="val offer-usd" style="display:none;">$2.99</span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">🌸 Book 2 — Ghibli &amp; Anime Style (100 prompts)</span><span class="val offer-inr">₹99</span><span class="val offer-usd" style="display:none;">$2.99</span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">📷 Book 3 — Childhood Nostalgia (100 prompts)</span><span class="val offer-inr">₹99</span><span class="val offer-usd" style="display:none;">$2.99</span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">🎨 Book 4 — Caricature &amp; Chibi (100 prompts)</span><span class="val offer-inr">₹99</span><span class="val offer-usd" style="display:none;">$2.99</span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">💼 Book 5 — Professional Headshots (100 prompts)</span><span class="val offer-inr">₹99</span><span class="val offer-usd" style="display:none;">$2.99</span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">📦 Book 6 — Product Photography (100 prompts)</span><span class="val offer-inr">₹99</span><span class="val offer-usd" style="display:none;">$2.99</span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">🎬 Book 7 — Cinematic Movie Poster (100 prompts)</span><span class="val offer-inr">₹99</span><span class="val offer-usd" style="display:none;">$2.99</span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">📜 Book 8 — Vintage Scrapbook (100 prompts)</span><span class="val offer-inr">₹99</span><span class="val offer-usd" style="display:none;">$2.99</span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">🐾 Book 9 — Pet Transformation (100 prompts)</span><span class="val offer-inr">₹99</span><span class="val offer-usd" style="display:none;">$2.99</span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">🕰️ Book 10 — Historical Time Travel (100 prompts)</span><span class="val offer-inr">₹99</span><span class="val offer-usd" style="display:none;">$2.99</span></div>
      <div class="offer-item"><span class="chk">✦</span><span class="name">✨ Book 11 — Bonus Trending Styles (100 prompts)</span><span class="val offer-inr">₹99</span><span class="val offer-usd" style="display:none;">$2.99</span></div>
      <div class="offer-bonus"><span class="chk">🎁</span><span class="name">🎯 The AI Image Cheat Code — Style guide + practice prompts (bonus for ALL buyers)</span><span class="val">FREE</span></div>
      <div class="offer-bonus"><span class="chk">🎁</span><span class="name">Private WhatsApp Community Access (new prompts, trending drops)</span><span class="val">FREE</span></div>
      <div class="offer-total-row">
        <span class="offer-total-label">If bought separately</span>
        <div class="offer-total-price">
          <span class="offer-total-orig" id="offerOrig">₹1,089</span>
          <span class="offer-total-today" id="offerToday">₹199</span>
        </div>
      </div>
    </div>
    <p style="margin-top:1rem;font-size:0.8rem;color:#555;" id="offerSavingLine">Save ₹890 on the bundle — plus 2 free bonuses included. Limited-time launch price.</p>
    <a href="#pricing" class="btn-primary" style="display:inline-block;margin-top:1.5rem;">Claim the Bundle →</a>
  </div>
</section>

<!-- PRICING -->
<section id="pricing" class="pricing-section">
  <div class="container" style="text-align:center;">
    <div class="section-badge">Pricing</div>
    <h2 class="section-title">One Price. No Subscription.<br><span style="color:var(--gold);">Use It For Years.</span></h2>
    <p class="section-sub" style="margin:0 auto 1.5rem;">No subscription. No renewal. No price creep. Pay once — own it for life, including every new book added to the collection.</p>

    <!-- Currency detector -->
    <div class="currency-bar" id="currencyBar">
      <span class="flag" id="currencyFlag">🇮🇳</span>
      <span class="detected" id="currencyLabel">Showing prices in Indian Rupees (₹)</span>
      <button class="currency-switch" id="currencySwitch">Switch to $</button>
    </div>

    <div class="pricing-grid">
      <!-- Single Book -->
      <div class="price-card">
        <div class="price-plan">Single Book</div>
        <div class="price-amount">
          <span class="currency" id="singleCurrency">₹</span><span id="singlePrice">99</span>
        </div>
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
          Start with One Book →
        </button>
        <div class="price-risk-reversal">Good if you want to test one style first before committing.</div>
      </div>

      <!-- Full Bundle -->
      <div class="price-card popular">
        <div class="price-badge">BEST VALUE</div>
        <div class="price-plan">Full Bundle</div>
        <div class="price-amount">
          <span class="currency" id="bundleCurrency">₹</span><span id="bundlePrice">199</span>
          <span class="original" id="bundleOriginal">₹1,089</span>
        </div>
        <div class="price-billing" id="bundleSavings">Save ₹890 · All 11 books + bonus guide · Lifetime access</div>
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
          Start Now — ₹199 →
        </button>
        <div class="price-risk-reversal">Best for serious use: all styles now, plus future books included.</div>
      </div>
    </div>

    <p style="margin-top:1.5rem;font-size:0.78rem;color:#555;" id="pricingAnchor">💡 At ₹199, that's just ₹18 per book — less than a cup of coffee for 100 prompts that change how you use AI.</p>
    <p style="margin-top:0.5rem;font-size:0.75rem;color:#444;">🔒 Secure checkout via Razorpay (India) · PayPal (International)</p>
    <p style="margin-top:0.5rem;font-size:0.75rem;color:#666;">✅ 24-hour technical guarantee: if your access/login link does not work, we'll fix it fast or refund you. No refunds after successful access.</p>
    <p style="margin-top:0.8rem;font-size:0.78rem;color:var(--gold);">👥 Join 200+ creators already using these prompts →</p>
  </div>
</section>

<!-- GUARANTEE -->
<section class="guarantee-section">
  <div class="container">
    <div class="guarantee-inner">
      <div class="guarantee-badge" style="width:72px;height:72px;background:rgba(212,168,54,0.1);border:2px solid rgba(212,168,54,0.4);border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;gap:1px;">
        <span style="font-size:1.5rem;line-height:1;">✓</span>
        <span style="font-family:'Courier New',monospace;font-size:0.45rem;letter-spacing:1.5px;color:var(--gold);text-transform:uppercase;">Guaranteed</span>
      </div>
      <div class="guarantee-body">
    <h3>Clear 24-Hour Access Guarantee.</h3>
        <p>This is a digital product with instant delivery. So we've kept the policy simple and transparent: you're protected if access fails, and fully supported once you're in.</p>
        <ul class="guarantee-points">
          <li><strong style="color:#fff;">If your access/login link fails within 24 hours</strong> — we'll resolve it quickly or issue a full refund.</li>
          <li><strong style="color:#fff;">After access is delivered successfully</strong> — refunds are not available (digital content policy).</li>
          <li><strong style="color:#fff;">Need help using the prompts?</strong> — we'll still support you directly with troubleshooting and usage guidance.</li>
        </ul>
        <div style="margin-top:1.2rem;padding:1rem 1.2rem;background:rgba(212,168,54,0.06);border:1px solid rgba(212,168,54,0.25);border-radius:4px;font-size:0.82rem;color:var(--gold);">
          🏆 &nbsp;200+ buyers &nbsp;·&nbsp; 4.6★ average rating &nbsp;·&nbsp; Most users create their first polished result in the first session
        </div>
        <a href="#pricing" class="btn-primary" style="display:inline-block;margin-top:1.5rem;">Start Now — 24h Access Guarantee →</a>
      </div>
    </div>
  </div>
</section>

<!-- URGENCY -->
<div class="urgency-section">
  <div class="urgency-inner">
    <div class="urgency-live-badge"><span class="urgency-live-dot"></span>OFFER ENDING SOON</div>
    <div class="urgency-icon">⚡</div>
    <div class="urgency-title">Launch pricing ends when this timer hits zero.</div>
    <!-- Countdown timer -->
    <div class="countdown-row" id="countdownRow">
      <div class="cd-block"><div class="cd-num" id="cdH">47</div><div class="cd-label">Hours</div></div>
      <div class="cd-sep">:</div>
      <div class="cd-block"><div class="cd-num" id="cdM">59</div><div class="cd-label">Min</div></div>
      <div class="cd-sep">:</div>
      <div class="cd-block"><div class="cd-num" id="cdS">59</div><div class="cd-label">Sec</div></div>
    </div>
    <div class="urgency-sub">The styles in these books — <strong>Ghibli, action figures, cinematic posters</strong> — are trending right now. Every day, more creators use these templates to move from random outputs to consistent results.<br><br>If you've been stuck in prompt trial-and-error, this is your shortcut to a first win today.<br><br><strong>200+ creators already started. You can start in minutes.</strong></div>
    <a href="#pricing" class="btn-primary" style="display:inline-block;margin-top:1.5rem;">Start Now — ₹199 →</a>
  </div>
</div>

<!-- COMMUNITY -->
<section class="community-section" id="community">
  <div class="container">
    <div class="community-inner">
      <div class="community-text">
        <div class="section-badge">Included with Every Purchase</div>
        <h2 class="section-title" style="font-size:2.2rem;">Your AI Creator<br>Inner Circle</h2>
        <p style="color:#888;margin-bottom:1.5rem;">Buying the prompts is just the start. Every purchase includes access to our private WhatsApp community — where the real value keeps compounding after day one.</p>
        <ul class="community-perks">
          <li><span class="perk-icon">📢</span>First access to new prompt books before public release</li>
          <li><span class="perk-icon">🔥</span>Trending AI style alerts — know what's going viral before everyone else</li>
          <li><span class="perk-icon">🎁</span>Free bonus prompts dropped exclusively in the group</li>
          <li><span class="perk-icon">🎨</span>Share your AI images and get feedback from fellow creators</li>
          <li><span class="perk-icon">🤝</span>Direct line to the creator — questions, tips, and personal support</li>
        </ul>
      </div>
      <div class="community-visual">
        <div class="wa-icon" style="font-size:3.5rem;display:flex;align-items:center;justify-content:center;gap:0.5rem;margin-bottom:0.8rem;">
          <span style="background:rgba(212,168,54,0.12);border:1px solid rgba(212,168,54,0.3);border-radius:50%;width:64px;height:64px;display:inline-flex;align-items:center;justify-content:center;font-size:2rem;">📲</span>
        </div>
        <div class="wa-count">PRIVATE · BUYERS ONLY</div>
        <div class="wa-title">AI Prompt Creators</div>
        <div class="wa-sub">Exclusive WhatsApp group for buyers.<br>Invite link sent after purchase.</div>
        <a href="#pricing" class="btn-wa">
          Join the Community →
        </a>
      </div>
    </div>
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
        ['I\'m new to AI. Will this still work for me?',
         'Yes — and honestly, complete beginners get the best results because they haven\'t built up bad habits yet. Prashant had never used Midjourney before he bought this. He made his son\'s action figure toy box the same evening, posted it Saturday morning. Three relatives asked where he bought it — he made it himself in 10 minutes. You don\'t need to understand anything about AI. If you can fill in a form, you can do this.'],
        ['Which AI tools does this work with?',
         'All the major ones — Midjourney, ChatGPT (DALL·E 3), Ideogram, Stable Diffusion, Adobe Firefly, and more. Wherever you paste a text prompt, these templates work.'],
        ['Is this a PDF I download? Do I install anything?',
         'No download, no app, no installation. You get a secure login to a website you open in any browser — on your phone, tablet, or desktop. It\'s always there whenever you need it.'],
        ['Can I use these prompts to create and sell art?',
         'Yes. The AI images you generate using these prompts are 100% yours — use them for personal projects, Etsy prints, client content, social media, YouTube thumbnails, whatever you like.'],
        ['What if I only want one specific book?',
         'You can buy any single book for ₹99 / $2.99. But the full bundle is ₹199 / $9 — you get all 11 for the price of 2, so most people grab the bundle.'],
        ['Are new books added to the bundle automatically?',
         'Yes. Bundle buyers automatically get every new book we publish — forever, at no extra charge. Your investment only grows.'],
        ['How is payment processed? Is it safe?',
         'India: Razorpay (UPI, PhonePe, Paytm, all cards) — used by Zomato, Swiggy, and millions of others. International: PayPal. Both are 100% secure and trusted globally.'],
        ['What is your refund policy?',
         'Because this is instant-access digital content, refunds are not available after successful access is delivered. If your access/login link fails, contact us within 24 hours — we will fix it quickly or refund you.'],
        ['What\'s in the WhatsApp community?',
         'It\'s where buyers get first access to new prompt drops, trending style alerts, tips, and direct support from the creator. Think of it as your AI creator inner circle.'],
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
    <h2>You're Minutes Away From<br><span>Your First "I Made This" AI Image</span></h2>
    <p class="sub">₹199 one-time. No subscriptions. Open your library, pick a style, fill in a few blanks, and generate something you'd actually share. No prompt-writing stress required.</p>
    <a href="#pricing" class="btn-primary" style="display:inline-block;font-size:1rem;padding:16px 40px;">
      Start Now — ₹199 →
    </a>
    <div class="whats-next">
      <div class="wn-step"><span class="wn-n">1</span>Click the button</div>
      <div class="wn-step"><span class="wn-n">2</span>Checkout in 30 sec</div>
      <div class="wn-step"><span class="wn-n">3</span>Login link hits your inbox</div>
      <div class="wn-step"><span class="wn-n">4</span>Create your first share-worthy image</div>
    </div>
    <div class="final-cta-pills" style="margin-top:1.2rem;">
      <span class="final-cta-pill">✦ 1,100 Prompt Templates</span>
      <span class="final-cta-pill">✦ 11 Style Books</span>
      <span class="final-cta-pill">🎁 Free Bonus Guide</span>
      <span class="final-cta-pill">⚡ Instant Access</span>
      <span class="final-cta-pill">📱 Any Device</span>
      <span class="final-cta-pill">🔒 Secure Checkout</span>
    </div>
    <div style="margin-top:1rem;font-size:0.78rem;color:var(--gold);">👥 200+ creators already inside · 4.6★ buyer-rated</div>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-logo">AI Prompt Books</div>
  <div class="footer-links">
    <a href="#books">Books</a>
    <a href="#pricing">Pricing</a>
    <a href="#community">Community</a>
    <a href="/login.php">Login</a>
  </div>
  <div class="footer-copy">© <?= date('Y') ?> AI Prompt Books. All rights reserved.</div>
</footer>

<!-- EXIT INTENT POPUP -->
<div class="exit-overlay" id="exitOverlay">
  <div class="exit-popup">
    <button type="button" class="exit-popup-close" data-action="dismiss-exit">×</button>
    <div class="exit-popup-badge">⚡ WAIT — BEFORE YOU GO</div>
    <h3>The price goes up when the timer hits zero.</h3>
    <p>Drop your email and we'll notify you before the price increases — and send you a free sample prompt from Book 2: Ghibli &amp; Anime.</p>
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
      <strong>Full Bundle — All 11 Books</strong>
      <span id="stickyPrice">₹199 · One-time payment</span>
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
        <div style="font-family:'Courier New',monospace;font-size:0.6rem;letter-spacing:2.5px;text-transform:uppercase;color:var(--gold);opacity:0.75;margin-bottom:0.3rem;">Step 1 of 2 · Select Style</div>
        <div style="font-size:1.2rem;font-weight:800;color:#fff;letter-spacing:-0.3px;">Pick Your Prompt Book</div>
      </div>
      <button type="button" class="modal-close-btn" data-action="close-modal">×</button>
    </div>
    <p style="font-size:0.78rem;color:#555;margin-bottom:1.2rem;padding-bottom:1rem;border-bottom:1px solid #1e1e1e;">100 prompts per book · 6 variable slots · <span style="color:var(--gold);">🎁 Cheat Code guide free with every purchase</span></p>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem;" id="modalBookGrid">
      <?php foreach ($books as $id => $book): if (!empty($book['bonus'])) continue; ?>
      <button type="button" data-action="proceed-checkout" data-plan="single" data-book-id="<?= $id ?>"
              class="modal-book-btn"
              style="--mbb-color:<?= htmlspecialchars($book['accent']) ?>;">
        <div class="mbb-accent"></div>
        <span class="mbb-emoji"><?= $book['emoji'] ?></span>
        <div class="mbb-title"><?= htmlspecialchars($book['title']) ?></div>
        <div class="mbb-meta">100 PROMPTS</div>
        <div class="mbb-price" id="modalPrice<?= $id ?>">₹99</div>
      </button>
      <?php endforeach; ?>
      <!-- Bonus card — included free with any purchase -->
      <div class="modal-bonus-card">
        <div class="mbb-accent" style="background:linear-gradient(90deg,#d4a836,#f59e0b);"></div>
        <span class="mbb-emoji">🎯</span>
        <div class="mbb-title">The AI Image Cheat Code</div>
        <div class="mbb-meta">STYLE GUIDE</div>
        <div class="mbb-free-badge"><span>🎁 FREE BONUS</span></div>
      </div>
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
      <input type="text" id="buyerName" placeholder="e.g. Raj Sharma" style="width:100%;background:#0a0a0a;border:1.5px solid #333;color:#fff;padding:10px 14px;border-radius:3px;font-size:0.88rem;outline:none;" onfocus="this.style.borderColor='#d4a836'" onblur="this.style.borderColor='#333'">
    </div>
    <div style="margin-bottom:1.5rem;">
      <label style="font-family:'Courier New',monospace;font-size:0.72rem;letter-spacing:1px;color:#888;display:block;margin-bottom:6px;">EMAIL ADDRESS</label>
      <input type="email" id="buyerEmail" placeholder="you@example.com" style="width:100%;background:#0a0a0a;border:1.5px solid #333;color:#fff;padding:10px 14px;border-radius:3px;font-size:0.88rem;outline:none;" onfocus="this.style.borderColor='#d4a836'" onblur="this.style.borderColor='#333'">
      <div style="font-size:0.75rem;color:#555;margin-top:4px;">Your login credentials will be sent to this email right after payment.</div>
    </div>
    <div id="paymentButtons" style="display:flex;flex-direction:column;gap:0.8rem;">
      <!-- Razorpay button (India) -->
      <button id="razorpayBtn" type="button" class="pay-btn-razorpay" data-action="pay-razorpay">
        Pay with UPI / Card (Razorpay)
      </button>
      <!-- PayPal button container -->
      <div id="paypalButtonContainer" style="display:none;"></div>
      <div style="text-align:center;font-size:0.72rem;color:#555;padding:4px 0;" id="paymentSecure">🔒 Secure · One-time payment · No subscription</div>
      <div style="text-align:center;font-size:0.7rem;color:#666;line-height:1.5;padding:2px 0 0;">
        24-hour technical guarantee for failed access/login link. No refunds after successful access.
      </div>
    </div>
    <div id="checkoutError" style="display:none;background:#3a1010;border:1px solid #7a2020;border-radius:3px;padding:10px 14px;font-size:0.82rem;color:#f87171;margin-top:1rem;"></div>
  </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID ?>&currency=USD&intent=capture" data-sdk-integration-source="button-factory"></script>
<script>
// ── Currency detection ────────────────────────────────────────────────────────
let currency = 'INR';

(function detectCurrency() {
  try {
    const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    if (!tz.startsWith('Asia/C') && tz !== 'Asia/Kolkata') {
      currency = 'USD';
    }
  } catch(e) {}
  // Check stored preference
  const stored = localStorage.getItem('aipb_currency');
  if (stored) currency = stored;
  updatePricing();
})();

function updatePricing() {
  const isIndia = (currency === 'INR');
  // Nav/hero prices
  document.querySelectorAll('[data-single-inr]').forEach(el => {
    el.textContent = isIndia ? el.dataset.singleInr : el.dataset.singleUsd;
  });
  // Modal book prices
  <?php foreach ($books as $id => $book): ?>
  const mp<?= $id ?> = document.getElementById('modalPrice<?= $id ?>');
  if (mp<?= $id ?>) mp<?= $id ?>.textContent = isIndia ? '₹99' : '$2.99';
  <?php endforeach; ?>
  // Pricing section
  document.getElementById('singleCurrency').textContent = isIndia ? '₹' : '$';
  document.getElementById('singlePrice').textContent    = isIndia ? '99' : '2.99';
  document.getElementById('bundleCurrency').textContent = isIndia ? '₹' : '$';
  document.getElementById('bundlePrice').textContent    = isIndia ? '199' : '9';
  document.getElementById('bundleOriginal').textContent = isIndia ? '₹1,089' : '$32.89';
  document.getElementById('bundleSavings').textContent  = isIndia
    ? 'Save ₹890 · All 11 books + bonus guide · Lifetime access'
    : 'Save $23.89 · All 11 books + bonus guide · Lifetime access';
  // Currency bar
  document.getElementById('currencyFlag').textContent    = isIndia ? '🇮🇳' : '🌍';
  document.getElementById('currencyLabel').textContent   = isIndia
    ? 'Showing prices in Indian Rupees (₹)'
    : 'Showing prices in US Dollars ($)';
  document.getElementById('currencySwitch').textContent = isIndia ? 'Switch to $' : 'Switch to ₹';
  // Offer stack
  const offerOrig = document.getElementById('offerOrig');
  const offerToday = document.getElementById('offerToday');
  const offerLine = document.getElementById('offerSavingLine');
  const pricingAnchor = document.getElementById('pricingAnchor');
  if (offerOrig) offerOrig.textContent  = isIndia ? '₹1,089' : '$32.89';
  if (offerToday) offerToday.textContent = isIndia ? '₹199'   : '$9';
  if (offerLine) offerLine.textContent   = isIndia
    ? 'Save ₹890 on the bundle — plus 2 free bonuses included. Limited-time launch price.'
    : 'Save $23.89 on the bundle — plus 2 free bonuses included. Limited-time launch price.';
  // Per-item currency labels in offer stack
  document.querySelectorAll('.offer-inr').forEach(el => el.style.display = isIndia ? '' : 'none');
  document.querySelectorAll('.offer-usd').forEach(el => el.style.display = isIndia ? 'none' : '');
  if (pricingAnchor) pricingAnchor.textContent = isIndia
    ? '💡 At ₹199, that\'s just ₹18 per book — less than a cup of coffee for 100 prompts that change how you use AI.'
    : '💡 At $9, that\'s less than 1 month of Netflix for a library that pays for itself the first time you use it.';
}

document.getElementById('currencySwitch').addEventListener('click', () => {
  currency = (currency === 'INR') ? 'USD' : 'INR';
  localStorage.setItem('aipb_currency', currency);
  updatePricing();
});

// ── Checkout flow ─────────────────────────────────────────────────────────────
let currentPlan   = null;
let currentBookId = null;
let paypalButtons = null;

function startCheckout(plan) {
  if (plan === 'single') {
    document.getElementById('bookModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
  } else {
    proceedCheckout('bundle', null);
  }
}

function closeModal() {
  document.getElementById('bookModal').style.display = 'none';
  document.body.style.overflow = '';
}

function proceedCheckout(plan, bookId) {
  closeModal();
  currentPlan   = plan;
  currentBookId = bookId;

  const isIndia = (currency === 'INR');
  const price   = plan === 'bundle'
    ? (isIndia ? '₹199' : '$9.00')
    : (isIndia ? '₹99'  : '$2.99');

  // Summary text
  const bookName = bookId
    ? <?= json_encode(array_map(fn($b) => $b['title'], $books)) ?>[bookId]
    : 'All 11 Books + Bonus Guide (Full Bundle)';
  document.getElementById('checkoutSummary').innerHTML =
    `<strong style="color:#fff">${plan === 'bundle' ? '📦 Full Bundle' : '📖 Single Book'}</strong><br>
     <span style="color:#888">${bookName}</span><br>
     <span style="color:#d4a836;font-weight:700;font-size:1.1rem;font-family:'Courier New',monospace;">${price}</span>`;

  // Show correct payment button
  document.getElementById('razorpayBtn').style.display      = isIndia ? 'block' : 'none';
  document.getElementById('paypalButtonContainer').style.display = isIndia ? 'none' : 'block';

  if (!isIndia && !paypalButtons) {
    renderPayPalButton();
  }

  document.getElementById('checkoutModal').style.display = 'block';
  document.body.style.overflow = 'hidden';
}

function closeCheckout() {
  document.getElementById('checkoutModal').style.display = 'none';
  document.body.style.overflow = '';
  document.getElementById('checkoutError').style.display = 'none';
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
}

// ── Razorpay ──────────────────────────────────────────────────────────────────
async function payWithRazorpay() {
  const data = getCheckoutData();
  if (!data) return;

  document.getElementById('razorpayBtn').textContent = 'Creating order…';
  document.getElementById('razorpayBtn').disabled    = true;

  try {
    const res = await fetch('/api/razorpay-order.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({
        plan:    currentPlan,
        book_id: currentBookId,
        email:   data.email,
        name:    data.name
      })
    });
    const order = await res.json();
    if (!order.id) { showError(order.error || 'Failed to create order. Please try again.'); return; }

    const options = {
      key:         '<?= RAZORPAY_KEY_ID ?>',
      amount:      order.amount,
      currency:    'INR',
      name:        '<?= SITE_NAME ?>',
      description: currentPlan === 'bundle' ? 'Full Bundle — All 11 Books + Bonus Guide' : 'Single Book Access',
      order_id:    order.id,
      prefill:     { name: data.name, email: data.email },
      theme:       { color: '#d4a836' },
      handler: async function(response) {
        await verifyRazorpay(response, data);
      },
      modal: {
        ondismiss: function() {
          document.getElementById('razorpayBtn').textContent = 'Pay with UPI / Card (Razorpay)';
          document.getElementById('razorpayBtn').disabled    = false;
        }
      }
    };
    new Razorpay(options).open();
  } catch(e) {
    showError('Network error. Please try again.');
  } finally {
    document.getElementById('razorpayBtn').textContent = 'Pay with UPI / Card (Razorpay)';
    document.getElementById('razorpayBtn').disabled    = false;
  }
}

async function verifyRazorpay(response, data) {
  const res = await fetch('/api/razorpay-verify.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({ ...response, email: data.email, name: data.name, plan: currentPlan, book_id: currentBookId })
  });
  const result = await res.json();
  if (result.token) {
    window.location.href = '/setup-account.php?token=' + result.token;
  } else {
    showError(result.error || 'Payment verification failed. Please contact support.');
  }
}

// ── PayPal ────────────────────────────────────────────────────────────────────
function renderPayPalButton() {
  paypalButtons = paypal.Buttons({
    createOrder: async function() {
      const data = getCheckoutData();
      if (!data) throw new Error('Validation failed');
      const res = await fetch('/api/paypal-order.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ plan: currentPlan, book_id: currentBookId, email: data.email, name: data.name })
      });
      const order = await res.json();
      if (!order.id) throw new Error(order.error || 'Failed to create order');
      return order.id;
    },
    onApprove: async function(data) {
      const buyer = getCheckoutData();
      const res = await fetch('/api/paypal-capture.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ order_id: data.orderID, email: buyer?.email, name: buyer?.name, plan: currentPlan, book_id: currentBookId })
      });
      const result = await res.json();
      if (result.token) {
        window.location.href = '/setup-account.php?token=' + result.token;
      } else {
        showError(result.error || 'Payment failed. Please contact support.');
      }
    },
    onError: function(err) {
      showError('PayPal error. Please try again or contact support.');
    }
  });
  paypalButtons.render('#paypalButtonContainer');
}

// ── FAQ toggles ───────────────────────────────────────────────────────────────
document.querySelectorAll('.faq-q').forEach(q => {
  q.addEventListener('click', () => {
    q.closest('.faq-item').classList.toggle('open');
  });
});

// ── Exit Intent ──────────────────────────────────────────────────────────────
(function initExitIntent() {
  if (localStorage.getItem('aipb_exit_seen')) return;
  let triggered = false;
  // Desktop: mouse leaves viewport upward
  document.addEventListener('mouseleave', (e) => {
    if (e.clientY < 30 && !triggered) {
      triggered = true;
      localStorage.setItem('aipb_exit_seen', '1');
      document.getElementById('exitOverlay').classList.add('show');
    }
  });
  // Mobile: scroll back up fast after 30s
  let lastScroll = 0, lastTime = 0;
  window.addEventListener('scroll', () => {
    const now = Date.now(), sy = window.scrollY;
    const velocity = (lastScroll - sy) / Math.max(1, now - lastTime);
    if (velocity > 2 && sy > 800 && !triggered && now - lastTime < 100) {
      triggered = true;
      localStorage.setItem('aipb_exit_seen', '1');
      document.getElementById('exitOverlay').classList.add('show');
    }
    lastScroll = sy; lastTime = now;
  }, { passive: true });
})();

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
  if (action === 'start-checkout') {
    startCheckout(actionElement.dataset.plan || 'bundle');
    return;
  }
  if (action === 'dismiss-exit') {
    dismissExit();
    return;
  }
  if (action === 'submit-exit-email') {
    submitExitEmail();
    return;
  }
  if (action === 'close-modal') {
    closeModal();
    return;
  }
  if (action === 'proceed-checkout') {
    const plan = actionElement.dataset.plan || 'single';
    const bookId = parseInt(actionElement.dataset.bookId || '0', 10);
    proceedCheckout(plan, Number.isNaN(bookId) ? null : bookId);
    return;
  }
  if (action === 'close-checkout') {
    closeCheckout();
    return;
  }
  if (action === 'pay-razorpay') {
    payWithRazorpay();
  }
});

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
    if (sp) sp.textContent = (currency === 'INR') ? '₹199 · One-time payment' : '$9 · One-time payment';
  }
  // Hook into currency switch
  const origUpdate = window.updatePricing;
  window.updatePricing = function() { origUpdate(); updateStickyPrice(); };

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
