<?php

namespace Duplicator\Views;

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Libs\Snap\SnapUtil;
use WP_Screen;

/**
 * Screen base class
 */
class ScreenBase
{
    /** @var ?WP_Screen Used as a placeholder for the current screen object */
    public $screen;

    /**
     *  Init this object when created
     */
    public function __construct()
    {
    }

    /**
     * Print custom CSS for the current color scheme
     *
     * @return void
     */
    public static function getCustomCss(): void
    {
        // Disable custom CSS
        return;

        /*
        if (!ControllersManager::getInstance()->isDuplicatorPage()) {
            return;
        }

        if (($colorScheme = self::getCurrentColorScheme()) === null) {
            return;
        }

        $primaryButtonColor = self::getPrimaryButtonColorByScheme();
        ?>
        <style>
            .dupli-meter.blue>span {
                background-color: <?php echo $colorScheme->colors[2]; ?>;
                background-image: none;
            }

            .dupli-recovery-point-actions>.copy-link {
                border-color: <?php echo $primaryButtonColor; ?>;
            }

            .dupli-recovery-point-actions>.copy-link .copy-icon {
                background-color: <?php echo $primaryButtonColor; ?>;
            }


            .tippy-box[data-theme~='duplicator'],
            .tippy-box[data-theme~='duplicator-filled'] {
                border-color: <?php echo $primaryButtonColor; ?>;
            }

            .tippy-box[data-theme~='duplicator'] h3,
            .tippy-box[data-theme~='duplicato-filled'] h3 {
                background-color: <?php echo $primaryButtonColor; ?>;
            }

            .tippy-box[data-theme~='duplicator-filled'] .tippy-content {
                background-color: <?php echo $primaryButtonColor; ?>;
            }

            .tippy-box[data-theme~='duplicator'][data-placement^='top']>.tippy-arrow::before,
            .tippy-box[data-theme~='duplicator-filled'][data-placement^='top']>.tippy-arrow::before {
                border-top-color: <?php echo $primaryButtonColor; ?>;
            }

            .tippy-box[data-theme~='duplicator'][data-placement^='bottom']>.tippy-arrow::before,
            .tippy-box[data-theme~='duplicator-filled'][data-placement^='bottom']>.tippy-arrow::before {
                border-bottom-color: <?php echo $primaryButtonColor; ?>;
            }

            .tippy-box[data-theme~='duplicator'][data-placement^='left']>.tippy-arrow::before,
            .tippy-box[data-theme~='duplicator-filled'][data-placement^='left']>.tippy-arrow::before {
                border-left-color: <?php echo $primaryButtonColor; ?>;
            }

            .tippy-box[data-theme~='duplicator'][data-placement^='right']>.tippy-arrow::before,
            .tippy-box[data-theme~='duplicator-filled'][data-placement^='right']>.tippy-arrow::before {
                border-right-color: <?php echo $primaryButtonColor; ?>;
            }

            nav.dup-dnload-menu-items button:hover {
                background-color: <?php echo $primaryButtonColor; ?>;
            }

            .button-primary.dup-base-color,
            .button-primary .dup-base-color,
            .button-primary i[data-tooltip].fa-question-circle.dup-base-color,
            .button-primary i[data-tooltip].fa-question-circle.dup-base-color {
                color: <?php echo $colorScheme->colors[1]; ?>;
            }

            .dup-radio-button-group-wrapper input[type="radio"] + label {
                color: <?php echo $primaryButtonColor; ?>;
                border-color: <?php echo $primaryButtonColor; ?>;
            }

            .dup-radio-button-group-wrapper input[type="radio"] + label:hover,
            .dup-radio-button-group-wrapper input[type="radio"]:focus + label,
            .dup-radio-button-group-wrapper input[type="radio"]:checked + label {
                background: <?php echo $primaryButtonColor; ?>;
                border-color: <?php echo $primaryButtonColor; ?>;
            }
        </style>
        <?php
        */
    }

    /**
     * Unfortunately not all color schemes take the same color as the buttons so you need to make a custom switch/
     *
     * @return string
     */
    public static function getPrimaryButtonColorByScheme()
    {
        $colorScheme = self::getCurrentColorScheme();
        $name        = strtolower($colorScheme->name);
        switch ($name) {
            case 'blue':
                return '#e3af55';
            case 'light':
            case 'midnight':
                return $colorScheme->colors[3];
            case 'ocean':
            case 'ectoplasm':
            case 'coffee':
            case 'sunrise':
            case 'default':
            default:
                return $colorScheme->colors[2];
        }
    }

    /**
     * Current color scheme
     *
     * @return object{name:string,colors:string[]} return the current color scheme object
     */
    public static function getCurrentColorScheme()
    {
        global $_wp_admin_css_colors;
        $colorScheme = get_user_option('admin_color');

        if (isset($_wp_admin_css_colors[$colorScheme])) {
            return $_wp_admin_css_colors[$colorScheme];
        } else {
            if (is_array($_wp_admin_css_colors) && count($_wp_admin_css_colors) > 0) {
                return $_wp_admin_css_colors[SnapUtil::arrayKeyFirst($_wp_admin_css_colors)];
            } else {
                return (object) [
                    'name'   => 'default',
                    'colors' => [
                        '#1d2327',
                        '#2c3338',
                        '#2271b1',
                        '#72aee6',
                    ],
                ];
            }
        }
    }
}
