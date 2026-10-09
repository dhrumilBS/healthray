<?php

/**
 * Duplicator messages sections
 */

use Duplicator\Controllers\StoragePageController;
use Duplicator\Core\CapMng;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Views\KsesHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */
$showAddNew     = $tplMng->getDataValueBool('showAddNew', true);
$minCheck       = $tplMng->getDataValueBool('minCheck', true);
$selectListData = $tplMng->getDataValueArray('selectListData');
$storageRows    = $selectListData['storageRows'];

?>
<table class="widefat dup-table-list storage-select-list small striped">
    <thead>
        <tr>
            <th></th>
            <th><?php esc_html_e('Name', 'duplicator') ?></th>
            <th><?php esc_html_e('Type', 'duplicator') ?></th>
            <th><?php esc_html_e('Location', 'duplicator') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($storageRows as $row) {
            if (!empty($row['isError'])) {
                ?>
                <tr class="<?php echo esc_attr(implode(' ', $row['rowClasses'])); ?>">
                    <td class="storage-checkbox">
                        <input type="checkbox" <?php disabled(true); ?> <?php checked(false); ?>>
                    </td>
                    <td colspan='4'>
                        <i class="fas fa-exclamation-triangle alert-color" ></i>
                        <?php esc_html_e('Unable to load storage type. Please validate the setup.', 'duplicator'); ?>
                        <strong><?php esc_html_e('Error:', 'duplicator'); ?></strong>
                        <?php echo esc_html($row['invalidMsg']); ?>
                    </td>
                </tr>
                <?php
                continue;
            }

            $storage = $row['storage'];
            ?>
            <tr class="<?php echo esc_attr(implode(' ', $row['rowClasses'])); ?>">
                <td class="storage-checkbox">
                    <?php
                    // Build parsley attributes
                    $parsleyAttrs = [];
                    if ($minCheck) {
                        $parsleyAttrs['data-parsley-mincheck'] = '1';
                        $parsleyAttrs['data-parsley-required'] = 'true';
                    }
                    ?>
                    <input
                        type="checkbox"
                        id="dup-chkbox-<?php echo esc_attr((string) $row['id']); ?>"
                        name="_storage_ids[]"
                        class="dupli-storage-input margin-bottom-0"
                        data-parsley-errors-container="#storage_error_container"
                        <?php
                        foreach ($parsleyAttrs as $name => $value) {
                            printf(
                                '%s="%s" ',
                                esc_attr($name),
                                esc_attr($value)
                            );
                        }
                        ?>
                        value="<?php echo esc_attr((string) $row['id']); ?>"
                        data-dupli-is-local="<?php echo ($storage::isLocal() ? '1' : '0'); ?>"
                        <?php disabled(!$row['isValid'] || !empty($row['isDisabled'])); ?>
                        <?php checked($row['isChecked']); ?>>
                </td>
                <td class="storage-name">
                    <?php echo wp_kses(StoragesUtil::getStatusIconHtml($storage), ['i' => ['class' => [], 'title' => []]]); ?>
                    <a href="<?php echo esc_url(StoragePageController::getEditUrl($storage)); ?>" target="_blank">
                        <?php echo esc_html($storage->getName()); ?>
                    </a>
                </td>
                <td class="storage-type">
                    <label for="dup-chkbox-<?php echo esc_attr((string) $row['id']); ?>" class="dup-store-lbl">
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
                        );
                        echo '&nbsp;' . esc_html($storage->getStypeName());
                        echo wp_kses_post(
                            apply_filters('duplicator_storage_select_list_type_suffix', '', $storage)
                        );
                        ?>
                    </label>
                </td>
                <td class="storage-location">
                    <?php
                    echo wp_kses_post($storage->getLocationHtml());
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
    <?php
    $extraStoragesFooter = (string) apply_filters('duplicator_storages_table_footer_content', '');
    if ($extraStoragesFooter !== '') :
        ?>
        <tfoot>
            <tr class="dup-table-footer-extra">
                <td colspan="4"><?php echo wp_kses($extraStoragesFooter, KsesHelper::getFooterAllowedTags()); ?></td>
            </tr>
        </tfoot>
    <?php endif; ?>
</table>
<div id="storage_error_container" class="duplicator-error-container"></div>
<?php if ($showAddNew) : ?>
    <div class="text-right">
        <?php if (CapMng::can(CapMng::CAP_STORAGE, false)) : ?>
            <a href="<?php echo esc_url(StoragePageController::getEditUrl()); ?>" target="_blank">
                [<?php esc_html_e('Add Storage', 'duplicator') ?>]
            </a>
        <?php else : ?>
            &nbsp;
        <?php endif; ?>
    </div>
<?php endif; ?>
