/* ==========================================================================
   Healthray Events - progressive enhancement for archive-events.php and
   single-events.php. Enqueued by HR_CPT_Registry (lib/cpt/definitions.php).

   Both pages work fully without this file. It only adds:
     1. Archive: filter the cards on the current page by format.
     2. Single:  close the "Add to calendar" menu on outside click / Escape.
     3. Single:  a "Copy link" share button.
   ========================================================================== */
(function () {
	'use strict';

	function toArray(list) {
		return Array.prototype.slice.call(list || []);
	}

	/* **** 1. Format filter **** */

	function initFormatFilter() {
		var filter = document.querySelector('[data-ev-filter]');
		var grid = document.querySelector('[data-ev-grid]');
		if (!filter || !grid) return;

		var buttons = toArray(filter.querySelectorAll('button[data-ev-type]'));
		var items = toArray(grid.children).filter(function (item) {
			return item.hasAttribute('data-ev-type');
		});
		var status = document.querySelector('[data-ev-filter-status]');
		if (!buttons.length || !items.length) return;

		function apply(type, label) {
			var shown = 0;

			items.forEach(function (item) {
				var match = type === 'all' || item.getAttribute('data-ev-type') === type;
				item.hidden = !match;
				if (match) shown++;
			});

			buttons.forEach(function (button) {
				button.setAttribute('aria-pressed', String(button.getAttribute('data-ev-type') === type));
			});

			if (status) {
				var noun = shown === 1 ? 'event' : 'events';
				status.textContent = type === 'all'
					? 'Showing all ' + shown + ' ' + noun + ' on this page.'
					: 'Showing ' + shown + ' ' + label + ' ' + noun + ' on this page.';
			}
		}

		filter.addEventListener('click', function (event) {
			var button = event.target.closest('button[data-ev-type]');
			if (!button || !filter.contains(button)) return;
			if (button.getAttribute('aria-pressed') === 'true') return;

			apply(button.getAttribute('data-ev-type'), button.getAttribute('data-ev-label') || '');
		});

		filter.hidden = false;
	}

	/* **** 2. "Add to calendar" menu **** */

	function initCalendarMenus() {
		var menus = toArray(document.querySelectorAll('[data-ev-cal]'));
		if (!menus.length) return;

		document.addEventListener('click', function (event) {
			menus.forEach(function (menu) {
				if (menu.open && !menu.contains(event.target)) menu.open = false;
			});
		});

		document.addEventListener('keydown', function (event) {
			if (event.key !== 'Escape' && event.key !== 'Esc') return;

			menus.forEach(function (menu) {
				if (!menu.open) return;
				menu.open = false;
				var summary = menu.querySelector('summary');
				if (summary) summary.focus();
			});
		});

		// Close once an option is chosen; the link or download still goes ahead.
		menus.forEach(function (menu) {
			menu.addEventListener('click', function (event) {
				if (event.target.closest('a')) menu.open = false;
			});
		});
	}

	/* **** 3. Copy link **** */

	function copyText(text) {
		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text);
		}

		// Fallback for http:// (local) and older browsers.
		return new Promise(function (resolve, reject) {
			var field = document.createElement('textarea');
			field.value = text;
			field.setAttribute('readonly', '');
			field.style.position = 'fixed';
			field.style.top = '0';
			field.style.opacity = '0';
			document.body.appendChild(field);
			field.select();

			var ok = false;
			try {
				ok = document.execCommand('copy');
			} catch (e) {
				ok = false;
			}

			document.body.removeChild(field);
			if (ok) {
				resolve();
			} else {
				reject(new Error('copy failed'));
			}
		});
	}

	function initCopyLink() {
		var canCopy = !!(navigator.clipboard && window.isSecureContext) ||
			!!(document.queryCommandSupported && document.queryCommandSupported('copy'));
		if (!canCopy) return;

		toArray(document.querySelectorAll('[data-ev-copy]')).forEach(function (button) {
			var url = button.getAttribute('data-ev-copy');
			var holder = button.closest('li');
			var status = document.querySelector('[data-ev-copy-status]');
			var timer = null;
			if (!url) return;

			if (holder) holder.hidden = false;

			button.addEventListener('click', function () {
				copyText(url).then(function () {
					button.classList.add('is-copied');
					if (status) status.textContent = 'Link copied to your clipboard.';
				}, function () {
					if (status) status.textContent = 'Copy failed. Use your browser\'s address bar instead.';
				});

				window.clearTimeout(timer);
				timer = window.setTimeout(function () {
					button.classList.remove('is-copied');
					if (status) status.textContent = '';
				}, 3000);
			});
		});
	}

	function init() {
		initFormatFilter();
		initCalendarMenus();
		initCopyLink();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
