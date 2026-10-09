<?php

namespace Duplicator\Core\Views;

use Duplicator\Models\GlobalEntity;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Upgrade\UpgradePlugin;
use Duplicator\Core\Views\TplMng;

/**
 * Notifications.
 */
class Notifications
{
    const SOURCE_PATH = '/dp-notifications.json';

    /**
     * WordPress option key containing notification data
     *
     * @var string
     */
    const NOTIFICATIONS_OPT_KEY = 'dupli_opt_notifications';

    /**
     * Duplicator notifications dismiss nonce key
     *
     * @var string
     */
    const NONCE_KEY = 'duplicator-notification-dismiss';

    /**
     * Option value.
     *
     * @var bool|array{update: int, feed: mixed[], events: mixed[], dismissed: mixed[]}
     */
    private static $option = false;

    /**
     * Initialize class.
     *
     * @return void
     */
    public static function init(): void
    {
        if (!GlobalEntity::getInstance()->isAmNoticesEnabled()) {
            return;
        }

        /**
         * Filter to gate access to admin notifications. Addons can require a more
         * restrictive capability if needed.
         *
         * @param bool $canView Whether the current user can view notifications
         */
        $canView = (bool) apply_filters(
            'duplicator_can_view_notifications',
            CapMng::can(CapMng::CAP_SETTINGS, false)
        );
        if (!$canView) {
            return;
        }

        // Add notification count to menu label.
        add_filter('duplicator_menu_label_' . ControllersManager::MAIN_MENU_SLUG, function ($label) {
            if (self::getCount() === 0) {
                return $label;
            }
            return $label . '<span class="awaiting-mod">' . self::getCount() . '</span>';
        });

        self::update();

        add_action('duplicator_before_packages_table_action', [self::class, 'output']);
    }

    /**
     * Dismis notification.
     *
     * @param string $id Notification id.
     *
     * @return bool
     */
    public static function dismiss($id)
    {
        $type   = is_numeric($id) ? 'feed' : 'events';
        $option = self::getOption();

        $option['dismissed'][] = $id;
        $option['dismissed']   = array_unique($option['dismissed']);

        // Remove notification.
        if (!is_array($option[$type]) || empty($option[$type])) {
            throw new \Exception('Notification type not set.');
        }

        foreach ($option[$type] as $key => $notification) {
            if ((string)$notification['id'] === (string)$id) {
                unset($option[$type][$key]);

                break;
            }
        }
        return update_option(self::NOTIFICATIONS_OPT_KEY, $option);
    }

    /**
     * Get option value.
     *
     * @param bool $cache Reference property cache if available.
     *
     * @return array{update: int, feed: mixed[], events: mixed[], dismissed: mixed[]}
     */
    private static function getOption($cache = true)
    {
        if (self::$option && $cache) {
            return self::$option;
        }

        self::$option = get_option(self::NOTIFICATIONS_OPT_KEY, [
            'update'    => 0,
            'feed'      => [],
            'events'    => [],
            'dismissed' => [],
        ]);

        return self::$option;
    }

    /**
     * Clear the persisted and in-memory notification cache.
     *
     * @return void
     */
    public static function resetCache(): void
    {
        delete_option(self::NOTIFICATIONS_OPT_KEY);
        self::$option = false;
    }

    /**
     * Fetch notifications from feed.
     *
     * @return mixed[]
     */
    private static function fetchFeed(): array
    {
        $response = wp_remote_get(
            self::getSourceUrl(),
            [
                'timeout'    => 10,
                'user-agent' => self::getUserAgent(),
            ]
        );

        if (is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);

        if (empty($body)) {
            return [];
        }

        return self::verify(json_decode($body, true));
    }

    /**
     * Return the notification feed URL for the configured domain.
     *
     * @return string
     */
    public static function getSourceUrl(): string
    {
        return rtrim(DUPLICATOR_NOTIFICATIONS_DOMAIN, '/') . self::SOURCE_PATH;
    }

    /**
     * Verify notification data before it is saved.
     *
     * @param mixed[] $notifications Array of notifications items to verify.
     *
     * @return mixed[]
     */
    private static function verify($notifications): array
    {
        $data = [];
        if (!is_array($notifications) || empty($notifications)) {
            return $data;
        }

        foreach ($notifications as $notification) {
            // Ignore if one of the conditional checks is true:
            //
            // 1. notification message is empty.
            // 2. plugin type does not match.
            // 3. notification is expired.
            // 4. notification has already been dismissed.
            // 5. notification existed before installing Duplicator.
            // (Prevents bombarding the user with notifications after activation).
            if (
                empty($notification['content']) ||
                !self::isPluginTypeMatch($notification) ||
                self::isExpired($notification) ||
                self::isDismissed($notification) ||
                self::isExisted($notification)
            ) {
                continue;
            }

            $data[] = $notification;
        }

        return $data;
    }

    /**
     * Verify saved notification data for active notifications.
     *
     * @param mixed[] $notifications Array of notifications items to verify.
     *
     * @return mixed[]
     */
    private static function verifyActive(array $notifications): array
    {
        if (empty($notifications)) {
            return [];
        }

        $current_timestamp = time();

        // Remove notifications that are not active.
        foreach ($notifications as $key => $notification) {
            if (
                (!empty($notification['start']) && $current_timestamp < strtotime($notification['start'])) ||
                (!empty($notification['end']) && $current_timestamp > strtotime($notification['end']))
            ) {
                unset($notifications[$key]);
            }
        }

        return $notifications;
    }

    /**
     * Get notification data.
     *
     * @return mixed[]
     */
    private static function get(): array
    {
        $option = self::getOption();

        $feed   = is_array($option['feed'] ?? null) ? self::verifyActive($option['feed']) : [];
        $events = is_array($option['events'] ?? null) ? self::verifyActive($option['events']) : [];

        return array_merge($feed, $events);
    }

    /**
     * Get notification count.
     *
     * @return int
     */
    private static function getCount(): int
    {
        return count(self::get());
    }

    /**
     * Add a new Event Driven notification.
     *
     * @param mixed[] $notification Notification data.
     *
     * @return void
     */
    public static function add($notification): void
    {
        if (!self::isValid($notification)) {
            return;
        }

        $option = self::getOption();

        // Notification ID already exists.
        if (!empty($option['events'][$notification['id']])) {
            return;
        }

        $notification = self::verify([$notification]);
        update_option(
            self::NOTIFICATIONS_OPT_KEY,
            [
                'update'    => $option['update'],
                'feed'      => $option['feed'],
                'events'    => array_merge($notification, $option['events']),
                'dismissed' => $option['dismissed'],
            ]
        );
    }

    /**
     * Determine if notification data is valid.
     *
     * @param mixed[] $notification Notification data.
     *
     * @return bool
     */
    private static function isValid($notification)
    {
        if (empty($notification['id'])) {
            return false;
        }

        return count(self::verify([$notification])) > 0;
    }

    /**
     * Determine if notification has already been dismissed.
     *
     * @param mixed[] $notification Notification data.
     *
     * @return bool
     */
    private static function isDismissed($notification): bool
    {
        $option = self::getOption();

        return !empty($option['dismissed']) && in_array($notification['id'], $option['dismissed']);
    }

    /**
     * Determine if plugin type matches the notification target.
     *
     * @param mixed[] $notification Notification data.
     *
     * @return bool
     */
    private static function isPluginTypeMatch($notification)
    {
        // A specific plugin type is not required.
        if (is_scalar($notification['type'])) {
            $notification['type'] = [$notification['type']];
        }

        if (empty($notification['type'])) {
            return false;
        }

        if (in_array('any', $notification['type'])) {
            return true;
        }

        return in_array(self::getPluginType(), $notification['type'], true);
    }

    /**
     * Determine if notification is expired.
     *
     * @param mixed[] $notification Notification data.
     *
     * @return bool
     */
    private static function isExpired($notification): bool
    {
        return !empty($notification['end']) && time() > strtotime($notification['end']);
    }

    /**
     * Determine if notification existed before installing Duplicator.
     *
     * @param mixed[] $notification Notification data.
     *
     * @return bool
     */
    private static function isExisted($notification): bool
    {
        $installInfo = UpgradePlugin::getInstallInfo();

        return !empty($notification['start']) && $installInfo['time'] > strtotime($notification['start']);
    }

    /**
     * Update notification data from feed.
     *
     * @return void
     */
    private static function update(): void
    {
        $option = self::getOption();

        //Only update twice daily
        if (time() < $option['update'] + DAY_IN_SECONDS / 2) {
            return;
        }

        $data = [
            'update'    => time(),
            'feed'      => self::fetchFeed(),
            'events'    => $option['events'],
            'dismissed' => $option['dismissed'],
        ];

        /**
         * Allow changing notification data before it will be updated in database.
         *
         * @param array $data New notification data.
         */
        $data = (array)apply_filters('duplicator_admin_notifications_update_data', $data);

        update_option(self::NOTIFICATIONS_OPT_KEY, $data);
    }

    /**
     * Enqueue assets on Form Overview admin page.
     *
     * @return void
     */
    private static function enqueues(): void
    {
        if (!self::getCount()) {
            return;
        }

        wp_enqueue_script(
            'dupli-admin-notifications',
            DUPLICATOR_PLUGIN_URL . "assets/js/admin-notifications.js",
            ['jquery'],
            DUPLICATOR_VERSION,
            true
        );

        wp_localize_script(
            'dupli-admin-notifications',
            'dupli_admin_notifications',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce(self::NONCE_KEY),
            ]
        );
    }

    /**
     * Output notifications on Form Overview admin area.
     *
     * @return void
     */
    public static function output(): void
    {
        $notificationsData = self::get();

        if (empty($notificationsData)) {
            return;
        }

        $content_allowed_tags = [
            'br'     => [],
            'em'     => [],
            'strong' => [],
            'span'   => [
                'style' => [],
            ],
            'p'      => [
                'id'    => [],
                'class' => [],
            ],
            'a'      => [
                'href'   => [],
                'target' => [],
                'rel'    => [],
            ],
        ];

        $notifications = [];
        foreach ($notificationsData as $notificationData) {
            // Prepare required arguments.
            $notificationData = wp_parse_args(
                $notificationData,
                [
                    'id'      => 0,
                    'title'   => '',
                    'content' => '',
                    'video'   => '',
                ]
            );

            $title   = self::getComponentData($notificationData['title']);
            $content = self::getComponentData($notificationData['content']);

            if (!$title && !$content) {
                continue;
            }

            $videoData       = self::getComponentData($notificationData['video']);
            $notifications[] = [
                'id'        => $notificationData['id'],
                'title'     => $title,
                'btns'      => self::getButtonsData($notificationData),
                'content'   => wp_kses(wpautop($content), $content_allowed_tags),
                'video_url' => is_string($videoData) ? wp_http_validate_url($videoData) : false,
            ];
        }

        self::enqueues();
        TplMng::getInstance()->render(
            'parts/Notifications/main',
            ['notifications' => $notifications]
        );
    }

    /**
     * Retrieve notification's buttons.
     *
     * @param array<string, mixed> $notification Notification data.
     *
     * @return array<int, mixed>
     */
    private static function getButtonsData($notification): array
    {
        if (empty($notification['btn']) || !is_array($notification['btn'])) {
            return [];
        }

        $buttons = [];
        if (!empty($notification['btn']['main_text']) && !empty($notification['btn']['main_url'])) {
            $buttons[] = [
                'class'  => 'secondary',
                'text'   => $notification['btn']['main_text'],
                'url'    => self::prepareBtnUrl($notification['btn']['main_url']),
                'target' => '_blank',
            ];
        }

        if (!empty($notification['btn']['alt_text']) && !empty($notification['btn']['alt_url'])) {
            $buttons[] = [
                'class'  => 'secondary hollow',
                'text'   => $notification['btn']['alt_text'],
                'url'    => self::prepareBtnUrl($notification['btn']['alt_url']),
                'target' => '_blank',
            ];
        }

        return $buttons;
    }

    /**
     * Retrieve notification's component data by plugin type.
     *
     * @param mixed $data Component data.
     *
     * @return false|mixed
     */
    private static function getComponentData($data)
    {
        if (empty($data['license'])) {
            return $data;
        }

        $pluginType = self::getPluginType();
        return !empty($data['license'][$pluginType]) ? $data['license'][$pluginType] : false;
    }

    /**
     * Retrieve the current plugin type.
     *
     * @return string
     */
    private static function getPluginType(): string
    {
        return strtolower(DUPLICATOR____TYPE);
    }

    /**
     * Prepare button URL.
     *
     * @param string $btnUrl Button url.
     *
     * @return string
     */
    private static function prepareBtnUrl($btnUrl)
    {
        if (empty($btnUrl)) {
            return '';
        }

        $replace_tags = [
            '{admin_url}' => admin_url(),
        ];

        return wp_http_validate_url(str_replace(array_keys($replace_tags), array_values($replace_tags), $btnUrl));
    }

    /**
     * User agent that will be used for the request
     *
     * @return string
     */
    private static function getUserAgent(): string
    {
        return 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url') . '; Duplicator/Lite-' . DUPLICATOR_VERSION;
    }
}
