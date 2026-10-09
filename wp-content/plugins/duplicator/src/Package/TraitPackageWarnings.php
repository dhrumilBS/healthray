<?php

/**
 * Trait for package build warning management
 */

declare(strict_types=1);

namespace Duplicator\Package;

use Duplicator\Utils\Logging\DupLog;

/**
 * Trait TraitPackageWarnings
 *
 * Handles non-fatal build warnings: conditions detected during the build that
 * don't compromise the resulting backup but must be surfaced to the user.
 * A backup with warnings is a successfully completed backup; the only
 * difference is that the warnings are shown in the interface.
 *
 * Warnings must never mask errors: any condition that can produce a corrupted
 * or incomplete archive must remain a build failure.
 *
 * @phpstan-require-extends AbstractPackage
 */
trait TraitPackageWarnings
{
    /** @var array<int, array{code: int, message: string}> */
    protected array $buildWarnings = [];

    /** @var int Not-included items written to the logs so far */
    protected int $skippedItemsLogged = 0;

    /**
     * Record a build warning and set the warnings flag on the package.
     * Duplicate entries (same code and message) are ignored, and entries
     * beyond the hard cap are dropped: warnings must be aggregated by the
     * caller, per-item details belong to the backup log.
     *
     * The caller is responsible for persisting the package afterwards.
     *
     * @param int    $code    One of the AbstractPackage::WARNING_* codes
     * @param string $message Display message for the user
     *
     * @return void
     */
    public function addBuildWarning(int $code, string $message): void
    {
        $entry = [
            'code'    => $code,
            'message' => $message,
        ];
        if (in_array($entry, $this->buildWarnings, true)) {
            return;
        }

        if (count($this->buildWarnings) >= AbstractPackage::BUILD_WARNINGS_MAX_ENTRIES) {
            DupLog::infoTrace("Build warning cap reached, entry dropped: [{$code}] {$message}");
            return;
        }

        $this->buildWarnings[] = $entry;
        $this->addFlag(AbstractPackage::FLAG_BUILD_WARNINGS);
    }

    /**
     * Record the aggregated skipped-files build warning.
     *
     * Shared by the archive engines and called from their per-item skip
     * branches: the identical entry is deduplicated, so any number of skips
     * produces a single warning. The per-item list stays in the backup log.
     *
     * @return void
     */
    public function addSkippedFilesBuildWarning(): void
    {
        $this->addBuildWarning(
            AbstractPackage::WARNING_ARCHIVE_SKIPPED_FILES,
            __(
                'Some files could not be included in the Backup, usually cache or temporary
                files that changed during the process. The full list is in the Backup log:
                if something important is listed there, create a new Backup when the site
                is less busy.',
                'duplicator'
            )
        );
    }

    /**
     * Write a not-included item to the Backup and trace logs.
     *
     * Shared by the archive engines: each skipped item is listed individually
     * up to a hard cap, after which a single "more items" line is written once
     * and further items are only counted, to keep the log readable.
     *
     * @param string $label  Item type label shown in the log (e.g. File, Directory)
     * @param string $path   Item path
     * @param string $reason Why the item was not included
     *
     * @return void
     */
    public function logSkippedItem(string $label, string $path, string $reason): void
    {
        $this->skippedItemsLogged++;
        if ($this->skippedItemsLogged > AbstractPackage::SKIPPED_ITEMS_LOG_MAX_ENTRIES) {
            if ($this->skippedItemsLogged === AbstractPackage::SKIPPED_ITEMS_LOG_MAX_ENTRIES + 1) {
                DupLog::infoTrace('WARNING: more items were not included in the Backup, further entries are not listed');
            }
            return;
        }

        DupLog::infoTrace("WARNING: {$label} not included in the Backup: {$path}. Reason: {$reason}");
    }

    /**
     * Check if the package has build warnings
     *
     * @return bool
     */
    public function hasBuildWarnings(): bool
    {
        return count($this->buildWarnings) > 0;
    }

    /**
     * Get the build warnings
     *
     * @return array<int, array{code: int, message: string}>
     */
    public function getBuildWarnings(): array
    {
        return $this->buildWarnings;
    }

    /**
     * Get the display messages of all build warnings
     *
     * @return string[]
     */
    public function getBuildWarningMessages(): array
    {
        return array_column($this->buildWarnings, 'message');
    }

    /**
     * Generate the descriptive display list of the build warnings:
     * one row per warning with the code label and the stored message.
     *
     * @return array<int, array{label: string, message: string}>
     */
    public function getBuildWarningsDisplayList(): array
    {
        return array_map(
            fn(array $entry): array => [
                'label'   => self::getWarningLabel($entry['code']),
                'message' => $entry['message'],
            ],
            $this->buildWarnings
        );
    }

    /**
     * Get the short translated label of a build warning code
     *
     * @param int $code One of the AbstractPackage::WARNING_* codes
     *
     * @return string
     */
    public static function getWarningLabel(int $code): string
    {
        switch ($code) {
            case AbstractPackage::WARNING_ARCHIVE_FILE_COUNT_UNVERIFIED:
                return __('File count check skipped', 'duplicator');
            case AbstractPackage::WARNING_ARCHIVE_VANISHED_FILES:
                return __('Some files changed during the Backup', 'duplicator');
            case AbstractPackage::WARNING_ARCHIVE_SKIPPED_FILES:
                return __('Some files were not included', 'duplicator');
            case AbstractPackage::WARNING_DB_ROW_COUNT_DRIFT:
                return __('Database changed during the Backup', 'duplicator');
            case AbstractPackage::WARNING_INSTALLER_FILES_UNVERIFIED:
                return __('Installer files check skipped', 'duplicator');
            default:
                return __('Non-critical warning', 'duplicator');
        }
    }
}
