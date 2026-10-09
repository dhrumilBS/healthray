<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
?>
<details
    id="dupli-autotune-session-card"
    class="dupli-autotune-card"
    data-session-states="running completed failed aborted"
    hidden>
    <summary>
        <div>
            <h2 class="dupli-autotune-card-title">
                <span class="dupli-autotune-step">2</span>
                <?php esc_html_e('Tuning Session', 'duplicator'); ?>
            </h2>
            <p class="dupli-autotune-card-subtitle">
                <?php esc_html_e('Every attempt runs a real test Backup and falls back to a more conservative configuration on failure.', 'duplicator'); ?>
            </p>
        </div>
        <div class="dupli-autotune-pillbar">
            <span id="dupli-autotune-session-pill" class="dupli-autotune-pill"></span>
        </div>
        <i class="fa-solid fa-chevron-down dupli-autotune-chevron" aria-hidden="true"></i>
    </summary>

    <div id="dupli-autotune-attempts" class="dupli-autotune-attempts" aria-live="polite"></div>
</details>
