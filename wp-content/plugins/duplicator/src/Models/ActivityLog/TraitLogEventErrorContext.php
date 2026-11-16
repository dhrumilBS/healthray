<?php

declare(strict_types=1);

namespace Duplicator\Models\ActivityLog;

use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\WpUtils\WpArchiveUtils;
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
     * Failure reason and fixes captured on this error event, or on the error children of an error parent row, as plain text with relative paths.
     *
     * @return array{reason:string,fixes:list<array{error:string,troubleshooting:string[],code_snippet:string}>}
     */
    public function getFailureDetails(): array
    {
        $errorData = $this->getErrorDataForDisplay('quickFixes', 'failureReason');

        $fixes = [];
        foreach ($errorData['quickFixes'] as $fix) {
            $fix     = self::readLegacyQuickFix((array) $fix);
            $fixes[] = [
                'error'           => self::htmlToPlainText($fix['errorText']),
                'troubleshooting' => array_map([self::class, 'htmlToPlainText'], $fix['troubleshooting']),
                'code_snippet'    => $fix['codeSnippet'],
            ];
        }

        return [
            'reason' => self::toPlainText(implode(' ', $errorData['failureReason'])),
            'fixes'  => $fixes,
        ];
    }

    /**
     * Fix text is stored as HTML, some of it with escaped names.
     *
     * @param string $html Stored text
     *
     * @return string
     */
    private static function htmlToPlainText(string $html): string
    {
        return self::toPlainText(html_entity_decode(wp_strip_all_tags($html), ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Text with the site's base paths made relative and its whitespace collapsed.
     *
     * @param string $text Text
     *
     * @return string
     */
    private static function toPlainText(string $text): string
    {
        // Source strings wrap across indented lines, which HTML collapses and plain text does not.
        return trim((string) preg_replace('/\s+/u', ' ', WpArchiveUtils::stripOriginalBasePaths($text)));
    }

    /**
     * Fetch captured error-data keys from this event, or per key from the newest error children of an error parent row.
     *
     * @param string ...$keys Data keys (e.g. 'backupLogContext', 'quickFixes')
     *
     * @return array<string, array<mixed>> Keyed by the requested key; missing keys return [].
     */
    private function getErrorDataForDisplay(string ...$keys): array
    {
        $result = array_fill_keys($keys, []);

        if ($this->subType === self::SUB_TYPE_ERROR) {
            foreach ($keys as $key) {
                if (!empty($this->data[$key])) {
                    $result[$key] = (array) $this->data[$key];
                }
            }
            return $result;
        }

        if ($this->parentId === 0 && $this->severity === self::SEVERITY_ERROR) {
            $childEvents = self::getChildLogsByParentId($this->getId(), 'DESC');

            foreach ($childEvents as $childEvent) {
                if ($childEvent->subType !== self::SUB_TYPE_ERROR) {
                    continue;
                }
                foreach ($keys as $key) {
                    if (empty($result[$key]) && !empty($childEvent->data[$key])) {
                        $result[$key] = (array) $childEvent->data[$key];
                    }
                }
                if (!in_array([], $result, true)) {
                    break;
                }
            }
        }

        return $result;
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
