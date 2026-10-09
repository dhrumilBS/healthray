<?php

/**
 * Duplicator messages sections
 */

use Duplicator\Models\Storages\AbstractStorageEntity;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var AbstractStorageEntity $storage
 */
$storage = $tplMng->getDataValueObjRequired('storage', AbstractStorageEntity::class);

?>
</table>