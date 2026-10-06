jQuery(document).ready(($) => {
    // =============================================
    // DOM ELEMENTS
    // =============================================
    const $commonSection = $('#common-section');
    const $adharOtpSection = $('#adhar-otp-section');
    const $mobileOtpSection = $('#mobile-otp-section');
    const $responseMessage = $('#response-message');
    const $loadingSpinner = $('#loading-spinner');
    const $resendButton = $('.otp-resend-btn');
    const $changeMobileBtn = $('.change-mobile');
    const $transactionIdField = $('#transaction-id');
    const $state = $("#state");
    const $district = $("#district");
    const $form = $("#create-abha-form");
    const $fname = $("#fname");
    const $mname = $("#mname");
    const $lname = $("#lname");
    const $day = $("#day");
    const $month = $("#month");
    const $year = $("#year");
    const $gender = $("input[name='gender']");
    const $pincode = $("#pincode");
    const $address = $("#address");
    const $continueButton = $form.find(".form-submit button");
    const $suggestionSection = $('.suggestion-section');
    const $suggestionList = $('.suggestion-list');

    // =============================================
    // CONSTANTS AND VARIABLES
    // =============================================
    const typeToAction = {
        'aadhaar': 'verify_aadhaar_otp',
        'mobile': 'verify_PHR_otp',
        'aadhaar-mobile': 'verify_adhar_mobile_otp'
    };

    let type = null;
    let adharnumber = null;
    let loadingInterval, resendTimer;
    let verifyResponse = null;

    // =============================================
    // HELPER FUNCTIONS
    // =============================================
    function toggleLoading(show, $element = $loadingSpinner) {
        if (show) {
            startLoadingAnimation($element);
        } else {
            stopLoadingAnimation($element);
        }
    }

    function startLoadingAnimation($element) {
        let dots = '';
        stopLoadingAnimation($element);
        loadingInterval = setInterval(() => {
            dots = dots.length < 3 ? dots + '.' : '';
            $element.text(`Loading${dots}`).show();
        }, 250);
    }

    function stopLoadingAnimation($element) {
        clearInterval(loadingInterval);
        $element.text('').hide();
    }

    function showMessage(message, type) {
        $responseMessage.text(message).removeClass('success error').addClass(type).show();
    }

    function updateTransactionID(transactionId) {
        $transactionIdField.val(transactionId);
        localStorage.setItem('transactionId', transactionId);
    }

    function clearAllErrors() {
        $responseMessage.empty().removeAttr('class')
        $form.find(".error-message").remove();
        $form.find(".error").removeClass("error");
    }

    function checkABHAadress(phrAddress, abha_address) {
        let phrAddress_sbx = phrAddress + '@sbx';
        return abha_address.some((item) => (phrAddress_sbx == item));
    }

    // =============================================
    // UI FUNCTIONS
    // =============================================
    function displayImagePreview(imageUrl) {
        $('.image-preview-modal').remove();
        const modalHtml = `
            <div class="image-preview-modal">
                <div class="modal-overlay"></div>
                <div class="modal-content">
                    <span class="close-btn">&times;</span>
                    <img src="${imageUrl}" alt="API Image" />
                    <a href="${imageUrl}" download="Image" class="download-btn btn">Download Image</a>
                </div>
            </div>
        `;
        $responseMessage.parent().append(`<div class="image-preview">
                <a href="${imageUrl}" download="Image" class="download-btn btn">Download Image</a>
            </div>`
        );
        $('body').append(modalHtml);
        $('.image-preview-modal').fadeIn();
        $('.close-btn, .modal-overlay').click(function () {
            $('.image-preview-modal').fadeOut(function () {
                $(this).hide();
            });
        });
    }

    function populateDateDropdowns() {
        const $dayDropdown = $("#day");
        const $monthDropdown = $("#month");
        const $yearDropdown = $("#year");

        // Populate days
        for (let i = 1; i <= 31; i++) {
            const dayValue = i.toString().padStart(2, '0');
            $dayDropdown.append(`<option value = "${dayValue}"> ${dayValue}</option> `);
        }

        // Populate months
        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        monthNames.forEach((month, index) => {
            const monthValue = (index + 1).toString().padStart(2, '0');
            $monthDropdown.append(`<option value = "${monthValue}" > ${month}</option> `);
        });

        // Populate years
        const currentYear = new Date().getFullYear();
        for (let i = currentYear; i >= 1900; i--) {
            $yearDropdown.append(`<option value = "${i}" > ${i}</option>`);
        }
    }

    function toggleButtonClick(event) {
        const $button = $(event.target);
        $('.link-action-btn').removeClass('active');
        $button.addClass('active');
        const view = $button.data('view');
        $('.auth-form').hide();
        $(`.auth-form[data-type="${view}"]`).show();
    }

    // =============================================
    // TIMER FUNCTIONS
    // =============================================
    function startResendTimer() {
        let countdown = 10;
        $resendButton.css('opacity', 0.75).prop('disabled', true).text(`Resend OTP in ${countdown}s`);
        resendTimer = setInterval(() => {
            $resendButton.text(`Resend OTP in ${--countdown}s`);
            if (countdown <= 0) {
                clearInterval(resendTimer);
                $resendButton.css('opacity', 1).prop('disabled', false).text('Resend OTP');
            }
        }, 1000);
    }

    // =============================================
    // VALIDATION FUNCTIONS
    // =============================================
    function validateForm() {
        clearAllErrors();
        let isValid = true;

        const fields = [
            [$fname, $fname.val().trim() !== "", "First name is required."],
            [$mname, $mname.val().trim() !== "", "Middle name is required."],
            [$lname, $lname.val().trim() !== "", "Last name is required."],
            [$day, parseInt($day.val(), 10) >= 1 && parseInt($day.val(), 10) <= new Date(parseInt($year.val(), 10), parseInt($month.val(), 10), 0).getDate(), `Day must be between 1 and ${new Date(parseInt($year.val(), 10), parseInt($month.val(), 10), 0).getDate()}.`],
            [$month, parseInt($month.val(), 10) >= 1 && parseInt($month.val(), 10) <= 12, "Month must be between 1 and 12."],
            [$year, parseInt($year.val(), 10) >= 1900 && parseInt($year.val(), 10) <= new Date().getFullYear(), `Year must be between 1900 and ${new Date().getFullYear()}.`],
            [$gender, $gender.is(":checked"), "Gender is required."],
            [$state, $state.val().trim() !== "", "State is required."],
            [$district, $district.val().trim() !== "", "District is required."],
            [$pincode, $pincode.val().trim() !== "", "Pincode is required."]
        ];

        fields.forEach(([element, condition, message]) => {
            if (!condition) {
                element.addClass('error');
                $responseMessage.append(`<p> <em>${message}</em></p> `).removeClass('success').addClass('error').show();
                isValid = false;
            }
        });

        return isValid;
    }

    // =============================================
    // API CALL FUNCTIONS
    // =============================================
    async function aadhaar_mobile_submit(payload, $form) {
        toggleLoading(true);
        $.ajax({
            url: ajax_obj,
            type: 'POST',
            data: { action: 'handle_aadhaar_mobile_submit', ...payload },
            dataType: 'json',
            success: ({ success, data }) => {
                showMessage(data.message, success ? 'success' : 'error');
                if (success) {
                    $form.hide();
                    $('#adhar-mobile-otp-section').show();
                }
            },
            error: (xhr) => showMessage(`Error: ${xhr.statusText || 'An unexpected error occurred.'}`, 'error'),
            complete: () => toggleLoading(false)
        });
    }

    async function handleLinkAbha(payload) {
        const mypayload = { action: "handle_link_abha", data: true, payload, aadhaar_number: adharnumber };
        toggleLoading(true);
        $.ajax({
            url: ajax_obj,
            type: 'POST',
            data: mypayload,
            dataType: 'json',
            success: ({ data }) => {
                data?.image_url ? displayImagePreview(data.image_url) : showMessage('Message False', 'error');
            },
            error: (xhr) => {
                showMessage(`Error: ${xhr.statusText || 'An unexpected error occurred.'}`, 'error');
            },
            complete: () => toggleLoading(false)
        });
    }

    async function fetchSuggestion(action, transaction_id) {
        $.ajax({
            type: "GET",
            url: ajax_obj,
            data: { action: action, transaction_id },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $('.suggestion-list-wrap').removeClass('no-data');
                    $('#abha-address-type').val(action);
                    $('#abha-address-input').attr('disabled', false);
                    $suggestionList.empty();

                    response.data.suggestions.forEach(name => {
                        const $suggestionItem = $(`<div class="suggestion-item" data-address="${name}"> ${name} </div>`);
                        $suggestionList.append($suggestionItem);
                        $suggestionItem.on('click', function () {
                            $('.suggestion-item').removeClass('selected-address');
                            $('#abha-address-input').val(name);
                            $(this).addClass('selected-address');
                        });
                    });

                    showMessage(response.data.message, 'success');
                } else {
                    showMessage(`Error fetching suggestions: ${response.data.message}`, 'error');
                }
            },
            error: function (xhr) {
                showMessage(`Unexpected error while fetching suggestions: ${xhr.responseText || 'An error occurred.'}`, 'error');
            }
        });
    }

    async function fetchStates() {
        $.ajax({
            type: "GET",
            url: ajax_obj,
            data: { action: "get_states" },
            dataType: "json",
            success: function (response) {
                if (response.status == 200) {
                    $state.empty().append('<option value="">Select a State</option>');
                    response.data.forEach(({ code, name }) => {
                        $state.append(`<option value="${code}">${name}</option>`);
                    });
                }
            },
            error: function (xhr) {
                console.log("Unexpected error while fetching states:", xhr.responseText || "An error occurred.");
            }
        });
    }

    async function fetchDistricts(stateID) {
        $.ajax({
            type: "GET",
            url: ajax_obj,
            data: { action: "get_districts", state_ID: stateID },
            dataType: "json",
            success: function (response) {
                $district.empty().append('<option value="">Select a District</option>').prop("disabled", true);

                if (response.success) {
                    response.data.data.forEach(({ code, name }) => {
                        $district.append(`<option value="${code}">${name}</option>`);
                    });
                    $district.prop("disabled", false);
                } else {
                    console.error(`Error fetching districts: ${response.data.message}`);
                }
            },
            error: function (xhr) {
                console.error("Unexpected error while fetching districts:", xhr.responseText || "An error occurred.");
            }
        });
    }

    async function handlePHRLogin(transactionId, phrAddress, already_registered = true) {
        if (!phrAddress) {
            return showMessage('PHR Address is required.', 'error');
        }

        const loginPayload = {
            transaction_id: transactionId,
            phr_address: phrAddress,
            is_health_number: false,
            already_registered,
        };

        $responseMessage.empty().removeAttr('class');
        $('.image-preview').remove();
        toggleLoading(true);
        $.ajax({
            type: "POST",
            url: ajax_obj,
            data: { action: 'login_phr_address', ...loginPayload },
            dataType: "json",
            success: function (loginResponse) {
                if (loginResponse.success) {
                    const imageUrl = loginResponse.data?.image_url;
                    if (imageUrl) {
                        displayImagePreview(imageUrl);
                        showMessage('Login successful.', 'success');
                    } else {
                        showMessage('Login successful, but no image was returned.', 'success');
                    }
                    $suggestionSection.hide();
                } else {
                    showMessage(loginResponse.data?.message || 'Login failed.', 'error');
                }
            },
            error: function (xhr) {
                showMessage(`Error: ${xhr.responseText || 'An unexpected error occurred.'}`, 'error');
            },
            complete: function () {
                toggleLoading(false);
            }
        });
    }

    // =============================================
    // RESPONSE HANDLERS
    // =============================================
    function aadharOTPresponse(response) {
        const { imageData, message, image_url } = response.data;
        if (imageData) {
            showMessage(message, 'success');
            displayImagePreview(image_url);
            $adharOtpSection.hide()
        }
    }

    function aadharmobilOTPresponse(response, $form) {
        verifyResponse = response;

        const { message, data: { transaction_id, otp_required } } = verifyResponse.data;
        updateTransactionID(transaction_id);
        if (otp_required) {
            const number = $('#adhar-otp-mobile-input').val().trim();
            const payload = { transaction_id, number };
            aadhaar_mobile_submit(payload, $form);
            fetchSuggestion("get_aadhaar_suggestion", transaction_id);
            $suggestionSection.show();
            return showMessage(message, 'success');
        } else {
            fetchSuggestion("get_aadhaar_suggestion", transaction_id);
            $suggestionSection.show();
            return showMessage(message, 'success');
        }
    }

    function PHROTPresponse(response) {
        const { transactionId, message, mappedPhrAddress } = response.data;
        updateTransactionID(transactionId);

        const $abhaList = $(".abha-list").empty();
        const $foundedAddress = $(".founded-address");
        const $foundedAddresstext = `We have found ABHA address linked with your mobile number.`;

        const $dropdown = $("<select class='input-field phr-dropdown'>").append('<option value="" disabled selected>Select PHR Address</option>');
        mappedPhrAddress.forEach(address => $dropdown.append(`<option value = "${address}"> ${address}</option> `));

        $foundedAddress.text($foundedAddresstext);
        $abhaList.append($dropdown, '<div class="form-submit"><button id="select-phr-btn" class="submit-btn">Continue</button></div>');
        $("#select-phr-btn").show().off('click').on('click', () => {
            const selectedAddress = $dropdown.val();
            $(this).hide();
            handlePHRLogin(transactionId, selectedAddress, true);
        });
        $('.choose-abha-section').show();
        showMessage(message, 'success');
    }

    // =============================================
    // FORM HANDLERS
    // =============================================
    async function handleAuthFormSubmit(e) {
        e.preventDefault();
        $responseMessage.empty().removeAttr('class');
        const $form = $(e.target);
        type = $form.data('type');
        toggleLoading(true);
        adharnumber = $form.find('.auth-input').val().trim();
        const action = type === 'aadhaar' ? 'aadhaar_auth_form_submit' : 'PHR_mobile_auth_form_submit';
        toggleLoading(true);
        $.ajax({
            url: ajax_obj,
            type: 'POST',
            data: { action, adharnumber },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const { transactionId, message } = response.data;
                    updateTransactionID(transactionId);
                    $commonSection.hide();
                    type === 'aadhaar' ? $adharOtpSection.show() : $mobileOtpSection.show();
                    showMessage(message, 'success');
                    startResendTimer();
                } else {
                    showMessage(response.data.message, 'error');
                }
            },
            error: function (xhr, status, error) {
                showMessage(`Error: ${xhr.statusText || 'An unexpected error occurred.'}`, 'error');
            },
            complete: function () {
                toggleLoading(false);
            }
        });
    }

    async function handleOtpFormSubmit(e) {
        e.preventDefault();
        const $form = $(e.target);
        const otp = $form.find('.otp-input').val().trim();
        const transactionId = localStorage.getItem('transactionId');
        const type = $form.data('type');

        const action = typeToAction[type];
        const payload = { action, adharNo: adharnumber, otp, transactionId };
        payload.number = (type === 'aadhaar' || type === 'aadhaar-mobile') ? $('#adhar-otp-mobile-input').val().trim() : '';

        if (otp.length !== 6 || (type !== 'mobile' && type !== 'aadhaar' && type !== 'aadhaar-mobile' && !/^\d{10}$/.test(payload.number))) {
            return showMessage('Invalid input.', 'error');
        }
        toggleLoading(true);

        $.ajax({
            url: ajax_obj,
            type: 'POST',
            data: payload,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    if (response.data.imageData) {
                        aadharOTPresponse(response);
                    } else if (response.data.userData) {
                        aadharmobilOTPresponse(response, $form);
                        return showMessage(response.data.message, 'success');
                    } else if (type === 'mobile') {
                        PHROTPresponse(response);
                        return showMessage(response.data.message, 'success');
                    }

                    if (response.data.verify_aadhaar_mobile_otp) {
                        fetchSuggestion("get_aadhaar_suggestion", response.data.transactionId);
                        $suggestionSection.show();
                    }
                } else {
                    return showMessage(response.data.message, 'error');
                }
            },
            error: function (xhr, status, error) {
                showMessage(`OTP Error: ${xhr.statusText || 'An unexpected error occurred.'}`, 'error');
            },
            complete: function () {
                toggleLoading(false);
            }
        });
    }

    async function handleSubmitSuggestion(response) {
        const { data: { transaction_id, first_name, last_name, middle_name, abha_address, abha_number, tokens: { token, refreshToken }, gender } } = response.data;
        const phrAddress = $('#abha-address-input').val().trim();
        const type = $('#abha-address-type').val().trim();

        if (!phrAddress) {
            return showMessage('Please Enter a PHR Address before submitting.', 'error');
        }
        if (checkABHAadress(phrAddress, abha_address)) {
            showMessage(`${phrAddress} is Already registerd.`, 'error');
        }
        if (type === 'get_aadhaar_suggestion') {
            const number = $('#adhar-otp-mobile-input').val().trim();
            const user_details = { first_name, middle_name, last_name, gender, abha_number, mobile_no: number, "tokens": { token, refresh_token: refreshToken } };
            const processDatapayload = { abha_address: phrAddress, transaction_id, ...user_details };
            handleLinkAbha(processDatapayload);
        }
    }

    // =============================================
    // EVENT LISTENERS
    // =============================================
    $resendButton.on('click', async (e) => {
        e.preventDefault();
        $responseMessage.empty().removeAttr('class');
        const actionType = type === 'aadhaar' ? 'aadhaar_auth_form_submit' : 'PHR_mobile_auth_form_submit';
        const transactionId = $transactionIdField.val().trim();
        if (!transactionId) return showMessage('Cannot resend OTP. Transaction ID is missing.', 'error');
        const payload = { action: actionType, adharnumber }

        toggleLoading(true);
        $.ajax({
            type: "POST",
            url: ajax_obj,
            data: payload,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    updateTransactionID(response.data.transactionId);
                    showMessage(response.data.message, 'success');
                    startResendTimer();
                } else {
                    showMessage(response.data.message || 'Failed to resend OTP.', 'error');
                }
            },
            error: function (xhr) {
                showMessage(`Error: ${xhr.responseText || 'An error occurred.'}`, 'error');
            },
            complete: function () {
                toggleLoading(false);
            }
        });
    });

    $changeMobileBtn.on('click', () => {
        $responseMessage.empty().removeAttr('class');
        $adharOtpSection.hide();
        $mobileOtpSection.hide();
        $commonSection.show();
    });

    $state.on("change", function () {
        const stateID = $(this).val();
        if (stateID) {
            fetchDistricts(stateID);
        } else {
            $district.empty().append('<option value="">Select a State First</option>').prop("disabled", true);
        }
    });

    $("#create-abha-btn").on("click", function () {
        populateDateDropdowns();
        $responseMessage.empty().removeAttr('class')
        $(".choose-abha-section").hide();
        $('.create-abha-section').show();
    });

    $('.link-action-btn').on('click', toggleButtonClick);
    $('.auth-form').on('submit', handleAuthFormSubmit);
    $('.otp-form').on('submit', handleOtpFormSubmit);

    $('#abha-address-input').on('input', function () {
        $('.suggestion-item').removeClass('selected-address');
        const inputValue = $(this).val().trim();
        const validRegex = /^[a-zA-Z0-9_.]+$/;
        const existingValues = $('.suggestion-item').map(function () {
            return $(this).text().trim();
        }).get();
        $('.error-message').remove();
        $('.suggestion-item').removeClass('selected-address');
        if (inputValue && !validRegex.test(inputValue)) {
            $(this).after('<div class="error-message" style="color: red;">Invalid input! Only alphabets, numbers, _, and . are allowed.</div>');
        }
        if (existingValues.includes(inputValue)) {
            $(this).after('<div class="error-message" style="color: red;">Duplicate entry! This address already exists.</div>');
        }
    });

    $('.abha-address-form').on('submit', (el) => {
        el.preventDefault();
        handleSubmitSuggestion(verifyResponse)
    });
// fetchStates();
    $continueButton.on("click", async function (e) {
        e.preventDefault();

        if (validateForm()) {
            toggleLoading(true);
            const payload = {
                action: "PHR_demographics_submit",
                transaction_id: $transactionIdField.val().trim(),
                first_name: $fname.val().trim(),
                middle_name: $mname.val().trim(),
                last_name: $lname.val().trim(),
                pin_code: $pincode.val().trim(),
                gender: $gender.filter(":checked").val().trim(),
                dob: `${$year.val().trim()}-${$month.val().trim()}-${$day.val().trim()}`,
                mobile_no: adharnumber,
                state_code: $state.val().trim(),
                district_code: $district.val().trim(),
                address: $address.val().trim()
            };

            toggleLoading(true);
            $.ajax({
                type: "POST",
                url: ajax_obj,
                data: payload,
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        $('.create-abha-section').hide();
                        fetchSuggestion("get_PHR_suggestion", $transactionIdField.val().trim());
                        $suggestionSection.show();
                        showMessage(response.data?.message || "Demographics submitted successfully.", 'success');
                    } else {
                        showMessage(response.data?.data || "Failed to submit demographics.", 'error');
                    }
                },
                error: function (xhr) {
                    showMessage(`Error: ${xhr.responseText || 'An error occurred.'}`, 'error');
                },
                complete: function () {
                    toggleLoading(false);
                }
            });
        }
    });

    $('#submitSuggession').on('click', async function () {
        const $transactionId = localStorage.getItem('transactionId');
        const $phrAddress = $('.suggestion-list').find('.selected-address').data('address');
        if (!$phrAddress) {
            showMessage('Please select a PHR Address before submitting.', 'error');
        }
        await handlePHRLogin($transactionId, $phrAddress, false);
    });

    // =============================================
    // INITIALIZATION
    // =============================================
    populateDateDropdowns();
});