/**
 * Case studies hub: filter the stories by problem solved, without a reload.
 *
 * Hidden stories get the hidden attribute, so they also leave the tab order
 * and the accessibility tree. The featured story filters like any card.
 */
(function () {
	var toolbar = document.querySelector('[data-cs-filter]');
	if (!toolbar) {
		return;
	}

	var buttons = Array.prototype.slice.call(toolbar.querySelectorAll('[data-filter]'));
	var items = Array.prototype.slice.call(document.querySelectorAll('.hub-list [data-cat]')).map(function (item) {
		return { el: item, cats: (item.getAttribute('data-cat') || '').split(' ') };
	});
	var count = document.getElementById('cs-count');
	var empty = document.getElementById('cs-empty');
	var total = items.length;
	var noun = total === 1 ? 'case study' : 'case studies';

	toolbar.addEventListener('click', function (event) {
		var btn = event.target.closest('[data-filter]');
		if (!btn || btn.getAttribute('aria-pressed') === 'true') {
			return;
		}

		var want = btn.getAttribute('data-filter');
		var shown = 0;

		buttons.forEach(function (b) {
			b.setAttribute('aria-pressed', String(b === btn));
		});

		items.forEach(function (item) {
			var ok = !want || item.cats.indexOf(want) !== -1;
			item.el.hidden = !ok;
			if (ok) {
				shown++;
			}
		});

		if (count) {
			count.textContent = want
				? 'Showing ' + shown + ' of ' + total + ' ' + noun + ' for ' + btn.getAttribute('data-name')
				: 'Showing all ' + total + ' ' + noun;
		}
		if (empty) {
			empty.hidden = shown !== 0;
		}
	});
})();
