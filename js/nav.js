/* ArtisRaw navigation: accessible mobile drawer + dropdown disclosures.
 * Vanilla, no deps. Progressive enhancement — links work with JS off. */
(function () {
  'use strict';

  var toggle  = document.getElementById('nav-toggle');
  var nav     = document.getElementById('primary-nav');
  var overlay = document.getElementById('nav-overlay');
  var body    = document.body;
  if (!toggle || !nav) return;

  var FOCUSABLE = 'a[href], button:not([disabled]), input, [tabindex]:not([tabindex="-1"])';
  var isOpen = false;
  var lastFocus = null;

  function focusable() {
    return Array.prototype.filter.call(
      nav.querySelectorAll(FOCUSABLE),
      function (el) { return el.offsetParent !== null || el === document.activeElement; }
    );
  }

  function openDrawer() {
    if (isOpen) return;
    isOpen = true;
    lastFocus = document.activeElement;
    body.classList.add('nav-open');
    toggle.setAttribute('aria-expanded', 'true');
    toggle.setAttribute('aria-label', toggle.getAttribute('data-label-close') || 'Close menu');
    if (overlay) overlay.hidden = false;
    var items = focusable();
    if (items.length) items[0].focus();
    document.addEventListener('keydown', onKeydown, true);
  }

  function closeDrawer(returnFocus) {
    if (!isOpen) return;
    isOpen = false;
    body.classList.remove('nav-open');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-label', toggle.getAttribute('data-label-open') || 'Open menu');
    if (overlay) overlay.hidden = true;
    document.removeEventListener('keydown', onKeydown, true);
    if (returnFocus !== false && lastFocus) lastFocus.focus();
  }

  function onKeydown(e) {
    if (e.key === 'Escape') { e.preventDefault(); closeDrawer(); return; }
    if (e.key !== 'Tab') return;
    // Focus trap within the drawer.
    var items = focusable();
    if (!items.length) return;
    var first = items[0], last = items[items.length - 1];
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  }

  toggle.addEventListener('click', function () { isOpen ? closeDrawer() : openDrawer(); });
  if (overlay) overlay.addEventListener('click', function () { closeDrawer(); });

  // Close the drawer after following an in-page nav link (mobile).
  nav.addEventListener('click', function (e) {
    var link = e.target.closest('a.nav__link, a.nav__sub-link');
    if (link && isOpen && window.matchMedia('(max-width: 1023px)').matches) {
      closeDrawer(false);
    }
  });

  // Reset state if resized up to desktop while open.
  window.addEventListener('resize', function () {
    if (isOpen && window.matchMedia('(min-width: 1024px)').matches) closeDrawer(false);
  });

  /* --- Dropdown disclosures ("Why ArtisRaw") --- */
  Array.prototype.forEach.call(nav.querySelectorAll('.nav__disclosure'), function (btn) {
    var panel = document.getElementById(btn.getAttribute('aria-controls'));
    var li = btn.closest('.nav__item');

    function setOpen(open) {
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      li.classList.toggle('is-open', open);
    }
    btn.addEventListener('click', function () {
      setOpen(btn.getAttribute('aria-expanded') !== 'true');
    });
    // Close on blur-out (desktop) / Esc.
    li.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && btn.getAttribute('aria-expanded') === 'true') {
        setOpen(false); btn.focus();
      }
    });
    if (panel) {
      li.addEventListener('focusout', function () {
        // Defer so the new focus target is known.
        setTimeout(function () {
          if (!li.contains(document.activeElement)) setOpen(false);
        }, 0);
      });
    }
  });
})();
