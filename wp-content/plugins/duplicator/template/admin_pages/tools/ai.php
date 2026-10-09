<?php

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$abilitiesAvailable = $tplMng->getDataValueBool('abilitiesAvailable');

// Render-time surface: a loaded addon echoes its own escaped markup here.
ob_start();
do_action('duplicator_tools_ai_cta');
$ctaHtml = trim((string) ob_get_clean());

$abilities = [
    __('List recent backups with their name, date, status and size.', 'duplicator'),
    __('Create a new backup using your default Backup template.', 'duplicator'),
    __('Check the progress of a backup until it completes.', 'duplicator'),
];
?>
<div class="dupli-tool-ai">
    <h1 class="dupli-tool-ai-title">
        <?php esc_html_e('Run Backups With Your AI Assistant', 'duplicator'); ?>
    </h1>

    <?php
    if ($ctaHtml !== '') {
        ?>
        <div class="dupli-tool-ai-card">
            <?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render-time action output; the hooked addon escapes it
            echo $ctaHtml;
            ?>
        </div>
        <?php
    } else {
        ?>
        <p>
            <?php
            printf(
                /* translators: %s: WPVibe link */
                esc_html__('Connect an assistant to this site with %s, then ask it to back the site up.', 'duplicator'),
                '<a href="https://wpvibe.ai/start/" target="_blank" rel="noopener noreferrer">' .
                esc_html__('WPVibe', 'duplicator') . '</a>'
            );
            ?>
            <br>
            <small><?php esc_html_e('WPVibe is a third-party product, not part of Duplicator.', 'duplicator'); ?></small>
        </p>
        <?php
    }
    ?>

    <p class="dupli-tool-ai-headline">
        <strong><?php esc_html_e('What An Assistant Can Do', 'duplicator'); ?></strong>
    </p>

    <ul class="dupli-tool-ai-abilities">
        <?php foreach ($abilities as $ability) : ?>
            <li>
                <i class="fa fa-caret-right"></i>
                <?php echo esc_html($ability); ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="dupli-tool-ai-notes">
        <?php if (!$abilitiesAvailable) : ?>
            <p class="dupli-tool-ai-unavailable">
                <i class="fa fa-triangle-exclamation"></i>
                <?php
                esc_html_e(
                    'These features are not active on this site. They need WordPress 6.9 or later,
                    the release that introduced the Abilities API.',
                    'duplicator'
                );
                ?>
            </p>
        <?php else : ?>
            <p>
                <?php
                esc_html_e(
                    'Requires WordPress 6.9 or later. Creating a backup requires the Backup creation capability;
                    listing backups and checking status require basic Duplicator access.',
                    'duplicator'
                );
                ?>
            </p>
        <?php endif; ?>
        <p>
            <?php
            esc_html_e(
                'If this site builds backups in the browser rather than on the server, a backup started by an
                assistant will not progress until someone loads a page on the site. Any page will do.',
                'duplicator'
            );
            ?>
        </p>
    </div>
</div>
