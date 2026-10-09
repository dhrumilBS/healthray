<?php

defined("ABSPATH") or die("");

/**
 * Settings > Packages, shell zip message
 *
 * Variables
 *
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
$hasShellZip = $tplMng->getDataValueBool('hasShellZip');

if ($hasShellZip) {
    esc_html_e('The "Shell Zip" mode allows Duplicator to use the server\'s internal zip command.', 'duplicator');
    ?>
    <br />
    <?php
    esc_html_e('When available this mode is recommended over the PHP "ZipArchive" mode.', 'duplicator');
} else {
    $multiScanPath = $tplMng->getDataValueBool('multiScanPath');
    if ($multiScanPath) {
        ?>

        <i style='color:maroon'>
            <i class='fa fa-exclamation-triangle'></i>
            <?php esc_html_e("This server is not configured for the Shell Zip engine - please use a different engine mode.", 'duplicator'); ?>
        </i>
    <?php } else { ?>
        <i style='color:maroon'>
            <i class='fa fa-exclamation-triangle'></i>
            <?php esc_html_e("This server is not configured for the Shell Zip engine - please use a different engine mode.", 'duplicator'); ?>
            <br />
            <?php
            printf(
                esc_html_x(
                    'Shell Zip is %1$srecommended%2$s when available. ',
                    '%1$s and %2$s are html anchor tags or link',
                    'duplicator'
                ),
                '<a href="' . esc_url(DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-work-with-the-different-zip-engines') . '" target="_blank">',
                '</a> '
            );
            printf(
                esc_html_x(
                    'For a list of supported hosting providers %1$sclick here%2$s.',
                    '%1$s and %2$s are html anchor tags or link',
                    'duplicator'
                ),
                '<a href="' . esc_url(DUPLICATOR_BLOG_URL . 'best-wordpress-hosting/') . '" target="_blank">',
                '</a> '
            );
            ?>
        </i>
        <?php
        $problems  = $tplMng->getDataValueArray('problems');
        $isWindows = $tplMng->getDataValueBool('isWindows');

        if (count($problems) > 0 && !$isWindows) {
            $shell_tooltip  = ' ';
            $shell_tooltip .= __("To make 'Shell Zip' available, ask your host to:", 'duplicator');
            echo '<br/>';
            $i = 1;
            foreach ($problems as $problem) {
                $shell_tooltip .= "{$i}. {$problem['fix']}<br/>";
                $i++;
            }
            $shell_tooltip .= '<br/>';
            echo wp_kses($shell_tooltip, ['br' => []]);
        }
    }
}
?>
