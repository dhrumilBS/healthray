(function () {
	'use strict';

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var hasIO = 'IntersectionObserver' in window;

	/**
	 * video.play() that ignores the expected AbortError (a pause() interrupting play()).
	 */
	function play(video, label) {
		var p = video.play();
		if (p && p.catch) {
			p.catch(function (err) {
				if (err && err.name !== 'AbortError') {
					console.warn(label + ' could not play:', err);
				}
			});
		}
	}

	function noContextMenu(e) {
		e.preventDefault();
	}

	/* 1. Customer story: plays with sound on tap; small play/pause button after that. */
	(function () {
		var card = document.getElementById('video');
		var cover = document.getElementById('playBtn');
		var video = document.getElementById('storyVideo');
		var btn = document.getElementById('storyBtn');
		if (!card || !cover || !video || !btn) {
			return;
		}

		function sync() {
			card.classList.toggle('is-playing', !video.paused);
			btn.setAttribute('aria-label', video.paused ? 'Play the video' : 'Pause the video');
		}

		video.addEventListener('loadedmetadata', function () {
			if (video.videoWidth && video.videoHeight) {
				card.style.aspectRatio = video.videoWidth + ' / ' + video.videoHeight;
			}
		});
		cover.addEventListener('click', function () {
			card.classList.add('started');
			btn.hidden = false;
			if (video.currentTime < 1) {
				video.currentTime = 0;
			}
			play(video, 'Customer video');
		});
		btn.addEventListener('click', function () {
			if (video.paused) {
				play(video, 'Customer video');
			} else {
				video.pause();
			}
		});
		video.addEventListener('play', sync);
		video.addEventListener('pause', sync);
		video.addEventListener('ended', function () {
			card.classList.remove('started');
			btn.hidden = true;
			sync();
		});
		video.addEventListener('contextmenu', noContextMenu);
	})();

	/* 2. Mobile action bar: hidden while the hero form, the final CTA or the footer is on screen. */
	(function () {
		var bar = document.getElementById('mbar');
		var targets = [
			document.getElementById('demo'),
			document.querySelector('.ppc-hims .final'),
			document.querySelector('.ppc-hims .foot')
		].filter(Boolean);
		if (!bar || !targets.length || !hasIO) {
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
			bar.classList.toggle('away', onScreen.length > 0);
		}, { threshold: 0.25 });
		targets.forEach(function (t) {
			io.observe(t);
		});
	})();

	/* WhatsApp: desktop floating button hidden over the final CTA and footer; clicks go to GTM. */
	(function () {
		var links = document.querySelectorAll('.ppc-hims .js-wa');
		links.forEach(function (a) {
			a.addEventListener('click', function () {
				window.dataLayer = window.dataLayer || [];
				window.dataLayer.push({ event: 'hr_whatsapp_click' });
			});
		});
		var float = document.querySelector('.ppc-hims .wa-float');
		var targets = [
			document.querySelector('.ppc-hims .final'),
			document.querySelector('.ppc-hims .foot')
		].filter(Boolean);
		if (!float || !targets.length || !hasIO) {
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
			float.classList.toggle('away', onScreen.length > 0);
		}, { threshold: 0.25 });
		targets.forEach(function (t) {
			io.observe(t);
		});
	})();

	/* 3. Walkthrough: autoplays muted on screen, pauses off screen; a manual pause sticks; fullscreen button. */
	(function () {
		var box = document.getElementById('player');
		var video = document.getElementById('walkVideo');
		var btn = document.getElementById('walkBtn');
		if (!box || !video || !btn) {
			return;
		}
		var userPaused = false;
		// No autoplay on Data Saver or a 2G connection; the play button still works.
		var conn = navigator.connection || {};
		var lowData = !!conn.saveData || /(^|-)2g$/.test(conn.effectiveType || '');

		function sync() {
			var on = !video.paused;
			box.classList.toggle('is-playing', on);
			btn.setAttribute('aria-label', on ? 'Pause the walkthrough' : 'Play the walkthrough');
		}

		function start() {
			video.preload = 'auto';
			play(video, 'Walkthrough video');
		}

		video.addEventListener('playing', function () {
			box.classList.add('is-ready');
			sync();
		});
		video.addEventListener('play', sync);
		video.addEventListener('pause', sync);
		video.addEventListener('contextmenu', noContextMenu);
		btn.addEventListener('click', function () {
			userPaused = !video.paused;
			if (userPaused) {
				video.pause();
			} else {
				start();
			}
		});

		/*
		 * Fullscreen, in order of support:
		 * 1. Fullscreen API on the video (Chrome, Edge, Firefox, Android, Safari 16.4+)
		 * 2. webkit-prefixed API (older desktop Safari, iPad)
		 * 3. webkitEnterFullscreen: the native iPhone player (iOS has no element fullscreen)
		 * Native controls show while full screen, so the viewer can seek through the whole video.
		 */
		var fsBtn = document.getElementById('walkFs');
		var inFs = false;

		function fsElement() {
			return document.fullscreenElement || document.webkitFullscreenElement || null;
		}

		function setFs(on) {
			inFs = on;
			video.controls = on;
			box.classList.toggle('is-fs', on);
			if (on) {
				// Phones: turn to landscape while full screen (Android Chrome; ignored where unsupported).
				if (screen.orientation && screen.orientation.lock) {
					screen.orientation.lock('landscape').catch(function () { });
				}
			} else {
				if (screen.orientation && screen.orientation.unlock) {
					try {
						screen.orientation.unlock();
					} catch (e) { }
				}
				// Paused in the full-screen player = keep it paused here.
				userPaused = video.paused;
				sync();
			}
		}

		function iosFullscreen() {
			if (!video.webkitEnterFullscreen) {
				return false;
			}
			try {
				video.webkitEnterFullscreen();
			} catch (e) {
				// Not loaded far enough yet: try again as soon as the size is known.
				video.addEventListener('loadedmetadata', function () {
					try {
						video.webkitEnterFullscreen();
					} catch (e2) { }
				}, { once: true });
			}
			return true;
		}

		function enterFullscreen() {
			userPaused = false;
			start();
			// iPhone: element fullscreen is off, go straight to the native player (inside the tap).
			if (!(document.fullscreenEnabled || document.webkitFullscreenEnabled) && video.webkitEnterFullscreen) {
				iosFullscreen();
			} else if (video.requestFullscreen) {
				var p = video.requestFullscreen();
				if (p && p.catch) {
					p.catch(iosFullscreen);
				}
			} else if (video.webkitRequestFullscreen) {
				video.webkitRequestFullscreen();
			} else {
				iosFullscreen();
			}
		}

		if (fsBtn) {
			if (!video.requestFullscreen && !video.webkitRequestFullscreen && !video.webkitEnterFullscreen) {
				fsBtn.hidden = true;
			} else {
				fsBtn.addEventListener('click', enterFullscreen);
				var onFsChange = function () {
					var on = fsElement() === video;
					if (on !== inFs) {
						setFs(on);
					}
				};
				document.addEventListener('fullscreenchange', onFsChange);
				document.addEventListener('webkitfullscreenchange', onFsChange);
				// iPhone native player.
				video.addEventListener('webkitbeginfullscreen', function () {
					setFs(true);
				});
				video.addEventListener('webkitendfullscreen', function () {
					setFs(false);
				});
			}
		}

		if (hasIO) {
			new IntersectionObserver(function (entries) {
				// Full screen changes the layout; never pause the video because of that.
				if (inFs) {
					return;
				}
				if (entries[0].isIntersecting) {
					// Load the first frame data early, so a tap on fullscreen works at once on iPhone.
					if (video.preload === 'none') {
						video.preload = 'metadata';
					}
					if (!userPaused && !reduceMotion && !lowData) {
						start();
					}
				} else if (!video.paused) {
					video.pause();
				}
			}, { threshold: 0.45 }).observe(box);
		}
	})();

	/*
	 * 4. Doctor wall: one-quote swipe carousel at <= 1024px (desktop keeps the grid).
	 * Swipe/snap is native CSS scroll-snap. This adds a seamless loop (a copy of the last
	 * quote before the first and of the first after the last; landing on a copy jumps to
	 * the real quote), dots, and 3s autoplay that pauses while touched, hovered, focused,
	 * off screen or in a hidden tab, and never runs for prefers-reduced-motion.
	 * The copies are hidden on desktop by CSS.
	 */
	(function () {
		var wall = document.querySelector('.ppc-hims .wall');
		var dotsBox = document.getElementById('wallDots');
		if (!wall || !dotsBox) {
			return;
		}
		var real = Array.prototype.slice.call(wall.querySelectorAll('.card-q'));
		var n = real.length;
		if (n < 2) {
			return;
		}

		var AUTOPLAY_MS = 7000;
		var mq = window.matchMedia('(max-width: 1024px)');
		var current = -1;
		var ticking = false;
		var settleTimer = null;
		var timer = null;
		var lastWidth = window.innerWidth;
		var paused = { hover: false, focus: false, touch: false, offscreen: hasIO, hidden: document.hidden };

		function makeCopy(card) {
			var copy = card.cloneNode(true);
			copy.classList.add('is-clone');
			copy.setAttribute('aria-hidden', 'true');
			return copy;
		}
		wall.insertBefore(makeCopy(real[n - 1]), real[0]);
		wall.appendChild(makeCopy(real[0]));
		var slides = Array.prototype.slice.call(wall.querySelectorAll('.card-q')); // n + 2

		var dots = real.map(function (card, i) {
			var b = document.createElement('button');
			b.type = 'button';
			b.setAttribute('aria-label', 'Show quote ' + (i + 1) + ' of ' + n);
			b.addEventListener('click', function () {
				goTo(i + 1, true);
				restart();
			});
			dotsBox.appendChild(b);
			return b;
		});

		function step() {
			return slides[1].offsetLeft - slides[0].offsetLeft || wall.clientWidth;
		}

		function slideIndex() {
			return Math.round(wall.scrollLeft / step());
		}

		function goTo(i, smooth) {
			var left = slides[i].offsetLeft - slides[0].offsetLeft;
			if (smooth) {
				wall.scrollTo({ left: left });
				return;
			}
			wall.style.scrollBehavior = 'auto';
			wall.style.scrollSnapType = 'none';
			wall.scrollLeft = left;
			window.requestAnimationFrame(function () {
				wall.style.scrollSnapType = '';
				wall.style.scrollBehavior = '';
			});
		}

		function setCurrent(i) {
			if (i === current) {
				return;
			}
			current = i;
			dots.forEach(function (d, k) {
				d.setAttribute('aria-current', k === i ? 'true' : 'false');
			});
		}

		// While scrolling: keep the dots in sync (a copy maps to its real quote).
		function sync() {
			ticking = false;
			var i = Math.max(0, Math.min(n + 1, slideIndex()));
			setCurrent((i - 1 + n) % n);
		}

		// When scrolling stops on a copy, jump to the real quote.
		function settle() {
			var i = slideIndex();
			if (i <= 0) {
				goTo(n, false);
			} else if (i >= n + 1) {
				goTo(1, false);
			}
		}

		wall.addEventListener('scroll', function () {
			if (!mq.matches) {
				return;
			}
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(sync);
			}
			clearTimeout(settleTimer);
			settleTimer = setTimeout(settle, 150);
		}, { passive: true });

		function canPlay() {
			return mq.matches && !reduceMotion && !paused.hover && !paused.focus && !paused.touch && !paused.offscreen && !paused.hidden;
		}

		function restart() {
			clearInterval(timer);
			timer = canPlay() ? setInterval(function () {
				goTo(Math.min(slideIndex() + 1, n + 1), true); // the trailing copy loops via settle()
			}, AUTOPLAY_MS) : null;
		}

		function pauseOn(key, value) {
			return function (e) {
				if (key === 'focus' && !value && wall.contains(e.relatedTarget)) {
					return;
				}
				paused[key] = value;
				restart();
			};
		}

		wall.addEventListener('mouseenter', pauseOn('hover', true));
		wall.addEventListener('mouseleave', pauseOn('hover', false));
		wall.addEventListener('focusin', pauseOn('focus', true));
		wall.addEventListener('focusout', pauseOn('focus', false));
		wall.addEventListener('touchstart', pauseOn('touch', true), { passive: true });
		wall.addEventListener('touchend', pauseOn('touch', false), { passive: true });
		wall.addEventListener('touchcancel', pauseOn('touch', false), { passive: true });
		document.addEventListener('visibilitychange', function () {
			paused.hidden = document.hidden;
			restart();
		});
		if (hasIO) {
			new IntersectionObserver(function (entries) {
				paused.offscreen = !entries[0].isIntersecting;
				restart();
			}, { threshold: 0.5 }).observe(wall);
		}

		// Carousel semantics only while it is a carousel; arrow keys scroll it natively.
		function applyMode() {
			if (mq.matches) {
				wall.setAttribute('role', 'region');
				wall.setAttribute('aria-roledescription', 'carousel');
				wall.setAttribute('aria-label', 'Doctor and hospital quotes');
				wall.setAttribute('tabindex', '0');
				goTo(Math.max(1, Math.min(n, current + 1)), false);
				sync();
			} else {
				['role', 'aria-roledescription', 'aria-label', 'tabindex'].forEach(function (a) {
					wall.removeAttribute(a);
				});
			}
			restart();
		}

		if (mq.addEventListener) {
			mq.addEventListener('change', applyMode);
		} else if (mq.addListener) {
			mq.addListener(applyMode);
		}
		// Keep the same quote in view on rotation/width change (ignore mobile URL-bar height changes).
		window.addEventListener('resize', function () {
			if (mq.matches && window.innerWidth !== lastWidth) {
				goTo(current + 1, false);
			}
			lastWidth = window.innerWidth;
		});

		setCurrent(0);
		applyMode();
	})();

	/* 5. FAQ: one answer open at a time. Native via <details name>; this covers older browsers. */
	(function () {
		var items = Array.prototype.slice.call(document.querySelectorAll('.ppc-hims .faq details'));
		items.forEach(function (d) {
			d.addEventListener('toggle', function () {
				if (!d.open) {
					return;
				}
				items.forEach(function (other) {
					if (other !== d) {
						other.open = false;
					}
				});
			});
		});
	})();
})();

document.addEventListener('DOMContentLoaded', function () {
	const forms = document.querySelectorAll('.lead-form.hr-demo-form');
	const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/*
	 * After a step change, keep the page still. Scroll only when the top of the form card is
	 * hidden (above the screen or under the sticky header), and then just enough to show it.
	 * Not used in the popup: it is fixed on screen and scrolls on its own.
	 */
	function keepCardInView(form) {
		const card = form.closest('.form-card');
		if (!card || form.closest('#myPopup')) {
			return;
		}
		const header = document.querySelector('.ppc-hims .top');
		const headerH = header && getComputedStyle(header).position === 'sticky' ? header.offsetHeight : 0;
		const top = card.getBoundingClientRect().top;
		if (top < headerH) {
			window.scrollBy({ top: top - headerH - 12, behavior: reduceMotion ? 'auto' : 'smooth' });
		}
	}

	forms.forEach(function (form) {
		const step1 = form.querySelector('.hr-step-1');
		const step2 = form.querySelector('.hr-step-2');
		const bedOptions = form.querySelectorAll('.hr-bed-option');
		// By name, not #bed_size: CF7 drops the id on the second copy of the form (the popup).
		const bedInput = form.querySelector('[name="bed_size"]');
		const selectedBed = form.querySelector('.hr-selected-bed-value');
		const backButton = form.querySelector('.hr-back-step');
		const changeButton = form.querySelector('.hr-change-bed');

		bedOptions.forEach(function (option) {
			option.addEventListener('click', function () {
				const bedValue = this.getAttribute('data-bed');

				if (!bedValue) {
					return;
				}

				if (bedInput) {
					bedInput.value = bedValue;
				}

				if (selectedBed) {
					selectedBed.textContent = bedValue;
				}

				step1.style.display = 'none';
				step2.style.display = 'block';

				keepCardInView(form);
			});
		});

		function goToStepOne() {
			step2.style.display = 'none';
			step1.style.display = 'block';

			keepCardInView(form);
		}

		if (backButton) {
			backButton.addEventListener('click', goToStepOne);
		}

		if (changeButton) {
			changeButton.addEventListener('click', goToStepOne);
		}
	});
});

/* 6. Modules menu: on phones/tablets the menu is cut to a preview; "Show all 40+ modules" opens the rest. */
(function () {
	'use strict';

	var app = document.getElementById('mmApp');
	var btn = document.getElementById('mmMore');
	if (!app || !btn) {
		return;
	}

	btn.addEventListener('click', function () {
		var all = app.classList.toggle('all');
		btn.setAttribute('aria-expanded', all ? 'true' : 'false');
		btn.textContent = all ? 'Show less' : 'Show all 40+ modules';
	});
})();

/*
 * 7. Mobile number: the campaign runs in India only. "+91" sits in its own box in front of the
 * field, and the field takes just the 10-digit number. "+91 " is added to the value CF7 sends
 * (formdata event), so the server check and Odoo still get "+91 9876543210".
 * Pasted or autofilled full numbers keep their last 10 digits ("+91 98765-43210",
 * "919876543210", "09876543210" all become "9876543210").
 */
(function () {
	'use strict';

	var CODE = '+91';
	var MAX = 10;
	var inputs = document.querySelectorAll('.lead-form.hr-demo-form input[name="your-number"]');
	if (!inputs.length) {
		return;
	}

	function digitsOnly(value) {
		var d = value.replace(/\D/g, '').replace(/^0+/, '');
		return d.length > MAX ? d.slice(-MAX) : d;
	}

	inputs.forEach(function (input) {
		var form = input.closest('form');

		var box = document.createElement('span');
		box.className = 'hr-phone';
		var cc = document.createElement('span');
		cc.className = 'hr-cc';
		cc.textContent = CODE;
		cc.setAttribute('aria-hidden', 'true');
		input.parentNode.insertBefore(box, input);
		box.appendChild(cc);
		box.appendChild(input);

		input.value = digitsOnly(input.value);
		input.placeholder = '98765 43210';
		input.setAttribute('inputmode', 'numeric');
		input.setAttribute('aria-label', 'Mobile number, India ' + CODE);

		// Typing stops at 10 digits (the 11th is ignored); a paste or autofill keeps the last 10,
		// so "+91 98765-43210" still becomes "9876543210".
		input.addEventListener('input', function (e) {
			var next;
			if (e.inputType === 'insertText') {
				next = input.value.replace(/\D/g, '').replace(/^0+/, '').slice(0, MAX);
			} else {
				next = digitsOnly(input.value);
			}
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

/*
 * 8. Meta Ads page (temp-ppc-hims-meta.php). Runs when <body> has the class "hr-hms-meta", or when the page has
 * the hero form slot (#formSlot) that only the Meta template prints.
 * The videos, mobile bar, FAQ and doctor wall are handled by sections 1 to 5 above; the 2-step Contact Form 7
 * form and the +91 field by the blocks above; the popup by the site-wide popup script (.hr-cta-btn), the same
 * as on the Google and ChatGPT pages. This block adds what is only on the Meta page: GTM events, UTM, fbclid
 * and the button location in the hidden fields of both forms (hero card and popup), and the thank-you panel
 * shown in place of the hero form. Older markup (.js-to-form buttons + #sheet bottom sheet) still works.
 */
(function () {
	'use strict';

	if (!document.body.classList.contains('hr-hms-meta') && !document.getElementById('formSlot')) {
		return;
	}

	var params = new URLSearchParams(window.location.search);
	window.dataLayer = window.dataLayer || [];

	// GTM: Meta Pixel "Lead" and the GA4 lead event fire on the dataLayer event "form_success".
	// Event names are plain business words, never health terms, so Meta's data filters do not block them.
	function track(event, data) {
		window.dataLayer.push(Object.assign({ event: event, segment: 'hms', channel: 'meta', page_type: 'hms_meta' }, data || {}));
	}

	var card = document.getElementById('demo');
	var slot = document.getElementById('formSlot');
	var formArea = document.getElementById('formArea');
	var doneArea = document.getElementById('doneArea');
	var sheet = document.getElementById('sheet');
	var panel = document.getElementById('sheetPanel');
	if (!card || !slot || !formArea || !doneArea) {
		return;
	}

	var activeLoc = 'hero';
	var popupLoc = 'button';
	var started = false;
	var lastLoc = 'hero';
	var lastLeadFocus = null;

	// The site-wide popup (#myPopup) holds the second copy of the form.
	function popup() {
		return document.getElementById('myPopup');
	}

	// UTM, fbclid and the button location go into a form's hidden fields (only the ones the form has).
	function fillHidden(root, loc) {
		if (!root) {
			return;
		}
		var vals = { cta_location: loc };
		['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'fbclid', 'gclid'].forEach(function (k) {
			var v = params.get(k);
			if (v) {
				vals[k] = v.slice(0, 200);
			}
		});
		root.querySelectorAll('input[type="hidden"]').forEach(function (inp) {
			if (Object.prototype.hasOwnProperty.call(vals, inp.name)) {
				inp.value = vals[inp.name];
			}
		});
	}
	function setTracking(loc) {
		activeLoc = loc || 'hero';
		fillHidden(card, activeLoc);
	}
	setTracking('hero');
	fillHidden(popup(), popupLoc);

	// "Book free demo" buttons that open the site-wide popup: count the click and note where it came from.
	// (The popup script itself opens the popup; nothing here stops it.)
	document.querySelectorAll('.hr-cta-btn').forEach(function (a) {
		a.addEventListener('click', function () {
			popupLoc = a.getAttribute('data-from') || 'button';
			track('cta_click', { cta_type: 'demo', cta_location: popupLoc });
			fillHidden(popup(), popupLoc);
			// In case the popup markup is filled in only when it opens.
			setTimeout(function () {
				fillHidden(popup(), popupLoc);
			}, 0);
		});
	});

	// Focus: the first bed-size button on step 1, else the first field on step 2.
	function focusFirst() {
		if (formArea.hidden) {
			return;
		}
		var step1 = card.querySelector('.hr-step-1');
		var f = step1 && step1.offsetParent !== null
			? step1.querySelector('.hr-bed-option')
			: card.querySelector('.hr-step-2 input:not([type="hidden"]):not([type="submit"])');
		if (f) {
			f.focus({ preventScroll: true });
		}
	}

	// Form events (the bed-size buttons are the first, easy tap), in the hero form or the popup form.
	document.addEventListener('click', function (e) {
		var opt = e.target.closest && e.target.closest('.hr-bed-option');
		if (!opt) {
			return;
		}
		var pop = popup();
		var inPopup = !!(pop && pop.contains(opt));
		if (!inPopup && !card.contains(opt)) {
			return;
		}
		var loc = inPopup ? popupLoc : activeLoc;
		if (!started) {
			started = true;
			track('form_start', { cta_location: loc });
		}
		track('form_step', { step: 2, beds: opt.getAttribute('data-bed') || '', cta_location: loc });
	});

	// Lead sent: GTM "form_success" for both forms (Meta Pixel Lead fires on it). The hero form then shows the
	// thank-you panel in its place; the popup form shows CF7's own message, as on the Google and ChatGPT pages.
	// (If CF7 has a redirect set for this form, that page shows instead.)
	document.addEventListener('wpcf7mailsent', function (e) {
		var form = e.target && e.target.closest ? e.target.closest('.wpcf7') : null;
		if (!form) {
			return;
		}
		var pop = popup();
		var inCard = card.contains(form);
		if (!inCard && !(pop && pop.contains(form))) {
			return;
		}
		var sent = {};
		((e.detail && e.detail.inputs) || []).forEach(function (i) {
			sent[i.name] = i.value;
		});
		var mobile = String(sent['your-number'] || '').replace(/\D/g, '').slice(-10);
		track('form_success', { form: 'hms_meta', beds: sent.bed_size || '', cta_location: inCard ? activeLoc : popupLoc, form_place: inCard ? 'hero' : 'popup' });
		if (!inCard) {
			return;
		}

		var nm = String(sent['your-name'] || '').trim().split(/\s+/).slice(0, 2).join(' ');
		var t = document.getElementById('doneTitle');
		var x = document.getElementById('doneText');
		if (t && nm) {
			t.textContent = 'Thank you, ' + nm + '. We have got your request.';
		}
		if (x && /^\d{10}$/.test(mobile)) {
			x.textContent = 'Our team will call you once on +91 ' + mobile.replace(/(\d{5})(\d{5})/, '$1 $2') + ' to fix a demo time.';
		}
		formArea.hidden = true;
		doneArea.hidden = false;
	});

	// Every "Book free demo" button opens the same form.
	// Near the top: focus the hero form. Further down: open it as a bottom sheet so the reader keeps their place.
	function heroFormVisible() {
		if (card.parentNode !== slot) {
			return false;
		}
		var r = card.getBoundingClientRect();
		return r.top < window.innerHeight * 0.55 && r.bottom > window.innerHeight * 0.3;
	}

	function openForm(from) {
		setTracking(from);
		lastLoc = from || 'button';
		track('cta_click', { cta_type: 'demo', cta_location: lastLoc });
		if (!sheet || !panel || heroFormVisible()) {
			focusFirst();
			return;
		}
		if (card.parentNode === slot) {
			slot.style.minHeight = slot.offsetHeight + 'px';
			panel.appendChild(card);
		}
		lastLeadFocus = document.activeElement;
		sheet.hidden = false;
		document.documentElement.classList.add('sheet-open');
		void sheet.offsetWidth;
		sheet.classList.add('open');
		setTimeout(function () {
			if (formArea.hidden) {
				document.getElementById('sheetClose').focus({ preventScroll: true });
			} else {
				focusFirst();
			}
		}, 80);
	}

	function closeForm() {
		if (!sheet || sheet.hidden) {
			return;
		}
		sheet.classList.remove('open');
		document.documentElement.classList.remove('sheet-open');
		setTimeout(function () {
			sheet.hidden = true;
			// The slot keeps its height, so the page never jumps (works on iPhone too).
			slot.appendChild(card);
			setTracking('hero');
			if (lastLeadFocus && lastLeadFocus.focus) {
				lastLeadFocus.focus({ preventScroll: true });
			}
		}, 220);
	}

	document.querySelectorAll('.js-to-form').forEach(function (a) {
		a.addEventListener('click', function (e) {
			e.preventDefault();
			openForm(a.getAttribute('data-from'));
		});
	});
	if (sheet) {
		document.getElementById('sheetClose').addEventListener('click', closeForm);
		document.getElementById('sheetBg').addEventListener('click', closeForm);
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				closeForm();
			}
		});
	}

	// WhatsApp and call clicks (delegated, so the ones inside the thank-you panel count too).
	document.addEventListener('click', function (e) {
		var a = e.target.closest && e.target.closest('a');
		if (!a) {
			return;
		}
		if (a.classList.contains('wa-done') || (a.getAttribute('href') || '').indexOf('wa.me/') > -1) {
			track('whatsapp_click');
		} else if ((a.getAttribute('href') || '').indexOf('tel:') === 0) {
			track('call_click');
		}
	});
})();