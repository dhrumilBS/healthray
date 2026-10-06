jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/before_after_slider.default', function ($scope) {
        initBeforeAfterSlider($scope);
    });
});

function initBeforeAfterSlider($scope) {
    const container = $scope.find('.ml-before-after-widget');
    if (!container.length) return;

    const before = container.find('.before-layer');
    const handle = container.find('.slider-handle');
    const divider = container.find('.slider-divider');
    const defaultPercent = parseFloat(container.data('default-percent')) || 50;

    const setPosition = (percent) => {
        const containerWidth = container.width();
        const x = containerWidth * (percent / 100);

        before.css('clip-path', `inset(0 ${containerWidth - x}px 0 0)`);
        handle.css('left', `${x}px`);
        divider.css('left', `${x}px`);
    };

    setPosition(defaultPercent);

    const drag = (e) => {
        const pageX = e.pageX || (e.touches && e.touches[0].pageX);
        const offset = container.offset().left;
        const x = pageX - offset;
        const percent = (x / container.width()) * 100;

        if (percent >= 0 && percent <= 100) {
            setPosition(percent);
        }
    };

    container.on('mousedown touchstart', function (e) {
        e.preventDefault();
        jQuery(document).on('mousemove touchmove', drag);
        jQuery(document).on('mouseup touchend', function () {
            jQuery(document).off('mousemove touchmove mouseup touchend', drag);
        });
    });
}