<?php



defined("ABSPATH") or die("");

use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Email\EmailSummary;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$global = GlobalEntity::getInstance();
?>

<h3 class="title">
    <?php esc_html_e('Email Summary', 'duplicator') ?>
</h3>
<hr size="1" />

<label class="lbl-larger">
    <?php esc_html_e('Frequency', 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <select
        id="email-summary-frequency"
        name="_email_summary_frequency"
        class="margin-0 width-xlarge">
        <?php foreach (EmailSummary::getAllFrequencyOptions() as $key => $label) : ?>
            <option value="<?php echo esc_attr((string) $key); ?>" <?php selected($global->getEmailSummaryFrequency(), $key); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="description">
        <?php
        printf(
            esc_html_x(
                'You can view the email summary example %1$shere%2$s.',
                '%1$s and %2$s are the opening and close <a> tags to the summary preview link',
                'duplicator'
            ),
            '<a href="' . esc_url(EmailSummary::getPreviewLink()) . '" target="_blank">',
            '</a>'
        );
        ?>
    </p>
</div>

<label class="lbl-larger">
    <?php esc_html_e('Recipients', 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <select
        id="email-summary-recipients"
        name="_email_summary_recipients[]" m
        multiple
        class="margin-0 width-xlarge">
        <?php foreach ($global->getEmailSummaryRecipients() as $email) : ?>
            <option value="<?php echo esc_attr($email); ?>" selected><?php echo esc_html($email); ?></option>
        <?php endforeach; ?>
        <?php foreach (EmailSummary::getRecipientSuggestions() as $email) : ?>
            <option value="<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></option>
        <?php endforeach; ?>
    </select>
    <?php if (count($global->getEmailSummaryRecipients()) === 0) : ?>
        <p class="descriptionred">
            <em>
                <span class="maroon">
                    <?php esc_html_e('No recipients entered. Email summary won\'t be sent.', 'duplicator') ?>
                </span>
            </em>
        </p>
    <?php endif; ?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#email-summary-recipients').select2({
            tags: true,
            tokenSeparators: [',', ' '],
            placeholder: '<?php esc_attr_e('Enter email addresses', 'duplicator'); ?>',
            minimumInputLength: 3,
        });
    });
</script>