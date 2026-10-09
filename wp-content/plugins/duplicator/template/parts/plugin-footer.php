<?php

/**
 * Plugin footer rendered inside #wpfooter on every Duplicator admin page.
 *
 * @package Duplicator
 */

defined("ABSPATH") || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng                   $tplMng
 */

$links      = $tplMng->getDataValueArray('links');
$linksCount = count($links);
$socialUrl  = DUPLICATOR_IMG_URL . '/social/';
?>
<div class="dup-styles dup-plugin-footer">
    <p class="dup-plugin-footer-tagline">
        <?php esc_html_e('Made with ♥ by the Duplicator Team', 'duplicator'); ?>
    </p>
    <?php if ($linksCount > 0) : ?>
        <ul class="dup-plugin-footer-links">
            <?php foreach ($links as $i => $item) : ?>
                <li>
                    <a href="<?php echo esc_url($item['url']); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html($item['label']); ?>
                    </a>
                    <?php if ($i < $linksCount - 1) : ?>
                        <span aria-hidden="true">/</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <ul class="dup-plugin-footer-social">
        <li>
            <a href="https://www.facebook.com/snapcreek/" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                <img src="<?php echo esc_url($socialUrl . 'facebook.svg'); ?>" alt="" width="16" height="16" />
            </a>
        </li>
        <li>
            <a href="https://x.com/duplicatorwp" target="_blank" rel="noopener noreferrer" aria-label="X">
                <img src="<?php echo esc_url($socialUrl . 'x.svg'); ?>" alt="" width="16" height="16" />
            </a>
        </li>
        <li>
            <a href="https://www.youtube.com/c/Snapcreek" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                <img src="<?php echo esc_url($socialUrl . 'youtube.svg'); ?>" alt="" width="17" height="16" />
            </a>
        </li>
    </ul>
</div>
