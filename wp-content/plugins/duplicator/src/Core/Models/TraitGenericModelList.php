<?php

namespace Duplicator\Core\Models;

use Duplicator\Utils\Logging\DupLog;
use Exception;
use Throwable;
use wpdb;

trait TraitGenericModelList
{
    /**
     * Get entity by id
     *
     * @param int $id entity id
     *
     * @return static|false Return entity istance or false on failure
     */
    public static function getById($id)
    {
        if ($id < 0) {
            return false;
        }

        $result = false;

        try {
            /** @var wpdb $wpdb */
            global $wpdb;

            $query = $wpdb->prepare(
                "SELECT * FROM `" . self::getTableName(true) . "` WHERE `id` = %d",
                $id
            );
            if (($row = $wpdb->get_row($query, ARRAY_A)) === null) {
                throw new Exception('Error getting row by id: ' . $id);
            }

            $result = static::getModelFromRow($row);
        } catch (Throwable $e) {
            DupLog::infoTraceException($e);
            $result = false;
        }

        return $result;
    }

    /**
     * Check if entity id exists
     *
     * @param int $id entity id
     *
     * @return bool true if exists false otherwise
     */
    public static function exists($id)
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        $where = static::getWhereClause();
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM `" . self::getTableName(true) . "` WHERE `id` = %d AND {$where}",
            $id
        );
        if (($count = $wpdb->get_var($query)) === null) {
            return false;
        }

        return $count > 0;
    }

    /**
     * Return the number of entities of current type
     *
     * @return int<0, max>
     */
    public static function count(): int
    {
        return (int) parent::countItemsFromDatabase();
    }

    /**
     * Delete entity by id
     *
     * @param int $id entity id
     *
     * @return bool true on success of false on failure
     */
    public static function deleteById($id)
    {
        if ($id < 0) {
            return true;
        }

        if (($entity = self::getById($id)) === false) {
            return true;
        }

        return $entity->delete();
    }

    /**
     * Get entities of current type
     *
     * @param int<0, max>                          $page           current page, if $pageSize is 0 o 1 $pase is the offset
     * @param int<0, max>                          $pageSize       page size, 0 return all entities
     * @param ?callable                            $sortCallback   sort function on items result
     * @param ?callable                            $filterCallback filter on items result
     * @param array{'col': string, 'mode': string} $orderby        query ordder by
     *
     * @return static[]|false return entities list of false on failure
     */
    public static function getAll(
        $page = 0,
        $pageSize = 0,
        $sortCallback = null,
        $filterCallback = null,
        $orderby = [
            'col'  => 'id',
            'mode' => 'ASC',
        ]
    ) {
        return parent::getItemsFromDatabase($page, $pageSize, $sortCallback, $filterCallback, $orderby);
    }

    /**
     * Get entities ids of current type
     *
     * @param int<0, max>                          $page           current page, if $pageSize is 0 o 1 $pase is the offset
     * @param int<0, max>                          $pageSize       page size, 0 return all entities
     * @param ?callable                            $sortCallback   sort function on items result
     * @param ?callable                            $filterCallback filter on items result
     * @param array{'col': string, 'mode': string} $orderby        query ordder by
     *
     * @return int[]|false return entities list of false on failure
     */
    public static function getIds(
        $page = 0,
        $pageSize = 0,
        $sortCallback = null,
        $filterCallback = null,
        $orderby = [
            'col'  => 'id',
            'mode' => 'ASC',
        ]
    ) {
        return parent::getIdsFromDatabase($page, $pageSize, $sortCallback, $filterCallback, $orderby);
    }

    /**
     * Execute a callback on selected entities
     *
     * @param callable                             $callback       callback function
     * @param int<0, max>                          $page           current page, if $pageSize is 0 o 1 $pase is the offset
     * @param int<0, max>                          $pageSize       page size, 0 return all entities
     * @param ?callable                            $sortCallback   sort function on items result
     * @param ?callable                            $filterCallback filter on items result
     * @param array{'col': string, 'mode': string} $orderby        query ordder by
     *
     * @return bool return true on success or false on failure
     */
    public static function listCallback(
        $callback,
        $page = 0,
        $pageSize = 0,
        $sortCallback = null,
        $filterCallback = null,
        $orderby = [
            'col'  => 'id',
            'mode' => 'ASC',
        ]
    ): bool {
        try {
            if (!is_callable($callback)) {
                throw new Exception('Callback is not callable');
            }

            $ids = static::getIds($page, $pageSize, $sortCallback, $filterCallback, $orderby);
            if ($ids === false) {
                throw new Exception('Error getting ids');
            }

            foreach ($ids as $id) {
                $entity = static::getById($id);
                if ($entity === false) {
                    continue;
                }
                call_user_func($callback, $entity);
            }
        } catch (Throwable $e) {
            DupLog::infoTraceException($e);
            return false;
        }

        return true;
    }

    /**
     * Delete all entity of current type
     *
     * @return int<0,max>|false The number of rows updated, or false on error.
     */
    public static function deleteAll()
    {
        $numDeleted = 0;

        $result = self::listCallback(function ($entity) use ($numDeleted): void {
            /** @var static $entity */
            if ($entity->delete() === false) {
                DupLog::trace('Can\'t delete entity ' . $entity->getId());
            } else {
                $numDeleted++;
            }
        });

        return ($result ? $numDeleted : false);
    }
}
