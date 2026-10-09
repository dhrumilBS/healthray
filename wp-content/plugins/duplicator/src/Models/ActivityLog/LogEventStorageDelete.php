<?php

namespace Duplicator\Models\ActivityLog;

use Duplicator\Core\CapMng;

/**
 * Log event for storage deletion operations
 */
class LogEventStorageDelete extends AbstractLogEvent
{
    /**
     * Class constructor
     *
     * @param int    $storageId        Storage ID
     * @param string $storageName      Storage name
     * @param string $storageType      Storage type
     * @param int    $affectedPackages Number of packages that had files in this storage
     */
    public function __construct(int $storageId, string $storageName, string $storageType, int $affectedPackages)
    {
        $this->subType  = 'delete';
        $this->severity = self::SEVERITY_INFO;

        $currentUser = wp_get_current_user();

        // Store deletion-specific data
        $this->data = [
            'user_id'           => get_current_user_id(),
            'user_display'      => $currentUser->display_name ?: $currentUser->user_login ?: 'system',
            'role'              => !empty($currentUser->roles) ? $currentUser->roles[0] : 'unknown',
            'screen'            => self::getCurrentScreenId(),
            'ip_address'        => self::getClientIP(),
            'timestamp'         => time(),

            // Storage-specific data
            'storage_id'        => $storageId,
            'storage_name'      => $storageName,
            'storage_type'      => $storageType,
            'affected_packages' => $affectedPackages,
        ];

        // Set user-friendly title
        $this->title = $this->generateTitle();
    }

    /**
     * Create and save a storage delete log event
     *
     * @param int    $storageId        Storage ID
     * @param string $storageName      Storage name
     * @param string $storageType      Storage type
     * @param int    $affectedPackages Number of packages that had files in this storage
     *
     * @return self
     */
    public static function create(int $storageId, string $storageName, string $storageType, int $affectedPackages): self
    {
        $logEvent = new self($storageId, $storageName, $storageType, $affectedPackages);
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
        return 'storage_delete';
    }

    /**
     * Return entity type label
     *
     * @return string
     */
    public static function getTypeLabel(): string
    {
        return __('Storage Management', 'duplicator');
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
        $storageName = $this->data['storage_name'] ?? 'Unknown';
        return sprintf(__('Storage "%s" deleted', 'duplicator'), $storageName);
    }

    /**
     * Display detailed information in html format
     *
     * @return void
     */
    public function detailHtml(): void
    {
        $storageName      = $this->data['storage_name'] ?? 'Unknown';
        $storageType      = $this->data['storage_type'] ?? 'Unknown';
        $affectedPackages = $this->data['affected_packages'] ?? 0;
        $userDisplay      = $this->data['user_display'] ?? 'Unknown';
        $userRole         = $this->data['role'] ?? 'unknown';
        $ipAddress        = $this->data['ip_address'] ?? 'Unknown';
        $screen           = $this->data['screen'] ?? 'Unknown';
        ?>
        <div class="dup-log-detail-meta">
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Action:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php esc_html_e('Storage Deletion', 'duplicator'); ?></span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Deleted By:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($userDisplay); ?> (<?php echo esc_html($userRole); ?>)</span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('IP Address:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($ipAddress); ?></span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Storage Name:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($storageName); ?></span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Storage Type:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($storageType); ?></span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Affected Packages:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php
                    echo esc_html(
                        sprintf(
                            _n(
                                '%d package had files in this storage',
                                '%d packages had files in this storage',
                                $affectedPackages,
                                'duplicator'
                            ),
                            $affectedPackages
                        )
                    );
                    ?>
                </span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Page:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($screen); ?></span>
            </div>
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
        return __('Storage Deletion', 'duplicator');
    }

    /**
     * Generate user-friendly title
     *
     * @return string
     */
    private function generateTitle(): string
    {
        $storageName      = $this->data['storage_name'] ?? 'Unknown';
        $storageType      = $this->data['storage_type'] ?? 'Unknown';
        $affectedPackages = $this->data['affected_packages'] ?? 0;
        return sprintf(
            __('Storage deleted: %1$s (%2$s) - %3$d package(s) affected', 'duplicator'),
            $storageName,
            $storageType,
            $affectedPackages
        );
    }
}
