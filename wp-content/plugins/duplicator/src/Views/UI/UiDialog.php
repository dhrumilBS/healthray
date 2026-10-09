<?php

namespace Duplicator\Views\UI;

use Duplicator\Core\Views\TplMng;

/**
 * Used to generate a thick box inline dialog such as an alert or confirm pop-up
 */
class UiDialog
{
    const TB_WINDOW_CLASS = 'dup-tb-wrapper dup-styles';

    /** @var int */
    protected static $uniqueIdCounter = 0;
    /** @var string  if not empty contains class of box */
    public $boxClass = '';
    /** @var bool if false don't disaply ok,confirm and cancel buttons */
    public $showButtons = true;
    /** @var bool if false don't disaply textarea */
    public $showTextArea = false;
    /** @var integer rows attribute of textarea */
    public $textAreaRows = 15;
    /** @var int cols attribute of textarea */
    public $textAreaCols = 100;
    /** @var string if not empty set class on wrapper buttons div */
    public $wrapperClassButtons;
    /** @var string The title that shows up in the dialog */
    public $title = '';
    /** @var array<string, mixed> The message displayed in the body of the dialog */
    public $templateArgs = [];
    /** @var string Path to the template to be displayed inside the modal */
    public $templatePath = '';
    /** @var string The message displayed in the body of the dialog */
    public $message = '';
    /** @var int The width of the dialog the default is used if not set */
    public $width = 500;
    /** @var int The height of the dialog the default is used if not set */
    public $height = 225;
    /** @var string When the progress meter is running show this text, Available only on confirm dialogs */
    public $progressText;
    /** @var bool When true a progress meter will run until page is reloaded, Available only on confirm dialogs */
    public $progressOn = true;
    /** @var ?string The javascript call back method to call when the 'Yes' or 'Ok' button is clicked */
    public $jsCallback;
    /** @var string */
    public $okText = '';
    /** @var string */
    public $cancelText = '';
    /** @var bool If true close dialog on confirm */
    public $closeOnConfirm = false;
    /** @var string The id given to the full dialog */
    private string $id;
    /** @var int A unique id that is added to all id elements */
    private $uniqid = 0;

    /**
     *  Init this object when created
     */
    public function __construct()
    {
        add_thickbox();
        $this->progressText = __('Processing please wait...', 'duplicator');
        $this->uniqid       = ++self::$uniqueIdCounter;
        $this->id           = 'dupli-dlg-' . $this->uniqid;
        $this->okText       = __('OK', 'duplicator');
        $this->cancelText   = __('Cancel', 'duplicator');
    }

    /**
     *
     * @return int
     */
    public function getUniqueIdCounter()
    {
        return $this->uniqid;
    }

    /**
     * Gets the unique id that is assigned to each instance of a dialog
     *
     * @return string The unique ID of this dialog
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * Gets the unique id that is assigned to each instance of a dialogs message text
     *
     * @return string The unique ID of the message
     */
    public function getMessageID(): string
    {
        return "{$this->id}_message";
    }

    /**
     * Display The html content used for the alert dialog
     *
     * @return void
     */
    public function initAlert(): void
    {
        ?>
        <div id="<?php echo esc_attr($this->id); ?>" style="display:none;">
            <?php if ($this->showTextArea) { ?>
                <div class="dupli-dlg-textarea-caption">Status</div>
                <textarea
                    id="<?php echo esc_attr($this->id); ?>_textarea"
                    class="dupli-dlg-textarea"
                    rows="<?php echo (int) $this->textAreaRows; ?>"
                    cols="<?php echo (int) $this->textAreaCols; ?>">
                </textarea>
            <?php } ?>
            <div id="<?php echo esc_attr($this->id); ?>-alert-txt" class="dupli-dlg-alert-txt <?php echo esc_attr($this->boxClass); ?>">
                <span id="<?php echo esc_attr($this->id); ?>_message">
                    <?php
                    if (strlen($this->templatePath) > 0) {
                        TplMng::getInstance()->render($this->templatePath, $this->templateArgs, true);
                    } else {
                        echo $this->message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    }
                    ?>
                </span>
            </div>
            <?php if ($this->showButtons) { ?>
                <div class="dupli-dlg-alert-btns <?php echo esc_attr($this->wrapperClassButtons); ?>">
                    <input
                        id="<?php echo esc_attr($this->id); ?>-confirm"
                        type="button"
                        class="button primary dup-dialog-confirm"
                        value="<?php echo esc_attr($this->okText); ?>"
                        onclick="<?php echo esc_attr($this->closeAlert()); ?>">
                </div>
            <?php } ?>
        </div>
        <?php
    }

    /**
     * Shows the alert base JS code used to display when needed
     *
     * @return void
     */
    public function showAlert(): void
    {
        echo "tb_show('" . esc_js($this->title) . "', '#TB_inline?width=" . (int) $this->width .
            "&height=" . (int) $this->height . "&inlineId=" . esc_js($this->id)  . "');" .
            "var styleData = jQuery('#TB_window').attr('style') + 'height: " . (int) $this->height . "px !important';\n" .
            "jQuery('#TB_window').attr('style', styleData);" .
            "jQuery('#TB_window').addClass(" . json_encode(self::TB_WINDOW_CLASS) . ");" .
            "DuplicatorTooltip.reload();";
    }


    /**
     * Close tick box
     *
     * @return string
     */
    public function closeAlert(): string
    {
        $onClickClose = '';
        if (!is_null($this->jsCallback)) {
            $onClickClose .= $this->jsCallback . ';';
        }
        return $onClickClose . 'tb_remove();';
    }

    /**
     * js code to update html message content from js var name
     *
     * @param string $jsVarName js var name
     *
     * @return void
     */
    public function updateMessage(string $jsVarName): void
    {
        echo '$("#' . esc_js($this->getID()) . '_message").html(' . esc_js($jsVarName) . ');';
    }

    /**
     * js code to update textarea content from js var name
     *
     * @param string $jsVarName js var name
     *
     * @return void
     */
    public function updateTextareaMessage(string $jsVarName): void
    {
        echo '$("#' . esc_js($this->getID()) . '_textarea").val(' . esc_js($jsVarName) . ');';
    }

    /**
     * Shows the confirm base JS code used to display when needed
     *
     * @return void
     */
    public function initConfirm(): void
    {
        $progress_func2 = '';

        $onClickConfirm = '';
        if (!is_null($this->jsCallback)) {
            $onClickConfirm .= $this->jsCallback . ';';
        }

        //Enable the progress spinner
        if ($this->progressOn) {
            $progress_func1  = "__dupli_dialog_" . $this->uniqid;
            $progress_func2  = ";{$progress_func1}(this)";
            $onClickConfirm .= $progress_func2 . ';';
        }

        if ($this->closeOnConfirm) {
            $onClickConfirm .= 'tb_remove();';
        } ?>
        <div id="<?php echo esc_attr($this->id); ?>" style="display:none">
            <div class="dupli-dlg-confirm-txt" id="<?php echo esc_attr($this->id); ?>-confirm-txt">
                <div id="<?php echo esc_attr($this->id); ?>_message">
                    <?php echo $this->message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                    ?>
                </div>
                <?php
                if ($this->progressOn) {
                    $tplMng = TplMng::getInstance();
                    $tplMng->render(
                        'parts/dialogs/confirm_progress',
                        [
                            'id'            => $this->id,
                            'function_name' => $progress_func1,
                            'progress_text' => $this->progressText,
                        ]
                    );
                }
                ?>
            </div>
            <?php if ($this->showButtons) { ?>
                <div class="dupli-dlg-confirm-btns <?php echo esc_attr($this->wrapperClassButtons); ?>">
                    <input
                        id="<?php echo esc_attr($this->id); ?>-cancel"
                        type="button"
                        class="button secondary hollow dup-dialog-cancel"
                        value="<?php echo esc_attr($this->cancelText); ?>"
                        onclick="tb_remove();">
                    <input
                        id="<?php echo esc_attr($this->id); ?>-confirm"
                        type="button"
                        class="button primary dup-dialog-confirm"
                        value="<?php echo esc_attr($this->okText); ?>"
                        onclick="<?php echo esc_attr($onClickConfirm); ?>">
                </div>
            <?php  } ?>
        </div>
        <?php
    }

    /**
     * Shows the confirm base JS code used to display when needed
     *
     * @return void
     */
    public function showConfirm(): void
    {
        self::showAlert();
    }
}
