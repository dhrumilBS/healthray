<?php

namespace Duplicator\Models\ActivityLog;

use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapString;

/**
 * Log event for orphan file cleanup operations
 */
class LogEventOrphanCleanup extends AbstractLogEvent
{
    /**
     * Class constructor
     *
     * @param int      $fileCount Number of files deleted
     * @param int      $totalSize Total size of deleted files in bytes
     * @param string[] $fileNames List of deleted file names
     */
    public function __construct(int $fileCount, int $totalSize, array $fileNames)
    {
        $this->subType  = 'cleanup';
        $this->severity = self::SEVERITY_INFO;

        $currentUser = wp_get_current_user();

        // Store cleanup-specific data
        $this->data = [
            'user_id'      => get_current_user_id(),
            'user_display' => $currentUser->display_name ?: $currentUser->user_login ?: 'system',
            'role'         => !empty($currentUser->roles) ? $currentUser->roles[0] : 'unknown',
            'screen'       => self::getCurrentScreenId(),
            'ip_address'   => self::getClientIP(),
            'timestamp'    => time(),

            // Cleanup-specific data
            'file_count'   => $fileCount,
            'total_size'   => $totalSize,
            'file_names'   => $fileNames,
        ];

        // Set user-friendly title
        $this->title = $this->generateTitle();
    }

    /**
     * Create and save an orphan cleanup log event
     *
     * @param int      $fileCount Number of files deleted
     * @param int      $totalSize Total size of deleted files in bytes
     * @param string[] $fileNames List of deleted file names
     *
     * @return self
     */
    public static function create(int $fileCount, int $totalSize, array $fileNames): self
    {
        $logEvent = new self($fileCount, $totalSize, $fileNames);
        $logEvent->save();
        return $logEvent;
    }

    /**
     * Return entity type identifier
     *
     * @return string
     */
    public static function getType(): string
    {
        return 'orphan_cleanup';
    }

    /**
     * Return entity type label
     *
     * @return string
     */
    public static function getTypeLabel(): string
    {
        return __('Maintenance', 'duplicator');
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
        $fileCount = $this->data['file_count'] ?? 0;
        return sprintf(
            _n(
                'Deleted %d orphaned file',
                'Deleted %d orphaned files',
                $fileCount,
                'duplicator'
            ),
            $fileCount
        );
    }

    /**
     * Display detailed information in html format
     *
     * @return void
     */
    public function detailHtml(): void
    {
        $fileCount   = $this->data['file_count'] ?? 0;
        $totalSize   = $this->data['total_size'] ?? 0;
        $fileNames   = $this->data['file_names'] ?? [];
        $userDisplay = $this->data['user_display'] ?? 'Unknown';
        $userRole    = $this->data['role'] ?? 'unknown';
        $ipAddress   = $this->data['ip_address'] ?? 'Unknown';
        $screen      = $this->data['screen'] ?? 'Unknown';
        ?>
        <div class="dup-log-detail-meta">
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Action:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php esc_html_e('Orphan File Cleanup', 'duplicator'); ?></span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Performed By:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($userDisplay); ?> (<?php echo esc_html($userRole); ?>)</span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('IP Address:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($ipAddress); ?></span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Files Deleted:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html((string) $fileCount); ?></span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Total Size Freed:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html(SnapString::byteSize($totalSize)); ?></span>
            </div>

            <?php if (!empty($fileNames)) : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Deleted Files:', 'duplicator'); ?></strong>
                <div class="dup-log-type">
                    <ul style="max-height: 200px; overflow-y: auto;">
                        <?php foreach ($fileNames as $fileName) : ?>
                            <li><code><?php echo esc_html($fileName); ?></code></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Return object type label
     *
     * @return string
     */
    public function getObjectTypeLabel(): string
    {
        return __('Orphan File Cleanup', 'duplicator');
    }

    /**
     * Generate user-friendly title
     *
     * @return string
     */
    private function generateTitle(): string
    {
        $fileCount = $this->data['file_count'] ?? 0;
        return sprintf(
            _n(
                'Orphan cleanup: %d file deleted',
                'Orphan cleanup: %d files deleted',
                $fileCount,
                'duplicator'
            ),
            $fileCount
        );
    }
}
