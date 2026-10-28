/* Nigeria PPC page (temp-ppc-ng.php); loaded deferred, so the DOM is ready */
(function () {
	'use strict';

	var HR = window.hrPpcNg || {};
	var q;
	try {
		q = new URLSearchParams(location.search);
	} catch (e) {
		q = null;
	}
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var header = document.querySelector('.ppc-ng .top');

	function track(event, sendTo, data) {
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push(Object.assign({ event: event }, data || {}));
		if (sendTo && typeof window.gtag === 'function') {
			window.gtag('event', 'conversion', { send_to: sendTo });
		}
	}

	// Ad match: headline follows ?kw=hms|emr|clinic|billing, else guessed from utm_term.
	var V = {
		hms: { e: 'Hospital management software for Nigeria' },
		emr: { e: 'EMR software in Nigeria', h: 'Patient records, billing and HMO claims, <span>all in one simple system.</span>', s: 'Doctors see every patient’s history on one screen. Tests and drugs they order go straight to the bill and the HMO claim. It runs online, so there’s no server to buy.' },
		clinic: { e: 'Clinic management software in Nigeria', h: 'Run your clinic from one screen. <span>Get your HMO claims paid.</span>', s: 'Appointments, patient records, prescriptions, billing and HMO claims in one simple system. It runs online, so there’s no server to buy. Most clinics start in under two weeks.' },
		billing: { e: 'Hospital billing software in Nigeria', h: 'Bill every service. <span>Get your HMO claims paid.</span>', s: 'Everything the doctor orders goes on the bill. See what each cashier collected by cash, POS and transfer, and what each HMO still owes you.' }
	};
	var kw = (q && q.get('kw') || '').toLowerCase();
	var term = (q && q.get('utm_term') || '').toLowerCase();
	if (!V[kw]) {
		kw = /emr|ehr|electronic|medical record|health record/.test(term) ? 'emr'
			: /clinic|practice/.test(term) ? 'clinic'
				: /billing|accounting|hmo|claim|nhia/.test(term) ? 'billing'
					: 'hms';
	}
	if (kw !== 'hms') {
		var v = V[kw];
		var eyebrow = document.getElementById('kwEyebrow');
		var h1 = document.getElementById('kwH1');
		var sub = document.getElementById('kwSub');
		if (eyebrow) {
			eyebrow.textContent = v.e;
		}
		if (h1) {
			h1.innerHTML = v.h;
		}
		if (sub) {
			sub.textContent = v.s;
		}
	}

	// WhatsApp clicks (links are built in temp-ppc-ng.php).
	document.querySelectorAll('.ppc-ng .js-wa').forEach(function (a) {
		a.addEventListener('click', function () {
			track('hr_whatsapp_click', HR.adsConversionWa, { kw_variant: kw });
		});
	});

	// Lead conversion when CF7 sends (hero card or popup).
	document.addEventListener('wpcf7mailsent', function () {
		track('hr_lead_submit', HR.adsConversion, { kw_variant: kw });
	});

	// Adds .away to el while any of the target sections is on screen.
	function awayWhileVisible(el, targets) {
		targets = targets.filter(Boolean);
		if (!el || !targets.length || !('IntersectionObserver' in window)) {
			return;
		}
		var onScreen = [];
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) {
				var i = onScreen.indexOf(e.target);
				if (e.isIntersecting && i < 0) {
					onScreen.push(e.target);
				} else if (!e.isIntersecting && i > -1) {
					onScreen.splice(i, 1);
				}
			});
			el.classList.toggle('away', onScreen.length > 0);
		}, { threshold: 0.25 });
		targets.forEach(function (t) {
			io.observe(t);
		});
	}
	var finalCta = document.querySelector('.ppc-ng .final');
	var foot = document.querySelector('.ppc-ng .foot');
	// Mobile bar: away over the hero form, final CTA and footer; desktop WhatsApp: away over final CTA and footer.
	awayWhileVisible(document.getElementById('mbar'), [document.getElementById('demo'), finalCta, foot]);
	awayWhileVisible(document.querySelector('.ppc-ng .wa-float'), [finalCta, foot]);

	// Header: hide on scroll down, show on scroll up (8px dead zone for touch jitter).
	if (header) {
		var lastY = window.scrollY;
		var ticking = false;
		var updateHeader = function () {
			ticking = false;
			var y = Math.max(0, window.scrollY);
			var delta = y - lastY;
			if (Math.abs(delta) < 8) {
				return;
			}
			header.classList.toggle('is-hidden', delta > 0 && y > header.offsetHeight);
			lastY = y;
		};
		window.addEventListener('scroll', function () {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(updateHeader);
			}
		}, { passive: true });
		header.addEventListener('focusin', function () {
			header.classList.remove('is-hidden');
		});
	}

	// FAQ: one answer open at a time (fallback for browsers without <details name>).
	var faqs = Array.prototype.slice.call(document.querySelectorAll('.ppc-ng .faq details'));
	faqs.forEach(function (d) {
		d.addEventListener('toggle', function () {
			if (d.open) {
				faqs.forEach(function (other) {
					if (other !== d) {
						other.open = false;
					}
				});
			}
		});
	});

	// Stepper form: after a step change, scroll only if the card top is hidden (not in the popup).
	function keepCardInView(form) {
		var card = form.closest('.form-card');
		if (!card || form.closest('#myPopup')) {
			return;
		}
		var headerH = header ? header.offsetHeight : 0;
		var top = card.getBoundingClientRect().top;
		if (top < headerH) {
			window.scrollBy({ top: top - headerH - 12, behavior: reduceMotion ? 'auto' : 'smooth' });
		}
	}

	document.querySelectorAll('.lead-form.hr-demo-form').forEach(function (form) {
		var step1 = form.querySelector('.hr-step-1');
		var step2 = form.querySelector('.hr-step-2');
		var bedInput = form.querySelector('[name="bed_size"]');
		var selectedBed = form.querySelector('.hr-selected-bed-value');
		if (!step1 || !step2) {
			return;
		}
		form.querySelectorAll('.hr-bed-option').forEach(function (option) {
			option.addEventListener('click', function () {
				var bed = option.getAttribute('data-bed');
				if (!bed) {
					return;
				}
				if (bedInput) {
					bedInput.value = bed;
				}
				if (selectedBed) {
					selectedBed.textContent = bed;
				}
				step1.style.display = 'none';
				step2.style.display = 'block';
				keepCardInView(form);
			});
		});
		form.querySelectorAll('.hr-back-step, .hr-change-bed').forEach(function (btn) {
			btn.addEventListener('click', function () {
				step2.style.display = 'none';
				step1.style.display = 'block';
				keepCardInView(form);
			});
		});
	});

	// Mobile number: +234 box plus 10 digits; CF7 receives "+234 8031234567" (checked by cf7_country_rules()).
	var CODE = '+234';
	var MAX = 10;
	function digitsOnly(value) {
		var d = value.replace(/\D/g, '').replace(/^0+/, '');
		return d.length > MAX ? d.slice(-MAX) : d;
	}
	document.querySelectorAll('.lead-form.hr-demo-form input[name="your-number"]').forEach(function (input) {
		var form = input.closest('form');
		var box = document.createElement('span');
		var cc = document.createElement('span');
		box.className = 'hr-phone';
		cc.className = 'hr-cc';
		cc.textContent = CODE;
		cc.setAttribute('aria-hidden', 'true');
		input.parentNode.insertBefore(box, input);
		box.appendChild(cc);
		box.appendChild(input);

		input.value = digitsOnly(input.value);
		input.placeholder = '803 123 4567';
		input.setAttribute('inputmode', 'numeric');
		input.setAttribute('aria-label', 'Mobile number, Nigeria ' + CODE);

		// Typing stops at 10 digits; paste or autofill keeps the last 10.
		input.addEventListener('input', function (e) {
			var next = e.inputType === 'insertText'
				? input.value.replace(/\D/g, '').replace(/^0+/, '').slice(0, MAX)
				: digitsOnly(input.value);
			if (next !== input.value) {
				input.value = next;
			}
		});

		if (form) {
			form.addEventListener('formdata', function (e) {
				var d = digitsOnly(input.value);
				e.formData.set('your-number', d ? CODE + ' ' + d : '');
			});
		}
	});
})();
