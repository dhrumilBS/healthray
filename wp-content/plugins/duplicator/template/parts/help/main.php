<?php

use Duplicator\Utils\Support\SupportToolkit;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$tagArticles   = $tplMng->getDataValueArray('tagArticles');
$topCategories = $tplMng->getDataValueArray('topCategories');
?>
<div id="dupli-help-wrapper">
    <div id="dupli-help-header">
        <img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL . 'assets/img/duplicator-header-logo.svg'); ?>" />
    </div>
    <div id="dupli-help-content">
        <div id="dupli-help-search">
            <input type="text" placeholder="<?php esc_attr_e("Search", 'duplicator'); ?>" />
            <ul id="dupli-help-search-results"></ul>
            <div id="dupli-help-search-results-empty"><?php esc_html_e("No results found", 'duplicator'); ?></div>
        </div>
        <div id="dupli-context-articles">
            <?php if (count($tagArticles) > 0) : ?>
                <h2><?php esc_html_e("Related Articles", 'duplicator'); ?></h2>
                <?php $tplMng->render('parts/help/article-list', ['articles' => $tagArticles]); ?>
            <?php endif; ?>
        </div>
        <div id="dupli-help-categories">
            <?php $tplMng->render('parts/help/category-list', ['categories' => $topCategories]); ?>
        </div>
        <div id="dupli-help-footer">
            <div class="dupli-help-footer-block">
                <i aria-hidden="true" class="fa fa-file-alt"></i>
                <h3><?php esc_html_e("View Documentation", 'duplicator'); ?></h3>
                <p>
                    <?php esc_html_e("Browse documentation, reference material, and tutorials for Duplicator.", 'duplicator'); ?>
                </p>
                <a
                    href="<?php echo esc_url(DUPLICATOR_BLOG_URL . 'docs'); ?>"
                    rel="noopener noreferrer"
                    target="_blank"
                    class="button">
                  <?php esc_html_e("View All Documentation", 'duplicator'); ?>
                </a>
            </div>
            <div class="dupli-help-footer-block">
                <i aria-hidden="true" class="fa fa-life-ring"></i>
                <h3><?php esc_html_e("Get Support", 'duplicator'); ?></h3>
                <p>
                    <?php esc_html_e("You can access our world-class support below.", 'duplicator'); ?>
                    <?php echo wp_kses(
                        sprintf(
                            _x(
                                'If reporting a bug, remember to include the %1$s to speed up the debugging process.',
                                '1: diagnostic data link with label or link to instructions to download logs manually',
                                'duplicator'
                            ),
                            SupportToolkit::getDiagnosticInfoLinks()
                        ),
                        [
                            'a' => [
                                'href'   => [],
                                'target' => [],
                            ],
                        ]
                    ); ?>
                </p>
                <a
                    href="<?php echo esc_url(SupportToolkit::getSupportUrl()); ?>"
                    rel="noopener noreferrer"
                    target="_blank"
                    class="button">
                    <?php esc_html_e("Get Support", 'duplicator'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
