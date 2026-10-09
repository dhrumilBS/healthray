<?php

/**
 * Duplicator messages sections
 */

use Duplicator\Models\Storages\UnknownStorage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var UnknownStorage $storage
 */
$storage = $tplMng->getDataValueObjRequired('storage', UnknownStorage::class);

$tplMng->render('admin_pages/storages/parts/provider_head');
?>
<tr valign="top">
    <th scope="row">
        <label>
            <?php esc_html_e("Unknown Storage Type", 'duplicator'); ?>
        </label>
    </th>
    <td>
    </td>
</tr>
<?php $tplMng->render('admin_pages/storages/parts/provider_foot');

