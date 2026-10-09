<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

if (!$tplMng->dataValueExists('tmpCleanUpSuccess')) {
    return;
}

$tmpCleanUpSuccess = $tplMng->getDataValueBool('tmpCleanUpSuccess');
$messageClasses    = [
    'notice',
    'dupli-admin-notice',
    'is-dismissible',
    'dupli-diagnostic-action-tmp-cache',
    ($tmpCleanUpSuccess ? 'notice-success' : 'notice-error'),
];

?>
<div id="message" class="<?php echo esc_attr(implode(' ', $messageClasses)); ?>">
    <p>
        <?php if ($tmpCleanUpSuccess) { ?>
            <?php esc_html_e('Build cache removed.', 'duplicator'); ?>
        <?php } else { ?>
            <?php esc_html_e(
                'Build cache was not removed because Duplicator could not confirm that cleanup was safe. Make sure no Backup is active and try again.',
                'duplicator'
            ); ?>
        <?php } ?>
    </p>
</div>
