<?php

use Duplicator\Models\TemplateEntity;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Views\PackageScreen;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

// The initial state of every icon is rendered server-side from the template values so the
// title shows the final state on load; the page scripts keep the icons reactive afterwards.
$template = $tplMng->getDataValueObjRequired('template', TemplateEntity::class);

$isDbOnly    = BuildComponents::isDBOnly($template->components);
$isMediaOnly = BuildComponents::isMediaOnly($template->components);

$showFileFilterIcon = ((bool) $template->archive_filter_on && !$isDbOnly);
$showDbFilterIcon   = (bool) $template->database_filter_on;

$lockEntry = PackageScreen::getSecurityLockIconByMode((int) $template->installer_opts_secure_on);
?>
<span class="dup-archive-filters-icons">
    <span id="dup-archive-filter-file-icon"
        class="<?php echo ($showFileFilterIcon ? '' : 'no-display'); ?>"
        title="<?php esc_attr_e('Folder/File Filters Enabled', 'duplicator'); ?>">
        <i class="fa-solid fa-folder-minus fa-sm primary-color"></i>
    </span>
    <span id="dup-archive-filter-db-icon"
        class="<?php echo ($showDbFilterIcon ? '' : 'no-display'); ?>"
        title="<?php esc_attr_e('Database Filters Enabled', 'duplicator'); ?>">
        <i class="fa-solid fa-table fa-sm primary-color"></i>
    </span>
    <span id="dup-archive-db-only-icon"
        class="<?php echo ($isDbOnly ? '' : 'no-display'); ?>"
        title="<?php esc_attr_e('Backup Only the Database', 'duplicator'); ?>">
        <i class="fa-solid fa-database fa-sm primary-color"></i>
    </span>
    <span id="dup-archive-media-only-icon"
        class="<?php echo ($isMediaOnly ? '' : 'no-display'); ?>"
        title="<?php esc_attr_e('Backup Only Media files', 'duplicator'); ?>">
        <i class="fa-solid fa-file-image fa-sm primary-color"></i>
    </span>
    <span id="dupli-install-secure-lock-icon" data-tooltip="<?php echo esc_attr($lockEntry['tooltip']); ?>" >
        <i class="<?php echo esc_attr($lockEntry['icon']); ?> fa-sm"></i>
    </span>
    <?php do_action('duplicator_package_setup_status_icons', $template); ?>
</span>
