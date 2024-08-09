function testmonial() {
  jQuery('.pt-testimonial .owl-carousel ').each(function () {
    var app_slider = jQuery(this);
    var rtl = false;
    var prev = 'fa fa-chevron-left';
    var next = 'fa fa-chevron-right';
    var prev_text = 'Prev';
    var next_text = 'Next';
    if (jQuery('body').hasClass('pt-is-rtl')) {
      rtl = true;
      prev = 'fa fa-chevron-right';
      next = 'fa fa-chevron-left';
    }
    if (app_slider.data('prev_text') && app_slider.data('prev_text') != '') {
      prev_text = app_slider.data('prev_text');
    }
    if (app_slider.data('next_text') && app_slider.data('next_text') != '') {
      next_text = app_slider.data('next_text');
    }
    app_slider.owlCarousel({
      rtl: rtl,
      items: app_slider.data("desk_num"),
      loop: app_slider.data("loop"),
      margin: app_slider.data("margin"),
      nav: app_slider.data("nav"),
      dots: app_slider.data("dots"),
      loop: app_slider.data("loop"),
      autoplay: app_slider.data("autoplay"),
      autoplayHoverPause: true,
      autoplayTimeout: app_slider.data("autoplay-timeout"),
      navText: ["<i class='" + prev + "'></i>", "<i class='" + next + "'></i>"],
      responsiveClass: true,
      responsive: {
        // breakpoint from 0 up
        0: {
          items: app_slider.data("mob_sm"),
          //nav: true,
          //dots: false
        },
        // breakpoint from 480 up
        480: {
          items: app_slider.data("mob_num"),
          //nav: true,
          //dots: false
        },
        // breakpoint from 786 up
        786: {
          items: app_slider.data("tab_num")
        },
        // breakpoint from 1023 up
        1023: {
          items: app_slider.data("lap_num")
        },
        1199: {
          items: app_slider.data("desk_num")
        }
      }
    });
  });
}; 
if (jQuery('.pt-testimonial').length > 0) {
  testmonial();
}