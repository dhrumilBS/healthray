<?php

declare(strict_types=1);

namespace Duplicator\Package\Failure;

use Duplicator\Models\Fix;

/**
 * Adds code-specific guidance to a generated build failure fix.
 */
interface FailureGuidanceProviderInterface
{
    /**
     * @param int $failureCode DupliException::CODE_* value
     *
     * @return bool
     */
    public static function supports(int $failureCode): bool;

    /**
     * @param Fix $fix         Generated failure fix
     * @param int $failureCode DupliException::CODE_* value
     *
     * @return Fix
     */
    public static function apply(Fix $fix, int $failureCode): Fix;
}
