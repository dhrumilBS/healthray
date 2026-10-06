/* ==========================================================================
   Healthray – theme script (cleaned up)
   ========================================================================== */

/* **** Shared helpers **** */

function getSiteData() {
	return (typeof window.siteData !== 'undefined' && window.siteData) ? window.siteData : null;
}

function getHomeUrl() {
	var data = getSiteData();
	var home = (data && data.homeUrl) || window.location.origin;
	return String(home).replace(/\/+$/, '');
}

/* **** 1. Nested list levels (level-1 / level-2 / level-3 + ARIA roles) **** */

function initNestedLists() {
	var parent = '.content-editor ol.main-list>li';

	for (var level = 1; level <= 3; level++) {
		var selector = parent + '>ol, ' + parent + '>ul';

		document.querySelectorAll(selector).forEach(function (el) {
			el.classList.add('level-' + level);
			el.setAttribute('role', 'list-' + level);
		});

		parent += '>.level-' + level + '>li';
	}
}

/* **** 2. Lead popup (scroll trigger + CTA buttons) with basic a11y **** */

function initPopup() {
	var data = getSiteData();
	if (!data) return; // popup needs siteData; everything else still runs

	var currentPageId = Number(data.pageId);
	var isLoggedIn = !!data.isLoggedIn;
	var specificPageIds = (data.pageIds || []).map(Number);

	var popupTriggered = specificPageIds.indexOf(currentPageId) !== -1 || isLoggedIn;

	var popupBg = document.getElementById('popupBackground');
	var popup = document.getElementById('myPopup');
	var popupCloseBtn = document.getElementById('closePopup');

	var lastFocused = null;
	var previousBodyOverflow = '';
	var triggerPoint = Infinity;
	var scrollListening = false;

	if (popup) {
		popup.setAttribute('role', 'dialog');
		popup.setAttribute('aria-modal', 'true');
		popup.setAttribute('aria-hidden', 'true');
		if (!popup.hasAttribute('tabindex')) popup.setAttribute('tabindex', '-1');
	}

	function isPopupOpen() {
		return !!popup && popup.classList.contains('model-open');
	}

	function getFocusable() {
		if (!popup) return [];
		return Array.prototype.slice.call(
			popup.querySelectorAll(
				'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
			)
		).filter(function (el) {
			return el.offsetParent !== null;
		});
	}

	function computeTriggerPoint() {
		// The popup can only fire if the page is at least twice the viewport tall,
		// otherwise "half the page height" is unreachable / triggers instantly.
		var height = document.documentElement.scrollHeight;
		triggerPoint = height > window.innerHeight * 2 ? height / 2 : Infinity;
	}

	function handleScroll() {
		if (popupTriggered) return;
		if (window.scrollY >= triggerPoint) openPopup();
	}

	function startScrollWatch() {
		if (popupTriggered || scrollListening) return;
		computeTriggerPoint();
		window.addEventListener('scroll', handleScroll, { passive: true });
		window.addEventListener('resize', computeTriggerPoint);
		window.addEventListener('load', computeTriggerPoint);
		scrollListening = true;
	}

	function stopScrollWatch() {
		if (!scrollListening) return;
		window.removeEventListener('scroll', handleScroll);
		window.removeEventListener('resize', computeTriggerPoint);
		window.removeEventListener('load', computeTriggerPoint);
		scrollListening = false;
	}

	function openPopup() {
		if (!popupBg || !popup || isPopupOpen()) return;

		lastFocused = document.activeElement;

		popupBg.style.display = 'block';
		popup.style.display = 'block';
		popup.classList.add('model-open');
		popup.setAttribute('aria-hidden', 'false');

		previousBodyOverflow = document.body.style.overflow;
		document.body.style.overflow = 'hidden';

		popupTriggered = true;
		stopScrollWatch();

		var focusable = getFocusable();
		(focusable[0] || popup).focus();
	}

	function closePopup() {
		if (!isPopupOpen()) return;

		if (popupBg) popupBg.style.display = 'none';
		if (popup) {
			popup.style.display = 'none';
			popup.classList.remove('model-open');
			popup.setAttribute('aria-hidden', 'true');
		}

		document.body.style.overflow = previousBodyOverflow;

		if (lastFocused && typeof lastFocused.focus === 'function') {
			lastFocused.focus();
		}
		lastFocused = null;
	}

	startScrollWatch();

	if (popupCloseBtn) {
		popupCloseBtn.addEventListener('click', closePopup);
	}

	if (popupBg) {
		popupBg.addEventListener('click', closePopup);
	}

	document.querySelectorAll('.hr-cta-btn, a[href*="calendly.com/healthray/healthray-technologies-lead-meeting"]').forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();

			var menuToggle = document.querySelector('.mega-menu-toggle');
			if (menuToggle && menuToggle.classList.contains('mega-menu-open')) {
				var megaClose = document.querySelector('.mega-close');
				if (megaClose) megaClose.click();
			}

			openPopup();
		});
	});

	document.addEventListener('keydown', function (e) {
		if (!isPopupOpen()) return;

		if (e.key === 'Escape') {
			closePopup();
			return;
		}

		// Focus trap
		if (e.key === 'Tab') {
			var focusable = getFocusable();
			if (!focusable.length) {
				e.preventDefault();
				popup.focus();
				return;
			}

			var first = focusable[0];
			var last = focusable[focusable.length - 1];

			if (e.shiftKey && (document.activeElement === first || document.activeElement === popup)) {
				e.preventDefault();
				last.focus();
			} else if (!e.shiftKey && document.activeElement === last) {
				e.preventDefault();
				first.focus();
			}
		}
	});
}

/* **** 3. Contact Form 7: button lock / reset / sent state **** */

function getBtn(formEl) {
	if (!formEl || !formEl.querySelector) return null;
	return formEl.querySelector('input[type="submit"], button[type="submit"]');
}

function lockBtn(btn) {
	if (!btn) return;
	btn.disabled = true;
	btn.classList.add('sending');

	if (btn.tagName === 'INPUT') {
		btn.dataset.original = btn.value;
		btn.value = 'Sending...';
	} else {
		btn.dataset.original = btn.innerHTML;
		btn.innerHTML = 'Sending...';
	}
}

function resetBtn(btn) {
	if (!btn) return;
	btn.disabled = false;
	btn.classList.remove('sending');

	if (btn.tagName === 'INPUT') {
		btn.value = btn.dataset.original || 'Submit';
	} else {
		btn.innerHTML = btn.dataset.original || 'Submit';
	}
}

function markBtnSent(btn) {
	if (!btn) return;
	btn.disabled = true;

	if (btn.tagName === 'INPUT') {
		btn.value = '\u2713 Sent';
	} else {
		btn.innerHTML = '&#10003; Sent';
	}

	btn.classList.remove('sending');
	btn.classList.add('sent');
}

function initFormSubmitGuards() {
	document.querySelectorAll('.wpcf7 form').forEach(function (form) {
		form.addEventListener('submit', function (e) {
			var btn = getBtn(this);
			if (!btn) return;

			if (btn.classList.contains('sending') || btn.disabled) {
				e.preventDefault();
				e.stopImmediatePropagation();
				return false;
			}
			lockBtn(btn);
		});
	});
}

function initFormButtons() {
	['wpcf7invalid', 'wpcf7mailfailed', 'wpcf7spam', 'wpcf7aborted'].forEach(function (evt) {
		document.addEventListener(evt, function (e) {
			resetBtn(getBtn(e.target));
		});
	});

	document.addEventListener('wpcf7mailsent', function (e) {
		markBtnSent(getBtn(e.target));
	});
}

/* **** 4. Contact Form 7: post-submit behaviour (whitepaper PDF / thank-you redirect) **** */

var WHITEPAPER_FORM_ID = '61816';

function showWhitepaperLink(wrapper, url) {
	var old = wrapper.querySelector('.pdf-download-message');
	if (old) old.remove();

	var msg = document.createElement('div');
	msg.className = 'pdf-download-message';

	var link = document.createElement('a');
	link.href = url;
	link.target = '_blank';
	link.rel = 'noopener';
	link.className = 'pdf-btn';
	link.textContent = 'Download again';

	msg.appendChild(link);
	wrapper.appendChild(msg);
}

function openWhitepaper(wrapper, url) {
	// Always show the link; browsers often block window.open after async work.
	showWhitepaperLink(wrapper, url);
	window.open(url, '_blank');
}

function handleWhitepaperSubmit(event) {
	var form = event && event.target && event.target.closest ? event.target : null;
	var selector = '[data-form-id="' + WHITEPAPER_FORM_ID + '"]';
	var wrapper = (form && form.closest(selector)) || document.querySelector(selector);
	if (!wrapper) {
		console.warn('Wrapper missing for whitepaper form');
		return;
	}

	// The PDF comes back with the form response itself (hr_whitepaper_feedback_response()
	// in lib/whitepaper-helpers.php), so no second request and no nonce is needed.
	var response = event && event.detail ? event.detail.apiResponse : null;
	var download = response ? response.whitepaper_download : null;

	if (download && download.url) {
		openWhitepaper(wrapper, download.url);
		return;
	}

	// Fallback: the nonce-checked admin-ajax lookup in lib/fn-admin.php.
	var postId = wrapper.getAttribute('data-post-id');
	var nonce = wrapper.getAttribute('data-nonce');
	var site = getSiteData() || {};
	var ajaxObj = window.ajax_obj || {};
	var ajaxUrl = site.ajaxUrl || ajaxObj.ajax_url || ajaxObj.ajaxurl || ajaxObj.url || window.ajaxurl;

	if (!ajaxUrl || !postId || !nonce) {
		alert('Invalid form data. Please reload and try again.');
		return;
	}

	fetch(ajaxUrl, {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: new URLSearchParams({
			action: 'get_whitepaper_pdf',
			nonce: nonce,
			post_id: postId
		})
	})
		.then(function (res) {
			return res.ok ? res.json() : Promise.reject('Network error');
		})
		.then(function (data) {
			if (!(data && data.success && data.data && data.data.url)) return;

			openWhitepaper(wrapper, data.data.url);
		})
		.catch(function (err) {
			console.error('Fetch error:', err);
		});
}

function redirectToThankYou() {
	try {
		var params = new URLSearchParams(window.location.search);
		var source = params.get('utm_source') || '';
		var home = getHomeUrl();
		var target = home + '/thank-you/';

		if (source === 'FacebookAds') {
			target = home + '/thank-you-fb/';
		} else if (source === 'OpenAIAds') {
			target = home + '/thank-you-openai/';
		}

		window.location.href = target;
	} catch (err) {
		console.error('Redirect error:', err);
	}
}

function initFormSubmitHandling() {
	document.addEventListener('wpcf7mailsent', function (event) {
		var formId = String(event.detail.contactFormId);

		if (formId === WHITEPAPER_FORM_ID) {
			handleWhitepaperSubmit(event);
			return;
		}

		// Opt-out: add data-no-redirect to the .wpcf7 wrapper of any form
		// (newsletter, small popup forms, etc.) that should NOT redirect.
		var wrapper = event.target && event.target.closest ? event.target.closest('.wpcf7') : null;
		if (wrapper && wrapper.hasAttribute('data-no-redirect')) return;
		if (event.target && event.target.hasAttribute && event.target.hasAttribute('data-no-redirect')) return;

		setTimeout(redirectToThankYou, 400);
	}, false);
}

/* **** 5. "Other" field toggle for selects **** */

function initOtherFieldToggle() {
	document.addEventListener('change', function (e) {
		if (!e.target || e.target.tagName !== 'SELECT') return;

		var name = e.target.getAttribute('name');
		if (name !== 'your-business' && name !== 'speciality') return;

		var form = e.target.closest('form');
		var wrap = form ? form.querySelector('.other-field-wrap[data-for="' + name + '"]') : null;
		if (!wrap) return;

		var input = wrap.querySelector('input');

		if (e.target.value === 'Other') {
			wrap.classList.add('is-visible');
			if (input) input.setAttribute('required', 'required');
		} else {
			wrap.classList.remove('is-visible');
			if (input) {
				input.removeAttribute('required');
				input.value = '';
			}
		}
	});

	// CF7 resets the form after a successful submit without firing "change"
	document.addEventListener('wpcf7reset', function (e) {
		if (!e.target || !e.target.querySelectorAll) return;
		e.target.querySelectorAll('.other-field-wrap').forEach(function (wrap) {
			wrap.classList.remove('is-visible');
			var input = wrap.querySelector('input');
			if (input) input.removeAttribute('required');
		});
	});
}

/* **** 6. Scroll reveal **** */

function initReveal() {
	var reveals = document.querySelectorAll('.reveal');
	if (!reveals.length) return;

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (!('IntersectionObserver' in window) || reduceMotion) {
		reveals.forEach(function (el) { el.classList.add('in'); });
		return;
	}

	var io = new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (entry.isIntersecting) {
				entry.target.classList.add('in');
				io.unobserve(entry.target);
			}
		});
	}, { threshold: 0.15 });

	reveals.forEach(function (el) { io.observe(el); });
}

/* **** 7. Preserve UTM / click-ID parameters on internal links
   Example: /page/?utm_source=FacebookAds  ->  /contact/?utm_source=FacebookAds **** */

function initTrackingParamPreserver() {
	var TRACKING_PARAMS = [
		'utm_source',
		'utm_medium',
		'utm_campaign',
		'utm_term',
		'utm_content',
		'utm_placement',
		'gclid',
		'fbclid',
		'msclkid'
	];

	var FILE_EXTENSION = /\.(pdf|docx?|xlsx?|pptx?|zip|rar|7z|csv|txt|png|jpe?g|gif|webp|svg|mp4|mp3)$/i;

	function normalizeHost(host) {
		return String(host).replace(/^www\./i, '').toLowerCase();
	}

	document.addEventListener('click', function (event) {
		var link = event.target.closest ? event.target.closest('a[href]') : null;
		if (!link) return;

		var href = link.getAttribute('href');
		if (!href) return;

		// Ignore special links
		if (
			href.charAt(0) === '#' ||
			href.indexOf('javascript:') === 0 ||
			href.indexOf('mailto:') === 0 ||
			href.indexOf('tel:') === 0
		) {
			return;
		}

		var currentParams = new URLSearchParams(window.location.search);

		var hasTrackingParams = TRACKING_PARAMS.some(function (param) {
			return !!currentParams.get(param);
		});
		if (!hasTrackingParams) return;

		var url;
		try {
			url = new URL(href, window.location.href);
		} catch (error) {
			return;
		}

		if (url.protocol !== 'http:' && url.protocol !== 'https:') return;

		// Only internal links (treat www and non-www as the same site)
		if (normalizeHost(url.hostname) !== normalizeHost(window.location.hostname)) return;

		// Skip downloadable files
		if (FILE_EXTENSION.test(url.pathname)) return;

		var changed = false;
		TRACKING_PARAMS.forEach(function (param) {
			var value = currentParams.get(param);
			if (value && !url.searchParams.has(param)) {
				url.searchParams.set(param, value);
				changed = true;
			}
		});

		if (changed) link.href = url.toString();
	}, true);
}

/* **** 8. "More categories" dropdown **** */

function initCategoryMoreDropdown() {
	var groups = Array.prototype.slice.call(document.querySelectorAll('.more-category'));
	if (!groups.length) return;

	var EDGE_GUTTER = 12;
	var MIN_VISIBLE = 180;
	var openGroup = null;
	var reflowFrame;

	function parts(group) {
		return {
			button: group.querySelector('.blog-category-more-link'),
			panel: group.querySelector('.blog-category-more-list')
		};
	}

	function items(group) {
		return Array.prototype.slice.call(group.querySelectorAll('.blog-category-more-scroll .cat-link'));
	}

	function isOpen(group) {
		var button = group.querySelector('.blog-category-more-link');
		return !!button && button.getAttribute('aria-expanded') === 'true';
	}

	function place(group) {
		var el = parts(group);
		if (!el.button || !el.panel) return;

		el.panel.style.setProperty('--hr-more-shift', '0px');

		var panelBox = el.panel.getBoundingClientRect();
		var shift = 0;

		if (panelBox.left < EDGE_GUTTER) {
			shift = EDGE_GUTTER - panelBox.left;
		} else if (panelBox.right > window.innerWidth - EDGE_GUTTER) {
			shift = (window.innerWidth - EDGE_GUTTER) - panelBox.right;
		}

		if (shift) {
			el.panel.style.setProperty('--hr-more-shift', Math.round(shift) + 'px');
		}

		var triggerBox = el.button.getBoundingClientRect();
		var roomBelow = window.innerHeight - triggerBox.bottom;
		var fitsBelow = roomBelow >= Math.min(panelBox.height, MIN_VISIBLE);
		var fitsAbove = triggerBox.top >= panelBox.height + EDGE_GUTTER;

		group.classList.toggle('is-flipped', !fitsBelow && fitsAbove);
	}

	function open(group) {
		if (openGroup && openGroup !== group) close(openGroup, false);

		var el = parts(group);
		if (!el.button || !el.panel) return;

		el.panel.hidden = false;
		el.button.setAttribute('aria-expanded', 'true');
		place(group);
		group.classList.add('is-open');

		var current = el.panel.querySelector('.cat-link.active');
		var scroller = el.panel.querySelector('.blog-category-more-scroll');

		if (current && scroller) {
			scroller.scrollTop = Math.max(0, current.offsetTop - 8);
		}

		openGroup = group;
	}

	function close(group, returnFocus) {
		var el = parts(group);
		if (!el.button || !el.panel) return;

		group.classList.remove('is-open', 'is-flipped');
		el.button.setAttribute('aria-expanded', 'false');
		el.panel.hidden = true;

		if (openGroup === group) openGroup = null;
		if (returnFocus) el.button.focus();
	}

	function moveFocus(group, from, step) {
		var links = items(group);
		if (!links.length) return;

		var index = links.indexOf(from);
		var next;

		if (step === 'first') next = 0;
		else if (step === 'last') next = links.length - 1;
		else if (index === -1) next = 0;
		else next = (index + step + links.length) % links.length;

		links[next].focus();
	}

	groups.forEach(function (group) {
		var el = parts(group);
		if (!el.button || !el.panel) return;

		el.button.addEventListener('click', function (event) {
			event.preventDefault();

			if (isOpen(group)) {
				close(group, false);
			} else {
				open(group);
			}
		});

		el.button.addEventListener('keydown', function (event) {
			if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') return;

			event.preventDefault();
			if (!isOpen(group)) open(group);
			moveFocus(group, null, event.key === 'ArrowDown' ? 'first' : 'last');
		});

		el.panel.addEventListener('keydown', function (event) {
			switch (event.key) {
				case 'ArrowDown':
					event.preventDefault();
					moveFocus(group, document.activeElement, 1);
					break;
				case 'ArrowUp':
					event.preventDefault();
					moveFocus(group, document.activeElement, -1);
					break;
				case 'Home':
					event.preventDefault();
					moveFocus(group, null, 'first');
					break;
				case 'End':
					event.preventDefault();
					moveFocus(group, null, 'last');
					break;
			}
		});

		group.addEventListener('focusout', function (event) {
			if (event.relatedTarget && !group.contains(event.relatedTarget)) {
				close(group, false);
			}
		});
	});

	document.addEventListener('click', function (event) {
		if (openGroup && !openGroup.contains(event.target)) close(openGroup, false);
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape' && openGroup) close(openGroup, true);
	});

	window.addEventListener('resize', function () {
		if (!openGroup) return;

		cancelAnimationFrame(reflowFrame);
		reflowFrame = requestAnimationFrame(function () {
			if (openGroup) place(openGroup);
		});
	});
}

/* **** Boot **** */

// Listeners that don't depend on the DOM being ready are registered immediately
// so CF7 events are never missed.
initFormSubmitHandling();
initFormButtons();
initOtherFieldToggle();
initTrackingParamPreserver();

document.addEventListener('DOMContentLoaded', function () {
	initNestedLists();
	initCategoryMoreDropdown();
	initFormSubmitGuards();
	initPopup();
	initReveal();
});