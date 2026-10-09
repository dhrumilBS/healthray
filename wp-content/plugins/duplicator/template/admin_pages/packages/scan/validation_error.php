<?php

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Package\InputValidator;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$validator = $tplMng->getDataValueObjRequired('validator', InputValidator::class);
?>

<form 
    id="form-duplicator scan-result" 
    method="post"
    action="<?php echo esc_attr(ControllersManager::getMenuLink(ControllersManager::PACKAGES_SUBMENU_SLUG)); ?>"
>
    <!--  ERROR MESSAGE -->
    <div id="dup-msg-error">
        <div class="dup-hdr-error"><i class="fa fa-exclamation-circle"></i> <?php esc_html_e('Input fields not valid', 'duplicator'); ?></div>
        <i><?php esc_html_e('Please try again!', 'duplicator'); ?></i><br/>
        <div style="text-align:left">
            <b><?php esc_html_e("Server Status:", 'duplicator'); ?></b> &nbsp;
            <div id="dup-msg-error-response-status" style="display:inline-block"></div><br/>
            <b><?php esc_html_e("Error Message:", 'duplicator'); ?></b>
            <div id="dup-msg-error-response-text">
                <ul>
                    <?php $validator->getErrorsFormat("<li>%s</li>"); ?>
                </ul>
            </div>
        </div>
    </div>
    <input
        type="button"
        value="&#9664; <?php esc_html_e("Back", 'duplicator') ?>"
        class="button hollow secondary dup-go-back-to-new1"
    >
</form>
