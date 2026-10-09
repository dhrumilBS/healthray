<?php

namespace Duplicator\Models\ActivityLog;

use Duplicator\Core\CapMng;
use Duplicator\Utils\ActivityLog\SettingsChangeDescriptor;

/**
 * Log event for settings changes
 */
class LogEventSettingsChange extends AbstractLogEvent
{
    const SUB_TYPE_GENERAL       = 'general';
    const SUB_TYPE_BACKUP        = 'backup';
    const SUB_TYPE_STORAGE       = 'storage';
    const SUB_TYPE_CAPABILITIES  = 'capabilities';
    const SUB_TYPE_QUICK_FIX     = 'quick_fix';
    const SUB_TYPE_IMPORT_EXPORT = 'import_export';

    /**
     * Class constructor
     *
     * @param string               $subType Settings change sub-type
     * @param array<string, mixed> $context Additional context data
     *                                      - changes:
     *                                      array<array{key:string,format:string,data:scalar[]}>
     *                                      Settings changes -
     *                                      action_type: string
     *                                      Action type identifier
     */
    public function __construct(string $subType, array $context = [])
    {
        $this->subType  = $subType;
        $this->severity = self::SEVERITY_INFO;

        $currentUser = wp_get_current_user();
        $changes     = $context['changes'] ?? [];

        // Store detailed context data
        $this->data = [
            'user_id'      => get_current_user_id(),
            'user_display' => $currentUser->display_name ?: $currentUser->user_login ?: 'system',
            'role'         => !empty($currentUser->roles) ? $currentUser->roles[0] : 'unknown',
            'screen'       => self::getCurrentScreenId(),
            'ip_address'   => self::getClientIP(),
            'timestamp'    => time(),

            // Store the flexible changes structure
            'changes'      => $changes,

            // Additional context
            'action_type'  => $context['action_type'] ?? 'settings_updated',
        ];

        // Set user-friendly title
        $this->title = $this->generateUserFriendlyTitle($subType, $context, $changes);
    }

    /**
     * Create and save a settings change log event
     *
     * @param string               $subType Settings change sub-type
     * @param array<string, mixed> $context Additional context data
     *
     * @return self
     */
    public static function create(string $subType, array $context = []): self
    {
        $logEvent = new self($subType, $context);
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
        return 'settings_change';
    }

    /**
     * Return entity type label
     *
     * @return string
     */
    public static function getTypeLabel(): string
    {
        return __('Settings Change', 'duplicator');
    }

    /**
     * Return required capability for this log event
     *
     * @return string
     */
    public static function getCapability(): string
    {
        return CapMng::CAP_SETTINGS;
    }

    /**
     * Return short description
     *
     * @return string
     */
    public function getShortDescription(): string
    {
        $actionType   = $this->data['action_type'] ?? 'settings_updated';
        $subTypeLabel = $this->getSubTypeLabel($this->subType);
        $changeCount  = count($this->data['changes'] ?? []);

        switch ($actionType) {
            case 'quick_fix_apply':
                return __('Applied automatic system fixes to resolve configuration issues', 'duplicator');
            case 'settings_import':
                return sprintf(__('Imported plugin settings from uploaded file', 'duplicator'), $subTypeLabel);
            case 'settings_export':
                return sprintf(__('Plugin settings exported to file', 'duplicator'), $subTypeLabel);
            case 'settings_reset':
                return sprintf(__('Reset %s configuration to factory defaults', 'duplicator'), $subTypeLabel);
            default:
                // More descriptive messages based on change count
                if ($changeCount > 0) {
                    return sprintf(
                        _n(
                            'Modified %2$d %1$s configuration setting',
                            'Modified %2$d %1$s configuration settings',
                            $changeCount,
                            'duplicator'
                        ),
                        $subTypeLabel,
                        $changeCount
                    );
                } else {
                    return sprintf(__('Updated %s configuration without detectable changes', 'duplicator'), $subTypeLabel);
                }
        }
    }

    /**
     * Display detailed information in html format
     *
     * @return void
     */
    public function detailHtml(): void
    {
        $subTypeLabel = $this->getSubTypeLabel($this->subType);
        $actionType   = $this->data['action_type'] ?? 'settings_updated';
        $userDisplay  = $this->data['user_display'] ?? 'Unknown';
        $userRole     = $this->data['role'] ?? 'unknown';
        $ipAddress    = $this->data['ip_address'] ?? 'Unknown';
        $screen       = $this->data['screen'] ?? 'Unknown';
        $changes      = $this->data['changes'] ?? [];
        ?>
        <div class="dup-log-detail-meta">
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Settings Category:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($subTypeLabel); ?></span>
            </div>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Action:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html(ucfirst($actionType)); ?></span>
            </div>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Changed By:', 'duplicator'); ?></strong>
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

            <?php if (!empty($changes) && is_array($changes)) : ?>
                <div class="dup-log-type-wrapper">
                    <strong><?php esc_html_e('Settings Changed:', 'duplicator'); ?></strong><br>
                    <div class="margin-top-1">
                        <table class="widefat dup-table-list striped dup-activity-log-table small">
                            <thead>
                                <tr>
                                    <th scope="col" class="manage-column"><?php esc_html_e('Setting', 'duplicator'); ?></th>
                                    <th scope="col" class="manage-column"><?php esc_html_e('Description', 'duplicator'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($changes as $change) : ?>
                                    <?php if (!isset($change['key'], $change['format'], $change['data'])) {
                                        continue;
                                    } ?>
                                <tr>
                                    <td class="font-bold">
                                        <?php echo esc_html(SettingsChangeDescriptor::getLabelFromKey($change['key'])); ?>
                                    </td>
                                    <td class="success-color">
                                        <?php echo esc_html(SettingsChangeDescriptor::getSentenceFromFormat(
                                            $change['format'],
                                            $change['data'],
                                            $change['options'] ?? []
                                        )); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Generate user-friendly title
     *
     * @param string                                               $subType Settings change sub-type
     * @param array<string, mixed>                                 $context Context data
     * @param array<array{key:string,format:string,data:scalar[]}> $changes Settings changes
     *
     * @return string
     */
    private function generateUserFriendlyTitle(string $subType, array $context, array $changes): string
    {
        $subTypeLabel = $this->getSubTypeLabel($subType);
        $actionType   = $context['action_type'] ?? 'settings_updated';

        switch ($actionType) {
            case 'quick_fix_apply':
                return __('Quick fix applied', 'duplicator');
            case 'settings_import':
                return __('Settings imported from file', 'duplicator');
            case 'settings_export':
                return __('Settings exported to file', 'duplicator');
            case 'settings_reset':
                return sprintf(__('%s settings reset to defaults', 'duplicator'), $subTypeLabel);
            default:
                // Simple titles without counts - details go in description
                return sprintf(__('%s settings updated', 'duplicator'), $subTypeLabel);
        }
    }

    /**
     * Get human-readable label for sub-type
     *
     * @param string $subType Sub-type identifier
     *
     * @return string
     */
    private function getSubTypeLabel(string $subType): string
    {
        $labels = [
            self::SUB_TYPE_GENERAL       => __('General', 'duplicator'),
            self::SUB_TYPE_BACKUP        => __('Backup', 'duplicator'),
            self::SUB_TYPE_STORAGE       => __('Storage', 'duplicator'),
            self::SUB_TYPE_CAPABILITIES  => __('Capabilities', 'duplicator'),
            self::SUB_TYPE_QUICK_FIX     => __('Quick Fix', 'duplicator'),
            self::SUB_TYPE_IMPORT_EXPORT => __('Import/Export', 'duplicator'),
        ];

        /** @var array<string, string> */
        $labels = apply_filters('duplicator_settings_change_sub_type_labels', $labels);

        return $labels[$subType] ?? ucfirst(str_replace('_', ' ', $subType));
    }
}
