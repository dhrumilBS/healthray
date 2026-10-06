/**
 * Case studies hub: software filter tabs (archive-case-studies.php).
 * Hides cards whose data-cat does not include the chosen term slug and keeps
 * the live count and empty state in step.
 */
(function () {
	var toolbar = document.querySelector('[data-cs-filter]');
	if (!toolbar) {
		return;
	}

	var buttons = Array.prototype.slice.call(toolbar.querySelectorAll('[data-filter]'));
	var items = Array.prototype.slice.call(document.querySelectorAll('.hub-list [data-cat]'));
	var count = document.getElementById('cs-count');
	var empty = document.getElementById('cs-empty');
	var total = items.length;
	var noun = total === 1 ? 'case study' : 'case studies';

	buttons.forEach(function (btn) {
		btn.addEventListener('click', function () {
			var want = btn.getAttribute('data-filter');
			var shown = 0;

			buttons.forEach(function (b) {
				b.setAttribute('aria-pressed', String(b === btn));
			});

			items.forEach(function (item) {
				var cats = (item.getAttribute('data-cat') || '').split(' ');
				var ok = !want || cats.indexOf(want) !== -1;
				item.hidden = !ok;
				if (ok) {
					shown++;
				}
			});

			if (count) {
				count.textContent = want
					? 'Showing ' + shown + ' of ' + total + ' ' + noun + ': ' + btn.getAttribute('data-name')
					: 'Showing all ' + total + ' ' + noun;
			}
			if (empty) {
				empty.hidden = shown !== 0;
			}
		});
	});
})();
