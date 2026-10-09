<?php

namespace Duplicator\Views\UI;

/**
 * Used to generate a thick box inline dialog such as an alert or confirm pop-up
 */
class UiMessages
{
    const UNIQUE_ID_PREFIX = 'dup_ui_msg_';
    const NOTICE           = 'updated';
    const WARNING          = 'update-nag';
    const ERROR            = 'error';

    private static int $unique_id = 0;

    private string $id;
    private string $type            = self::NOTICE;
    public string $content          = '';
    public bool $hide_on_init       = true;
    public bool $is_dismissible     = false;
    public int $auto_hide_delay     = 0;
    public string $callback_on_show = '';
    public string $callback_on_hide = '';

    /**
     * Class constructor
     *
     * @param string $content Content of the message
     * @param string $type    Type of the message (NOTICE, WARNING, ERROR)
     */
    public function __construct(string $content = '', string $type = self::NOTICE)
    {
        self::$unique_id++;
        $this->id = self::UNIQUE_ID_PREFIX . self::$unique_id;

        $this->content = (string) $content;
        $this->type    = $type;
    }

    /**
     * Get the classes for the notice
     *
     * @param string[] $classes Additional classes
     *
     * @return string
     */
    protected function getNoticeClasses(array $classes = []): string
    {
        if ($this->is_dismissible) {
            $classes[] = 'is-dismissible';
        }

        $result = array_merge(['notice', $this->type], $classes);
        return trim(implode(' ', $result));
    }

    /**
     * Initialize the message
     *
     * @return void
     */
    public function initMessage(): void
    {
        $classes = [];
        if ($this->hide_on_init) {
            $classes[] = 'no-display';
        }
        ?>
        <div id="<?php echo esc_attr($this->id); ?>" class="<?php echo esc_attr($this->getNoticeClasses($classes)); ?>">
            <p class="msg-content">
                <?php echo $this->content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                ?>
            </p>
        </div>
        <?php
    }

    /**
     * Update the message content
     *
     * @param string $jsVarName Name of the variable containing the new content
     *
     * @return void
     */
    public function updateMessage(string $jsVarName): void
    {
        echo 'jQuery("#' . esc_js($this->id) . ' > .msg-content").html(' . esc_js($jsVarName) . ');';
    }

    /**
     * Show the message
     *
     * @return void
     */
    public function showMessage(): void
    {
        echo 'jQuery("body, html").animate({ scrollTop: 0 }, 500 );';
        echo 'jQuery("#' . esc_js($this->id) . '").fadeIn( "slow", function() { jQuery(this).removeClass("no_display");});';

        if ($this->auto_hide_delay > 0) {
            echo 'setTimeout(function () { jQuery("#' . esc_js($this->id) . '").fadeOut( "slow", function() { jQuery(this).addClass("no_display");}); }, '
                . (int) $this->auto_hide_delay . ');';
        }
    }
}
