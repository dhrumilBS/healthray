<?php

declare(strict_types=1);

namespace Duplicator\Views;

use Duplicator\Package\AutoTune\Attempt;
use Duplicator\Package\AutoTune\AutoTuneSessionEntity;
use Duplicator\Package\Failure\BuildFailureRemedies;

/**
 * Assembles the terminal result presentation for an AutoTune session.
 */
final class AutoTuneResultViewData
{
    /**
     * @param int      $status         Session status
     * @param string   $failureMessage Terminal failure message
     * @param int      $attemptCount   Total attempt count
     * @param int      $changedCount   Changed setting count
     * @param ?Attempt $lastAttempt    Last session attempt
     *
     * @return array<string, mixed>
     */
    public static function get(
        int $status,
        string $failureMessage,
        int $attemptCount,
        int $changedCount,
        ?Attempt $lastAttempt
    ): array {
        if ($status === AutoTuneSessionEntity::STATUS_COMPLETED) {
            return [
                'kind'               => 'completed',
                'description'        => __('The optimized values are already active for future Backups.', 'duplicator'),
                'pillLabel'          => sprintf(
                    _n('%d setting changed', '%d settings changed', $changedCount, 'duplicator'),
                    $changedCount
                ),
                'message'            => '',
                'messageDescription' => '',
                'troubleshooting'    => [],
                'codeSnippet'        => '',
                'docUrl'             => '',
                'docLabel'           => '',
            ];
        }

        if (in_array($status, [AutoTuneSessionEntity::STATUS_ABORTED, AutoTuneSessionEntity::STATUS_TIMEOUT], true)) {
            return [
                'kind'               => 'aborted',
                'description'        => $status === AutoTuneSessionEntity::STATUS_ABORTED
                    ? __('The session was stopped at your request.', 'duplicator')
                    : __('The session stopped before a working configuration was confirmed.', 'duplicator'),
                'pillLabel'          => $status === AutoTuneSessionEntity::STATUS_TIMEOUT
                    ? __('Session timed out', 'duplicator')
                    : __('Session aborted', 'duplicator'),
                'message'            => wp_kses_post($failureMessage),
                'messageDescription' => '',
                'troubleshooting'    => [],
                'codeSnippet'        => '',
                'docUrl'             => '',
                'docLabel'           => '',
            ];
        }

        if ($status === AutoTuneSessionEntity::STATUS_ERROR) {
            return [
                'kind'               => 'failed',
                'description'        => __('AutoTune stopped because its session could not continue.', 'duplicator'),
                'pillLabel'          => __('AutoTune error', 'duplicator'),
                'message'            => wp_kses_post($failureMessage),
                'messageDescription' => '',
                'troubleshooting'    => [],
                'codeSnippet'        => '',
                'docUrl'             => '',
                'docLabel'           => '',
            ];
        }

        $fix     = self::getResultFix($lastAttempt);
        $message = $fix['errorText'] !== ''
            ? $fix['errorText']
            : ($failureMessage !== ''
                ? $failureMessage
                : sprintf(
                    /* translators: %d: number of failed test Backups */
                    _n('%d test Backup failed.', 'All %d test Backups failed.', $attemptCount, 'duplicator'),
                    $attemptCount
                ));

        return [
            'kind'               => 'failed',
            'description'        => __('No configuration completed a test Backup on this server.', 'duplicator'),
            'pillLabel'          => __('No working configuration', 'duplicator'),
            'message'            => wp_kses_post($message),
            'messageDescription' => $fix['description'],
            'troubleshooting'    => $fix['troubleshooting'],
            'codeSnippet'        => $fix['codeSnippet'],
            'docUrl'             => $fix['docUrl'],
            'docLabel'           => $fix['docLabel'],
        ];
    }

    /**
     * Resolve structured guidance from the failed attempt.
     *
     * @param ?Attempt $attempt Last failed attempt
     *
     * @return array{
     *     errorText:string,
     *     description:string,
     *     troubleshooting:string[],
     *     codeSnippet:string,
     *     docUrl:string,
     *     docLabel:string
     * }
     */
    private static function getResultFix(?Attempt $attempt): array
    {
        $empty = [
            'errorText'       => '',
            'description'     => '',
            'troubleshooting' => [],
            'codeSnippet'     => '',
            'docUrl'          => '',
            'docLabel'        => '',
        ];
        if ($attempt === null) {
            return $empty;
        }

        $fix = $attempt->getFailureFix();
        if (
            $fix === [] &&
            $attempt->getFailCode() !== null &&
            BuildFailureRemedies::hasGuidance($attempt->getFailCode())
        ) {
            $fix = BuildFailureRemedies::resolveGuidance(
                $attempt->getFailCode(),
                $attempt->getFailMessage()
            )->getViewData();
        }
        if ($fix === []) {
            return $empty;
        }

        $troubleshooting = isset($fix['troubleshooting']) && is_array($fix['troubleshooting'])
            ? array_values(array_filter($fix['troubleshooting'], 'is_string'))
            : [];
        $troubleshooting = array_map('wp_kses_post', $troubleshooting);

        return [
            'errorText'       => isset($fix['errorText']) ? wp_kses_post((string) $fix['errorText']) : '',
            'description'     => isset($fix['description']) ? wp_kses_post((string) $fix['description']) : '',
            'troubleshooting' => $troubleshooting,
            'codeSnippet'     => isset($fix['codeSnippet']) ? (string) $fix['codeSnippet'] : '',
            'docUrl'          => isset($fix['docUrl']) ? esc_url((string) $fix['docUrl']) : '',
            'docLabel'        => isset($fix['docLabel']) ? wp_strip_all_tags((string) $fix['docLabel']) : '',
        ];
    }
}
