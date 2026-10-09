<?php

use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Views\TplMng;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 *
 * Mock body markup mirrors the real Pro Recovery (Tools > Recovery) panel so the
 * blurred silhouette behind the upgrade popup looks identical. Content is static
 * and not localized: it is rendered inside .dup-mock-blur and is not read.
 */

$recoveryPoint = $tplMng->getDataValueArrayRequired('recoveryPoint');
$backups       = $tplMng->getDataValueArrayRequired('backups');
?>
<div class="dup-mock-blur" aria-hidden="true">
    <h2 class="margin-bottom-0">
        <i class="fas fa-house-fire" aria-hidden="true"></i>&nbsp;Disaster Recovery
    </h2>
    <hr>
    <p class="margin-bottom-1">
        Quickly restore this site to a specific Backup in time.
        <span class="link-style">Need more help?</span>
    </p>

    <div class="dupli-recovery-details-max-width-wrapper">
        <div class="dupli-recovery-widget-wrapper">
            <div class="dupli-recovery-point-details margin-bottom-1">
                <div class="dupli-recovery-active-link-wrapper">
                    <div class="dupli-recovery-active-link-header">
                        <i class="fas fa-house-fire main-icon" aria-hidden="true"></i>
                        <div class="main-title">Disaster Recovery Backup is Set</div>
                        <div class="main-subtitle margin-bottom-1">
                            <b>Backup Age:</b>&nbsp;
                            <span class="dupli-recovery-status green">
                                <?php echo esc_html($recoveryPoint['ageLabel']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="dupli-recovery-package-info margin-bottom-1">
                        <table>
                            <tbody>
                                <tr>
                                    <td>Name:</td>
                                    <td><b><?php echo esc_html($recoveryPoint['name']); ?></b></td>
                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td><b><?php echo esc_html($recoveryPoint['date']); ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="dupli-recovery-point-selector">
                <div class="dupli-recovery-point-selector-area-wrapper">
                    <span class="dupli-opening-packages-windows">
                        <span class="link-style">[Create New]</span>
                    </span>
                    <label>
                        <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
                        <b>Step 1 :</b> <i>Choose Recovery Point Archive</i>
                    </label>
                    <div class="dupli-recovery-point-selector-area">
                        <select class="recovery-select">
                            <option value=""> -- Not selected -- </option>
                            <?php foreach ($backups as $groupLabel => $options) : ?>
                                <optgroup label="<?php echo esc_attr($groupLabel); ?>">
                                    <?php foreach ($options as $option) : ?>
                                        <option value="<?php echo esc_attr($option['id']); ?>">
                                            <?php echo esc_html($option['label']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="button secondary hollow small">Reset</button>
                        <button type="button" class="button primary small">Set</button>
                    </div>
                </div>

                <div class="dupli-recovery-point-actions">
                    <label>
                        <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
                        <b>Step 2 :</b> <i>Copy Recovery URL &amp; Store in Safe Place</i>
                    </label>
                    <div class="copy-link">
                        <div class="content">
                            <?php echo esc_html($recoveryPoint['url']); ?>
                        </div>
                        <i class="far fa-copy copy-icon" aria-hidden="true"></i>
                    </div>
                    <div class="dupli-recovery-buttons">
                        <span class="button primary hollow dupli-launch small">
                            <i class="fas fa-undo-alt" aria-hidden="true"></i>&nbsp;Restore Backup
                        </span>
                        <span class="button primary hollow small dupli-recovery-download-launcher">
                            <i class="fa fa-rocket" aria-hidden="true"></i>&nbsp;Download Launcher
                        </span>
                        <span class="button primary small hollow dupli-recovery-copy-url">
                            <i class="far fa-copy copy-icon" aria-hidden="true"></i>&nbsp;Copy LINK
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
TplMng::getInstance()->render('litebase/mocks/static-popup', [
    'title'       => __('Recover your site in seconds with Recovery Points!', 'duplicator'),
    'warningText' => __('Recovery Points are not available in Duplicator Lite!', 'duplicator'),
    'paragraphs'  => [
        __(
            'Recovery Points provide protection against mistakes and bad updates by letting you quickly rollback your system to a known, good state.',
            'duplicator'
        ),
    ],
    'upgradeUrl'  => LiteBaseLinks::getUpgradeUrl('blurred-mocks', 'Recovery'),
]);
