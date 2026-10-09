<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Utils;

/**
 * Build duplicator.com URLs with UTM parameters.
 */
class LiteBaseLinks
{
    const DUPLICATOR_URL     = 'https://duplicator.com/';
    const L1_DOCS_PATH       = 'knowledge-base';
    const L1_DOCS_CATEGORIES = 'knowledge-base-article-categories';

    /**
     * Build a duplicator.com URL with UTM parameters.
     *
     * @param string|string[] $paths   Path (or path segments) appended to the base URL.
     * @param string          $medium  utm_medium value.
     * @param string          $content utm_content value.
     *
     * @return string
     */
    public static function buildUrl($paths, string $medium = '', string $content = ''): string
    {
        $utm = [
            'utm_source'   => 'WordPress',
            'utm_campaign' => 'liteplugin',
        ];

        if ($medium !== '') {
            $utm['utm_medium'] = $medium;
        }

        if ($content !== '') {
            $utm['utm_content'] = $content;
        }

        $paths = trim(implode('/', (array) $paths), '/') . '/';

        return self::DUPLICATOR_URL . $paths . '?' . http_build_query($utm);
    }

    /**
     * URL for the upgrade landing page.
     *
     * @param string $medium  utm_medium value (e.g. 'lite-upgrade-bar').
     * @param string $content utm_content value (typically the page/section identifier).
     *
     * @return string
     */
    public static function getUpgradeUrl(string $medium, string $content = ''): string
    {
        return self::buildUrl('lite-upgrade', $medium, $content);
    }

    /**
     * URL for a documentation article.
     *
     * @param string $slug    Article slug appended to the docs path.
     * @param string $medium  utm_medium value.
     * @param string $content utm_content value.
     *
     * @return string
     */
    public static function getDocUrl(string $slug = '', string $medium = '', string $content = ''): string
    {
        $paths = [self::L1_DOCS_PATH];
        if ($slug !== '') {
            $paths[] = $slug;
        }

        return self::buildUrl($paths, $medium, $content);
    }

    /**
     * URL for a documentation article-category listing.
     *
     * @param string $slug    Category slug appended to the docs categories path.
     * @param string $medium  utm_medium value.
     * @param string $content utm_content value.
     *
     * @return string
     */
    public static function getDocCategoryUrl(string $slug, string $medium = '', string $content = ''): string
    {
        return self::buildUrl([self::L1_DOCS_CATEGORIES, $slug], $medium, $content);
    }

    /**
     * URL for a duplicator.com blog/post slug.
     *
     * @param string $slug    Post slug appended to the base URL.
     * @param string $medium  utm_medium value.
     * @param string $content utm_content value.
     *
     * @return string
     */
    public static function getPostUrl(string $slug, string $medium = '', string $content = ''): string
    {
        return self::buildUrl($slug, $medium, $content);
    }
}
