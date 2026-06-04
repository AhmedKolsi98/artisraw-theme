/* ArtisRaw components: accordion, stat count-up, sticky CTA, GA4 events.
 * Vanilla, deferred. All content works without JS (panels server-rendered open
 * via :target fallback is unnecessary — answers are in the DOM). */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* --- GA4 helper: push to dataLayer and/or gtag if present --- */
  function track(event, params) {
    params = params || {};
    if (window.dataLayer && window.dataLayer.push) {
      window.dataLayer.push(Object.assign({ event: event }, params));
    }
    if (typeof window.gtag === 'function') {
      window.gtag('event', event, params);
    }
  }
  window.artisrawTrack = track; // shared with forms.js

  /* --- Accordion (WAI-ARIA, multiple-open, deep-linkable) --- */
  function initAccordion(root) {
    var triggers = root.querySelectorAll('.accordion__trigger');
    Array.prototype.forEach.call(triggers, function (btn) {
      var panel = document.getElementById(btn.getAttribute('aria-controls'));
      if (!panel) return;
      btn.addEventListener('click', function () {
        var open = btn.getAttribute('aria-expanded') === 'true';
        setPanel(btn, panel, !open);
        if (!open) {
          var item = btn.closest('.accordion__item');
          var q = btn.textContent.trim();
          track('faq_expand', { item_index: indexOf(item), question: q });
          if (item && item.id) history.replaceState(null, '', '#' + item.id);
        }
      });
    });
  }
  function setPanel(btn, panel, open) {
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    panel.hidden = !open;
  }
  function indexOf(item) {
    if (!item || !item.parentNode) return -1;
    return Array.prototype.indexOf.call(item.parentNode.children, item);
  }
  // Open the accordion item targeted by the URL hash on load.
  function openHashTarget() {
    if (!location.hash) return;
    var item = document.getElementById(location.hash.slice(1));
    if (item && item.classList.contains('accordion__item')) {
      var btn = item.querySelector('.accordion__trigger');
      var panel = item.querySelector('.accordion__panel');
      if (btn && panel) { setPanel(btn, panel, true); item.scrollIntoView(); }
    }
  }
  Array.prototype.forEach.call(document.querySelectorAll('[data-accordion]'), initAccordion);
  openHashTarget();

  /* --- Stat count-up (gated by reduced-motion + IntersectionObserver) --- */
  function countUp(el) {
    var target = parseFloat(el.getAttribute('data-count-to'));
    if (isNaN(target)) return;
    var raw = el.textContent.trim();
    var prefix = (raw.match(/^[^0-9]*/) || [''])[0];
    var suffix = (raw.match(/[^0-9]*$/) || [''])[0];
    var dur = 1100, start = null;
    function frame(ts) {
      if (start === null) start = ts;
      var p = Math.min((ts - start) / dur, 1);
      var val = Math.floor(target * (0.5 - Math.cos(p * Math.PI) / 2)); // ease
      el.textContent = prefix + val.toLocaleString() + suffix;
      if (p < 1) requestAnimationFrame(frame);
      else el.textContent = raw; // restore exact formatting
    }
    requestAnimationFrame(frame);
  }
  var stats = document.querySelectorAll('.stat__value[data-count-to]');
  if (stats.length && !reduceMotion && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries, obs) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { countUp(e.target); obs.unobserve(e.target); }
      });
    }, { threshold: 0.4 });
    Array.prototype.forEach.call(stats, function (s) { io.observe(s); });
  }

  /* --- Sticky mobile CTA: show, hide while any form is in view --- */
  var sticky = document.querySelector('[data-sticky-cta]');
  if (sticky) {
    var forms = document.querySelectorAll('form');
    var formVisible = false;
    function refresh() { sticky.hidden = formVisible; }
    if (forms.length && 'IntersectionObserver' in window) {
      var seen = new Set();
      var fio = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) seen.add(e.target); else seen.delete(e.target);
        });
        formVisible = seen.size > 0;
        refresh();
      }, { threshold: 0.05 });
      Array.prototype.forEach.call(forms, function (f) { fio.observe(f); });
    }
    sticky.hidden = false;
    refresh();
  }

  /* --- GA4 click delegation (cta_click, doc_download, whatsapp_click) --- */
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-ga]');
    if (!el) return;
    var ev = el.getAttribute('data-ga');
    if (ev === 'doc_download') track('doc_download', { doc_name: el.getAttribute('data-doc-name') || '' });
    else if (ev === 'whatsapp_click') track('whatsapp_click', { page: location.pathname });
    else if (ev === 'cta_click') track('cta_click', { location: el.getAttribute('data-ga-location') || '', label: el.getAttribute('data-ga-label') || '' });
  });
})();
