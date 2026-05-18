/**
 * Shared Meta Pixel helpers for consistent event tracking.
 */
(function initPixelHelpers(windowObject) {
  if (!windowObject) return;

  if (typeof windowObject.pixelTrack !== 'function') {
    windowObject.pixelTrack = function pixelTrack(eventName, params) {
      if (typeof windowObject.fbq !== 'function') return;
      windowObject.fbq('track', eventName, params || {});
    };
  }

  if (typeof windowObject.pixelTrackCustom !== 'function') {
    windowObject.pixelTrackCustom = function pixelTrackCustom(eventName, params) {
      if (typeof windowObject.fbq !== 'function') return;
      windowObject.fbq('trackCustom', eventName, params || {});
    };
  }
})(window);
