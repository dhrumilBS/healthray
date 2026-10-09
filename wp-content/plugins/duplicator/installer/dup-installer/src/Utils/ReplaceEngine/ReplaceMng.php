<?php

namespace Duplicator\Installer\Utils\ReplaceEngine;

use Duplicator\Installer\Utils\Log\Log;

/**
 * Search and replace manager
 * singleton class
 */
final class ReplaceMng
{
    const GLOBAL_SCOPE_KEY = '___!GLOBAL!___!SCOPE!___';

    /** @var ?self */
    private static $instance;

    /**
     * full list items not sorted
     *
     * @var ReplaceItem[]
     */
    private $items = [];

    /**
     * items sorted by priority and scope
     * [
     *      10 => [
     *             '___!GLOBAL!___!SCOPE!___' => [
     *                  SearchReplaceItem
     *                  SearchReplaceItem
     *                  SearchReplaceItem
     *              ],
     *              'scope_one' => [
     *                  SearchReplaceItem
     *                  SearchReplaceItem
     *              ]
     *          ],
     *      20 => [
     *          .
     *          .
     *          .
     *      ]
     * ]
     *
     * @var array<int,array<string,ReplaceItem[]>>
     */
    private $prorityScopeItems = [];

    /**
     *
     * @return ReplaceMng
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Class constructor
     */
    private function __construct()
    {
    }

    /**
     *
     * @return array<array{id:int,search:string,replace:string,type:string,prority:int,scope:string[]}>
     */
    public function getArrayData(): array
    {
        $data = [];

        foreach ($this->items as $item) {
            $data[] = $item->toArray();
        }

        return $data;
    }

    /**
     * Set data from array
     *
     * @param array<array{search:string,replace:string,type:string,prority:int,scope:string[]}> $data Array of data
     *
     * @return void
     */
    public function setFromArrayData($data): void
    {

        foreach ($data as $itemArray) {
            $new_item = ReplaceItem::getItemFromArray($itemArray);
            $this->setNewItem($new_item);
        }
    }

    /**
     *
     * @param string               $search  search string
     * @param string               $replace replace string
     * @param string               $type    item type ReplaceItem::[TYPE_STRING|TYPE_URL|TYPE_URL_NORMALIZE_DOMAIN|TYPE_PATH]
     * @param int                  $prority lower first
     * @param bool|string|string[] $scope   true = global scope | false = never | string signle scope | string[] scope list
     *
     * @return boolean|ReplaceItem false if fail
     */
    public function addItem($search, $replace, $type = ReplaceItem::TYPE_STRING, $prority = 10, $scope = true)
    {
        $search  = (string) $search;
        $replace = (string) $replace;

        if (strlen($search) == 0 || $search === $replace) {
            return false;
        }

        if (is_bool($scope)) {
            $scope = $scope ? self::GLOBAL_SCOPE_KEY : '';
        }

        if (is_array($scope)) {
            $scopeStr = implode(',', $scope);
            $scopeStr = (strlen($scopeStr) > 50 ? substr($scopeStr, 0, 50) . "..." : $scopeStr);
        } else {
            $scopeStr = 'ALL';
        }

        Log::info(
            "SEARCH ITEM[T:" . str_pad($type, 5) . "|P:" . str_pad((string) $prority, 2) . "]" .
                " SEARCH: " . $search .
                " REPLACE: " . $replace . " [SCOPE: " . $scopeStr . "]"
        );

        $new_item = new ReplaceItem($search, $replace, $type, $prority, $scope);

        return $this->setNewItem($new_item);
    }

    /**
     * Set new item
     *
     * @param ReplaceItem $new_item new item
     *
     * @return ReplaceItem
     */
    private function setNewItem(ReplaceItem $new_item): ReplaceItem
    {
        $this->items[$new_item->getId()] = $new_item;

        // create priority array
        if (!isset($this->prorityScopeItems[$new_item->prority])) {
            $this->prorityScopeItems[$new_item->prority] = [];

            // sort by priority
            ksort($this->prorityScopeItems);
        }

        // create scope list
        foreach ($new_item->scope as $scope) {
            if (!isset($this->prorityScopeItems[$new_item->prority][$scope])) {
                $this->prorityScopeItems[$new_item->prority][$scope] = [];
            }
            $this->prorityScopeItems[$new_item->prority][$scope][] = $new_item;
        }

        return $new_item;
    }

    /**
     * get all search and reaple items by scpoe
     *
     * @param null|string $scope       if scope is empty get only global scope
     * @param bool        $globalScope if true get global scope
     *
     * @return ReplaceItem[]
     */
    private function getSearchReplaceItems($scope = null, $globalScope = true): array
    {
        $items_list = [];
        foreach ($this->prorityScopeItems as $priority => $priority_list) {
            // get scope list
            if (!empty($scope) && isset($priority_list[$scope])) {
                foreach ($priority_list[$scope] as $item) {
                    $items_list[] = $item;
                }
            }

            // get global scope
            if ($globalScope && isset($priority_list[self::GLOBAL_SCOPE_KEY])) {
                foreach ($priority_list[self::GLOBAL_SCOPE_KEY] as $item) {
                    $items_list[] = $item;
                }
            }
        }

        return $items_list;
    }

    /**
     * get replace list by scope
     * result
     * [
     *      ['search' => ...,'replace' => ...]
     *      ['search' => ...,'replace' => ...]
     * ]
     *
     * @param null|string $scope         if scope is empty get only global scope
     * @param bool        $unique_search If true it eliminates the double searches leaving the one with lower priority.
     * @param bool        $globalScope   if true get global scope
     *
     * @return array<array{search:string,replace:string}> pairs search and replace
     */
    public function getSearchReplaceList($scope = null, $unique_search = true, $globalScope = true): array
    {
        Log::info('-- SEARCH LIST -- SCOPE: ' . Log::v2str($scope), Log::LV_DEBUG);

        $items_list = $this->getSearchReplaceItems($scope, $globalScope);
        if (Log::isLevel(Log::LV_HARD_DEBUG)) {
            Log::info('-- SEARCH LIST ITEMS --' . "\n" . print_r($items_list, true), Log::LV_HARD_DEBUG);
        }

        if ($unique_search) {
            $items_list = self::uniqueSearchListItem($items_list);
            if (Log::isLevel(Log::LV_HARD_DEBUG)) {
                Log::info('-- UNIQUE LIST ITEMS --' . "\n" . print_r($items_list, true), Log::LV_HARD_DEBUG);
            }
        }

        Log::info('--- BASE STRINGS ---');
        foreach ($items_list as $index => $item) {
            Log::info(
                'SEARCH[' . str_pad($item->type, 5, ' ', STR_PAD_RIGHT) . ']' . str_pad((string) ($index + 1), 3, ' ', STR_PAD_LEFT) . ":" .
                    str_pad(Log::v2str($item->search) . " ", 50, '=', STR_PAD_RIGHT) .
                    "=> " .
                    Log::v2str($item->replace)
            );
        }

        $result = [];

        foreach ($items_list as $item) {
            $result = array_merge($result, $item->getPairsSearchReplace());
        }

        // remove empty search strings
        $result = array_filter($result, function ($val): bool {
            if (!empty($val['search'])) {
                return true;
            } else {
                Log::info('Empty search string remove, replace: ' . Log::v2str($val['replace']), Log::LV_DETAILED);
                return false;
            }
        });

        if (Log::isLevel(Log::LV_DEBUG)) {
            Log::info('--- REXEXES ---');
            foreach ($result as $index => $c_sr) {
                Log::info(
                    'SEARCH' . str_pad((string) ($index + 1), 3, ' ', STR_PAD_LEFT) . ":" .
                        str_pad(Log::v2str($c_sr['search']) . " ", 50, '=', STR_PAD_RIGHT) .
                        "=> " .
                        Log::v2str($c_sr['replace'])
                );
            }
        }

        return $result;
    }

    /**
     * remove duplicated search strings.
     * Leave the object at lower priority
     *
     * @param ReplaceItem[] $list list of SearchReplaceItem
     *
     * @return ReplaceItem[]
     */
    private static function uniqueSearchListItem(array $list): array
    {
        $search_strings = [];
        $result         = [];

        foreach ($list as $item) {
            if (!in_array($item->search, $search_strings)) {
                $result[]         = $item;
                $search_strings[] = $item->search;
            }
        }

        return $result;
    }
}
