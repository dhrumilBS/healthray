<?php

/**
 * Duplicator messages sections
 */

use Duplicator\Controllers\SettingsPageController;

defined("ABSPATH") or die("");
?>
<p class="description">
    <?php esc_html_e("When this limit is exceeded, the oldest Backup will be deleted. Set to 0 for no limit.", 'duplicator'); ?>
    <i 
        class="fa-solid fa-question-circle fa-sm dark-gray-color"
            data-tooltip="<?php echo esc_attr(
                sprintf(
                    _x(
                        'To configure how the associated backup record in the "Backups" screen is handled check out the
                        %1$s"Delete Backup Records"%2$s setting.',
                        '1: <a> tag, 2: </a> tag',
                        'duplicator'
                    ),
                    '<a href="' . esc_url(SettingsPageController::getInstance()->getMenuLink(SettingsPageController::L2_SLUG_STORAGE)) . '">',
                    '</a>'
                )
            ); ?>"
    >
    </i>
</p>
