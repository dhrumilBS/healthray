<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
?>
<aside class="dupli-autotune-side" aria-live="polite">
    <div class="dupli-autotune-side-heading"><?php esc_html_e('Session', 'duplicator'); ?></div>
    <div class="dupli-autotune-side-row">
        <span class="dupli-autotune-side-key"><?php esc_html_e('Status', 'duplicator'); ?></span>
        <span class="dupli-autotune-side-value">
            <span id="dupli-autotune-status-dot" class="dupli-autotune-status-dot"></span>
            <span id="dupli-autotune-status-label"></span>
        </span>
    </div>
    <div class="dupli-autotune-side-row">
        <span class="dupli-autotune-side-key"><?php esc_html_e('Attempts', 'duplicator'); ?></span>
        <span class="dupli-autotune-side-value">
            <span id="dupli-autotune-attempt-count">—</span>
            <span id="dupli-autotune-attempt-summary" class="dupli-autotune-side-sub"></span>
        </span>
    </div>
    <div class="dupli-autotune-side-row">
        <span class="dupli-autotune-side-key"><?php esc_html_e('Duration', 'duplicator'); ?></span>
        <span class="dupli-autotune-side-value">
            <span id="dupli-autotune-duration">—</span>
            <span class="dupli-autotune-side-sub">
                <?php esc_html_e('max', 'duplicator'); ?> <span id="dupli-autotune-max-duration"></span>
            </span>
        </span>
    </div>
    <div class="dupli-autotune-side-actions">
        <button id="dupli-autotune-action" type="button" class="button primary expanded margin-bottom-0">
            <i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i>
            <span></span>
        </button>
    </div>
    <p id="dupli-autotune-side-note" class="dupli-autotune-side-note"></p>
</aside>
