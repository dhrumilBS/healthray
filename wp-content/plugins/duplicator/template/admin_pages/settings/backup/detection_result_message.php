<?php

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$messageTitle        = $tplMng->getDataValueString('messageTitle', __('Settings Saved', 'duplicator'));
$showProcessSettings = $tplMng->getDataValueBool('showProcessSettings', true);
$sqlLockPassed       = $tplMng->getDataValueBool('sqlLockPassed');
$fileLockPassed      = $tplMng->getDataValueBool('fileLockPassed');
$clientSideKickoff   = $tplMng->getDataValueBool('clientSideKickoff');
$kickoffOverride     = $tplMng->getDataValueString('kickoffOverride', 'auto');
$kickoffMismatch     = $tplMng->getDataValueBool('kickoffMismatch');
$lockMismatch        = $tplMng->getDataValueBool('lockMismatch');

if ($showProcessSettings) {
    $kickSettingLabel      = $tplMng->getDataValueStringRequired('kickSettingLabel');
    $ajaxSettingLabel      = $tplMng->getDataValueStringRequired('ajaxSettingLabel');
    $basicAuthSettingLabel = $tplMng->getDataValueStringRequired('basicAuthSettingLabel');
    $ajaxUrl               = $tplMng->getDataValueStringRequired('ajaxUrl');
    $basicAuthConfigured   = $tplMng->getDataValueBool('basicAuthConfigured');
    $basicAuthUser         = $tplMng->getDataValueString('basicAuthUser');
    $basicAuthTooltip      = $basicAuthConfigured
        ? __('Enabled. Duplicator attaches the configured HTTP Basic Authentication credentials to server requests.', 'duplicator')
        : __(
            'Disabled. No HTTP Basic Authentication credentials are configured. They are only needed when the site
            shows a browser login prompt before WordPress loads.',
            'duplicator'
        );
}

$sqlLockStatusClass  = $sqlLockPassed ? 'success-color' : ($lockMismatch ? 'alert-color' : 'warning-color');
$fileLockStatusClass = $fileLockPassed ? 'success-color' : ($lockMismatch ? 'alert-color' : 'warning-color');
$kickoffStatusClass  = $clientSideKickoff ? 'warning-color' : ($kickoffMismatch ? 'alert-color' : 'success-color');

if ($clientSideKickoff) {
    $kickoffTooltip = $kickoffOverride === 'client'
        ? __('Client-side. The build workers start from the browser because client-side kickoff is selected manually.', 'duplicator')
        : __('Client-side. The server cannot reach itself, so the build workers start from the browser.', 'duplicator');
} elseif ($kickoffMismatch) {
    $kickoffTooltip = __(
        'Server-side. This mode is forced manually even though the server cannot reach itself. Backup creation may fail.',
        'duplicator'
    );
} else {
    $kickoffTooltip = __('Server-side. The server starts the build workers on its own.', 'duplicator');
}

?>
<b><?php echo esc_html($messageTitle); ?></b>
<?php if ($showProcessSettings) : ?>
<br><br>
<b><?php esc_html_e('Process Settings', 'duplicator'); ?></b><br>
    <?php esc_html_e('Client-side Kickoff', 'duplicator'); ?>: <b><?php echo esc_html($kickSettingLabel); ?></b><br>
    <?php esc_html_e('Server-to-Server Ajax', 'duplicator'); ?>: <b><?php echo esc_html($ajaxSettingLabel); ?></b>
(<?php echo esc_html($ajaxUrl); ?>)<br>
    <?php esc_html_e('Password-Protected Access', 'duplicator'); ?>:
<b>
    <?php echo esc_html($basicAuthSettingLabel); ?>
    <?php if ($basicAuthConfigured) {
        printf(
            esc_html_x('(Auth Enabled, user: %s)', '%s is the basic auth username', 'duplicator'),
            esc_html($basicAuthUser)
        );
    } else {
        esc_html_e('(Auth Disabled)', 'duplicator');
    } ?>
</b>
<i class="fa-solid fa-circle-info dark-gray-color dupli-detection-info"
    data-tooltip-title="<?php esc_attr_e('HTTP Basic Authentication', 'duplicator'); ?>"
    data-tooltip="<?php echo esc_attr($basicAuthTooltip); ?>"
    data-tooltip-width="400"></i>
<?php endif; ?>
<br><br>
<b><?php esc_html_e('Detection Results', 'duplicator'); ?></b><br>
<span class="<?php echo esc_attr($sqlLockStatusClass); ?>">
    <i
        class="fa-solid <?php echo esc_attr($sqlLockPassed ? 'fa-circle-check' : 'fa-triangle-exclamation'); ?>"
        aria-hidden="true"></i>
</span>
<?php esc_html_e('SQL lock', 'duplicator'); ?>:
<b>
    <?php echo $sqlLockPassed ? esc_html__('Reliable', 'duplicator') : esc_html__('Not reliable', 'duplicator'); ?>
</b><br>
<span class="<?php echo esc_attr($fileLockStatusClass); ?>">
    <i
        class="fa-solid <?php echo esc_attr($fileLockPassed ? 'fa-circle-check' : 'fa-triangle-exclamation'); ?>"
        aria-hidden="true"></i>
</span>
<?php esc_html_e('File lock', 'duplicator'); ?>:
<b>
    <?php echo $fileLockPassed ? esc_html__('Reliable', 'duplicator') : esc_html__('Not reliable', 'duplicator'); ?>
</b><br>
<span class="<?php echo esc_attr($kickoffStatusClass); ?>">
    <i
        class="fa-solid <?php echo esc_attr($clientSideKickoff ? 'fa-triangle-exclamation' : 'fa-circle-check'); ?>"
        aria-hidden="true"></i>
</span>
<?php esc_html_e('Client-side Kickoff', 'duplicator'); ?>:
<b>
    <?php echo $clientSideKickoff ? esc_html__('Enabled', 'duplicator') : esc_html__('Disabled', 'duplicator'); ?>
</b>
<i class="fa-solid fa-circle-info dark-gray-color dupli-detection-info"
    data-tooltip-title="<?php esc_attr_e('Kickoff mode', 'duplicator'); ?>"
    data-tooltip="<?php echo esc_attr($kickoffTooltip); ?>"
    data-tooltip-width="400"></i><br>
<?php if ($lockMismatch) : ?>
    <br><br>
    <span class="alert-color">
        <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
        <b><?php esc_html_e('Warning', 'duplicator'); ?>:</b>
        <?php esc_html_e(
            'Both process lock reliability tests failed.
            Concurrent backup workers may not be prevented, which can cause overlapping writes and server overload.
            Contact support if backups misbehave on this server.',
            'duplicator'
        ); ?>
    </span>
<?php endif; ?>
<?php if ($kickoffMismatch) : ?>
    <br><br>
    <span class="alert-color">
        <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
        <b><?php esc_html_e('Warning', 'duplicator'); ?>:</b>
        <?php esc_html_e(
            'The loopback self-request test failed, but kickoff is forced to Server.
            Backup creation may not work. Keep this setting only if you are sure the server can reach itself
            or if instructed by support.',
            'duplicator'
        ); ?>
    </span>
<?php endif; ?>
