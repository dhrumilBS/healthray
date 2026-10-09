<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$upgradeUrl      = $tplMng->getDataValueString('upgradeUrl');
$dismissNonce    = $tplMng->getDataValueString('dismissNonce');
$discountPercent = $tplMng->getDataValueInt('discountPercent');
/** @var string[] $features */
$features = $tplMng->getDataValueArray('features');
?>
<div id="dup-litebase-settings-feature-box"
     class="dupli-dismissable"
     data-dismiss-action="duplicator_settings_feature_box_dismiss"
     data-dismiss-nonce="<?php echo esc_attr($dismissNonce); ?>">
    <a href="#" class="dismiss dupli-dismissable-dismiss" title="<?php esc_attr_e('Dismiss this message', 'duplicator'); ?>">
        <i class="fa fa-times-circle" aria-hidden="true"></i>
    </a>
    <h5><?php esc_html_e('Get Duplicator Pro and Unlock all the Powerful Features', 'duplicator'); ?></h5>
    <p>
        <?php esc_html_e(
            'Thanks for being a loyal Duplicator Lite user. Upgrade to Duplicator Pro to unlock all the awesome features and 
            experience why Duplicator is consistently rated the best WordPress migration plugin.',
            'duplicator'
        ); ?>
    </p>
    <p>
        <?php
        echo wp_kses_post(sprintf(
            /* translators: %s - star icons. */
            __(
                'We know that you will truly love Duplicator. It has over 4000+ five star ratings (%s) and is active on over 1 million websites.',
                'duplicator'
            ),
            str_repeat('<i class="fa fa-star" aria-hidden="true"></i>', 5)
        ));
        ?>
    </p>
    <h6><?php esc_html_e('Pro Features:', 'duplicator'); ?></h6>
    <ul class="list">
        <?php foreach ($features as $feature) : ?>
            <li class="item"><span><?php echo esc_html($feature); ?></span></li>
        <?php endforeach; ?>
    </ul>
    <p>
        <a href="<?php echo esc_url($upgradeUrl); ?>"
           class="button primary margin-bottom-0"
           target="_blank"
           rel="noopener noreferrer">
            <?php esc_html_e('Get Duplicator Pro Today and Unlock all the Powerful Features »', 'duplicator'); ?>
        </a>
    </p>
    <?php if ($discountPercent > 0) : ?>
        <p class="bonus">
            <?php
            echo wp_kses_post(sprintf(
                /* translators: %1$d - discount percentage. */
                __(
                    '<strong>Bonus:</strong> Duplicator Lite users get <span class="green">%1$d%% off regular price</span>, automatically applied at checkout.',
                    'duplicator'
                ),
                $discountPercent
            ));
            ?>
        </p>
    <?php endif; ?>
</div>
