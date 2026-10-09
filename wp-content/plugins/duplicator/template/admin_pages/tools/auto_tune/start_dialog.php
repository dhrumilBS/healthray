<?php

use Duplicator\Core\Options\Rules\ArchiveEngineRule;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$excludableOptions = $tplMng->getDataValueArrayRequired('excludableOptions');
?>
<dialog id="dupli-autotune-start-dialog" class="dupli-autotune-dialog is-wide">
    <form method="dialog">
        <div class="dupli-autotune-dialog-header">
            <h2><?php esc_html_e('Start AutoTune', 'duplicator'); ?></h2>
            <button class="dupli-autotune-dialog-close" value="cancel" aria-label="<?php esc_attr_e('Close', 'duplicator'); ?>">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
        <div class="dupli-autotune-dialog-body">
            <p>
                <?php esc_html_e(
                    'AutoTune runs real full-site test Backups and changes the build settings shown below.
                    Other Backups are blocked until the session ends.',
                    'duplicator'
                ); ?>
            </p>

            <h3><?php esc_html_e('Server Overview', 'duplicator'); ?></h3>
            <?php $tplMng->render('admin_pages/tools/auto_tune/server_checks'); ?>

            <?php if (count($excludableOptions) > 0) : ?>
                <fieldset class="dupli-autotune-preferences">
                    <legend><?php esc_html_e('Configurations to test', 'duplicator'); ?></legend>
                    <p class="description">
                        <?php esc_html_e(
                            'Clear an optional value to skip it. AutoTune recalculates the starting configuration
                            when the session begins.',
                            'duplicator'
                        ); ?>
                    </p>
                    <?php foreach ($excludableOptions as $groupIndex => $group) : ?>
                        <div class="dupli-autotune-check-group">
                            <div class="dupli-autotune-check-group-label"><?php echo esc_html($group['label']); ?></div>
                            <div class="dupli-autotune-check-group-chips">
                                <?php foreach ($group['options'] as $index => $option) : ?>
                                    <?php $inputId = 'dupli-autotune-option-' . (int) $groupIndex . '-' . (int) $index; ?>
                                    <label
                                        for="<?php echo esc_attr($inputId); ?>"
                                        <?php if ($option['hint'] !== '') : ?>
                                            data-tooltip="<?php echo esc_attr($option['hint']); ?>"
                                        <?php endif; ?>>
                                        <input
                                            id="<?php echo esc_attr($inputId); ?>"
                                            class="dupli-autotune-option"
                                            type="checkbox"
                                            data-option-key="<?php echo esc_attr($option['optionKey']); ?>"
                                            data-option-value="<?php echo esc_attr($option['valueJson']); ?>"
                                            <?php checked($option['checked']); ?>
                                            <?php disabled($option['disabled']); ?>>
                                        <?php echo esc_html($option['label']); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php if ($group['key'] === ArchiveEngineRule::OPTION_KEY && $tplMng->getDataValueBool('isLiteSpeed')) : ?>
                            <p class="black-color display-flex">
                                <i class="fa-solid fa-triangle-exclamation warning-color margin-right-half" aria-hidden="true"></i>
                                <span>
                                    <?php esc_html_e(
                                        'Shell Zip is not selected by default on LiteSpeed web servers due to its high failure rate
                                        and possible interruptions of the zip process by the server.',
                                        'duplicator'
                                    ); ?>
                                    <br>
                                    <?php esc_html_e(
                                        'You can still select it manually, but it is not recommended.',
                                        'duplicator'
                                    ); ?>
                                </span>
                            </p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </fieldset>
            <?php endif; ?>
        </div>
        <div class="dupli-autotune-dialog-actions">
            <button type="submit" value="cancel" class="button hollow secondary margin-bottom-0">
                <?php esc_html_e('Cancel', 'duplicator'); ?>
            </button>
            <button id="dupli-autotune-confirm-start" type="button" class="button primary margin-bottom-0">
                <i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i>
                <?php esc_html_e('Run AutoTune', 'duplicator'); ?>
            </button>
        </div>
    </form>
</dialog>
