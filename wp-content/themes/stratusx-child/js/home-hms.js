/* Built from home-page/js */
/* ppc-hims.js */
; (function () {
	'use strict';

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var hasIO = 'IntersectionObserver' in window;

	/* play(), ignore AbortError */
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

	/* Customer story */
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

	/* Mobile action bar */
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

	/* WhatsApp button */
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

	/* Walkthrough video */
	(function () {
		var box = document.getElementById('player');
		var video = document.getElementById('walkVideo');
		var btn = document.getElementById('walkBtn');
		if (!box || !video || !btn) {
			return;
		}
		var userPaused = false;
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

		/* Fullscreen */
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
				if (screen.orientation && screen.orientation.lock) {
					screen.orientation.lock('landscape').catch(function () { });
				}
			} else {
				if (screen.orientation && screen.orientation.unlock) {
					try {
						screen.orientation.unlock();
					} catch (e) { }
				}
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
				if (inFs) {
					return;
				}
				if (entries[0].isIntersecting) {
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

	/* Doctor wall carousel */
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
		var slides = Array.prototype.slice.call(wall.querySelectorAll('.card-q'));

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

		function sync() {
			ticking = false;
			var i = Math.max(0, Math.min(n + 1, slideIndex()));
			setCurrent((i - 1 + n) % n);
		}

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
				goTo(Math.min(slideIndex() + 1, n + 1), true);
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
		window.addEventListener('resize', function () {
			if (mq.matches && window.innerWidth !== lastWidth) {
				goTo(current + 1, false);
			}
			lastWidth = window.innerWidth;
		});

		setCurrent(0);
		applyMode();
	})();

	/* FAQ accordion */
	(function () {
		var items = Array.prototype.slice.call(document.querySelectorAll('.ppc-hims .fq-q'));
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

/* Modules menu */
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

;
/* departments.js */
;/* Departments section */
(function () {
	var root = document.getElementById('departments');
	if (!root) return;

	var AUTO = +root.dataset.auto || 6000;

	var rail = root.querySelector('.g3-rail');
	var pin = root.querySelector('.dp-pin');
	var grid = root.querySelector('.g3-grid');
	var stack = root.querySelector('.st-stack');
	var head = document.querySelector('header.top');
	var buttons = root.querySelectorAll('[data-go]');
	var N = buttons.length, cur = -1, timer = 0, stopped = false;
	var pinned = false, armed = false, STEP = 400, pinTop = 0, ticking = false, lastSize = '';
	var calm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var touch = window.matchMedia && window.matchMedia('(hover: none)').matches;
	var wide = window.matchMedia ? window.matchMedia('(min-width: 901px)') : { matches: true };

	function onScreen() {
		var r = root.getBoundingClientRect();
		return r.bottom > innerHeight * 0.3 && r.top < innerHeight * 0.7;
	}
	function show(i) {
		if (i === cur) return;
		cur = i;
		root.querySelectorAll('[data-i]').forEach(function (el) { el.classList.toggle('on', +el.dataset.i === i); });
		revealChip();
	}
	function revealChip() {
		if (!rail || rail.scrollWidth <= rail.clientWidth + 2) return;
		var chip = rail.querySelector('.col-item.on'); if (!chip) return;
		var left = chip.offsetLeft - rail.offsetLeft - 20;
		try { rail.scrollTo({ left: Math.max(0, left), behavior: calm ? 'auto' : 'smooth' }); } catch (e) { rail.scrollLeft = Math.max(0, left); }
	}

	/* Pinned */
	function headH() {
		if (!head) {
			var v = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--hr-header-h'));
			return v > 0 ? v : 0;
		}
		var p = getComputedStyle(head).position;
		return (p === 'sticky' || p === 'fixed') ? head.getBoundingClientRect().height : 0;
	}
	function startY() { return pin.getBoundingClientRect().top + scrollY - pinTop; }

	function unpin() {
		pinned = false; root.classList.remove('pinned', 'pin-compact');
		['--pin-h', '--step-p', '--stack-w', '--pin-top'].forEach(function (k) { root.style.removeProperty(k); });
	}
	function layout() {
		if (!pin || !grid || !stack) return;
		var gap = wide.matches ? 16 : 12;
		root.style.setProperty('--stack-w', 'none');
		root.classList.add('pinned');
		root.classList.remove('pin-compact');
		pinTop = Math.round(headH() + gap);
		var picH = stack.offsetHeight, picW = stack.offsetWidth;
		var over = grid.offsetHeight + pinTop + gap - innerHeight;
		if (over > 0 && !wide.matches) {
			root.classList.add('pin-compact');
			over = grid.offsetHeight + pinTop + gap - innerHeight;
		}
		var newH = over > 0 ? picH - over : picH;
		if (!picH || newH < picH * 0.7) {
			var was = pinned; unpin();
			if (was || !armed) { armed = true; select(Math.max(cur, 0)); }
			return;
		}
		if (over > 0) root.style.setProperty('--stack-w', Math.floor(newH * picW / picH) + 'px');
		if (!wide.matches && over < 0) pinTop += Math.floor(-over / 2);
		STEP = Math.max(wide.matches ? 320 : 260, Math.round(innerHeight * (wide.matches ? 0.5 : 0.45)));
		root.style.setProperty('--pin-top', pinTop + 'px');
		root.style.setProperty('--pin-h', (grid.offsetHeight + N * STEP) + 'px');
		if (!pinned) { pinned = true; root.classList.remove('auto', 'hold'); clearTimeout(timer); }
		onScroll();
	}
	function onScroll() {
		if (!pinned) return;
		var p = (scrollY - startY()) / STEP;
		var i = Math.min(N - 1, Math.max(0, Math.floor(p)));
		var frac = p < 0 ? 0 : Math.min(1, Math.max(0, p - i));
		show(i);
		root.style.setProperty('--dp-i', i);
		root.style.setProperty('--step-p', frac.toFixed(3));
	}
	addEventListener('scroll', function () {
		if (!pinned || ticking) return;
		ticking = true;
		requestAnimationFrame(function () { ticking = false; onScroll(); });
	}, { passive: true });
	function goTo(i) {
		i = Math.min(N - 1, Math.max(0, i));
		show(i); root.style.setProperty('--step-p', '0');
		var y = Math.round(startY() + i * STEP + 2);
		try { scrollTo({ top: y, behavior: calm ? 'auto' : 'smooth' }); } catch (e) { scrollTo(0, y); }
	}

	/* Not pinned */
	function restartBar() { root.classList.remove('auto'); void root.offsetWidth; if (!calm && !stopped && !pinned) root.classList.add('auto'); }
	function arm() {
		clearTimeout(timer);
		if (calm || stopped || pinned) return;
		timer = setTimeout(function () {
			if (onScreen() && !document.hidden) select(cur + 1);
			else { root.classList.add('hold'); arm(); }
		}, AUTO);
	}
	function select(i, byHand) {
		if (pinned) { goTo(i); return; }
		i = (i + N) % N;
		if (byHand && touch) stopped = true;
		root.classList.remove('hold');
		show(i); restartBar(); arm();
	}

	buttons.forEach(function (b) {
		b.addEventListener('click', function () { select(+b.dataset.go, true); });
	});
	root.addEventListener('keydown', function (e) {
		var fwd = e.key === 'ArrowDown' || e.key === 'ArrowRight', back = e.key === 'ArrowUp' || e.key === 'ArrowLeft';
		if ((!fwd && !back) || !e.target.closest('[data-go]')) return;
		e.preventDefault();
		var next = cur + (fwd ? 1 : -1);
		if (pinned) next = Math.min(N - 1, Math.max(0, next));
		select(next, true);
		var btn = root.querySelector('[data-go="' + ((next + N) % N) + '"]');
		try { btn.focus({ preventScroll: true }); } catch (err) { btn.focus(); }
	});

	var rt = 0;
	function relayout(force) {
		var size = innerWidth + 'x' + innerHeight;
		if (force !== true && size === lastSize) return;
		if (force !== true && !wide.matches && lastSize) {
			var p = lastSize.split('x');
			if (+p[0] === innerWidth && Math.abs(+p[1] - innerHeight) < 120) return;
		}
		lastSize = size;
		clearTimeout(rt); rt = setTimeout(layout, 120);
	}
	addEventListener('resize', relayout);
	addEventListener('load', function () { relayout(true); });
	if (document.fonts && document.fonts.ready) document.fonts.ready.then(function () { relayout(true); });
	stack.querySelectorAll('img').forEach(function (im) { if (!im.complete) im.addEventListener('load', function () { relayout(true); }, { once: true }); });

	show(0);
	lastSize = innerWidth + 'x' + innerHeight;
	layout();
	window.__departments = {
		select: select, cur: function () { return cur; }, pinned: function () { return pinned; },
		step: function () { return STEP; }, startY: startY, root: root
	};
})();
;
/* proof.js */
;/* Testimonial rail */
document.querySelectorAll('[data-rail]').forEach(function (sec) {
	var rail = sec.querySelector('.tm-rail'), prev = sec.querySelector('[data-prev]'), next = sec.querySelector('[data-next]');
	if (!rail || !prev || !next) return;
	function step() { var c = rail.querySelector('.tm'); return c ? c.getBoundingClientRect().width + 20 : 300; }
	function sync() { prev.disabled = rail.scrollLeft < 4; next.disabled = rail.scrollLeft + rail.clientWidth > rail.scrollWidth - 4; }
	function go(d) {
		var to = Math.max(0, Math.min(rail.scrollWidth - rail.clientWidth, rail.scrollLeft + d * step()));
		rail.scrollTo({ left: to, behavior: 'smooth' });
		setTimeout(function () { if (Math.abs(rail.scrollLeft - to) > 8) rail.scrollTo({ left: to, behavior: 'auto' }); sync(); }, 700);
	}
	prev.addEventListener('click', function () { go(-1); });
	next.addEventListener('click', function () { go(1); });
	rail.addEventListener('scroll', sync, { passive: true });
	addEventListener('resize', sync);
	sync();
});
;
/* polish.js */
;
/* autoslide.js */
;/* Auto-slide rows */
(function () {
	var phone = window.matchMedia ? window.matchMedia('(max-width: 760px)') : null;
	var calm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	if (!phone || calm) return;

	var EVERY = 4500;
	var WAIT = 10000;

	function setup(rail, cardSel) {
		if (!rail) return;
		var cards = rail.querySelectorAll(cardSel);
		if (cards.length < 2) return;
		var seen = false, heldUntil = 0, timer = 0;

		function current() {
			var pad = parseFloat(getComputedStyle(rail).paddingLeft) || 0, best = 0, bestD = 1e9;
			for (var i = 0; i < cards.length; i++) {
				var d = Math.abs(cards[i].offsetLeft - pad - rail.scrollLeft);
				if (d < bestD) { bestD = d; best = i; }
			}
			return best;
		}
		function goTo(i) {
			var pad = parseFloat(getComputedStyle(rail).paddingLeft) || 0;
			var left = Math.max(0, Math.min(rail.scrollWidth - rail.clientWidth, cards[i].offsetLeft - pad));
			try { rail.scrollTo({ left: left, behavior: 'smooth' }); } catch (e) { rail.scrollLeft = left; }
		}
		function tick() {
			timer = setTimeout(tick, EVERY);
			if (!phone.matches || !seen || document.hidden || Date.now() < heldUntil) return;
			var atEnd = rail.scrollLeft + rail.clientWidth >= rail.scrollWidth - 4;
			goTo(atEnd ? 0 : (current() + 1) % cards.length);
		}
		function hold() { heldUntil = Date.now() + WAIT; }
		['touchstart', 'pointerdown', 'wheel', 'keydown'].forEach(function (ev) { rail.addEventListener(ev, hold, { passive: true }); });

		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (es) {
				es.forEach(function (e) {
					var was = seen; seen = e.intersectionRatio >= 0.6;
					if (seen && !was) heldUntil = Math.max(heldUntil, Date.now() + 2500);
				});
			}, { threshold: [0, 0.6, 1] }).observe(rail);
		} else { seen = true; }
		timer = setTimeout(tick, EVERY);
	}

	setup(document.querySelector('#case-studies .cs-rows'), '.cs-row');
	setup(document.querySelector('#testimonials .tm-rail'), '.tm');
})();
;