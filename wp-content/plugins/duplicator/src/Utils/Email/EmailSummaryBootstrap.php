<?php

namespace Duplicator\Utils\Email;

use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Package\Storage\UploadInfo;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Package\AbstractPackage;
use Duplicator\Utils\CronUtils;
use Exception;
use Throwable;

/**
 * Email summary bootstrap
 */
class EmailSummaryBootstrap
{
    const CRON_HOOK = 'duplicator_email_summary_cron';

    /**
     * Init
     *
     * @return void
     */
    public static function init(): void
    {
        //Init Preview page
        \Duplicator\Controllers\EmailSummaryPreviewPageController::getInstance();

        //Storage hooks
        add_action('duplicator_after_storage_create', function ($storageId): void {
            EmailSummary::getInstance()->addStorage($storageId);
        });
        add_action('duplicator_after_storage_delete', function ($storageId): void {
            EmailSummary::getInstance()->removeStorage($storageId);
        });

        //Package hooks
        add_action('duplicator_build_completed', function (AbstractPackage $package): void {
            EmailSummary::getInstance()->addPackage($package);
        });
        add_action('duplicator_build_fail', function (AbstractPackage $package, int $previousStatus, Throwable $exception): void {
            EmailSummary::getInstance()->addFailed($package);
        }, 10, 3);

        //Backup transfer hooks
        add_action('duplicator_transfer_failed', function (UploadInfo $uploadInfo): void {
            if ($uploadInfo->isDownloadFromRemote()) {
                return;
            }

            EmailSummary::getInstance()->addFailedUpload($uploadInfo);
        });

        add_action('duplicator_transfer_cancelled', function (UploadInfo $uploadInfo): void {
            if ($uploadInfo->isDownloadFromRemote()) {
                return;
            }

            EmailSummary::getInstance()->addCancelledUpload($uploadInfo);
        });

        add_action('duplicator_upload_complete', function (UploadInfo $uploadInfo): void {
            if ($uploadInfo->isDownloadFromRemote()) {
                return;
            }

            EmailSummary::getInstance()->addSuccessfulUpload($uploadInfo);
        });
    }

    /**
     * Init cron on activation
     *
     * @return void
     */
    public static function activationAction(): void
    {
        $frequency = GlobalEntity::getInstance()->getEmailSummaryFrequency();
        if ($frequency === EmailSummary::SEND_FREQ_NEVER) {
            return;
        }

        if (self::updateCron($frequency) == false) {
            DupLog::trace("FAILED TO INIT EMAIL SUMMARY CRON. Frequency: {$frequency}");
        }
    }

    /**
     * Removes cron on deactivation
     *
     * @return void
     */
    public static function deactivationAction(): void
    {
        if (self::updateCron(EmailSummary::SEND_FREQ_NEVER) == false) {
            DupLog::trace("FAILED TO REMOVE EMAIL SUMMARY CRON.");
        }
    }

    /**
     * Updates the WP Cron job base on frequency or settings
     *
     * @param string $frequency The frequency
     *
     * @return bool True if the cron was updated or false on error
     */
    private static function updateCron($frequency = '')
    {
        if (strlen($frequency) === 0) {
            $frequency = GlobalEntity::getInstance()->getEmailSummaryFrequency();
        }

        if ($frequency === EmailSummary::SEND_FREQ_NEVER) {
            if (wp_next_scheduled(self::CRON_HOOK)) {
                //have to check return like this because
                //wp_clear_scheduled_hook returns void in WP < 5.1
                return !self::isFalseOrWpError(wp_clear_scheduled_hook(self::CRON_HOOK));
            } else {
                return true;
            }
        } else {
            if (wp_next_scheduled(self::CRON_HOOK) && self::isFalseOrWpError(wp_clear_scheduled_hook(self::CRON_HOOK))) {
                return false;
            }

            return !self::isFalseOrWpError(
                wp_schedule_event(
                    self::getFirstRunTime($frequency),
                    self::getCronSchedule($frequency),
                    self::CRON_HOOK
                )
            );
        }
    }

    /**
     * Update next send time on frequency setting change
     *
     * @param string $oldFrequency The old frequency
     * @param string $newFrequency The new frequency
     *
     * @return bool True if the cron was updated or false on error
     */
    public static function updateFrequency($oldFrequency, $newFrequency)
    {
        if ($oldFrequency === $newFrequency) {
            return true;
        }

        return self::updateCron($newFrequency);
    }

    /**
     * Get the cron schedule
     *
     * @param string $frequency The frequency
     *
     * @return string
     */
    private static function getCronSchedule($frequency): string
    {
        switch ($frequency) {
            case EmailSummary::SEND_FREQ_DAILY:
                return CronUtils::INTERVAL_DAILY;
            case EmailSummary::SEND_FREQ_WEEKLY:
                return CronUtils::INTERVAL_WEEKLY;
            case EmailSummary::SEND_FREQ_MONTHLY:
                return CronUtils::INTERVAL_MONTHLY;
            default:
                throw new Exception("Unknown frequency: " . $frequency);
        }
    }

    /**
     * Set next send time based on frequency
     *
     * @param string $frequency Frequency
     *
     * @return int
     */
    private static function getFirstRunTime($frequency)
    {
        switch ($frequency) {
            case EmailSummary::SEND_FREQ_DAILY:
                $firstRunTime = strtotime('tomorrow 14:00');
                break;
            case EmailSummary::SEND_FREQ_WEEKLY:
                $firstRunTime = strtotime('next monday 14:00');
                break;
            case EmailSummary::SEND_FREQ_MONTHLY:
                $firstRunTime = strtotime('first day of next month 14:00');
                break;
            case EmailSummary::SEND_FREQ_NEVER:
                return 0;
            default:
                throw new Exception("Unknown frequency: " . $frequency);
        }

        return $firstRunTime - SnapWP::getGMTOffset();
    }

    /**
     * Send email
     *
     * @return void
     */
    public static function send(): void
    {
        DupLog::trace("CRON: Sending email summary");
        $recipients = GlobalEntity::getInstance()->getEmailSummaryRecipients();
        $frequency  = GlobalEntity::getInstance()->getEmailSummaryFrequency();
        if (count($recipients) === 0 || $frequency === EmailSummary::SEND_FREQ_NEVER) {
            DupLog::trace("CRON: No recipients or frequency is never");
            return;
        }

        $parsedHomeUrl = wp_parse_url(home_url());
        $siteDomain    = ($parsedHomeUrl['host'] ?? '');

        if (is_multisite() && isset($parsedHomeUrl['path'])) {
            $siteDomain .= $parsedHomeUrl['path'];
        }

        // Emails use the site language regardless of the sending request's locale
        $switchedLocale = switch_to_locale(get_locale());

        $subject = sprintf(
            _x(
                'Your Duplicator Summary for %s',
                '%s is the site domain',
                'duplicator'
            ),
            $siteDomain
        );

        $content = TplMng::getInstance()->render('mail/email_summary', EmailSummary::getInstance()->getData(), false);

        $sent = wp_mail($recipients, $subject, $content, ['Content-Type: text/html; charset=UTF-8']);

        if ($switchedLocale) {
            restore_previous_locale();
        }

        if (!$sent) {
            DupLog::trace("FAILED TO SEND EMAIL SUMMARY.");
            DupLog::traceObject("RECIPIENTS: ", $recipients);
            return;
        }

        DupLog::trace("EMAIL SUMMARY SENT SUCCESSFULLY.");
        EmailSummary::getInstance()->reset();
    }

    /**
     * Returns true if is false or wp_error
     *
     * @param mixed $value The value
     *
     * @return bool
     */
    private static function isFalseOrWpError($value): bool
    {
        return $value === false || is_wp_error($value);
    }
}
