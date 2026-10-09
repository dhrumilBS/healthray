<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Notifications;

use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Views\TplMng;

/**
 * Renders the footer block at the bottom of the email summary sent to the
 * administrator after backups.
 */
class EmailSummaryFooter
{
    /**
     * Wire the footer block to the email-summary extension hook.
     *
     * @return void
     */
    public static function init(): void
    {
        add_action('duplicator_email_summary_extra_sections', [self::class, 'render'], 9999);
    }

    /**
     * Render the footer block.
     *
     * @return void
     */
    public static function render(): void
    {
        TplMng::getInstance()->render('litebase/mail/email-summary-footer', [
            'upgradeUrl' => LiteBaseLinks::getUpgradeUrl('email-summary', 'Upgrade to PRO'),
        ]);
    }
}
