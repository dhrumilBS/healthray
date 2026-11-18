/* Healthray home page: all scripts in one file (built from home-page/js by build_template.py). Order matters. */
/* ===== ppc-hims.js ===== */
;(function () {
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

;
/* ===== departments.js ===== */
;/* Departments section: shows one department at a time.
   Pinned (computer and phone, whenever it fits on the screen): the names and the picture stay in place while the page
   scrolls. Each step of scrolling opens the next department, and after the last one (Reports) the page moves on.
   Computer: pinned just under the top bar; the blue line under the open name fills as you scroll.
   Phone: pinned at the top of the screen with the row of names above the picture; the text keeps only the heading and
   one line, and the picture may get a little smaller so it all fits. The demo bar at the bottom hides meanwhile (js/polish.js).
   A tap or click on a name, or the arrow keys, scroll the page to that department.
   Not pinned (a screen too short even for that): it changes by itself every few seconds; a tap stops that. */
(function () {
  var root = document.getElementById('departments');
  if (!root) return;

  var AUTO = +root.dataset.auto || 6000; /* not pinned: milliseconds each department stays before the next one opens */

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
  /* Phone: keep the chosen name in view inside its row, without moving the page. */
  function revealChip() {
    if (!rail || rail.scrollWidth <= rail.clientWidth + 2) return;
    var chip = rail.querySelector('.col-item.on'); if (!chip) return;
    var left = chip.offsetLeft - rail.offsetLeft - 20;
    try { rail.scrollTo({ left: Math.max(0, left), behavior: calm ? 'auto' : 'smooth' }); } catch (e) { rail.scrollLeft = Math.max(0, left); }
  }

  /* ---------- Pinned ---------- */
  function headH() {
    if (!head) {
      /* The site's own header is the page header now; the theme publishes its height as --hr-header-h. */
      var v = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--hr-header-h'));
      return v > 0 ? v : 0;
    }
    var p = getComputedStyle(head).position;
    return (p === 'sticky' || p === 'fixed') ? head.getBoundingClientRect().height : 0;
  }
  /* Where the page is when department 0 starts, in page coordinates. */
  function startY() { return pin.getBoundingClientRect().top + scrollY - pinTop; }

  function unpin() {
    pinned = false; root.classList.remove('pinned', 'pin-compact');
    ['--pin-h', '--step-p', '--stack-w', '--pin-top'].forEach(function (k) { root.style.removeProperty(k); });
  }
  function layout() {
    if (!pin || !grid || !stack) return;
    var gap = wide.matches ? 16 : 12;
    /* Try the pinned layout (it is more compact on a phone), measure it with the picture at full size, then decide. */
    root.style.setProperty('--stack-w', 'none');
    root.classList.add('pinned');
    root.classList.remove('pin-compact');
    pinTop = Math.round(headH() + gap);
    var picH = stack.offsetHeight, picW = stack.offsetWidth;
    var over = grid.offsetHeight + pinTop + gap - innerHeight;
    /* Phone: with the full text it does not fit, so keep only the heading and one line. */
    if (over > 0 && !wide.matches) {
      root.classList.add('pin-compact');
      over = grid.offsetHeight + pinTop + gap - innerHeight;
    }
    var newH = over > 0 ? picH - over : picH;
    /* The picture may get smaller so it all fits, but never below 70% of its size. */
    if (!picH || newH < picH * 0.7) {
      var was = pinned; unpin();
      if (was || !armed) { armed = true; select(Math.max(cur, 0)); }
      return;
    }
    if (over > 0) root.style.setProperty('--stack-w', Math.floor(newH * picW / picH) + 'px');
    /* Phone: the screen is taller than the pinned area, so centre it instead of leaving a blank strip at the bottom. */
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
  /* Pinned: a tap, a click or an arrow key scrolls the page to that department. */
  function goTo(i) {
    i = Math.min(N - 1, Math.max(0, i));
    show(i); root.style.setProperty('--step-p', '0');
    var y = Math.round(startY() + i * STEP + 2);
    try { scrollTo({ top: y, behavior: calm ? 'auto' : 'smooth' }); } catch (e) { scrollTo(0, y); }
  }

  /* ---------- Not pinned ---------- */
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
    /* On a touch screen, a tap means the reader is choosing: stop changing by itself. */
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

  /* Re-measure when the screen size changes (on a phone only when the width changes or the height changes a lot,
     so the address bar sliding in and out does not make it jump), and when the pictures and fonts have loaded. */
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
/* ===== proof.js ===== */
;/* Testimonial rail: the arrows move it one card at a time. */
document.querySelectorAll('[data-rail]').forEach(function (sec) {
  var rail = sec.querySelector('.tm-rail'), prev = sec.querySelector('[data-prev]'), next = sec.querySelector('[data-next]');
  if (!rail || !prev || !next) return;
  function step() { var c = rail.querySelector('.tm'); return c ? c.getBoundingClientRect().width + 20 : 300; }
  function sync() { prev.disabled = rail.scrollLeft < 4; next.disabled = rail.scrollLeft + rail.clientWidth > rail.scrollWidth - 4; }
  /* Slide smoothly; if the browser does not animate it, jump there. */
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
/* ===== polish.js ===== */
;/* Phone: show the demo bar once the form at the top is out of sight. Hide it again
   - near the closing section, which has its own button,
   - while another "Book a demo" button is already on the screen (no two identical buttons at once),
   - while the departments section is pinned on the screen, so the picture has the whole screen. */
(function () {
  var bar = document.getElementById('mcta');
  var form = document.getElementById('demo');
  var closing = document.getElementById('book-demo');
  var depts = document.getElementById('departments');
  var siteFooter = document.querySelector('footer.hr-home-footer') || document.querySelector('body > footer');
  if (!bar || !form) return;
  var ctas = [].slice.call(document.querySelectorAll('.hr-home-main .hr-cta-btn')).filter(function (a) {
    return !bar.contains(a) && !form.contains(a);
  });
  var queued = false;
  function visible(el, margin) {
    var r = el.getBoundingClientRect();
    return r.width > 0 && r.height > 0 && r.bottom > 0 && r.top < innerHeight - (margin || 0);
  }
  function update() {
    queued = false;
    var f = form.getBoundingClientRect();
    var formGone = f.bottom < 0;
    var c = closing ? closing.getBoundingClientRect() : null;
    var atClosing = c ? (c.top < innerHeight * 0.75 && c.bottom > 0) : false;
    var otherBtn = ctas.some(function (a) { return visible(a, 70); });
    var pinnedNow = depts && depts.classList.contains('pinned') && visible(depts.querySelector('.dp-pin') || depts);
    var atFooter = siteFooter ? visible(siteFooter) : false;
    bar.classList.toggle('show', formGone && !atClosing && !atFooter && !otherBtn && !pinnedNow);
  }
  function ask() { if (!queued) { queued = true; (window.requestAnimationFrame || setTimeout)(update, 16); } }
  addEventListener('scroll', ask, { passive: true });
  addEventListener('resize', ask);
  /* a timer as well, for browsers that hold back scroll callbacks */
  setInterval(update, 600);
  update();
})();
;
/* ===== autoslide.js ===== */
;/* Phone: the doctor reviews and the case-study cards slide to the next card by themselves.
   - Only on a phone-width screen (760px or less), where these rows are cards you swipe.
   - Only while the row is on the screen and the tab is open; after the last card it goes back to the first.
   - When the reader touches or swipes the row, it waits 10 seconds before sliding again, so it never fights the reader.
   - Off for people who ask their phone for less motion. */
(function () {
  var phone = window.matchMedia ? window.matchMedia('(max-width: 760px)') : null;
  var calm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!phone || calm) return;

  var EVERY = 4500;   /* milliseconds each card stays */
  var WAIT = 10000;   /* milliseconds to wait after the reader touches the row */

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
          /* when the row comes into view, give the reader a moment before the first slide */
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