<?php

use Duplicator\Addons\LiteBase\Utils\ExtraPlugins\ExtraItem;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

/** @var ExtraItem $plugin */
$plugin = $tplMng->getDataValueObjRequired('plugin', ExtraItem::class);

switch ($plugin->getStatus()) {
    case ExtraItem::STATUS_ACTIVE:
        $buttonLabel = __('Activated', 'duplicator');
        $buttonClass = 'button primary small margin-bottom-0 disabled';
        $statusClass = 'status-active';
        break;
    case ExtraItem::STATUS_INSTALLED:
        $buttonLabel = __('Activate', 'duplicator');
        $buttonClass = 'button secondary small margin-bottom-0';
        $statusClass = 'status-installed';
        break;
    case ExtraItem::STATUS_NOT_INSTALLED:
    default:
        $buttonLabel = __('Install Plugin', 'duplicator');
        $buttonClass = 'button primary small margin-bottom-0';
        $statusClass = 'status-missing';
        break;
}
?>
<div class="addons-container">
    <div class="addon-item">
        <div class="details">
            <img src="<?php echo esc_url($plugin->getIcon()); ?>"
                 alt="<?php echo esc_attr($plugin->getName()); ?> logo">
            <h5><?php echo esc_html($plugin->getName()); ?></h5>
            <p><?php echo esc_html($plugin->getDesc()); ?></p>
        </div>
        <div class="actions">
            <div class="status">
                <strong>
                    <?php esc_html_e('Status:', 'duplicator'); ?>
                    <span class="status-label <?php echo esc_attr($statusClass); ?>">
                        <?php echo esc_html($plugin->getStatusText()); ?>
                    </span>
                </strong>
            </div>
            <div class="action-button">
                <?php if ($plugin->getURLType() === ExtraItem::URL_TYPE_GENERIC) : ?>
                    <a href="<?php echo esc_url($plugin->getUrl()); ?>"
                       title="<?php echo esc_attr($buttonLabel); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="<?php echo esc_attr($buttonClass); ?>">
                        <?php echo esc_html($buttonLabel); ?>
                    </a>
                <?php else : ?>
                    <button class="<?php echo esc_attr($buttonClass); ?> dupli-litebase-extra-plugin-item"
                            data-plugin="<?php echo esc_attr($plugin->getSlug()); ?>">
                        <?php echo esc_html($buttonLabel); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
