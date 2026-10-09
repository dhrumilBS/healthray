<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$quickStartUrl = $tplMng->getDataValueString('quickStartUrl');
$backupDocUrl  = $tplMng->getDataValueString('backupDocUrl');
$migrateDocUrl = $tplMng->getDataValueString('migrateDocUrl');
?>
<div class="dupli-litebase-about-section dupli-litebase-about-section-first-form">
    <div class="dupli-litebase-about-section-first-form-text">
        <h2><?php esc_html_e('Creating Your First Backup', 'duplicator'); ?></h2>
        <p>
            <?php esc_html_e(
                'Want to get started creating your first Backup with Duplicator? By following the step by step instructions in this
                walkthrough, you can easily create a backup or migration.',
                'duplicator'
            ); ?>
        </p>
        <p>
            <?php esc_html_e(
                'To begin, you\'ll need to be logged into the WordPress admin area. Once there, click on Duplicator in the admin sidebar
                to go to the Backups page.',
                'duplicator'
            ); ?>
        </p>
        <p>
            <?php esc_html_e(
                'In the Backups page, the Backups list will be empty because there are no Backups yet. To create a new Backup, click on
                the Create New button, and this will launch the Backup Creation Wizard.',
                'duplicator'
            ); ?>
        </p>
        <ul class="dupli-litebase-about-list-plain">
            <li>
                <a href="<?php echo esc_url($quickStartUrl); ?>" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('Quick Start Guide', 'duplicator'); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url($backupDocUrl); ?>" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('How to Create a Backup', 'duplicator'); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo esc_url($migrateDocUrl); ?>" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('How to Migrate to a New Site', 'duplicator'); ?>
                </a>
            </li>
        </ul>
    </div>
</div>
