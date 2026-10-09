<?php



defined("ABSPATH") or die("");

use Duplicator\Core\Views\TplMng;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\StoragesUtil;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var Duplicator\Models\Storages\AbstractStorageEntity $storage
 */
$storage = $tplMng->getDataValueObjRequired('storage', AbstractStorageEntity::class);
$index   = $tplMng->getDataValueIntRequired('index');

$typeName        = $storage->getStypeName();
$typeId          = $storage->getSType();
$invalidErrorMsg = __('Invalid storage configuration', 'duplicator');
$isValid         = $storage->isValid($invalidErrorMsg);
$isSupported     = $storage::isSupported();
$disabledReason  = '';
$isDisabled      = !$storage::isEnabled($disabledReason);

$row_classes = [ 'storage-row' ];

if ($index % 2) {
    $row_classes[] = 'alternate';
}

if (!$isSupported) {
    $row_classes[]    = 'unsupported';
    $shortDescWarning = __('Storage Type Not Supported', 'duplicator');
    $longDescWarning  = __(
        'This storage type is not supported on this server. Please remove this storage or contact your host to install the required extensions.',
        'duplicator'
    );
} elseif ($isDisabled) {
    $row_classes[]    = 'disabled';
    $shortDescWarning = __('Storage Disabled', 'duplicator');
    $longDescWarning  = $disabledReason;
} elseif (!$isValid) {
    $row_classes[]    = 'invalid';
    $shortDescWarning = $invalidErrorMsg;
    $longDescWarning  = __(
        'This storage has invalid configuration and cannot be used. Please edit and fix the configuration.',
        'duplicator'
    );
} else {
    $shortDescWarning = '';
    $longDescWarning  = '';
}

?>
<tr id="main-view-<?php echo (int) $storage->getId() ?>"
    class="<?php echo esc_attr(implode(' ', $row_classes)); ?>"
    data-delete-view="<?php echo esc_attr($storage->getDeleteView(false)); ?>"
>
    <td class="storage-checkbox">
        <?php if (!$storage->isDeletable()) : ?>
            <input type="checkbox" disabled="disabled" />
        <?php else : ?>
            <input name="selected_id[]" type="checkbox" value="<?php echo (int) $storage->getId(); ?>" class="item-chk" />
        <?php endif; ?>
    </td>
    <td class="storage-name">
        <a href="javascript:void(0);" onclick="DupliJs.Storage.Edit('<?php echo (int) $storage->getId(); ?>')">
            <?php echo wp_kses(StoragesUtil::getStatusIconHtml($storage), ['i' => ['class' => [], 'title' => []]]); ?>
            <b><?php echo esc_html($storage->getName()); ?></b>
        </a>
        <div class="sub-menu">
            <a href="javascript:void(0);" onclick="DupliJs.Storage.Edit('<?php echo (int) $storage->getId(); ?>')">
                <?php esc_html_e('Edit', 'duplicator'); ?>
            </a>
            |
            <a href="javascript:void(0);" onclick="DupliJs.Storage.View('<?php echo (int) $storage->getId(); ?>');">
                <?php esc_html_e('Quick View', 'duplicator'); ?>
            </a>
            <?php if (!$storage::isUnique() && !$storage->isLocal()) : ?>
                |
                <a href="javascript:void(0);" onclick="DupliJs.Storage.CopyEdit('<?php echo (int) $storage->getId(); ?>');">
                    <?php esc_html_e('Copy', 'duplicator'); ?>
                </a>
            <?php endif; ?>
            <?php if ($storage->isDeletable()) : ?>
                |
                <a href="javascript:void(0);" onclick="DupliJs.Storage.deleteSingle('<?php echo (int) $storage->getId(); ?>');">
                    <?php esc_html_e('Delete', 'duplicator'); ?>
                </a>
            <?php endif; ?>
        </div>
    </td>
    <td class="storage-type">
        <?php
        echo wp_kses(
            $storage->getStypeIcon(),
            [
                'i'   => [
                    'class' => [],
                ],
                'img' => [
                    'src'   => [],
                    'class' => [],
                    'alt'   => [],
                ],
            ]
        ),
        '&nbsp;',
        esc_html($storage->getStypeName());
        ?>
    </td>
    <td class="storage-location">
        <?php echo wp_kses_post($storage->getLocationHtml()); ?>
    </td>
</tr>
<?php
ob_start();
try { ?>
    <tr id='quick-view-<?php echo (int) $storage->getId(); ?>'
        class='<?php echo ($index % 2) ? 'alternate' : ''; ?> storage-detail'>
        <td colspan="4">
            <b><?php esc_html_e('QUICK VIEW', 'duplicator') ?></b> <br/>
            <div>
                <label><?php esc_html_e('Name', 'duplicator') ?>:</label>
                <?php echo esc_html($storage->getName()); ?>
            </div>
            <div>
                <label><?php esc_html_e('Notes', 'duplicator') ?>:</label>
                <?php echo (strlen($storage->getNotes())) ? esc_html($storage->getNotes()) : esc_html__('(no notes)', 'duplicator'); ?>
            </div>
            <div>
                <label><?php esc_html_e('Type', 'duplicator') ?>:</label>
                <?php echo esc_html($storage->getStypeName()); ?>
            </div>
            <?php $storage->getListQuickView(); ?>
            <?php if (!$isSupported || !$isValid) : ?>
            <div class="storage-status-warning">
                <p>
                    <?php echo wp_kses(StoragesUtil::getStatusIconHtml($storage), ['i' => ['class' => [], 'title' => []]]); ?>&nbsp;
                    <strong><?php echo esc_html($shortDescWarning); ?></strong>
                </p>
                <?php echo $isDisabled ? wp_kses_post($longDescWarning) : esc_html($longDescWarning); ?>
            </div>
            <?php endif; ?>
            <button type="button" class="button secondary hollow tiny"
                    onclick="DupliJs.Storage.View('<?php echo (int) $storage->getId(); ?>');">
                <?php esc_html_e('Close', 'duplicator') ?>
            </button>
        </td>
    </tr>
    <?php
} catch (Exception $e) {
    ob_clean(); ?>
    <tr id='quick-view-<?php echo intval($storage->getId()); ?>' class='<?php echo ($index % 2) ? 'alternate' : ''; ?>'>
        <td colspan="4">
            <?php TplMng::getInstance()->render(
                'admin_pages/storages/parts/storage_error',
                ['exception' => $e]
            ); ?>
            <br><br>
            <button type="button" class="button" onclick="DupliJs.Storage.View('<?php echo intval($storage->getId()); ?>');">
            <?php esc_html_e('Close', 'duplicator') ?>
            </button>
        </td>
    </tr>
    <?php
}
ob_end_flush();
