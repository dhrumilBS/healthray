<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$counts = $tplMng->getDataValueArrayRequired('serverCheckCounts');

$readyTooltip      = sprintf(
    _n('%d check is ready.', '%d checks are ready.', (int) $counts['ready'], 'duplicator'),
    (int) $counts['ready']
);
$suggestionTooltip = sprintf(
    _n('%d suggestion to review.', '%d suggestions to review.', (int) $counts['suggestion'], 'duplicator'),
    (int) $counts['suggestion']
);
$errorTooltip      = sprintf(
    _n('%d blocking error.', '%d blocking errors.', (int) $counts['error'], 'duplicator'),
    (int) $counts['error']
);
?>
<details id="dupli-autotune-server-card" class="dupli-autotune-card" open>
    <summary>
        <div>
            <h2 class="dupli-autotune-card-title">
                <span class="dupli-autotune-step">1</span>
                <?php esc_html_e('Server Overview', 'duplicator'); ?>
            </h2>
            <p class="dupli-autotune-card-subtitle">
                <?php esc_html_e('What this server offers to the build process, checked before any test Backup runs.', 'duplicator'); ?>
            </p>
        </div>
        <div class="dupli-autotune-pillbar">
            <?php if ($counts['ready'] > 0) : ?>
                <span class="dupli-autotune-pill is-ready" data-tooltip="<?php echo esc_attr($readyTooltip); ?>">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                    <?php echo (int) $counts['ready']; ?>
                </span>
            <?php endif; ?>
            <?php if ($counts['suggestion'] > 0) : ?>
                <span class="dupli-autotune-pill is-suggestion" data-tooltip="<?php echo esc_attr($suggestionTooltip); ?>">
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                    <?php echo (int) $counts['suggestion']; ?>
                </span>
            <?php endif; ?>
            <?php if ($counts['error'] > 0) : ?>
                <span class="dupli-autotune-pill is-error" data-tooltip="<?php echo esc_attr($errorTooltip); ?>">
                    <i class="fa-solid fa-circle-xmark" aria-hidden="true"></i>
                    <?php echo (int) $counts['error']; ?>
                </span>
            <?php endif; ?>
        </div>
        <i class="fa-solid fa-chevron-down dupli-autotune-chevron" aria-hidden="true"></i>
    </summary>

    <?php $tplMng->render('admin_pages/tools/auto_tune/server_checks'); ?>
</details>

<dialog id="dupli-autotune-check-dialog" class="dupli-autotune-dialog">
    <form method="dialog">
        <div class="dupli-autotune-dialog-header">
            <h2 class="dupli-autotune-check-dialog-title"></h2>
            <button class="dupli-autotune-dialog-close" value="cancel" aria-label="<?php esc_attr_e('Close', 'duplicator'); ?>">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
        <div class="dupli-autotune-dialog-body">
            <p class="dupli-autotune-check-dialog-description"></p>
            <ul class="dupli-autotune-check-dialog-status" hidden></ul>
            <div class="dupli-autotune-check-dialog-troubleshoot" hidden>
                <h3><?php esc_html_e('Troubleshooting', 'duplicator'); ?></h3>
                <ul></ul>
            </div>
            <p class="dupli-autotune-check-dialog-action" hidden>
                <a href="#" class="link-style dupli-autotune-check-dialog-link"></a>
            </p>
        </div>
        <div class="dupli-autotune-dialog-actions">
            <button type="submit" value="cancel" class="button hollow secondary margin-bottom-0">
                <?php esc_html_e('Close', 'duplicator'); ?>
            </button>
        </div>
    </form>
</dialog>
