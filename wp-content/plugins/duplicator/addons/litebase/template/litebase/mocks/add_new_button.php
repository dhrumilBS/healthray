<?php

defined('ABSPATH') || exit;

/**
 * Header "Add New" button for LiteBase mock pages.
 *
 * Renders a disabled primary button next to the page title for mock pages
 * (Schedule, Staging) where the real Pro page exposes an "Add New" action.
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */
?>
<span class="dup-new-package-wrapper">
    <button
        type="button"
        class="button primary tiny margin-bottom-0"
        disabled
    >
        <?php esc_html_e('Add New', 'duplicator'); ?>
    </button>
</span>
