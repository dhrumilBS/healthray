<?php

declare(strict_types=1);

namespace Duplicator\Models\ActivityLog;

use Duplicator\Core\CapMng;
use Duplicator\Core\Views\TplMng;

/**
 * Log event for loopback connectivity test failures
 */
class LogEventLoopbackFailure extends AbstractLogEvent
{
    /**
     * Class constructor
     *
     * @param string               $failureReason Human-readable reason for the loopback failure
     * @param array<string, mixed> $context       Diagnostic context: `url`, `http_code`, `body_excerpt`, `wp_error_code`
     */
    public function __construct(string $failureReason, array $context = [])
    {
        $this->subType  = 'loopback_failure';
        $this->severity = self::SEVERITY_WARNING;

        $this->data = [
            'timestamp'      => time(),
            'failure_reason' => $failureReason,
            'url'            => isset($context['url']) ? (string) $context['url'] : '',
            'http_code'      => isset($context['http_code']) ? (int) $context['http_code'] : 0,
            'body_excerpt'   => isset($context['body_excerpt']) ? (string) $context['body_excerpt'] : '',
            'wp_error_code'  => isset($context['wp_error_code']) ? (string) $context['wp_error_code'] : '',
        ];

        $this->title = __('Loopback Connectivity Failure', 'duplicator');
    }

    /**
     * Create and save a loopback failure log event
     *
     * @param string               $failureReason Human-readable reason for the loopback failure
     * @param array<string, mixed> $context       Diagnostic context (see constructor)
     *
     * @return self
     */
    public static function create(string $failureReason, array $context = []): self
    {
        $logEvent = new self($failureReason, $context);
        $logEvent->save();
        return $logEvent;
    }

    /**
     * Return entity type identifier
     *
     * @return string
     */
    public static function getType(): string
    {
        return 'loopback_failure';
    }

    /**
     * Return entity type label
     *
     * @return string
     */
    public static function getTypeLabel(): string
    {
        return __('Connectivity', 'duplicator');
    }

    /**
     * Return required capability for this log event
     *
     * @return string
     */
    public static function getCapability(): string
    {
        return CapMng::CAP_BASIC;
    }

    /**
     * Return short description
     *
     * @return string
     */
    public function getShortDescription(): string
    {
        $reason = $this->data['failure_reason'] ?? 'Unknown';
        return sprintf(
            __('Loopback test failed: %s', 'duplicator'),
            $reason
        );
    }

    /**
     * Display detailed information in html format
     *
     * @return void
     */
    public function detailHtml(): void
    {
        $failureReason = $this->data['failure_reason'] ?? 'Unknown';
        $url           = (string) ($this->data['url'] ?? '');
        $httpCode      = (int) ($this->data['http_code'] ?? 0);
        $wpErrorCode   = (string) ($this->data['wp_error_code'] ?? '');
        $bodyExcerpt   = (string) ($this->data['body_excerpt'] ?? '');
        ?>
        <div class="dup-log-detail-meta">
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Event:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php esc_html_e('Loopback Connectivity Failure', 'duplicator'); ?></span>
            </div>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Failure Reason:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($failureReason); ?></span>
            </div>

            <?php if ($url !== '') : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Tested URL:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_url($url); ?></span>
            </div>
            <?php endif; ?>

            <?php if ($httpCode > 0) : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('HTTP Code:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html((string) $httpCode); ?></span>
            </div>
            <?php endif; ?>

            <?php if ($wpErrorCode !== '') : ?>
            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('WP Error Code:', 'duplicator'); ?></strong>
                <span class="dup-log-type"><?php echo esc_html($wpErrorCode); ?></span>
            </div>
            <?php endif; ?>

            <div class="dup-log-type-wrapper">
                <strong><?php esc_html_e('Impact:', 'duplicator'); ?></strong>
                <span class="dup-log-type">
                    <?php esc_html_e(
                        'Packages will use client-side kickoff (browser polling) instead of server-side kickoff.',
                        'duplicator'
                    ); ?>
                </span>
            </div>

            <?php if ($bodyExcerpt !== '') : ?>
                <hr>
                <div class="dup-log-type-wrapper">
                    <strong><?php esc_html_e('Response Body (excerpt):', 'duplicator'); ?></strong>
                </div>
                <?php TplMng::getInstance()->render(
                    'admin_pages/activity_log/parts/error_log_context',
                    [
                        'logLines'   => explode("\n", $bodyExcerpt),
                        'logUrl'     => '',
                        'footerText' => sprintf(
                            /* translators: %d: number of characters shown from the HTTP response body */
                            __('Showing first %d characters of the HTTP response body', 'duplicator'),
                            strlen($bodyExcerpt)
                        ),
                    ]
                ); ?>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Return object type label
     *
     * @return string
     */
    public function getObjectTypeLabel(): string
    {
        return __('Loopback Failure', 'duplicator');
    }
}
