<?php



defined("ABSPATH") or die("");

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Models\TemplateEntity;
use Duplicator\Views\UI\UiViewState;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */
$template     = $tplMng->getDataValueObjRequired('template', TemplateEntity::class);
$dbHost       = $template->installer_opts_db_host;
$dbName       = $template->installer_opts_db_name;
$dbUser       = $template->installer_opts_db_user;
$cpnlEnable   = $template->installer_opts_cpnl_enable;
$cpnlHost     = $template->installer_opts_cpnl_host;
$cpnlUser     = $template->installer_opts_cpnl_user;
$cpnlDbAction = $template->installer_opts_cpnl_db_action;
$cpnlDbHost   = $template->installer_opts_cpnl_db_host;
$cpnlDbName   = $template->installer_opts_cpnl_db_name;
$cpnlDbUser   = $template->installer_opts_cpnl_db_user;

$ui_css_installer = (UiViewState::getValue('dupli-pack-installer-panel') ? 'display:block' : 'display:none');

?>
<div class="dup-box">
    <div class="dup-box-title">
        <i class="fa fa-bolt fa-sm"></i> <?php esc_html_e('Installer', 'duplicator') ?>
        <button class="dup-box-arrow">
            <span class="screen-reader-text"><?php esc_html_e('Toggle panel:', 'duplicator') ?>
                <?php esc_html_e('Installer Settings', 'duplicator') ?>
            </span>
        </button>
    </div>
    <div class="dup-box-panel" id="dupli-pack-installer-panel" style="<?php echo esc_attr($ui_css_installer); ?>">
        <?php do_action('duplicator_backup_installer_section_before'); ?>

        <div class="dup-package-hdr-1">
            <?php esc_html_e("Prefills", 'duplicator') ?>&nbsp;
            <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
                data-tooltip-title="<?php esc_attr_e("Setup/Prefills", 'duplicator'); ?>"
                data-tooltip="<?php
                                esc_attr_e(
                                    'All values in this section are OPTIONAL! If you know ahead of time the database input fields the installer will use, 
                    then you can optionally enter them here and they will be prefilled at install time. 
                    Otherwise you can just enter them in at install time and ignore all these options in the Installer section.',
                                    'duplicator'
                                );
                                ?>">
            </i>
        </div>

        <!-- ===================
        BASIC/CPANEL TABS -->
        <div data-dupli-tabs="true">
            <ul>
                <li id="dupli-bsc-tab-lbl"><?php esc_html_e('Basic', 'duplicator') ?></li>
                <li id="dupli-cpnl-tab-lbl"><?php esc_html_e('cPanel', 'duplicator') ?></li>
            </ul>

            <!-- ===================
            TAB1: Basic -->
            <div>
                <div class="dup-package-hdr-2">
                    <?php esc_html_e("MySQL Server", 'duplicator') ?>
                    <div class="dup-package-hdr-usecurrent">
                        <a href="javascript:void(0)" onclick="DupliJs.Pack.ApplyDataCurrent('s1-installer-dbbasic')">
                            [<?php esc_html_e('use current', 'duplicator') ?>]
                        </a>
                    </div>
                </div>

                <div id="s1-installer-dbbasic">
                    <label class="lbl-larger">
                        <?php esc_html_e("Host", 'duplicator') ?>:
                    </label>
                    <div>
                        <input
                            type="text"
                            name="installer_opts_db_host"
                            id="dbhost"
                            maxlength="200"
                            placeholder="<?php esc_html_e("example: localhost (value is optional)", 'duplicator') ?>"
                            data-current="<?php echo esc_attr(DB_HOST); ?>"
                            value="<?php echo esc_attr($dbHost); ?>">
                    </div>

                    <label class="lbl-larger">
                        <?php esc_html_e("Database", 'duplicator') ?>:
                    </label>
                    <div>
                        <input
                            type="text"
                            name="installer_opts_db_name"
                            id="dbname"
                            maxlength="100"
                            placeholder="<?php esc_html_e("example: DatabaseName (value is optional)", 'duplicator') ?>"
                            data-current="<?php echo esc_attr(DB_NAME) ?>"
                            value="<?php echo esc_attr($dbName); ?>">
                    </div>

                    <label class="lbl-larger">
                        <?php esc_html_e("User", 'duplicator') ?>:
                    </label>
                    <div>
                        <input
                            type="text"
                            name="installer_opts_db_user"
                            id="dbuser"
                            maxlength="100"
                            placeholder="<?php esc_html_e("example: DatabaseUser (value is optional)", 'duplicator') ?>"
                            data-current="<?php echo esc_attr(DB_USER); ?>"
                            value="<?php echo esc_attr($dbUser); ?>">
                    </div>
                </div>

            </div>

            <!-- ===================
            TAB2: cPanel -->
            <div>
                <div class="dup-package-hdr-2">
                    <?php esc_html_e("cPanel Login", 'duplicator') ?>
                </div>

                <label class="lbl-larger">
                    <?php esc_html_e("Automation", 'duplicator') ?>:
                </label>
                <div>
                    <?php
                    $tipContent = __(
                        'Enabling this option will automatically select the cPanel tab when step one of the installer is shown.',
                        'duplicator'
                    );
                    ?>
                    <input
                        type="checkbox"
                        name="installer_opts_cpnl_enable"
                        id="cpnl-enable"
                        value="1"
                        <?php checked($cpnlEnable); ?>>
                    <label for="cpnl-enable"><?php esc_html_e('Auto Select cPanel', 'duplicator') ?></label>
                    <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
                        data-tooltip-title="<?php esc_attr_e('Auto Select cPanel', 'duplicator'); ?>"
                        data-tooltip="<?php echo esc_attr($tipContent); ?>">
                    </i>
                </div>

                <label class="lbl-larger">
                    <?php esc_html_e("Host", 'duplicator') ?>:
                </label>
                <div>
                    <input
                        type="text"
                        name="installer_opts_cpnl_host"
                        id="cpnl-host"
                        maxlength="200"
                        value="<?php echo esc_attr($cpnlHost); ?>"
                        placeholder="<?php esc_attr_e("example: cpanelHost (value is optional)", 'duplicator') ?>">
                </div>

                <label class="lbl-larger">
                    <?php esc_html_e("User", 'duplicator') ?>:
                </label>
                <div>
                    <input
                        type="text"
                        name="installer_opts_cpnl_user"
                        id="cpnl-user"
                        value="<?php echo esc_attr($cpnlUser); ?>"
                        maxlength="200"
                        placeholder="<?php esc_attr_e("example: cpanelUser (value is optional)", 'duplicator') ?>">
                </div>

                <div class="dup-package-hdr-2">
                    <?php esc_html_e("MySQL Server", 'duplicator') ?>
                    <div class="dup-package-hdr-usecurrent">
                        <a href="javascript:void(0)" onclick="DupliJs.Pack.ApplyDataCurrent('s1-installer-dbcpanel')">
                            [<?php esc_html_e('use current', 'duplicator') ?>]
                        </a>
                    </div>
                </div>

                <div id="s1-installer-dbcpanel">
                    <label class="lbl-larger">
                        <?php esc_html_e("Action", 'duplicator') ?>:
                    </label>
                    <div>
                        <select name="installer_opts_cpnl_db_action" id="cpnl-dbaction">
                            <option value="" <?php selected($cpnlDbAction, ''); ?>>
                                <?php esc_html_e('Default', 'duplicator'); ?>
                            </option>
                            <option value="create" <?php selected($cpnlDbAction, 'create'); ?>>
                                <?php esc_html_e('Create A New Database', 'duplicator'); ?>
                            </option>
                            <option value="empty" <?php selected($cpnlDbAction, 'empty'); ?>>
                                <?php esc_html_e('Connect and Delete Any Existing Data', 'duplicator'); ?>
                            </option>
                            <option value="rename" <?php selected($cpnlDbAction, 'rename'); ?>>
                                <?php esc_html_e('Connect and Backup Any Existing Data', 'duplicator'); ?>
                            </option>
                            <option value="manual" <?php selected($cpnlDbAction, 'manual'); ?>>
                                <?php esc_html_e('Skip Database Extraction', 'duplicator'); ?>
                            </option>
                        </select>
                    </div>

                    <label class="lbl-larger">
                        <?php esc_html_e("Host", 'duplicator') ?>:
                    </label>
                    <div>
                        <input
                            type="text"
                            name="installer_opts_cpnl_db_host"
                            id="cpnl-dbhost"
                            value="<?php echo esc_attr($cpnlDbHost); ?>"
                            maxlength="200"
                            placeholder="<?php esc_attr_e("example: localhost (value is optional)", 'duplicator') ?>"
                            data-current="<?php echo esc_html(DB_HOST); ?>">
                    </div>

                    <label class="lbl-larger">
                        <?php esc_html_e("Database", 'duplicator') ?>:
                    </label>
                    <div>
                        <input
                            type="text"
                            name="installer_opts_cpnl_db_name"
                            value="<?php echo esc_attr($cpnlDbName); ?>"
                            id="cpnl-dbname"
                            data-parsley-pattern="/^[a-zA-Z0-9-_]+$/"
                            maxlength="100"
                            placeholder="<?php esc_attr_e("example: DatabaseName (value is optional)", 'duplicator') ?>"
                            data-current="<?php echo esc_html(DB_NAME); ?>">
                    </div>

                    <label class="lbl-larger">
                        <?php esc_html_e("User", 'duplicator') ?>:
                    </label>
                    <div>
                        <input
                            type="text"
                            name="installer_opts_cpnl_db_user"
                            value="<?php echo esc_attr($cpnlDbUser); ?>"
                            id="cpnl-dbuser"
                            data-parsley-pattern="/^[a-zA-Z0-9-_]+$/"
                            maxlength="100"
                            placeholder="<?php esc_attr_e("example: DatabaseUserName (value is optional)", 'duplicator') ?>"
                            data-current="<?php echo esc_html(DB_USER); ?>">
                    </div>
                </div>
            </div>
        </div><br />

        <small><?php esc_html_e("Additional inputs can be entered at install time.", 'duplicator') ?></small>
        <br /><br />
    </div>
</div><br />

<script>
    (function($) {
        DupliJs.Pack.ApplyDataCurrent = function(id) {
            $('#' + id + ' input').each(function() {
                var attr = $(this).attr('data-current');
                if (typeof attr !== typeof undefined && attr !== false) {
                    $(this).val($(this).attr('data-current'));
                }
            });
        };


    }(window.jQuery));
</script>