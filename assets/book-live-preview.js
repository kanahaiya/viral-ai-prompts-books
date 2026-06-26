/**
 * Shared live preview enhancer for chapter prompt books.
 * Adds a Prompt #1 live preview card and flashes updated values.
 */
(function initBookLivePreviewEnhancer() {
  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function escapeRegExp(value) {
    return String(value).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  }

  function ensureStyles() {
    if (document.getElementById('js-live-preview-style')) return;
    const styleElement = document.createElement('style');
    styleElement.id = 'js-live-preview-style';
    styleElement.textContent = `
      .setup-live-preview {
        margin-top: 1.2rem;
        border-top: 1px solid #eee;
        padding-top: 1rem;
      }
      .setup-live-preview__title {
        font-family: 'Courier New', monospace;
        font-size: 0.72rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #666;
        margin-bottom: 0.6rem;
      }
      .setup-live-preview .prompt-card {
        margin-bottom: 0;
      }
      .live-preview-flash {
        animation: livePreviewFlash 700ms ease-out;
      }
      .live-value-flash {
        background: #fff3bf;
        color: #1a1a1a;
        border-radius: 2px;
        padding: 0 2px;
        animation: liveValueFade 900ms ease-out;
      }
      @keyframes livePreviewFlash {
        0% { box-shadow: 0 0 0 0 rgba(212, 168, 54, 0.5); }
        100% { box-shadow: 0 0 0 10px rgba(212, 168, 54, 0); }
      }
      @keyframes liveValueFade {
        0% { background: #ffe58f; }
        100% { background: transparent; }
      }
    `;
    document.head.appendChild(styleElement);
  }

  function injectLivePreviewCard(setupElement) {
    if (document.getElementById('card-live-preview')) return;
    const actionRowElement = setupElement.querySelector('.action-row');
    if (!actionRowElement) return;

    const livePreviewWrapper = document.createElement('div');
    livePreviewWrapper.className = 'setup-live-preview';
    livePreviewWrapper.setAttribute('aria-live', 'polite');
    livePreviewWrapper.innerHTML = `
      <div class="setup-live-preview__title">Live Preview — Prompt #1</div>
      <div class="prompt-card" id="card-live-preview">
        <div class="card-top">
          <span class="pnum">#1</span>
          <span class="headline" id="hl-live"></span>
        </div>
        <div class="hormozi-block" id="hb-live"></div>
        <button type="button" class="copy-btn" id="copy-live-preview-btn">Copy Prompt #1</button>
      </div>
    `;

    setupElement.insertBefore(livePreviewWrapper, actionRowElement);
  }

  function attachCopyHandler() {
    const copyButtonElement = document.getElementById('copy-live-preview-btn');
    if (!copyButtonElement) return;

    copyButtonElement.addEventListener('click', () => {
      const liveBlockElement = document.getElementById('hb-live');
      if (!liveBlockElement) return;
      navigator.clipboard.writeText(liveBlockElement.textContent || '');
    });
  }

  function start() {
    const setupElement = document.getElementById('setup');
    if (!setupElement) return;
    if (typeof window.buildBlock !== 'function' || typeof window.update !== 'function') return;

    ensureStyles();
    injectLivePreviewCard(setupElement);
    attachCopyHandler();

    const liveBlockElement = document.getElementById('hb-live');
    const liveHeadlineElement = document.getElementById('hl-live');
    if (!liveBlockElement) return;

    let pendingHighlightValue = '';
    const inputElements = setupElement.querySelectorAll('.var-input');
    inputElements.forEach((inputElement) => {
      inputElement.addEventListener('input', () => {
        pendingHighlightValue = inputElement.value.trim();
      }, true);
    });

    function renderLivePreview(highlightValue) {
      const liveText = window.buildBlock(1);
      if (!highlightValue) {
        liveBlockElement.textContent = liveText;
      } else {
        const escapedLiveText = escapeHtml(liveText);
        const highlightedHtml = escapedLiveText.replace(
          new RegExp(escapeRegExp(escapeHtml(highlightValue)), 'g'),
          `<span class="live-value-flash">${escapeHtml(highlightValue)}</span>`
        );
        liveBlockElement.innerHTML = highlightedHtml;
        liveBlockElement.classList.remove('live-preview-flash');
        void liveBlockElement.offsetWidth;
        liveBlockElement.classList.add('live-preview-flash');
      }

      if (liveHeadlineElement && window.H && window.H[1]) {
        liveHeadlineElement.textContent = window.H[1];
      }
    }

    const originalUpdate = window.update;
    window.update = function wrappedUpdate() {
      originalUpdate.apply(this, arguments);
      renderLivePreview(pendingHighlightValue);
      pendingHighlightValue = '';
    };

    renderLivePreview('');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();
