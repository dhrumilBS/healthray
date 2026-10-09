<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\GlobalEntity;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\WpUtils\WpArchiveUtils;
use Duplicator\Package\ClientSideKick;

$global  = GlobalEntity::getInstance();
$dGlobal = DynamicGlobalEntity::getInstance();

$kickoffOverride      = $dGlobal->getValString(ClientSideKick::KICKOFF_OVERRIDE_KEY);
$ajaxProtocolOverride = $dGlobal->getValString(ClientSideKick::AJAX_PROTOCOL_OVERRIDE_KEY);
$ajaxUrlOverride      = $dGlobal->getValString(ClientSideKick::AJAX_URL_OVERRIDE_KEY);

$basicAuthMode       = $tplMng->getDataValueString('basicAuthMode');
$showMismatchWarning = $tplMng->getDataValueBool('showMismatchWarning');
$detectedAuthUser    = $tplMng->getDataValueString('detectedAuthUser');
$savedAuthUser       = $tplMng->getDataValueString('savedAuthUser');
$savedAuthPass       = $tplMng->getDataValueString('savedAuthPass');

// Auto status text: the live-detected user, falling back to the stored one the
// detection keeps in sync (what the requests will actually send).
$autoAuthUser = ($detectedAuthUser !== '') ? $detectedAuthUser : $savedAuthUser;

?>

<div style="margin-top: 30px;"></div>

<h3 class="title">
    <?php esc_html_e("Server Detection", 'duplicator'); ?>
</h3>
<hr size="1" />

<label class="lbl-larger">
    <?php esc_html_e('Client-side Kickoff', 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <input type="radio" name="override_kickoff" id="kickoff_auto" class="margin-0"
        value="auto" <?php checked($kickoffOverride, 'auto'); ?>>
    <label for="kickoff_auto"><?php esc_html_e("Auto", 'duplicator'); ?></label>&nbsp;
    <input type="radio" name="override_kickoff" id="kickoff_server" class="margin-0"
        value="server" <?php checked($kickoffOverride, 'server'); ?>>
    <label for="kickoff_server"><?php esc_html_e("Disabled", 'duplicator'); ?></label>&nbsp;
    <input type="radio" name="override_kickoff" id="kickoff_client" class="margin-0"
        value="client" <?php checked($kickoffOverride, 'client'); ?>>
    <label for="kickoff_client"><?php esc_html_e("Enabled", 'duplicator'); ?></label>
    <p class="description">
        <?php esc_html_e('Auto uses the loopback detection result. Override only if instructed by support.', 'duplicator'); ?>
    </p>
</div>

<label class="lbl-larger">
    <?php esc_html_e('Server-to-Server Ajax', 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <input type="radio" name="override_ajax_protocol" id="ajax_proto_auto" class="ajax_protocol margin-0"
        value="auto" <?php checked($ajaxProtocolOverride, 'auto'); ?>>
    <label for="ajax_proto_auto"><?php esc_html_e("Auto", 'duplicator'); ?></label>&nbsp;
    <input type="radio" name="override_ajax_protocol" id="ajax_proto_http" class="ajax_protocol margin-0"
        value="http" <?php checked($ajaxProtocolOverride, 'http'); ?>>
    <label for="ajax_proto_http"><?php esc_html_e("HTTP", 'duplicator'); ?></label>&nbsp;
    <input type="radio" name="override_ajax_protocol" id="ajax_proto_https" class="ajax_protocol margin-0"
        value="https" <?php checked($ajaxProtocolOverride, 'https'); ?>>
    <label for="ajax_proto_https"><?php esc_html_e("HTTPS", 'duplicator'); ?></label>&nbsp;
    <input type="radio" name="override_ajax_protocol" id="ajax_proto_custom" class="ajax_protocol margin-0"
        value="custom" <?php checked($ajaxProtocolOverride, 'custom'); ?>>
    <label for="ajax_proto_custom"><?php esc_html_e("Custom URL", 'duplicator'); ?></label>
    <br />
    <input type="<?php echo ($ajaxProtocolOverride === 'custom' ? 'text' : 'hidden'); ?>"
        id="override_ajax_url" name="override_ajax_url" class="width-xlarge"
        placeholder="<?php esc_attr_e('Consult support before changing.', 'duplicator'); ?>"
        value="<?php echo esc_attr($ajaxUrlOverride); ?>">
    <p class="description">
        <?php esc_html_e(
            'Backend URL used by the server to call itself and chain build steps (not the browser AJAX URL).
            Auto uses the scheme proven by the loopback test. Only change if instructed by support.',
            'duplicator'
        ); ?>
    </p>
</div>

<label class="lbl-larger">
    <?php esc_html_e("Password-Protected Access", 'duplicator'); ?>
    <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
        data-tooltip-title="<?php esc_attr_e("HTTP Basic authentication", 'duplicator'); ?>"
        data-tooltip="<?php esc_attr_e(
            "When HTTP Basic authentication is applied Duplicator needs to attach the login credentials to each request to the server.
                This is required for the build process to work properly. If you see a browser popup login window when accessing the admin area,
                then basic authentication should be enabled. If you are not sure, please consult your hosting provider.",
            'duplicator'
        ); ?>"></i>
</label>
<?php if ($showMismatchWarning) : ?>
    <div class="notice notice-warning inline dupli-notice-inline-gap">
        <p>
            <?php
            printf(
                esc_html_x(
                    'Detected credentials differ from saved ones. Detected user: %1$s, Saved user: %2$s.
                    If builds are failing, consider updating the saved credentials.',
                    '%1$s is the detected username, %2$s is the saved username',
                    'duplicator'
                ),
                '<code>' . esc_html($detectedAuthUser) . '</code>',
                '<code>' . esc_html($savedAuthUser) . '</code>'
            );
            ?>
        </p>
    </div>
<?php endif; ?>
<div class="margin-bottom-1">
    <input type="radio" name="override_basic_auth" id="basic_auth_auto" class="margin-0"
        value="auto" <?php checked($basicAuthMode, 'auto'); ?>>
    <label for="basic_auth_auto"><?php esc_html_e("Auto", 'duplicator'); ?></label>&nbsp;
    <input type="radio" name="override_basic_auth" id="basic_auth_custom" class="margin-0"
        value="custom" <?php checked($basicAuthMode, 'custom'); ?>>
    <label for="basic_auth_custom"><?php esc_html_e("Custom", 'duplicator'); ?></label>
    <div id="dup-basic-auth-auto-status" class="<?php echo ($basicAuthMode === 'custom' ? 'no-display' : ''); ?>">
        <?php if ($autoAuthUser !== '') {
            printf(
                esc_html_x(
                    'Basic authentication enabled with user %s.',
                    '%s is the detected basic auth username',
                    'duplicator'
                ),
                '<b>' . esc_html($autoAuthUser) . '</b>'
            );
        } else {
            esc_html_e('No basic authentication detected.', 'duplicator');
        } ?>
    </div>
    <div id="dup-basic-auth-login-wrapper" class="<?php echo ($basicAuthMode === 'custom' ? '' : 'no-display'); ?>">
        <input
            autocomplete="off"
            placeholder="<?php esc_attr_e('User', 'duplicator'); ?>"
            type="text"
            name="basic_auth_user"
            id="basic_auth_user"
            class="margin-0"
            value="<?php echo esc_attr($savedAuthUser); ?>">
        <span class="dup-password-toggle">
            <input
                autocomplete="off"
                placeholder="<?php esc_attr_e('Password', 'duplicator'); ?>"
                type="password"
                name="basic_auth_password"
                id="basic_auth_password"
                class="margin-0"
                value="<?php echo esc_attr($savedAuthPass); ?>">
            <button type="button">
                <i class="fas fa-eye fa-sm"></i>
            </button>
        </span>
    </div>
    <p class="description">
        <?php esc_html_e(
            'Auto detects the basic auth credentials from the server and keeps them in sync.
            Select Custom to enter them manually; leave both fields empty if no basic auth is set.',
            'duplicator'
        ); ?>
    </p>
</div>

    <label class="lbl-larger">&nbsp;</label>
    <div class="margin-bottom-1">
        <button type="submit" class="button secondary hollow tiny margin-bottom-0">
            <?php esc_html_e("Save & Test Server Detection", 'duplicator'); ?>
        </button>
    </div>

<div style="margin-top: 30px;"></div>

<h3 class="title">
    <?php esc_html_e("Advanced", 'duplicator'); ?>
</h3>
<hr size="1" />

<label class="lbl-larger">
    <?php esc_html_e('Root path', 'duplicator') ?>
</label>
<div class="margin-bottom-1">
    <input
        type="checkbox"
        name="homepath_as_abspath"
        id="homepath_as_abspath"
        class="margin-0"
        <?php disabled(WpArchiveUtils::isAbspathHomepathEquivalent()); ?>
        <?php checked($global->isHomePathAsAbsolute()); ?>
        value="1">
    <label for="homepath_as_abspath">
        <?php
        printf(
            esc_html_x(
                'Use ABSPATH %s as root path.',
                '%s represents the ABSPATH surrounded with bold (<b>) tags',
                'duplicator'
            ),
            '<b>' . esc_html(WpArchiveUtils::getArchiveListPaths('abs')) . '</b>'
        );
        ?>
        <br>
    </label>
    <p class="description">
        <?php
        if (WpArchiveUtils::isAbspathHomepathEquivalent()) {
            esc_html_e('Abspath and home path are equivalent so this option is disabled', 'duplicator');
        } else {
            ?>
            <?php
            printf(
                esc_html_x(
                    'In this installation the default root path is %s.',
                    '%s represents the root path surrounded with bold (<b>) tags',
                    'duplicator'
                ),
                '<b>' . esc_html(SnapIO::safePathUntrailingslashit(get_home_path(), true)) . '</b>'
            ); ?><br>
            <?php
            esc_html_e(
                'The path of the WordPress core is different. Activate this option if you want to consider ABSPATH as root path.',
                'duplicator'
            );
        }
        ?>

    </p>
</div>

<label class="lbl-larger">
    <?php esc_html_e('Scan File Checks', 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <input
        type="checkbox"
        name="_skip_archive_scan"
        id="_skip_archive_scan"
        class="margin-0"
        <?php checked($global->isArchiveScanSkipped()); ?>>
    <label for="_skip_archive_scan">
        <?php esc_html_e("Skip", 'duplicator') ?>
    </label><br />
    <p class="description">
        <?php
        esc_html_e(
            'If enabled all file checks on scan will be skipped before Backup creation.
            In some cases, this option can be beneficial if the scan process is having issues running or returning errors.',
            'duplicator'
        );
        ?>
    </p>
</div>

<script>
    (function($) {
        $('input[name="override_basic_auth"]').on('change', function() {
            var isAuto = ($(this).val() === 'auto');
            $('#dup-basic-auth-auto-status').toggleClass('no-display', !isAuto);
            $('#dup-basic-auth-login-wrapper').toggleClass('no-display', isAuto);
        });

        $('.ajax_protocol').on('change', function() {
            var field = $('#override_ajax_url');
            if ($(this).val() === 'custom') {
                field.attr('type', 'text').show();
            } else {
                field.attr('type', 'hidden');
            }
        });
    }(window.jQuery || jQuery))
</script>
