<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$storageUrl  = $tplMng->getDataValueStringRequired('storageUrl');
$settingsUrl = $tplMng->getDataValueStringRequired('settingsUrl');
?>
<span class="dashicons dashicons-warning"></span>
<div class="dup-sub-content">
    <h3><?php esc_html_e('Encrypted Settings Reset', 'duplicator'); ?></h3>
    <p>
        <?php printf(
            esc_html__(
                'The encryption key used by %s was changed or removed, so some encrypted settings (such as storage credentials) 
                could not be decrypted and were reset to their defaults.',
                'duplicator'
            ),
            esc_html(DUPLICATOR____NAME)
        ); ?>
    </p>
    <p>
        <?php esc_html_e(
            'This typically happens after migrating, cloning, or manually editing the site\'s wp-config.php file.',
            'duplicator'
        ); ?>
    </p>
    <p>
        <?php printf(
            esc_html__(
                'If anything is missing, re-enter the credentials in %1$sStorages%2$s or check the %3$sSettings%4$s page.',
                'duplicator'
            ),
            '<a href="' . esc_url($storageUrl) . '">',
            '</a>',
            '<a href="' . esc_url($settingsUrl) . '">',
            '</a>'
        ); ?>
    </p>
</div>
