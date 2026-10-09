<?php

defined('ABSPATH') || exit;

use Duplicator\Core\Views\TplMng;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng                   $tplMng
 */

$packageNonceUrl = $tplMng->getDataValueString('packageNonceUrl');
$imgBase         = $tplMng->getDataValueString('imgBase');
$seeAllUrl       = $tplMng->getDataValueString('seeAllUrl');
$upgradeNowUrl   = $tplMng->getDataValueString('upgradeNowUrl');
$upgradeFooter   = $tplMng->getDataValueString('upgradeFooter');
/** @var array<int, array{img:string,title:string,desc:string}> $features */
$features = $tplMng->getDataValueArray('features');
/** @var string[] $proFeatures */
$proFeatures = $tplMng->getDataValueArray('proFeatures');
/** @var array<int, array{img:string,quote:string,author:string,role:string}> $testimonials */
$testimonials = $tplMng->getDataValueArray('testimonials');
?>
<div class="wrap dup-styles"> 
    <div id="dupli-litebase-welcome">
        <div class="dupli-litebase-welcome-container">
            <?php
            TplMng::getInstance()->render('litebase/welcome/intro', [
                'packageNonceUrl' => $packageNonceUrl,
                'imgBase'         => $imgBase,
            ]);
            TplMng::getInstance()->render('litebase/welcome/features', [
                'features'  => $features,
                'seeAllUrl' => $seeAllUrl,
            ]);
            TplMng::getInstance()->render('litebase/welcome/upgrade-box', [
                'proFeatures'   => $proFeatures,
                'upgradeNowUrl' => $upgradeNowUrl,
            ]);
            TplMng::getInstance()->render('litebase/welcome/testimonials', ['testimonials' => $testimonials]);
            TplMng::getInstance()->render('litebase/welcome/footer', [
                'packageNonceUrl' => $packageNonceUrl,
                'upgradeFooter'   => $upgradeFooter,
            ]);
            ?>
        </div>
    </div>
</div>
