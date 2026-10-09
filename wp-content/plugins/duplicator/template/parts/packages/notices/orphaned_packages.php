<?php

/**
 * Duplicator Backup row in table Backups list
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$count = $tplMng->getDataValueIntRequired('count');
$size  = $tplMng->getDataValueStringRequired('size');
$url   = $tplMng->getDataValueStringRequired('url');
?>
<?php echo esc_html(
    sprintf(
        _x(
            'There are currently (%1$s) orphaned Backup files taking up %2$s of space.
            These Backup files are no longer visible in the backups list below and are safe to remove.',
            '%1$s is the number of orphaned packages, %2$s is the total size of orphaned packages',
            'duplicator'
        ),
        $count,
        $size
    )
); ?>
<br>
<?php esc_html_e(
    'Go to: Tools > General > Information > Stored Data > look for the [Delete Backups Orphans] button for more details.',
    'duplicator'
); ?>
<br>
<a href="<?php echo esc_url($url); ?>">
    <?php esc_html_e('Take me there now!', 'duplicator'); ?>
</a>
