<?php

namespace Duplicator\Models\ActivityLog;

use Duplicator\Core\CapMng;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\PackageUtils;
use Exception;

/**
 * Log event for backup scanning
 */
class LogEventWebsitesScan extends AbstractLogEvent
{
    use TraitLogEventErrorContext;

    const SUB_TYPE_ERROR         = 'scan_error';
    const SUB_TYPE_START         = 'scan_start';
    const SUB_TYPE_END           = 'scan_end';
    const SCAN_LOG_CONTEXT_LINES = 100;
    /** @var int Max chars of the error message shown in the log list */
    const ERROR_SHORT_DESC_LENGTH = 80;

    /**
     * Class constructor
     *
     * @param AbstractPackage $package  Package
     * @param string          $status   Status ENUM self::SUB_TYPE_*
     * @param int             $parentId Parent ID, if 0 the event have no event parent
     */
    public function __construct(AbstractPackage $package, string $status, int $parentId = 0)
    {
        $this->data['packageId']   = $package->getId();
        $this->data['packageName'] = $package->getName();
        $this->data['execType']    = PackageUtils::getTypeString($package);
        $this->data['components']  = $package->components;
        $this->data['filterOn']    = $package->Archive->FilterOn;
        $this->data['filterDirs']  = strlen($package->Archive->FilterDirs) > 0 ? explode(';', $package->Archive->FilterDirs) : [];
        $this->data['filterExts']  = strlen($package->Archive->FilterExts) > 0 ? explode(';', $package->Archive->FilterExts) : [];
        $this->data['filterFiles'] = strlen($package->Archive->FilterFiles) > 0 ? explode(';', $package->Archive->FilterFiles) : [];
        $this->data['fileCount']   = $package->Archive->FileCount;
        $this->data['dirCount']    = $package->Archive->DirCount;
        $this->data['size']        = $package->Archive->Size;

        // Database table count
        $this->data['dbExcluded']   = BuildComponents::isDBExcluded($package->components);
        $this->data['dbTableCount'] = 0;
        $this->data['dbSize']       = 0;
        $this->data['dbRowCount']   = 0;

        if (!$this->data['dbExcluded'] && isset($package->Database->info)) {
            $this->data['dbTableCount'] = $package->Database->info->tablesFinalCount;
            $this->data['dbSize']       = $package->Database->info->tablesSizeOnDisk;
            $this->data['dbRowCount']   = $package->Database->info->tablesRowCount;
        }

        // Snapshot of the per-status timers, kept so the log survives the package deletion
        $this->data['stateTimes'] = $package->getStateTimes();

        switch ($status) {
            case self::SUB_TYPE_ERROR:
                $this->data['logFileName']    = $package->getLogFilename();
                $this->data['scanLogContext'] = $this->captureLogTail(
                    $package,
                    self::SCAN_LOG_CONTEXT_LINES,
                    __('(scan log not available)', 'duplicator')
                );
                $this->data['quickFixes']     = $this->captureQuickFixes();
                $this->title                  = sprintf(__('%s Scan - Error', 'duplicator'), $this->data['execType']);
                break;
            case self::SUB_TYPE_START:
                $this->title = sprintf(__('%s Scan', 'duplicator'), $this->data['execType']);
                break;
            case self::SUB_TYPE_END:
                $this->title = sprintf(__('%s Scan - Completed', 'duplicator'), $this->data['execType']);
                break;
            default:
                throw new Exception('Invalid status: ' . $status);
        }
        $this->subType  = $status;
        $this->severity = $this->subType === self::SUB_TYPE_ERROR ? self::SEVERITY_ERROR : self::SEVERITY_INFO;
        $this->parentId = $parentId;
    }

    /**
     * Return entity type identifier
     *
     * @return string
     */
    public static function getType(): string
    {
        return 'websites_scan';
    }

    /**
     * Return entity type label
     *
     * @return string
     */
    public static function getTypeLabel(): string
    {
        return __('Websites Scan', 'duplicator');
    }

    /**
     * Return required capability for this log event
     *
     * @return string
     */
    public static function getCapability(): string
    {
        return CapMng::CAP_BASIC;
    }

    /**
     * Return short description
     *
     * @return string
     */
    public function getShortDescription(): string
    {
        switch ($this->subType) {
            case self::SUB_TYPE_ERROR:
                if (!empty($this->data['quickFixes'])) {
                    $errorText = self::readLegacyQuickFix((array) $this->data['quickFixes'][0])['errorText'];
                    $errorText = trim((string) preg_replace('/\s+/', ' ', $errorText));
                    if ($errorText !== '') {
                        return sprintf(
                            __('Error: %s', 'duplicator'),
                            SnapString::truncateString($errorText, self::ERROR_SHORT_DESC_LENGTH)
                        );
                    }
                }
                return __('Scan failed - see details', 'duplicator');
            case self::SUB_TYPE_START:
                // Show scan initiation details with what will be scanned
                $description = sprintf(
                    __('Scan started: %1$s components', 'duplicator'),
                    BuildComponents::displayComponentsList($this->data['components'], ", ")
                );

                // Add filter info if filters are applied
                if ($this->data['filterOn']) {
                    $filterCount  = count($this->data['filterDirs']) + count($this->data['filterFiles']) + count($this->data['filterExts']);
                    $description .= sprintf(
                        __('; %d filters applied', 'duplicator'),
                        $filterCount
                    );
                }

                return $description;
            case self::SUB_TYPE_END:
                $description = sprintf(
                    __('Scan completed: %1$d files, %2$d directories; size: %3$s', 'duplicator'),
                    $this->data['fileCount'],
                    $this->data['dirCount'],
                    SnapString::byteSize((int)$this->data['size'])
                );

                // Add database information if available
                if (!$this->data['dbExcluded'] && $this->data['dbTableCount'] > 0) {
                    $description .= sprintf(
                        __('; DB: %1$d tables, %2$d rows, %3$s', 'duplicator'),
                        $this->data['dbTableCount'],
                        $this->data['dbRowCount'] ?? 0,
                        SnapString::byteSize($this->data['dbSize'] ?? 0)
                    );
                }

                return $description;
            default:
                return __('Scan', 'duplicator');
        }
    }

    /**
     * Get execution time for the scan phase from the state-times snapshot
     *
     * @param string $subType The sub-event type (unused for scan, kept for interface consistency)
     *
     * @return string Formatted execution time string
     */
    public function getExecutionTimeForPhase(string $subType): string
    {
        $stateTimes = (array) ($this->data['stateTimes'] ?? []);
        if (count($stateTimes) === 0) {
            // Log persisted before the state-times snapshot: flat legacy timer keys
            $start = (float) ($this->data['scanTimeStart'] ?? 0);
            $end   = (float) ($this->data['scanTimeEnd'] ?? 0);
            return PackageUtils::getDurationLabel($start > 0 && $end > 0 ? max(0, $end - $start) : -1);
        }

        return PackageUtils::getDurationLabel(
            AbstractPackage::phaseDurationFromTimes($stateTimes, AbstractPackage::PHASE_SCAN)
        );
    }

    /**
     * Update the state-times snapshot of this log
     *
     * @param array<int, array{start: float, end: float}> $stateTimes Package state times
     *
     * @return void
     */
    public function updateTimingData(array $stateTimes): void
    {
        $this->data['stateTimes'] = $stateTimes;
    }

    /**
     * Display detailed information in html format
     *
     * @return void
     */
    public function detailHtml(): void
    {
        ?>
        <div class="dup-log-detail-meta">
            <?php if (!empty($this->data['execType'])) : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Run Type:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html($this->data['execType']); ?>
                </span>
            </div>
            <?php endif; ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Components:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html(BuildComponents::displayComponentsList($this->data['components'], ", ")); ?>
                </span>
            </div>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Filter On:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php echo esc_html($this->data['filterOn'] ? __('Yes', 'duplicator') : __('No', 'duplicator')); ?>
                </span>
            </div>


            <?php if (!$this->data['dbExcluded'] && $this->data['dbTableCount'] > 0) : ?>
                <div class="dup-log-type-wrapper">
                    <strong><?php esc_html_e('Database Tables:', 'duplicator'); ?></strong>
                    <span class="dup-log-type">
                        <?php echo esc_html(number_format($this->data['dbTableCount'])); ?>
                    </span>
                </div>
                <div class="dup-log-type-wrapper">
                    <strong><?php esc_html_e('Database Size:', 'duplicator'); ?></strong>
                    <span class="dup-log-type">
                        <?php echo esc_html(SnapString::byteSize($this->data['dbSize'])); ?>
                    </span>
                </div>
                <?php if ($this->data['dbRowCount'] > 0) : ?>
                    <div class="dup-log-type-wrapper">
                        <strong><?php esc_html_e('Database Rows:', 'duplicator'); ?></strong>
                        <span class="dup-log-type">
                            <?php echo esc_html(number_format($this->data['dbRowCount'])); ?>
                        </span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($this->data['filterOn']) : ?>
                <?php if (count($this->data['filterDirs']) > 0) : ?>
                    <div class="dup-log-type-wrapper">
                        <strong><?php esc_html_e('Filter Dirs:', 'duplicator'); ?></strong><br>
                        <?php foreach ($this->data['filterDirs'] as $dir) : ?>
                            - <?php echo esc_html($dir); ?><br>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if (count($this->data['filterFiles']) > 0) : ?>
                    <div class="dup-log-type-wrapper">
                        <strong><?php esc_html_e('Filter Files:', 'duplicator'); ?></strong><br>
                        <?php foreach ($this->data['filterFiles'] as $file) : ?>
                            - <?php echo esc_html($file); ?><br>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if (count($this->data['filterExts']) > 0) : ?>
                    <div class="dup-log-type-wrapper">
                        <strong><?php esc_html_e('Filter Exts:', 'duplicator'); ?></strong>
                        <span class="dup-log-type">
                            <?php echo esc_html(implode(', ', $this->data['filterExts'])); ?>
                        </span>
                    </div>
                <?php endif; ?>
                <?php
            endif;
            switch ($this->subType) {
                case self::SUB_TYPE_START:
                    $subEvents = array_merge(
                        [$this],
                        self::getList(
                            [
                                'parent_id' => $this->getId(),
                                'order'     => 'ASC',
                                'orderby'   => 'created_at',
                            ]
                        )
                    );

                    if (count($subEvents) > 0) {
                        ?>
                        <div class="margin-top-1">
                            <?php TplMng::getInstance()->render(
                                'admin_pages/activity_log/parts/sub_table_mini',
                                [
                                    'logs'      => $subEvents,
                                    'parentLog' => $this,
                                ]
                            ); ?>
                        </div>
                        <?php
                    }
                    break;
                case self::SUB_TYPE_END:
                    ?>
                    <div class="dup-activity-log-scan-end">
                        <div class="dup-log-type-wrapper">
                            <strong><?php esc_html_e('File Count:', 'duplicator'); ?></strong>
                            <span class="dup-log-type">
                                <?php echo esc_html($this->data['fileCount']); ?>
                            </span>
                        </div>
                        <div class="dup-log-type-wrapper">
                            <strong><?php esc_html_e('Directory Count:', 'duplicator'); ?></strong>
                            <span class="dup-log-type">
                                <?php echo esc_html($this->data['dirCount']); ?>
                            </span>
                        </div>
                        <div class="dup-log-type-wrapper">
                            <strong><?php esc_html_e('Size:', 'duplicator'); ?></strong>
                            <span class="dup-log-type">
                                <?php echo esc_html(SnapString::byteSize((int)$this->data['size'])); ?>
                            </span>
                        </div>
                    </div>
                    <?php
                    break;
                case self::SUB_TYPE_ERROR:
                    $this->renderErrorDetails();
                    break;
            }
            ?>
        </div>
        <?php
    }

    /**
     * Render the Reason and Error Context blocks for a scan-error event.
     *
     * @return void
     */
    private function renderErrorDetails(): void
    {
        $this->renderQuickFixesReason((array) ($this->data['quickFixes'] ?? []));

        if (!empty($this->data['scanLogContext'])) {
            $logLines = (array) $this->data['scanLogContext'];
            $count    = count($logLines);
            ?>
            <hr>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Error Context:', 'duplicator'); ?></strong>
            </div>
            <?php
            $this->renderLogContext(
                $logLines,
                (string) ($this->data['logFileName'] ?? ''),
                sprintf(
                    /* translators: %d: number of log lines shown */
                    _n(
                        'Showing %d line from when the scan error occurred',
                        'Showing %d lines from when the scan error occurred',
                        $count,
                        'duplicator'
                    ),
                    $count
                )
            );
        }
    }

    /**
     * Return object type label, can be overridden by child classes
     * by default it returns the same as static::getTypeLabel() but can change in base of object properties
     *
     * @return string
     */
    public function getObjectTypeLabel(): string
    {
        switch ($this->subType) {
            case self::SUB_TYPE_ERROR:
                return __('Scan Error', 'duplicator');
            case self::SUB_TYPE_START:
                return __('Scan Start', 'duplicator');
            case self::SUB_TYPE_END:
                return __('Scan End', 'duplicator');
            default:
                return __('Scan', 'duplicator');
        }
    }
}
