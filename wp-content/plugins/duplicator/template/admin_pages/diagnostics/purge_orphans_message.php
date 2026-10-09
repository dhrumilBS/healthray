<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

if (!$tplMng->dataValueExists('purgeOrphansSuccess')) {
    return;
}

$purgeOrphansSuccess = $tplMng->getDataValueBool('purgeOrphansSuccess');
$purgeOrphansFiles   = $tplMng->getDataValueArray('purgeOrphansFiles');

$messageClasses = [
    'notice',
    'dupli-admin-notice',
    'is-dismissible',
    'dupli-diagnostic-action-purge-orphans',
    ($purgeOrphansSuccess ? 'notice-success' : 'notice-error'),
];
?>
<div id="message" class="<?php echo esc_attr(implode(' ', $messageClasses)); ?>">
    <p>
        <?php esc_html_e('Cleaned up orphaned Backup files!', 'duplicator'); ?>
    </p>
    <?php
    foreach ($purgeOrphansFiles as $path => $deleted) {
        if ($deleted) {
            ?>
            <div class='success'>
                <i class='fa fa-check'></i> <?php echo esc_html($path); ?>
            </div>
        <?php } else { ?>
            <div class='failed'>
                <i class='fa fa-exclamation-triangle'></i> <?php echo esc_html($path); ?>
            </div>
            <?php
        }
    }
    ?>
    <p>
        <i>
            <?php esc_html_e('If any orphaned files didn\'t get removed then delete them manually', 'duplicator') ?>. 
        </i>
    </p>
</div>
