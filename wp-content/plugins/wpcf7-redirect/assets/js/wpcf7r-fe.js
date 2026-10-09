// @ts-check

import '../css/wpcf7-redirect-frontend.scss';

var wpcf7_redirect;

(function ($) {
    /**
     * Main Wpcf7_redirect class that handles Contact Form 7 submission responses
     * @constructor
     */
    function Wpcf7_redirect() {
        /**
         * Initializes the handler functions
         */
        this.init = function () {
            this.wpcf7_redirect_mailsent_handler();
        };

        /**
         * Sets up event listeners for CF7 form submission events
         * @fires wpcf7r-mailsent - Triggered after CF7 mail is sent
         * @fires wpcf7r-invalid - Triggered when CF7 form validation fails
         */
        this.wpcf7_redirect_mailsent_handler = function () {

            document.addEventListener('wpcf7mailsent', function (event) {

                $(document.body).trigger('wpcf7r-mailsent', [event]);

                if (typeof event.detail.apiResponse != 'undefined' && event.detail.apiResponse) {
                    const apiResponse = event.detail.apiResponse;
                    let actionDelay = 0;

                    //handle api response
                    if (typeof apiResponse.api_url_request != 'undefined' && apiResponse.api_url_request) {
                        wpcf7_redirect.handle_api_action(apiResponse.api_url_request);
                    }

                    //handle api response
                    if (typeof apiResponse.api_json_xml_request != 'undefined' && apiResponse.api_json_xml_request) {
                        wpcf7_redirect.handle_api_action(apiResponse.api_json_xml_request);
                    }

                    //handle fire javascript action
                    if (typeof apiResponse.FireScript != 'undefined' && apiResponse.FireScript) {
                        actionDelay = typeof apiResponse.FireScript.delay_redirect != 'undefined' ? apiResponse.FireScript.delay_redirect : actionDelay;
                        window.setTimeout(function () {
                            wpcf7_redirect.handle_javascript_action(apiResponse.FireScript);
                        }, actionDelay);
                    }

                    //catch and handle popup action
                    if (typeof apiResponse.popup != 'undefined' && apiResponse.popup) {
                        wpcf7_redirect.handle_popups(apiResponse.popup, event);
                    }

                    //catch redirect to paypal
                    if (typeof apiResponse.redirect_to_paypal != 'undefined' && apiResponse.redirect_to_paypal) {
                        actionDelay = typeof apiResponse.redirect_to_paypal.delay_redirect != 'undefined' ? apiResponse.redirect_to_paypal.delay_redirect : actionDelay;
                        window.setTimeout(function () {
                            wpcf7_redirect.handle_redirect_action(apiResponse.redirect_to_paypal);
                        }, actionDelay);
                    }

                    //catch redirect action
                    if (typeof apiResponse.redirect != 'undefined' && apiResponse.redirect) {
                        actionDelay = typeof apiResponse.redirect.delay_redirect != 'undefined' ? apiResponse.redirect.delay_redirect : actionDelay;
                        window.setTimeout(function () {
                            wpcf7_redirect.handle_redirect_action(apiResponse.redirect);
                        }, actionDelay);
                    }
                }
            }, false);

            document.addEventListener('wpcf7invalid', function (event) {

                $(document.body).trigger('wpcf7r-invalid', [event]);

                if (typeof event.detail.apiResponse != 'undefined' && event.detail.apiResponse) {
                    const response = event.detail.apiResponse;
                    if (response.invalidFields) {
                        //support for multistep by ninja
                        wpcf7_redirect.ninja_multistep_mov_to_invalid_tab(event, response);
                    }
                }
            });
        };
       
        /**
         * Handles popup creation and management after form submission
         * @param {Object[]} popups - Array of popup configuration objects
         * @param {string} popups[].popup-template - HTML template for the popup
         * @param {string} popups[].template-name - Class name to add to the body when popup is displayed
         * @param {Event} event - Form submission event object
         * @fires wpcf7r-before-open-popup - Triggered before opening the popup
         * @fires wpcf7r-popup-appended - Triggered after the popup has been appended to the DOM
         * @fires wpcf7r-popup-removed - Triggered after the popup has been removed from the DOM
         */
        this.handle_popups = function (popups, event) {

            $(document.body).trigger('wpcf7r-before-open-popup', [event]);

            $.each(popups, function (k, popup) {

                var $new_elem = $(popup['popup-template']);

                $(document.body).append($new_elem);
                $(document.body).addClass(popup['template-name']);

                window.setTimeout(function () {
                    $(document.body).addClass('modal-popup-open');
                    $new_elem.addClass('is-open');
                }, 1000);

                $new_elem.find('.close-button').on('click', function () {

                    $new_elem.removeClass('is-open').addClass('fade');

                    $(document.body).removeClass('modal-popup-open');

                    window.setTimeout(function () {
                        $('.wpcf7r-modal').remove();
                        $(document.body).trigger('wpcf7r-popup-removed', [$new_elem]);
                    }, 4000);
                });

                $(document.body).trigger('wpcf7r-popup-appended', [$new_elem]);
            });

        }

        /**
         * Handles API action results by executing JavaScript returned from the API
         * @param {Object[]} send_to_api_result - Array of API result objects
         * @param {string} [send_to_api_result[].result_javascript] - JavaScript code to be executed
         */
        this.handle_api_action = function (send_to_api_result) {

            $.each(send_to_api_result, function (k, v) {
                try {
                    if ( !v.result_javascript || typeof v.result_javascript !== 'string' ) {
                        return;
                    }

                    if (
                        v.api_response &&
                        typeof v.api_response === 'string'
                        && v.api_response.trim() !== ''
                    ) {
                        window.rcf7_response = JSON.parse(v.api_response);
                    }

                    eval(v.result_javascript);
                } catch (e) {
                    console.error("Error handling API action:", e);
                }
            });
        };

        /**
         * Handles navigation to the form tab with invalid fields in Ninja multi-step forms
         * @param {Event} event - Form submission event
         * @param {Object} response - API response containing invalidFields information
         * @param {Array} response.invalidFields - Array of invalid field objects
         */
        this.ninja_multistep_mov_to_invalid_tab = function (event, response) {

            if ($('.fieldset-cf7mls-wrapper').length) {
                const form = $(event.target);
                const first_invalid_field = response.invalidFields[0];
                const parent_step = $(first_invalid_field.into).parents('fieldset');
                const current_fs = form.find('.cf7mls_current_fs');
                const previous_fs = parent_step;

                form.find('.fieldset-cf7mls').removeClass('cf7mls_current_fs');
                parent_step.addClass('cf7mls_current_fs').removeClass('cf7mls_back_fs');
                if (form.find('.cf7mls_progress_bar').length) {
                    form.find('.cf7mls_progress_bar li').eq(form.find("fieldset.fieldset-cf7mls").index(previous_fs)).addClass("current");
                    form.find('.cf7mls_progress_bar li').eq(form.find("fieldset.fieldset-cf7mls").index(current_fs)).removeClass("active current");
                }
            }
        }

        /**
         * Handles redirect actions (page redirect, new tab, or form submission)
         * @param {Object[]} redirect - Array of redirect configuration objects
         * @param {number} [redirect[].delay] - Delay in seconds before the redirect occurs
         * @param {string} [redirect[].redirect_url] - URL to redirect to
         * @param {string} [redirect[].type] - Type of redirect ('redirect' or 'new_tab')
         * @param {string} [redirect[].form] - HTML form to submit
         * @fires wpcf7r-handle_redirect_action - Triggered before executing redirect action
         */
        this.handle_redirect_action = function (redirect) {

            $(document.body).trigger('wpcf7r-handle_redirect_action', [redirect]);

            $.each(redirect, function (k, v) {
                const delay = (v.delay || 0) * 1000;

                window.setTimeout(function (v) {
                    const redirect_url = v.redirect_url || '';
                    const type = v.type || '';

                    if (typeof v.form != 'undefined' && v.form) {
                        $('body').append(v.form);
                        $('#cf7r-result-form').submit();
                    } else {

                        if (redirect_url && type == 'redirect') {
                            window.location = redirect_url;
                        } else if (redirect_url && type == 'new_tab') {
                            window.open(redirect_url);
                        }
                    }
                }, delay, v);

            });

        };

        /**
         * Executes JavaScript code passed from the server
         * @param {Object} scripts - Object containing JavaScript code to execute
         * @fires wpcf7r-handle_javascript_action - Triggered before executing JavaScript actions
         */
        this.handle_javascript_action = function (scripts) {

            $(document.body).trigger('wpcf7r-handle_javascript_action', [scripts]);

            $.each(scripts, function (k, script) {
                eval(script); //not using user input
            });

        };

        /**
         * Decodes HTML special characters to their corresponding characters
         * @param {string} string - The string containing HTML entities to decode
         * @returns {string} The decoded string
         */
        this.htmlspecialchars_decode = function (string) {

            var map = {
                '&amp;': '&',
                '&#038;': "&",
                '&lt;': '<',
                '&gt;': '>',
                '&quot;': '"',
                '&#039;': "'",
                '&#8217;': "’",
                '&#8216;': "‘",
                '&#8211;': "–",
                '&#8212;': "—",
                '&#8230;': "…",
                '&#8221;': '”'
            };

            return string.replace(/\&[\w\d\#]{2,5}\;/g, function (m) { return map[m]; });
        };

        this.init();
    }
    
    wpcf7_redirect = new Wpcf7_redirect();
})(jQuery);