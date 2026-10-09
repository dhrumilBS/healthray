<?php

namespace Duplicator\Models\ActivityLog;

/**
 * Unknown log event, used when a log event type is not found
 */
final class LogEventUnknown extends AbstractLogEvent
{
    /**
     * Return entity type identifier
     *
     * @return string
     */
    public static function getType(): string
    {
        return 'unknown';
    }

    /**
     * Return entity type label
     *
     * @return string
     */
    public static function getTypeLabel(): string
    {
        return __('Unknown', 'duplicator');
    }

    /**
     * Return required capability for this log event
     *
     * @return string
     */
    public static function getCapability(): string
    {
        return \Duplicator\Core\CapMng::CAP_BASIC;
    }

    /**
     * Return short description
     *
     * @return string
     */
    public function getShortDescription(): string
    {
        return __('Unknown log event', 'duplicator');
    }

    /**
     * Display detailed information in html format
     *
     * @return void
     */
    public function detailHtml(): void
    {
        esc_html_e('Unknown log event', 'duplicator');
    }

    /**
     * Save entity
     *
     * @return bool True on success, or false on error.
     */
    public function save(): bool
    {
        // Can't save unknown log event
        return false;
    }
}
