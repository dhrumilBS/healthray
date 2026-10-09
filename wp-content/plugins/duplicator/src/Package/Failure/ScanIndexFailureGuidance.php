<?php

declare(strict_types=1);

namespace Duplicator\Package\Failure;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Models\Fix;

/**
 * Adds recovery guidance to interrupted scan-index failures.
 */
final class ScanIndexFailureGuidance implements FailureGuidanceProviderInterface
{
    /** @var int[] */
    private const FAILURE_CODES = [
        DupliException::CODE_SCAN_INDEX_INVALID,
        DupliException::CODE_INDEX_FILE_EMPTY,
        DupliException::CODE_INDEX_FILE_MISSING,
    ];

    /**
     * @param int $failureCode DupliException::CODE_* value
     *
     * @return bool
     */
    public static function supports(int $failureCode): bool
    {
        return in_array($failureCode, self::FAILURE_CODES, true);
    }

    /**
     * @param Fix $fix         Generated failure fix
     * @param int $failureCode DupliException::CODE_* value
     *
     * @return Fix
     */
    public static function apply(Fix $fix, int $failureCode): Fix
    {
        if (!self::supports($failureCode)) {
            return $fix;
        }

        $troubleshooting   = $fix->getTroubleshooting();
        $troubleshooting[] = __(
            'The background file scan may have been interrupted before its index was completed or validated.
            Run the Backup again. If the failure repeats, ask your hosting provider to check for
            terminated background PHP processes or removed Duplicator temporary files.',
            'duplicator'
        );

        return $fix
            ->setTroubleshooting($troubleshooting)
            ->setDocReference(
                DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-resolve-scanner-warnings-errors-and-timeout-issues/',
                __('How to resolve scanner warnings, errors and timeout issues', 'duplicator')
            );
    }
}
