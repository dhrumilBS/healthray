<?php

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Addons\LiteBase\EducationStrings;

$upgradeUrl = EducationStrings::UPGRADE_URL;
$discount   = EducationStrings::DEFAULT_DISCOUNT;
?>
<style>
    .dupli-litebase-installer-cta {
        background-color: #fff;
        border: 1px solid #dadada;
        padding: 25px 20px;
        margin: 10px auto 20px;
        width: calc(900px + 42px);
        max-width: calc(100vw - 40px);
        box-sizing: border-box;
        position: relative;
        font-family: Verdana, Arial, sans-serif;
    }
    .dupli-litebase-installer-cta h5 {
        margin: 0 0 16px;
        font-size: 18px;
        font-weight: 700;
    }
    .dupli-litebase-installer-cta h6 {
        font-weight: 700;
        font-size: 14px;
        margin: 0 0 16px;
    }
    .dupli-litebase-installer-cta p {
        color: #555;
        font-size: 14px;
        margin: 0 0 16px;
    }
    .dupli-litebase-installer-cta p:last-of-type {
        margin: 0;
    }
    .dupli-litebase-installer-cta a {
        color: #1da867;
        font-weight: 600;
        text-decoration: none;
    }
    .dupli-litebase-installer-cta a:hover {
        color: #178a52;
    }
    .dupli-litebase-installer-cta .list {
        display: flex;
        margin: 0 0 16px 0;
        padding: 0;
        max-width: 900px;
        flex-direction: row;
        flex-wrap: wrap;
        color: #555;
        font-size: 14px;
        list-style: none;
    }
    .dupli-litebase-installer-cta .list > .item {
        flex: 33.33% 0;
        box-sizing: border-box;
        padding: 0 0 2px 0;
    }
    .dupli-litebase-installer-cta .list > .item > span::before {
        content: '+ ';
    }
    .dupli-litebase-installer-cta .green {
        color: #1da867;
        font-weight: 600;
    }
    .dupli-litebase-installer-cta .fa-star {
        color: #f5b400;
        margin: 0 1px;
    }
    @media (max-width: 900px) {
        .dupli-litebase-installer-cta .list > .item {
            flex: 50% 0;
        }
    }
    @media (max-width: 600px) {
        .dupli-litebase-installer-cta .list > .item {
            flex: 100% 1;
        }
    }
</style>
<div class="dupli-litebase-installer-cta">
    <h5>Get Duplicator Pro and Unlock all the Powerful Features</h5>
    <p>
        Thanks for being a loyal Duplicator Lite user. Upgrade to Duplicator Pro to unlock all the awesome features and
        experience why Duplicator is consistently rated the best WordPress migration plugin.
    </p>
    <p>
        We know that you will truly love Duplicator. It has over 4000+ five star ratings
        (<?php echo str_repeat('<i class="fa fa-star" aria-hidden="true"></i>', 5); ?>)
        and is active on over 1 million websites.
    </p>
    <h6>Pro Features:</h6>
    <ul class="list">
        <?php foreach (EducationStrings::getFooterFeatureList() as $feature) : ?>
            <li class="item"><span><?php echo DUPX_U::esc_html($feature); ?></span></li>
        <?php endforeach; ?>
    </ul>
    <p>
        <a href="<?php echo DUPX_U::esc_url($upgradeUrl); ?>" target="_blank" rel="noopener noreferrer">
            Get Duplicator Pro Today and Unlock all the Powerful Features &raquo;
        </a>
    </p>
    <p>
        <strong>Bonus:</strong> Duplicator Lite users get
        <span class="green"><?php echo (int) $discount; ?>% off regular price</span>,
        automatically applied at checkout.
    </p>
</div>
