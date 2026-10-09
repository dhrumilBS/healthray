/**
 * ABHA Card form - front-end controller.
 *
 * Two entry points, both ending in an ABHA card:
 *
 *   Aadhaar tab : aadhaar OTP -> (optional alternate-mobile OTP) -> address
 *                 suggestions -> link  ->  card image
 *   Mobile tab  : mobile OTP -> pick an existing ABHA address, or fill in
 *                 demographics -> suggestions -> login  ->  card image
 *
 * Every request goes to admin-ajax.php with the abha_nonce; the PHP side in
 * includes/class-abha-card.php owns all validation and talks to the API.
 */
(function ($) {
    'use strict';

    $(function () {
        const $wrapper = $('.abha-wrapper');
        if (!$wrapper.length) {
            return;
        }

        const $commonSection = $('#common-section');
        const $adharOtpSection = $('#adhar-otp-section');
        const $mobileOtpSection = $('#mobile-otp-section');
        const $adharMobileOtpSection = $('#adhar-mobile-otp-section');
        const $chooseAbhaSection = $('#choose-abha-section');
        const $createAbhaSection = $('#create-abha-section');
        const $suggestionSection = $('#suggestion-section');

        const $sections = $commonSection
            .add($adharOtpSection).add($mobileOtpSection).add($adharMobileOtpSection)
            .add($chooseAbhaSection).add($createAbhaSection).add($suggestionSection);

        const $responseMessage = $('#response-message');
        const $loadingSpinner = $('#loading-spinner');
        const $transactionIdField = $('#transaction-id');

        const $resendButtons = $('.otp-resend-btn');
        const $altMobileInput = $('#adhar-otp-mobile-input');

        // Demographics form
        const $createForm = $('#create-abha-form');
        const $fname = $('#fname');
        const $mname = $('#mname');
        const $lname = $('#lname');
        const $day = $('#day');
        const $month = $('#month');
        const $year = $('#year');
        const $genderInputs = $('input[name="gender"]');
        const $genderGroup = $('.gender-group');
        const $state = $('#state');
        const $district = $('#district');
        const $pincode = $('#pincode');
        const $address = $('#address');
        const $demographicsBtn = $('#demographics-submit-btn');

        // Suggestions
        const $suggestionList = $('.suggestion-list');
        const $suggestionWrap = $('.suggestion-list-wrap');
        const $addressInput = $('#abha-address-input');
        const $addressType = $('#abha-address-type');
        const $submitSuggestion = $('#submitSuggestion');
        const VERIFY_ACTION = {
            'aadhaar': 'verify_aadhaar_otp',
            'mobile': 'verify_PHR_otp',
            'aadhaar-mobile': 'verify_adhar_mobile_otp',
        };

        const AADHAAR_RE = /^[2-9][0-9]{11}$/;
        const MOBILE_RE = /^[6-9][0-9]{9}$/;
        const OTP_RE = /^[0-9]{6}$/;
        const ADDRESS_RE = /^[a-zA-Z0-9_.]+$/;
        const TX_STORAGE_KEY = 'abha_transactionId';

        let currentType = null;   
        let currentNumber = null;   
        let aadhaarProfile = null;   
        let statesLoaded = false;
        let resendTimer = null;
        function toggleLoading(show) {
            if (show) {
                $loadingSpinner.removeAttr('hidden').attr('aria-busy', 'true');
            } else {
                $loadingSpinner.attr('hidden', '').attr('aria-busy', 'false');
            }
        }

        function showMessage(text, kind) {
            $responseMessage
                .text(text)
                .removeAttr('hidden')
                .removeClass('is-success is-error')
                .addClass(kind);
        }

        function clearMessage() {
            $responseMessage.attr('hidden', '').removeClass('is-success is-error').text('');
        }

        function clearFieldErrors() {
            $createForm.find('.inline-error').remove();
            $createForm.find('.is-error').removeClass('is-error');
        }

        function addFieldError($el, message) {
            $el.addClass('is-error');
            $('<span class="inline-error" role="alert"></span>').text(message).insertAfter($el);
        }

        function digitsOnly(value) {
            return String(value == null ? '' : value).replace(/\D+/g, '');
        }

        function requestErrorMessage(xhr) {
            if (xhr && xhr.status === 0) {
                return 'Network error. Please check your connection and try again.';
            }

            const fromServer = xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message;

            return fromServer || 'Something went wrong. Please try again.';
        }

        function lockButton($button) {
            if (!$button || !$button.length) {
                return function () { };
            }

            $button.prop('disabled', true).attr('aria-busy', 'true');

            return function () {
                $button.prop('disabled', false).removeAttr('aria-busy');
            };
        }

        function showSection($section) {
            $sections.attr('hidden', '');
            $section.removeAttr('hidden');
            clearMessage();
        }

        function setTransactionId(id) {
            if (!id) {
                return;
            }

            $transactionIdField.val(id);
            try {
                localStorage.setItem(TX_STORAGE_KEY, id);
            } catch (err) {
                // Private mode / storage disabled - the hidden field still holds it.
            }
        }

        function getTransactionId() {
            let id = $transactionIdField.val().trim();

            if (!id) {
                try {
                    id = localStorage.getItem(TX_STORAGE_KEY) || '';
                } catch (err) {
                    id = '';
                }
            }

            return id;
        }

        function clearTransactionId() {
            $transactionIdField.val('');

            try {
                localStorage.removeItem(TX_STORAGE_KEY);
            } catch (err) {
                // Nothing to clean up.
            }
        }

        function ajaxUrl() {
            return (typeof ajax_obj === 'object' && ajax_obj.url) ? ajax_obj.url : ajax_obj;
        }

        function withNonce(data) {
            const nonce = (typeof ajax_obj === 'object' && ajax_obj.nonce) ? ajax_obj.nonce : '';

            return $.extend({ _ajax_nonce: nonce }, data);
        }

        function apiPost(action, data) {
            return $.ajax({
                url: ajaxUrl(),
                type: 'POST',
                dataType: 'json',
                data: withNonce($.extend({ action: action }, data)),
            });
        }

        function apiGet(action, params) {
            return $.ajax({
                url: ajaxUrl(),
                type: 'GET',
                dataType: 'json',
                data: withNonce($.extend({ action: action }, params)),
            });
        }

        function displayImagePreview(imageUrl) {
            $wrapper.find('.image-preview').remove();
            $('body').find('.abha-modal-overlay').remove();

            const $img = $('<img alt="ABHA Card">').attr('src', imageUrl);
            const $download = $('<a class="btn btn-primary" download="ABHA_Card">Download Card</a>')
                .attr('href', imageUrl);

            $wrapper.append(
                $('<div class="image-preview"></div>').append($img, $download)
            );

            const $modal = $('<div class="abha-modal-overlay" role="dialog" aria-modal="true" aria-label="ABHA Card"></div>');
            const $close = $('<button type="button" class="abha-modal-close" aria-label="Close">&times;</button>');

            $modal.append(
                $('<div class="abha-modal-box"></div>').append(
                    $close,
                    $img.clone(),
                    $download.clone()
                )
            );

            $('body').append($modal);

            $modal.on('click', function (e) {
                if (e.target === this || $(e.target).is('.abha-modal-close')) {
                    $modal.remove();
                }
            });
        }

        function populateDateDropdowns() {
            if ($day.find('option').length > 1) {
                return;
            }

            for (let d = 1; d <= 31; d++) {
                const value = String(d).padStart(2, '0');
                $day.append($('<option></option>').val(value).text(value));
            }

            const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December',
            ];

            months.forEach(function (name, i) {
                $month.append($('<option></option>').val(String(i + 1).padStart(2, '0')).text(name));
            });

            const thisYear = new Date().getFullYear();

            for (let y = thisYear; y >= 1900; y--) {
                $year.append($('<option></option>').val(String(y)).text(String(y)));
            }
        }

        function startResendTimer(seconds) {
            let remaining = seconds || 30;

            clearInterval(resendTimer);
            $resendButtons.prop('disabled', true).text('Resend in ' + remaining + 's');

            resendTimer = setInterval(function () {
                remaining -= 1;

                if (remaining <= 0) {
                    clearInterval(resendTimer);
                    $resendButtons.prop('disabled', false).text('Resend OTP');
                    return;
                }

                $resendButtons.text('Resend in ' + remaining + 's');
            }, 1000);
        }

        function resetFlow() {
            clearInterval(resendTimer);
            clearTransactionId();
            currentType = null;
            currentNumber = null;
            aadhaarProfile = null;
            $resendButtons.prop('disabled', true).text('Resend OTP');
            $wrapper.find('.image-preview').remove();
            $('.otp-input').val('');
            $suggestionList.empty().text('No suggestions yet.');
            $suggestionWrap.addClass('no-data');
            $addressInput.val('').prop('disabled', true);
            $submitSuggestion.prop('disabled', true);
        }

        function validateDemographicsForm() {
            clearFieldErrors();

            let valid = true;

            function required($el, message) {
                if (!$el.val() || !$el.val().trim()) {
                    addFieldError($el, message);
                    valid = false;
                }
            }

            required($fname, 'First name is required.');
            required($state, 'State is required.');
            required($district, 'District is required.');
            required($pincode, 'Pincode is required.');
            required($address, 'Address is required.');

            if ($pincode.val() && !/^[1-9][0-9]{5}$/.test($pincode.val().trim())) {
                addFieldError($pincode, 'Enter a valid 6-digit pincode.');
                valid = false;
            }

            const day = parseInt($day.val(), 10);
            const month = parseInt($month.val(), 10);
            const year = parseInt($year.val(), 10);
            const thisYear = new Date().getFullYear();
            const maxDay = (month && year) ? new Date(year, month, 0).getDate() : 31;

            if (!month || month < 1 || month > 12) {
                addFieldError($month, 'Month is required.');
                valid = false;
            }

            if (!year || year < 1900 || year > thisYear) {
                addFieldError($year, 'Year must be between 1900 and ' + thisYear + '.');
                valid = false;
            }

            if (!day || day < 1 || day > maxDay) {
                addFieldError($day, 'Day must be between 1 and ' + maxDay + '.');
                valid = false;
            }

            if (!$genderInputs.is(':checked')) {
                addFieldError($genderGroup, 'Gender is required.');
                valid = false;
            }

            return valid;
        }

        function fetchStates() {
            apiGet('get_states')
                .done(function (res) {
                    if (res.status === 200 && $.isArray(res.data)) {
                        $state.empty().append($('<option value="">Select a State</option>'));

                        res.data.forEach(function (item) {
                            $state.append($('<option></option>').val(item.code).text(item.name));
                        });

                        return;
                    }

                    statesLoaded = false;
                    showMessage(
                        (res.data && res.data.message) || res.message || 'Could not load the state list.',
                        'is-error'
                    );
                })
                .fail(function (xhr) {
                    statesLoaded = false;
                    showMessage(requestErrorMessage(xhr), 'is-error');
                });
        }

        function fetchDistricts(stateId) {
            $district.empty().append($('<option value="">Loading…</option>')).prop('disabled', true);

            apiGet('get_districts', { state_ID: stateId })
                .done(function (res) {
                    $district.empty().append($('<option value="">Select a District</option>'));

                    const districts = (res.success && res.data && res.data.data) || [];

                    if ($.isArray(districts) && districts.length) {
                        districts.forEach(function (item) {
                            $district.append($('<option></option>').val(item.code).text(item.name));
                        });

                        $district.prop('disabled', false);
                        return;
                    }

                    $district.append($('<option value="">No districts found</option>'));
                    showMessage(
                        (res.data && res.data.message) || 'Could not load districts for that state.',
                        'is-error'
                    );
                })
                .fail(function (xhr) {
                    $district.empty().append($('<option value="">Failed to load</option>'));
                    showMessage(requestErrorMessage(xhr), 'is-error');
                });
        }

        function fetchSuggestions(action, transactionId) {
            apiGet(action, { transaction_id: transactionId })
                .done(function (res) {
                    const suggestions = (res.success && res.data && res.data.suggestions) || [];

                    if ($.isArray(suggestions) && suggestions.length) {
                        renderSuggestions(suggestions, action);
                        return;
                    }

                    enableAddressEntry(action);
                    showMessage(
                        (res.data && res.data.message) || 'No suggestions available. Enter a custom ABHA address.',
                        'is-error'
                    );
                })
                .fail(function (xhr) {
                    enableAddressEntry(action);
                    showMessage(requestErrorMessage(xhr), 'is-error');
                });
        }

        function enableAddressEntry(actionType) {
            $suggestionWrap.removeClass('no-data');
            $addressType.val(actionType);
            $addressInput.prop('disabled', false);
            $submitSuggestion.prop('disabled', false);
        }

        function renderSuggestions(suggestions, actionType) {
            enableAddressEntry(actionType);
            $suggestionList.empty();

            suggestions.forEach(function (name) {
                const $item = $('<div class="suggestion-item"></div>')
                    .attr('data-address', name)
                    .text(name);

                $item.on('click', function () {
                    $suggestionList.find('.suggestion-item').removeClass('selected-address');
                    $item.addClass('selected-address');
                    $addressInput.val(name).siblings('.inline-error').remove();
                });

                $suggestionList.append($item);
            });
        }

        function handleAuthFormSubmit(e) {
            e.preventDefault();
            clearMessage();

            const $form = $(e.currentTarget);
            const $submit = $form.find('button[type="submit"]');
            const type = $form.data('type');
            const number = digitsOnly($form.find('.auth-input').val());
            const action = (type === 'aadhaar') ? 'aadhaar_auth_form_submit' : 'PHR_mobile_auth_form_submit';
            const $consent = $form.find('input[type="checkbox"]');
            if ($consent.length && !$consent.is(':checked')) {
                showMessage('Please accept the consent checkbox to continue.', 'is-error');
                return;
            }

            if (!number) {
                showMessage(type === 'aadhaar' ? 'Aadhaar number is required.' : 'Mobile number is required.', 'is-error');
                return;
            }

            if (type === 'aadhaar' && !AADHAAR_RE.test(number)) {
                showMessage('Please enter a valid 12-digit Aadhaar number.', 'is-error');
                return;
            }

            if (type === 'mobile' && !MOBILE_RE.test(number)) {
                showMessage('Please enter a valid 10-digit mobile number.', 'is-error');
                return;
            }

            resetFlow();
            currentType = type;
            currentNumber = number;

            const unlock = lockButton($submit);
            toggleLoading(true);

            apiPost(action, { adharnumber: number })
                .done(function (res) {
                    if (!res.success) {
                        showMessage((res.data && res.data.message) || 'Failed to send OTP.', 'is-error');
                        return;
                    }

                    setTransactionId(res.data.transactionId);
                    showSection(type === 'aadhaar' ? $adharOtpSection : $mobileOtpSection);
                    showMessage(res.data.message || 'OTP sent.', 'is-success');
                    startResendTimer();
                })
                .fail(function (xhr) {
                    showMessage(requestErrorMessage(xhr), 'is-error');
                })
                .always(function () {
                    toggleLoading(false);
                    unlock();
                });
        }

        function handleOtpFormSubmit(e) {
            e.preventDefault();
            clearMessage();

            const $form = $(e.currentTarget);
            const $submit = $form.find('button[type="submit"]');
            const formType = $form.data('type');
            const otp = digitsOnly($form.find('.otp-input').val());
            const transactionId = getTransactionId();

            if (!OTP_RE.test(otp)) {
                showMessage('Please enter a valid 6-digit OTP.', 'is-error');
                return;
            }

            if (!transactionId) {
                showMessage('Your session has expired. Please request a new OTP.', 'is-error');
                return;
            }

            const payload = {
                otp: otp,
                transactionId: transactionId,
            };

            if (formType === 'aadhaar' || formType === 'aadhaar-mobile') {
                const mobile = digitsOnly($altMobileInput.val());

                if (formType === 'aadhaar' && !MOBILE_RE.test(mobile)) {
                    showMessage('Please enter a valid 10-digit mobile number.', 'is-error');
                    return;
                }

                payload.number = mobile;
            }

            const unlock = lockButton($submit);
            toggleLoading(true);

            apiPost(VERIFY_ACTION[formType], payload)
                .done(function (res) {
                    if (!res.success) {
                        showMessage((res.data && res.data.message) || 'Verification failed.', 'is-error');
                        return;
                    }

                    const data = res.data;

                    if (formType === 'aadhaar') {
                        if (data.imageData) {
                            onCardImage(data);
                        } else if (data.userData) {
                            onAadhaarVerified(data);
                        }
                        return;
                    }

                    if (formType === 'mobile') {
                        onPhrVerified(data);
                        return;
                    }

                    if (formType === 'aadhaar-mobile') {
                        onAltMobileVerified(data);
                    }
                })
                .fail(function (xhr) {
                    showMessage(requestErrorMessage(xhr), 'is-error');
                })
                .always(function () {
                    toggleLoading(false);
                    unlock();
                });
        }

        function onCardImage(data) {
            displayImagePreview(data.image_url);
            $sections.attr('hidden', '');
            showMessage(data.message || 'Your ABHA card is ready.', 'is-success');
        }

        function onAadhaarVerified(data) {
            aadhaarProfile = data.data || {};
            setTransactionId(aadhaarProfile.transaction_id);
            showMessage(data.message || 'OTP verified.', 'is-success');

            if (aadhaarProfile.otp_required) {
                sendAltMobileOtp(getTransactionId(), digitsOnly($altMobileInput.val()));
                return;
            }

            showSection($suggestionSection);
            fetchSuggestions('get_aadhaar_suggestion', getTransactionId());
        }

        function onPhrVerified(data) {
            setTransactionId(data.transactionId);

            const addresses = $.isArray(data.mappedPhrAddress) ? data.mappedPhrAddress : [];
            const $list = $('.abha-list').empty();
            const $foundText = $('.founded-address');

            if (addresses.length) {
                $foundText.text('We found ' + addresses.length + ' ABHA address(es) linked to your number.');

                const $dropdown = $('<select class="input-field phr-dropdown" aria-label="ABHA address"></select>')
                    .append($('<option value="" disabled selected>Select ABHA Address</option>'));

                addresses.forEach(function (address) {
                    $dropdown.append($('<option></option>').val(address).text(address));
                });

                const $continue = $('<button type="button" class="btn btn-primary" id="select-phr-btn">Continue</button>');

                $continue.on('click', function () {
                    const selected = $dropdown.val();

                    if (!selected) {
                        showMessage('Please select an ABHA address.', 'is-error');
                        return;
                    }

                    phrLogin(getTransactionId(), selected, true, $continue);
                });

                $list.append($dropdown, $('<div class="form-actions"></div>').append($continue));
            } else {
                $foundText.text('No existing ABHA address found. Create a new one below.');
            }

            showSection($chooseAbhaSection);
            showMessage(data.message || 'OTP verified.', 'is-success');
        }

        function onAltMobileVerified(data) {
            setTransactionId(data.transactionId || (data.data && data.data.transaction_id));
            showSection($suggestionSection);
            fetchSuggestions('get_aadhaar_suggestion', getTransactionId());
            showMessage(data.message || 'OTP verified.', 'is-success');
        }

        function sendAltMobileOtp(transactionId, mobileNumber) {
            toggleLoading(true);

            apiPost('handle_aadhaar_mobile_submit', {
                transaction_id: transactionId,
                number: mobileNumber,
            })
                .done(function (res) {
                    if (!res.success) {
                        showMessage((res.data && res.data.message) || 'Failed to send OTP.', 'is-error');
                        return;
                    }

                    setTransactionId(res.data.transactionId);
                    showSection($adharMobileOtpSection);
                    showMessage(res.data.message || 'OTP sent to your mobile number.', 'is-success');
                    startResendTimer();
                })
                .fail(function (xhr) {
                    showMessage(requestErrorMessage(xhr), 'is-error');
                })
                .always(function () {
                    toggleLoading(false);
                });
        }

        function handleAddressSubmit(e) {
            e.preventDefault();

            const address = $addressInput.val().trim();
            const actionType = $addressType.val().trim();

            if (!address) {
                showMessage('Please enter or select an ABHA address.', 'is-error');
                return;
            }

            if (!ADDRESS_RE.test(address)) {
                showMessage('ABHA address can contain only letters, numbers, underscore and dot.', 'is-error');
                return;
            }

            if (actionType === 'get_PHR_suggestion') {
                phrLogin(getTransactionId(), address, false, $submitSuggestion);
                return;
            }

            if (!aadhaarProfile) {
                showMessage('Your session has expired. Please start again.', 'is-error');
                return;
            }

            const existing = $.isArray(aadhaarProfile.abha_address) ? aadhaarProfile.abha_address : [];

            if (existing.indexOf(address) !== -1 || existing.indexOf(address + '@sbx') !== -1) {
                showMessage('"' + address + '" is already registered.', 'is-error');
                return;
            }

            linkAbha({
                abha_address: address,
                transaction_id: getTransactionId(),
                first_name: aadhaarProfile.first_name,
                middle_name: aadhaarProfile.middle_name,
                last_name: aadhaarProfile.last_name,
                gender: aadhaarProfile.gender,
                abha_number: aadhaarProfile.abha_number,
                mobile_no: digitsOnly($altMobileInput.val()),
                tokens: {
                    token: aadhaarProfile.tokens && aadhaarProfile.tokens.token,
                    refresh_token: aadhaarProfile.tokens && aadhaarProfile.tokens.refresh_token,
                },
            });
        }

        function linkAbha(payload) {
            const unlock = lockButton($submitSuggestion);

            clearMessage();
            toggleLoading(true);

            apiPost('handle_link_abha', { payload: payload })
                .done(function (res) {
                    if (!res.success) {
                        showMessage((res.data && res.data.message) || 'Failed to create the ABHA address.', 'is-error');
                        return;
                    }
                    if (res.data.image_url) {
                        displayImagePreview(res.data.image_url);
                    }

                    $suggestionSection.attr('hidden', '');
                    showMessage(res.data.message || 'ABHA card created successfully.', 'is-success');
                })
                .fail(function (xhr) {
                    showMessage(requestErrorMessage(xhr), 'is-error');
                })
                .always(function () {
                    toggleLoading(false);
                    unlock();
                });
        }

        function phrLogin(transactionId, phrAddress, alreadyRegistered, $button) {
            if (!phrAddress) {
                showMessage('Please select or enter an ABHA address.', 'is-error');
                return;
            }

            const unlock = lockButton($button);

            clearMessage();
            $wrapper.find('.image-preview').remove();
            toggleLoading(true);

            apiPost('login_phr_address', {
                transaction_id: transactionId,
                phr_address: phrAddress,
                already_registered: alreadyRegistered,
            })
                .done(function (res) {
                    if (!res.success) {
                        showMessage((res.data && res.data.message) || 'Could not confirm that ABHA address.', 'is-error');
                        return;
                    }

                    if (res.data.image_url) {
                        displayImagePreview(res.data.image_url);
                    }

                    $suggestionSection.attr('hidden', '');
                    $chooseAbhaSection.attr('hidden', '');
                    showMessage(res.data.message || 'ABHA address confirmed.', 'is-success');
                })
                .fail(function (xhr) {
                    showMessage(requestErrorMessage(xhr), 'is-error');
                })
                .always(function () {
                    toggleLoading(false);
                    unlock();
                });
        }

        function submitDemographics() {
            if (!validateDemographicsForm()) {
                return;
            }

            const transactionId = getTransactionId();

            if (!transactionId) {
                showMessage('Your session has expired. Please start again.', 'is-error');
                return;
            }

            const unlock = lockButton($demographicsBtn);
            const dob = [$year.val(), $month.val(), $day.val()].join('-');

            toggleLoading(true);

            apiPost('PHR_demographics_submit', {
                transaction_id: transactionId,
                first_name: $fname.val().trim(),
                middle_name: $mname.val().trim(),
                last_name: $lname.val().trim(),
                pin_code: digitsOnly($pincode.val()),
                gender: $genderInputs.filter(':checked').val(),
                dob: dob,
                mobile_no: currentNumber,
                state_code: $state.val(),
                district_code: $district.val(),
                address: $address.val().trim(),
            })
                .done(function (res) {
                    if (!res.success) {
                        showMessage((res.data && res.data.message) || 'Submission failed.', 'is-error');
                        return;
                    }

                    showSection($suggestionSection);
                    fetchSuggestions('get_PHR_suggestion', transactionId);
                    showMessage(res.data.message || 'Details submitted.', 'is-success');
                })
                .fail(function (xhr) {
                    showMessage(requestErrorMessage(xhr), 'is-error');
                })
                .always(function () {
                    toggleLoading(false);
                    unlock();
                });
        }

        $('.tab-btn').on('click', function () {
            const view = $(this).data('view');

            $('.tab-btn').removeClass('active').attr('aria-selected', 'false');
            $(this).addClass('active').attr('aria-selected', 'true');

            $('.auth-form').removeClass('active');
            $('.auth-form[data-type="' + view + '"]').addClass('active');

            clearMessage();
        });

        $('.auth-form').on('submit', handleAuthFormSubmit);
        $('.otp-form').on('submit', handleOtpFormSubmit);
        $('#abha-address-form').on('submit', handleAddressSubmit);
        $demographicsBtn.on('click', submitDemographics);

        $resendButtons.on('click', function (e) {
            e.preventDefault();
            clearMessage();
            if ($(this).closest('.abha-section').is($adharMobileOtpSection)) {
                sendAltMobileOtp(getTransactionId(), digitsOnly($altMobileInput.val()));
                return;
            }

            if (!currentType || !currentNumber) {
                showMessage('Please enter your number again to receive a new OTP.', 'is-error');
                return;
            }

            const action = (currentType === 'aadhaar') ? 'aadhaar_auth_form_submit' : 'PHR_mobile_auth_form_submit';

            $resendButtons.prop('disabled', true);
            toggleLoading(true);

            apiPost(action, { adharnumber: currentNumber })
                .done(function (res) {
                    if (!res.success) {
                        showMessage((res.data && res.data.message) || 'Failed to resend OTP.', 'is-error');
                        $resendButtons.prop('disabled', false);
                        return;
                    }

                    setTransactionId(res.data.transactionId);
                    showMessage(res.data.message || 'OTP resent.', 'is-success');
                    startResendTimer();
                })
                .fail(function (xhr) {
                    showMessage(requestErrorMessage(xhr), 'is-error');
                    $resendButtons.prop('disabled', false);
                })
                .always(function () {
                    toggleLoading(false);
                });
        });

        $('.change-mobile').on('click', function () {
            resetFlow();
            showSection($commonSection);
        });

        $state.on('change', function () {
            const stateId = $(this).val();
            if (stateId) {
                fetchDistricts(stateId);
                return;
            }

            $district.empty().append($('<option value="">Select State First</option>')).prop('disabled', true);
        });

        $('#create-abha-btn').on('click', function () {
            populateDateDropdowns();
            if (!statesLoaded) {
                statesLoaded = true;
                fetchStates();
            }

            showSection($createAbhaSection);
        });

        $addressInput.on('input', function () {
            const $input = $(this);

            $input.siblings('.inline-error').remove();
            $suggestionList.find('.suggestion-item').removeClass('selected-address');
            const value = $input.val().trim();
            if (value && !ADDRESS_RE.test(value)) {
                addFieldError($input, 'Only letters, numbers, _ and . are allowed.');
                return;
            }

            const taken = $suggestionList.find('.suggestion-item').filter(function () {
                return $(this).text().trim() === value;
            }).length > 0;

            if (taken) {
                addFieldError($input, 'This address is in the suggestions above - select it there.');
            }
        });

        $('.auth-input, .otp-input, #adhar-otp-mobile-input, #pincode').on('input paste', function () {
            const $input = $(this);
            setTimeout(function () {
                const cleaned = digitsOnly($input.val());

                if ($input.val() !== cleaned) {
                    $input.val(cleaned);
                }
            }, 0);
        });
        clearTransactionId();
        populateDateDropdowns();
    });
})(jQuery);