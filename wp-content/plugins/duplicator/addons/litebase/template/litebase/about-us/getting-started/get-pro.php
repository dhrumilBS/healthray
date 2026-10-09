<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$upgradeUrl      = $tplMng->getDataValueString('upgradeUrl');
$discountPercent = $tplMng->getDataValueInt('discountPercent');
/** @var string[] $features */
$features = $tplMng->getDataValueArray('features');

$upgradeStrong = '<strong>' . esc_html__('Upgrade to Duplicator Pro', 'duplicator') . '</strong>';
$ratingStrong  = '<strong>' . esc_html__('4000+ five star ratings', 'duplicator') . '</strong>';
$starsHtml     = str_repeat('<i class="fa fa-star" aria-hidden="true"></i>', 5);
$bonusStrong   = '<strong>' . esc_html__('Bonus:', 'duplicator') . '</strong>';
$discountHtml  = '<span class="green">' . esc_html(sprintf(
    /* translators: %d - discount percentage. */
    __('%d%% off regular price', 'duplicator'),
    $discountPercent
)) . '</span>';
?>
<div class="dupli-litebase-about-section dupli-litebase-about-section-hero">
    <div class="dupli-litebase-about-section-hero-main">
        <h2><?php esc_html_e('Get Duplicator Pro and Unlock all the Powerful Features', 'duplicator'); ?></h2>
        <p class="bigger">
            <?php
            echo wp_kses_post(sprintf(
                /* translators: %s - "Upgrade to Duplicator Pro" emphasised text. */
                __(
                    'Thanks for being a loyal Duplicator Lite user. %s to unlock all the awesome features
                    and experience why Duplicator is consistently rated the best WordPress migration plugin.',
                    'duplicator'
                ),
                $upgradeStrong
            ));
            ?>
        </p>
        <p>
            <?php
            echo wp_kses_post(sprintf(
                /* translators: %1$s - "4000+ five star ratings" emphasised text, %2$s - star icons. */
                __('We know that you will truly love Duplicator. It has over %1$s (%2$s) and is active on over 1 million websites.', 'duplicator'),
                $ratingStrong,
                $starsHtml
            ));
            ?>
        </p>
    </div>
    <div class="dupli-litebase-about-section-hero-extra">
        <div class="dupli-litebase-about-section-features">
            <ul class="list">
                <?php foreach ($features as $feature) : ?>
                    <li class="item"><span><?php echo esc_html($feature); ?></span></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <hr/>
        <h3 class="call-to-action margin-bottom-1">
            <a href="<?php echo esc_url($upgradeUrl); ?>" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('Get Duplicator Pro Today and Unlock all the Powerful Features', 'duplicator'); ?>
            </a>
        </h3>
        <?php if ($discountPercent > 0) : ?>
            <p>
                <?php
                echo wp_kses_post(sprintf(
                    /* translators: %1$s - "Bonus:" emphasised label, %2$s - discount percentage block. */
                    __('%1$s Duplicator Lite users get %2$s, automatically applied at checkout.', 'duplicator'),
                    $bonusStrong,
                    $discountHtml
                ));
                ?>
            </p>
        <?php endif; ?>
    </div>
</div>
