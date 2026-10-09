// @ts-check

/**
 * Document ready handler for extension management functionalities
 */
jQuery(document).ready(function () {
    set_event_listeneres();
    disply_updates_marks();
});

/**
 * Displays update markers for available extension updates
 * Adds a notification badge to the extensions panel tab
 */
function disply_updates_marks() {
    const update_count = jQuery('.extensions-list .has-update').length;
    if (update_count) {
        jQuery('li#extensions-panel-tab a').append(' <span class="update-plugins wpcf7r-update-extensions"><span class="plugin-count">' + update_count + '</span></span>');
    }
}

/**
 * Sets up all event listeners for extension management
 */
function set_event_listeneres() {
    activate_serial_handler();
    close_promo_box();
    serial_activation_handler();
    extension_deactivate_handler();
    extension_update_handler();
}

/**
 * Handles click events on extension update buttons
 * Triggers the update process for an extension
 */
function extension_update_handler() {
    jQuery('.extensions').on('click', '.promo-box .btn-update', function (e) {
        e.preventDefault();
        const extensionElement = jQuery(this).parents('.promo-box');
        show_extension_loader(extensionElement);
        update_wpcf7r_extension(extensionElement);
    });
}

/**
 * Sets up event listeners for serial activation buttons
 * Shows the serial input field when the activate button is clicked
 */
function activate_serial_handler() {
    jQuery('.extensions').on('click', '.promo-box .btn-activate', function (e) {
        e.preventDefault();
        jQuery(this).parents('.promo-box').find('.serial').addClass('open');
    });
}

/**
 * Sets up event listeners for closing promo boxes
 * Hides the serial input field when the close button is clicked
 */
function close_promo_box() {
    jQuery('.extensions').on('click', '.promo-box .btn-close', function (e) {
        e.preventDefault();
        jQuery(this).parents('.promo-box').find('.serial').removeClass('open');
    });
}

/**
 * Handles the serial activation process
 * Validates the serial number and sends it for activation
 */
function serial_activation_handler() {
    jQuery('.extensions').on('click', '.promo-box .btn-activate-serial', function (e) {
        e.preventDefault();
        const extensionElement = jQuery(this).parents('.promo-box');
        const serial = extensionElement.find('.serial-number').val();
        if (!serial) {
            extensionElement.find('.serial-number').addClass('err');
            return false;
        }
        extensionElement.find('.serial-number').removeClass('err');
        show_extension_loader(extensionElement);
        activate_extension(extensionElement, serial);
    });
}

/**
 * Sets up event listeners for extension deactivation buttons
 * Triggers the deactivation process for an extension
 */
function extension_deactivate_handler() {
    jQuery('.extensions').on('click', '.promo-box .btn-deactivate', function (e) {
        e.preventDefault();
        const extensionElement = jQuery(this).parents('.promo-box');
        show_extension_loader(extensionElement);
        deactivate_plugin_license(extensionElement);
    });
}

/**
 * Displays a loading indicator on the extension element
 * @param {jQuery} extensionElement - The jQuery element representing the extension 
 */
function show_extension_loader(extensionElement) {
    extensionElement.append('<div class="wpcf7r_loader active"></div>');
}

/**
 * Deactivates a plugin license
 * Makes an AJAX call to deactivate the extension's license
 * @param {jQuery} extensionElement - The jQuery element representing the extension
 */
function deactivate_plugin_license(extensionElement) {
    const extensionName = extensionElement.data('extension');

    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: ajaxurl,
        data: {
            action: "deactivate_wpcf7r_extension",
            extension_name: extensionName,
            wpcf7r_nonce: wpcf_get_nonce(),
        },
        success: function (response) {
            console.log(response);
            if (typeof response.error != 'undefined') {
                jQuery('.actions').after('<div class="err">' + response.error + '</div>');
            } else if (typeof response.extension_html != 'undefined') {
                extensionElement.replaceWith(response.extension_html);
            }
            remove_extension_loader();
        }
    });
}

/**
 * Removes all loading indicators
 * Called after AJAX operations are complete
 */
function remove_extension_loader() {
    jQuery('.wpcf7r_loader').remove();
}

/**
 * Updates an extension via AJAX
 * @param {jQuery} extensionElement - The jQuery element representing the extension
 */
function update_wpcf7r_extension(extensionElement) {
    const extensionName = extensionElement.data('extension');

    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: ajaxurl,
        data: {
            action: "wpcf7r_extension_update",
            extension_name: extensionName,
            wpcf7r_nonce: wpcf_get_nonce(),
        },
        success: function (response) {
            if (response.extension_html != 'undefined' && response.extension_html) {
                extensionElement.replaceWith(response.extension_html);
            } else if (typeof response.error != 'undefined' && response.error) {
                jQuery('.actions').after('<div class="err">' + response.error + '</div>');
            }
            remove_extension_loader();
        }
    });
}

/**
 * Activates an extension using the provided serial number
 * Makes an AJAX call to validate and activate the extension
 * @param {jQuery} extensionElement - The jQuery element representing the extension
 * @param {string} serial - The serial number for activation
 */
function activate_extension(extensionElement, serial) {
    const extensionName = extensionElement.data('extension');
    
    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: ajaxurl, 
        data: {
            action: "activate_wpcf7r_extension",
            extension_name: extensionName,
            serial: serial,
            wpcf7r_nonce: wpcf_get_nonce(),
        },
        success: function (response) {
            if (response.extension_html != 'undefined' && response.extension_html) {
                extensionElement.replaceWith(response.extension_html);
                window.location.reload();
            } else if (typeof response.error != 'undefined' && response.error) {
                extensionElement.find('.err').remove();
                extensionElement.append('<div class="err">' + response.error + '</div>');
            }

            remove_extension_loader();
        }
    });
}