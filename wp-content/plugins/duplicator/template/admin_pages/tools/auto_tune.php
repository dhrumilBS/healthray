<?php

use Duplicator\Core\CapMng;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

if (!CapMng::can(CapMng::CAP_SETTINGS, false)) {
    return;
}

$serverCheckCounts = $tplMng->getDataValueArrayRequired('serverCheckCounts');
?>
<div
    id="dupli-autotune"
    class="dupli-autotune"
    data-state="none"
    data-can-start="<?php echo ($serverCheckCounts['error'] > 0 || !CapMng::can(CapMng::CAP_CREATE, false)) ? '0' : '1'; ?>">
    <h2>
        <?php esc_html_e('AutoTune', 'duplicator'); ?>
    </h2>
    <hr>
    <p class="dupli-autotune-intro">
        <?php esc_html_e('AutoTune finds the most reliable build configuration for this server by running a series of test Backups.', 'duplicator'); ?>
        <br>
        <?php esc_html_e(
            'It starts from the fastest selected configuration and falls back to a more conservative one after each failure.',
            'duplicator'
        ); ?>
    </p>

    <div class="dupli-autotune-layout">
        <div class="dupli-autotune-page">
            <?php $tplMng->render('admin_pages/tools/auto_tune/server_overview'); ?>
            <?php $tplMng->render('admin_pages/tools/auto_tune/tuning_session'); ?>
            <?php $tplMng->render('admin_pages/tools/auto_tune/results'); ?>
        </div>

        <?php $tplMng->render('admin_pages/tools/auto_tune/sidebar'); ?>
    </div>

    <?php $tplMng->render('admin_pages/tools/auto_tune/start_dialog'); ?>
    <?php $tplMng->render('admin_pages/tools/auto_tune/client_templates'); ?>
</div>
