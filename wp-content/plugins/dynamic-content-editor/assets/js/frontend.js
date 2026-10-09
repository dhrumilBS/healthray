(document).addEventListener('click', function (event) {
    var toggle = event.target.closest('.dce-comparison-section-toggle');

    if (!toggle) {
        return;
    }

    var id = toggle.getAttribute('aria-controls');
    var panel = document.getElementById(id);

    if (!panel) {
        return;
    }

    var open = toggle.getAttribute('aria-expanded') === 'true';

    toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
    panel.classList.toggle('is-open', !open);
});
