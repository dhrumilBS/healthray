<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

echo esc_html(
    __('When enabled the archive file will be encrypted with a password that is required to open.', 'duplicator') . ' ' .
    __('The installer will also prompt for the password at install time.', 'duplicator') . ' ' .
    __('Encryption is a general deterrent and should not be substituted for properly keeping your files secure.', 'duplicator') . ' ' .
    __('Be sure to remove all installer files when the install process is completed.', 'duplicator')
);
?>
<ul>
    <li>
        <b>
            <?php esc_html_e('No protection:', 'duplicator'); ?>
        </b>
        <?php esc_html_e(
            "No protection system activated!  The installer or archive files can be accessed by any resource that knows the full URL to either file.",
            'duplicator'
        ); ?>
    </li>
    <li>
        <b>
            <?php esc_html_e('Installer password:', 'duplicator'); ?>
        </b>
        <?php esc_html_e(
            'The archive is NOT encrypted. When the installer starts, it will prompt for a password to prevent anyone from running the installer.',
            'duplicator'
        ); ?>       
    </li>
    <li>
    <b>
        <?php esc_html_e('Archive encryption:', 'duplicator'); ?>
        </b>
        <?php
        esc_html_e(
            'The archive IS encrypted with a password, and the installer will ask for a password when started.',
            'duplicator'
        );
        esc_html_e(
            'This option is the recommended maximum level of security.',
            'duplicator'
        );
        ?>  
    </li>
</ul>
