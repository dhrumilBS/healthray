<?php

/**
 * Trait for package query operations
 */

declare(strict_types=1);

namespace Duplicator\Package;

/**
 * Trait TraitPackageQuery
 *
 * Handles package query operations for selecting, filtering and counting packages.
 *
 * @phpstan-require-extends AbstractPackage
 */
trait TraitPackageQuery
{
    /**
     * Get the next active Backup
     *
     * @param bool $refresh If true, bypass the static cache and re-query the database
     *
     * @return ?AbstractPackage
     */
    public static function getNextActive(bool $refresh = false): ?AbstractPackage
    {
        static $package = null;

        if ($package === null || $refresh) {
            $result = static::getPackagesByStatus([
                'relation' => 'AND',
                [
                    'op'     => '>=',
                    'status' => AbstractPackage::STATUS_PRE_PROCESS,
                ],
                [
                    'op'     => '<',
                    'status' => AbstractPackage::STATUS_COMPLETE,
                ]
            ], 1, 0, '`id` ASC');

            $package = count($result) > 0 ? $result[0] : false;
        }

        return $package ?: null;
    }

    /**
     * Get Backup by hash
     *
     * @param string $hash Hash
     *
     * @return false|static false if fail
     */
    public static function getByHash($hash)
    {
        global $wpdb;
        $table = static::getTableName();
        $sql   = $wpdb->prepare("SELECT * FROM `{$table}` where hash = %s", $hash);
        $row   = $wpdb->get_row($sql);
        if ($row) {
            return static::packageFromRow($row);
        } else {
            return false;
        }
    }

    /**
     * Get backup by archive filename. Excludes the FLAG_TEMPORARY scaffolding row,
     * which can share archive_name with its real clone.
     *
     * @param string $archiveName Archive filename
     *
     * @return ?static Return Backup or null on failure
     */
    public static function getByArchiveName($archiveName)
    {
        global $wpdb;
        if (!preg_match(DUPLICATOR_ARCHIVE_REGEX_PATTERN, $archiveName, $matches)) {
            return null;
        }

        $table = static::getTableName();
        $sql   = $wpdb->prepare(
            "SELECT * FROM `{$table}` "
            . "WHERE archive_name = %s "
            . "AND FIND_IN_SET(%s, `flags`) = 0 "
            . "ORDER BY `id` DESC LIMIT 1",
            $archiveName,
            AbstractPackage::FLAG_TEMPORARY
        );
        $row   = $wpdb->get_row($sql);
        if ($row) {
            return static::packageFromRow($row);
        } else {
            return null;
        }
    }

    /**
     * Select Backups from database
     *
     * @param string   $where                 where conditions
     * @param int      $limit                 max row numbers if 0 the limit is PHP_INT_MAX
     * @param int      $offset                offset 0 is at begin
     * @param string   $orderBy               default `id` ASC if empty no order
     * @param string   $resultType            ids => int[], row => row without Backup blob, fullRow => row with Backup blob, objs => DUP_Package objects[]
     * @param string[] $backupTypes           backup types to include, is empty all types are included
     * @param bool     $includeTemporary      if true include temporary packages (default: false)
     * @param bool     $includeWithoutStorage if true include all packages regardless of storage flags.
     *                                        Default: false filters out completed orphaned packages (status=100
     *                                        without local/remote storage). In-progress and failed packages
     *                                        are always included regardless of this setting.
     *
     * @return self[]|object[]|int[]
     */
    public static function dbSelect(
        string $where,
        int $limit = 0,
        int $offset = 0,
        string $orderBy = '`id` ASC',
        string $resultType = 'objs',
        array $backupTypes = [],
        bool $includeTemporary = false,
        bool $includeWithoutStorage = false
    ): array {
        global $wpdb;
        $table = static::getTableName();
        $where = ' WHERE ' . (strlen($where) > 0 ? $where : '1');

        if (!$includeTemporary) {
            $where .= $wpdb->prepare(' AND FIND_IN_SET(%s, `flags`) = 0', AbstractPackage::FLAG_TEMPORARY);
        }

        // Filter out completed backups without storage (orphaned packages)
        if (!$includeWithoutStorage) {
            $where .= $wpdb->prepare(
                ' AND (`status` <> %d OR FIND_IN_SET(%s, `flags`) > 0 OR FIND_IN_SET(%s, `flags`) > 0)',
                AbstractPackage::STATUS_COMPLETE,
                AbstractPackage::FLAG_HAVE_LOCAL,
                AbstractPackage::FLAG_HAVE_REMOTE
            );
        }

        if (count($backupTypes) > 0) {
            $placeholders = implode(',', array_fill(0, count($backupTypes), '%s'));
            $where       .= $wpdb->prepare(" AND `type` IN ($placeholders)", ...$backupTypes);
        }

        $packages   = [];
        $offsetStr  = $wpdb->prepare(' OFFSET %d', $offset);
        $limitStr   = $wpdb->prepare(' LIMIT %d', ($limit > 0 ? $limit : PHP_INT_MAX));
        $orderByStr = empty($orderBy) ? '' : ' ORDER BY ' . $orderBy . ' ';
        switch ($resultType) {
            case 'ids':
                $cols = '`id`';
                break;
            case 'row':
                $cols = '`id`,`type`,`name`,`hash`,`archive_name`,`status`,`flags`,`version`,`created`,`updated_at`';
                break;
            case 'fullRow':
            case 'objs':
            default:
                $cols = '*';
                break;
        }

        $rows = $wpdb->get_results('SELECT ' . $cols . ' FROM `' . $table . '` ' . $where . $orderByStr . $limitStr . $offsetStr);
        if ($rows != null) {
            switch ($resultType) {
                case 'ids':
                    foreach ($rows as $row) {
                        $packages[] = (int) $row->id;
                    }
                    break;
                case 'row':
                case 'fullRow':
                    $packages = $rows;
                    break;
                case 'objs':
                default:
                    foreach ($rows as $row) {
                        $package = static::packageFromRow($row);
                        if ($package != null) {
                            $packages[] = $package;
                        }
                    }
            }
        }
        return $packages;
    }


    /**
     * Conditions Example
     * [
     *   relation = 'AND',
     *   [ 'op' => '>=' , 'status' =>  self::STATUS_START ]
     *   [ 'op' => '<' , 'status' =>  self::STATUS_COMPLETE ]
     * ]
     *
     * @param array<string|int,string|array{op:string,status:int}> $conditions Conditions
     *
     * @return string
     */
    protected static function statusContitionsToWhere($conditions = []): string
    {
        $accepted_op = [
            '<',
            '>',
            '=',
            '<>',
            '>=',
            '<=',
        ];
        $relation    = (isset($conditions['relation']) && strtoupper($conditions['relation']) == 'OR') ? ' OR ' : ' AND ';
        unset($conditions['relation']);
        $where = '';
        if (!empty($conditions)) {
            $str_conds = [];
            foreach ($conditions as $cond) {
                $op          = (isset($cond['op']) && in_array($cond['op'], $accepted_op)) ? $cond['op'] : '=';
                $status      = isset($cond['status']) ? (int) $cond['status'] : 0;
                $str_conds[] = 'status ' . $op . ' ' . $status;
            }

            $where = implode($relation, $str_conds) . ' ';
        } else {
            $where = '1 ';
        }

        return $where;
    }

    /**
     * Execute $callback function foreach Backup result
     *
     * @param callable $callback    function callback(self $package)
     * @param string   $where       where conditions
     * @param int      $limit       max row numbers if 0 the limit is PHP_INT_MAX
     * @param int      $offset      offset 0 is at begin
     * @param string   $orderBy     default `id` ASC if empty no order
     * @param string[] $backupTypes backup types to include, is empty all types are included
     *
     * @return void
     */
    public static function dbSelectCallback(
        callable $callback,
        string $where,
        int $limit = 0,
        int $offset = 0,
        string $orderBy = '`id` ASC',
        array $backupTypes = []
    ): void {
        $ids = static::dbSelect($where, $limit, $offset, $orderBy, 'ids', $backupTypes);

        foreach ($ids as $id) {
            if (($package = static::getById($id)) == false) {
                continue;
            }

            call_user_func($callback, $package);
            unset($package);
        }
    }

    /**
     * Get Backups with status conditions and/or pagination
     * Conditions Example
     * [
     *   relation = 'AND',
     *   [ 'op' => '>=' , 'status' =>  self::STATUS_START ]
     *   [ 'op' => '<' , 'status' =>  self::STATUS_COMPLETE ]
     * ]
     *
     * @param array<string|int,string|array{op:string,status:int}> $conditions  Conditions if empty get all Backups
     * @param int                                                  $limit       max row numbers if 0 the limit is PHP_INT_MAX
     * @param int                                                  $offset      offset 0 is at begin
     * @param string                                               $orderBy     default `id` ASC if empty no order
     * @param string                                               $resultType  ids => int[], row => row without Backup blob,
     *                                                                          fullRow => row with Backup blob,
     *                                                                          objs => DUP_Package objects[]
     * @param string[]                                             $backupTypes backup types to include, is empty all types are included
     *
     * @return DupPackage[]|object[]|int[]
     */
    public static function getPackagesByStatus(
        array $conditions = [],
        int $limit = 0,
        int $offset = 0,
        string $orderBy = '`id` ASC',
        string $resultType = 'objs',
        array $backupTypes = []
    ): array {
        return static::dbSelect(static::statusContitionsToWhere($conditions), $limit, $offset, $orderBy, $resultType, $backupTypes);
    }

    /**
     * Get Backups row db with status conditions and/or pagination
     *
     * Conditions Example
     * [
     *   relation = 'AND',
     *   [ 'op' => '>=' , 'status' =>  self::STATUS_START ]
     *   [ 'op' => '<' , 'status' =>  self::STATUS_COMPLETE ]
     * ]
     *
     * @param array<string|int,string|array{op:string,status:int}> $conditions  Conditions if empty get all Backups
     * @param int                                                  $limit       max row numbers if 0 the limit is PHP_INT_MAX
     * @param int                                                  $offset      offset 0 is at begin
     * @param string                                               $orderBy     default `id` ASC if empty no order
     * @param string[]                                             $backupTypes backup types to include, is empty all types are included
     *
     * @return object[]      // return row database without Backup blob
     */
    public static function getRowByStatus(
        array $conditions = [],
        int $limit = 0,
        int $offset = 0,
        string $orderBy = '`id` ASC',
        array $backupTypes = []
    ): array {
        return static::dbSelect(static::statusContitionsToWhere($conditions), $limit, $offset, $orderBy, 'row', $backupTypes);
    }

    /**
     * Get Backups ids with status conditions and/or pagination
     * Conditions Example
     * [
     *   relation = 'AND',
     *   [ 'op' => '>=' , 'status' =>  self::STATUS_START ]
     *   [ 'op' => '<' , 'status' =>  self::STATUS_COMPLETE ]
     * ]
     *
     * @param array<string|int,string|array{op:string,status:int}> $conditions  Conditions if empty get all Backups
     * @param int                                                  $limit       max row numbers if 0 the limit is PHP_INT_MAX
     * @param int                                                  $offset      offset 0 is at begin
     * @param string                                               $orderBy     default `id` ASC if empty no order
     * @param string[]                                             $backupTypes backup types to include, is empty all types are included
     *
     * @return int[] return row database without Backup blob
     */
    public static function getIdsByStatus(
        array $conditions = [],
        int $limit = 0,
        int $offset = 0,
        string $orderBy = '`id` ASC',
        array $backupTypes = []
    ): array {
        return static::dbSelect(static::statusContitionsToWhere($conditions), $limit, $offset, $orderBy, 'ids', $backupTypes);
    }

    /**
     * count Backup with status condition
     * Conditions Example
     * [
     *   relation = 'AND',
     *   [ 'op' => '>=' , 'status' =>  self::STATUS_START ]
     *   [ 'op' => '<' , 'status' =>  self::STATUS_COMPLETE ]
     * ]
     *
     * @param array<string|int,string|array{op:string,status:int}> $conditions  Conditions if empty get all Backups
     * @param string[]                                             $backupTypes backup types to include, is empty all types are included
     *
     * @return int
     */
    public static function countByStatus(array $conditions = [], array $backupTypes = []): int
    {
        $where = static::statusContitionsToWhere($conditions);
        $ids   = static::dbSelect($where, 0, 0, '', 'ids', $backupTypes);
        return count($ids);
    }

    /**
     * Execute $callback function foreach Backup result
     * For each iteration the memory is released
     * Conditions Example
     * [
     *   relation = 'AND',
     *   [ 'op' => '>=' , 'status' =>  self::STATUS_START ]
     *   [ 'op' => '<' , 'status' =>  self::STATUS_COMPLETE ]
     * ]
     *
     * @param callable                                             $callback    function callback(self $package)
     * @param array<string|int,string|array{op:string,status:int}> $conditions  Conditions if empty get all Backups
     * @param int                                                  $limit       max row numbers if 0 the limit is PHP_INT_MAX
     * @param int                                                  $offset      offset 0 is at begin
     * @param string                                               $orderBy     default `id` ASC if empty no order
     * @param string[]                                             $backupTypes backup types to include, is empty all types are included
     *
     * @return void
     */
    public static function dbSelectByStatusCallback(
        callable $callback,
        array $conditions = [],
        int $limit = 0,
        int $offset = 0,
        string $orderBy = '`id` ASC',
        array $backupTypes = []
    ): void {
        static::dbSelectCallback($callback, static::statusContitionsToWhere($conditions), $limit, $offset, $orderBy, $backupTypes);
    }

    /**
     * Reads the current Backup status directly from the database, bypassing any
     * in-memory state. Includes temporary-flagged rows.
     *
     * @param int $id Backup ID
     *
     * @return ?int The stored status, or null if the row doesn't exist
     */
    public static function getDbStatusById(int $id): ?int
    {
        global $wpdb;

        if ($id < 0) {
            return null;
        }

        $table  = static::getTableName();
        $status = $wpdb->get_var($wpdb->prepare("SELECT `status` FROM `{$table}` WHERE id = %d", $id));
        return $status === null ? null : (int) $status;
    }

    /**
     * Quickly determine without going through the overhead of creating Backup objects
     *
     * Narrow check: true only when a Backup row is in a building status. As a
     * guard against interfering with the build lifecycle use
     * PackageUtils::isBackupCreationBlocked() instead, which also covers the
     * running AutoTune session and in-progress cancellations; use this method
     * directly only when the running-build condition alone is intended.
     *
     * @return bool
     */
    public static function isPackageRunning(): bool
    {
        $ids = static::getIdsByStatus(
            [
                [
                    'op'     => '>=',
                    'status' => AbstractPackage::STATUS_PRE_PROCESS,
                ],
                [
                    'op'     => '<',
                    'status' => AbstractPackage::STATUS_COMPLETE,
                ],
            ]
        );
        return count($ids) > 0;
    }

    /**
     * True when a Backup is in the active build phase, reading files and database.
     * Storage processing is excluded: the archive is already closed at that point.
     *
     * @return bool
     */
    public static function isPackageBuilding(): bool
    {
        $ids = static::getIdsByStatus(
            [
                [
                    'op'     => '>=',
                    'status' => AbstractPackage::STATUS_PRE_PROCESS,
                ],
                [
                    'op'     => '<',
                    'status' => AbstractPackage::STATUS_STORAGE_PROCESSING,
                ],
            ]
        );
        return count($ids) > 0;
    }
}
