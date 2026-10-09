<?php

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

if (!$tplMng->dataValueExists('redetectRan')) {
    return;
}

$messageClasses = [
    'notice',
    'dupli-admin-notice',
    'is-dismissible',
    'dupli-diagnostic-action-redetect-server',
];

if (!$tplMng->getDataValueBool('redetectRan')) { ?>
    <div id="message" class="<?php echo esc_attr(implode(' ', array_merge($messageClasses, ['notice-warning']))); ?>">
        <p>
            <?php esc_html_e(
                'Server detection tests skipped: a backup is currently in progress. Try again when no backup is running.',
                'duplicator'
            ); ?>
        </p>
    </div>
    <?php
    return;
}

$lockSql           = $tplMng->getDataValueBool('redetectLockSql');
$lockFile          = $tplMng->getDataValueBool('redetectLockFile');
$loopbackPass      = $tplMng->getDataValueBool('redetectLoopbackPass');
$clientSideKickoff = $tplMng->getDataValueBool('redetectClientSideKickoff');
$kickoffOverride   = $tplMng->getDataValueString('redetectKickoffOverride', 'auto');
$lockMismatch      = !$lockSql && !$lockFile;
$kickoffMismatch   = $kickoffOverride === 'server' && !$loopbackPass;

$isWarning        = $lockMismatch || !$loopbackPass;
$messageClasses[] = ($isWarning ? 'notice-warning' : 'notice-success');
?>
<div id="message" class="<?php echo esc_attr(implode(' ', $messageClasses)); ?>">
    <?php $tplMng->render(
        'admin_pages/settings/backup/detection_result_message',
        [
            'messageTitle'        => __('Server detection tests completed.', 'duplicator'),
            'showProcessSettings' => false,
            'sqlLockPassed'       => $lockSql,
            'fileLockPassed'      => $lockFile,
            'clientSideKickoff'   => $clientSideKickoff,
            'kickoffOverride'     => $kickoffOverride,
            'kickoffMismatch'     => $kickoffMismatch,
            'lockMismatch'        => $lockMismatch,
        ]
    ); ?>
</div>
