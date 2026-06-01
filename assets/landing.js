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

function getCheckoutMetrics(plan, selectedCount = 0) {
  if (plan === 'bundle') {
    return { value: BUNDLE_PRICE_INR, numItems: 11, contentName: 'Full System' };
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
  currentPlan = plan;
  currentBookId = bookId;
  currentBookIds = Array.isArray(bookIds) ? bookIds : (bookId ? [bookId] : []);
  const selectedCount = currentBookIds.length;
  const totalInr = plan === 'bundle' ? 299 : (selectedCount * SINGLE_BOOK_PRICE_INR);
  const price = `₹${totalInr}`;

  const booksById = (window.__AIPB_CONFIG && window.__AIPB_CONFIG.booksById) || {};
  const selectedBookNames = currentBookIds.map((id) => booksById[id]).filter(Boolean);
  const bookName = plan === 'bundle'
    ? 'All 11 Books + Bonus Guide (Full System)'
    : selectedBookNames.join(', ');
  const planLabel = plan === 'bundle'
    ? '📦 Full System'
    : `📚 Selected Books (${selectedCount})`;
  document.getElementById('checkoutSummary').innerHTML =
    `<strong style="color:#fff">${planLabel}</strong><br>
     <span style="color:#888">${bookName}</span><br>
     <span style="color:#d4a836;font-weight:700;font-size:1.1rem;font-family:'Courier New',monospace;">${price}</span>`;

  document.getElementById('checkoutModal').style.display = 'block';
  document.body.style.overflow = 'hidden';

  trackCheckoutEvent('InitiateCheckout', plan, plan === 'bundle' ? 11 : selectedCount, {
    selected_books: plan === 'single' ? currentBookIds.join(',') : '',
    source: plan === 'bundle' ? 'bundle-cta' : 'book-modal'
  });
  trackEvent('AddToCart', {
    currency,
    value: totalInr,
    num_items: plan === 'bundle' ? 11 : selectedCount,
    content_name: plan === 'bundle' ? 'Full System' : `Selected Books (${selectedCount})`,
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
  const nameElement = document.getElementById('buyerName');
  const emailElement = document.getElementById('buyerEmail');

  if (!nameElement || !emailElement) {
    showError('Checkout form is updating. Please refresh and try again.');
    return null;
  }

  const name = nameElement.value.trim();
  const email = emailElement.value.trim();
  if (!name) { showError('Please enter your name.'); return null; }
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
      name: (window.__AIPB_CONFIG && window.__AIPB_CONFIG.siteName) || 'AI Prompt Books',
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
    document.getElementById('razorpayBtn').textContent = 'Pay with UPI / Card (Razorpay)';
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
    });
    window.location.href = '/setup-account.php?token=' + result.token;
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
    });
    trackCustomEvent('PaymentVerified', {
      plan: currentPlan || 'unknown',
      selected_count: currentBookIds.length,
      order_id: orderId
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
