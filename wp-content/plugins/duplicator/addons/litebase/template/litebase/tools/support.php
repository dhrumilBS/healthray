<?php

defined('ABSPATH') || exit;

/**
 * @var Duplicator\Core\Views\TplMng $tplMng
 *
 * @var array<int, array{label: string, url: string}> $kbItems
 * @var string                                        $upgradeUrl
 * @var string                                        $forumUrl
 */

$kbItems    = $tplMng->getDataValueArray('kbItems');
$upgradeUrl = $tplMng->getDataValueStringRequired('upgradeUrl');
$forumUrl   = $tplMng->getDataValueStringRequired('forumUrl');
?>
<div class="dupli-litebase-support">
    <p class="dupli-litebase-support-intro">
        <?php esc_html_e(
            'Migrating WordPress is a complex process and the logic to make all the magic happen smoothly may not work quickly
with every site. With over 30,000 plugins and a very complex server eco-system some migrations may run into issues.
This is why Duplicator includes a detailed knowledgebase that can help with many common issues.
Resources to additional support, approved hosting, and alternatives to fit your needs can be found below.',
            'duplicator'
        ); ?>
    </p>

    <div class="dupli-litebase-support-boxes">
        <div class="dup-box dupli-litebase-support-box">
            <div class="dup-box-title">
                <i class="fas fa-book" aria-hidden="true"></i>
                <?php esc_html_e('Knowledgebase', 'duplicator'); ?>
            </div>
            <div class="dup-box-panel">
                <p><?php esc_html_e('Complete online documentation', 'duplicator'); ?></p>
                <select id="dupli-litebase-support-kb-links" class="dupli-litebase-support-kb-select">
                    <option disabled selected value="">
                        <?php esc_html_e('Choose a section', 'duplicator'); ?>
                    </option>
                    <?php foreach ($kbItems as $item) : ?>
                        <option value="<?php echo esc_url($item['url']); ?>">
                            <?php echo esc_html($item['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="dup-box dupli-litebase-support-box">
            <div class="dup-box-title">
                <i class="fas fa-life-ring" aria-hidden="true"></i>
                <?php esc_html_e('Premium Support', 'duplicator'); ?>
            </div>
            <div class="dup-box-panel">
                <p>
                    <?php esc_html_e(
                        'Having a problem with your backups or migrations? Upgrade to get our Premium Support.',
                        'duplicator'
                    ); ?>
                </p>
                <p>
                    <a
                        href="<?php echo esc_url($upgradeUrl); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="button primary margin-bottom-0"
                    >
                        <?php esc_html_e('Upgrade Now', 'duplicator'); ?>
                    </a>
                </p>
                <p class="dupli-litebase-support-forum">
                    <?php
                    printf(
                        esc_html_x(
                            'Free users: %1$sSupport Forum%2$s',
                            '1 and 2 are opening and closing anchor tags',
                            'duplicator'
                        ),
                        '<a href="' . esc_url($forumUrl) . '" target="_blank" rel="noopener noreferrer">',
                        '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(function ($) {
        $('#dupli-litebase-support-kb-links').on('change', function () {
            var url = $(this).val();
            if (url) {
                window.open(url, '_blank', 'noopener');
            }
        });
    });
</script>
