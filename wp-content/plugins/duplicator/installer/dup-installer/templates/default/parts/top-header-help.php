<?php



defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$archiveConfig = DUPX_ArchiveConfig::getInstance();

?>
<table cellspacing="0" class="header-wizard">
    <tr>
        <td style="width:100%;">
            <div class="dupx-branding-header">
                <?php if (isset($archiveConfig->header['logo']) && !empty($archiveConfig->header['logo'])) : ?>
                    Help
                <?php else : ?>
                    <i class="fa fa-bolt fa-sm"></i> <?php echo DUPX_U::esc_html($archiveConfig->getInstallerName('help')); ?>
                <?php endif; ?>
            </div>
        </td>
    </tr>
</table>
