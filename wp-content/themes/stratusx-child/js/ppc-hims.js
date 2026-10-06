/**
 * PPC - Hospital Management Software (temp-ppc-hims.php).
 *
 * Page-only interactivity. Loaded deferred in the footer by functions.php (section 12).
 * Handled elsewhere, so not repeated here:
 * - "Book a free demo" buttons (.hr-cta-btn) open the lead popup: js/script.js.
 * - CF7 behaviour (button lock, "Other" fields, thank-you redirect): js/script.js.
 * - Image fallbacks (.noimg / initials): small inline script in <head>, functions.php.
 *
 * 1. Customer story video
 * 2. Mobile action bar (hidden over the hero form, final CTA and footer)
 * 3. Walkthrough video
 * 4. Doctor wall carousel (tablet/mobile)
 * 5. FAQ accordion
 * 6. Modules menu "Show all" (tablet/mobile)
 */
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

	/* 3. Walkthrough: autoplays muted on screen, pauses off screen; a manual pause sticks. */
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

		if (hasIO) {
			new IntersectionObserver(function (entries) {
				if (entries[0].isIntersecting) {
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

				form.scrollIntoView({
					behavior: 'smooth',
					block: 'start'
				});
			});
		});

		function goToStepOne() {
			step2.style.display = 'none';
			step1.style.display = 'block';

			form.scrollIntoView({
				behavior: 'smooth',
				block: 'start'
			});
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
