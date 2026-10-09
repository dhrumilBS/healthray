<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$packageNonceUrl = $tplMng->getDataValueString('packageNonceUrl');
$upgradeFooter   = $tplMng->getDataValueString('upgradeFooter');
?>
<div class="dupli-litebase-welcome-footer">
    <div class="dupli-litebase-welcome-block">
        <div class="dupli-litebase-welcome-button-wrap">
            <a href="<?php echo esc_url($packageNonceUrl); ?>"
               class="button primary large margin-bottom-0">
                <?php esc_html_e('Create Your First Backup', 'duplicator'); ?>
            </a>
            <a href="<?php echo esc_url($upgradeFooter); ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="button large margin-bottom-0 dupli-litebase-welcome-upgrade-button">
                <?php esc_html_e('Upgrade to Duplicator Pro', 'duplicator'); ?>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
