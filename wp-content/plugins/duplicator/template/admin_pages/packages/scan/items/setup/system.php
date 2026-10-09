<?php

use Duplicator\Core\Constants;
use Duplicator\Libs\Snap\SnapOpenBasedir;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\DynamicGlobalEntity;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$webServers       = implode(', ', Constants::SERVER_LIST);
$serverSoftware   = SnapUtil::sanitizeTextInput(INPUT_SERVER, 'SERVER_SOFTWARE', 'unknown');
$fopenEnabled     = SnapServer::isURLFopenEnabled() ? '1' : '0';
$isCurlEnabled    = SnapUtil::isCurlEnabled() ? __('True', 'duplicator') : __('False', 'duplicator');
$openBaseDir      = SnapOpenBasedir::isEnabled() ? esc_html__('on', 'duplicator') : esc_html__('off', 'duplicator');
$maxExecutionTime = set_time_limit(0) === true ? 0 : @ini_get('max_execution_time');
$memoryLimit      = @ini_get('memory_limit');
?>

<div class="scan-item scan-item-first">
    <div class='title' onclick="DupliJs.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('System', 'duplicator'); ?></div>
        <div id="data-srv-php-all"></div>
    </div>
    <div class="info">
        <div class="scan-system-divider"><i class="fa fa-list"></i>&nbsp; <?php esc_html_e('General Checks', 'duplicator'); ?></div>
        <?php do_action('duplicator_scan_system_checks'); ?>
        <span id="data-srv-php-websrv"></span>
        &nbsp;<b><?php esc_html_e('Web Server', 'duplicator') ?>:</b>
        &nbsp; <?php echo esc_html($serverSoftware); ?><br />
        <div class="scan-system-subnote">
            <?php esc_html_e("Supported Web Servers:", 'duplicator'); ?>&nbsp;<?php echo esc_html($webServers); ?>
        </div>
        <hr size="1" />
        <span id="data-srv-php-mysqli"></span>&nbsp;<b><?php esc_html_e('MySQLi', 'duplicator'); ?></b><br />
        <div class="scan-system-subnote">
            <?php esc_html_e(
                'Creating the Backup does not require the mysqli module. However the installer file requires
                that the PHP module mysqli be installed on the server it is deployed on.',
                'duplicator'
            ); ?>
            <i><a href="http://php.net/manual/en/mysqli.installation.php" target="_blank">[<?php esc_html_e('details', 'duplicator'); ?>]</a></i>
        </div>
        <div class="scan-system-divider margin-top-1"><i class="fa fa-list"></i>&nbsp;<?php esc_html_e('PHP Checks', 'duplicator'); ?></div>
        <span id="data-srv-php-version"></span>&nbsp;<b><?php esc_html_e('PHP Version: ', 'duplicator'); ?> </b> <?php echo PHP_VERSION; ?> <br />
        <div class="scan-system-subnote">
            <?php
            echo esc_html(
                sprintf(
                    __(
                        'The minimum PHP version supported by Duplicator is %1$s, however it is highly
                        recommended to use PHP %2$s or higher for improved stability.',
                        'duplicator'
                    ),
                    DupliPhpVersionCheck::getMinVer(),
                    DupliPhpVersionCheck::getSuggestedVer()
                )
            ); ?>
        </div>
        <hr size="1" />
        <span id="data-srv-php-openbase"></span>&nbsp;
        <b><?php esc_html_e('PHP Open Base Dir', 'duplicator'); ?>:</b>&nbsp;<?php echo esc_html($openBaseDir); ?>
        <br />
        <div class="scan-system-subnote">
            <?php esc_html_e(
                'When [open_basedir] is enabled, issues may arise if there are symbolic links pointing outside the open_basedir restrictions.
                To view a list of unreadable files, please consult the Read Checks section.
                If you encounter problems while building a Backup,
                consider working with your server admin or hosting provider to disable this setting in the php.ini file.',
                'duplicator'
            ); ?>
            &nbsp;
            <i>
                <a href="http://php.net/manual/en/ini.core.php#ini.open-basedir" target="_blank">[<?php esc_html_e('details', 'duplicator'); ?>]</a>
            </i>
            <br />
        </div>

        <hr size="1" />
        <span id="data-srv-php-maxtime"></span>&nbsp;
        <b><?php esc_html_e('PHP Max Execution Time', 'duplicator'); ?>:</b>&nbsp; <?php echo esc_html($maxExecutionTime); ?>
        <br />
        <div class="scan-system-subnote">
            <?php
            esc_html(
                sprintf(
                    __(
                        'Issues might occur for larger Backups when the [max_execution_time] value in the php.ini is too low.
                        The minimum recommended timeout is "%1$s" seconds or higher.
                        An attempt is made to override this value if the server allows it. A value of 0 (recommended) indicates that PHP has no time limits.',
                        'duplicator'
                    ),
                    DUPLICATOR_SCAN_TIMEOUT
                )
            ); ?>
            &nbsp;
            <i>
                <a href="http://www.php.net/manual/en/info.configuration.php#ini.max-execution-time" target="_blank">
                    [<?php esc_html_e('details', 'duplicator'); ?>]
                </a>
            </i>
        </div>

        <hr size="1" />
        <span id="data-srv-php-minmemory"></span>&nbsp;
        <b><?php esc_html_e('PHP Memory Limit', 'duplicator'); ?>:</b>&nbsp; <?php echo esc_html($memoryLimit); ?>
        <br />
        <div class="scan-system-subnote">
            <?php
            echo wp_kses(
                sprintf(
                    _x(
                        'Issues might occur for larger Backups when the [memory_limit] value in the php.ini is too low.
                    The minimum recommended memory limit is "%1$s" or higher. An attempt is made to override this value if the server allows it.
                    To manually increase the memory limit have a look at this %2$s[FAQ item]%3$s',
                        '1: memory limit, 2: link start, 3: link end',
                        'duplicator'
                    ),
                    DUPLICATOR_MIN_MEMORY_LIMIT,
                    "<i><a href='" . DUPLICATOR_DUPLICATOR_DOCS_URL . "how-to-manage-server-resources-cpu-memory-disk' target='_blank'>",
                    "</a></i>"
                ),
                [
                    'a' => [
                        'href'   => [],
                        'target' => [],
                    ],
                    'i' => [],
                ]
            ); ?>
        </div>

        <br />
    </div>
</div>
