<?php

use Duplicator\Addons\LiteBase\Notifications\DashboardRecommendedPlugin;
use Duplicator\Addons\LiteBase\Utils\ExtraPlugins\ExtraItem;
use Duplicator\Core\CapMng;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$plugin       = $tplMng->getDataValueObjRequired('plugin', ExtraItem::class);
$dismissNonce = $tplMng->getDataValueStringRequired('dismissNonce');
$slugFolder   = dirname($plugin->getSlug());
$installUrl   = wp_nonce_url(
    self_admin_url('update.php?action=install-plugin&plugin=' . rawurlencode($slugFolder)),
    'install-plugin_' . $slugFolder
);
$moreUrl      = $plugin->getWpOrgURL() !== false ? $plugin->getWpOrgURL() : $plugin->getUrl();
?>
<div class="dupli-litebase-dashboard-recommended dupli-dismissable"
     data-dismiss-action="<?php echo esc_attr(DashboardRecommendedPlugin::DISMISS_NONCE_KEY); ?>"
     data-dismiss-nonce="<?php echo esc_attr($dismissNonce); ?>">
    <hr>
    <div class="dup-flex-content">
        <div>
            <span class="dupli-litebase-dashboard-recommended-label">
                <?php esc_html_e('Recommended Plugin:', 'duplicator'); ?>
            </span>
            <b><?php echo esc_html($plugin->getName()); ?></b>
            -
            <span class="dupli-litebase-dashboard-recommended-actions">
                <?php if (CapMng::can('install_plugins', false) && CapMng::can('activate_plugins', false)) : ?>
                    <a href="<?php echo esc_url($installUrl); ?>">
                        <?php esc_html_e('Install', 'duplicator'); ?>
                    </a>
                <?php endif; ?>
                <a href="<?php echo esc_url($moreUrl); ?>" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('Learn More', 'duplicator'); ?>
                </a>
            </span>
        </div>
        <div>
            <a class="dupli-dismissable-dismiss dupli-litebase-dashboard-recommended-dismiss"
               href="#"
               title="<?php esc_attr_e('Dismiss recommended plugin', 'duplicator'); ?>">
                <span class="dashicons dashicons-no-alt"></span>
            </a>
        </div>
    </div>
</div>
