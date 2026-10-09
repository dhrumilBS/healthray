<?php

use Duplicator\Ajax\ServicesActivityLog;
use Duplicator\Models\ActivityLog\LogUtils;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\GlobalEntity;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$global          = GlobalEntity::getInstance();
$dGlobal         = DynamicGlobalEntity::getInstance();
$retentionMonths = $dGlobal->getValInt('activity_log_retention') / MONTH_IN_SECONDS;
?>

<div class="dup-accordion-wrapper display-separators close">
    <div class="accordion-header">
        <h3 class="title" id="advanced-section-header">
            <?php esc_html_e('Advanced', 'duplicator') ?>
        </h3>
    </div>
    <div class="accordion-content">

        <label class="lbl-larger">
            <?php esc_html_e('Settings', 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <button
                id="dupli-reset-all"
                class="button secondary hollow small margin-0"
                onclick="DupliJs.Pack.ConfirmResetAll(); return false">
                <i class="fas fa-redo fa-sm"></i> <?php esc_html_e('Reset All Settings', 'duplicator'); ?>
            </button>
            <p class="description">
                <?php
                esc_html_e("Reset all settings to their defaults.", 'duplicator');
                $tContent = __(
                    'Resets standard settings to defaults. Does not affect capabilities, license key, storage or schedules.',
                    'duplicator'
                );
                ?>
                <i
                    class="fa-solid fa-question-circle fa-sm dark-gray-color"
                    data-tooltip-title="<?php esc_attr_e("Reset Settings", 'duplicator'); ?>"
                    data-tooltip="<?php echo esc_attr($tContent); ?>">
                </i>
            </p>
        </div>

        <label class="lbl-larger">
            <?php esc_html_e("Foreign JavaScript", 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <input
                type="checkbox"
                name="_unhook_third_party_js"
                id="_unhook_third_party_js"
                value="1"
                class="margin-0"
                <?php checked($global->shouldUnhookThirdPartyJs()); ?>>
            <label for="_unhook_third_party_js"><?php esc_html_e("Disable", 'duplicator'); ?></label> <br />
            <p class="description">
                <?php
                printf(
                    /* translators: %s: plugin name */
                    esc_html__("Check this option if JavaScript from the theme or other plugins conflicts with %s pages.", 'duplicator'),
                    esc_html(DUPLICATOR____NAME)
                );
                ?>
                <br>
                <?php
                esc_html_e("Do not modify this setting unless you know the expected result or have talked to support.", 'duplicator');
                ?>
            </p>
        </div>

        <label class="lbl-larger">
            <?php esc_html_e("Foreign CSS", 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <input
                type="checkbox"
                name="_unhook_third_party_css"
                id="unhook_third_party_css"
                value="1"
                class="margin-0"
                <?php checked($global->shouldUnhookThirdPartyCss()); ?>>
            <label for="unhook_third_party_css"><?php esc_html_e("Disable", 'duplicator'); ?></label> <br />
            <p class="description">
                <?php
                printf(
                    /* translators: %s: plugin name */
                    esc_html__("Check this option if CSS from the theme or other plugins conflicts with %s pages.", 'duplicator'),
                    esc_html(DUPLICATOR____NAME)
                );
                ?>
                <br>
                <?php
                esc_html_e("Do not modify this setting unless you know the expected result or have talked to support.", 'duplicator');
                ?>
            </p>
        </div>

        <label class="lbl-larger">
            <?php esc_html_e("Activity Log Retention", 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <input
                type="number"
                class="width-small inline-display margin-0"
                name="activity_log_retention_months"
                id="activity_log_retention_months"
                value="<?php echo (int) $retentionMonths; ?>"
                min="0"
                step="1">
            &nbsp;<span class="inline-display"><?php esc_html_e("months", 'duplicator'); ?></span>
            <p class="description">
                <?php
                esc_html_e("Set how many months to keep activity log entries. Enter 0 to keep all logs permanently.", 'duplicator');
                ?>
                <br>
                <?php
                esc_html_e("Activity logs older than the specified number of months will be automatically deleted.", 'duplicator');
                ?>
            </p>
        </div>

        <label class="lbl-larger">
            <?php esc_html_e("Delete Activity Logs", 'duplicator'); ?>
        </label>
        <div class="margin-bottom-1">
            <button
                type="button"
                id="dup-delete-activity-logs"
                class="button secondary hollow small margin-0"
                data-nonce="<?php echo esc_attr(wp_create_nonce(ServicesActivityLog::NONCE_DELETE_ALL)); ?>"
                onclick="DupliJs.Settings.ConfirmDeleteActivityLogs(); return false;">
                <i class="fas fa-trash fa-sm"></i> <?php esc_html_e('Delete All Logs', 'duplicator'); ?>
            </button>
            <p class="description">
                <?php esc_html_e("Permanently delete all activity log entries. This action cannot be undone.", 'duplicator'); ?>
            </p>
        </div>
    </div>
</div>