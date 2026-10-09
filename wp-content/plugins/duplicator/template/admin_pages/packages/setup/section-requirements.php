<?php

/**
 * @package   Duplicator
 * @copyright (c) 2026, Snap Creek LLC
 */

defined("ABSPATH") or die("");

use Duplicator\Core\Options\OptionsUIHelper;
use Duplicator\Core\Options\Requirements\ConfigValidation;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$validation = $tplMng->getDataValueObjRequired('validation', ConfigValidation::class);

if ($validation->isValid()) {
    return;
}
?>
<div class="dup-box dup-requirements-wrapper">
    <div class="dup-box-title">
        <i class="far fa-check-circle"></i>
        <?php esc_html_e("Requirements:", 'duplicator'); ?> <div class="dup-sys-fail">Fail</div>
        <button class="dup-box-arrow">
            <span class="screen-reader-text">
                <?php esc_html_e('Toggle panel:', 'duplicator') ?> <?php esc_html_e('Requirements:', 'duplicator') ?>
            </span>
        </button>
    </div>
    <div class="dup-box-panel">
        <div class="dup-sys-section">
            <i><?php esc_html_e("System requirements must pass for the Duplicator to work properly.  Click each link for details.", 'duplicator'); ?></i>
        </div>

        <?php foreach ($validation->getFailedBaseline() as $requirement) { ?>
            <div class='dup-sys-req'>
                <div class='dup-sys-title'>
                    <a><?php echo esc_html($requirement->getLabel()); ?></a>
                    <div><?php esc_html_e('Fail', 'duplicator'); ?></div>
                </div>
                <div class="dup-sys-info dup-info-box">
                    <?php
                    $tplMng->render(
                        'parts/requirements/availability_message',
                        [
                            'failedRequirements' => [$requirement],
                            'messages'           => [],
                        ]
                    );
                    ?>
                </div>
            </div>
        <?php } ?>

        <?php foreach ($validation->getOptionFailures() as $failure) { ?>
            <div class='dup-sys-req'>
                <div class='dup-sys-title'>
                    <a>
                        <?php
                        printf(
                            esc_html_x(
                                '%s setting',
                                '%s is the backup option label, e.g. "Archive Engine"',
                                'duplicator'
                            ),
                            esc_html($failure->getOptionLabel())
                        );
                        ?>
                    </a>
                    <div><?php esc_html_e('Fail', 'duplicator'); ?></div>
                </div>
                <div class="dup-sys-info dup-info-box">
                    <?php if (!OptionsUIHelper::hasAvailableValue($failure->getOptionKey())) : ?>
                        <?php OptionsUIHelper::renderOptionError($failure->getOptionKey()); ?>
                    <?php else : ?>
                        <p>
                            <?php
                            printf(
                                esc_html__(
                                    'The stored %s setting is not available on this server. Update it in the backup settings.',
                                    'duplicator'
                                ),
                                esc_html($failure->getOptionLabel())
                            );
                            ?>
                        </p>
                        <?php
                        $tplMng->render(
                            'parts/requirements/availability_message',
                            [
                                'failedRequirements' => $failure->getFailedRequirements(),
                                'messages'           => $failure->getMessages(),
                            ]
                        );
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php } ?>

        <!-- ONLINE SUPPORT -->
        <div class="dup-sys-contact">
            <?php
            printf(
                "<i class='fa fa-question-circle'></i> %s <a href='" . esc_attr(DUPLICATOR_TECH_FAQ_URL) . "' target='_blank'>[%s]</a>",
                esc_html__("For additional help please see the ", 'duplicator'),
                esc_html__("online FAQs", 'duplicator')
            );
            ?>
        </div>

    </div>
</div>
<script>
    //INIT
    jQuery(document).ready(function($) {
        DupliJs.Pack.ToggleSystemDetails = function(anchor) {
            $(anchor).parent().siblings('.dup-sys-info').toggle();
        }

        //Init: Toogle for system requirment detial links
        $('.dup-sys-title a').each(function() {
            $(this).attr('href', 'javascript:void(0)');
            $(this).click(function() {
                DupliJs.Pack.ToggleSystemDetails(this);
            });
            $(this).prepend("<span class='ui-icon ui-icon-triangle-1-e dup-toggle' />");
        });

        //Init: Color code Pass/Fail/Warn items
        $('.dup-sys-title div').each(function() {
            $(this).addClass(($(this).text() == 'Pass') ? 'dup-sys-pass' : 'dup-sys-fail');
        });

    });
</script>
