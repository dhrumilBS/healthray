<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$packageNonceUrl = $tplMng->getDataValueString('packageNonceUrl');
$imgBase         = $tplMng->getDataValueString('imgBase');
?>
<div class="dupli-litebase-welcome-intro">
    <div class="dupli-litebase-welcome-sullie">
        <img src="<?php echo esc_url($imgBase . 'willie.svg'); ?>"
             alt="<?php esc_attr_e('Willie the Duplicator mascot', 'duplicator'); ?>">
    </div>
    <div class="dupli-litebase-welcome-block">
        <h1><?php esc_html_e('Never miss an important update', 'duplicator'); ?></h1>
    </div>
    <div class="dupli-litebase-welcome-block">
        <h6>
            <?php esc_html_e(
                'Opt in to get email notifications for security & feature updates, educational content,
                and occasional offers, and to share some basic WordPress environment info. This will
                help us make the plugin more compatible with your site and better at doing what you need it to.',
                'duplicator'
            ); ?>
        </h6>
        <div class="dupli-litebase-welcome-button-wrap">
            <button id="dupli-litebase-welcome-enable-usage-stats"
                    class="button primary large margin-bottom-0">
                <?php esc_html_e('Allow & Continue', 'duplicator'); ?>
                <i class="fas fa-arrow-right"></i>
            </button>
            <a href="<?php echo esc_url($packageNonceUrl); ?>"
               class="button gray large margin-bottom-0"
               rel="noopener noreferrer">
                <?php esc_html_e('Skip', 'duplicator'); ?>
            </a>
        </div>
    </div>
    <div class="dupli-litebase-welcome-block dupli-litebase-welcome-terms-container">
        <div class="dupli-litebase-welcome-terms-toggle">
            <?php esc_html_e('This will allow Duplicator to', 'duplicator'); ?>
            <i class="fas fa-chevron-right fa-sm"></i>
        </div>
        <ul class="dupli-litebase-welcome-terms-list" style="display: none;">
            <li>
                <i class="fas fa-user"></i>
                <div>
                    <b><?php esc_html_e('View Basic Profile Info', 'duplicator'); ?></b>
                    <p>
                        <?php esc_html_e("Your WordPress user's: first & last name, and email address", 'duplicator'); ?>
                    </p>
                </div>
            </li>
            <li>
                <i class="fas fa-globe"></i>
                <div>
                    <b><?php esc_html_e('View Basic Website Info', 'duplicator'); ?></b>
                    <p>
                        <?php esc_html_e('Homepage URL & title, WP & PHP versions, and site language', 'duplicator'); ?>
                    </p>
                </div>
            </li>
            <li>
                <i class="fas fa-plug"></i>
                <div>
                    <b><?php esc_html_e('View Basic Plugin Info', 'duplicator'); ?></b>
                    <p>
                        <?php esc_html_e('Current plugin & SDK versions, and if active or uninstalled', 'duplicator'); ?>
                    </p>
                </div>
            </li>
            <li>
                <i class="fas fa-palette"></i>
                <div>
                    <b><?php esc_html_e('View Plugins & Themes List', 'duplicator'); ?></b>
                    <p>
                        <?php esc_html_e('Names, slugs, versions, and if active or not', 'duplicator'); ?>
                    </p>
                </div>
            </li>
        </ul>
    </div>
</div>
