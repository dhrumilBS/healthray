(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.alt-matrix').forEach(function (table) {
			var headerCells = table.querySelectorAll('.alt-accordion__header-cell');

			function getPanel(key) {
				return table.querySelector('[data-accordion-panel="' + key + '"]');
			}

			function setState(headerRow, cell, open) {
				var key = headerRow.getAttribute('data-accordion');
				var panel = getPanel(key);

				headerRow.classList.toggle('is-open', open);
				cell.setAttribute('aria-expanded', open ? 'true' : 'false');
				if (panel) panel.classList.toggle('is-open', open);
			}

			function toggle(cell) {
				var headerRow = cell.closest('tr');
				var willOpen = !headerRow.classList.contains('is-open');

				headerCells.forEach(function (otherCell) {
					var otherRow = otherCell.closest('tr');
					if (otherRow !== headerRow) setState(otherRow, otherCell, false);
				});

				setState(headerRow, cell, willOpen);
			}

			headerCells.forEach(function (cell) {
				cell.addEventListener('click', function () {
					toggle(cell);
				});
				cell.addEventListener('keydown', function (e) {
					if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
						e.preventDefault();
						toggle(cell);
					}
				});
			});
		});

		// Mobile TOC — the sticky bar that replaces the sidebar under 992px. The
		// panel is an overlay, so it also has to close on an outside click and on
		// Escape; the toggle alone is not enough of an escape hatch when the list
		// is covering the article.
		(function () {
			var box = document.getElementById('alt-toc-mobile');
			if (!box) return;

			var toggle = box.querySelector('.alt-toc-mobile__toggle');
			var panel = document.getElementById('alt-toc-mobile-panel');
			if (!toggle || !panel) return;

			function isOpen() {
				return !panel.classList.contains('is-collapsed');
			}

			function setOpen(open) {
				panel.classList.toggle('is-collapsed', !open);
				toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			}

			toggle.addEventListener('click', function (e) {
				e.stopPropagation();
				setOpen(!isOpen());
			});

			// Jumping to a section is handled natively by the href plus
			// scroll-margin-top, so this only has to get the panel out of the way.
			panel.addEventListener('click', function (e) {
				if (e.target.closest('a')) setOpen(false);
			});

			document.addEventListener('click', function (e) {
				if (isOpen() && !box.contains(e.target)) setOpen(false);
			});

			document.addEventListener('keydown', function (e) {
				if (('Escape' === e.key || 'Esc' === e.key) && isOpen()) {
					setOpen(false);
					toggle.focus();
				}
			});

			// Dismiss. Hides the bar for this page view only - deliberately not
			// persisted to storage, so a refresh or a return visit shows it again.
			// Focus moves to the article so it does not fall into the void once the
			// button it was sitting on stops being rendered.
			var dismiss = box.querySelector('.alt-toc-mobile__dismiss');

			if (dismiss) {
				dismiss.addEventListener('click', function (e) {
					e.stopPropagation();
					setOpen(false);
					box.classList.add('is-dismissed');

					var article = box.closest('.alt-main');
					if (article) {
						article.setAttribute('tabindex', '-1');
						article.focus({ preventScroll: true });
					}
				});
			}
		})();

		// Sidebar TOC scrollspy — highlights the link for whichever section is
		// currently nearest the top of the viewport.
		(function () {
			var links = Array.prototype.slice.call(document.querySelectorAll('.alt-toc__link'));
			if (!links.length || !('IntersectionObserver' in window)) return;

			var targets = links
				.map(function (link) {
					var id = link.getAttribute('href').slice(1);
					var el = document.getElementById(id);
					return el ? { link: link, el: el } : null;
				})
				.filter(Boolean);

			if (!targets.length) return;

			var activeEl = null;

			function setActive(el) {
				if (el === activeEl) return;
				activeEl = el;
				targets.forEach(function (t) {
					t.link.classList.toggle('is-active', t.el === el);
				});
			}

			var visible = new Map();
			var observer = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						visible.set(entry.target, entry.boundingClientRect.top);
					} else {
						visible.delete(entry.target);
					}
				});

				if (!visible.size) return;

				var topMost = null;
				var topMostY = Infinity;
				visible.forEach(function (y, el) {
					if (y < topMostY) {
						topMostY = y;
						topMost = el;
					}
				});
				if (topMost) setActive(topMost);
			}, { rootMargin: '-110px 0px -70% 0px', threshold: 0 });

			targets.forEach(function (t) {
				observer.observe(t.el);
			});

			setActive(targets[0].el);
		})();

		// Healthray profile video (hr_alt_render_profile_media() in
		// lib/alternatives-helpers.php). No controls: it is muted and loops, starts
		// once at least half of it is on screen and pauses once it has left the
		// screen, or the tab is hidden. The frame is one transparent button, so a click,
		// or Enter on keyboard focus, still pauses it (WCAG 2.2.2), and a pause the
		// visitor chose sticks until they press again. It waits with a play icon
		// instead of starting by itself for "reduce motion", Data Saver and 2G, the
		// same rule as the PPC walkthrough, and when the browser refuses autoplay.
		(function () {
			var frames = document.querySelectorAll('.alt-profile__screenshot--video');
			if (!frames.length) return;

			var canWatch = 'IntersectionObserver' in window;
			var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			var conn = navigator.connection || {};
			var lowData = !!conn.saveData || /(^|-)2g$/.test(conn.effectiveType || '');
			var autoplay = canWatch && !reduceMotion && !lowData;

			frames.forEach(function (frame) {
				var video = frame.querySelector('video');
				var toggle = frame.querySelector('.alt-profile__video-toggle');
				if (!video || !toggle) return;

				var label = toggle.getAttribute('data-label') || 'video';
				var wants = autoplay; // play whenever it is on screen
				var visible = !canWatch; // any of it on screen
				var half = !canWatch; // at least half of it on screen

				function render() {
					frame.classList.toggle('is-waiting', !wants);
					toggle.setAttribute('aria-label', (wants ? 'Pause the ' : 'Play the ') + label);
				}

				function play() {
					video.muted = true;
					var p = video.play();
					if (p && p.catch) {
						p.catch(function (err) {
							// AbortError is a pause() cutting in, which scrolling does.
							// Anything else means the browser said no: wait for a click.
							if (err && err.name === 'AbortError') return;
							wants = false;
							render();
						});
					}
				}

				// Starting waits for half of it; stopping waits until none of it is
				// left, so it never freezes while still partly in view.
				function update() {
					if (!wants || !visible || document.hidden) {
						if (!video.paused) video.pause();
					} else if (half && video.paused) {
						play();
					}
				}

				toggle.addEventListener('click', function () {
					wants = !wants;
					render();
					if (wants) {
						// It was just clicked, so it is on screen, even if less than half of it is.
						visible = true;
						play();
					} else {
						video.pause();
					}
				});

				document.addEventListener('visibilitychange', update);

				// Both thresholds matter: Chrome only reports crossings of the ones
				// listed, so 0 is what says it has left the screen completely.
				if (canWatch) {
					new IntersectionObserver(function (entries) {
						var entry = entries[entries.length - 1];
						visible = entry.isIntersecting;
						half = entry.intersectionRatio >= 0.5;
						update();
					}, { threshold: [0, 0.5] }).observe(frame);
				}

				toggle.hidden = false;
				render();
			});
		})();

		// FAQ accordion — uses native <details name="alt-faq-group"> so the browser
		// keeps only one FAQ open at a time, no JS needed here.
	});
})();