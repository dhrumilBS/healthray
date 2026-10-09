<?php
defined("ABSPATH") or die("");
?>
<script>
/*! ============================================================================
* DESCRIPTION: Methods and Objects in this file are global and common in nature
* use this file to place all shared methods and varibles
* NAMESPACE: DupliJs (defined in dupli-namespace.js, loaded via main.js bundle)
* ============================================================================ */

DupliJs.Pack.DownloadFile = function (url, fileName='') {
    var link = document.createElement('a');
    link.className = "dupli-dnload-menu-item";
    link.href = url;
    if (fileName !== '') {
        link.download = fileName;
    }
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    return false;
};

(function ($) {

    /* ============================================================================
     *  BASE NAMESPACE: All methods at the top of the Duplicator Namespace
     * ============================================================================ */

    DupliJs._WordPressInitDateTime = '<?php echo esc_js(current_time("D M d Y H:i:s O")) ?>';
    DupliJs._WordPressInitTime = '<?php echo esc_js(current_time("H:i:s")) ?>';
    DupliJs._ServerInitDateTime = '<?php echo esc_js(date("D M d Y H:i:s O")) ?>';
    DupliJs._ClientInitDateTime = new Date();

    DupliJs.parseJSON = function (mixData) {
        try {
            var parsed = JSON.parse(mixData);
            return parsed;
        } catch (e) {
            console.log("JSON parse failed - 1");
            console.log(mixData);
        }

        if (mixData.indexOf('[') > -1 && mixData.indexOf('{') > -1) {
            if (mixData.indexOf('{') < mixData.indexOf('[')) {
                var startBracket = '{';
                var endBracket = '}';
            } else {
                var startBracket = '[';
                var endBracket = ']';
            }
        } else if (mixData.indexOf('[') > -1 && mixData.indexOf('{') === -1) {
            var startBracket = '[';
            var endBracket = ']';
        } else {
            var startBracket = '{';
            var endBracket = '}';
        }

        var jsonStartPos = mixData.indexOf(startBracket);
        var jsonLastPos = mixData.lastIndexOf(endBracket);
        if (jsonStartPos > -1 && jsonLastPos > -1) {
            var expectedJsonStr = mixData.slice(jsonStartPos, jsonLastPos + 1);
            try {
                var parsed = JSON.parse(expectedJsonStr);
                return parsed;
            } catch (e) {
                console.log("JSON parse failed - 2");
                console.log(mixData);
                throw e;
                // errorCallback(xHr, textstatus, 'extract');
                return false;
            }
        }
        // errorCallback(xHr, textstatus, 'extract');
        throw "could not parse the JSON";
        return false;
    }

    DupliJs.escapeHtml = function(str) {
        return str
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
    };

    DupliJs.isInViewport = function ( $element ) {
        const rect = $element[ 0 ].getBoundingClientRect();

        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= ( window.innerHeight || document.documentElement.clientHeight ) &&
            rect.right <= ( window.innerWidth || document.documentElement.clientWidth )
        );
    };

    /**
     *
     * @param string message // html message conent
     * @param string errLevel // notice warning error
     * @param function updateCallback // called after message content is updated
     *
     * @returns void
     */
    DupliJs.addAdminMessage = function (message, errLevel, options) {
        let settings = $.extend({}, {
            'isDismissible': true,
            'hideDelay': 0, // 0 no hide or millisec
            'updateCallback': false
        }, options);

        var classErrLevel = 'notice';
        switch (errLevel) {
            case 'error':
                classErrLevel = 'notice-error';
                break;
            case 'warning':
                classErrLevel = 'update-nag';
                break;
            case 'notice':
            default:
                classErrLevel = 'updated notice-success';
                break;
        }

        var noticeCLasses = 'dupli-admin-notice notice ' + classErrLevel + ' no-display';
        if (settings.isDismissible) {
            noticeCLasses += ' is-dismissible';
        }

        var msgNode = $('<div class="' + noticeCLasses + '">' +
                '<div class="msg-content">' + message + '</div>' +
                '</div>');
        var dismissButton = $('<button type="button" class="notice-dismiss">' +
                '<span class="screen-reader-text">Dismiss this notice.</span>' +
                '</button>');

        let anchor = $(".wp-header-end").first();
        msgNode.insertAfter(anchor);

        if (settings.isDismissible) {
            dismissButton.appendTo(msgNode).click(function () {
                dismissButton.closest('.is-dismissible').fadeOut("slow", function () {
                    $(this).remove();
                });
            });
        }

        if (typeof settings.updateCallback === "function") {
            settings.updateCallback(msgNode);
        }

        $("body, html").animate({scrollTop: 0}, 500);
        $(msgNode).css('display', 'none').removeClass("no-display").fadeIn("slow", function () {
            if (settings.hideDelay > 0) {
                setTimeout(function () {
                    dismissButton.closest('.is-dismissible').fadeOut("slow", function () {
                        $(this).remove();
                    });
                }, settings.hideDelay);
            }
        });
    };

    /**
     *
     * @param string filename
     * @param string content
     * @param string mimeType // text/html, text/plain
     * @returns {undefined}
     */
    DupliJs.downloadContentAsfile = function (filename, content, mimeType) {
        mimeType = (typeof mimeType !== 'undefined') ? mimeType : 'text/plain';
        var element = document.createElement('a');
        element.setAttribute('href', 'data:' + mimeType + ';charset=utf-8,' + encodeURIComponent(content));
        element.setAttribute('download', filename);

        element.style.display = 'none';
        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);
    }


    DupliJs.openWindow = function () {
        $("[data-dup-open-window]").each(function () {
            let url = $(this).data('dup-open-window');
            let name = $(this).data('dup-window-name');

            $(this).click(function () {
                window.open(url, name);
            });
        });
    }

    DupliJs.passwordToggle = function () {
        $('.dup-password-toggle').each(function () {
            let inputElem = $(this).find('input');
            let buttonElem = $(this).find('button');
            let iconElem = $(this).find('button i');

            buttonElem.click(function () {
                if (inputElem.attr('type') == 'password') {
                    inputElem.attr('type','text');
                    iconElem.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    inputElem.attr('type','password');
                    iconElem.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
        });
    }

})(jQuery);
</script>

<?php
    require_once(DUPLICATOR____PATH . '/assets/js/duplicator/dup.ui.php');
    require_once(DUPLICATOR____PATH . '/assets/js/duplicator/dup.util.php');
?>
<script>
    <?php
        require_once(DUPLICATOR____PATH . '/assets/js/modal-box.js');
    ?>
</script>

<script>
//Init
jQuery(document).ready(function ($)
{
    DupliJs.openWindow();

    //INIT: DupliJs Tabs
    $("div[data-dupli-tabs='true']").each(function ()
    {
        //Load Tab Setup
        var $root = $(this);
        var $lblRoot = $root.find('> ul:first-child')
        var $lblKids = $lblRoot.children('li');
        var $lblButton = $lblKids.find('button');
        var $pnls = $root.children('div');

        //Apply Styles
        $root.addClass('categorydiv');
        $lblRoot.addClass('category-tabs');
        $pnls.addClass('tabs-panel').css('display', 'none');

        //Init accessibility improvement
        $lblKids.each(function () {
            var $content = $(this).text();
            $(this).html("<button role='tabs' aria-selected='false'>" +
                "<span class='screen-reader-text'><?php esc_html_e('Toggle Tab: ', 'duplicator') ?></span> "+$content+
                "</button>")
        })

        //Activate first tab
        $lblKids.eq(0).addClass('tabs').css('font-weight', 'bold');
        $lblKids.eq(0).find('button').attr("aria-selected", true)
        $pnls.eq(0).show();

        //Initialize tab click event
        var _clickEvt = function (evt)
        {
            var $target = $(evt.target);
            if (evt.target.nodeName === 'BUTTON') {
                $target = $(evt.target).parent();
            }
            var $lbls = $target.parent().children('li');
            var $pnls = $target.parent().parent().children('div');
            var index = $target.index();

            $lbls.removeClass('tabs').css('font-weight', 'normal');
            $lbls.find("button").attr("aria-selected", false);

            $lbls.eq(index).addClass('tabs').css('font-weight', 'bold');
            $lbls.eq(index).find("button").attr("aria-selected", true);

            $pnls.hide();
            $pnls.eq(index).show();

            return false;
        }

        //Attach Events
        $lblKids.click(_clickEvt);
        $lblButton.on("click", _clickEvt);
    });

    //INIT: Toggle MetaBoxes (static boxes are always open and not clickable)
    $('div.dup-box:not(.dupli-box-static) div.dup-box-title').each(function () {
        var $title = $(this);
        var $panel = $title.parent().find('.dup-box-panel');
        var $arrow = $title.find('.dup-box-arrow');

        $title.click(DupliJs.UI.ToggleMetaBox);
        //$arrow.on("keypress", DupliJs.UI.ToggleMetaBox)
        $arrow.attr("aria-haspopup", true);

        if ($panel.is(":visible")) {
            $arrow.attr("aria-expanded", true);
            $arrow.append('<i class="fa fa-caret-up"></i>');
        } else {
            $arrow.attr("aria-expanded", false);
            $arrow.append('<i class="fa fa-caret-down"></i>')
        }
    });

    DuplicatorTooltip.load();
    DupliJs.passwordToggle();

    //HANDLEBARS HELPERS
    if (typeof DupliJs !== "undefined" && DupliJs.Libs && typeof DupliJs.Libs.Handlebars !== "undefined") {

        function _handleBarscheckCondition(v1, operator, v2) {
            switch (operator) {
                case '==':
                    return (v1 == v2);
                case '===':
                    return (v1 === v2);
                case '!==':
                    return (v1 !== v2);
                case '<':
                    return (v1 < v2);
                case '<=':
                    return (v1 <= v2);
                case '>':
                    return (v1 > v2);
                case '>=':
                    return (v1 >= v2);
                case '&&':
                    return (v1 && v2);
                case '||':
                    return (v1 || v2);
                case 'obj||':
                    v1 = typeof (v1) == 'object' ? v1.length : v1;
                    v2 = typeof (v2) == 'object' ? v2.length : v2;
                    return (v1 != 0 || v2 != 0);
                default:
                    return false;
            }
        }

        DupliJs.Libs.Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {
            return _handleBarscheckCondition(v1, operator, v2)
                    ? options.fn(this)
                    : options.inverse(this);
        });

        DupliJs.Libs.Handlebars.registerHelper('if_eq', function (a, b, opts) {
            return (a == b) ? opts.fn(this) : opts.inverse(this);
        });
        DupliJs.Libs.Handlebars.registerHelper('if_neq', function (a, b, opts) {
            return (a != b) ? opts.fn(this) : opts.inverse(this);
        });
    }

    $('.dup-pseudo-checkbox').each(function () {
        let checkbox = $(this);
        checkbox.attr("tabindex", 0);
        checkbox.attr("role", "checkbox")

        checkbox.on('click', function(e) {
            e.stopPropagation();
            if (checkbox.hasClass('disabled')) {
                return;
            }
            checkbox.toggleClass('checked');
        });

        checkbox.on('keypress', function(e) {
            e.stopPropagation();
            e.preventDefault();
            if (checkbox.hasClass('disabled')) {
                return;
            }
            checkbox.toggleClass('checked');
        });

        checkbox.closest('label').on('click', function () {
            checkbox.trigger('click');
        });
    });

    /**
     * Register a change event handler for all forms with the class 'dup-monitored-form'.
     * This will set a flag to indicate that the form has unsaved changes.
     */
    $('form.dup-monitored-form').each(function (index, form) {
        DupliJs.UI.formOnChangeValues($(form), function() {
            DupliJs.UI.hasUnsavedChanges = true;
        });
    });

    /**
     * Accordion
     */
    $('.dup-accordion-wrapper .accordion-header').on('click', function () {
        let accordion = $(this).parent();
        let content = accordion.find('.accordion-content');
        if (accordion.hasClass('close')) {
            accordion.removeClass('close').addClass('open');
            content.css('opacity', 0).animate({
                opacity: 1
            }, 300);
        } else {
            content.animate({
                opacity: 0
            }, 300, function() {
                accordion.removeClass('open').addClass('close');
            });
        }
    });

    /**
    * Meta Screen
    */
    if ($('#screen-meta-links').length && $('body').hasClass('duplicator-page')) {
        $('#wpcontent').css('position', 'relative');
    }

    $('#screen-meta-links, #screen-meta').prependTo('#dup-meta-screen');
    $('#screen-meta-links').show();

    /**
    * Header tabs scroll
    */
    if($('.dup-nav-item:last-child').length > 0) {
        let $header  = $('.dup-body-header').first();
        let $lastTab = $header.find('.dup-nav-item:last-child');
        if (!DupliJs.isInViewport($lastTab)) {
            $header.addClass('dup-scrollable-header');
        }

        $header.on('scroll', function() {
            $header.toggleClass('dup-scrollable-header', !DupliJs.isInViewport($lastTab));
        });
    }

    /**
     * When a form is submitting, we want to clear the unsaved changes flag.
     * Otherwise, the user will be prompted to save changes when they are not actually leaving the page.
     */
    window.addEventListener('submit', function (e) {
        DupliJs.UI.hasUnsavedChanges = false;
    });

    /**
     * Check if we have unsaved changes, and if so, prevent the user from navigating away from the page.
     */
    window.addEventListener('beforeunload', function (e) {
        if (DupliJs.UI.hasUnsavedChanges) {
            e.preventDefault();
            // Most browsers ignore the value, but historically some browsers are known to honor this value. So it's here as a backup
            e.returnValue = '<?php echo esc_js(__('Changes you made may not be saved.', 'duplicator')) ?>';
        }
    });
});
</script>
