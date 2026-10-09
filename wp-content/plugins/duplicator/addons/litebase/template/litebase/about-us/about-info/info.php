<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$teamImageUrl  = $tplMng->getDataValueString('teamImageUrl');
$wpbeginnerUrl = $tplMng->getDataValueString('wpbeginnerUrl');
$omUrl         = $tplMng->getDataValueString('omUrl');
$miUrl         = $tplMng->getDataValueString('miUrl');

$wpbeginnerLink = '<a href="' . esc_url($wpbeginnerUrl) . '" target="_blank" rel="noopener noreferrer">WPBeginner</a>';
$omLink         = '<a href="' . esc_url($omUrl) . '" target="_blank" rel="noopener noreferrer">OptinMonster</a>';
$miLink         = '<a href="' . esc_url($miUrl) . '" target="_blank" rel="noopener noreferrer">MonsterInsights</a>';
?>
<div class="dupli-litebase-about-section dupli-litebase-about-columns">
    <div class="dupli-litebase-about-column-60">
        <h3>
            <?php
            esc_html_e(
                'Hello and welcome to Duplicator, the most reliable WordPress backup and migration plugin.
                At Duplicator, we build software that helps protect your website with our reliable secure
                backups and migrate your website without any manual effort.',
                'duplicator'
            );
            ?>
        </h3>
        <p>
            <?php
            esc_html_e(
                'Over the years, we found that most WordPress backup and migration plugins were unreliable,
                buggy, slow, and very hard to use. So we started with a simple goal: build a WordPress backup
                and migration plugin that\'s both easy and powerful.',
                'duplicator'
            );
            ?>
        </p>
        <p>
            <?php esc_html_e('Our goal is to take the pain out of creating backups and migrations, and make it easy.', 'duplicator'); ?>
        </p>
        <p>
            <?php
            echo wp_kses_post(sprintf(
                /* translators: %1$s - WPBeginner link, %2$s - OptinMonster link, %3$s - MonsterInsights link. */
                __(
                    'Duplicator is brought to you by the same team that\'s behind the largest WordPress
                    resource site, %1$s, the most popular lead-generation software, %2$s, the best
                    WordPress analytics plugin, %3$s, and more!',
                    'duplicator'
                ),
                $wpbeginnerLink,
                $omLink,
                $miLink
            ));
            ?>
        </p>
        <p>
            <?php esc_html_e('Yup, we know a thing or two about building awesome products that customers love.', 'duplicator'); ?>
        </p>
    </div>
    <div class="dupli-litebase-about-column-40 dupli-litebase-about-column-last">
        <figure>
            <img src="<?php echo esc_url($teamImageUrl); ?>"
                 alt="<?php esc_attr_e('The Awesome Motive Team photo', 'duplicator'); ?>">
            <figcaption>
                <?php esc_html_e('The Awesome Motive Team', 'duplicator'); ?>
            </figcaption>
        </figure>
    </div>
</div>
