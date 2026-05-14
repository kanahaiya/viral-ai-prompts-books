<?php
require_once __DIR__ . '/auth.php';
requireLogin();

$user    = getCurrentUser();
$books   = getBooks();
$welcome = isset($_GET['welcome']);
$isBundle = $user['plan'] === 'bundle';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Books — AI Prompt Books</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Segoe UI',Arial,sans-serif;background:#0a0a0a;color:#e8e4de;line-height:1.7;min-height:100vh;}

/* NAV */
.nav{background:#111;border-bottom:1px solid #1a1a1a;padding:0 2rem;}
.nav-inner{max-width:1100px;margin:0 auto;height:56px;display:flex;align-items:center;justify-content:space-between;}
.nav-logo{font-family:'Courier New',monospace;font-size:0.75rem;letter-spacing:3px;text-transform:uppercase;color:#d4a836;}
.nav-logo a{color:inherit;text-decoration:none;}
.nav-user{display:flex;align-items:center;gap:1rem;}
.nav-name{font-size:0.82rem;color:#888;}
.nav-logout{font-family:'Courier New',monospace;font-size:0.7rem;letter-spacing:1px;text-transform:uppercase;color:#555;border:1px solid #2a2a2a;padding:5px 12px;border-radius:2px;text-decoration:none;transition:all 0.15s;}
.nav-logout:hover{color:#ddd;border-color:#555;}

/* MAIN */
.main{max-width:1100px;margin:0 auto;padding:2.5rem 2rem;}

/* WELCOME BANNER */
.welcome-banner{background:linear-gradient(135deg,#1a1505 0%,#141414 100%);border:1px solid #3a2a05;border-radius:6px;padding:1.5rem 2rem;margin-bottom:2rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;}
.welcome-text h2{font-size:1.2rem;font-weight:900;color:#fff;margin-bottom:0.3rem;}
.welcome-text p{font-size:0.85rem;color:#888;}
.wa-btn{background:#25d366;color:#fff;font-family:'Courier New',monospace;font-size:0.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:10px 20px;border:none;border-radius:3px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:background 0.15s;white-space:nowrap;}
.wa-btn:hover{background:#1eb857;}

/* PLAN BADGE */
.plan-bar{display:flex;align-items:center;gap:1rem;margin-bottom:2rem;flex-wrap:wrap;}
.plan-badge{font-family:'Courier New',monospace;font-size:0.7rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:5px 14px;border-radius:2px;}
.plan-badge.bundle{background:#d4a836;color:#000;}
.plan-badge.single{background:#2a2a2a;color:#d4a836;border:1px solid #3a3a2a;}
.plan-label{font-size:0.82rem;color:#888;}
.upgrade-link{font-family:'Courier New',monospace;font-size:0.7rem;letter-spacing:1px;color:#d4a836;text-decoration:none;border:1px solid rgba(212,168,54,0.3);padding:5px 12px;border-radius:2px;transition:all 0.15s;margin-left:auto;}
.upgrade-link:hover{background:rgba(212,168,54,0.08);}

/* SECTION HEADER */
.section-head{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:1.5rem;padding-bottom:0.8rem;border-bottom:1px solid #1a1a1a;}
.section-head h2{font-size:1.1rem;font-weight:700;color:#fff;}
.section-count{font-family:'Courier New',monospace;font-size:0.7rem;color:#555;letter-spacing:1px;}

/* BOOKS GRID */
.books-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.2rem;}
.book-card{background:#141414;border:1px solid #2a2a2a;border-radius:6px;overflow:hidden;position:relative;transition:transform 0.15s,border-color 0.15s;}
.book-card.accessible{cursor:pointer;}
.book-card.accessible:hover{transform:translateY(-2px);border-color:var(--accent);}
.book-card.locked{opacity:0.5;cursor:default;}
.book-accent-bar{height:3px;background:var(--accent);}
.book-body{padding:1.3rem 1.1rem;}
.book-num{font-family:'Courier New',monospace;font-size:0.62rem;letter-spacing:2px;color:#555;margin-bottom:0.5rem;}
.book-emoji{font-size:1.6rem;margin-bottom:0.6rem;}
.book-title{font-size:0.9rem;font-weight:700;color:#fff;line-height:1.3;margin-bottom:0.8rem;}
.book-status{font-family:'Courier New',monospace;font-size:0.68rem;letter-spacing:1px;}
.book-status.open{color:var(--accent);}
.book-status.locked-lbl{color:#444;}
.book-actions{display:flex;align-items:center;justify-content:space-between;gap:0.8rem;margin-top:0.75rem;}
.book-download-link{font-family:'Courier New',monospace;font-size:0.62rem;letter-spacing:1px;color:#888;text-decoration:none;border:1px solid #2a2a2a;border-radius:2px;padding:3px 8px;transition:all 0.15s;}
.book-download-link:hover{color:#d4a836;border-color:#d4a836;}
.book-lock-icon{position:absolute;top:12px;right:12px;font-size:0.9rem;color:#333;}
.book-card.accessible .book-lock-icon{display:none;}

/* COMMUNITY BOX */
.community-box{background:#0d1a0d;border:1px solid #1a3a1a;border-radius:6px;padding:1.8rem 2rem;display:flex;align-items:center;gap:2rem;flex-wrap:wrap;margin-top:2.5rem;}
.community-icon{font-size:2.5rem;flex-shrink:0;}
.community-content h3{font-size:1rem;font-weight:700;color:#fff;margin-bottom:0.3rem;}
.community-content p{font-size:0.83rem;color:#888;}
.community-content a.wa-btn{margin-top:1rem;display:inline-flex;}

@media(max-width:600px){
  .main{padding:1.5rem 1rem;}
  .welcome-banner{flex-direction:column;align-items:flex-start;}
  .community-box{flex-direction:column;}
}
</style>
</head>
<body>

<!-- NAV -->
<nav class="nav">
  <div class="nav-inner">
    <div class="nav-logo"><a href="/dashboard.php">AI Prompt Books</a></div>
    <div class="nav-user">
      <span class="nav-name">👋 <?= htmlspecialchars($user['name'] ?? $user['email']) ?></span>
      <a href="/logout.php" class="nav-logout">Logout</a>
    </div>
  </div>
</nav>

<main class="main">

  <!-- WELCOME BANNER (shown once after purchase) -->
  <?php if ($welcome): ?>
  <div class="welcome-banner">
    <div class="welcome-text">
      <h2>🎉 Welcome! Your account is ready.</h2>
      <p>Click any book below to start using your prompts. Join the WhatsApp community for tips and early releases.</p>
    </div>
    <a href="<?= htmlspecialchars(WHATSAPP_INVITE_LINK) ?>" target="_blank" rel="noopener" class="wa-btn">
      💬 Join WhatsApp Community
    </a>
  </div>
  <?php endif; ?>

  <!-- PLAN BADGE -->
  <?php
    $allBooks   = getBooks();
    $paidBooks  = array_filter($allBooks, fn($b) => empty($b['bonus']));
    $totalPaid  = count($paidBooks);
    $ownedBooks = $isBundle ? array_keys($paidBooks) : json_decode($user['books_access'] ?? '[]', true);
    $ownedCount = count($ownedBooks);
  ?>
  <div class="plan-bar">
    <?php if ($isBundle): ?>
      <span class="plan-badge bundle">BUNDLE — ALL <?= $totalPaid ?> BOOKS + BONUS</span>
      <span class="plan-label">Full access · All future books included</span>
    <?php else: ?>
      <span class="plan-badge single"><?= $ownedCount ?> BOOK<?= $ownedCount > 1 ? 'S' : '' ?></span>
      <span class="plan-label">Individual access · Bonus guide included free</span>
      <a href="/#pricing" class="upgrade-link">Upgrade to Bundle →</a>
    <?php endif; ?>
  </div>

  <!-- BOOKS GRID -->
  <div class="section-head">
    <h2>Your Prompt Books</h2>
    <span class="section-count">
      <?= $isBundle ? $totalPaid . ' / ' . $totalPaid . ' BOOKS' : $ownedCount . ' / ' . $totalPaid . ' BOOKS' ?> UNLOCKED
    </span>
  </div>

  <div class="books-grid">
    <?php foreach ($allBooks as $id => $book):
      $hasAccess  = userHasBookAccess($user, $id);
      $isBonus    = !empty($book['bonus']);
      $statusText = $isBonus
        ? ($book['label'] ?? 'BONUS GUIDE') . ' → OPEN'
        : ($hasAccess ? '100 PROMPTS → OPEN' : '🔒 LOCKED');
    ?>
    <div class="book-card <?= $hasAccess ? 'accessible' : 'locked' ?>"
         style="--accent:<?= htmlspecialchars($book['accent']) ?>"
         <?= $hasAccess ? "onclick=\"window.location='/book.php?id={$id}'\"" : '' ?>>
      <div class="book-accent-bar"></div>
      <div class="book-body">
        <div class="book-num"><?= $isBonus ? '🎁 FREE BONUS' : 'BOOK ' . str_pad($id, 2, '0', STR_PAD_LEFT) ?></div>
        <div class="book-emoji"><?= $book['emoji'] ?></div>
        <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
        <div class="book-actions">
          <div class="book-status <?= $hasAccess ? 'open' : 'locked-lbl' ?>">
            <?= $statusText ?>
          </div>
          <?php if ($hasAccess): ?>
            <a class="book-download-link" href="/book.php?id=<?= $id ?>&download=1">Download</a>
          <?php endif; ?>
        </div>
      </div>
      <?php if (!$hasAccess): ?>
        <div class="book-lock-icon">🔒</div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- COMMUNITY BOX -->
  <div class="community-box">
    <div class="community-icon">💬</div>
    <div class="community-content">
      <h3>AI Prompt Creators — WhatsApp Community</h3>
      <p>Share your AI images, get prompt tips, early access to new books, and exclusive free prompts.</p>
      <a href="<?= htmlspecialchars(WHATSAPP_INVITE_LINK) ?>" target="_blank" rel="noopener" class="wa-btn">
        Join the Community
      </a>
    </div>
  </div>

</main>
<script>
document.querySelectorAll('.book-download-link').forEach((downloadLinkElement) => {
  downloadLinkElement.addEventListener('click', (event) => {
    // Prevent parent card click handler from opening the viewer page.
    event.stopPropagation();
  });
});
</script>
</body>
</html>
