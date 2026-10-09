<?php

/**
 * Unified failure-message notice template.
 *
 * Renders one group of fixes sharing the same title: error messages,
 * suggested actions, troubleshooting hints, documentation references
 * and the optional Activity Log link.
 */

use Duplicator\Models\Fix;
use Duplicator\Views\KsesHelper;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$title = $tplMng->getDataValueStringRequired('title');
/** @var array<string, array{type:string,errorText:string,description:string,suggestionText:string,troubleshooting:string[],codeSnippet:string,docUrl:string,docLabel:string}> $fixes */
$fixes          = $tplMng->getDataValueArrayRequired('fixes');
$activityLogUrl = $tplMng->getDataValueString('activityLogUrl', '');
$autoTuneUrl    = $tplMng->getDataValueString('autoTuneUrl', '');

$actions = array_filter($fixes, fn(array $fix): bool => $fix['type'] === Fix::TYPE_ACTION);

$troubleshooting = [];
$codeSnippets    = [];
$docReferences   = [];
foreach ($fixes as $fix) {
    $troubleshooting = array_merge($troubleshooting, $fix['troubleshooting']);
    if ($fix['codeSnippet'] !== '') {
        $codeSnippets[$fix['codeSnippet']] = $fix['codeSnippet'];
    }
    if ($fix['docUrl'] !== '') {
        $docReferences[$fix['docUrl']] = $fix['docLabel'];
    }
}
$troubleshooting = array_unique($troubleshooting);
?>
<span class="dashicons dashicons-warning"></span>
<div class="dup-sub-content">
    <h3><?php echo esc_html($title); ?></h3>
    <?php if (count($fixes) === 1) : ?>
        <?php $key = array_key_first($fixes); ?>
        <div class="dupli-fix-section dupli-fix-error-item" data-fix-key="<?php echo esc_attr((string) $key); ?>">
            <p>
                <b><?php esc_html_e('Error:', 'duplicator'); ?></b>
                <?php echo wp_kses_post($fixes[$key]['errorText']); ?>
            </p>
            <?php if ($fixes[$key]['description'] !== '') : ?>
                <div class="dupli-fix-description">
                    <?php echo wp_kses_post($fixes[$key]['description']); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <div class="dupli-fix-section">
            <p><b><?php esc_html_e('Error(s):', 'duplicator'); ?></b></p>
            <ul class="dupli-simple-style-disc">
                <?php foreach ($fixes as $key => $fix) : ?>
                    <li class="dupli-fix-error-item" data-fix-key="<?php echo esc_attr($key); ?>">
                        <?php echo wp_kses_post($fix['errorText']); ?>
                        <?php if ($fix['description'] !== '') : ?>
                            <div class="dupli-fix-description">
                                <?php echo wp_kses_post($fix['description']); ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if ($activityLogUrl !== '') : ?>
        <p class="dupli-fix-section">
            <?php printf(
                wp_kses(
                    _x(
                        'For more details, check the %1$sActivity Log%2$s.',
                        '1 and 2 are opening and closing anchor tags',
                        'duplicator'
                    ),
                    KsesHelper::GEN_TAGS
                ),
                '<a href="' . esc_url($activityLogUrl) . '"><b>',
                '</b></a>'
            ); ?>
        </p>
    <?php endif; ?>
    <?php if (count($troubleshooting) > 0 || $autoTuneUrl !== '' || count($actions) > 0) : ?>
        <div class="dupli-fix-section">
            <p><b><?php esc_html_e('Troubleshooting:', 'duplicator'); ?></b></p>
            <ul class="dupli-simple-style-disc">
                <?php foreach ($troubleshooting as $hint) : ?>
                    <li><?php echo wp_kses_post($hint); ?></li>
                <?php endforeach; ?>
                <?php if ($autoTuneUrl !== '') : ?>
                    <li>
                        <b><?php esc_html_e('Recommended:', 'duplicator'); ?></b>
                        <?php printf(
                            wp_kses(
                                _x(
                                    'Run %1$sAutoTune%2$s to automatically find a working build configuration.',
                                    '1 and 2 are opening and closing anchor tags',
                                    'duplicator'
                                ),
                                KsesHelper::GEN_TAGS
                            ),
                            '<a href="' . esc_url($autoTuneUrl) . '">',
                            '</a>'
                        ); ?>
                    </li>
                <?php endif; ?>
                <?php foreach ($actions as $key => $fix) : ?>
                    <li class="dupli-quick-fix-action" data-fix-key="<?php echo esc_attr($key); ?>">
                        <b><?php esc_html_e('Quick fix:', 'duplicator'); ?></b>
                        <?php echo wp_kses_post($fix['suggestionText']); ?>
                        <button
                            type="button"
                            class="button secondary hollow xtiny dupli-quick-fix"
                            data-fix-key="<?php echo esc_attr($key); ?>"
                        ><?php esc_html_e('Apply', 'duplicator'); ?></button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if (count($codeSnippets) > 0) : ?>
        <div class="dupli-fix-section">
            <?php foreach ($codeSnippets as $snippet) : ?>
                <div class="dupli-fix-code-box">
                    <div class="dupli-fix-code-header">
                        <b><?php esc_html_e('Suggested server configuration (.htaccess)', 'duplicator'); ?></b>
                        <button
                            type="button"
                            class="button secondary hollow xtiny"
                            data-dup-copy-value="<?php echo esc_attr($snippet); ?>">
                            <i class="far fa-copy" aria-hidden="true"></i>
                            <?php esc_html_e('Copy', 'duplicator'); ?>
                        </button>
                    </div>
                    <pre class="dupli-fix-code"><code><?php echo esc_html($snippet); ?></code></pre>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (count($docReferences) > 0) : ?>
        <div class="dupli-fix-section">
            <p><b><?php esc_html_e('Online documentation:', 'duplicator'); ?></b></p>
            <ul class="dupli-simple-style-disc">
                <?php foreach ($docReferences as $docUrl => $docLabel) : ?>
                    <li>
                        <a href="<?php echo esc_url($docUrl); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html($docLabel); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
