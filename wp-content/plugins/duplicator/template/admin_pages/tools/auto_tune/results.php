<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$logsUrl    = $tplMng->getDataValueStringRequired('logsUrl');
$supportUrl = $tplMng->getDataValueStringRequired('supportUrl');
?>
<details
    id="dupli-autotune-results-card"
    class="dupli-autotune-card"
    data-session-states="completed failed aborted"
    hidden>
    <summary>
        <div>
            <h2 class="dupli-autotune-card-title">
                <span class="dupli-autotune-step">3</span>
                <?php esc_html_e('Results', 'duplicator'); ?>
            </h2>
            <p id="dupli-autotune-results-description" class="dupli-autotune-card-subtitle"></p>
        </div>
        <div class="dupli-autotune-pillbar">
            <span id="dupli-autotune-results-pill" class="dupli-autotune-pill"></span>
        </div>
        <i class="fa-solid fa-chevron-down dupli-autotune-chevron" aria-hidden="true"></i>
    </summary>

    <table id="dupli-autotune-results-table" class="dupli-autotune-report" hidden>
        <thead>
            <tr>
                <th><?php esc_html_e('Setting', 'duplicator'); ?></th>
                <th><?php esc_html_e('Before AutoTune', 'duplicator'); ?></th>
                <th><?php esc_html_e('After AutoTune', 'duplicator'); ?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div id="dupli-autotune-failure-report" hidden>
        <div class="dupli-autotune-check">
            <span id="dupli-autotune-result-icon" class="dupli-autotune-check-icon is-error">
                <i class="fa-solid fa-circle-xmark" aria-hidden="true"></i>
            </span>
            <div class="dupli-autotune-check-body">
                <h3 id="dupli-autotune-result-title" class="dupli-autotune-check-title"></h3>
                <div id="dupli-autotune-result-error-details" class="dupli-fix-section dupli-fix-error-item">
                    <p>
                        <b><?php esc_html_e('Error:', 'duplicator'); ?></b>
                        <span id="dupli-autotune-result-message"></span>
                    </p>
                    <div id="dupli-autotune-result-fix-description" class="dupli-fix-description" hidden></div>
                </div>
                <div id="dupli-autotune-result-troubleshooting-section" class="dupli-fix-section margin-top-1" hidden>
                    <p><b><?php esc_html_e('Troubleshooting:', 'duplicator'); ?></b></p>
                    <ul id="dupli-autotune-result-troubleshooting" class="dupli-simple-style-disc"></ul>
                </div>
                <div id="dupli-autotune-result-code-section" class="dupli-fix-section margin-top-1" hidden>
                    <div class="dupli-fix-code-box">
                        <div class="dupli-fix-code-header">
                            <b><?php esc_html_e('Suggested server configuration (.htaccess)', 'duplicator'); ?></b>
                            <button
                                id="dupli-autotune-result-code-copy"
                                type="button"
                                class="button secondary hollow xtiny">
                                <i class="far fa-copy" aria-hidden="true"></i>
                                <?php esc_html_e('Copy', 'duplicator'); ?>
                            </button>
                        </div>
                        <pre class="dupli-fix-code"><code id="dupli-autotune-result-code"></code></pre>
                    </div>
                </div>
                <div id="dupli-autotune-result-doc-section" class="dupli-fix-section margin-top-1" hidden>
                    <p><b><?php esc_html_e('Online documentation:', 'duplicator'); ?></b></p>
                    <ul class="dupli-simple-style-disc">
                        <li>
                            <a
                                id="dupli-autotune-result-doc-link"
                                href="#"
                                target="_blank"
                                rel="noopener noreferrer"></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="dupli-autotune-check" data-autotune-failure-guidance>
            <span class="dupli-autotune-check-icon is-suggestion">
                <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
            </span>
            <div class="dupli-autotune-check-body">
                <div class="dupli-autotune-check-title"><?php esc_html_e('Review the suggestions in Server Overview', 'duplicator'); ?></div>
                <div class="dupli-autotune-check-description">
                    <?php esc_html_e('Enabling suggested options widens what AutoTune can test on the next run.', 'duplicator'); ?>
                </div>
            </div>
        </div>
        <div class="dupli-autotune-check" data-autotune-failure-guidance>
            <span class="dupli-autotune-check-icon is-suggestion">
                <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
            </span>
            <div class="dupli-autotune-check-body">
                <div class="dupli-autotune-check-title"><?php esc_html_e('Check the build logs', 'duplicator'); ?></div>
                <div class="dupli-autotune-check-description">
                    <?php esc_html_e('The Duplicator logs show where each test Backup stopped.', 'duplicator'); ?>
                </div>
            </div>
            <span class="dupli-autotune-check-action">
                <a href="<?php echo esc_url($logsUrl); ?>" class="link-style">
                    <?php esc_html_e('Duplicator Logs', 'duplicator'); ?>
                </a>
            </span>
        </div>
        <div class="dupli-autotune-check" data-autotune-failure-guidance>
            <span class="dupli-autotune-check-icon is-suggestion">
                <i class="fa-solid fa-life-ring" aria-hidden="true"></i>
            </span>
            <div class="dupli-autotune-check-body">
                <div class="dupli-autotune-check-title"><?php esc_html_e('Contact support', 'duplicator'); ?></div>
                <div class="dupli-autotune-check-description">
                    <?php esc_html_e('Share the session details with the Duplicator team for a server-specific diagnosis.', 'duplicator'); ?>
                </div>
            </div>
            <span class="dupli-autotune-check-action">
                <a href="<?php echo esc_url($supportUrl); ?>" target="_blank" rel="noopener noreferrer" class="link-style">
                    <?php esc_html_e('Get help', 'duplicator'); ?>
                    <i class="fa-solid fa-arrow-up-right-from-square fa-xs" aria-hidden="true"></i>
                </a>
            </span>
        </div>
    </div>
</details>
