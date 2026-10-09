<?php

/**
 * @package   Duplicator
 * @copyright (c) 2026, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$iconId  = $tplMng->getDataValueStringRequired('iconId');
$visible = $tplMng->getDataValueBool('visible');
/** @var string[] $reasons */
$reasons = $tplMng->getDataValueArray('reasons');
$tooltip = implode(' ', $reasons);
?>
<i
    id="<?php echo esc_attr($iconId); ?>"
    class="fas fa-exclamation-triangle fa-sm dup-option-value-warning<?php echo $visible ? '' : ' display-none'; ?>"
    data-tooltip-title="<?php esc_attr_e('Not available on this server', 'duplicator'); ?>"
    data-tooltip="<?php echo esc_attr($tooltip); ?>">
</i>
