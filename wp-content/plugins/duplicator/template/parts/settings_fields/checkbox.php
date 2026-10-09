<?php

/**
 * Duplicator messages sections
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$fieldLabel         = $tplMng->getDataValueStringRequired('fieldLabel');
$fieldName          = $tplMng->getDataValueStringRequired('fieldName');
$fieldChecked       = $tplMng->getDataValueBool('fieldChecked');
$fieldCheckboxLabel = $tplMng->getDataValueStringRequired('fieldCheckboxLabel');
$fieldDescription   = $tplMng->getDataValueString('fieldDescription');
?>
<tr>
    <th scope="row">
        <?php echo esc_html($fieldLabel); ?>
    </th>
    <td>
        <fieldset>
            <legend class="screen-reader-text">
                <span><?php echo esc_html($fieldLabel); ?></span>
            </legend>
            <label>
                <input
                    id="<?php echo esc_attr('dup-id-' . $fieldName); ?>"
                    name="<?php echo esc_attr($fieldName); ?>"
                    type="checkbox"
                    value="1"
                    <?php checked($fieldChecked); ?>
                    >
                    <?php echo esc_html($fieldCheckboxLabel); ?>
            </label>
            <?php if (!empty($fieldDescription)) { ?>
                <p class="description">
                    <?php echo esc_html($fieldDescription); ?>
                </p>
            <?php } ?>
        </fieldset>
    </td>
</tr>
