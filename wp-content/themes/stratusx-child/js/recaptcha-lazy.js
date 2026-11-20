/**
 * reCAPTCHA v3 for Contact Form 7, loaded on demand.
 *
 * CF7 normally loads Google's api.js (~350 KB of JavaScript) on every page view. This replaces CF7's own
 * reCAPTCHA script (handle "wpcf7-recaptcha", which functions.php dequeues) with the same behaviour, except
 * that api.js only loads once the visitor starts interacting with the page:
 *   - first scroll / mouse move / tap / key press anywhere -> load api.js and record a "homepage" action
 *   - a field changes -> refresh the token (as CF7 does)
 *   - the form is submitted -> always get a fresh token first, then let CF7 send it
 * The server still verifies a token on every submission, so spam protection is unchanged.
 * Settings (sitekey, script URL, action names) come from the inline `hrRecaptcha` object added in functions.php.
 */
(function () {
	'use strict';

	var cfg = window.hrRecaptcha;
	if (!cfg || !cfg.sitekey || !cfg.src) {
		return;
	}
	var actions = cfg.actions || {};
	var formAction = actions.contactform || 'contactform';
	var loading = null;

	function load() {
		if (loading) {
			return loading;
		}
		loading = new Promise(function (resolve, reject) {
			var s = document.createElement('script');
			s.src = cfg.src;
			s.async = true;
			s.onload = function () {
				window.grecaptcha.ready(resolve);
			};
			s.onerror = function () {
				loading = null; // allow a retry on the next interaction
				reject(new Error('reCAPTCHA failed to load'));
			};
			document.head.appendChild(s);
		});
		return loading;
	}

	function execute(action) {
		return load()
			.then(function () {
				return window.grecaptcha.execute(cfg.sitekey, { action: action });
			})
			.then(function (token) {
				// Same event + hidden field CF7's own reCAPTCHA module uses.
				document.dispatchEvent(new CustomEvent('wpcf7grecaptchaexecuted', { detail: { action: action, token: token } }));
				var fields = document.querySelectorAll('form.wpcf7-form input[name="_wpcf7_recaptcha_response"]');
				for (var i = 0; i < fields.length; i++) {
					fields[i].setAttribute('value', token);
					fields[i].value = token;
				}
				return token;
			});
	}

	function inForm(e) {
		return e.target && e.target.closest && e.target.closest('form.wpcf7-form');
	}

	// Start reCAPTCHA on the visitor's FIRST interaction anywhere on the page (scroll, mouse move, touch, key, focus),
	// not only on the form: reCAPTCHA v3 scores a visitor from how they behave on the page, and loading it only at the
	// form gave it too little to go on, so some real visitors scored as bots. Speed tests don't interact, so the page
	// still loads without reCAPTCHA. Like CF7, it records a "homepage" action as soon as it's ready.
	var homeAction = actions.homepage || 'homepage';
	var warmEvents = ['scroll', 'pointermove', 'pointerdown', 'touchstart', 'keydown', 'focusin', 'wheel'];
	function warm() {
		warmEvents.forEach(function (type) {
			window.removeEventListener(type, warm, true);
		});
		execute(homeAction).catch(function (err) {
			console.error(err);
		});
	}
	warmEvents.forEach(function (type) {
		window.addEventListener(type, warm, { capture: true, passive: true });
	});

	// Refresh the token when a field changes, once reCAPTCHA is loaded (CF7 does the same).
	document.addEventListener('change', function (e) {
		if (loading && inForm(e)) {
			execute(formAction).catch(function (err) {
				console.error(err);
			});
		}
	});

	// Get a fresh token right before every submission. CF7 creates window.wpcf7.submit in its own
	// DOMContentLoaded handler; this file loads after CF7's, so this listener runs after it.
	document.addEventListener('DOMContentLoaded', function () {
		if (typeof window.wpcf7 === 'undefined' || typeof window.wpcf7.submit !== 'function') {
			return;
		}
		var submit = window.wpcf7.submit;
		window.wpcf7.submit = function (form, options) {
			execute(formAction)
				.catch(function (err) {
					// If reCAPTCHA can't load (blocked/offline), still send: the server decides, as before.
					console.error(err);
				})
				.then(function () {
					submit(form, options || {});
				});
		};
	});
})();