<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng                   $tplMng
 */

$upgradeUrl      = $tplMng->getDataValueString('upgradeUrl');
$discountPercent = $tplMng->getDataValueInt('discountPercent');
?>
<div class="dup-settings-wrapper margin-bottom-1">
    <h3 class="title">
        <?php esc_html_e('License', 'duplicator'); ?>
    </h3>
    <hr size="1" />

<label class="lbl-larger">
    <?php esc_html_e('License Key', 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <p><?php esc_html_e("You're using Duplicator Lite - no license needed. Enjoy!", 'duplicator'); ?> 🙂</p>
    <p>
        <?php
        printf(
            wp_kses(
                /* translators: %1$s opening anchor, %2$s closing anchor */
                __(
                    'To unlock more features consider <strong>%1$supgrading to PRO%2$s</strong>.',
                    'duplicator'
                ),
                [
                    'a'      => [
                        'href'   => [],
                        'class'  => [],
                        'target' => [],
                        'rel'    => [],
                    ],
                    'strong' => [],
                ]
            ),
            '<a href="' . esc_url($upgradeUrl) . '" target="_blank" rel="noopener noreferrer">',
            '</a>'
        );
        ?>
    </p>
    <?php if ($discountPercent > 0) : ?>
        <p>
            <?php
            printf(
                wp_kses(
                    /* translators: %d: discount percent */
                    __(
                        'As a valued Duplicator Lite user you receive <strong>%d%% off</strong>, automatically applied at checkout!',
                        'duplicator'
                    ),
                    ['strong' => []]
                ),
                (int) $discountPercent
            );
            ?>
        </p>
    <?php endif; ?>
</div>

<label class="lbl-larger">
    <?php esc_html_e('Already Purchased?', 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <p>
        <?php
        echo wp_kses(
            __('Connect to unlock <b>Duplicator PRO!</b>', 'duplicator'),
            ['b' => []]
        );
        ?>
    </p>
    <p>
    <button type="button" class="button primary margin-bottom-0" id="dup-settings-connect-btn">
        <?php esc_html_e('Connect to Duplicator Pro', 'duplicator'); ?>
    </button>
    </p>
    <p class="description">
        <?php esc_html_e(
            "This opens connect.duplicator.com where you'll securely connect to Duplicator Pro.",
            'duplicator'
        ); ?>
    </p>
</div>
</div>
