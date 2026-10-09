<?php
/**
 * Admin Notifications content.
 *
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

defined('ABSPATH') || exit;

$id       = $tplMng->getDataValueStringRequired('id');
$title    = $tplMng->getDataValueStringRequired('title');
$content  = $tplMng->getDataValueStringRequired('content');
$btns     = $tplMng->getDataValueArray('btns');
$videoUrl = $tplMng->getDataValueString('video_url');
?>
<div class="dup-notifications-message" data-message-id="<?php echo esc_attr($id); ?>;">
    <h3 class="dup-notifications-title">
        <?php echo esc_html($title); ?>
        <?php if ($videoUrl !== '') : ?>
            <a
                class="dup-notifications-badge"
                href="<?php echo esc_url($videoUrl); ?>"
                target="_blank"
                rel="noopener noreferrer">
                <i class="fa fa-play" aria-hidden="true"></i> <?php esc_html_e('Watch video', 'duplicator'); ?>
            </a>
        <?php endif; ?>
    </h3>
    <div class="dup-notifications-content">
        <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </div>
    <?php foreach ($btns as $btn) : ?>
        <a 
            href="<?php echo esc_attr($btn['url']); ?>" 
            class="button small <?php echo esc_attr($btn['class']); ?>" 
            <?php echo $btn['target'] === '_blank' ? 'target="_blank"' : ''; ?>>
            <?php echo esc_html($btn['text']); ?>
        </a>
    <?php endforeach; ?>
</div>
