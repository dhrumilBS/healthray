// BLog Page category Dropdown
window.addEventListener('load', function () {
	document.addEventListener('click', function(e) {
		if (!e.target.closest('.blog-category-more-list, .blog-category-more-link')) {
			document.querySelectorAll('.blog-category-more-link').forEach(function(element) {
				element.classList.remove('active');
				element.parentElement.classList.remove('more-category-open');
			});
			document.querySelectorAll('.blog-category-more-list').forEach(function(element) {
				element.style.display = 'none';
			});
		}
	});

	document.querySelectorAll('.blog-category-more-link').forEach(function(element) {
		element.addEventListener('click', function(e) {
			this.classList.toggle('active');
			this.parentElement.classList.toggle('more-category-open');
			const moreList = this.parentElement.querySelector('.blog-category-more-list');
			if (moreList.style.display === 'none' || moreList.style.display === '') {
				moreList.style.display = 'block';
			} else {
				moreList.style.display = 'none';
			}
			e.stopPropagation();
		});
	});
		swiperSlider()
});