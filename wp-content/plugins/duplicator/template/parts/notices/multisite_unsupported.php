<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$title       = $tplMng->getDataValueStringRequired('title');
$message     = $tplMng->getDataValueStringRequired('message');
$buttonUrl   = $tplMng->getDataValueString('buttonUrl');
$buttonLabel = $tplMng->getDataValueString('buttonLabel');
?>
<span class="dashicons dashicons-warning"></span>
<div class="dup-sub-content">
    <h3><?php echo esc_html($title); ?></h3>
    <p><?php echo esc_html($message); ?></p>
    <?php if ($buttonUrl !== '' && $buttonLabel !== '') : ?>
        <a class="button primary small margin-top-1 margin-bottom-0"
           target="_blank"
           rel="noopener noreferrer"
           href="<?php echo esc_url($buttonUrl); ?>">
            <?php echo esc_html($buttonLabel); ?>
        </a>
    <?php endif; ?>
</div>
