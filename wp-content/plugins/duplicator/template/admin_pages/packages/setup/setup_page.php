<?php



defined("ABSPATH") or die("");

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\Options\Requirements\ConfigValidation;
use Duplicator\Models\TemplateEntity;
use Duplicator\Views\UI\UiDialog;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$validation = $tplMng->getDataValueObjRequired('validation', ConfigValidation::class);
$blur       = $tplMng->getDataValueBool('blur');

$manual_template = TemplateEntity::getManualTemplate();

$tplMng->render('admin_pages/packages/setup/section-requirements');
$form_action_url = PackagesPageController::getInstance()->getPackageBuildS2Url();

// Optional aside content (e.g. the template selector) injected by addons through the hook.
// The core only checks whether the buffered output is empty, never which addon is hooked.
ob_start();
do_action('duplicator_package_general_area_after', $manual_template);
$generalAsideHtml = trim((string) ob_get_clean());
?>
<form
    id="dup-form-opts"
    class="<?php echo ($blur ? 'dup-mock-blur' : ''); ?>"
    method="post"
    action="<?php echo esc_attr($form_action_url); ?>"
    data-parsley-validate data-parsley-ui-enabled="true">
    <?php $tplMng->getAction(PackagesPageController::ACTION_UPDATE_TEMPLATE)->getActionNonceFileds(); ?>
    <div class="dupli-setup-split">
        <div class="dupli-setup-main">
            <div class="dup-box dupli-box-static dupli-general-box">
                <div class="dup-box-title">
                    <i class="fas fa-list-check fa-sm"></i> <?php esc_html_e('General', 'duplicator') ?>
                    <?php $tplMng->render('parts/packages/filters/section_filters_incons', ['template' => $manual_template]); ?>
                </div>
                <div class="dup-box-panel">
                    <div class="dupli-general-area <?php echo ($generalAsideHtml === '' ? 'dupli-general-area-no-aside' : ''); ?>">
                        <div class="dupli-general-name">
                            <?php
                            $tplMng->render(
                                'admin_pages/packages/setup/name-format-controls',
                                ['nameFormat' => $manual_template->package_name_format]
                            );
                            ?>
                        </div>
                        <?php if ($generalAsideHtml !== '') { ?>
                            <div class="dupli-general-aside">
                                <?php
                                // Buffered addon output, already escaped by the addon that rendered it
                                echo $generalAsideHtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                ?>
                            </div>
                        <?php } ?>
                        <div class="dupli-general-storage">
                            <?php $tplMng->render('admin_pages/packages/setup/section_storages'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $tplMng->render(
                'parts/packages/filters/section_filters',
                [
                    'isTemplateEdit' => false,
                    'template'       => $manual_template,
                ]
            );
            $tplMng->render(
                'parts/packages/filters/section_installer',
                ['template' => $manual_template]
            );
            ?>

            <div class="dup-button-footer">
                <input
                    type="button"
                    value="<?php esc_attr_e("Reset", 'duplicator') ?>"
                    class="button hollow secondary small" <?php echo ($validation->isValid()) ? '' : 'disabled="disabled"'; ?>
                    onClick="DupliJs.Pack.ResetSettings()">&nbsp;
                <input
                    id="button-next"
                    type="submit"
                    value="<?php esc_attr_e("Next", 'duplicator') ?> &#9654;"
                    class="button primary small" <?php echo ($validation->isValid()) ? '' : 'disabled="disabled"'; ?>>
            </div>
        </div>
        <div class="dupli-setup-side">
            <?php
            $tplMng->render(
                'parts/packages/security_notes_box',
                [
                    'secureOn'   => $manual_template->installer_opts_secure_on,
                    'securePass' => $manual_template->installerPassowrd,
                    'notes'      => $manual_template->notes,
                ]
            );
            ?>
        </div>
    </div>
</form>

<!-- CACHE PROTECTION: If the back-button is used from the scanner page then we need to
refresh page in-case any filters where set while on the scanner page -->
<form id="cache_detection">
    <input type="hidden" id="cache_state" name="cache_state" value="" />
</form>
<?php
$confirm1               = new UiDialog();
$confirm1->title        = __('Would you like to continue', 'duplicator');
$confirm1->message      = __('This will clear all of the current backup settings.', 'duplicator');
$confirm1->progressText = __('Please Wait...', 'duplicator');
$confirm1->jsCallback   = 'DupliJs.Pack.ResetSettingsRun()';
$confirm1->initConfirm();
?>
<script>
    jQuery(function($) {
        DupliJs.Pack.BeforeSubmit = function(e) {
            $('#mu-exclude option').each(function() {
                $(this).prop('selected', true);
            });

            DupliJs.Pack.FillExcludeTablesList();

            return true;
        };

        $('#dup-form-opts').submit(function() {
            return DupliJs.Pack.BeforeSubmit();
        })

        DupliJs.Pack.ResetSettings = function() {
            <?php $confirm1->showConfirm(); ?>
        };

        DupliJs.Pack.ResetSettingsRun = function() {
            $('#dup-form-opts')[0].reset();
            setTimeout(function() {
                tb_remove();
            }, 800);
        }
    });

    //INIT
    jQuery(document).ready(function($) {
        DupliJs.Pack.checkPageCache = function() {
            var $state = $('#cache_state');
            if ($state.val() == "") {
                $state.val("fresh-load");
            } else {
                $state.val("cached");
                <?php $redirect = PackagesPageController::getInstance()->getPackageBuildS1Url(); ?>
                window.location.href = '<?php echo esc_js($redirect); ?>';
            }
        }

        // Default EnableTemplate — may be overridden by addons
        if (typeof DupliJs.Pack.EnableTemplate !== 'function') {
            DupliJs.Pack.EnableTemplate = function() {
                DupliJs.EnableInstallerPassword();
                DupliJs.Pack.ToggleFileFilters();
                DupliJs.Pack.ToggleDBFilters();
                DupliJs.Pack.ToggleActiveThemes();
                DupliJs.Pack.ToggleActivePlugins();
                DupliJs.Pack.ToggleDBExcluded();
                DupliJs.Pack.ToggleNoPrefixTables(false);
                DupliJs.Pack.ToggleNoSubsiteExistsTables(false);
            }
        }

        DupliJs.Pack.checkPageCache();
        DupliJs.Pack.EnableTemplate();
    });
</script>
