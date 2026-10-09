<?php



defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$archiveConfig = DUPX_ArchiveConfig::getInstance();
?>
<span class="dup-version-header-link">
    <a href="javascript:void(0)" onclick="DUPX.openServerDetails()">version:<?php echo $archiveConfig->version_dup; ?></a>&nbsp;
    <?php DUPX_View_Funcs::helpLockLink(); ?>
</span>
<?php
dupxTplRender('pages-parts/head/server-details');
