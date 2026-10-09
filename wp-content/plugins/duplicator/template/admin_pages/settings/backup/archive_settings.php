<?php

use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\OptionsUIHelper;
use Duplicator\Core\Options\Rules\ArchiveEngineRule;
use Duplicator\Core\Options\Rules\CompressionRule;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\Archive\PackageArchive;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$global       = GlobalEntity::getInstance();
$shellZipData = $tplMng->getDataValueArray('shellZipData');
$optionsMng   = OptionsManager::getInstance();

// The form preselects a valid value: the stored one when available, else the first available default
$engineSelection      = OptionsUIHelper::getSelectionValue(ArchiveEngineRule::OPTION_KEY, $global->getBuildMode());
$compressionSelection = OptionsUIHelper::getSelectionValue(
    CompressionRule::OPTION_KEY,
    $global->isArchiveCompressionEnabled()
);
?>

<h3 class="title">
    <?php esc_html_e("Archive", 'duplicator') ?>
</h3>
<hr size="1" />

<label class="lbl-larger">
    <?php echo esc_html($optionsMng->getOptionLabel(ArchiveEngineRule::OPTION_KEY)); ?>
</label>
<div class="margin-bottom-1">
    <?php
    $engineRadios = [
        [
            'value'   => PackageArchive::BUILD_MODE_DUP_ARCHIVE,
            'domId'   => 'archive_build_mode3',
            'spacing' => ' &nbsp; &nbsp;',
        ],
        [
            'value'   => PackageArchive::BUILD_MODE_SHELL_EXEC,
            'domId'   => 'archive_build_mode1',
            'spacing' => ' &nbsp; &nbsp;',
        ],
        [
            'value'   => PackageArchive::BUILD_MODE_ZIP_ARCHIVE,
            'domId'   => 'archive_build_mode2',
            'spacing' => '',
        ],
    ];
    foreach ($engineRadios as $radio) {
        $isAvailable = OptionsUIHelper::isValueAvailable(ArchiveEngineRule::OPTION_KEY, $radio['value']);
        ?>
        <div class="engine-radio <?php echo $isAvailable ? '' : 'engine-radio-disabled'; ?> inline-display">
            <input
                onclick="DupliJs.UI.SetArchiveOptionStates();"
                type="radio"
                name="archive_build_mode"
                id="<?php echo esc_attr($radio['domId']); ?>"
                class="margin-0"
                value="<?php echo (int) $radio['value']; ?>"
                <?php checked($engineSelection == $radio['value']); ?>
                <?php disabled(!$isAvailable) ?>>
            <label for="<?php echo esc_attr($radio['domId']); ?>">
                <?php OptionsUIHelper::renderValueWarning(ArchiveEngineRule::OPTION_KEY, $radio['value'], 'archive_build_mode'); ?>
                <?php echo esc_html($optionsMng->getValueLabel(ArchiveEngineRule::OPTION_KEY, $radio['value'])); ?>
            </label>
            <?php echo $radio['spacing']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
    <?php } ?>

    <br style="clear:both" />
    <?php OptionsUIHelper::renderOptionError(ArchiveEngineRule::OPTION_KEY); ?>

    <!-- DUPARCHIVE -->
    <div class="engine-sub-opts" id="engine-details-3" style="display:none">
        <b class="dupli-engine-sub-opts-title"><?php esc_html_e('DupArchive Options', 'duplicator'); ?></b>
        <?php
        esc_html_e('This option creates a custom Duplicator Archive Format (.daf) archive file.', 'duplicator');
        echo '<br/>  ';
        esc_html_e('This option is fully multi-threaded and recommended for large sites or throttled servers.', 'duplicator');
        echo '<br/>  ';
        printf(
            '%s <a href="' . esc_url(DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-work-with-daf-files-and-the-duparchive-extraction-tool')
                . '" target="_blank">%s</a> ',
            esc_html__('For details on how to use and manually extract the DAF format please see the ', 'duplicator'),
            esc_html__('online documentation.', 'duplicator')
        );
        ?>
    </div>

    <!-- SHELL EXEC  -->
    <div class="engine-sub-opts" id="engine-details-1" style="display:none">
        <b class="dupli-engine-sub-opts-title"><?php esc_html_e('Shell Zip Options', 'duplicator'); ?></b>
        <?php
        $tplMng->render(
            'parts/settings/shellZipMessage',
            $shellZipData
        );
        ?>
    </div>

    <!-- ZIP ARCHIVE -->
    <div class="engine-sub-opts" id="engine-details-2" style="display:none;">
        <b class="dupli-engine-sub-opts-title"><?php esc_html_e('ZipArchive Options', 'duplicator'); ?></b>
        <div class="margin-bottom-1">
            <span><?php esc_html_e("Process Mode", 'duplicator'); ?></span>&nbsp;
            <select name="ziparchive_mode" id="ziparchive_mode" onchange="DupliJs.UI.setZipArchiveMode();" class="inline-display width-medium margin-0">
                <option <?php selected($global->getZipArchiveMode(), PackageArchive::ZIP_MODE_MULTI_THREAD); ?>
                    value="<?php echo (int) PackageArchive::ZIP_MODE_MULTI_THREAD ?>">
                    <?php esc_html_e("Multi-Threaded", 'duplicator'); ?>
                </option>
                <option <?php selected($global->getZipArchiveMode() == PackageArchive::ZIP_MODE_SINGLE_THREAD); ?>
                    value="<?php echo (int) PackageArchive::ZIP_MODE_SINGLE_THREAD ?>">
                    <?php esc_html_e("Single-Threaded", 'duplicator'); ?>
                </option>
            </select>&nbsp;
            <i style="margin-right:7px;" class="fa-solid fa-question-circle fa-sm dark-gray-color"
                data-tooltip-title="<?php esc_attr_e("PHP ZipArchive Mode", 'duplicator'); ?>"
                data-tooltip="<?php
                                esc_attr_e(
                                    'Single-Threaded mode attempts to create the entire archive in one request.  
                Multi-Threaded mode allows the archive to be chunked over multiple requests. 
                Multi-Threaded mode is typically slower but much more reliable especially for larger sites.',
                                    'duplicator'
                                );
                                ?>"></i>
        </div>

        <div id="dupli-ziparchive-mode-st">
            <input type="checkbox" id="ziparchive_validation" name="ziparchive_validation" class="margin-0"
                <?php checked($global->isZipArchiveValidationEnabled()); ?>>
            <label for="ziparchive_validation">Enable file validation</label>
        </div>

        <div id="dupli-ziparchive-mode-mt">
            <span><?php esc_html_e("Buffer Size", 'duplicator'); ?></span>&nbsp;
            <input
                maxlength="4"
                class="inline-display width-small margin-0"
                data-parsley-required data-parsley-errors-container="#ziparchive_chunk_size_error_container"
                data-parsley-min="5" data-parsley-type="number"
                type="text" name="ziparchive_chunk_size_in_mb" id="ziparchive_chunk_size_in_mb"
                value="<?php echo (int) $global->getZipArchiveChunkSize(); ?>">
            <?php esc_html_e('MB', 'duplicator'); ?>
            <?php
            $toolTipContent = __(
                'Buffer size only applies to multi-threaded requests and indicates how large an archive will get before a close is registered. 
            Higher values are faster but can be more unstable based on the host\'s max_execution_time.',
                'duplicator'
            );
            ?>
            <i style="margin-right:7px" class="fa-solid fa-question-circle fa-sm dark-gray-color"
                data-tooltip-title="<?php esc_attr_e("PHP ZipArchive Buffer", 'duplicator'); ?>"
                data-tooltip="<?php echo esc_attr($toolTipContent); ?>">
            </i>
            <div id="ziparchive_chunk_size_error_container" class="duplicator-error-container"></div>
        </div>
    </div>
</div>

<label class="lbl-larger">
    <?php echo esc_html($optionsMng->getOptionLabel(CompressionRule::OPTION_KEY)); ?>
</label>
<div class="margin-bottom-1">
    <input
        type="radio"
        name="archive_compression"
        id="archive_compression_off"
        value="0"
        class="margin-0"
        <?php checked($compressionSelection == false); ?>
        <?php disabled(!OptionsUIHelper::isValueAvailable(CompressionRule::OPTION_KEY, false)); ?>>
    <label for="archive_compression_off">
        <?php OptionsUIHelper::renderValueWarning(CompressionRule::OPTION_KEY, false, 'archive_compression'); ?>
        <?php echo esc_html($optionsMng->getValueLabel(CompressionRule::OPTION_KEY, false)); ?>
    </label> &nbsp;
    <input
        type="radio"
        name="archive_compression"
        id="archive_compression_on"
        value="1"
        class="margin-0"
        <?php checked($compressionSelection == true); ?>
        <?php disabled(!OptionsUIHelper::isValueAvailable(CompressionRule::OPTION_KEY, true)); ?>>
    <label for="archive_compression_on">
        <?php OptionsUIHelper::renderValueWarning(CompressionRule::OPTION_KEY, true, 'archive_compression'); ?>
        <?php echo esc_html($optionsMng->getValueLabel(CompressionRule::OPTION_KEY, true)); ?>
    </label>
    <?php $tipContent = __(
        'This setting controls archive compression. The setting applies to all Archive Engine formats.',
        'duplicator'
    ); ?>&nbsp;
    <i style="margin-right:7px;" class="fa-solid fa-question-circle fa-sm dark-gray-color"
        data-tooltip-title="<?php esc_attr_e("Archive Compression", 'duplicator'); ?>"
        data-tooltip="<?php echo esc_attr($tipContent); ?>">
    </i>
    <?php OptionsUIHelper::renderOptionError(CompressionRule::OPTION_KEY); ?>
</div>