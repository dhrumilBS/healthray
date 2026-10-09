<?php

declare(strict_types=1);

namespace Duplicator\Views;

/**
 * Single source of truth for the allowed-tag sets used with `wp_kses`.
 *
 * Each set is a closed list — extend it consciously when a new producer needs
 * a tag, do not loosen it ad-hoc.
 */
class KsesHelper
{
    /**
     * Icon-only markup: `<i>` / `<span>` with a class attribute. Use for places
     * that render a single glyph (e.g. legend entries, flag cells).
     *
     * @var array<string, array<string, bool>>
     */
    const ICON_TAGS = [
        'i'    => ['class' => true],
        'span' => ['class' => true],
        'img'  => [
            'src'   => true,
            'class' => true,
            'alt'   => true,
            'title' => true,
        ],
    ];

    /**
     * Minimal inline formatting (b, i, u, br, a).
     *
     * @var array<string, array<string, bool>>
     */
    const GEN_TAGS = [
        'b'  => [],
        'i'  => [],
        'u'  => [],
        'br' => [],
        'a'  => [
            'href'   => true,
            'target' => true,
        ],
    ];

    /**
     * Small rich-content snippets that mix anchors, buttons, icons and short
     * sublists (e.g. tooltip bodies built from multiple producers).
     *
     * @var array<string, array<string, bool>>
     */
    const RICH_TAGS = [
        'a'      => [
            'href'            => true,
            'class'           => true,
            'data-package-id' => true,
        ],
        'button' => [
            'type'            => true,
            'class'           => true,
            'data-package-id' => true,
        ],
        'span'   => ['class' => true],
        'i'      => ['class' => true],
        'img'    => [
            'src'   => true,
            'class' => true,
            'alt'   => true,
            'title' => true,
        ],
        'ul'     => ['class' => true],
        'li'     => ['class' => true],
        'br'     => [],
    ];

    /**
     * Common attributes shared by the footer tag set.
     *
     * @var array<string, bool>
     */
    const FOOTER_COMMON_ATTRS = [
        'id'                  => true,
        'class'               => true,
        'title'               => true,
        'aria-label'          => true,
        'aria-hidden'         => true,
        'data-dismiss-action' => true,
        'data-dismiss-nonce'  => true,
    ];

    /**
     * Allowed tags for a storage type icon (font icon or image)
     *
     * @return array<string, array<string, array<mixed>>>
     */
    public static function getStorageIconAllowedTags(): array
    {
        return [
            'i'   => ['class' => []],
            'img' => [
                'src'   => [],
                'class' => [],
                'alt'   => [],
                'title' => [],
            ],
        ];
    }

    /**
     * Allowed tags/attrs for HTML injected via the table-footer filters
     * (`duplicator_packages_table_footer_content`, `duplicator_storages_table_footer_content`).
     *
     * @return array<string, array<string, bool>>
     */
    public static function getFooterAllowedTags(): array
    {
        return [
            'div'    => self::FOOTER_COMMON_ATTRS,
            'span'   => self::FOOTER_COMMON_ATTRS,
            'p'      => self::FOOTER_COMMON_ATTRS,
            'strong' => self::FOOTER_COMMON_ATTRS,
            'em'     => self::FOOTER_COMMON_ATTRS,
            'br'     => [],
            'i'      => self::FOOTER_COMMON_ATTRS,
            'img'    => array_merge(self::FOOTER_COMMON_ATTRS, ['src' => true, 'alt' => true]),
            'a'      => array_merge(self::FOOTER_COMMON_ATTRS, ['href' => true, 'target' => true, 'rel' => true]),
            'button' => array_merge(self::FOOTER_COMMON_ATTRS, ['type' => true]),
        ];
    }
}
