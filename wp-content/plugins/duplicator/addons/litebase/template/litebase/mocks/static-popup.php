<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$title       = $tplMng->getDataValueStringRequired('title');
$warningText = $tplMng->getDataValueStringRequired('warningText');
$upgradeUrl  = $tplMng->getDataValueStringRequired('upgradeUrl');
$paragraphs  = $tplMng->getDataValueArrayRequired('paragraphs');
if (!is_array($paragraphs)) {
    $paragraphs = [(string) $paragraphs];
}
?>
<div class="dupli-litebase-static-popup">
    <div class="dupli-litebase-static-popup-notice">
        <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
        <?php echo esc_html($warningText); ?>
    </div>
    <div class="dupli-litebase-static-popup-content">
        <h2><?php echo esc_html($title); ?></h2>
        <?php foreach ($paragraphs as $paragraph) : ?>
            <?php if ($paragraph !== '') : ?>
                <p><?php echo wp_kses_post($paragraph); ?></p>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <div class="dupli-litebase-static-popup-cta">
        <a
            href="<?php echo esc_url($upgradeUrl); ?>"
            class="button large margin-bottom-0 dupli-litebase-upgrade-btn"
            target="_blank"
            rel="noopener noreferrer"
        >
            <?php esc_html_e('Upgrade to Duplicator Pro Now', 'duplicator'); ?>
        </a>
    </div>
</div>
