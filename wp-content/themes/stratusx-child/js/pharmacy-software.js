(function () {
	var track = document.getElementById('tTrack');
	if (!track) return;

	var step = function () { return track.clientWidth + 16; };

	var prevBtn = document.getElementById('tPrev');
	var nextBtn = document.getElementById('tNext');

	if (prevBtn) {
		prevBtn.addEventListener('click', function () {
			track.scrollBy({ left: -step(), behavior: 'smooth' });
		});
	}
	if (nextBtn) {
		nextBtn.addEventListener('click', function () {
			track.scrollBy({ left: step(), behavior: 'smooth' });
		});
	}
})();