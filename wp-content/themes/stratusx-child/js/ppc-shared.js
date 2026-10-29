/* Shared PPC behaviour for Nigeria and Kenya; retains per-page settings and script handles. */
(function () {
	'use strict';

	var root = document.querySelector('body.ppc-ke, body.ppc-ng');
	if (!root || root.dataset.hrPpcReady === '1') { return; }
	root.dataset.hrPpcReady = '1';
	var isKenya = root.classList.contains('ppc-ke');
	var HR = (isKenya ? window.hrPpcKe : window.hrPpcNg) || {};
	var q;
	try {
		q = new URLSearchParams(location.search);
	} catch (e) {
		q = null;
	}
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var header = root.querySelector('.top');

	function track(event, sendTo, data) {
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push(Object.assign({ event: event }, data || {}));
		if (sendTo && typeof window.gtag === 'function') {
			window.gtag('event', 'conversion', { send_to: sendTo });
		}
	}

	// Broken images: show the text fallback (.fb), initials (data-fallback) or remove the image (data-fallback-remove).
	function imgFallback(img) {
		var parent = img.parentNode;
		if (!parent) {
			return;
		}
		if (img.hasAttribute('data-fallback-remove')) {
			img.remove();
		} else if (parent.querySelector('.fb')) {
			parent.classList.add('noimg');
		} else if (img.getAttribute('data-fallback')) {
			parent.textContent = img.getAttribute('data-fallback');
		}
	}
	(isKenya ? root.querySelectorAll('img') : []).forEach(function (img) {
		if (img.complete && img.naturalWidth === 0 && img.getAttribute('src')) {
			imgFallback(img);
		} else {
			img.addEventListener('error', function () {
				imgFallback(img);
			}, { once: true });
		}
	});

	// Ad match: country-specific variants and utm_term rules.
	// Defaults: software for Kenya, hms for Nigeria.
	var V = isKenya ? {
		software: {},
		hms: { h: 'Hospital management system that gets your <span>SHA claims through</span>' },
		hmis: { h: 'A DHA-certified HMIS that gets your <span>SHA claims through</span>' },
		emr: { h: 'EMR system with your patient records and <span>SHA claims in one place</span>', s: 'Doctors and clinical officers see every patient’s history on one screen. Tests and drugs they order go straight to the bill and the SHA claim. M‑Pesa and eTIMS are built in.' },
		clinic: { h: 'Clinic management system that gets your <span>SHA claims through</span>', s: 'Registration, consultation, pharmacy, M‑Pesa payments and SHA claims in one simple system. We set it up and train your staff.' },
		sha: { h: 'Move your SHA claims from the Provider Portal to a <span>DHA-certified HMIS</span>', s: 'Check cover, request pre-authorisation and submit claims from the patient’s bill. We set it up, move your records and train your staff.' }
	} : {
		hms: { e: 'Hospital management software for Nigeria' },
		emr: { e: 'EMR software in Nigeria', h: 'Patient records, billing and HMO claims, <span>all in one simple system.</span>', s: 'Doctors see every patient’s history on one screen. Tests and drugs they order go straight to the bill and the HMO claim. It runs online, so there’s no server to buy.' },
		clinic: { e: 'Clinic management software in Nigeria', h: 'Run your clinic from one screen. <span>Get your HMO claims paid.</span>', s: 'Appointments, patient records, prescriptions, billing and HMO claims in one simple system. It runs online, so there’s no server to buy. Most clinics start in under two weeks.' },
		billing: { e: 'Hospital billing software in Nigeria', h: 'Bill every service. <span>Get your HMO claims paid.</span>', s: 'Everything the doctor orders goes on the bill. See what each cashier collected by cash, POS and transfer, and what each HMO still owes you.' }
	};
	var kw = (q && q.get('kw') || '').toLowerCase();
	var term = (q && q.get('utm_term') || '').toLowerCase();
	if (!V[kw]) {
		if (isKenya) {
			kw = /\bsha\b|provider portal|dha|certif/.test(term) ? 'sha'
			: /hmis/.test(term) ? 'hmis'
				: /emr|ehr|electronic|medical record|health record/.test(term) ? 'emr'
					: /clinic|practice|patient management/.test(term) ? 'clinic'
						: /system/.test(term) ? 'hms'
							: 'software';
		} else {
			kw = /emr|ehr|electronic|medical record|health record/.test(term) ? 'emr'
			: /clinic|practice/.test(term) ? 'clinic'
				: /billing|accounting|hmo|claim|nhia/.test(term) ? 'billing'
					: 'hms';
		}
	}
	var v = V[kw];
	var eyebrow = document.getElementById('kwEyebrow');
	if (!isKenya && kw !== 'hms' && eyebrow) { eyebrow.textContent = v.e; }
	var h1 = document.getElementById('kwH1');
	var sub = document.getElementById('kwSub');
	if (v.h && h1) {
		h1.innerHTML = v.h;
	}
	if (v.s && sub) {
		sub.textContent = v.s;
	}

	if (!isKenya) {
	// WhatsApp buttons: shown only when a Nigerian number is set.
	var waNum = String(HR.waNumber || '').replace(/\D/g, '');
	if (/^234\d{10}$/.test(waNum)) {
		var waHref = 'https://wa.me/' + waNum + '?text=' + encodeURIComponent(HR.waText || '');
		document.querySelectorAll('.ppc-ng .js-wa').forEach(function (a) {
			a.href = waHref;
			a.hidden = false;
			a.addEventListener('click', function () {
				track('hr_whatsapp_click', HR.adsConversionWa);
			});
		});
	}

	}

	// Lead conversion when CF7 sends (hero card or popup).
	document.addEventListener('wpcf7mailsent', function () {
		track('hr_lead_submit', HR.adsConversion, { kw_variant: kw });
	});

	// Mobile bar: hidden while the hero form, final CTA or footer is on screen.
	var bar = document.getElementById('mbar');
	var barTargets = [
		document.getElementById('demo'),
		root.querySelector('.final'),
		root.querySelector('.foot')
	].filter(Boolean);
	if (bar && barTargets.length && 'IntersectionObserver' in window) {
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
			bar.classList.toggle('away', onScreen.length > 0);
		}, { threshold: 0.25 });
		barTargets.forEach(function (t) {
			io.observe(t);
		});
	}

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
	var faqs = Array.prototype.slice.call(root.querySelectorAll('.faq details'));
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

	// FAQ: phones show 4 questions; "More questions" shows the rest.
	var faqMore = document.getElementById('faqMore');
	if (isKenya && faqMore) {
		faqMore.addEventListener('click', function () {
			var faq = faqMore.closest('.faq');
			if (faq) {
				faq.classList.add('all');
			}
		});
	}

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
				if (isKenya) { track('hr_form_step1', null, { bed_size: bed }); }
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

	// Country phone formatting: Kenya +254 / 9 digits; Nigeria +234 / 10 digits.
	// Accepts 0712 345 678, 712 345 678, 0110 123 456, 254712345678 or +254 712 345 678.
	var CODE = isKenya ? '+254' : '+234';
	var MAX = isKenya ? 9 : 10;
	function digitsOnly(value) {
		var d = value.replace(/\D/g, '');
		if (isKenya && d.indexOf('254') === 0 && d.length > MAX) {
			d = d.slice(3);
		}
		d = d.replace(/^0+/, '');
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
		input.placeholder = isKenya ? '712 345 678' : '803 123 4567';
		input.setAttribute('inputmode', 'numeric');
		input.setAttribute('aria-label', 'Mobile number, ' + (isKenya ? 'Kenya ' : 'Nigeria ') + CODE);

		// Typing is limited to the national length; paste/autofill accepts country prefixes.
		function typed(value) {
			var d = value.replace(/\D/g, '');
			if (isKenya && d.indexOf('254') === 0 && d.length > MAX) {
				d = d.slice(3);
			}
			return d.replace(/^0+/, '').slice(0, MAX);
		}
		input.addEventListener('input', function (e) {
			var next = e.inputType === 'insertText' ? typed(input.value) : digitsOnly(input.value);
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
