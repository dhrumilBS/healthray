<?php

declare(strict_types=1);

namespace Duplicator\Libs\Snap;

/**
 * Assigns the first external trace caller to an exception created by a factory.
 */
trait TraitFactoryExceptionOrigin
{
    /**
     * Use the first external trace frame with complete source metadata as the native origin.
     *
     * @param string $factoryFile File containing the exception factory
     *
     * @return void
     */
    private function useFactoryCallerOrigin(string $factoryFile): void
    {
        foreach ($this->getTrace() as $frame) {
            if (
                !isset($frame['file'], $frame['line']) ||
                !is_string($frame['file']) ||
                !is_int($frame['line']) ||
                $frame['file'] === $factoryFile
            ) {
                continue;
            }

            $this->file = $frame['file'];
            $this->line = $frame['line'];
            return;
        }
    }
}
