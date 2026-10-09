<?php

/**
 * LiteBase email summary footer block, rendered at the bottom of the
 * email summary via the `duplicator_email_summary_extra_sections` hook.
 *
 * @package Duplicator
 */

defined('ABSPATH') || exit;

use Duplicator\Utils\Email\EmailHelper;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$upgradeUrl = $tplMng->getDataValueStringRequired('upgradeUrl');

$buttonStyle = 'display: inline-block; padding: 12px 24px; margin-top: 10px;'
    . ' background-color: #e27730; color: #ffffff; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;'
    . ' font-size: 14px; font-weight: bold; text-decoration: none; border-radius: 3px;';
?>
<p <?php EmailHelper::printStyle('p subtitle txt-center'); ?>>
    <strong <?php EmailHelper::printStyle('strong'); ?>>
        <?php esc_html_e('To unlock scheduled backups, remote storages and many other features, upgrade to PRO!', 'duplicator'); ?>
    </strong>
</p>
<p <?php EmailHelper::printStyle('p txt-center'); ?>>
    <a href="<?php echo esc_url($upgradeUrl); ?>" style="<?php echo esc_attr($buttonStyle); ?>">
        <?php esc_html_e('Upgrade to PRO', 'duplicator'); ?>
    </a>
</p>
