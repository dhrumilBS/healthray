<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Controllers\Mocks;

use Duplicator\Addons\LiteBase\Controllers\LiteToolsController;
use Duplicator\Addons\LiteBase\Controllers\Mocks\Settings\MockCapabilitiesSettingsController;
use Duplicator\Core\Controllers\ControllersManager;

/**
 * Registry of the blurred mock upsell surfaces rendered by LiteBase.
 */
final class MockPages
{
    /**
     * Whether the current page is a blurred mock surface with its own upsell popup.
     *
     * @return bool
     */
    public static function isCurrentMockUpsellPage(): bool
    {
        return ControllersManager::isCurrentPage(MockImportPageController::PAGE_SLUG)
            || ControllersManager::isCurrentPage(MockSchedulePageController::PAGE_SLUG)
            || ControllersManager::isCurrentPage(MockStagingPageController::PAGE_SLUG)
            || ControllersManager::isCurrentPage(ControllersManager::TOOLS_SUBMENU_SLUG, LiteToolsController::L2_SLUG_RECOVERY)
            || ControllersManager::isCurrentPage(ControllersManager::TOOLS_SUBMENU_SLUG, LiteToolsController::L2_SLUG_TEMPLATES)
            || ControllersManager::isCurrentPage(ControllersManager::SETTINGS_SUBMENU_SLUG, MockCapabilitiesSettingsController::L2_SLUG);
    }
}
