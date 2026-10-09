<?php



defined('ABSPATH') || defined('DUPXABSPATH') || exit;

 /**
 * Variables
  *
 * @var string $paramView
 * @var string $bodyId
 * @var string $bodyClasses
 */

$archiveConfig = DUPX_ArchiveConfig::getInstance();

?><!DOCTYPE html>
<html>
    <head>
        <?php dupxTplRender('pages-parts/head/meta'); ?>
        <title><?php echo $archiveConfig->header['name']; ?></title>
        <?php dupxTplRender('pages-parts/head/css-scripts'); ?>
        <?php dupxTplRender('pages-parts/head/css-template-custom'); ?>
    </head>
    <?php
    dupxTplRender('pages-parts/body/body-tag', [
        'bodyId'      => $bodyId,
        'bodyClasses' => $bodyClasses,
    ]);
    ?>
    <div id="content">
        <?php
        dupxTplRender('parts/top-header.php', ['paramView' => $paramView]);
        if (!isset($skipTopMessages) || $skipTopMessages !== true) {
            dupxTplRender('parts/top-messages.php');
        }
