<?php



defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Variables
 *
 * @var string $bodyClasses
 */

dupxTplRender('pages-parts/page-header', [
    'paramView'       => 'secure',
    'bodyId'          => 'page-secure',
    'bodyClasses'     => $bodyClasses,
    'skipTopMessages' => true,
]);
?>
<div id="content-inner">
    <?php
    dupxTplRender('pages-parts/head/header-main', ['htmlTitle' => 'Installer Security']);
    ?>
    <div id="main-content-wrapper" >
        <?php dupxTplRender('pages-parts/secure/main'); ?>
    </div>
</div>
<?php
dupxTplRender('scripts/secure-init');
dupxTplRender('pages-parts/page-footer');
