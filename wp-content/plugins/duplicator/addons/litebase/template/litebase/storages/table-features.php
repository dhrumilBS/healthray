<?php

defined("ABSPATH") || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$upgradeUrl = $tplMng->getDataValueStringRequired('upgradeUrl');
/** @var array<int, array{img:string,name:string}> $providers */
$providers = $tplMng->getDataValueArray('providers');
?>
<div class="dupli-storages-features">
    <strong class="dupli-storages-features-text">
        <?php esc_html_e('Need more storage options?', 'duplicator'); ?>
    </strong>
    <span class="dupli-storages-features-icons">
        <?php foreach ($providers as $provider) : ?>
            <img src="<?php echo esc_url($provider['img']); ?>"
                 alt="<?php echo esc_attr($provider['name']); ?>"
                 title="<?php echo esc_attr($provider['name']); ?>"
                 class="dupli-storages-features-icon">
        <?php endforeach; ?>
    </span>
    <a href="<?php echo esc_url($upgradeUrl); ?>"
       class="button hollow small margin-bottom-0 dupli-litebase-upgrade-btn"
       target="_blank" rel="noopener noreferrer">
        <?php esc_html_e('Upgrade to Pro', 'duplicator'); ?>
    </a>
</div>
