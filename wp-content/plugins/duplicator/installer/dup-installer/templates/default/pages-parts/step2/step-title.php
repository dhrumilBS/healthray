<?php

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$htmlTitle = 'Step <span class="step">2</span> of 4: Install Database <div class="sub-header">This step will install the database from the archive.</div>';

dupxTplRender(
    'pages-parts/head/header-main',
    ['htmlTitle' => $htmlTitle]
);
