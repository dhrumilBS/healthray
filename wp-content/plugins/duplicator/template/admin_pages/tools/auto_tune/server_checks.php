<?php

defined('ABSPATH') || exit;

/**
 * Server check groups with detail chips, shared between the Server Overview
 * card and the start dialog.
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$groups = $tplMng->getDataValueArrayRequired('serverCheckGroups');
?>
<div class="dupli-autotune-checks">
    <?php foreach ($groups as $group) : ?>
        <div class="dupli-autotune-check-group">
            <div class="dupli-autotune-check-group-label"><?php echo esc_html($group['label']); ?></div>
            <div class="dupli-autotune-check-group-chips">
                <?php foreach ($group['items'] as $check) : ?>
                    <button
                        type="button"
                        class="dupli-autotune-chip is-<?php echo esc_attr($check['severity']); ?>"
                        data-title="<?php echo esc_attr($check['title']); ?>"
                        data-description="<?php echo esc_attr($check['description']); ?>"
                        data-status-list="<?php echo esc_attr((string) wp_json_encode($check['statusList'])); ?>"
                        data-troubleshoot="<?php echo esc_attr((string) wp_json_encode($check['troubleshoot'])); ?>"
                        data-action-url="<?php echo esc_url($check['actionUrl']); ?>"
                        data-action-label="<?php echo esc_attr($check['actionLabel']); ?>"
                        data-external="<?php echo $check['external'] ? '1' : '0'; ?>">
                        <?php if ($check['severity'] === 'ready') : ?>
                            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        <?php elseif ($check['severity'] === 'error') : ?>
                            <i class="fa-solid fa-circle-xmark" aria-hidden="true"></i>
                        <?php else : ?>
                            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                        <?php endif; ?>
                        <?php echo esc_html($check['valueLabel']); ?>
                        <i class="fa-solid fa-circle-info dupli-autotune-chip-info" aria-hidden="true"></i>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
