<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
?>
<template id="dupli-autotune-attempt-template">
    <div class="dupli-autotune-attempt">
        <span class="dupli-autotune-marker"><i class="fa-solid" aria-hidden="true"></i></span>
        <div class="dupli-autotune-attempt-body">
            <div class="dupli-autotune-attempt-heading">
                <strong class="dupli-autotune-attempt-title"></strong>
                <span class="dupli-autotune-badge"><b></b><span></span></span>
                <span class="dupli-autotune-attempt-time"></span>
            </div>
            <progress class="dupli-autotune-progress" max="100" hidden></progress>
            <details class="dupli-autotune-config">
                <summary title="<?php esc_attr_e('Full settings', 'duplicator'); ?>">
                    <span class="dupli-autotune-config-summary"></span>
                    <i class="fa-solid fa-chevron-down dupli-autotune-chevron" aria-hidden="true"></i>
                </summary>
                <table class="dupli-autotune-table"><tbody></tbody></table>
                <p class="dupli-autotune-context">
                    <span class="dupli-autotune-context-text"></span>
                    <a class="dupli-autotune-package-link link-style"></a>
                </p>
            </details>
        </div>
    </div>
</template>

<template id="dupli-autotune-delta-template">
    <span class="dupli-autotune-delta"><span></span> <b></b></span>
</template>

<template id="dupli-autotune-setting-row-template">
    <tr><td></td><td><span class="dupli-autotune-setting-value"></span><span class="dupli-autotune-was"></span></td></tr>
</template>

<template id="dupli-autotune-result-row-template">
    <tr><td></td><td></td><td></td></tr>
</template>
