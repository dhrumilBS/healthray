<?php



defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Variables
 *
 * @var string $bodyClasses
 */

dupxTplRender('pages-parts/page-header', [
    'paramView'   => 'step4',
    'bodyId'      => 'page-step4',
    'bodyClasses' => $bodyClasses,
]);
?>
<div id="content-inner">
    <?php dupxTplRender('pages-parts/step4/step-title'); ?>
    <div id="main-content-wrapper" >
        <?php dupxTplRender('pages-parts/step4/main'); ?>
    </div>
    <?php
    dupxTplRender('parts/ajax-error');
    dupxTplRender('parts/progress-bar');
    ?>
</div>
<?php
dupxTplRender('scripts/step4-init');
dupxTplRender('pages-parts/page-footer');
