jQuery(document).ready(function ($) {
	jQuery.noConflict()
	// 	=========================================== BLOG FAQ ===========================================
	jQuery('.accordion-list > .elementor-toggle-item .elementor-tab-title').click(function () {
		if ($(this).hasClass("elementor-active")) {
			$(this).removeClass("elementor-active").parent().find('.elementor-tab-content').removeClass("elementor-active").slideUp();
		} else {
			$(this).addClass("elementor-active").parent().find('.elementor-tab-content').addClass("elementor-active").slideUp();
			$(this).parent().find('.elementor-tab-content').slideDown();
		}
		return false;
	});
	
	if(jQuery('.owl-carousel').length){
	    owl_carousel($(this))
	}
	// 	=========================================== owl-carousel ===========================================
});

function owl_carousel(e) {
	jQuery('.owl-carousel').each(function () {
		var app_slider = jQuery(this);

		var prev = '<span><svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.99983 1L3.05284 4.87375C3.03612 4.89016 3.02283 4.90979 3.01375 4.93147C3.00468 4.95316 3 4.97646 3 5C3 5.02354 3.00468 5.04684 3.01375 5.06853C3.02283 5.09021 3.03612 5.10984 3.05284 5.12625L7 9" stroke="currentcolor" stroke-linecap="round"/> </svg></span>';
		var next = '<span><svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M3.00017 9L6.94716 5.12625C6.96388 5.10984 6.97717 5.09021 6.98625 5.06853C6.99532 5.04684 7 5.02354 7 5C7 4.97646 6.99532 4.95316 6.98625 4.93147C6.97717 4.90979 6.96388 4.89016 6.94716 4.87375L3 1" stroke="currentcolor" stroke-linecap="round"/> </svg></span>';
		var prev_text = 'Prev';
		var next_text = 'Next';
		if (app_slider.data('prev_text') && app_slider.data('prev_text') != '') {
			prev_text = app_slider.data('prev_text');
		}
		if (app_slider.data('next_text') && app_slider.data('next_text') != '') {
			next_text = app_slider.data('next_text');
		}
		app_slider.owlCarousel({
			items: app_slider.data("desk_num"),
			loop: app_slider.data("loop"),
			margin: app_slider.data("margin"),
			dots: app_slider.data("dots"),
			loop: app_slider.data("loop"),
			autoplay: app_slider.data("autoplay"),
			autoplayTimeout: 5000,
			autoplayHoverPause:true,
			nav: app_slider.data("nav"),
			autoHeight: app_slider.data("autoheight"),
			navText: [prev, next],
			responsiveClass: true,
			responsive: {
				0: { items: app_slider.data("mob_sm") },
				480: { items: app_slider.data("mob_num") },
				786: { items: app_slider.data("tab_num") },
				1023: { items: app_slider.data("lap_num") },
				1199: { items: app_slider.data("desk_num") }
			}
		});
		if (jQuery('.custom-toggle-tabs').length > 0) {
			jQuery('.custom-toggle-tabs li:first').addClass('active');
			jQuery('.custom-toggle-tabs').on('click', 'li', function (e) {
				jQuery(this).addClass('active');
				jQuery(this).siblings().removeClass('active');
				app_slider.trigger('to.owl.carousel', [jQuery(this).index(), 300]);
			});
			app_slider.on('changed.owl.carousel', function (event) {
				var currentIndex = event.item.index;
				jQuery('.custom-toggle-tabs li').removeClass('active');
				jQuery('.custom-toggle-tabs li').eq(currentIndex).addClass('active');
			})
		}
		// Custom Pagination Functionality
		jQuery(".custom-pagination .dot").on("click", function () {
			const slideIndex = jQuery(this).data("slide");
			app_slider.trigger("to.owl.carousel", [slideIndex, 300]);
			jQuery(".custom-pagination .dot").removeClass("active");
			jQuery(this).addClass("active");
		});
		app_slider.on("changed.owl.carousel", function (event) {
			const currentIndex = event.item.index - event.relatedTarget._clones.length / 2;
			const realIndex = currentIndex < 0 ? event.item.count - 1 : currentIndex % event.item.count;
			jQuery(".custom-pagination .dot").removeClass("active");
			jQuery(".custom-pagination .dot").eq(realIndex).addClass("active");
		});
		jQuery(".custom-pagination .dot").eq(0).addClass("active");
	});
}

// ============================= TOGGLE LINK custom_toggle  ->  custom-toggle.php =====================================
function customToggle() {
	jQuery('.toggleLink').click(function (event) {
		event.preventDefault();
		var jQuerytoggleList = jQuery(this).next('.toggle-list');
		jQuerytoggleList.slideToggle(function () {
			var isVisible = jQuerytoggleList.is(":visible");
			if (isVisible) {
				jQuery(this).prev('.toggleLink').text('Read less -');
			} else {
				jQuery(this).prev('.toggleLink').text('Read more +');
			}
		});
	});
}

function alternative(e) {
	var selectedFile = e.find('#jsonFiles').val()
	tableContent(selectedFile);
	var yesImage = '<img width="26" height="26" decoding="async" style="width: 26px; height: 26px;" src="https://healthray.com/wp-content/themes/stratusx-child/assets/right.webp" alt="true" class="entered lazyloaded">';
	var noImage = '<img width="26" height="26" decoding="async" style="width: 26px; height: 26px;" src="https://healthray.com/wp-content/themes/stratusx-child/assets/cross.webp" alt="cross" class="entered lazyloaded">';

	// ml-alternative
	function tableContent(fileName) {
		jQuery.ajax({
			url: fileName,
			type: 'GET',
			dataType: 'json',
			success: function (jsonData) {
				if (jsonData.img) {
					jQuery('#jsonData tbody').html('')
					jQuery.each(jsonData.content, function (key, value) {
						const mapYesNo = val => val.trim().toLowerCase() === "yes" ? yesImage : val.trim().toLowerCase() === "no" ? noImage : val;						
						jQuery('#jsonData tbody').append('<tr> <td class="feature">' + value.key + '</td> <td class="our"> ' + mapYesNo(value.our) + '</td> <td class="other"> ' + mapYesNo(value.other) + '</td> </tr>');
					});
				}
			}
		});
	}
}

function new_slider() {
	const items = document.querySelectorAll('.left-column .item');
	const sliderContents = document.querySelectorAll('.right-column .slider-content');
	items.forEach(item => {
		item.addEventListener('click', () => {
			items.forEach(i => i.classList.remove('active'));
			sliderContents.forEach(content => content.classList.remove('active'));
			item.classList.add('active');
			const targetId = item.getAttribute('data-target');
			document.getElementById(targetId).classList.add('active');
		});
	});
}
function tabbar() {
	const tabLinks = document.querySelectorAll(".tab-link");
	const tabPanels = document.querySelectorAll(".tab-panel");

	tabLinks.forEach(link => {
		link.addEventListener("click", event => {
			event.preventDefault();
			const activeLink = document.querySelector(".tab-link.active");
			const activePanel = document.querySelector(".tab-panel.active");
			if (activeLink) activeLink.classList.remove("active");
			if (activePanel) activePanel.classList.remove("active");
			link.classList.add("active");
			const targetPanel = document.querySelector(link.getAttribute("href"));
			if (targetPanel) targetPanel.classList.add("active");
		});
	});
}



jQuery(window).on('elementor/frontend/init', function () {
	elementorFrontend.hooks.addAction('frontend/element_ready/swiper_widget.default', owl_carousel);
	elementorFrontend.hooks.addAction('frontend/element_ready/ml-doctor-reviews.default', owl_carousel);
	elementorFrontend.hooks.addAction('frontend/element_ready/slider_logo.default', owl_carousel);
	elementorFrontend.hooks.addAction('frontend/element_ready/ml-ehr-product-slider.default', owl_carousel);
	elementorFrontend.hooks.addAction('frontend/element_ready/ml-product-slider-2.default', owl_carousel);
	elementorFrontend.hooks.addAction('frontend/element_ready/ML_slider_set.default', owl_carousel);
	elementorFrontend.hooks.addAction('frontend/element_ready/testimonial.default', owl_carousel);
	elementorFrontend.hooks.addAction('frontend/element_ready/ml-pms-product-slider.default', owl_carousel);
	
	elementorFrontend.hooks.addAction('frontend/element_ready/ml-alternative.default', alternative);
	elementorFrontend.hooks.addAction('frontend/element_ready/custom_toggle.default', customToggle);
	elementorFrontend.hooks.addAction('frontend/element_ready/healthray-tabs2.default', tabbar);

	elementorFrontend.hooks.addAction('frontend/element_ready/ml_slider.default', new_slider);
});

jQuery(window).on('elementor/frontend/init', function () {
	setTimeout(function() {
		console.log(typeof jQuery.fn.owlCarousel);
	}, 500);
});