<?php

declare(strict_types=1);

namespace Duplicator\Views;

use Duplicator\Controllers\SettingsPageController;
use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;
use Duplicator\Models\StaticGlobal;
use Duplicator\Utils\Logging\DupLog;

/**
 * Renders the admin footer surface for Duplicator pages:
 *   - the plugin "Made with ♥" footer inside #wpfooter
 *   - the rate-us text on `admin_footer_text`
 *   - the version suffix on `update_footer`
 *   - the trace log control panel on `admin_footer`
 */
class PluginFooter
{
    /**
     * Register all footer-related hooks.
     *
     * @return void
     */
    public static function init(): void
    {
        if (ControllersManager::getInstance()->isDuplicatorPage()) {
            add_filter('admin_footer_text', [self::class, 'rateUsText'], 1);
            add_filter('update_footer', [self::class, 'versionSuffix'], 99999);
            add_action('in_admin_footer', [self::class, 'render']);
        }

        add_action('admin_footer', [self::class, 'traceLogPanel']);
    }

    /**
     * Render the Duplicator plugin footer inside #wpfooter.
     *
     * @return void
     */
    public static function render(): void
    {
        /** @var array<int, array{label: string, url: string}> $links */
        $links = apply_filters('duplicator_plugin_footer_links', []);

        TplMng::getInstance()->render('parts/plugin-footer', ['links' => $links]);
    }

    /**
     * Replacement for the WP "Thank you for creating with WordPress" footer text:
     * asks the user to rate the plugin on WordPress.org.
     *
     * @return string
     */
    public static function rateUsText(): string
    {
        $text  = '';
        $text .= '<span class="dup-styles" ><i>';
        $text .= sprintf(
            wp_kses(
                _x(
                    'Please rate <strong>Duplicator</strong> %1$s on %2$sWordPress.org%3$s to help us spread the word.',
                    '%1$s represents 5 start symbols linked to wordpress.org review page, %2$s,%2$s represents one,close link',
                    'duplicator'
                ),
                [
                    'a'      => [
                        'href'   => [],
                        'target' => [],
                        'rel'    => [],
                    ],
                    'strong' => [],
                ]
            ),
            '<a href="https://wordpress.org/support/plugin/duplicator/reviews/?filter=5#new-post"
            class="link-style" target="_blank" rel="noopener noreferrer">' .
                '&#9733;&#9733;&#9733;&#9733;&#9733;</a>',
            '<a href="https://wordpress.org/support/plugin/duplicator/reviews/?filter=5#new-post"
            class="link-style" target="_blank" rel="noopener">',
            '</a>'
        );
        return $text . '</i></span>';
    }

    /**
     * Append the Duplicator name and version to the WordPress footer version string.
     *
     * @param mixed $defaultText Default WP footer version text, other hooks may pass a non-string value.
     *
     * @return string
     */
    public static function versionSuffix($defaultText): string
    {
        $canBeString = is_scalar($defaultText) || (is_object($defaultText) && method_exists($defaultText, '__toString'));

        return sprintf(
            '%1$s | %2$s %3$s',
            $canBeString ? (string) $defaultText : '',
            esc_html(DUPLICATOR____NAME),
            esc_html(DUPLICATOR_VERSION)
        );
    }

    /**
     * Render the trace log control panel pinned to the bottom-right corner
     * of every Duplicator admin page when trace logging is enabled.
     *
     * @return void
     */
    public static function traceLogPanel(): void
    {
        if (
            !ControllersManager::getInstance()->isDuplicatorPage() ||
            !StaticGlobal::getTraceLogEnabledOption()
        ) {
            return;
        }

        if (!CapMng::can(CapMng::CAP_SETTINGS, false) && !CapMng::can(CapMng::CAP_CREATE, false)) {
            return;
        }

        $txt_trace_zero = esc_html__('Download', 'duplicator') . ' (0B)';
        $turnOffUrl     = SettingsPageController::getInstance()->getTraceActionUrl(false);
        $traceLogUrl    = ControllersManager::getMenuLink(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            ToolsPageController::L2_SLUG_LOGS
        );

        $ajaxGetTraceUrl = admin_url('admin-ajax.php') . '?' . http_build_query([
            'action' => 'duplicator_get_trace_log',
            'nonce'  => wp_create_nonce('duplicator_get_trace_log'),
        ]);

        if (
            ControllersManager::isCurrentPage(
                ControllersManager::TOOLS_SUBMENU_SLUG,
                ToolsPageController::L2_SLUG_LOGS
            )
        ) {
            $clear_trace_log_js = 'DupliJs.UI.ClearTraceLog(1);';
        } else {
            $clear_trace_log_js = 'DupliJs.UI.ClearTraceLog(0); jQuery("#dupli-trace-txt").html(' . wp_json_encode($txt_trace_zero) . '); ';
        }
        ?>
        <div class="dup-styles">
            <div id="dupli-monitor-trace-area">
                <b><?php esc_html_e('TRACE LOG OPTIONS', 'duplicator'); ?></b><br />
                <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
                    <a class="button tiny hollow gray margin-bottom-0" href="<?php echo esc_url($traceLogUrl); ?>" target="_duptracelog">
                        <i class="fa fa-file-alt"></i> <?php esc_html_e('View', 'duplicator'); ?>
                    </a>
                    <a class="button tiny hollow gray margin-bottom-0" onclick="<?php echo esc_attr($clear_trace_log_js); ?>">
                        <i class="fa fa-times"></i> <?php esc_html_e('Clear', 'duplicator'); ?>
                    </a>
                    <a
                        class="button tiny hollow gray margin-bottom-0"
                        onclick="<?php echo esc_attr('location.href = ' . json_encode($ajaxGetTraceUrl) . ';'); ?>">
                        <i class="fa fa-download"></i> <span id="dupli-trace-txt">
                            <?php echo esc_html__('Download', 'duplicator') . ' (' . esc_html(DupLog::getTraceStatus()) . ')'; ?>
                        </span>
                    </a>
                <?php } ?>
                <?php if (CapMng::can(CapMng::CAP_SETTINGS, false)) { ?>
                    <a class="button tiny hollow gray margin-bottom-0" href="<?php echo esc_url($turnOffUrl); ?>">
                        <i class="fa fa-power-off"></i> <?php echo esc_html__('Turn Off', 'duplicator'); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
        <?php
    }
}
