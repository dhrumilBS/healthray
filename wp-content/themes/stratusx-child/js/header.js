(function () {
	'use strict';
	var wrap = document.getElementById('mega-menu-wrap-primary_navigation');
	if (!wrap) {
		return;
	}

	var header = wrap.closest('header.elementor-location-header');
	var menu = document.getElementById('mega-menu-primary_navigation');
	var toggle = wrap.querySelector('.mega-menu-toggle');
	var root = document.documentElement;
	var breakpoint = parseInt((menu && menu.getAttribute('data-breakpoint')) || '992', 10);
	var escapeClosesDrawer = null;
	var drawerIsOpen = null;
	if (header) {
		var stuck = false;
		var queued = false;

		var syncStuck = function () {
			queued = false;
			var next = (window.pageYOffset || root.scrollTop || 0) > 4;
			if (next === stuck) {
				return;
			}

			stuck = next;
			header.classList.toggle('hr-header-stuck', stuck);
		};

		var onScroll = function () {
			if (queued) {
				return;
			}
			queued = true;
			window.requestAnimationFrame(syncStuck);
		};

		window.addEventListener('scroll', onScroll, { passive: true });
		syncStuck();
	}

	if (header) {
		var publishHeaderHeight = function () {
			var height = Math.round(header.getBoundingClientRect().height);
			if (height > 0) {
				root.style.setProperty('--hr-header-h', height + 'px');
			}
		};

		publishHeaderHeight();
		window.addEventListener('resize', publishHeaderHeight);
		window.addEventListener('load', publishHeaderHeight);
		if ('ResizeObserver' in window) {
			new ResizeObserver(publishHeaderHeight).observe(header);
		}
	}

	if (toggle) {
		var pluginLockClass = 'mega-menu-primary_navigation-off-canvas-open';
		var isOpen = function () {
			return toggle.classList.contains('mega-menu-open');
		};

		var syncLock = function () {
			var open = isOpen();
			if (open && !root.classList.contains(pluginLockClass)) {
				root.classList.add('hr-nav-locked');
			} else if (!open) {
				root.classList.remove('hr-nav-locked');
			}

			document.body.classList.toggle('hr-nav-open', open);
		};

		var observer = new MutationObserver(syncLock);
		observer.observe(toggle, { attributes: true, attributeFilter: ['class'] });
		syncLock();
		var closeDrawer = function () {
			if (!isOpen()) {
				return;
			}

			var closeBtn = wrap.querySelector('button.mega-close');
			if (closeBtn) {
				closeBtn.click();
				return;
			}

			var toggleBtn = toggle.querySelector('button.mega-toggle-animated, .mega-toggle-label');
			if (toggleBtn) {
				toggleBtn.click();
			}
		};

		var resizeTimer = null;

		window.addEventListener('resize', function () {
			if (resizeTimer) {
				window.clearTimeout(resizeTimer);
			}

			resizeTimer = window.setTimeout(function () {
				if (window.innerWidth > breakpoint) {
					closeDrawer();
					root.classList.remove('hr-nav-locked');
					document.body.classList.remove('hr-nav-open');
				}
			}, 150);
		});

		escapeClosesDrawer = closeDrawer;
		drawerIsOpen = isOpen;
	}
	document.addEventListener('keydown', function (event) {
		if ('Escape' !== event.key && 'Esc' !== event.key) {
			return;
		}

		if (drawerIsOpen && drawerIsOpen()) {
			escapeClosesDrawer();
			return;
		}

		if (!menu) {
			return;
		}

		var open = menu.querySelectorAll('li.mega-toggle-on');
		if (!open.length) {
			return;
		}

		var outermost = open[0];
		var trigger = outermost.querySelector('a.mega-menu-link');
		Array.prototype.forEach.call(open, function (li) {
			li.classList.remove('mega-toggle-on');
			var link = li.querySelector('a.mega-menu-link');
			if (link && link.hasAttribute('aria-expanded')) {
				link.setAttribute('aria-expanded', 'false');
			}
		});
		if (trigger && document.activeElement !== trigger) {
			trigger.focus();
		}
	});
	if (header && !document.querySelector('.hr-skip-link')) {
		var target = document.querySelector('.content') || document.querySelector('main');
		if (target) {
			if (!target.id) {
				target.id = 'hr-content';
			}

			var skip = document.createElement('a');
			skip.className = 'hr-skip-link';
			skip.href = '#' + target.id;
			skip.textContent = 'Skip to content';
			skip.addEventListener('click', function () {
				target.setAttribute('tabindex', '-1');
				target.focus({ preventScroll: true });
			});
			header.parentNode.insertBefore(skip, header);
		}
	}
	if (menu) {
		var current = menu.querySelector(
			'li.mega-current-menu-item > a.mega-menu-link[href], li.current-menu-item > a.mega-menu-link[href]'
		);

		if (current) {
			current.setAttribute('aria-current', 'page');
		}
	}
})();