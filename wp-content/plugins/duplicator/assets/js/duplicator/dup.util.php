<?php
/*! ============================================================================
*  UTIL NAMESPACE: All methods at the top of the Duplicator Namespace
*  =========================================================================== */
defined("ABSPATH") or die("");
?>

<script>
    DupliJs.Util.ajaxProgress = null;

    DupliJs.Util.ajaxProgressShow = function() {
        if (DupliJs.Util.ajaxProgress === null) {
            DupliJs.Util.ajaxProgress = jQuery('#dup-ajax-loader')
        }
        DupliJs.Util.ajaxProgress
            .stop(true, true)
            .css('display', 'block')
            .delay(1000)
            .animate({
                opacity: 1
            }, 500);
    }

    DupliJs.Util.ajaxProgressHide = function() {
        if (DupliJs.Util.ajaxProgress === null) {
            return;
        }
        DupliJs.Util.ajaxProgress
            .stop(true, true)
            .delay(500)
            .animate({
                opacity: 0
            }, 300, function() {
                jQuery(this).css({
                    'display': 'none'
                });
            });
    }

    DupliJs.Util.ajaxWrapper = function(ajaxData, callbackSuccess, callbackFail, options = {}) {
        let opts = jQuery.extend({
            showProgress: true, // Is true show the ajax loader, can be disabled for custom progress handling
            timeout: 30000,
            silentSuccess: false, // If true, suppress the default success notice when no callbackSuccess message is returned
            silentError: false    // If true, suppress the default error notice (both result.success === false and HTTP errors)
        }, options);

        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            timeout: opts.timeout,
            dataType: "json",
            data: ajaxData,
            beforeSend: function(xhr) {
                if (opts.showProgress) {
                    DupliJs.Util.ajaxProgressShow();
                }
            },
            success: function(result, textStatus, jqXHR) {
                var message = '';
                if (result.success) {
                    if (typeof callbackSuccess === "function") {
                        try {
                            message = callbackSuccess(result, result.data, (result.data || {}).funcData, textStatus, jqXHR);
                        } catch (error) {
                            console.error(error);
                            if (!opts.silentError) {
                                DupliJs.addAdminMessage(error.message, 'error');
                            }
                            message = '';
                        }
                    } else if (!opts.silentSuccess) {
                        message = '<?php echo esc_js(__('RESPONSE SUCCESS', 'duplicator')); ?>';
                    }
                    if (message != null && String(message).length) {
                        DupliJs.addAdminMessage(message, 'notice');
                    }
                } else {
                    if (typeof callbackFail === "function") {
                        try {
                            message = callbackFail(result, result.data, (result.data || {}).funcData, textStatus, jqXHR);
                        } catch (error) {
                            console.error(error);
                            message = error.message;
                        }
                    } else if (!opts.silentError) {
                        message = '<?php echo esc_js(__('RESPONSE ERROR!', 'duplicator')); ?>';
                        if (result.data && result.data.message) {
                            message += '<br><br>' + result.data.message;
                        }
                    }
                    if (message != null && String(message).length) {
                        DupliJs.addAdminMessage(message, 'error');
                    }
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                // Make sure the progress is hidden even if the request fails
                DupliJs.Util.ajaxProgressHide();
                if (opts.silentError || textStatus === "abort") {
                    return;
                }

                if (xhr.responseText) {
                    console.error('Duplicator AJAX error response:', String(xhr.responseText).substring(0, 2000));
                }

                DupliJs.addAdminMessage(DupliJs.Util._ajaxErrorMessage(xhr, textStatus, opts.timeout), 'error');
            },
            complete: function() {
                if (opts.showProgress) {
                    DupliJs.Util.ajaxProgressHide();
                }
            }
        });
    };

    /**
     * Get human size from bytes number.
     * Is size is -1 return unknown
     *
     * @param {size} int bytes size
     */
    DupliJs.Util.humanFileSize = function(size) {
        if (size < 0) {
            return "unknown";
        } else if (size == 0) {
            return "0";
        } else {
            var i = Math.floor(Math.log(size) / Math.log(1024));
            return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
        }
    };

    DupliJs.Util.isEmpty = function(val) {
        return (val === undefined || val == null || val.length <= 0) ? true : false;
    };

    DupliJs.Util.toggleShow = function(selector, show = 'auto') {
        var element = jQuery(selector);
        if (show === 'auto') {
            show = !element.is(":visible");
        }

        if (show) {
            element.hide().removeClass('no-display');
            element.fadeIn();
        } else {
            element.fadeOut();
        }
    };


    DupliJs.Util.dynamicFormSubmit = function(url, method, params) {
        var form = jQuery('<form>', {
            method: method,
            action: url
        });

        jQuery.each(params, function(key, value) {
            form.append(jQuery('<input>', {
                'type': 'hidden',
                'name': key,
                'value': value
            }));
        });

        jQuery("body").append(form);
        form.submit();
    };

    /**
     * Build a user friendly message for a failed AJAX request.
     *
     * @param {Object} xhr        jQuery XHR object
     * @param {string} textStatus jQuery error status: timeout, error or parsererror
     * @param {number} timeoutMs  request timeout in milliseconds
     * @return {string}
     */
    DupliJs.Util._ajaxErrorMessage = function(xhr, textStatus, timeoutMs) {
        if (textStatus === "timeout") {
            return <?php echo wp_json_encode(
                /* translators: %s: number of seconds */
                __('The request timed out after waiting for a response for %s second(s)', 'duplicator')
            ); ?>.replace('%s', timeoutMs / 1000);
        }

        if (textStatus === "parsererror") {
            let body = String(xhr.responseText || '').trim();
            if (body === '0' || body === '-1') {
                return <?php echo wp_json_encode(
                    __('Your session may have expired. Reload the page, log in again if needed and retry.', 'duplicator')
                ); ?>;
            }
            return <?php echo wp_json_encode(
                __('The server response is not valid JSON. This usually means a PHP error occurred, check the PHP error log for details.', 'duplicator')
            ); ?>;
        }

        if (xhr.status === 0) {
            return <?php echo wp_json_encode(
                __('Could not connect to the server. Check your internet connection and try again.', 'duplicator')
            ); ?>;
        }

        if (xhr.status === 401 || xhr.status === 403) {
            return <?php echo wp_json_encode(
                /* translators: %s: HTTP status code */
                __('The request was denied (HTTP %s). Your session may have expired or a security rule blocked it, try reloading the page.', 'duplicator')
            ); ?>.replace('%s', xhr.status);
        }

        if (xhr.status === 413) {
            return <?php echo wp_json_encode(
                __('The server rejected the request because it is too large (HTTP 413). Increase the server upload size limits and try again.', 'duplicator')
            ); ?>;
        }

        if (xhr.status === 500) {
            return <?php echo wp_json_encode(
                __('The server encountered an internal error (HTTP 500), usually a PHP fatal error. Check the server error log for details.', 'duplicator')
            ); ?>;
        }

        if (xhr.status >= 502) {
            return <?php echo wp_json_encode(
                /* translators: %s: HTTP status code */
                __('The server or a proxy dropped the request (HTTP %s). The server may be overloaded or the request took too long.', 'duplicator')
            ); ?>.replace('%s', xhr.status);
        }

        return <?php echo wp_json_encode(
            /* translators: %s: HTTP status code and text */
            __('AJAX ERROR!<br>Request failed with HTTP status %s', 'duplicator')
        ); ?>.replace('%s', xhr.status + ' ' + (xhr.statusText || ''));
    };

</script>
