<?php

/**
 * Duplicator Backup row in table Backups list
 */

use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$numStorages = $tplMng->getDataValueIntRequired('numStorages');

if (
    !CapMng::can(CapMng::CAP_STORAGE, false) &&
    !CapMng::can(CapMng::CAP_CREATE, false) &&
    !CapMng::can(CapMng::CAP_BACKUP_RESTORE, false)
) {
    return;
}

?>
<hr class="separator" >
<div class="dup-section-sections">
    <ul>
        <?php if (CapMng::can(CapMng::CAP_STORAGE, false)) { ?>
        <li>
            <span class="dup-section-label-fixed-width" >
                <span class="dashicons dashicons-database gary"></span>
                <a href="<?php echo esc_url(ControllersManager::getMenuLink(ControllersManager::STORAGE_SUBMENU_SLUG)); ?>"><?php
                    echo esc_html(sprintf(
                        _n(
                            '%s Storage',
                            '%s Storages',
                            $numStorages,
                            'duplicator'
                        ),
                        $numStorages
                    ));
                            ?>
                </a>
            </span>
        </li>
        <?php } ?>
        <?php do_action('duplicator_dashboard_widget_sections'); ?>
    </ul>
</div>