<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$filteredCoreDirs  = $tplMng->getDataValueArray('filteredCoreDirs');
$filteredCoreFiles = $tplMng->getDataValueArray('filteredCoreFiles');
?>
<div class="scan-item">
    <div class='title' onclick="DupliJs.Pack.toggleScanItem(this);">
        <div class="text"><i class="fa fa-caret-right"></i> <?php esc_html_e('WordPress', 'duplicator'); ?></div>
        <div id="data-srv-wp-all"></div>
    </div>
    <div class="info">
        <span id="data-srv-wp-version"></span>&nbsp;
        <b><?php esc_html_e('WordPress Version', 'duplicator'); ?>:</b>&nbsp;<?php echo esc_html(get_bloginfo('version')); ?> <br />
        <hr size="1" /><span id="data-srv-wp-core"></span>&nbsp;<b> <?php esc_html_e('Core Files', 'duplicator'); ?></b> <br />
        <?php if (count($filteredCoreDirs) > 0) : ?>
            <div id="data-srv-wp-core-missing-dirs">
                <?php echo wp_kses(
                    __(
                        "The core WordPress directories below will <u>not</u> be included in the archive.
                        These paths are required for WordPress to function!",
                        'duplicator'
                    ),
                    ['u' => []]
                ); ?>
                <br />
                <?php foreach ($filteredCoreDirs as $coreDir) : ?>
                    <b class="margin-left-1"><i class="fa fa-exclamation-circle scan-warn margin-right-1"></i><?php echo esc_html($coreDir); ?></b><br />
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (count($filteredCoreFiles) > 0) : ?>
            <div id="data-srv-wp-core-missing-dirs">
                <?php echo wp_kses(
                    __(
                        "The core WordPress files below will <u>not</u> be included in the archive.
                        These files are required for WordPress to function!",
                        'duplicator'
                    ),
                    ['u' => []]
                ); ?>
                <br />
                <?php foreach ($filteredCoreFiles as $coreFile) : ?>
                    <b class="margin-left-1"><i class="fa fa-exclamation-circle scan-warn margin-right-1"></i><?php echo esc_html($coreFile); ?></b>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (count($filteredCoreDirs) > 0 || count($filteredCoreFiles) > 0) : ?>
            <div class="scan-system-subnote">
                <?php esc_html_e(
                    'Note: Please change the file and directory filters if you wish to include the WordPress core files 
                otherwise the data will have to be manually copied to the new location for the site to function properly.',
                    'duplicator'
                ); ?>
            </div>
        <?php endif; ?>
        <?php if (empty($filteredCoreDirs) && empty($filteredCoreFiles)) : ?>
            <div class="scan-system-subnote">
                <?php esc_html_e(
                    "If the scanner is unable to locate the wp-config.php file in the root directory, 
                    then you will need to manually copy it to its new location. 
                    This check will also look for core WordPress paths that should be included in the archive for WordPress to work correctly.",
                    'duplicator'
                ); ?>
            </div>
        <?php endif; ?>
        <?php if (!is_multisite()) { ?>
            <hr size="1" />
            <span>
                <div class="dup-scan-good"><i class="fa fa-check"></i></div>
            </span>
            <b> <?php esc_html_e('Multisite: N/A', 'duplicator'); ?></b> <br />
            <div class="scan-system-subnote">
                <?php esc_html_e('Multisite was not detected on this site. It is currently configured as a standard WordPress site.', 'duplicator'); ?>
                <i>
                    <a href='https://developer.wordpress.org/advanced-administration/multisite/create-network/' target='_blank'>
                        [<?php esc_html_e('details', 'duplicator'); ?>]
                    </a>
                </i>
            </div>
        <?php } else {
            do_action('duplicator_scan_setup_wordpress_multisite');
        } ?>
    </div>
</div>
