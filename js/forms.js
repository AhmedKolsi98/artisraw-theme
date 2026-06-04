/* ArtisRaw two-step quote form (SPEC §5.5).
 * Inline validation + AJAX submit + success/Step-2 reveal + GA4 form_submit.
 * Posts to the REST endpoint declared on the form's data-endpoint. */
(function () {
  'use strict';

  var track = window.artisrawTrack || function () {};

  function qs(form) { return new URLSearchParams(location.search); }

  function validators(field) {
    var input = field.querySelector('input, select, textarea');
    if (!input) return true;
    var name = input.name, val = (input.value || '').trim();
    if (name === 'email') return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(val) ? '' : 'Please enter a valid work email.';
    if (input.hasAttribute('required') && !val) {
      if (name === 'company') return 'Please enter your company name.';
      if (name === 'country') return 'Please select a destination country.';
      return 'This field is required.';
    }
    return '';
  }

  function setFieldState(field, msg) {
    var slot = field.querySelector('.field__msg');
    if (msg) {
      field.classList.add('is-error'); field.classList.remove('is-valid');
      if (slot) slot.textContent = msg;
    } else {
      field.classList.remove('is-error'); field.classList.add('is-valid');
      if (slot) slot.textContent = '';
    }
    return !msg;
  }

  function initForm(form) {
    var endpoint = form.getAttribute('data-endpoint');
    var nonce = form.getAttribute('data-nonce');
    var location_ = form.getAttribute('data-location') || 'inline';
    var step1 = form.querySelector('[data-step="1"]');
    var successPanel = form.querySelector('[data-step="success"]');
    var step2 = form.querySelector('[data-step="2"]');
    var errBox = form.querySelector('.form-errors');

    // Capture UTM + page URL into hidden fields.
    var p = qs();
    ['utm_source', 'utm_medium', 'utm_campaign'].forEach(function (k) {
      var input = form.querySelector('[name="' + k + '"]');
      if (input && p.get(k)) input.value = p.get(k);
    });
    var pageUrl = form.querySelector('[name="page_url"]');
    if (pageUrl) pageUrl.value = location.href;

    // Per-field validation on blur.
    var required = step1.querySelectorAll('.field input[required], .field select[required], .field input[type="email"]');
    Array.prototype.forEach.call(required, function (input) {
      var field = input.closest('.field');
      input.addEventListener('blur', function () { setFieldState(field, validators(field)); });
    });

    function gather() {
      var data = {};
      var els = form.querySelectorAll('input, select, textarea');
      Array.prototype.forEach.call(els, function (el) {
        if (!el.name) return;
        if (el.type === 'checkbox') {
          if (el.checked) {
            if (el.name.slice(-2) === '[]') { var k = el.name.slice(0, -2); (data[k] = data[k] || []).push(el.value); }
            else data[el.name] = el.value;
          }
        } else {
          data[el.name] = el.value;
        }
      });
      return data;
    }

    function post(payload, btn) {
      if (btn) { btn.setAttribute('aria-busy', 'true'); btn.disabled = true; }
      return fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
        body: JSON.stringify(payload)
      }).then(function (r) { return r.json().then(function (j) { return { status: r.status, body: j }; }); })
        .finally(function () { if (btn) { btn.removeAttribute('aria-busy'); btn.disabled = false; } });
    }

    function showErrors(map) {
      var first = null;
      Object.keys(map).forEach(function (name) {
        var input = form.querySelector('[name="' + name + '"]');
        if (input) {
          var field = input.closest('.field');
          setFieldState(field, map[name]);
          if (!first) first = input;
        }
      });
      if (errBox) {
        errBox.hidden = false;
        errBox.innerHTML = 'Please check the highlighted fields.';
      }
      if (first) first.focus();
    }

    // Step 1 submit.
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (errBox) errBox.hidden = true;
      var fields = step1.querySelectorAll('.field');
      var ok = true, firstBad = null;
      Array.prototype.forEach.call(fields, function (field) {
        var input = field.querySelector('[required], input[type=email]');
        if (!input) return;
        var msg = validators(field);
        if (!setFieldState(field, msg)) { ok = false; if (!firstBad) firstBad = input; }
      });
      if (!ok) { if (firstBad) firstBad.focus(); return; }

      var payload = gather();
      payload.step = 1;
      // Invisible Turnstile token, if the widget + library are present.
      if (window.turnstile && form.querySelector('.cf-turnstile')) {
        try { payload.turnstile_token = window.turnstile.getResponse(); } catch (e2) {}
      }
      var btn = form.querySelector('.quote-form__submit');
      post(payload, btn).then(function (res) {
        if (res.status === 200 && res.body.ok) {
          var emailVal = (form.querySelector('[name="email"]') || {}).value || '';
          var emailSlot = form.querySelector('[data-success-email]');
          if (emailSlot) emailSlot.textContent = emailVal;
          step1.hidden = true;
          successPanel.hidden = false;
          successPanel.scrollIntoView({ block: 'nearest' });
          var h = successPanel.querySelector('h3'); if (h) { h.setAttribute('tabindex', '-1'); h.focus(); }
          track('form_submit', { step: 1, location: location_ });
        } else if (res.body && res.body.errors) {
          showErrors(res.body.errors);
        } else {
          if (errBox) { errBox.hidden = false; errBox.textContent = (res.body && res.body.message) || 'Something went wrong. Please try again or email us.'; }
        }
      }).catch(function () {
        if (errBox) { errBox.hidden = false; errBox.textContent = 'Network error. Please try again or email us directly.'; }
      });
    });

    // Step 2 submit (optional enrichment).
    var submit2 = form.querySelector('.quote-form__submit2');
    if (submit2 && step2) {
      submit2.addEventListener('click', function () {
        var payload = gather();
        payload.step = 2;
        post(payload, submit2).then(function (res) {
          if (res.status === 200 && res.body.ok) {
            step2.hidden = true;
            var done = form.querySelector('[data-step="done"]');
            if (done) { done.hidden = false; done.setAttribute('tabindex', '-1'); done.focus(); }
            track('form_submit', { step: 2, location: location_ });
          }
        });
      });
    }
  }

  Array.prototype.forEach.call(document.querySelectorAll('[data-quote-form]'), initForm);
})();
