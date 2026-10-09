<?php

namespace Duplicator\Package;

use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\Rules\EncryptionRule;
use Duplicator\Core\Views\TplMng;

class SettingsUtils
{
    /**
     * Return true if archive encryption is available
     *
     * @param string $unavaliableMessage if encryption isn't available the reason is put here (can contain HTML)
     *
     * @return bool
     */
    public static function isArchiveEncryptionAvailable(&$unavaliableMessage = ''): bool
    {
        $availability = OptionsManager::getInstance()->availability(EncryptionRule::OPTION_KEY);
        if ($availability->isAvailable(true)) {
            return true;
        }

        $unavaliableMessage = TplMng::getInstance()->render(
            'parts/requirements/availability_message',
            [
                'failedRequirements' => $availability->getFailedRequirements(true),
                'messages'           => $availability->getMessages(true),
            ],
            false
        );
        return false;
    }
}
