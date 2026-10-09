<?php

use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Views\TplMng;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */
?>
<div class="dup-mock-blur" aria-hidden="true">
    <div class="dup-capabilities-selector-wrapper">
        <h3 class="title">Roles and Permissions</h3>
        <p>
            Select the user roles and/or users that are allowed to manage different aspects of Duplicator.<br>
            By default, all permissions are provided only to administrator users. <br>
            Some capabilities depend on others so if you select for example storage capability automatically the Backup
            read and Backup edit capabilities are assigned.<br>
            <b>It is not possible to self remove the manage settings capabilities.</b>
        </p>
        <hr size="1">
        <div class="dup-settings-wrapper margin-bottom-1">
            <label class="lbl-larger">
                Backup Read&nbsp;
                <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
            </label>
            <div class="margin-bottom-1">
                <span class="select2 select2-container select2-container--default" dir="ltr" style="width: 500px;">
                    <span class="selection">
                        <span class="select2-selection select2-selection--multiple">
                            <ul class="select2-selection__rendered">
                                <li class="select2-selection__choice">
                                    <span class="select2-selection__choice__remove" role="presentation">×</span>
                                    Administrator
                                </li>
                                <li class="select2-search select2-search--inline">
                                    <input
                                        class="select2-search__field"
                                        type="search"
                                        tabindex="-1"
                                        autocomplete="off"
                                        readonly
                                        style="width: 0.75em;"
                                    >
                                </li>
                            </ul>
                        </span>
                    </span>
                </span>
            </div>

            <label class="lbl-larger">
                -&nbsp;&nbsp;Backup Create&nbsp;
                <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
            </label>
            <div class="margin-bottom-1">
                <span class="select2 select2-container select2-container--default" dir="ltr" style="width: 500px;">
                    <span class="selection">
                        <span class="select2-selection select2-selection--multiple">
                            <ul class="select2-selection__rendered">
                                <li class="select2-selection__choice">
                                    <span class="select2-selection__choice__remove" role="presentation">×</span>
                                    Administrator
                                </li>
                                <li class="select2-search select2-search--inline">
                                    <input
                                        class="select2-search__field"
                                        type="search"
                                        tabindex="-1"
                                        autocomplete="off"
                                        readonly
                                        style="width: 0.75em;"
                                    >
                                </li>
                            </ul>
                        </span>
                    </span>
                </span>
            </div>

            <label class="lbl-larger">
                -&nbsp;&nbsp;-&nbsp;&nbsp;Manage Storage&nbsp;
                <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
            </label>
            <div class="margin-bottom-1">
                <span class="select2 select2-container select2-container--default" dir="ltr" style="width: 500px;">
                    <span class="selection">
                        <span class="select2-selection select2-selection--multiple">
                            <ul class="select2-selection__rendered">
                                <li class="select2-selection__choice">
                                    <span class="select2-selection__choice__remove" role="presentation">×</span>
                                    Administrator
                                </li>
                                <li class="select2-search select2-search--inline">
                                    <input
                                        class="select2-search__field"
                                        type="search"
                                        tabindex="-1"
                                        autocomplete="off"
                                        readonly
                                        style="width: 0.75em;"
                                    >
                                </li>
                            </ul>
                        </span>
                    </span>
                </span>
            </div>

            <label class="lbl-larger">
                -&nbsp;&nbsp;-&nbsp;&nbsp;Manage Schedules&nbsp;
                <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
            </label>
            <div class="margin-bottom-1">
                <span class="select2 select2-container select2-container--default" dir="ltr" style="width: 500px;">
                    <span class="selection">
                        <span class="select2-selection select2-selection--multiple">
                            <ul class="select2-selection__rendered">
                                <li class="select2-selection__choice">
                                    <span class="select2-selection__choice__remove" role="presentation">×</span>
                                    Administrator
                                </li>
                                <li class="select2-search select2-search--inline">
                                    <input
                                        class="select2-search__field"
                                        type="search"
                                        tabindex="-1"
                                        autocomplete="off"
                                        readonly
                                        style="width: 0.75em;"
                                    >
                                </li>
                            </ul>
                        </span>
                    </span>
                </span>
            </div>

            <label class="lbl-larger">
                -&nbsp;&nbsp;Restore Backup&nbsp;
                <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
            </label>
            <div class="margin-bottom-1">
                <span class="select2 select2-container select2-container--default" dir="ltr" style="width: 500px;">
                    <span class="selection">
                        <span class="select2-selection select2-selection--multiple">
                            <ul class="select2-selection__rendered">
                                <li class="select2-selection__choice">
                                    <span class="select2-selection__choice__remove" role="presentation">×</span>
                                    Administrator
                                </li>
                                <li class="select2-search select2-search--inline">
                                    <input
                                        class="select2-search__field"
                                        type="search"
                                        tabindex="-1"
                                        autocomplete="off"
                                        readonly
                                        style="width: 0.75em;"
                                    >
                                </li>
                            </ul>
                        </span>
                    </span>
                </span>
            </div>

            <label class="lbl-larger">
                -&nbsp;&nbsp;-&nbsp;&nbsp;Backup Import&nbsp;
                <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
            </label>
            <div class="margin-bottom-1">
                <span class="select2 select2-container select2-container--default" dir="ltr" style="width: 500px;">
                    <span class="selection">
                        <span class="select2-selection select2-selection--multiple">
                            <ul class="select2-selection__rendered">
                                <li class="select2-selection__choice">
                                    <span class="select2-selection__choice__remove" role="presentation">×</span>
                                    Administrator
                                </li>
                                <li class="select2-search select2-search--inline">
                                    <input
                                        class="select2-search__field"
                                        type="search"
                                        tabindex="-1"
                                        autocomplete="off"
                                        readonly
                                        style="width: 0.75em;"
                                    >
                                </li>
                            </ul>
                        </span>
                    </span>
                </span>
            </div>

            <label class="lbl-larger">
                -&nbsp;&nbsp;Backup Export&nbsp;
                <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
            </label>
            <div class="margin-bottom-1">
                <span class="select2 select2-container select2-container--default" dir="ltr" style="width: 500px;">
                    <span class="selection">
                        <span class="select2-selection select2-selection--multiple">
                            <ul class="select2-selection__rendered">
                                <li class="select2-selection__choice">
                                    <span class="select2-selection__choice__remove" role="presentation">×</span>
                                    Administrator
                                </li>
                                <li class="select2-search select2-search--inline">
                                    <input
                                        class="select2-search__field"
                                        type="search"
                                        tabindex="-1"
                                        autocomplete="off"
                                        readonly
                                        style="width: 0.75em;"
                                    >
                                </li>
                            </ul>
                        </span>
                    </span>
                </span>
            </div>

            <label class="lbl-larger">
                -&nbsp;&nbsp;Manage Settings&nbsp;
                <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
            </label>
            <div class="margin-bottom-1">
                <span class="select2 select2-container select2-container--default" dir="ltr" style="width: 500px;">
                    <span class="selection">
                        <span class="select2-selection select2-selection--multiple">
                            <ul class="select2-selection__rendered">
                                <li class="select2-selection__choice">
                                    <span class="select2-selection__choice__remove" role="presentation">×</span>
                                    Administrator
                                </li>
                                <li class="select2-search select2-search--inline">
                                    <input
                                        class="select2-search__field"
                                        type="search"
                                        tabindex="-1"
                                        autocomplete="off"
                                        readonly
                                        style="width: 0.75em;"
                                    >
                                </li>
                            </ul>
                        </span>
                    </span>
                </span>
            </div>

            <label class="lbl-larger">
                -&nbsp;&nbsp;-&nbsp;&nbsp;License Settings&nbsp;
                <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
            </label>
            <div class="margin-bottom-1">
                <span class="select2 select2-container select2-container--default" dir="ltr" style="width: 500px;">
                    <span class="selection">
                        <span class="select2-selection select2-selection--multiple">
                            <ul class="select2-selection__rendered">
                                <li class="select2-selection__choice">
                                    <span class="select2-selection__choice__remove" role="presentation">×</span>
                                    Administrator
                                </li>
                                <li class="select2-search select2-search--inline">
                                    <input
                                        class="select2-search__field"
                                        type="search"
                                        tabindex="-1"
                                        autocomplete="off"
                                        readonly
                                        style="width: 0.75em;"
                                    >
                                </li>
                            </ul>
                        </span>
                    </span>
                </span>
            </div>

            <label class="lbl-larger">
                -&nbsp;&nbsp;Staging Sites&nbsp;
                <i class="fa-solid fa-question-circle fa-sm dark-gray-color" aria-hidden="true"></i>
            </label>
            <div class="margin-bottom-1">
                <span class="select2 select2-container select2-container--default" dir="ltr" style="width: 500px;">
                    <span class="selection">
                        <span class="select2-selection select2-selection--multiple">
                            <ul class="select2-selection__rendered">
                                <li class="select2-selection__choice">
                                    <span class="select2-selection__choice__remove" role="presentation">×</span>
                                    Administrator
                                </li>
                                <li class="select2-search select2-search--inline">
                                    <input
                                        class="select2-search__field"
                                        type="search"
                                        tabindex="-1"
                                        autocomplete="off"
                                        readonly
                                        style="width: 0.75em;"
                                    >
                                </li>
                            </ul>
                        </span>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <hr>
    <p>
        <span class="button primary small">Update Capabilities</span>
        &nbsp;
        <span class="button secondary hollow small">Reset to Default</span>
    </p>
</div>
<?php
TplMng::getInstance()->render('litebase/mocks/static-popup', [
    'title'       => __('Advanced Backup Permissions', 'duplicator'),
    'warningText' => __('Advanced Backup Permissions are not available in Duplicator Lite!', 'duplicator'),
    'paragraphs'  => [
        __(
            'Elevate your backup capabilities with advanced permissions, allowing for precise control over the creation, 
            exportation, restoration, and management of control settings. Enjoy granular access control to ensure only 
            authorized users can perform these critical functions.',
            'duplicator'
        ),
    ],
    'upgradeUrl'  => LiteBaseLinks::getUpgradeUrl('blurred-mocks', 'Settings Access Tab'),
]);
