<?php

defined("ABSPATH") or die("");

/**
 * Dialog Confirm Progress Template
 *
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */
$id            = $tplMng->getDataValueStringRequired("id");
$function_name = $tplMng->getDataValueStringRequired("function_name");
$progress_text = $tplMng->getDataValueStringRequired("progress_text");
?>

<div class='dupli-dlg-confirm-progress' id="<?php echo esc_attr($id) ?>-progress">
    <br/><br/>
    <i class='fa fa-circle-notch fa-spin fa-lg fa-fw'></i> <?php echo esc_html($progress_text) ?></div>
<script> 
    function <?php echo esc_js($function_name) ?>(obj) 
    {
        (function($,obj){
            console.log($('#<?php echo esc_attr($id) ?>'));
            // Set object for reuse
            var e = $(obj);
            // Check and set progress
            if($('#<?php echo esc_attr($id) ?>-progress'))  $('#<?php echo esc_attr($id) ?>-progress').show();
            // Check and set confirm button
            if($('#<?php echo esc_attr($id) ?>-confirm'))   $('#<?php echo esc_attr($id) ?>-confirm').attr('disabled', 'true');
            // Check and set cancel button
            if($('#<?php echo esc_attr($id) ?>-cancel'))    $('#<?php echo esc_attr($id) ?>-cancel').attr('disabled', 'true');
        }(window.jQuery, obj));
    }
</script>