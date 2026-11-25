/*
 * Healthray Pharmacy PPC landing pages: shared script.
 * Used by temp-ppc-pharma-google.php, temp-ppc-pharma-meta.php and temp-ppc-pharma-chatgpt.php.
 * Each template sets window.hrPharmaPage = { channel, defaultVariant, variants } just before
 * this file loads (wp_add_inline_script 'before'). Everything else is identical on all pages.
 */
    (function() {
      'use strict';
      /* ===== Per-page settings come from the template (window.hrPharmaPage): channel and ?v= headlines ===== */
      var page = window.hrPharmaPage || {};
      var VARIANTS = page.variants || {};
      var defaultVariant = page.defaultVariant || Object.keys(VARIANTS)[0] || '';
      var params = new URLSearchParams(location.search);
      var v = (params.get('v') || defaultVariant).toLowerCase();
      if (!VARIANTS[v]) v = defaultVariant;
      if (VARIANTS[v]) {
        if (VARIANTS[v].h) document.getElementById('hero-title').textContent = VARIANTS[v].h;
        if (VARIANTS[v].s) document.getElementById('hero-sub').textContent = VARIANTS[v].s;
        if (VARIANTS[v].k) document.getElementById('hero-eyebrow').textContent = VARIANTS[v].k;
      }

      window.dataLayer = window.dataLayer || [];

      function track(event, data) {
        dataLayer.push(Object.assign({
          event: event,
          segment: 'retail',
          channel: page.channel || '',
          variant: v
        }, data || {}));
      }
      var reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

      /* ===== Contact Form 7 and shared form popup ===== */
      var formHost = document.getElementById('hr-pharmacy-cf7');
      var formHome = document.getElementById('hr-pharmacy-form-home');
      var dialog = document.getElementById('hr-pharmacy-modal');
      var modalSlot = document.getElementById('hr-pharmacy-modal-slot');
      var modalTitle = document.getElementById('hr-pharmacy-modal-title');
      var modalHint = document.getElementById('hr-pharmacy-modal-hint');
      // The "Start free" popup text is written in each template's <dialog>; keep it so it can be restored.
      var startTitle = modalTitle.textContent;
      var startHint = modalHint.textContent;
      var previousFocus = null;

      var activeMode = 'start_free',
        activeLocation = 'hero';

      function setTracking(mode, loc) {
        activeMode = mode;
        activeLocation = loc || '';
        var values = {
          variant: v,
          cta_type: mode,
          cta_location: loc || ''
        };
        ['gclid', 'fbclid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function(k) {
          var val = params.get(k);
          if (val) values[k] = val.slice(0, 200);
        });
        // Populate existing CF7 hidden tags; mail tags remain controlled in CF7 admin.
        formHost.querySelectorAll('input[type="hidden"]').forEach(function(input) {
          var key = input.name.replace(/_cf7$/, '');
          if (Object.prototype.hasOwnProperty.call(values, key)) input.value = values[key];
        });
      }
      setTracking('start_free', 'hero');

      function openForm(e, mode, loc) {
        e.preventDefault();
        previousFocus = e.currentTarget;
        var demo = mode === 'demo';
        modalTitle.textContent = demo ? 'Book a free demo' : startTitle;
        modalHint.textContent = demo ? "Fill in your details. We'll call to arrange your free demo." : startHint;
        setTracking(mode, loc);
        track('cta_click', {
          cta_type: mode,
          cta_location: loc
        });
        // Keep the initialized CF7 DOM intact, including validation and entered values.
        if (typeof dialog.showModal !== 'function') {
          document.getElementById('start').scrollIntoView({
            behavior: reduceMotion ? 'auto' : 'smooth'
          });
          return;
        }
        modalSlot.appendChild(formHost);
        dialog.showModal();
        document.documentElement.classList.add('hr-pharmacy-modal-open');
        focusFormStep();
      }
      document.querySelectorAll('.js-start,.js-demo').forEach(function(el) {
        el.setAttribute('aria-haspopup', 'dialog');
        el.setAttribute('aria-controls', 'hr-pharmacy-modal');
        el.addEventListener('click', function(e) {
          openForm(e, el.classList.contains('js-demo') ? 'demo' : 'start_free', el.dataset.cta);
        });
      });
      dialog.querySelector('.hr-pharmacy-modal-close').addEventListener('click', function() {
        dialog.close();
      });
      dialog.addEventListener('click', function(e) {
        if (e.target !== dialog) return;
        var r = dialog.getBoundingClientRect();
        if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) dialog.close();
      });
      // Native dialog handles Escape and traps focus; restore the form and trigger on close.
      dialog.addEventListener('close', function() {
        formHome.appendChild(formHost);
        setTracking('start_free', 'hero');
        document.documentElement.classList.remove('hr-pharmacy-modal-open');
        if (previousFocus) previousFocus.focus({
          preventScroll: true
        });
      });
      var started = false;
      formHost.addEventListener('input', function() {
        if (!started) {
          started = true;
          track('form_start');
        }
      });
      formHost.addEventListener('wpcf7mailsent', function() {
        track('form_success', {
          form: 'pharmacy_ppc'
        });
      });

      /* Two-step CF7 form: billing choice (buttons -> hidden bed_size string), then contact details. */
      var cf7Form = formHost.querySelector('form');
      var stepOne = formHost.querySelector('.hr-fstep-1');
      var stepTwo = formHost.querySelector('.hr-fstep-2');
      var currentStep = 1;

      function focusFormStep() {
        var scope = stepOne && stepTwo ? (currentStep === 1 ? stepOne : stepTwo) : formHost;
        var first = scope.querySelector('input:not([type="hidden"]):not([type="submit"]):not([disabled]),select,textarea') || scope.querySelector('button');
        if (first) first.focus({
          preventScroll: true
        });
      }

      if (cf7Form && stepOne && stepTwo) {
        var billInput = cf7Form.querySelector('input[name="bed_size"]');
        var billButtons = stepOne.querySelectorAll('.hr-bill-option');
        var next = stepOne.querySelector('.hr-next');
        var back = stepTwo.querySelector('.hr-back');
        var softwareError = stepOne.querySelector('.hr-software-err');
        var softwareBox = stepOne.querySelector('.hr-software');

        // Match the HMS reference: keep the input local and prefix the submitted FormData.
        var mobileField = cf7Form.querySelector('input[name="your-number"]:not([type="hidden"])') || cf7Form.querySelector('input[name="mobile-local"]');
        function normalizeMobile(value) {
          var digits = value.replace(/\D/g, '');
          if (digits.length === 12 && digits.slice(0, 2) === '91') return digits.slice(2);
          if (digits.length === 11 && digits.charAt(0) === '0') return digits.slice(1);
          return digits.slice(0, 10);
        }
        cf7Form.addEventListener('formdata', function(event) {
          var digits = mobileField ? normalizeMobile(mobileField.value) : '';
          event.formData.set('your-number', /^\d{10}$/.test(digits) ? '+91 ' + digits : '');
        });

        var mobileError = stepTwo.querySelector('.hr-mobile-err');
        var stepLabel = formHost.querySelector('.hr-fsteps-label');
        var pickedLabel = stepTwo.querySelector('.hr-picked-label');

        // Announce step changes without reading the decorative progress bar.
        var status = document.createElement('span');
        status.setAttribute('role', 'status');
        status.setAttribute('aria-live', 'polite');
        status.style.cssText = 'position:absolute;width:1px;height:1px;padding:0;overflow:hidden;clip-path:inset(50%);white-space:nowrap';
        formHost.appendChild(status);

        /* ---- billing buttons ---- */
        function chosenSoftware() {
          return billInput && billInput.value ? billInput : null;
        }
        function selectBill(btn) {
          billButtons.forEach(function(b) {
            var on = b === btn;
            b.classList.toggle('is-selected', on);
            b.setAttribute('aria-pressed', on ? 'true' : 'false');
          });
          billInput.value = btn.dataset.bed; // plain string, not an array
          if (softwareError) softwareError.hidden = true;
          if (softwareBox) softwareBox.classList.remove('invalid');
        }
        function clearBill() {
          if (billInput) billInput.value = '';
          billButtons.forEach(function(b) {
            b.classList.remove('is-selected');
            b.setAttribute('aria-pressed', 'false');
          });
        }
        billButtons.forEach(function(btn) {
          btn.addEventListener('click', function() { selectBill(btn); });
        });

        function showStep(step, focus) {
          currentStep = step;
          formHost.dataset.step = String(step);
          stepOne.hidden = step !== 1;
          stepTwo.hidden = step !== 2;
          var text = 'Step ' + step + ' of 2';
          if (stepLabel) stepLabel.textContent = text;
          status.textContent = text;
          var chosen = chosenSoftware();
          if (pickedLabel) pickedLabel.textContent = chosen ? chosen.value : '';
          if (focus) focusFormStep();
        }

        function goNext() {
          var chosen = chosenSoftware();
          if (softwareError) softwareError.hidden = !!chosen;
          if (softwareBox) softwareBox.classList.toggle('invalid', !chosen);
          if (!chosen) {
            if (billButtons[0]) billButtons[0].focus();
            status.textContent = 'Please pick one.';
            return false;
          }
          showStep(2, true);
          return true;
        }
        if (next) next.addEventListener('click', goNext);
        if (back) back.addEventListener('click', function() {
          showStep(1, true);
        });

        function validMobile() {
          var valid = !!mobileField && /^\d{10}$/.test(mobileField.value);
          if (mobileError) {
            mobileError.hidden = valid;
            mobileError.textContent = 'Please enter a 10-digit mobile number.';
          }
          if (mobileField) {
            mobileField.setCustomValidity(valid ? '' : 'Please enter a 10-digit mobile number.');
            mobileField.setAttribute('aria-invalid', valid ? 'false' : 'true');
          }
          return valid;
        }
        // Lets the phone, browser or Facebook/Instagram in-app browser fill the name and mobile in one tap.
        var nameField = cf7Form.querySelector('input[name="your-name"]');
        if (nameField && !nameField.getAttribute('autocomplete')) nameField.setAttribute('autocomplete', 'name');
        if (mobileField && !mobileField.getAttribute('autocomplete')) mobileField.setAttribute('autocomplete', 'tel-national');
        if (mobileField) {
          mobileField.setAttribute('inputmode', 'numeric');
          mobileField.setAttribute('pattern', '[0-9]{10}');
          mobileField.removeAttribute('maxlength');
          mobileField.value = normalizeMobile(mobileField.value);
          if (mobileError) {
            mobileError.id = 'hr-pharmacy-mobile-error';
            mobileError.textContent = 'Please enter a 10-digit mobile number.';
            var describedBy = (mobileField.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean);
            if (describedBy.indexOf(mobileError.id) === -1) describedBy.push(mobileError.id);
            mobileField.setAttribute('aria-describedby', describedBy.join(' '));
          }
          mobileField.addEventListener('input', function(event) {
            mobileField.value = event.inputType === 'insertText' ? mobileField.value.replace(/\D/g, '').slice(0, 10) : normalizeMobile(mobileField.value);
            if (mobileError && !mobileError.hidden) {
              validMobile();
            } else {
              mobileField.setCustomValidity('');
              mobileField.setAttribute('aria-invalid', 'false');
            }
          });
        }

        // Capture before CF7's submit handler; CF7 remains responsible for sending.
        cf7Form.addEventListener('submit', function(e) {
          if (currentStep === 1 || !chosenSoftware()) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (currentStep !== 1) showStep(1, false);
            goNext();
            return;
          }
          if (!validMobile()) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (mobileField) mobileField.focus();
            return;
          }
        }, true);

        cf7Form.addEventListener('reset', function() {
          setTimeout(function() {
            // A hidden input keeps a JS-set value through reset, so clear it explicitly.
            clearBill();
            if (softwareError) softwareError.hidden = true;
            if (softwareBox) softwareBox.classList.remove('invalid');
            if (mobileError) mobileError.hidden = true;
            if (mobileField) {
              mobileField.setCustomValidity('');
              mobileField.removeAttribute('aria-invalid');
            }
            showStep(1, false);
            // Restore CTA and campaign fields after CF7 resets hidden defaults.
            setTracking(activeMode, activeLocation);
          }, 0);
        });

        formHost.addEventListener('wpcf7invalid', function(e) {
          var invalid = e.detail && e.detail.apiResponse && e.detail.apiResponse.invalid_fields || [];
          var softwareInvalid = invalid.some(function(field) {
            return field.field === 'bed_size';
          });
          showStep(softwareInvalid ? 1 : 2, true);
        });

        showStep(1, false);
      }

      // CF7 owns submission, validation, mail delivery and response messages.

      /* ===== FAQ accordion: first open, only one expanded at a time ===== */
      var faqItems = document.querySelectorAll('.hr-pharmacy-landing #faq .faq details');
      faqItems.forEach(function(item) {
        // Fallback for browsers without exclusive details-name grouping.
        item.addEventListener('toggle', function() {
          if (!item.open) return;
          faqItems.forEach(function(other) {
            if (other !== item) other.open = false;
          });
        });
      });

      /* ===== Scroll-driven UI: sticky mobile bar + tour video ===== */
      var sticky = document.getElementById('sticky');
      var heroForm = document.getElementById('start');
      var tour = document.getElementById('tour-video');
      var stickyShown = false,
        ticking = false,
        tourStarted = false,
        tourDone = false;

      function inView(el, share) {
        var r = el.getBoundingClientRect();
        var visible = Math.min(r.bottom, innerHeight) - Math.max(r.top, 0);
        return visible > r.height * share;
      }

      function onScroll() {
        ticking = false;
        var r = heroForm.getBoundingClientRect();
        var show = r.bottom < 0 || r.top > innerHeight;
        if (show !== stickyShown) {
          stickyShown = show;
          sticky.classList.toggle('show', show);
          sticky.setAttribute('aria-hidden', show ? 'false' : 'true');
          sticky.querySelectorAll('a').forEach(function(a) {
            a.tabIndex = show ? 0 : -1;
          });
        }
        // The tour has no sound and no controls: play it while at least a third of it is on screen.
        if (inView(tour, .35)) {
          if (tour.paused) tour.play().catch(function() {});
        } else if (!tour.paused) {
          tour.pause();
        }
      }
      addEventListener('scroll', function() {
        if (!ticking) {
          ticking = true;
          requestAnimationFrame(onScroll);
        }
      }, {
        passive: true
      });
      addEventListener('resize', onScroll);
      onScroll();

      tour.addEventListener('play', function() {
        if (!tourStarted) {
          tourStarted = true;
          track('video_start', {
            video: 'product_tour'
          });
        }
      });
      tour.addEventListener('timeupdate', function() {
        if (!tourDone && tour.duration && tour.currentTime > tour.duration - 1) {
          tourDone = true;
          track('video_complete', {
            video: 'product_tour'
          });
        }
      });
      var yr = document.getElementById('yr');
      if (yr) yr.textContent = new Date().getFullYear();
    })();
