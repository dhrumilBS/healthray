<?php

/**
 * DUPLICATOR AI READY ADDON
 *
 * @package   Duplicator
 * @copyright (c) 2026, Snap Creek LLC
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

declare(strict_types=1);

namespace Duplicator\Addons\AiReadyAddon;

use Duplicator\Core\Addons\AbstractAddonCore;

/**
 * Main class for the AiReadyAddon: hooks the Abilities API registrar on init.
 */
class AiReadyAddon extends AbstractAddonCore
{
    const ADDON_PATH = __DIR__;

    /**
     * @return void
     */
    public function init(): void
    {
        if (!function_exists('wp_register_ability')) {
            return;
        }

        $registrar = new AbilitiesRegistrar();
        add_action('wp_abilities_api_categories_init', [$registrar, 'registerCategory']);
        add_action('wp_abilities_api_init', [$registrar, 'registerAbilities']);
    }

    /**
     * @return string
     */
    public static function getAddonPath(): string
    {
        return self::ADDON_PATH;
    }

    /**
     * @return string
     */
    public static function getAddonFile(): string
    {
        return __FILE__;
    }
}
