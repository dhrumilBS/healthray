<?php

use Duplicator\Package\NameFormat;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$nameFormat = $tplMng->getDataValueStringRequired('nameFormat');

$helpContent = $tplMng->render('admin_pages/packages/setup/name-format-help', [], false);
?>

<div class="dupli-general-field-head">
    <label for="package-name-format" class="lbl-larger large">
        <?php esc_html_e('Backup Name Format', 'duplicator') ?>:
    </label>
    <i
        class="fa-solid fa-question-circle fa-sm dark-gray-color"
        data-tooltip-title="<?php esc_attr_e("Backup name format", 'duplicator'); ?>"
        data-tooltip="<?php echo esc_attr($helpContent); ?>"
        data-tooltip-width="400"
    ></i>
</div>

<div class="display-flex dupli-name-format-group dupli-general-field-control" >
    <input
        type="text"
        id="package-name-format"
        name="package_name_format"
        class="margin-0"
        data-parsley-errors-container="#template_package_name_error_container"
        data-parsley-required="true"
        value="<?php echo esc_attr($nameFormat); ?>"
        autocomplete="off"
    >
    <select class="dup-format-name-tags width-medium margin-0" >
        <option value="" selected >
            <?php esc_html_e('Dynamic Tags', 'duplicator') ?>
        </option>
        <?php // Same filtered tag list as the help tooltip, addon tags included ?>
        <?php foreach (NameFormat::getTagsDescriptions() as $format => $description) { ?>
            <option value="%<?php echo esc_attr($format); ?>%" title="<?php echo esc_attr($description); ?>">
                %<?php echo esc_html($format); ?>%
            </option>
        <?php } ?>
    </select>
</div>
<div id="template_package_name_error_container" class="duplicator-error-container"></div>

<script>
    jQuery(document).ready(function($) {
        $('.dup-format-name-tags').change(function(e) {
            e.stopPropagation();

            if ($(this).val() === '') {
                return;
            }

            let input = $('#package-name-format');
            let currentValue = input.val();
            let newValue = currentValue + $(this).val();
            input.val(newValue);

            $(this).val('');
        });
    })
</script>
