<?php

/**
 * Build step iterator for extensible build sequencing
 */

declare(strict_types=1);

namespace Duplicator\Package\Create;

use Duplicator\Package\AbstractPackage;
use Iterator;

/**
 * Determines the next build step via package state and an extensibility filter.
 *
 * Callers are responsible for firing the action hook for the current step:
 *   do_action('duplicator_backup_build_step', $iterator->current(), $package, [$iterator, 'stop'])
 *
 * Usage:
 *   while ($iterator->valid()) {
 *       do_action('duplicator_backup_build_step', $iterator->current(), $package, [$iterator, 'stop']);
 *       $iterator->next();
 *   }
 *
 * @implements Iterator<string|null, string|null>
 */
class BuildStepIterator implements Iterator
{
    protected AbstractPackage $package;

    protected bool $shouldContinue = true;

    const STEP_INITIALIZE        = 'initialize';
    const STEP_DATABASE          = 'database';
    const STEP_DATABASE_COMPRESS = 'database_compress';
    const STEP_ARCHIVE           = 'archive';
    const STEP_INSTALLER         = 'installer';
    const STEP_FINALIZE          = 'finalize';

    /**
     * Constructor
     *
     * @param AbstractPackage $package The package
     *
     * @return void
     */
    public function __construct(AbstractPackage $package)
    {
        $this->package = $package;
    }

    /**
     * Return the base (unfiltered) step name determined from package state.
     *
     * @return ?string Step name, or null when all steps have completed
     */
    public function current(): ?string
    {
        $current  = null;
        $progress = $this->package->build_progress;
        if (!$progress->initialized) {
            $current = self::STEP_INITIALIZE;
        } elseif (!$progress->database_script_built) {
            $current = self::STEP_DATABASE;
        } elseif (!$progress->database_compressed) {
            $current = self::STEP_DATABASE_COMPRESS;
        } elseif (!$progress->archive_built) {
            $current = self::STEP_ARCHIVE;
        } elseif (!$progress->installer_built) {
            $current = self::STEP_INSTALLER;
        } elseif (!$progress->finalized) {
            $current = self::STEP_FINALIZE;
        }

        return apply_filters('duplicator_build_next_step', $current, $this->package);
    }

    /**
     * Return the current step key with the extensibility filter applied.
     *
     * Applies the 'duplicator_build_next_step' filter so addons can override
     * or inject custom steps. A filter returning null terminates the build early.
     *
     * @return ?string Filtered step name, or null when build is complete
     */
    public function key(): ?string
    {
        /** @var ?string */
        return $this->current();
    }

    /**
     * Advance the iterator.
     *
     * Step state lives in the package's build_progress and advances externally
     * when action handlers mark steps as completed.
     *
     * @return void
     */
    public function next(): void
    {
    }

    /**
     * Rewind the iterator.
     *
     * State lives in the package's build_progress, so there is nothing to reset.
     *
     * @return void
     */
    public function rewind(): void
    {
    }

    /**
     * Sets valid to false so the loop of the iterator breaks
     *
     * @return void
     */
    public function stop(): void
    {
        $this->shouldContinue = false;
    }

    /**
     * Check whether there is a step to execute.
     *
     * @return bool True when key() returns a non-null step name
     */
    public function valid(): bool
    {
        if ($this->current() === null) {
            return false;
        }

        return $this->shouldContinue;
    }
}
