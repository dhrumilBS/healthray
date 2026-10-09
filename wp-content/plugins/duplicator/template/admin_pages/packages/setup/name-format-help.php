<?php

use Duplicator\Package\NameFormat;
use Duplicator\Views\KsesHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

?>
<p>
    <?php esc_html_e(
        'It is possible to customize the name of the backups using a fixed part and dynamic parts through tags. 
        The available tags are as follows:',
        'duplicator'
    ); ?>

    <ul>
        <?php foreach (NameFormat::getTagsDescriptions() as $tag => $description) : ?>
            <li>
                <strong>%<?php echo esc_html($tag); ?>%</strong> - <?php echo esc_html($description); ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <p>
        <?php
        echo wp_kses(
            __(
                'Important: <b>Backup date and time expressed in UTC</b> (Coordinated Universal Time). 
                The displayed date corresponds to the server\'s international time, independent of local time zones.',
                'duplicator'
            ),
            KsesHelper::GEN_TAGS
        );
        ?>
    </p>

    <p>
        <?php esc_html_e(
            'Here are some examples of name formats:',
            'duplicator'
        ); ?>
    </p>

    <ul>
        <li>
            <strong>%year%%month%%day%_%sitetitle%</strong> - 
            <?php esc_html_e('Backup name with date and site title (it\'s the default)', 'duplicator'); ?>            
        </li>
        <li>
            <strong>%year%%month%%day%_%hour%%minute%%second%_mytext_</strong> - 
            <?php esc_html_e('Backup name with date and time and fixed text', 'duplicator'); ?>
        </li>
    </ul>
</p>