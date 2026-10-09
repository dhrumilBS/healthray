<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Notifications;

use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Views\TplMng;

class PackageComponentsLite
{
    const ACTION_MEDIA  = 'media';
    const ACTION_CUSTOM = 'custom';

    /**
     * @return void
     */
    public static function init(): void
    {
        add_filter('duplicator_component_preset_actions', [self::class, 'addDisabledPresets']);
        add_filter('duplicator_component_action_label', [self::class, 'getActionLabel'], 10, 2);
        add_filter('duplicator_component_action_icon', [self::class, 'getActionIcon'], 10, 2);
        add_action('duplicator_package_components_after_presets', [self::class, 'renderMessage']);
    }

    /**
     * Add Media Only and Custom presets as disabled.
     *
     * @param array<array{value: string, disabled: bool}> $actions current preset actions
     *
     * @return array<array{value: string, disabled: bool}>
     */
    public static function addDisabledPresets(array $actions): array
    {
        $actions[] = [
            'value'    => self::ACTION_MEDIA,
            'disabled' => true,
        ];
        $actions[] = [
            'value'    => self::ACTION_CUSTOM,
            'disabled' => true,
        ];

        return $actions;
    }

    /**
     * @param string $label  current label
     * @param string $action the component action
     *
     * @return string
     */
    public static function getActionLabel(string $label, string $action): string
    {
        if ($label !== '') {
            return $label;
        }

        switch ($action) {
            case self::ACTION_MEDIA:
                return __('Media Only', 'duplicator');
            case self::ACTION_CUSTOM:
                return __('Custom', 'duplicator');
            default:
                return $label;
        }
    }

    /**
     * @param string $icon   current icon HTML
     * @param string $action the component action
     *
     * @return string
     */
    public static function getActionIcon(string $icon, string $action): string
    {
        if ($icon !== '') {
            return $icon;
        }

        switch ($action) {
            case self::ACTION_MEDIA:
                return '<i class="fa-solid fa-images"></i>';
            case self::ACTION_CUSTOM:
                return '<i class="fa-solid fa-puzzle-piece"></i>';
            default:
                return $icon;
        }
    }

    /**
     * @return void
     */
    public static function renderMessage(): void
    {
        TplMng::getInstance()->render('litebase/packages/components-message', [
            'upgradeUrl' => LiteBaseLinks::getUpgradeUrl('package-components-lite', 'upgrade to Pro'),
        ]);
    }
}
