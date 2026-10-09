<?php

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Addons\LiteBase\EducationStrings;

$features = EducationStrings::getDidYouKnowList();
$feature  = $features[array_rand($features)];
$url      = EducationStrings::HEADER_UPGRADE_URL;
?>
<style>
    .dupli-litebase-installer-did-you-know {
        background-color: #fff;
        border: 1px solid #dadada;
        border-left: 4px solid #1da867;
        padding: 12px 16px;
        margin: 10px auto;
        max-width: 100%;
        box-sizing: border-box;
        font-family: Verdana, Arial, sans-serif;
        font-size: 13px;
        color: #555;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .dupli-litebase-installer-did-you-know .icon {
        color: #1da867;
        font-size: 18px;
        flex-shrink: 0;
    }
    .dupli-litebase-installer-did-you-know .text {
        flex: 1;
    }
    .dupli-litebase-installer-did-you-know strong {
        color: #222;
    }
    .dupli-litebase-installer-did-you-know .upgrade-btn,
    .dupli-litebase-installer-did-you-know .upgrade-btn:visited {
        display: inline-block;
        background: transparent;
        border: 1px solid #1da867;
        border-radius: 3px;
        color: #1da867;
        font-weight: 700;
        text-decoration: none;
        padding: 6px 14px;
        line-height: 1;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .dupli-litebase-installer-did-you-know .upgrade-btn:hover,
    .dupli-litebase-installer-did-you-know .upgrade-btn:focus {
        background: transparent;
        border-color: #178a52;
        color: #178a52;
    }
</style>
<div class="dupli-litebase-installer-did-you-know">
    <i class="fa fa-lightbulb icon" aria-hidden="true"></i>
    <span class="text">
        <strong>Did you know Duplicator Pro has:</strong>
        <?php echo DUPX_U::esc_html($feature); ?>
    </span>
    <a class="upgrade-btn" href="<?php echo DUPX_U::esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
        Upgrade to Pro
    </a>
</div>
