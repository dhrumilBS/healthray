(function () {
	var facade = document.getElementById('videoFacade');
	if (facade) {
		var loadVideo = function () {
			facade.innerHTML = '<iframe src="https://www.youtube-nocookie.com/embed/VRHZ9ejnBWk?autoplay=1&rel=0" title="An Operations Leader\'s Perspective - Healthray at Universal Hospital, Surat" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
			facade.style.cursor = 'default';
		};
		facade.addEventListener('click', loadVideo, { once: true });
		facade.addEventListener('keydown', function (e) {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				loadVideo();
			}
		}, { once: true });
	}
})();