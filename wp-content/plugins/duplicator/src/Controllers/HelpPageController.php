<?php

/**
 * Impost installer page controller
 */

namespace Duplicator\Controllers;

use Duplicator\Core\CapMng;
use Duplicator\Core\Views\TplMng;
use Duplicator\Core\Controllers\AbstractBlankPageController;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Utils\Help\Article;
use Duplicator\Utils\Help\Category;
use Duplicator\Utils\Help\Help;

class HelpPageController extends AbstractBlankPageController
{
    const HELP_SLUG = 'duplicator-dynamic-help';

    /** @var string Help article tag of current page */
    protected $tag = '';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->pageSlug     = self::HELP_SLUG;
        $this->capatibility = CapMng::CAP_BASIC;
        $this->tag          = SnapUtil::sanitizeInput(INPUT_GET, 'tag', '');

        add_action('duplicator_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
    }

    /**
     * Render page content
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return void
     */
    public function renderContent($currentLevelSlugs, $innerPage): void
    {
        $help          = Help::getInstance();
        $tagArticles   = $help->getArticlesByTag($this->tag);
        $topCategories = $help->getTopLevelCategories();

        $categoriesWithArticles = [];
        $this->buildCategoryArticlesMap($help, $topCategories, $categoriesWithArticles);

        TplMng::getInstance()->render(
            "parts/help/main",
            [
                'tag'                    => $this->tag,
                'tagArticles'            => $tagArticles,
                'topCategories'          => $topCategories,
                'categoriesWithArticles' => $categoriesWithArticles,
            ]
        );
    }

    /**
     * Recursively build a map of category ID => articles for all categories.
     *
     * @param Help                  $help                   Help instance
     * @param Category[]            $categories             Categories to process
     * @param array<int, Article[]> $categoriesWithArticles Map of category ID => articles (passed by reference)
     *
     * @return void
     */
    private function buildCategoryArticlesMap(Help $help, array $categories, array &$categoriesWithArticles): void
    {
        foreach ($categories as $category) {
            if ($category->getArticleCount() > 0) {
                $categoriesWithArticles[$category->getId()] = $help->getArticlesByCategory($category->getId());
            }
            if (count($category->getChildren()) > 0) {
                $this->buildCategoryArticlesMap($help, $category->getChildren(), $categoriesWithArticles);
            }
        }
    }
}
