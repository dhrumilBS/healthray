<?php



defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Params\PrmMng;

/**
 * Variables
 *
 * @var DUPX_Validation_manager $validationManager
 */
?>
<div class="clearfix" >
    <?php PrmMng::getInstance()->getHtmlFormParam(PrmMng::PARAM_VALIDATION_SHOW_ALL); ?>
</div>
<?php
dupxTplRender('parts/validation/validation-category', [
    'title'    => 'General',
    'category' => DUPX_Validation_manager::CAT_GENERAL,
]);
dupxTplRender('parts/validation/validation-category', [
    'title'    => 'File System',
    'category' => DUPX_Validation_manager::CAT_FILESYSTEM,
]);
dupxTplRender('parts/validation/validation-category', [
    'title'    => 'PHP config',
    'category' => DUPX_Validation_manager::CAT_PHP,
]);
dupxTplRender('parts/validation/validation-category', [
    'title'    => 'Database',
    'category' => DUPX_Validation_manager::CAT_DATABASE,
]);

DUPX_U_Html::js();