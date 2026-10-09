<?php

/**
 * Duplicator messages sections
 */

use Duplicator\Core\Addons\AddonsManager;
use Duplicator\Models\Storages\AbstractStorageEntity;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */
$storage       = $tplMng->getDataValueObjRequired('storage', AbstractStorageEntity::class);
$storageTypes  = $tplMng->getDataValueArray('storageTypes');
$sTypeSelected = $tplMng->getDataValueInt('sTypeSelected', -1);
$isEditMode    = $tplMng->getDataValueBool('isEditMode');
?>

<div class="dup-storage-type-selector" id="dup-storage-type-selector">
    <span class="dup-storage-type-selector__current" id="dup-storage-current">
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
        ?>
        <span><?php echo esc_html($storage->getStypeName()); ?></span>
    </span>

    <?php if ($isEditMode) : ?>
        <button type="button" class="dup-storage-type-selector__toggle button primary hollow margin-bottom-0" id="dup-storage-toggle">
            <?php esc_html_e('Select', 'duplicator'); ?>
        </button>

        <div class="dup-storage-type-selector__grid" id="dup-storage-grid">
            <div class="dup-storage-type-selector__grid-inner">
                <?php foreach ($storageTypes as $storageType) : ?>
                    <?php
                    $cardClasses  = 'dup-storage-card';
                    $cardClasses .= ($sTypeSelected === $storageType['type']) ? ' is-selected' : '';
                    $cardClasses .= $storageType['disabled'] ? ' is-disabled' : '';
                    $cardTitle    = $storageType['disabled'] ? wp_strip_all_tags($storageType['reason']) : $storageType['name'];
                    ?>
                    <label
                        class="<?php echo esc_attr($cardClasses); ?>"
                        title="<?php echo esc_attr($cardTitle); ?>"
                        data-storage-type="<?php echo (int) $storageType['type']; ?>"
                        data-storage-name="<?php echo esc_attr($storageType['name']); ?>"
                        data-storage-disabled="<?php echo $storageType['disabled'] ? '1' : '0'; ?>"
                    >
                        <input
                            type="radio"
                            name="storage_type_radio"
                            value="<?php echo (int) $storageType['type']; ?>"
                            <?php checked($sTypeSelected, $storageType['type']); ?>
                            <?php disabled($storageType['disabled']); ?>
                        >
                        <?php
                        echo wp_kses(
                            $storageType['icon'],
                            [
                                'i'   => ['class' => []],
                                'img' => [
                                    'src'   => [],
                                    'class' => [],
                                    'alt'   => [],
                                ],
                            ]
                        );
                        ?>
                        <div class="label" >
                            <?php echo esc_attr($storageType['name']); ?>
                        </div>
                        <?php if ($storageType['disabled']) : ?>
                            <span class="dup-storage-card__disabled-overlay"></span>
                        <?php endif; ?>
                    </label>
                    <?php if ($storageType['gridBreak']) : ?>
                        <div class="dup-storage-grid-break"></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <select id="change-mode" name="storage_type" onchange="DupliJs.Storage.ChangeMode()" class="width-medium" style="display: none;">
            <?php foreach ($storageTypes as $storageType) : ?>
                <option
                    value="<?php echo (int) $storageType['type']; ?>"
                    <?php selected($sTypeSelected, $storageType['type']); ?>
                    <?php disabled($storageType['disabled']); ?>
                >
                    <?php echo esc_html($storageType['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php else : ?>
        <span id="dup-storage-mode-fixed" data-storage-type="<?php echo (int) $storage->getSType(); ?>" style="display: none;"></span>
    <?php endif; ?>
</div>

<script>
    jQuery(document).ready(function ($) {
        DupliJs.Storage.InitTypeSelector = function($container) {
            const $toggle = $container.find('.dup-storage-type-selector__toggle');
            const $cards = $container.find('.dup-storage-card');
            const $current = $container.find('.dup-storage-type-selector__current');
            const $select = $('#change-mode');

            $toggle.on('click', function() {
                $container.toggleClass('is-open');
            });

            $cards.on('click', function() {
                const $card = $(this);
                const isDisabled = $card.data('storage-disabled') === 1;

                // Prevent selection of disabled storage types
                if (isDisabled) {
                    return false;
                }

                const storageType = $card.data('storage-type');
                const storageName = $card.data('storage-name');
                const $icon = $card.find('i, img').clone();

                $card.find('input[type="radio"]').prop('checked', true);

                $select.val(storageType).trigger('change');

                $cards.removeClass('is-selected');
                $card.addClass('is-selected');

                $current.html($icon.add('<span>' + storageName + '</span>'));

                $container.removeClass('is-open');

                DupliJs.Storage.ChangeMode();
            });
        };

        // Initialize the type selector if it exists
        const $typeSelector = $('#dup-storage-type-selector');
        if ($typeSelector.length > 0) {
            DupliJs.Storage.InitTypeSelector($typeSelector);
        }

        DupliJs.Storage.BindParsley = function (node)
        {
            $('#dup-storage-form').parsley().destroy();
            $('#dup-storage-form .provider input').attr('data-parsley-excluded', 'true');

            node.find('input').removeAttr('data-parsley-excluded');

            $('#dup-storage-form').parsley();
        };

        DupliJs.Storage.Autofill = function (mode) {
        <?php if (AddonsManager::getInstance()->isAddonEnabled('AmazonS3Addon')) : ?>
            switch (parseInt(mode)) {
                case <?php echo (int) \Duplicator\Addons\AmazonS3Addon\Models\BackblazeStorage::getSType(); ?>:
                case <?php echo (int) \Duplicator\Addons\AmazonS3Addon\Models\DreamStorage::getSType(); ?>:
                    autoFillRegion(mode, 1);
                    break;
                case <?php echo (int) \Duplicator\Addons\AmazonS3Addon\Models\VultrStorage::getSType(); ?>:
                case <?php echo (int) \Duplicator\Addons\AmazonS3Addon\Models\DigitalOceanStorage::getSType(); ?>:
                    autoFillRegion(mode, 0);
                    break;
                case <?php echo (int) \Duplicator\Addons\AmazonS3Addon\Models\WasabiStorage::getSType(); ?>:
                    let wasabiRegion   = $("#s3_region_" + mode);
                    let wasabiEndpoint = $("#s3_endpoint_" + mode);

                    wasabiRegion.change(function(e) {
                        if (wasabiEndpoint.val().length > 0) {
                            return;
                        }

                        let regionVal = $(this).val();
                        if (regionVal.length > 0) {
                            wasabiEndpoint.val("s3." + regionVal + ".wasabisys.com");
                        } else {
                            wasabiEndpoint.val("");
                        }
                    });
                    break;
            }

            function autoFillRegion(type, regionPos) {
                let region      = $("#s3_region_" + type);
                let endpoint    = $("#s3_endpoint_" + type);

                endpoint.change(function(e) {
                    bindEndpointToRegion(region, endpoint, regionPos);
                });
            }

            function bindEndpointToRegion(region, endpoint, pos) {
                if (region.val().length > 0) {
                    return;
                }

                if (endpoint.val().length > 0) {
                    let regionStr = endpoint.val().replace(/.*:\/\//g,'').split(".")[pos];
                    region.val(regionStr);
                } else {
                    region.val("");
                }
            }
        <?php else : ?>
            return;
        <?php endif; ?>
        }

        // GENERAL STORAGE LOGIC
        DupliJs.Storage.ChangeMode = function (animateOverride = 400) {
            const mode = $('#dup-storage-mode-fixed').length > 0
                ? $('#dup-storage-mode-fixed').data('storage-type')
                : $('#change-mode option:selected').val();

            // Reset the copy source ID select
            $('#dup-copy-source-id-select').val(-1);

            // Disable copy controls for local storage
            const isCopyDisabled = parseInt(mode) === 0;
            $('#dup-copy-source-id-select, #dup-copy-storage-btn').prop('disabled', isCopyDisabled);

            // Disable non-matching options
            $('#dup-copy-source-id-select option').each(function () {
                const option = $(this);
                const optionType = option.data('stype');
                option.prop('disabled', optionType !== parseInt(mode));

                // Hide options that are disabled
                if (option.prop('disabled')) {
                    option.hide();
                } else {
                    option.show();
                }
            });

            const providerConfigNode =  $('#provider-' + mode);

            $('.provider').hide();
            providerConfigNode.show(animateOverride);

            DupliJs.Storage.BindParsley(providerConfigNode);
            DupliJs.Storage.Autofill(mode);
        }

        $('#dup-storage-form').parsley();
        DupliJs.Storage.ChangeMode(0);
    });
</script>

