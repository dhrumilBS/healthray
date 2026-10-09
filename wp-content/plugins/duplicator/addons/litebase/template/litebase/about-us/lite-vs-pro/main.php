<?php

use Duplicator\Addons\LiteBase\Controllers\AboutUsPageController;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$upgradeUrl      = $tplMng->getDataValueString('upgradeUrl');
$discountPercent = $tplMng->getDataValueInt('discountPercent');
/** @var array<int, array{title: string, lite: string, liteText?: string, proText?: string}> $features */
$features = $tplMng->getDataValueArray('features');

$liteStrong = '<strong>' . esc_html__('Lite', 'duplicator') . '</strong>';
$proStrong  = '<strong>' . esc_html__('Pro', 'duplicator') . '</strong>';

$bonusStrong  = '<strong>' . esc_html__('Bonus:', 'duplicator') . '</strong>';
$discountHtml = '<span class="green">' . esc_html(sprintf(
    /* translators: %d - discount percentage. */
    __('%d%% off regular price', 'duplicator'),
    $discountPercent
)) . '</span>';
?>
<div id="dupli-litebase-about">
    <div class="dupli-litebase-about-section dupli-litebase-about-section-squashed">
        <h1 class="centered">
            <?php
            echo wp_kses_post(sprintf(
                /* translators: %1$s - "Lite" emphasised label, %2$s - "Pro" emphasised label. */
                __('%1$s vs %2$s', 'duplicator'),
                $liteStrong,
                $proStrong
            ));
            ?>
        </h1>
        <p class="centered">
            <?php esc_html_e('Get the most out of Duplicator by upgrading to Pro and unlocking all of the powerful features.', 'duplicator'); ?>
        </p>
    </div>

    <div class="dupli-litebase-about-section dupli-litebase-about-section-squashed dupli-litebase-about-section-table">
        <table class="dupli-litebase-about-comparison">
            <thead>
                <tr>
                    <th><?php esc_html_e('Feature', 'duplicator'); ?></th>
                    <th><?php esc_html_e('Lite', 'duplicator'); ?></th>
                    <th><?php esc_html_e('Pro', 'duplicator'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $liteIconMap = [
                    AboutUsPageController::LITE_FULL    => 'fa-check-circle',
                    AboutUsPageController::LITE_PARTIAL => 'fa-minus-circle',
                    AboutUsPageController::LITE_NONE    => 'fa-times-circle',
                ];
                foreach ($features as $feature) :
                    $liteState = $feature['lite'];
                    $liteIcon  = $liteIconMap[$liteState] ?? 'fa-times-circle';
                    ?>
                    <tr>
                        <td class="feature-title"><?php echo esc_html($feature['title']); ?></td>
                        <td class="feature-lite features-<?php echo esc_attr($liteState); ?>">
                            <i class="fa <?php echo esc_attr($liteIcon); ?>" aria-hidden="true"></i>
                            <span>
                                <?php
                                if (isset($feature['liteText'])) {
                                    echo esc_html($feature['liteText']);
                                } elseif ($liteState === AboutUsPageController::LITE_FULL) {
                                    esc_html_e('Included', 'duplicator');
                                } else {
                                    esc_html_e('Not Available', 'duplicator');
                                }
                                ?>
                            </span>
                        </td>
                        <td class="feature-pro features-full">
                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                            <span>
                                <?php echo isset($feature['proText']) ? esc_html($feature['proText']) : esc_html__('Included', 'duplicator'); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="dupli-litebase-about-section dupli-litebase-about-section-hero">
        <div class="dupli-litebase-about-section-hero-main no-border">
            <h3 class="call-to-action centered margin-bottom-1">
                <a href="<?php echo esc_url($upgradeUrl); ?>" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('Get Duplicator Pro Today and Unlock all the Powerful Features', 'duplicator'); ?>
                </a>
            </h3>
            <?php if ($discountPercent > 0) : ?>
                <p class="centered">
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
</div>
