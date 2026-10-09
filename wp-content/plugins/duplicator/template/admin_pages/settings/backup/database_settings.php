<?php

use Duplicator\Models\GlobalEntity;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Core\Constants;
use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\OptionsUIHelper;
use Duplicator\Core\Options\Rules\DbDumpEngineRule;
use Duplicator\Libs\Shell\Shell;
use Duplicator\Libs\WpUtils\WpDbUtils;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$global               = GlobalEntity::getInstance();
$optionsMng           = OptionsManager::getInstance();
$is_shellexec_on      = Shell::test();
$mysqlDumpPath        = WpDbUtils::getMySqlDumpPath();
$mysqlDumpFound       = (bool) $mysqlDumpPath;
$isMysqldumpAvailable = OptionsUIHelper::isValueAvailable(DbDumpEngineRule::OPTION_KEY, DbDumpEngineRule::VALUE_MYSQLDUMP);

// The form preselects a valid value: the stored one when available, else the first available default
$dbEngineSelection = OptionsUIHelper::getSelectionValue(
    DbDumpEngineRule::OPTION_KEY,
    $global->isMysqldumpEnabled() ? DbDumpEngineRule::VALUE_MYSQLDUMP : DbDumpEngineRule::VALUE_PHP
);
?>

<h3 class="title">
    <?php esc_html_e("Database", 'duplicator') ?>
</h3>
<hr size="1" />

<label class="lbl-larger">
    <?php echo esc_html($optionsMng->getOptionLabel(DbDumpEngineRule::OPTION_KEY)); ?>
</label>
<div class="margin-bottom-1">
    <div class="margin-bottom-1">
        <div class="engine-radio <?php echo $isMysqldumpAvailable ? '' : 'engine-radio-disabled'; ?> inline-display">
            <input
                type="radio"
                name="_package_dbmode"
                value="mysql"
                id="package_mysqldump"
                class="margin-0"
                <?php checked($dbEngineSelection === DbDumpEngineRule::VALUE_MYSQLDUMP); ?>
                <?php disabled(!$isMysqldumpAvailable); ?> onclick="DupliJs.UI.SetDBEngineMode();">
            <label for="package_mysqldump">
                <?php OptionsUIHelper::renderValueWarning(DbDumpEngineRule::OPTION_KEY, DbDumpEngineRule::VALUE_MYSQLDUMP, '_package_dbmode'); ?>
                <?php echo esc_html($optionsMng->getValueLabel(DbDumpEngineRule::OPTION_KEY, DbDumpEngineRule::VALUE_MYSQLDUMP)); ?>
            </label>
            &nbsp; &nbsp; &nbsp;
        </div>

        <div class="engine-radio inline-display">
            <input
                type="radio"
                name="_package_dbmode"
                id="package_phpdump"
                value="php"
                class="margin-0"
                <?php checked($dbEngineSelection === DbDumpEngineRule::VALUE_PHP); ?>
                <?php disabled(!OptionsUIHelper::isValueAvailable(DbDumpEngineRule::OPTION_KEY, DbDumpEngineRule::VALUE_PHP)); ?>
                onclick="DupliJs.UI.SetDBEngineMode();">
            <label for="package_phpdump">
                <?php OptionsUIHelper::renderValueWarning(DbDumpEngineRule::OPTION_KEY, DbDumpEngineRule::VALUE_PHP, '_package_dbmode'); ?>
                <?php echo esc_html($optionsMng->getValueLabel(DbDumpEngineRule::OPTION_KEY, DbDumpEngineRule::VALUE_PHP)); ?>
            </label>
        </div>
        <?php OptionsUIHelper::renderOptionError(DbDumpEngineRule::OPTION_KEY); ?>
    </div>

    <!-- SHELL EXEC  -->
    <div class="engine-sub-opts" id="dbengine-details-1" style="display:none">
        <b class="dupli-engine-sub-opts-title"><?php esc_html_e('Mysqldump Options', 'duplicator'); ?></b>
        <!-- MYSQLDUMP IN-ACTIVE -->
        <?php if (!$is_shellexec_on) :
            ?>
            <div class="dup-feature-notfound">
                <?php
                esc_html_e(
                    'In order to use Mysqldump, the PHP functions popen/pclose must be enabled.',
                    'duplicator'
                );
                echo ' ';
                esc_html_e('Please contact your host or server admin to enable this function.', 'duplicator');
                echo ' ';
                printf(
                    esc_html_x(
                        'For a list of approved providers that support this function, %1$sclick here%2$s.',
                        '%1$s and %2$s are the opening and closing tags of a link.',
                        'duplicator'
                    ),
                    '<a href="' . esc_url(DUPLICATOR_BLOG_URL . 'best-wordpress-hosting/')
                        . '" target="_blank">',
                    '</a>'
                );
                echo ' ';
                esc_html_e('The "PHP Code" setting will be used until this issue is resolved by your hosting provider.', 'duplicator');
                ?>
                <p>
                    <?php
                    esc_html_e('Below is a list of possible functions to activate to solve the problem.', 'duplicator');
                    echo ' ';
                    esc_html_e('If the problem persists, look at the log for a more thorough analysis.', 'duplicator');
                    ?>
                </p>
                <br />
                <b><?php esc_html_e('Disabled Functions:', 'duplicator'); ?></b>
                <code class="display-block margin-bottom-1">
                    <?php
                    foreach (['escapeshellarg', 'escapeshellcmd', 'extension_loaded', 'popen', 'pclose'] as $func) {
                        if (Shell::hasDisabledFunctions($func)) {
                            echo esc_html($func);
                            echo '<br>';
                        }
                    }
                    ?>
                </code>
                <?php
                printf(
                    esc_html_x(
                        'FAQ: %1$sHow to enable disabled PHP functions.%2$s',
                        '%1$s and %2$s are the opening and closing tags of a link.',
                        'duplicator'
                    ),
                    '<a href="' . esc_url(DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-resolve-dependency-checks') . '" target="_blank">',
                    '</a>'
                );
                ?>
            </div>
            <!-- MYSQLDUMP ACTIVE -->
            <?php
        else :
            $tipContent =  esc_attr__(
                'Add a custom path if the path to mysqldump is not properly detected.   
                For all paths use a forward slash as the path separator.
                On Linux systems use mysqldump for Windows systems use mysqldump.exe.
                If the path tried does not work please contact your hosting provider for details on the correct path.',
                'duplicator'
            );
            ?>
            <?php if (!$isMysqldumpAvailable) { ?>
                <p class="description">
                    <?php esc_html_e(
                        'Set a valid custom mysqldump path and save the settings to make the Mysqldump engine available.',
                        'duplicator'
                    ); ?>
                </p>
            <?php } ?>
            <span><?php esc_html_e("Current Path:", 'duplicator'); ?></span>&nbsp;
            <?php
            SettingsPageController::getMySQLDumpMessage(
                $mysqlDumpFound,
                (!empty($mysqlDumpPath) ? $mysqlDumpPath : $global->getMysqldumpPath())
            ); ?><br><br>
            <span><?php esc_html_e("Custom Path:", 'duplicator'); ?></span>&nbsp;
            <input
                class="width-large inline-display"
                type="text"
                name="_package_mysqldump_path"
                id="_package_mysqldump_path"
                value="<?php echo esc_attr($global->getMysqldumpPath()); ?>"
                placeholder="<?php esc_attr_e("/usr/bin/mypath/mysqldump", 'duplicator'); ?>">&nbsp;
            <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
                data-tooltip-title="<?php esc_attr_e("mysqldump", 'duplicator'); ?>"
                data-tooltip="<?php echo esc_attr($tipContent); ?>">
            </i><br>

            <div class="<?php echo $isMysqldumpAvailable ? '' : 'no-display'; ?>">
                <label><?php esc_html_e("Switch Options:", 'duplicator'); ?></label>
                <div class="dup-group-option-wrapper">
                    <?php
                    $mysqldumpOptions = $global->getMysqldumpOptions();
                    foreach ($mysqldumpOptions as $key => $option) {
                        ?>
                        <div class="dup-group-option-item">
                            <input
                                type="checkbox"
                                name="<?php echo esc_attr($option->getInputName()); ?>"
                                id="<?php echo esc_attr($option->getInputName()); ?>"
                                class="margin-0"
                                <?php checked($option->getEnabled()); ?>>
                            --<?php echo esc_html($option->getOptionName()); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php
        endif; ?>
    </div>

    <!-- PHP OPTION -->
    <div class="engine-sub-opts" id="dbengine-details-2" style="display:none">
        <b class="dupli-engine-sub-opts-title"><?php esc_html_e('PHP Code Options', 'duplicator'); ?></b>
        <span><?php esc_html_e("Process Mode", 'duplicator'); ?></span>&nbsp;
        <select name="_phpdump_mode" class="width-medium inline-display margin-0">
            <option
                <?php selected($global->getPhpDumpMode(), WpDbUtils::PHPDUMP_MODE_MULTI); ?>
                value="<?php echo (int) WpDbUtils::PHPDUMP_MODE_MULTI; ?>">
                <?php esc_html_e("Multi-Threaded", 'duplicator'); ?>
            </option>
            <option
                <?php selected($global->getPhpDumpMode(), WpDbUtils::PHPDUMP_MODE_SINGLE); ?>
                value="<?php echo (int) WpDbUtils::PHPDUMP_MODE_SINGLE; ?>">
                <?php esc_html_e("Single-Threaded", 'duplicator'); ?>
            </option>
        </select>&nbsp;
        <i style="margin-right:7px;" class="fa-solid fa-question-circle fa-sm dark-gray-color"
            data-tooltip-title="<?php esc_attr_e("PHP Code Mode", 'duplicator'); ?>"
            data-tooltip="<?php
                            esc_attr_e(
                                'Single-Threaded mode attempts to create the entire database script in one request. 
                Multi-Threaded mode allows the database script to be chunked over multiple requests.
                Multi-Threaded mode is typically slower but much more reliable especially for larger databases.',
                                'duplicator'
                            );
                            ?>"></i>
    </div>
</div>

<label class="lbl-larger" for="_package_mysqldump_qrylimit">
    <?php esc_html_e("Query Size", 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <select name="_package_mysqldump_qrylimit" id="_package_mysqldump_qrylimit" class="width-small inline-display margin-0">
        <?php
        foreach (Constants::MYSQL_DUMP_CHUNK_SIZES as $value => $label) {
            echo '<option ' . selected($global->getMysqldumpQueryLimit(), $value, false) . ' value="' . (int) $value . '">'
                . esc_html($label) . '</option>';
        }
        ?>
    </select>&nbsp;
    <?php $tipContent = __(
        'A higher limit size will speed up the database build time, however it will use more memory.
        If your host has memory caps start off low.',
        'duplicator'
    ); ?>
    <i style="margin-right:7px" class="fa-solid fa-question-circle fa-sm dark-gray-color"
        data-tooltip-title="<?php esc_attr_e("MYSQL Query Limit Size", 'duplicator'); ?>"
        data-tooltip="<?php echo esc_attr($tipContent); ?>">
    </i>
</div>