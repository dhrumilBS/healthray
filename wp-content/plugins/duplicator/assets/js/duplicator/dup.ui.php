<?php

use Duplicator\Libs\WpUtils\WpDbUtils;

?>
<script>
    /*! ============================================================================
     *  UI NAMESPACE: All methods at the top of the Duplicator Namespace
     *  =========================================================================== */
    (function($) {
        /**
         * Indicates if we have any form changes.
         * Primarily used to prevent the user from navigating away from a page with unsaved changes.
         * @type {boolean}
         */
        DupliJs.UI.hasUnsavedChanges = false;

        /*  Stores the state of a view into the database  */
        DupliJs.UI.SaveViewStateByPost = function(key, value) {
            if (key != undefined && value != undefined) {
                DupliJs.Util.ajaxWrapper(
                    {
                        action: 'duplicator_view_state_update',
                        key: key,
                        value: value,
                        nonce: '<?php echo esc_js(wp_create_nonce('duplicator_view_state_update')); ?>'
                    },
                    function() { return ''; }
                );
            }
        }

        DupliJs.UI.SaveMulViewStatesByPost = function(states) {
            DupliJs.Util.ajaxWrapper(
                {
                    action: 'duplicator_view_state_update',
                    states: states,
                    nonce: '<?php echo esc_js(wp_create_nonce('duplicator_view_state_update')); ?>'
                },
                function() { return ''; }
            );
        }

        DupliJs.UI.SetScanMode = function() {
            var scanMode = jQuery('#scan-mode').val();

            if (scanMode == <?php echo (int) WpDbUtils::PHPDUMP_MODE_MULTI; ?>) {
                jQuery('#scan-multithread-size').show();
                jQuery('#scan-chunk-size-label').show();
            } else {
                jQuery('#scan-multithread-size').hide();
                jQuery('#scan-chunk-size-label').hide();
            }

        }

        DupliJs.UI.IsSaveViewState = true;
        /*  Toggle MetaBoxes */
        DupliJs.UI.ToggleMetaBox = function() {
            var $title = jQuery(this);
            var $panel = $title.parent().find('.dup-box-panel');
            var $arrowParent = $title.parent().find('.dup-box-arrow');
            var $arrow = $title.parent().find('.dup-box-arrow i');
            var key = $panel.attr('id');
            var value = $panel.is(":visible") ? 0 : 1;

            if (DupliJs.UI.IsSaveViewState) {
                DupliJs.UI.SaveViewStateByPost(key, value);
            }

            if (value) {
                $panel.removeClass('no-display');
                $panel.show();
                $arrowParent.attr("aria-expanded", true);
                $arrow.removeClass().addClass('fa fa-caret-up');
            } else {
                $panel.hide();
                $arrowParent.attr("aria-expanded", false);
                $arrow.removeClass().addClass('fa fa-caret-down');
            }

            return false;
        }

        DupliJs.UI.ClearTraceLog = function(reload) {
            var reload = reload || 0;
            DupliJs.Util.ajaxWrapper(
                {
                    action: 'duplicator_delete_trace_log',
                    nonce: '<?php echo esc_js(wp_create_nonce('duplicator_delete_trace_log')); ?>'
                },
                function() {
                    if (reload) {
                        window.location.reload();
                    }
                }
            );
            return false;
        }

        /* Clock generator, used to show an active clock.
         * Intended use is to be called once per page load
         * such as:
         *      <div id="dupli-clock-container"></div>
         *      DupliJs.UI.Clock(DupliJs._WordPressInitTime); */
        DupliJs.UI.Clock = function() {
            var timeDiff;
            var timeout;

            function addZ(n) {
                return (n < 10 ? '0' : '') + n;
            }

            function formatTime(d) {
                return addZ(d.getHours()) + ':' + addZ(d.getMinutes()) + ':' + addZ(d.getSeconds());
            }

            return function(s) {

                var now = new Date();
                var then;
                // Set lag to just after next full second
                var lag = 1015 - now.getMilliseconds();

                // Get the time difference when first run
                if (s) {
                    s = s.split(':');
                    then = new Date(now);
                    then.setHours(+s[0], +s[1], +s[2], 0);
                    timeDiff = now - then;
                }

                now = new Date(now - timeDiff);
                jQuery('#dupli-clock-container').html(formatTime(now));
                timeout = setTimeout(DupliJs.UI.Clock, lag);
            };
        }();

        /*  Runs callback function when form values change */
        DupliJs.UI.formOnChangeValues = function(form, callback) {
            let previousValues = form.serialize();

            $('form :input').on('change input', function() {
                if (previousValues !== form.serialize()) {
                    previousValues = form.serialize();
                    callback();
                }
            });

            $('.dup-pseudo-checkbox, #dbnone, #dball').on('click', function() {
                // Since the pseudo checkbox is not a form input,
                // assume the state is changed on click
                callback();
            });
        };
    })(jQuery);
</script>