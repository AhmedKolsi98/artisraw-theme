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

  /* --- Reveal-on-scroll (Figma .reveal). Reduced-motion / no-IO → show all.
     CSS hides .reveal only under .js, so non-JS visitors always see content. */
  var reveals = document.querySelectorAll('.reveal');
  if (reveals.length) {
    if (reduceMotion || !('IntersectionObserver' in window)) {
      Array.prototype.forEach.call(reveals, function (el) { el.classList.add('is-visible'); });
    } else {
      var rio = new IntersectionObserver(function (entries, obs) {
        entries.forEach(function (e) {
          if (e.isIntersecting) { e.target.classList.add('is-visible'); obs.unobserve(e.target); }
        });
      }, { threshold: 0.12 });
      Array.prototype.forEach.call(reveals, function (el) { rio.observe(el); });
    }
  }

  /* --- hero_view: hero ≥50% in view for 1s (SPEC §8) --- */
  var hero = document.querySelector('[data-hero]');
  if (hero && 'IntersectionObserver' in window) {
    var heroTimer = null, heroFired = false;
    var hio = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (heroFired) return;
        if (e.isIntersecting && e.intersectionRatio >= 0.5) {
          heroTimer = setTimeout(function () {
            heroFired = true;
            track('hero_view', { section: hero.getAttribute('data-hero') || 'hero' });
            hio.disconnect();
          }, 1000);
        } else if (heroTimer) {
          clearTimeout(heroTimer); heroTimer = null;
        }
      });
    }, { threshold: [0.5] });
    hio.observe(hero);
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

  /* --- Newsletter signup (progressive enhancement) --- */
  Array.prototype.forEach.call(document.querySelectorAll('[data-newsletter]'), function (form) {
    var endpoint = form.getAttribute('data-endpoint');
    var nonce = form.getAttribute('data-nonce');
    var msg = form.querySelector('[data-newsletter-msg]');
    function say(text, ok) {
      if (!msg) return;
      msg.textContent = text;
      msg.hidden = false;
      msg.setAttribute('data-state', ok ? 'ok' : 'error');
    }
    form.addEventListener('submit', function (e) {
      if (!endpoint || !window.fetch) return; // no-JS path already handled by action=/contact/
      e.preventDefault();
      var emailEl = form.querySelector('input[name="email"]');
      var btn = form.querySelector('button[type="submit"]');
      var body = {
        email: emailEl ? emailEl.value : '',
        website: (form.querySelector('input[name="website"]') || {}).value || '',
        source: form.getAttribute('data-location') || 'inline'
      };
      if (btn) btn.setAttribute('aria-busy', 'true');
      fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
        body: JSON.stringify(body)
      }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
        .then(function (res) {
          if (btn) btn.removeAttribute('aria-busy');
          if (res.ok && res.d.ok) {
            form.reset();
            say(res.d.message || 'You’re subscribed.', true);
            track('newsletter_signup', { location: body.source });
          } else {
            say((res.d && res.d.message) || 'Something went wrong — please try again.', false);
          }
        })
        .catch(function () {
          if (btn) btn.removeAttribute('aria-busy');
          say('Network error — please try again.', false);
        });
    });
  });

  /* --- Product-scroll carousel: auto-scroll + progress bar line ---------- */
  function initProductScroll(root) {
    var track = root.querySelector('.product-scroll__track');
    var bar = root.querySelector('.product-scroll__bar');
    var thumb = root.querySelector('.product-scroll__bar-thumb');
    if (!track) return;

    var isHovering = false;
    var userPaused = false;
    var autoId = null;
    var resumeTimer = null;
    var speed = 0.8; // px per frame
    var resumeDelay = 3000; // ms

    function hasOverflow() {
      return track.scrollWidth > track.clientWidth + 1;
    }

    function updateThumb() {
      if (!thumb) return;
      var max = track.scrollWidth - track.clientWidth;
      var ratio = max > 0 ? track.scrollLeft / max : 0;
      var widthPct = max > 0 ? (track.clientWidth / track.scrollWidth) * 100 : 100;
      thumb.style.width = widthPct + '%';
      thumb.style.left = (ratio * Math.max(0, 100 - widthPct)) + '%';
    }

    function start() {
      stop();
      if (reduceMotion || userPaused || isHovering || !hasOverflow()) return;
      autoId = requestAnimationFrame(function step() {
        if (userPaused || isHovering || !hasOverflow()) return;
        var max = track.scrollWidth - track.clientWidth;
        track.scrollLeft += speed;
        if (track.scrollLeft >= max - 1) {
          track.scrollLeft = 0;
        }
        autoId = requestAnimationFrame(step);
      });
    }

    function stop() {
      if (autoId) { cancelAnimationFrame(autoId); autoId = null; }
    }

    function userScroll() {
      userPaused = true;
      stop();
      clearTimeout(resumeTimer);
      resumeTimer = setTimeout(function () {
        userPaused = false;
        if (!isHovering) start();
      }, resumeDelay);
    }

    track.addEventListener('scroll', function () {
      updateThumb();
      userScroll();
    }, { passive: true });

    track.addEventListener('mouseenter', function () { isHovering = true; stop(); });
    track.addEventListener('mouseleave', function () { isHovering = false; if (!userPaused) start(); });
    track.addEventListener('touchstart', function () { isHovering = true; stop(); }, { passive: true });
    track.addEventListener('touchend', function () { isHovering = false; userScroll(); });

    updateThumb();
    if (bar) bar.hidden = !hasOverflow();
    start();
  }

  Array.prototype.forEach.call(document.querySelectorAll('.product-scroll[data-autoscroll="true"]'), initProductScroll);

  /* --- Home hero carousel (PDF variants): auto-rotate + dot nav ---------- */
  function initHeroCarousel(root) {
    var track = root.querySelector('.hero-carousel__track');
    var slides = root.querySelectorAll('.hero-carousel__slide');
    var dots = root.querySelectorAll('.hero-carousel__dot');
    if (!track || slides.length < 2) return;

    var interval = parseInt(root.getAttribute('data-carousel-interval'), 10) || 6000;
    var current = 0;
    var timer = null;
    var paused = false;

    function show(index) {
      index = (index + slides.length) % slides.length;
      slides[current].classList.remove('is-active');
      slides[current].setAttribute('aria-hidden', 'true');
      if (dots[current]) {
        dots[current].classList.remove('is-active');
        dots[current].setAttribute('aria-selected', 'false');
      }
      current = index;
      slides[current].classList.add('is-active');
      slides[current].setAttribute('aria-hidden', 'false');
      if (dots[current]) {
        dots[current].classList.add('is-active');
        dots[current].setAttribute('aria-selected', 'true');
      }
    }

    function next() { show(current + 1); }
    function start() {
      stop();
      if (!paused && !reduceMotion) timer = setInterval(next, interval);
    }
    function stop() { if (timer) { clearInterval(timer); timer = null; } }

    Array.prototype.forEach.call(dots, function (dot, i) {
      dot.addEventListener('click', function () {
        show(i);
        paused = true;
        stop();
      });
    });

    root.addEventListener('mouseenter', function () { paused = true; stop(); });
    root.addEventListener('mouseleave', function () { paused = false; start(); });
    root.addEventListener('focusin', function () { paused = true; stop(); });
    root.addEventListener('focusout', function () { paused = false; start(); });

    start();
  }

  Array.prototype.forEach.call(document.querySelectorAll('.hero-carousel'), initHeroCarousel);

  /* --- GA4 click delegation (cta_click, doc_download, whatsapp_click) --- */
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-ga]');
    if (!el) return;
    var ev = el.getAttribute('data-ga');
    if (ev === 'doc_download') {
      var doc = el.getAttribute('data-doc-name') || '';
      track('doc_download', { doc_name: doc });
      // Distinct events for the two priority assets (SPEC §8).
      if (/line-?sheet/i.test(doc)) track('linesheet_download', { location: el.getAttribute('data-ga-location') || '' });
      if (/compliance-?pack/i.test(doc)) track('compliance_pack_download', { location: el.getAttribute('data-ga-location') || '' });
    }
    else if (ev === 'whatsapp_click') track('whatsapp_click', { page: location.pathname });
    else if (ev === 'cta_click') track('cta_click', { location: el.getAttribute('data-ga-location') || '', label: el.getAttribute('data-ga-label') || '' });
  });
})();
