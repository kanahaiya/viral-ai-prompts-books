window.addEventListener('DOMContentLoaded', () => {
const currency = 'INR';
const BUNDLE_PRICE_INR = 299;
const RAZORPAY_CHECKOUT_SRC = 'https://checkout.razorpay.com/v1/checkout.js';
const CASHFREE_CHECKOUT_SRC = 'https://sdk.cashfree.com/js/v3/cashfree.js';
let razorpayLoaderPromise = null;
let cashfreeLoaderPromise = null;
const SCROLL_DEPTH_MILESTONES = [25, 50, 75, 90];
const ENGAGEMENT_TIME_CHECKPOINTS_SECONDS = [15, 30, 60, 120];
let hasTrackedCheckoutFormStart = false;

function trackEvent(eventName, params = {}, options = null) {
  if (typeof window.pixelTrack === 'function') {
    window.pixelTrack(eventName, params, options);
    return;
  }
  if (typeof window.fbq !== 'function') return;
  if (options) {
    window.fbq('track', eventName, params, options);
  } else {
    window.fbq('track', eventName, params);
  }
}

function trackCustomEvent(eventName, params = {}) {
  if (typeof window.pixelTrackCustom === 'function') {
    window.pixelTrackCustom(eventName, params);
    return;
  }
  if (typeof window.fbq !== 'function') return;
  window.fbq('trackCustom', eventName, params);
}

function getSessionAttributionParams() {
  const queryParams = new URLSearchParams(window.location.search);
  const readParam = (key) => queryParams.get(key) || '';
  return {
    landing_path: window.location.pathname,
    landing_referrer: document.referrer || 'direct',
    utm_source: readParam('utm_source'),
    utm_medium: readParam('utm_medium'),
    utm_campaign: readParam('utm_campaign'),
    utm_term: readParam('utm_term'),
    utm_content: readParam('utm_content')
  };
}

function initSessionAttributionTracking() {
  trackCustomEvent('LandingSessionStarted', getSessionAttributionParams());
}

function initScrollDepthTracking() {
  const firedMilestones = new Set();

  function evaluateScrollDepth() {
    const doc = document.documentElement;
    const maxScrollable = Math.max(1, doc.scrollHeight - window.innerHeight);
    const percent = Math.min(100, Math.round((window.scrollY / maxScrollable) * 100));

    SCROLL_DEPTH_MILESTONES.forEach((milestone) => {
      if (percent >= milestone && !firedMilestones.has(milestone)) {
        firedMilestones.add(milestone);
        trackCustomEvent('ScrollDepthReached', { depth_percent: milestone });
      }
    });
  }

  window.addEventListener('scroll', evaluateScrollDepth, { passive: true });
  evaluateScrollDepth();
}

function initTimeOnPageTracking() {
  ENGAGEMENT_TIME_CHECKPOINTS_SECONDS.forEach((seconds) => {
    window.setTimeout(() => {
      trackCustomEvent('TimeOnPageCheckpoint', { seconds });
    }, seconds * 1000);
  });
}

function initCheckoutFormIntentTracking() {
  const nameElement = document.getElementById('buyerName');
  const emailElement = document.getElementById('buyerEmail');
  if (!nameElement && !emailElement) return;

  function trackFormStart(fieldName) {
    if (hasTrackedCheckoutFormStart) return;
    hasTrackedCheckoutFormStart = true;
    trackCustomEvent('CheckoutFormStarted', { first_field: fieldName });
  }

  if (nameElement) {
    nameElement.addEventListener('focus', () => trackFormStart('name'), { once: true });
  }
  if (emailElement) {
    emailElement.addEventListener('focus', () => trackFormStart('email'), { once: true });
  }
}

function getCheckoutMetrics() {
  return { value: BUNDLE_PRICE_INR, numItems: 11, contentName: 'Full System' };
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

function getActivePaymentProvider() {
  return (window.__AIPB_CONFIG && window.__AIPB_CONFIG.paymentProvider) || 'razorpay';
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

function ensureCashfreeLoaded() {
  if (typeof window.Cashfree === 'function') {
    return Promise.resolve();
  }
  if (cashfreeLoaderPromise) {
    return cashfreeLoaderPromise;
  }

  cashfreeLoaderPromise = new Promise((resolve, reject) => {
    const scriptElement = document.createElement('script');
    scriptElement.src = CASHFREE_CHECKOUT_SRC;
    scriptElement.async = true;
    scriptElement.onload = () => resolve();
    scriptElement.onerror = () => reject(new Error('Cashfree SDK failed to load.'));
    document.head.appendChild(scriptElement);
  });

  return cashfreeLoaderPromise;
}

let currentPlan = null;
let currentBookId = null;
let currentBookIds = [];
const FULL_SYSTEM_BOOK_COUNT = 11;
const MAX_CHECKOUT_PREVIEW_THUMBNAILS = 3;
const CHECKOUT_PREVIEW_BOOK_IDS_FOR_BUNDLE = [2, 5, 6];
let currentCheckoutAmountInr = BUNDLE_PRICE_INR;
const CHECKOUT_BOOK_PREVIEW_MAP = {
  1: { image: '/assets/checkout/action-figures-thumb.webp', alt: 'Action figure style image example', label: 'Action Figure' },
  2: { image: '/assets/checkout/ghibli-art-thumb.webp', alt: 'Ghibli anime style image example', label: 'Ghibli & Anime Style' },
  3: { image: '/assets/checkout/childhood-nostalgia-thumb.webp', alt: 'Childhood nostalgia style image example', label: 'Childhood Nostalgia' },
  4: { image: '/assets/checkout/caricature-chibi-thumb.webp', alt: 'Caricature and chibi style image example', label: 'Caricature & Chibi' },
  5: { image: '/assets/checkout/professional-headshots-thumb.webp', alt: 'Professional headshot style image example', label: 'Professional Headshots' },
  6: { image: '/assets/checkout/product-photography-thumb.webp', alt: 'Product photography style image example', label: 'Product Photography' },
  7: { image: '/assets/checkout/cinematic-movie-poster-thumb.webp', alt: 'Cinematic movie poster style image example', label: 'Cinematic Movie Poster' },
  8: { image: '/assets/checkout/vintage-scrapbook-thumb.webp', alt: 'Vintage scrapbook style image example', label: 'Vintage Scrapbook' },
  9: { image: '/assets/checkout/pet-transformation-thumb.webp', alt: 'Pet transformation style image example', label: 'Pet Transformation' },
  10: { image: '/assets/checkout/historical-time-travel-thumb.webp', alt: 'Historical portrait style image example', label: 'Historical Time Travel' },
  11: { image: '/assets/checkout/trending-styles-thumb.webp', alt: 'Trending styles image example', label: 'Trending Styles' }
};

function updateCheckoutCtaLabel(amountInr) {
  const razorpayButtonElement = document.getElementById('razorpayBtn');
  if (!razorpayButtonElement) return;
  razorpayButtonElement.textContent = `Unlock Instant Access — ₹${amountInr}`;
}

function getBookTitleById(bookId) {
  const booksById = (window.__AIPB_CONFIG && window.__AIPB_CONFIG.booksById) || {};
  const rawTitle = booksById[bookId] || '';
  return rawTitle.replace(/^Bonus\s+/i, '');
}

function getBookPreviewById(bookId) {
  if (CHECKOUT_BOOK_PREVIEW_MAP[bookId]) {
    return CHECKOUT_BOOK_PREVIEW_MAP[bookId];
  }
  return {
    image: '/assets/checkout/ghibli-art-thumb.webp',
    alt: 'AI style image example',
    label: getBookTitleById(bookId) || 'AI Prompt Book'
  };
}

function updateCheckoutTitleAndSubtitle(plan, selectedCount) {
  const titleElement = document.getElementById('checkoutTitle');
  const subtitleElement = document.getElementById('checkoutSubtitle');
  if (!titleElement || !subtitleElement) return;

  if (plan === 'bundle') {
    titleElement.innerHTML = 'Unlock Your Full<br><span class="checkout-title-accent">AI Prompt System</span>';
    subtitleElement.textContent = 'Get instant access to all 11 interactive prompt books + bonus AI guide.';
    return;
  }

  titleElement.innerHTML = 'Unlock Your Custom<br><span class="checkout-title-accent">AI Prompt System</span>';
  subtitleElement.textContent = `Get instant access to your ${selectedCount} selected interactive prompt book${selectedCount > 1 ? 's' : ''} + bonus AI guide.`;
}

function renderSelectedBooksPreviewLines(selectedBookNames, overflowLabelTemplate = '+{count} more selected {bookWord}', maxVisibleCount = 3) {
  const booksListElement = document.getElementById('checkoutSummaryBooksList');
  const overflowElement = document.getElementById('checkoutSummaryOverflow');
  if (!booksListElement || !overflowElement) return;

  const selectedCount = selectedBookNames.length;
  const visibleBookNames = selectedCount <= maxVisibleCount ? selectedBookNames : selectedBookNames.slice(0, maxVisibleCount);
  booksListElement.innerHTML = '';

  visibleBookNames.forEach((bookName) => {
    const listItemElement = document.createElement('li');
    listItemElement.textContent = bookName;
    booksListElement.appendChild(listItemElement);
  });

  if (selectedCount > visibleBookNames.length) {
    const hiddenCount = selectedCount - visibleBookNames.length;
    const bookWord = hiddenCount === 1 ? 'book' : 'books';
    overflowElement.textContent = overflowLabelTemplate
      .replace('{count}', String(hiddenCount))
      .replace('{bookWord}', bookWord);
    overflowElement.hidden = false;
  } else {
    overflowElement.hidden = true;
  }

  if (selectedCount === 0) {
    const fallbackItemElement = document.createElement('li');
    fallbackItemElement.textContent = 'Please select at least one book.';
    booksListElement.appendChild(fallbackItemElement);
    overflowElement.hidden = true;
  }
}

function renderBundlePreviewLines() {
  const booksById = (window.__AIPB_CONFIG && window.__AIPB_CONFIG.booksById) || {};
  const allBookNames = Object.keys(booksById)
    .map((bookId) => parseInt(bookId, 10))
    .filter((bookId) => !Number.isNaN(bookId) && bookId > 0 && bookId <= FULL_SYSTEM_BOOK_COUNT)
    .sort((leftId, rightId) => leftId - rightId)
    .map((bookId) => getBookTitleById(bookId))
    .filter(Boolean);

  // Show all 11 books for the Full System plan — there's only one plan now,
  // so the list should read as complete rather than a truncated preview.
  renderSelectedBooksPreviewLines(allBookNames, '+{count} More Prompt {bookWord}', FULL_SYSTEM_BOOK_COUNT);
}

function updateCheckoutPreviewTitle(plan) {
  const proofTitleElement = document.getElementById('checkoutProofTitle');
  if (!proofTitleElement) return;
  proofTitleElement.textContent = plan === 'bundle' ? '✨ What You Can Create ✨' : '✨ Selected Styles Preview ✨';
}

function renderSelectedBooksAccordion(selectedBookNames) {
  const toggleElement = document.getElementById('checkoutSelectedBooksToggle');
  const listElement = document.getElementById('checkoutSelectedBooksList');
  if (!toggleElement || !listElement) return;

  listElement.innerHTML = '';
  selectedBookNames.forEach((bookName) => {
    const listItemElement = document.createElement('li');
    listItemElement.textContent = bookName;
    listElement.appendChild(listItemElement);
  });

  if (selectedBookNames.length >= 4) {
    const defaultLabel = selectedBookNames.length <= 6 ? 'View All Selected Books' : 'View Selected Books';
    toggleElement.dataset.openLabel = defaultLabel;
    toggleElement.textContent = defaultLabel;
    toggleElement.hidden = false;
    toggleElement.setAttribute('aria-expanded', 'false');
    listElement.hidden = true;
    listElement.classList.remove('is-open');
    return;
  }

  toggleElement.hidden = true;
  toggleElement.setAttribute('aria-expanded', 'false');
  listElement.hidden = selectedBookNames.length === 0;
  listElement.classList.remove('is-open');
}

function renderCheckoutProofStrip(bookIds, totalSelectedCount = null) {
  const safeBookIds = Array.isArray(bookIds) ? bookIds : [];
  const uniqueBookIds = [...new Set(
    safeBookIds
      .map((bookId) => parseInt(String(bookId), 10))
      .filter((bookId) => !Number.isNaN(bookId) && bookId > 0)
  )];
  const validPreviewItems = uniqueBookIds
    .map((bookId) => ({ bookId, previewData: CHECKOUT_BOOK_PREVIEW_MAP[bookId] || null }))
    .filter((item) => item.previewData !== null && item.previewData.image)
    .filter((item) => typeof item.previewData.image === 'string' && item.previewData.image.trim().length > 0);
  const previewItems = validPreviewItems
    .slice(0, MAX_CHECKOUT_PREVIEW_THUMBNAILS);
  const effectiveTotalCount = Number.isInteger(totalSelectedCount) && totalSelectedCount > 0
    ? totalSelectedCount
    : validPreviewItems.length;
  const moreCount = Math.max(0, effectiveTotalCount - previewItems.length);
  const moreBadgeElement = document.getElementById('checkoutProofMoreBadge');
  const stripElement = document.querySelector('.checkout-proof-strip');

  function syncPreviewStripCount() {
    if (!stripElement) return;
    const visibleCount = Array.from({ length: MAX_CHECKOUT_PREVIEW_THUMBNAILS }).reduce((count, _, index) => {
      const itemElement = document.getElementById(`checkoutProofItem${index + 1}`);
      if (!itemElement) return count;
      return itemElement.style.display === 'none' ? count : count + 1;
    }, 0);
    stripElement.setAttribute('data-preview-count', String(visibleCount));
  }

  // Always hard reset all preview slots first to avoid stale/duplicate cards.
  for (let index = 0; index < MAX_CHECKOUT_PREVIEW_THUMBNAILS; index += 1) {
    const itemElement = document.getElementById(`checkoutProofItem${index + 1}`);
    const imageElement = document.getElementById(`checkoutProofImage${index + 1}`);
    const captionElement = document.getElementById(`checkoutProofCaption${index + 1}`);
    if (!itemElement || !imageElement || !captionElement) continue;
    itemElement.hidden = true;
    itemElement.style.display = 'none';
    imageElement.src = '';
    imageElement.alt = '';
    imageElement.onerror = null;
    captionElement.textContent = '';
  }

  for (let index = 0; index < previewItems.length; index += 1) {
    const itemElement = document.getElementById(`checkoutProofItem${index + 1}`);
    const imageElement = document.getElementById(`checkoutProofImage${index + 1}`);
    const captionElement = document.getElementById(`checkoutProofCaption${index + 1}`);
    const previewData = previewItems[index].previewData;
    if (!itemElement || !imageElement || !captionElement || !previewData) continue;
    itemElement.style.display = 'grid';
    itemElement.hidden = false;
    imageElement.onerror = function handlePreviewLoadError() {
      itemElement.hidden = true;
      itemElement.style.display = 'none';
      syncPreviewStripCount();
    };
    imageElement.src = previewData.image;
    imageElement.alt = previewData.alt;
    captionElement.textContent = previewData.label;
  }
  syncPreviewStripCount();

  if (moreBadgeElement) {
    if (moreCount > 0) {
      moreBadgeElement.textContent = `+${moreCount} More Style${moreCount > 1 ? 's' : ''}`;
      moreBadgeElement.hidden = false;
    } else {
      moreBadgeElement.hidden = true;
    }
  }
}

function updateCheckoutMicrocopy(plan) {
  const microcopyElement = document.getElementById('checkoutCtaMicrocopy');
  if (!microcopyElement) return;
  microcopyElement.textContent = plan === 'bundle'
    ? 'Instant access to your full AI prompt system after payment.'
    : 'Instant access to your selected AI prompt system after payment.';
}

// NOTE: The single-book selection flow (per-book modal, book picker, etc.)
// is currently disabled — the site only sells the Full System bundle.
// The underlying single-book plan is still fully supported end-to-end in
// api/razorpay-order.php, api/paypal-order.php, db.sql, and setup-account.php
// so existing single-book purchasers keep working. To re-enable a per-book
// purchase UI, restore the removed book-selection modal markup/JS from git
// history (see project history around this comment) and route
// startCheckout('single') back to it.
// Warm the browser's image cache for the checkout modal's fixed preview thumbnails and
// payment logos the moment a user shows intent to open checkout (hover/touchstart/focus on
// any "start-checkout" CTA), rather than preloading them for every visitor on page load.
// This keeps initial page load lean while still avoiding a visible pop-in delay for users
// who actually proceed to checkout, since hover-to-click is almost always >150ms.
let checkoutAssetsPrefetched = false;
function prefetchCheckoutAssets() {
  if (checkoutAssetsPrefetched) return;
  checkoutAssetsPrefetched = true;
  const assetPaths = [
    '/assets/checkout/ghibli-art-thumb.webp',
    '/assets/checkout/professional-headshots-thumb.webp',
    '/assets/checkout/product-photography-thumb.webp',
    '/assets/icons/payment-logo-upi.webp',
    '/assets/icons/payment-logo-gpay.webp',
    '/assets/icons/payment-logo-phonepe.webp',
    '/assets/icons/payment-logo-paytm.webp',
    '/assets/icons/payment-logo-visa.webp'
  ];
  assetPaths.forEach((path) => {
    const img = new Image();
    img.src = path;
  });
}
document.querySelectorAll('[data-action="start-checkout"]').forEach((element) => {
  element.addEventListener('mouseenter', prefetchCheckoutAssets, { once: true });
  element.addEventListener('touchstart', prefetchCheckoutAssets, { once: true, passive: true });
  element.addEventListener('focus', prefetchCheckoutAssets, { once: true });
});
// Belt-and-suspenders: also warm the cache once the page has fully loaded and the browser
// is idle, so checkout still opens instantly even for a user who taps the CTA immediately
// with no hover/focus dwell time (e.g. a fast mobile tap). Scheduling this on requestIdleCallback
// after 'load' means it only runs once everything render-critical is already done, so it never
// competes with or slows down initial page load performance (LCP/FCP/TTI are unaffected).
window.addEventListener('load', () => {
  const scheduleIdle = window.requestIdleCallback || ((cb) => setTimeout(cb, 2000));
  scheduleIdle(prefetchCheckoutAssets, { timeout: 5000 });
});

// Warm the payment gateway SDK (Razorpay/Cashfree, ~184KB) the same way, instead of forcing
// every visitor to download it on initial page load via <link rel="preload">. This calls the
// SAME ensureXLoaded() functions the actual payment flow uses — they cache their loading
// promise, so calling them early here and again inside payWithRazorpay()/payWithCashfree()
// is safe: it never double-loads the script and never blocks/delays the real payment call,
// it just means the SDK is often already loaded (or loading) by the time the user clicks Pay.
// If this warm-up never fires for any reason (idle callback unsupported, no hover, etc.), the
// payment flow works exactly as before since payWithRazorpay/payWithCashfree independently
// await their own ensureXLoaded() call regardless of prefetch state.
function prefetchPaymentSdk() {
  const provider = getActivePaymentProvider();
  if (provider === 'cashfree') {
    ensureCashfreeLoaded().catch(() => {});
  } else {
    ensureRazorpayLoaded().catch(() => {});
  }
}
document.querySelectorAll('[data-action="start-checkout"]').forEach((element) => {
  element.addEventListener('mouseenter', prefetchPaymentSdk, { once: true });
  element.addEventListener('touchstart', prefetchPaymentSdk, { once: true, passive: true });
  element.addEventListener('focus', prefetchPaymentSdk, { once: true });
});
window.addEventListener('load', () => {
  const scheduleIdle = window.requestIdleCallback || ((cb) => setTimeout(cb, 2000));
  scheduleIdle(prefetchPaymentSdk, { timeout: 5000 });
});

function startCheckout(plan) {
  trackCustomEvent('CheckoutStarted', { plan });
  prefetchCheckoutAssets();
  prefetchPaymentSdk();
  proceedCheckout('bundle', null);
}

function proceedCheckout(plan, bookId, bookIds = null) {
  currentPlan = plan;
  currentBookId = bookId;
  currentBookIds = Array.isArray(bookIds) ? bookIds : (bookId ? [bookId] : []);
  const selectedCount = currentBookIds.length;

  const totalInr = BUNDLE_PRICE_INR;
  const price = `₹${totalInr}`;

  const planLabel = 'Full System Access';
  const selectedBooksText = 'Includes:';
  const summaryPlanElement = document.getElementById('checkoutSummaryPlan');
  const summarySelectionElement = document.getElementById('checkoutSummarySelection');
  const summaryPriceElement = document.getElementById('checkoutSummaryPrice');
  if (summaryPlanElement) summaryPlanElement.textContent = planLabel;
  if (summarySelectionElement) summarySelectionElement.textContent = selectedBooksText;
  if (summaryPriceElement) summaryPriceElement.textContent = price;
  updateCheckoutTitleAndSubtitle(plan, selectedCount);
  updateCheckoutMicrocopy(plan);
  updateCheckoutPreviewTitle(plan);

  renderBundlePreviewLines();
  renderCheckoutProofStrip(CHECKOUT_PREVIEW_BOOK_IDS_FOR_BUNDLE, FULL_SYSTEM_BOOK_COUNT);

  document.getElementById('checkoutModal').style.display = 'block';
  document.body.style.overflow = 'hidden';
  currentCheckoutAmountInr = totalInr;
  updateCheckoutCtaLabel(totalInr);

  trackCheckoutEvent('InitiateCheckout', plan, 11, {
    selected_books: '',
    source: 'bundle-cta'
  });
  trackEvent('AddToCart', {
    currency,
    value: totalInr,
    num_items: 11,
    content_name: 'Full System',
    content_type: 'product_group'
  });
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

function normalizeIndianPhoneNumber(rawPhoneNumber) {
  const digitsOnly = String(rawPhoneNumber || '').replace(/\D/g, '');
  if (digitsOnly.length === 10) return digitsOnly;
  if (digitsOnly.length === 12 && digitsOnly.startsWith('91')) return digitsOnly.slice(2);
  return '';
}

function getCheckoutData() {
  const emailElement = document.getElementById('buyerEmail');

  if (!emailElement) {
    showError('Checkout form is updating. Please refresh and try again.');
    return null;
  }

  const email = emailElement.value.trim();
  if (!email || !email.includes('@')) { showError('Please enter a valid email address.'); return null; }

  const nameElement = document.getElementById('buyerName');
  const name = nameElement ? nameElement.value.trim() : '';

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

async function payWithCashfree() {
  const data = getCheckoutData();
  if (!data) return;

  trackCheckoutEvent('AddPaymentInfo', currentPlan || 'bundle', currentBookIds.length || (currentPlan === 'bundle' ? 11 : 1), {
    payment_gateway: 'cashfree'
  });
  trackCustomEvent('CashfreeOrderStarted', {
    plan: currentPlan || 'unknown',
    selected_count: currentBookIds.length
  });

  document.getElementById('cashfreeBtn').textContent = 'Creating order…';
  document.getElementById('cashfreeBtn').disabled = true;

  try {
    await ensureCashfreeLoaded();
    if (typeof window.Cashfree !== 'function') {
      showError('Payment window could not be initialized. Please refresh and try again.');
      return;
    }

    const res = await fetch('/api/cashfree-order.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({
        plan: currentPlan,
        book_id: currentBookId,
        book_ids: currentBookIds,
        email: data.email,
        name: data.name,
        phone: data.phone
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
    if (!res.ok || !order.order_id || !order.payment_session_id) {
      showError(order.error || 'Failed to create order. Please try again.');
      return;
    }

    const cashfree = window.Cashfree({
      mode: (window.__AIPB_CONFIG && window.__AIPB_CONFIG.cashfreeEnv) || 'sandbox'
    });
    const checkoutResult = await cashfree.checkout({
      paymentSessionId: order.payment_session_id,
      redirectTarget: '_modal'
    });

    if (checkoutResult && checkoutResult.error) {
      showError(checkoutResult.error.message || 'Payment was cancelled or failed.');
      return;
    }

    await verifyCashfreeOrder(order.order_id);
  } catch (e) {
    showError('Could not reach payment service. Check internet and try again.');
  } finally {
    document.getElementById('cashfreeBtn').textContent = 'Pay with UPI / Card (Cashfree)';
    document.getElementById('cashfreeBtn').disabled = false;
  }
}

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
  document.getElementById('razorpayBtn').disabled = true;

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
        plan: currentPlan,
        book_id: currentBookId,
        book_ids: currentBookIds,
        email: data.email,
        name: data.name
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
      key: (window.__AIPB_CONFIG && window.__AIPB_CONFIG.razorpayKeyId) || '',
      amount: order.amount,
      currency: 'INR',
      name: (window.__AIPB_CONFIG && window.__AIPB_CONFIG.siteName) || 'AI Prompt System',
      description: currentPlan === 'bundle' ? 'Full System Access' : `${currentBookIds.length} Book Access`,
      order_id: order.id,
      prefill: { name: data.name, email: data.email },
      theme: { color: '#d4a836' },
      handler: async function(response) {
        await verifyRazorpayOrder(response);
      },
      modal: {
        ondismiss: function() {
          trackCustomEvent('PaymentPopupDismissed', {
            plan: currentPlan || 'unknown',
            selected_count: currentBookIds.length
          });
        }
      }
    };

    const razorpayInstance = new window.Razorpay(options);
    trackCustomEvent('PaymentPopupOpened', {
      payment_gateway: 'razorpay',
      plan: currentPlan || 'unknown',
      selected_count: currentBookIds.length
    });
    razorpayInstance.on('payment.failed', function() {
      showError('Payment was cancelled or failed.');
    });
    razorpayInstance.open();
  } catch (e) {
    showError('Could not reach payment service. Check internet and try again.');
  } finally {
    updateCheckoutCtaLabel(currentCheckoutAmountInr);
    document.getElementById('razorpayBtn').disabled = false;
  }
}

async function verifyRazorpayOrder(paymentResponse) {
  const res = await fetch('/api/razorpay-verify.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(paymentResponse)
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
    }, {
      eventID: paymentResponse.razorpay_payment_id
    });
    setTimeout(() => {
      window.location.href = '/setup-account.php?token=' + result.token;
    }, 800);
  } else {
    showError(result.error || 'Payment verification failed. Please contact support.');
  }
}

async function verifyCashfreeOrder(orderId) {
  const res = await fetch('/api/cashfree-verify.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({ order_id: orderId })
  });
  const result = await res.json();
  if (result.token) {
    const metrics = getCheckoutMetrics(currentPlan || 'bundle', currentBookIds.length || (currentPlan === 'bundle' ? 11 : 1));
    trackEvent('Purchase', {
      currency,
      value: metrics.value,
      content_name: metrics.contentName,
      num_items: metrics.numItems,
      payment_method: 'cashfree'
    }, {
      eventID: orderId
    });
    trackCustomEvent('PaymentVerified', {
      plan: currentPlan || 'unknown',
      selected_count: currentBookIds.length,
      order_id: orderId
    });
    setTimeout(() => {
      window.location.href = '/setup-account.php?token=' + result.token;
    }, 800);
  } else {
    trackCustomEvent('PaymentVerificationFailed', {
      plan: currentPlan || 'unknown',
      selected_count: currentBookIds.length
    });
    showError(result.error || 'Payment verification failed. Please contact support.');
  }
}

document.querySelectorAll('.faq-q').forEach((q) => {
  q.addEventListener('click', () => {
    const itemElement = q.closest('.faq-item');
    if (!itemElement) return;
    const isOpen = itemElement.classList.toggle('open');
    q.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    trackCustomEvent('FaqToggled', {
      question: (q.textContent || '').trim().slice(0, 80),
      expanded: isOpen
    });
  });
});

function dismissExit() {
  document.getElementById('exitOverlay').classList.remove('show');
}

function submitExitEmail() {
  const email = document.getElementById('exitEmail').value.trim();
  if (!email || !email.includes('@')) {
    document.getElementById('exitEmail').style.borderColor = '#f87171';
    return;
  }
  localStorage.setItem('aipb_waitlist_email', email);
  const popup = document.querySelector('.exit-popup');
  popup.innerHTML = '<div style="text-align:center;padding:1rem 0;"><div style="font-size:2rem;margin-bottom:1rem;">✅</div><h3 style="color:#fff;margin-bottom:0.5rem;">You are on the list!</h3><p style="color:#888;font-size:0.85rem;">Check your inbox — your free Ghibli sample prompt is on its way.</p><button type="button" class="exit-popup-dismiss" data-action="dismiss-exit" style="display:block;margin:1rem auto 0;">Close this →</button></div>';
  setTimeout(() => dismissExit(), 4000);
}

document.addEventListener('click', (event) => {
  const actionElement = event.target.closest('[data-action]');
  if (!actionElement) return;

  const action = actionElement.dataset.action;
  const actionPlan = actionElement.dataset.plan || 'unknown';
  if (action === 'start-checkout') {
    event.preventDefault();
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
  if (action === 'close-checkout') {
    closeCheckout();
    return;
  }
  // Cashfree path intentionally disabled for now.
  if (action === 'pay-razorpay') {
    trackCustomEvent('PayButtonClicked', { plan: currentPlan || 'unknown' });
    payWithRazorpay();
  }
});

function initFunnelTracking() {
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
}

function initCountdown() {
  const KEY = 'aipb_cd_end';
  let end = parseInt(localStorage.getItem(KEY) || '0', 10);
  if (!end || end < Date.now()) {
    end = Date.now() + 48 * 60 * 60 * 1000;
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
    if (cdH) cdH.textContent = String(h).padStart(2, '0');
    if (cdM) cdM.textContent = String(m).padStart(2, '0');
    if (cdS) cdS.textContent = String(s).padStart(2, '0');
    if (diff === 0) clearInterval(timer);
  }
  tick();
  const timer = setInterval(tick, 1000);
}

function initActivityTicker() {
  const feed = [
    'Meera from Bengaluru generated her first poster style result · 2 min ago',
    'Rajiv from Kolkata made his first action figure image · 4 min ago',
    'Sneha from Ahmedabad finished her Ghibli portrait set · 7 min ago',
    'Arjun from Jaipur tested Book 7 — Movie Poster prompts · 11 min ago',
    'Kavya from Kochi joined the buyers community · 15 min ago',
    'Nikhil from Hyderabad created product visuals for his side project · 19 min ago',
    'Priya from Chennai turned her cat photo into a print she framed · 23 min ago',
    'Siddharth from Mumbai created his daughter\'s birthday portrait · 28 min ago'
  ];
  const el = document.getElementById('liveActivityText');
  if (!el) return;
  let idx = 0;
  setInterval(() => {
    idx = (idx + 1) % feed.length;
    el.style.opacity = '0';
    setTimeout(() => { el.textContent = feed[idx]; el.style.opacity = '1'; }, 300);
  }, 5000);
}

function initStickyCta() {
  const stickyCta = document.getElementById('stickyCta');
  if (!stickyCta) return;
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
}

function initNonCriticalFeatures() {
  initSessionAttributionTracking();
  initFunnelTracking();
  initScrollDepthTracking();
  initTimeOnPageTracking();
  initCheckoutFormIntentTracking();
  initCountdown();
  initActivityTicker();
  initStickyCta();
}

if ('requestIdleCallback' in window) {
  window.requestIdleCallback(() => initNonCriticalFeatures(), { timeout: 2000 });
} else {
  window.setTimeout(() => initNonCriticalFeatures(), 600);
}
});
