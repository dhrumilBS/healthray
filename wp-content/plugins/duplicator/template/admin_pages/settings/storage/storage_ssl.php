<?php



defined("ABSPATH") or die("");

use Duplicator\Models\GlobalEntity;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$global = GlobalEntity::getInstance();
?>
<h3 class="title">
    <?php esc_html_e("SSL", 'duplicator'); ?>
</h3>
<hr>
<p class="description">
    <?php esc_html_e("Do not modify SSL settings unless you know the expected result or have talked to support.", 'duplicator'); ?>
</p>

<label class="lbl-larger">
    <?php esc_html_e("SSL certificates", 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <input
        type="checkbox"
        name="ssl_useservercerts"
        id="ssl_useservercerts"
        value="1"
        class="margin-0"
        <?php checked($global->isUsingServerCerts()); ?>>
    <label for="ssl_useservercerts">
        <?php esc_html_e("Use server's SSL certificates", 'duplicator'); ?>
    </label>
    <p class="description">
        <?php
        printf(
            /* translators: %s: plugin name */
            esc_html__(
                "To use server's SSL certificates please enable it.
            By default %s uses its own store of SSL certificates to verify the identity of remote storage sites.",
                'duplicator'
            ),
            esc_html(DUPLICATOR____NAME)
        );
        ?>
    </p>

    <input
        type="checkbox"
        name="ssl_disableverify"
        id="ssl_disableverify"
        value="1"
        class="margin-0"
        <?php checked(!$global->isSslVerifyEnabled()); ?>>
    <label for="ssl_disableverify">
        <?php esc_html_e("Disable verification of SSL certificates", 'duplicator'); ?>
    </label>
    <p class="description">
        <?php
        esc_html_e("To disable verification of a host and the peer's SSL certificate.", 'duplicator');
        ?>
    </p>
</div>

<label class="lbl-larger">
    <?php esc_html_e("Use IPv4 only", 'duplicator'); ?>
</label>
<div class="margin-bottom-1">
    <input
        type="checkbox"
        name="ipv4_only"
        id="ipv4_only"
        value="1"
        class="margin-0"
        <?php checked($global->isIpv4Only()); ?>>
    <label for="ipv4_only">
        <?php esc_html_e("Use IPv4 only", 'duplicator'); ?>
    </label>
    <p class="description">
        <?php
        esc_html_e(
            "To use IPv4 only, which can help if your host has a broken IPv6 setup (currently only supported by Google Drive)",
            'duplicator'
        );
        ?>
    </p>
</div>