<?php

declare(strict_types=1);

namespace Duplicator\Models\ActivityLog;

use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\Views\TplMng;
use Duplicator\Models\Fix;
use Duplicator\Models\FixesEntity;
use Duplicator\Package\AbstractPackage;
use Duplicator\Utils\Logging\DupLog;

/**
 * Shared error-context capture and rendering for activity-log events.
 *
 * @phpstan-import-type FixViewData from Fix
 */
trait TraitLogEventErrorContext
{
    /**
     * Read the last $maxLines of the package log, with $sentinel as fallback.
     *
     * @param AbstractPackage $package  Package
     * @param int             $maxLines Max trailing lines to capture
     * @param string          $sentinel Single-line fallback when the log is unavailable
     *
     * @return string[]
     */
    private function captureLogTail(AbstractPackage $package, int $maxLines, string $sentinel): array
    {
        $lines = DupLog::getLogContext($package->getNameHash(), $maxLines);
        return empty($lines) ? [$sentinel] : $lines;
    }

    /**
     * Snapshot the display-only fix data.
     *
     * @return array<int, FixViewData>
     */
    private function captureQuickFixes(): array
    {
        return FixesEntity::getInstance()->getViewData();
    }

    /**
     * Render the Error Context block via the shared error_log_context partial.
     *
     * @param string[] $logLines    Lines to display
     * @param string   $logFileName Filename used to build the full-log link
     * @param string   $footerText  Pre-built footer string
     *
     * @return void
     */
    private function renderLogContext(array $logLines, string $logFileName, string $footerText): void
    {
        TplMng::getInstance()->render(
            'admin_pages/activity_log/parts/error_log_context',
            [
                'logLines'   => $logLines,
                'logUrl'     => ToolsPageController::getLogViewerURL($logFileName, false),
                'footerText' => $footerText,
            ]
        );
    }

    /**
     * Read a persisted fix entry, converting the legacy RecommendedFix shape
     * (error_text/fix_text and an int type where 1 = quick fix) and the
     * legacy notice suggestionText to the current troubleshooting list.
     *
     * @param array<string, mixed> $fix Persisted fix entry
     *
     * @return array{type:string,errorText:string,suggestionText:string,troubleshooting:string[],codeSnippet:string}
     */
    private static function readLegacyQuickFix(array $fix): array
    {
        if (isset($fix['error_text']) || isset($fix['fix_text'])) {
            $normalized = [
                'type'            => ((int) ($fix['type'] ?? 0)) === 1 ? Fix::TYPE_ACTION : Fix::TYPE_NOTICE,
                'errorText'       => (string) ($fix['error_text'] ?? ''),
                'suggestionText'  => (string) ($fix['fix_text'] ?? ''),
                'troubleshooting' => [],
                'codeSnippet'     => '',
            ];
        } else {
            $normalized = [
                'type'            => (string) ($fix['type'] ?? Fix::TYPE_NOTICE),
                'errorText'       => (string) ($fix['errorText'] ?? ''),
                'suggestionText'  => (string) ($fix['suggestionText'] ?? ''),
                'troubleshooting' => array_map('strval', (array) ($fix['troubleshooting'] ?? [])),
                'codeSnippet'     => (string) ($fix['codeSnippet'] ?? ''),
            ];
        }

        if ($normalized['type'] === Fix::TYPE_NOTICE && $normalized['suggestionText'] !== '') {
            $normalized['troubleshooting'][] = $normalized['suggestionText'];
            $normalized['suggestionText']    = '';
        }

        return $normalized;
    }

    /**
     * Render the reason block. Action suggestions are suppressed because they can't be applied from the historical event.
     *
     * @param array<int, array<string, mixed>> $quickFixes Captured fixes
     *
     * @return void
     */
    private function renderQuickFixesReason(array $quickFixes): void
    {
        if (empty($quickFixes)) {
            return;
        }
        ?>
        <hr>
        <div class="dup-log-type-wrapper">
            <strong><?php esc_html_e('Reason:', 'duplicator'); ?></strong>
        </div>
        <?php foreach ($quickFixes as $fix) : ?>
            <?php $fix = self::readLegacyQuickFix((array) $fix); ?>
            <div class="dup-log-type-wrapper">
                <strong><?php echo wp_kses_post($fix['errorText']); ?></strong>
                <?php foreach ($fix['troubleshooting'] as $hint) : ?>
                    <div><?php echo wp_kses_post($hint); ?></div>
                <?php endforeach; ?>
                <?php if ($fix['codeSnippet'] !== '') : ?>
                    <pre class="dupli-fix-code"><code><?php echo esc_html($fix['codeSnippet']); ?></code></pre>
                <?php endif; ?>
            </div>
        <?php endforeach;
    }
}
