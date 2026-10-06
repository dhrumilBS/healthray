function healthrayTabs($scope) {
  var widgetRoot = $scope[0];

  widgetRoot.querySelectorAll('.tabs').forEach(function (tabsGroup) {
    var toggles = tabsGroup.querySelectorAll('.tab-toggle');
    console.log(toggles);

    // FIX 3: Initialize the first tab as active on load if none is already active.
    var hasActive = tabsGroup.querySelector('.tab-toggle.active');
    if (!hasActive && toggles.length > 0) {
      var firstId = toggles[0].dataset.tab;
      toggles[0].classList.add('active');

      var firstContent = tabsGroup.querySelector('.tab-content[data-content="' + firstId + '"]');
      if (firstContent) {
        firstContent.classList.add('active');
        // FIX 2: Guard against missing .position-relative ancestor before accessing classList.
        var firstParent = firstContent.closest('.position-relative');
        if (firstParent) firstParent.classList.add('active');
      }
    }

    toggles.forEach(function (toggle) {
      toggle.addEventListener('click', function () {
        var id = this.dataset.tab;

        // Deactivate all toggles, contents, and position-relative wrappers.
        tabsGroup
          .querySelectorAll('.tab-toggle, .tab-content, .tabs-content > .position-relative')
          .forEach(function (el) {
            el.classList.remove('active');
          });

        // Activate all toggles matching this id (e.g. mobile + desktop copies).
        tabsGroup
          .querySelectorAll('.tab-toggle[data-tab="' + id + '"]')
          .forEach(function (el) {
            el.classList.add('active');
          });

        var content = tabsGroup.querySelector('.tab-content[data-content="' + id + '"]');

        // FIX 2: Guard null — if content or its wrapper is missing, bail out safely.
        if (!content) return;
        content.classList.add('active');

        var wrapper = content.closest('.position-relative');
        if (wrapper) wrapper.classList.add('active');
      });
    });
  });

  // FIX 6: Scope height sync to this widget only, not the whole document.
  function syncTabsHeight() {
    widgetRoot.querySelectorAll('.tabs').forEach(function (tabsGroup) {
      var leftCol = tabsGroup.querySelector('.tabs-left');
      var contentCol = tabsGroup.querySelector('.tabs-content');
      if (!leftCol || !contentCol) return;

      var leftHeight = leftCol.offsetHeight;
      contentCol.style.maxHeight = leftHeight + 'px';
      contentCol.style.overflowY = 'auto';
    });
  }

  function handleTabsHeight() {
    if (window.innerWidth >= 992) {
      syncTabsHeight();
    } else {
      // Reset inline styles on narrow viewports.
      widgetRoot.querySelectorAll('.tabs-content').forEach(function (el) {
        el.style.maxHeight = '';
        el.style.overflowY = '';
      });
    }
  }

  // FIX 5: Debounce the resize handler to avoid excessive layout recalculations.
  var resizeTimer;
  function debouncedResize() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(handleTabsHeight, 150);
  }

  // FIX 4: Run once immediately (Elementor fires this after the element is ready,
  // so fonts/layout are settled), and also on resize.
  handleTabsHeight();
  window.addEventListener('resize', debouncedResize);
}

jQuery(window).on('elementor/frontend/init', function () {
  elementorFrontend.hooks.addAction('frontend/element_ready/healthray-tabs.default', healthrayTabs );
});