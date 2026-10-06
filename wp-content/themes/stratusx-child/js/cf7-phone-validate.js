document.addEventListener('wpcf7init', function () {
    const input = document.querySelector('#lf-phone');
    if (!input) return;

    const iti = window.intlTelInput(input, {
        initialCountry: 'in',        // fixed country
        countries: ['in'],           // restrict to this country only
        showFlags: false,            // no flag icon
        allowDropdown: false,        // no dropdown/selector at all
        separateDialCode: true,      // shows "+91" as a static prefix
        utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@23/build/js/utils.js'
    });

    const errorBox = document.querySelector('#lf-phone-error');

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.style.display = 'block';
        input.setCustomValidity(msg);
    }
    function clearError() {
        errorBox.style.display = 'none';
        input.setCustomValidity('');
    }

    function validate() {
        if (input.value.trim() === '') {
            showError('Mobile number is required.');
            return false;
        }
        if (!iti.isValidNumber()) {
            const errorMap = {
                0: 'Invalid number.',
                2: 'Number is too short.',
                3: 'Number is too long.'
            };
            const code = iti.getValidationError();
            showError(errorMap[code] || 'Please enter a valid 10-digit mobile number.');
            return false;
        }
        clearError();
        return true;
    }

    input.addEventListener('blur', validate);
    input.addEventListener('input', clearError);

    const form = input.closest('form');
    form.addEventListener('wpcf7beforesubmit', function (e) {
        if (!validate()) {
            e.preventDefault();
        }
    });

    // Inject full E.164 number into a hidden field right before submit
    form.addEventListener('submit', function () {
        let hidden = form.querySelector('#lf-phone-full');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'your-number-full';
            hidden.id = 'lf-phone-full';
            form.appendChild(hidden);
        }
        hidden.value = iti.getNumber(); // e.g. +919876543210
    });
});