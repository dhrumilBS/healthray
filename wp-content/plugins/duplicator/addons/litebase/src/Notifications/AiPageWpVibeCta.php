<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Notifications;

use Duplicator\Addons\LiteBase\Utils\ExtraPlugins\ExtraPluginsMng;
use Duplicator\Core\Views\TplMng;

/**
 * Replaces the plain WPVibe link on the Tools > AI page with an installable
 * card, so Lite users can install WPVibe without leaving the page.
 */
class AiPageWpVibeCta
{
    /**
     * Register hooks.
     *
     * @return void
     */
    public static function init(): void
    {
        add_action('duplicator_tools_ai_cta', [self::class, 'render']);
    }

    /**
     * Render the WPVibe install card on the Tools > AI page.
     *
     * @return void
     */
    public static function render(): void
    {
        if (!current_user_can('install_plugins')) {
            return;
        }

        $plugin = ExtraPluginsMng::getInstance()->getBySlug(ExtraPluginsMng::SLUG_WPVIBE);
        if ($plugin === null) {
            return;
        }

        TplMng::getInstance()->render('litebase/tools/ai-wpvibe', ['plugin' => $plugin]);
    }
}
