<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Notifications;

use Duplicator\Addons\LiteBase\Controllers\Mocks\MockPages;
use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Views\AdminNotices;

/**
 * Provides title, message and button data for the multisite-unsupported notice.
 */
class MultisiteNotice
{
    /**
     * Wire the notice filter.
     *
     * @return void
     */
    public static function init(): void
    {
        if (!is_multisite()) {
            return;
        }
        add_filter('duplicator_multisite_unsupported_notice', [self::class, 'filterNotice']);
        add_filter('duplicator_admin_notices', [self::class, 'filterAdminNotices']);
    }

    /**
     * Remove the multisite-unsupported notice on the blurred mock upsell surfaces,
     * which already carry their own upgrade popup.
     *
     * @param array<int, callable> $notices Registered admin notice callbacks
     *
     * @return array<int, callable>
     */
    public static function filterAdminNotices(array $notices): array
    {
        if (!MockPages::isCurrentMockUpsellPage()) {
            return $notices;
        }

        return array_values(array_filter(
            $notices,
            fn($notice): bool => $notice !== [
                AdminNotices::class,
                'multisiteUnsupportedNotice',
            ]
        ));
    }

    /**
     * @param array{title:string, message:string, buttonUrl:string, buttonLabel:string} $data Default notice data.
     *
     * @return array{title:string, message:string, buttonUrl:string, buttonLabel:string}
     */
    public static function filterNotice(array $data): array
    {
        $data['title']       = __('Duplicator Lite does not officially support WordPress multisite functionality', 'duplicator');
        $data['message']     = __(
            'Upgrade to unlock the ability to create backups and do advanced migrations on multi-site installations!',
            'duplicator'
        );
        $data['buttonUrl']   = LiteBaseLinks::getUpgradeUrl('lite-multisite-notice', 'Upgrade now!');
        $data['buttonLabel'] = __('Upgrade Now!', 'duplicator');

        return $data;
    }
}
