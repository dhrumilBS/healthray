<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

/** @var array<int, array{img:string,title:string,desc:string}> $features */
$features  = $tplMng->getDataValueArray('features');
$seeAllUrl = $tplMng->getDataValueString('seeAllUrl');
?>
<div class="dupli-litebase-welcome-features">
    <div class="dupli-litebase-welcome-block">
        <h1><?php esc_html_e('Duplicator Features', 'duplicator'); ?></h1>
        <h6>
            <?php esc_html_e(
                'Duplicator is both easy to use and extremely powerful. We have tons of helpful features
                that allow us to give you everything you need from a backup & migration plugin.',
                'duplicator'
            ); ?>
        </h6>

        <div class="dupli-litebase-welcome-feature-list">
            <?php foreach ($features as $i => $feature) :
                $position = ($i % 2 === 0) ? 'first' : 'last';
                ?>
                <div class="dupli-litebase-welcome-feature-block dupli-litebase-welcome-feature-<?php echo esc_attr($position); ?>">
                    <img src="<?php echo esc_url($feature['img']); ?>" alt="">
                    <h5><?php echo esc_html($feature['title']); ?></h5>
                    <p><?php echo esc_html($feature['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="dupli-litebase-welcome-button-wrap dupli-litebase-welcome-button-wrap--centered">
            <a href="<?php echo esc_url($seeAllUrl); ?>"
               class="button gray large margin-bottom-0"
               rel="noopener noreferrer"
               target="_blank">
                <?php esc_html_e('See All Features', 'duplicator'); ?>
            </a>
        </div>
    </div>
</div>
