<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\Controllers\ControllersManager;

$blur             = $tplMng->getDataValueBool('blur');
$package_list_url = ControllersManager::getMenuLink(ControllersManager::PACKAGES_SUBMENU_SLUG);
?>
<form 
    id="form-duplicator" 
    class="<?php echo ($blur ? 'dup-mock-blur' : ''); ?>  scan-result" 
    method="post" 
    action="<?php echo esc_attr($package_list_url); ?>"
>
    <?php PackagesPageController::getInstance()->getActionByKey(PackagesPageController::ACTION_CREATE_FROM_TEMP)->getActionNonceFileds(); ?>
    <?php do_action('duplicator_scan_header'); ?>
    <div id="dup-progress-area">
        <!--  PROGRESS BAR -->
        <div class="dup-progress-bar-area">
            <div class="dupli-title" >
                <?php esc_html_e('Scanning Site', 'duplicator'); ?>
            </div>
            <div class="dupli-meter-wrapper" >
                <div class="dupli-meter green dupli-fullsize">
                    <span></span>
                </div>
                <span class="text"></span>
            </div>
            <b><?php esc_html_e('Please Wait...', 'duplicator'); ?></b><br/><br/>
            <i><?php esc_html_e('Keep this window open during the scan process.', 'duplicator'); ?></i><br/>
            <i><?php esc_html_e('This can take several minutes.', 'duplicator'); ?></i><br/>
        </div>

        <!--  SCAN DETAILS REPORT -->
        <div id="dup-msg-success" style="display:none">
            <div style="text-align:center">
                <div class="dup-hdr-success">
                    <i class="far fa-check-square fa-nr"></i> <?php esc_html_e('Scan Complete', 'duplicator'); ?>
                </div>
                <div id="dup-msg-success-subtitle">
                    <?php esc_html_e("Process Time:", 'duplicator'); ?> <span id="data-rpt-scantime"></span>
                </div>
            </div>
            <div class="details">
                <?php $tplMng->render('admin_pages/packages/scan/items/setup/main'); ?>
                <br/>
                <?php $tplMng->render('admin_pages/packages/scan/items/archive/main'); ?>
                <?php $tplMng->render('admin_pages/packages/scan/items/database/main'); ?>
                <?php do_action('duplicator_scan_report_footer'); ?>
            </div>
        </div>

        <!--  ERROR MESSAGE -->
        <div id="dup-msg-error" style="display:none">
            <div id="dupli-msg-error-default">
                <div class="dup-hdr-error">
                    <i class="fa fa-exclamation-circle"></i>
                    <span id="dup-msg-error-title"><?php esc_html_e('Scan Error', 'duplicator'); ?></span>
                </div>
                <i id="dup-msg-error-subtitle"><?php esc_html_e('Please try again!', 'duplicator'); ?></i><br/>
                <div style="text-align:left">
                    <b><?php esc_html_e("Server Status:", 'duplicator'); ?></b> &nbsp;
                    <div id="dup-msg-error-response-status" style="display:inline-block"></div><br/>
                    <b><?php esc_html_e("Error Message:", 'duplicator'); ?></b>
                    <div id="dup-msg-error-response-text"></div>
                </div>
            </div>
            <div
                id="dupli-msg-error-fix"
                class="dupli-fix-group-inline dupli-notice-icon-warning-wrapper no-display"
            ></div>
        </div>
    </div>

    <!-- WARNING CONTINUE -->
    <div id="dupli-scan-warning-continue">
        <div class="msg2">
            <?php esc_html_e("Scan checks are not required to pass, however they could cause issues on some systems.", 'duplicator'); ?>
            <br/>
            <?php esc_html_e("Please review the details for each section by clicking on the detail title.", 'duplicator'); ?>
        </div>
    </div>

    <div id="dupli-confirm-area">
        <?php esc_html_e('Do you want to continue?', 'duplicator'); ?>
        <br/>
        <?php esc_html_e('At least one or more checkboxes were checked in "Quick Filters".', 'duplicator') ?>
        <br/>
        <i style="font-weight:normal">
            <?php esc_html_e('To apply a "Quick Filter" click the "Add Filters & Rescan" button', 'duplicator') ?>
        </i><br/>
        <input 
            type="checkbox" 
            id="dupli-confirm-check" 
            onclick="jQuery('#dup-build-button').removeAttr('disabled');"
            class="margin-bottom-0"
        >
        <?php esc_html_e('Yes. Continue without applying any file filters.', 'duplicator') ?>
    </div>
    <div class="dup-button-footer" style="display:none">
        <input
            type="button"
            class="button hollow secondary small dup-go-back-to-new1"
            value="&#9664; <?php esc_html_e("Back", 'duplicator') ?>"
        >
        <input 
            type="button" 
            class="button hollow secondary small"
            value="<?php esc_attr_e("Rescan", 'duplicator') ?>" 
            onclick="DupliJs.Pack.reRunScanner()"
        >
        <input 
            type="button" 
            onclick="DupliJs.Pack.startBuild();" 
            class="button primary small" 
            id="dup-build-button" 
            value='<?php esc_attr_e("Create Backup", 'duplicator') ?> &#9654'
        >
    </div>
    <?php do_action('duplicator_scan_footer'); ?>
</form>
<?php $tplMng->render('admin_pages/packages/scan/scripts'); ?>
