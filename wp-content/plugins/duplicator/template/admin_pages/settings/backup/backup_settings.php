<?php

use Duplicator\Controllers\SettingsPageController;
use Duplicator\Core\Controllers\ControllersManager;

defined("ABSPATH") or die("");


/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$availabilityMatrix = $tplMng->getDataValueArrayRequired('availabilityMatrix');
?>
<form
    id="dup-settings-form" class="dup-settings-pack-basic"
    action="<?php echo esc_url(ControllersManager::getCurrentLink()); ?>"
    method="post" data-parsley-validate
>
        <?php $tplMng->getAction(SettingsPageController::ACTION_PACKAGE_BASIC_SAVE)->getActionNonceFileds(); ?>
    <div class="dup-settings-wrapper margin-bottom-1">
        <?php $tplMng->render('admin_pages/settings/backup/database_settings'); ?>
        <?php $tplMng->render('admin_pages/settings/backup/archive_settings'); ?>
        <?php $tplMng->render('admin_pages/settings/backup/processing_settings'); ?>
        <?php $tplMng->render('admin_pages/settings/backup/installer_settings'); ?>
        <?php $tplMng->render('admin_pages/settings/backup/advanced_settings'); ?>
    </div>
    <p class="submit dupli-save-submit">
    <input
        type="submit" 
        name="submit" 
        id="submit" 
        class="button primary small"
        value="<?php esc_attr_e('Save Settings', 'duplicator') ?>" style="display: inline-block;"
    >
    </p>
</form>

<script>
    jQuery(document).ready(function ($)
    {

        DupliJs.UI.SetDBEngineMode = function ()
        {
            var isMysqlDump = $('#package_mysqldump').is(':checked');
            var isPHPMode = $('#package_phpdump').is(':checked');
            var isPHPChunkMode = $('#package_phpchunkingdump').is(':checked');

            $('#dbengine-details-1, #dbengine-details-2').hide();
            switch (true) {
                case isMysqlDump :
                    $('#dbengine-details-1').show();
                    break;
                case isPHPMode  :
                case isPHPChunkMode :
                    $('#dbengine-details-2').show();
                    break;
            }

            // Keep the custom-path remediation visible when auto-detection
            // disabled the Mysqldump radio.
            if ($('#package_mysqldump').is(':disabled') && $('#_package_mysqldump_path').length) {
                $('#dbengine-details-1').show();
            }
        }

        DupliJs.UI.setZipArchiveMode = function ()
        {
            $('#dupli-ziparchive-mode-st, #dupli-ziparchive-mode-mt').hide();
            if ($('#ziparchive_mode').val() == 0) {
                $('#dupli-ziparchive-mode-mt').show();
            } else {
                $('#dupli-ziparchive-mode-st').show();
            }
        }

        DupliJs.UI.SetArchiveOptionStates = function ()
        {
            var isShellZipSelected = $('#archive_build_mode1').is(':checked');
            var isZipArchiveSelected = $('#archive_build_mode2').is(':checked');
            var isDupArchiveSelected = $('#archive_build_mode3').is(':checked');

            $("[name='ziparchive_mode']").prop('disabled', !isZipArchiveSelected);

            $('#engine-details-1, #engine-details-2, #engine-details-3').hide();
            switch (true) {
                case isShellZipSelected       :
                    $('#engine-details-1').show();
                    break;
                case isZipArchiveSelected   :
                    $('#engine-details-2').show();
                    break;
                case isDupArchiveSelected   :
                    $('#engine-details-3').show();
                    break;
            }
            DupliJs.UI.setZipArchiveMode();
        }

        /**
         * Generic availability update: for every gated option, look up the
         * outcome of each of its values under the current parent values and
         * sync disabled state, tooltip and the optional reasons notice.
         * Same data source as the backend gate, no logic duplicated here.
         */
        DupliJs.UI.applyOptionAvailability = function (matrix)
        {
            $.each(matrix, function (optionKey, cfg) {
                var update = function () {
                    var comboParts = [];
                    $.each(cfg.dependsOn, function (i, depKey) {
                        comboParts.push(String($("input[name='" + cfg.parentFields[depKey] + "']:checked").val()));
                    });
                    var outcomes = cfg.entries[comboParts.join('|')];
                    if (!outcomes) {
                        return;
                    }

                    $("input[name='" + cfg.field + "']").each(function () {
                        var outcome = outcomes[String($(this).val())];
                        if (!outcome) {
                            return;
                        }
                        $(this).prop('disabled', !outcome.available);

                        var warningIcon = $('#' + cfg.field + '_warning_' + String($(this).val()));
                        if (warningIcon.length) {
                            var reasonsText = outcome.reasons.join(' ');
                            warningIcon
                                .attr('data-tooltip', reasonsText)
                                .toggleClass('display-none', outcome.available);

                            // Tippy builds its content at creation time: push the new reasons
                            // into the live instance or the tooltip body goes stale.
                            var iconEl = warningIcon.get(0);
                            if (iconEl && iconEl._tippy) {
                                var header = iconEl.dataset.tooltipTitle ? '<h3>' + iconEl.dataset.tooltipTitle + '</h3>' : '';
                                iconEl._tippy.setContent(header + '<div class="dup-tippy-content">' + reasonsText + '</div>');
                            }
                        }
                    });

                    // A selection turned unavailable falls back to the first available value
                    if ($("input[name='" + cfg.field + "']:checked").prop('disabled')) {
                        $("input[name='" + cfg.field + "']:not(:disabled)").first().prop('checked', true);
                    }
                };

                $.each(cfg.dependsOn, function (i, depKey) {
                    $(document).on('change', "input[name='" + cfg.parentFields[depKey] + "']", update);
                });
                update();
            });
        }

        //INIT
        DupliJs.UI.SetArchiveOptionStates();
        DupliJs.UI.SetDBEngineMode();
        DupliJs.UI.applyOptionAvailability(<?php echo wp_json_encode($availabilityMatrix); ?>);

        DupliJs.UI.cleanupModeRadioSwitched = function() {
            if ($('#cleanup_mode_Cleanup_Off').is(":checked")){
                $('#auto_cleanup_hours').attr('readonly','readonly');
                $('#cleanup_email').attr('readonly','readonly');
            } else if ($('#cleanup_mode_Email_Notice').is(":checked")) {
                $('#auto_cleanup_hours').attr('readonly','readonly');
                $("#cleanup_email").removeAttr('readonly');
            } else if ($('#cleanup_mode_Auto_Cleanup').is(":checked")) {
                $("#auto_cleanup_hours").removeAttr('readonly');
                $("#cleanup_email").removeAttr('readonly');
            }
        }

        $('input[type=radio][name=cleanup_mode]').change(function () {
            DupliJs.UI.cleanupModeRadioSwitched();
        });
        // We must call this also once in the beginning, after UI is loaded
        DupliJs.UI.cleanupModeRadioSwitched();

    });
</script>