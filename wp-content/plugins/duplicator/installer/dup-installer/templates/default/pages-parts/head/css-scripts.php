<?php

use Duplicator\Installer\ViewHelpers\Resources;

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$versionDup   = DUPX_ArchiveConfig::getInstance()->version_dup;
$baseUrl      = Resources::getAssetsBaseUrl();
$vendorSuffix = DUPX_Constants::isDebugMode() ? '' : '.min';

$cssList =  [
    'assets/normalize.css',
    'assets/font-awesome/css/all.min.css',
    'assets/fonts/dots/dots-font.css',
    'assets/dupli-tippy.css',
    'assets/build/css/installer-vendor' . $vendorSuffix . '.css',
];

$jsList = [
    'assets/build/js/installer-vendor' . $vendorSuffix . '.js',
];

// CSS
foreach ($cssList as $css) {
    ?>
    <link rel="stylesheet" href="<?php echo $baseUrl . '/' . $css . '?ver=' . $versionDup; ?>" type="text/css" media="all">
    <?php
}
require(DUPX_INIT . '/assets/inc.css.php');

// JAVASCRIPT
foreach ($jsList as $js) {
    ?>
    <script src="<?php echo $baseUrl . '/' . $js . '?ver=' . $versionDup; ?>"></script>
    <?php
}
require(DUPX_INIT . '/assets/inc.js.php');
dupxTplRender('scripts/dupx-functions');
