<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Menu;

use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;

class PluginActionLink
{
    const CSS_CLASS = 'dupli-plugins-list-pro-upgrade';

    /**
     * @return void
     */
    public static function register(): void
    {
        $basename = plugin_basename(DUPLICATOR____FILE);

        add_filter('plugin_action_links_' . $basename, [self::class, 'addUpgradeLink']);
        add_filter('network_admin_plugin_action_links_' . $basename, [self::class, 'addUpgradeLink']);
    }

    /**
     * @param string[] $links current plugin action link HTML strings
     *
     * @return string[]
     */
    public static function addUpgradeLink(array $links): array
    {
        $url = LiteBaseLinks::getUpgradeUrl('plugin-actions-link', 'Upgrade to Pro');

        $html = sprintf(
            '<a class="%1$s" href="%2$s" target="_blank" rel="noopener noreferrer"><strong>%3$s</strong></a>',
            esc_attr(self::CSS_CLASS),
            esc_url($url),
            esc_html__('Upgrade to Pro', 'duplicator')
        );

        array_unshift($links, $html);

        return $links;
    }
}
