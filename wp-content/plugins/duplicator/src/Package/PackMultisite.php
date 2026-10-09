<?php

namespace Duplicator\Package;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Libs\Snap\SnapDB;
use Duplicator\Libs\Snap\SnapWP;

class PackMultisite
{
    /** @var int[] */
    public $FilterSites = [];
    /** @var ?string[] */
    protected $tablesFilters;

    /**
     * Filter props on json encode
     *
     * @return string[]
     */
    public function __sleep(): array
    {
        $props = array_keys(get_object_vars($this));
        return array_diff($props, ['tablesFilters']);
    }

    /**
     * Wakeup
     *
     * @return void
     */
    public function __wakeup(): void
    {
        if (is_string($this->FilterSites)) {
            $this->FilterSites = [];
        }
    }

    /**
     * Get dirs to filter
     *
     * @return string[]
     */
    public function getDirsToFilter(): array
    {
        if (!empty($this->FilterSites)) {
            $path_arr       = [];
            $wp_content_dir = str_replace("\\", "/", WP_CONTENT_DIR);
            foreach ($this->FilterSites as $site_id) {
                if ($site_id == 1) {
                    if (SnapWP::getMuGeneration() == SnapWP::MU_GENERATION_35_PLUS) {
                        $uploads_dir = $wp_content_dir . '/uploads';
                        foreach (scandir($uploads_dir) as $node) {
                            $fullpath = $uploads_dir . '/' . $node;
                            if ($node == '.' || $node == '.htaccess' || $node == '..') {
                                continue;
                            }
                            if (is_dir($fullpath)) {
                                if ($node != 'sites') {
                                    $path_arr[] = $fullpath;
                                }
                            }
                        }
                    } else {
                        $path_arr[] = $wp_content_dir . '/uploads';
                    }
                } else {
                    if (file_exists($wp_content_dir . '/uploads/sites/' . $site_id)) {
                        $path_arr[] = $wp_content_dir . '/uploads/sites/' . $site_id;
                    }
                    if (file_exists($wp_content_dir . '/blogs.dir/' . $site_id)) {
                        $path_arr[] = $wp_content_dir . '/blogs.dir/' . $site_id;
                    }
                }
            }
            return $path_arr;
        } else {
            return [];
        }
    }

    /**
     * Get tables to filter
     *
     * @return string[]
     */
    public function getTablesToFilter(): array
    {
        if (is_null($this->tablesFilters)) {
            global $wpdb;
            $this->tablesFilters = [];
            if (!empty($this->FilterSites)) {
                $prefixes = [];
                foreach ($this->FilterSites as $site_id) {
                    $prefix = $wpdb->get_blog_prefix($site_id);
                    if ($site_id == 1) {
                        $default_tables = [
                            'commentmeta',
                            'comments',
                            'links',
                            //'options', include always options table
                            'postmeta',
                            'posts',
                            'terms',
                            'term_relationships',
                            'term_taxonomy',
                            'termmeta',
                        ];
                        foreach ($default_tables as $tb) {
                            $this->tablesFilters[] = $prefix . $tb;
                        }
                    } else {
                        $prefixes[] = $prefix;
                    }
                }

                if (count($prefixes)) {
                    foreach ($prefixes as &$value) {
                        $value = SnapDB::quoteRegex($value);
                    }
                    $regex     = '^(' . implode('|', $prefixes) . ').+';
                    $sql_query = "SHOW TABLES WHERE Tables_in_" . esc_sql(DB_NAME) . " REGEXP '" . esc_sql($regex) . "'";
                    DupLog::trace('TABLE QUERY PREFIX FILTER: ' . $sql_query);
                    $sub_tables          = $wpdb->get_col($sql_query);
                    $this->tablesFilters = array_merge($this->tablesFilters, $sub_tables);
                }
            }
            DupLog::traceObject('TABLES TO FILTERS:', $this->tablesFilters);
        }
        return $this->tablesFilters;
    }
}
