<?php

use Duplicator\Installer\Package\ArchiveDescriptor;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$secureOn   = $tplMng->getDataValueInt('secureOn', ArchiveDescriptor::SECURE_MODE_NONE);
$securePass = $tplMng->getDataValueString('securePass');
$notes      = $tplMng->getDataValueString('notes');
?>
<div class="dup-box dupli-box-static dupli-general-side">
    <div class="dup-box-title">
        <i class="fa-solid fa-shield-halved fa-sm"></i> <?php esc_html_e('Security & Notes', 'duplicator') ?>
    </div>
    <div class="dup-box-panel">
        <?php
        $tplMng->render(
            'parts/packages/filters/section_security',
            [
                'secureOn'   => $secureOn,
                'securePass' => $securePass,
            ]
        );
        ?>
        <div class="dupli-general-side-notes">
            <?php
            $tplMng->render(
                'admin_pages/packages/setup/section_notes',
                ['notes' => $notes]
            );
            ?>
        </div>
    </div>
</div>
