window.addEventListener('DOMContentLoaded', () => {
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
  currentPlan = plan;
  currentBookId = bookId;
  currentBookIds = Array.isArray(bookIds) ? bookIds : (bookId ? [bookId] : []);
  const selectedCount = currentBookIds.length;
  const totalInr = plan === 'bundle' ? 299 : (selectedCount * SINGLE_BOOK_PRICE_INR);
  const price = `₹${totalInr}`;

  const booksById = (window.__AIPB_CONFIG && window.__AIPB_CONFIG.booksById) || {};
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
  const name = document.getElementById('buyerName').value.trim();
  const email = document.getElementById('buyerEmail').value.trim();
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
      description: currentPlan === 'bundle' ? 'Full Bundle — All 11 Books + Bonus Guide' : `${currentBookIds.length} Book Access`,
      order_id: order.id,
      prefill: { name: data.name, email: data.email },
      theme: { color: '#d4a836' },
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
          document.getElementById('razorpayBtn').disabled = false;
        }
      }
    };
    new Razorpay(options).open();
  } catch (e) {
    showError('Could not reach payment service. Check internet and try again.');
  } finally {
    document.getElementById('razorpayBtn').textContent = 'Pay with UPI / Card (Razorpay)';
    document.getElementById('razorpayBtn').disabled = false;
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

document.querySelectorAll('.faq-q').forEach((q) => {
  q.addEventListener('click', () => {
    q.closest('.faq-item').classList.toggle('open');
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
  if (action === 'pay-razorpay') {
    trackCustomEvent('PayButtonClicked', { plan: currentPlan || 'unknown' });
    payWithRazorpay();
  }
});

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

(function initCountdown() {
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
})();

(function initActivityTicker() {
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
})();

(function initStickyCta() {
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
})();
});
