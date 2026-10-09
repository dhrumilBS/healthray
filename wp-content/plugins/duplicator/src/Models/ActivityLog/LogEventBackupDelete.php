<?php

namespace Duplicator\Models\ActivityLog;

use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Package\AbstractPackage;

/**
 * Log event for backup delete operations
 */
class LogEventBackupDelete extends AbstractLogEvent
{
    /**
     * Class constructor
     *
     * @param AbstractPackage $package The package being deleted
     */
    public function __construct(AbstractPackage $package)
    {
        $this->subType  = 'delete';
        $this->severity = self::SEVERITY_INFO;

        $currentUser = wp_get_current_user();

        // Collect storage information
        $storages = [];
        foreach ($package->upload_infos as $uploadInfo) {
            $storage    = $uploadInfo->getStorage();
            $storages[] = [
                'id'   => $uploadInfo->getStorageId(),
                'name' => $storage->getName(),
                'type' => $storage->getStypeName(),
            ];
        }

        // Store deletion-specific data
        $this->data = [
            'user_id'         => get_current_user_id(),
            'user_display'    => $currentUser->display_name ?: $currentUser->user_login ?: 'system',
            'role'            => !empty($currentUser->roles) ? $currentUser->roles[0] : 'unknown',
            'screen'          => self::getCurrentScreenId(),
            'ip_address'      => self::getClientIP(),
            'timestamp'       => time(),

            // Package-specific data
            'package_id'      => $package->getId(),
            'package_name'    => $package->getName(),
            'package_hash'    => $package->getHash(),
            'package_status'  => $package->getStatus(),
            'package_size'    => $package->Archive->Size,
            'package_created' => $package->getCreated(),
            'storages'        => $storages,
        ];

        // Set user-friendly title
        $this->title = $this->generateTitle();
    }

    /**
     * Create and save a backup delete log event
     *
     * @param AbstractPackage $package The package being deleted
     *
     * @return self
     */
    public static function create(AbstractPackage $package): self
    {
        $logEvent = new self($package);
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
        return 'backup_delete';
    }

    /**
     * Return entity type label
     *
     * @return string
     */
    public static function getTypeLabel(): string
    {
        return __('Backup Management', 'duplicator');
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
        $packageName = $this->data['package_name'] ?? 'Unknown';
        return sprintf(__('Backup "%s" deleted', 'duplicator'), $packageName);
    }

    /**
     * Display detailed information in html format
     *
     * @return void
     */
    public function detailHtml(): void
    {
        $packageName    = $this->data['package_name'] ?? 'Unknown';
        $packageHash    = $this->data['package_hash'] ?? 'Unknown';
        $packageSize    = $this->data['package_size'] ?? 0;
        $packageCreated = $this->data['package_created'] ?? 'Unknown';
        $storages       = $this->data['storages'] ?? [];
        $userDisplay    = $this->data['user_display'] ?? 'Unknown';
        $userRole       = $this->data['role'] ?? 'unknown';
        $ipAddress      = $this->data['ip_address'] ?? 'Unknown';
        $screen         = $this->data['screen'] ?? 'Unknown';
        ?>
        <div class="dup-log-detail-meta">
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Action:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php esc_html_e('Delete', 'duplicator'); ?></span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Package Name:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($packageName); ?></span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Package Hash:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($packageHash); ?></span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Package Size:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html(SnapString::byteSize($packageSize)); ?></span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Created Date:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($packageCreated); ?></span>
            </div>

            <?php if (!empty($storages)) : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Storage Locations:', 'duplicator'); ?></strong>
                <div class="dup-log-type">
                    <ul>
                        <?php foreach ($storages as $storage) : ?>
                            <li><?php echo esc_html($storage['name'] . ' (' . $storage['type'] . ')'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Deleted By:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($userDisplay); ?> (<?php echo esc_html($userRole); ?>)</span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('IP Address:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($ipAddress); ?></span>
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
        return __('Backup Deletion', 'duplicator');
    }

    /**
     * Generate user-friendly title
     *
     * @return string
     */
    private function generateTitle(): string
    {
        $packageName = $this->data['package_name'] ?? 'Unknown';
        return sprintf(__('Backup deleted: %s', 'duplicator'), $packageName);
    }
}
