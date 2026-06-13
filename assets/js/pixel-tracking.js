/**
 * Shared Meta Pixel helpers for consistent event tracking.
 */
(function initPixelHelpers(windowObject) {
  if (!windowObject) return;

  if (typeof windowObject.fbq === 'function' && windowObject.__aipbPageViewSent !== true) {
    windowObject.fbq('track', 'PageView');
    windowObject.__aipbPageViewSent = true;
  }

  if (typeof windowObject.pixelTrack !== 'function') {
    windowObject.pixelTrack = function pixelTrack(eventName, params, options) {
      if (typeof windowObject.fbq !== 'function') return;
      if (options) {
        windowObject.fbq('track', eventName, params || {}, options);
      } else {
        windowObject.fbq('track', eventName, params || {});
      }
    };
  }

  if (typeof windowObject.pixelTrackCustom !== 'function') {
    windowObject.pixelTrackCustom = function pixelTrackCustom(eventName, params) {
      if (typeof windowObject.fbq !== 'function') return;
      windowObject.fbq('trackCustom', eventName, params || {});
    };
  }
})(window);
