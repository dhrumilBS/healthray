<?php

use Duplicator\Views\UserUIOptions;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$uiOpts = UserUIOptions::getInstance();

$perPage = $uiOpts->get(UserUIOptions::VAL_ACTIVITY_LOG_PER_PAGE);
?>
<fieldset class="screen-options margin-b-20px" >
    <legend>
        <?php esc_html_e('Pagination', 'duplicator'); ?>
    </legend>
    <label for="activity_log_per_page" class="inline-display" >
        <?php esc_html_e('Activity Logs Per Page', 'duplicator'); ?>
    </label>&nbsp;
    <input
        type="number"
        step="1"
        min="1"
        max="999"
        class="screen-per-page inline-display margin-0 width-small"
        name="activity_log_per_page"
        id="activity_log_per_page"
        maxlength="3"
        value="<?php echo esc_html($perPage); ?>"
    >
</fieldset>
<input type="hidden" name="wp_screen_options[option]" value="activity_log_screen_options">
<input type="hidden" name="wp_screen_options[value]" value="val">
<input
    type="submit"
    name="screen-options-apply"
    id="screen-options-apply"
    class="button secondary hollow small margin-0"
    value="Apply"
>
