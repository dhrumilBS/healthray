<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteLegacyAddon;

use Duplicator\Addons\LiteLegacyAddon\Models\LegacyBackupMigration;
use Duplicator\Core\Addons\AbstractAddonCore;

/**
 * Migrates backups left by the standalone legacy Lite plugin.
 */
class LiteLegacyAddon extends AbstractAddonCore
{
    /**
     * Initialize the addon.
     *
     * @return void
     */
    public function init(): void
    {
        add_action('duplicator_upgrade', [LegacyBackupMigration::class, 'migrate'], 100, 4);
    }

    /**
     * Addon directory.
     *
     * @return string
     */
    public static function getAddonPath(): string
    {
        return __DIR__;
    }

    /**
     * Addon bootstrap file.
     *
     * @return string
     */
    public static function getAddonFile(): string
    {
        return __FILE__;
    }
}
