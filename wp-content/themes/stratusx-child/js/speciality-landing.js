/**
 * Speciality landing pages:
 * - auto-advance the testimonial track (template-parts/speciality/testimonials.php),
 *   like the Owl carousel it replaces. The track is a native scroll-snap row, so
 *   swipe, trackpad and keyboard scrolling work without this file.
 * - before/after comparison in feature rows (template-parts/speciality/feature-row.php):
 *   the range input moves the divider.
 * - FAQ (template-parts/speciality/faq.php): <details name="sp-faq"> keeps one item
 *   open natively; this closes the others for browsers without that support.
 * Reveal-on-scroll stays in script.js.
 */
(function () {
	document.querySelectorAll('.sp-faq__list').forEach(function (list) {
		var items = list.querySelectorAll('.sp-faq__item');
		items.forEach(function (item) {
			item.addEventListener('toggle', function () {
				if (!item.open) {
					return;
				}
				items.forEach(function (other) {
					if (other !== item && other.open) {
						other.open = false;
					}
				});
			});
		});
	});

	document.querySelectorAll('[data-sp-compare]').forEach(function (compare) {
		var range = compare.querySelector('.sp-compare__range');
		if (!range) {
			return;
		}
		function update() {
			compare.style.setProperty('--sp-pos', range.value + '%');
		}
		range.addEventListener('input', update);
		update();
	});

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	if (reduceMotion) {
		return;
	}

	document.querySelectorAll('[data-sp-slider]').forEach(function (slider) {
		var track = slider.querySelector('.sp-t__track');
		var paused = false;
		var stopped = false;
		if (!track) {
			return;
		}

		function advance() {
			var card = track.firstElementChild;
			var max = track.scrollWidth - track.clientWidth;
			if (paused || stopped || document.hidden || !card || max <= 0) {
				return;
			}
			var gap = parseFloat(getComputedStyle(track).columnGap) || 0;
			var next = track.scrollLeft + card.offsetWidth + gap;
			track.scrollTo({ left: next > max - 2 ? 0 : next, behavior: 'smooth' });
		}

		slider.addEventListener('mouseenter', function () { paused = true; });
		slider.addEventListener('mouseleave', function () { paused = false; });
		slider.addEventListener('focusin', function () { paused = true; });
		slider.addEventListener('focusout', function () { paused = false; });
		// A reader who swipes has taken over: stop advancing for this visit.
		slider.addEventListener('touchstart', function () { stopped = true; }, { passive: true });

		setInterval(advance, 5000);
	});
})();