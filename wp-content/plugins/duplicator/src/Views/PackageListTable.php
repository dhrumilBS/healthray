<?php

namespace Duplicator\Views;

use WP_List_Table;

/**
 * List table class
 */
class PackageListTable extends WP_List_Table
{
    /**
     * Get num items per page
     *
     * @return int
     */
    public static function get_per_page(): int // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        return UserUIOptions::getInstance()->get(UserUIOptions::VAL_PACKAGES_PER_PAGE);
    }

    /**
     * Display pagination
     *
     * @param int $total_items Total items
     * @param int $per_page    Per page
     *
     * @return void
     */
    public function display_pagination($total_items, $per_page = 10): void // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ]);
        $which = 'top';
        $this->pagination($which);
    }
}
