<?php



defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Variables
 *
 * @var string $bodyClasses
 * @var Exception $exception
 */

dupxTplRender('pages-parts/page-header', [
    'paramView'   => 'exception',
    'bodyId'      => 'page-exception',
    'bodyClasses' => $bodyClasses,
]);
?>
<div id="content-inner">
    <?php
    dupxTplRender('pages-parts/head/header-main', ['htmlTitle' => 'Exception error']);
    ?>
    <div id="main-content-wrapper" >
        <?php
        dupxTplRender('pages-parts/exception/main', ['exception' => $exception]);
        ?>
    </div>
</div>

<?php
dupxTplRender('pages-parts/page-footer');
