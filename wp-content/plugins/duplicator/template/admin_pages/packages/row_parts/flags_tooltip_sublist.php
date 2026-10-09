<?php

use Duplicator\Views\KsesHelper;

defined("ABSPATH") or die("");

/**
 * @var \Duplicator\Core\Views\TplMng $tplMng
 * @var array<int, array{icon_html: string, text: string, url: string}> $rows
 */

$rows = $tplMng->getDataValueArrayRequired('rows');

?>
<ul class="dup-package-flags-tooltip-sublist">
    <?php foreach ($rows as $row) { ?>
        <li>
            <?php echo wp_kses($row['icon_html'], KsesHelper::ICON_TAGS); ?>
            <?php if (!empty($row['url'])) { ?>
                &nbsp;<a class="dup-package-flags-tooltip-link" href="<?php echo esc_url($row['url']); ?>"><?php echo esc_html($row['text']); ?></a>
            <?php } else { ?>
                <?php echo esc_html($row['text']); ?>
            <?php } ?>
        </li>
    <?php } ?>
</ul>
