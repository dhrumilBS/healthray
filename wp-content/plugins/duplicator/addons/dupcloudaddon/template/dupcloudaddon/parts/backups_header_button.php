<?php

/**
 * Template for Duplicator Cloud Connect Step 1
 */

use Duplicator\Addons\DupCloudAddon\Models\DupCloudStorage;
use Duplicator\Addons\DupCloudAddon\Utils\DupCloudClient;
use Duplicator\Core\CapMng;
use Duplicator\Models\Storages\StoragesUtil;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
if (!CapMng::can(CapMng::CAP_STORAGE, false)) {
    return;
}

$storage      = StoragesUtil::getUniqueStorage(DupCloudStorage::class);
$dashboardUrl = $storage->isAuthorized()
    ? $storage->getBackupsUrl()
    : DupCloudClient::manageWebsitesUrl();
?>
<span>
    <a href="<?php echo esc_url($dashboardUrl); ?>" target="_blank"
        id="dup-dupcloud-manage-website"
        class="button secondary hollow tiny margin-bottom-0"
    >
        <i class="fa-solid fa-cloud"></i>&nbsp;
        <?php esc_html_e('Cloud Dashboard', 'duplicator'); ?>
    </a>
</span>