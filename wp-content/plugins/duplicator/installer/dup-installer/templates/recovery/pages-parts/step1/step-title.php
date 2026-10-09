<?php

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

dupxTplRender('pages-parts/head/header-main', [
    'htmlTitle'       => 'Step <span class="step">1</span> of 2: ' .
        'Restore Backup <div class="sub-header">Launch the installer to restore the site.</div>',
    'showSwitchView'  => false,
    'showHeaderLinks' => false,
]);
