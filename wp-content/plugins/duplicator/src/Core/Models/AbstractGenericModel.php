<?php

namespace Duplicator\Core\Models;

use Duplicator\Utils\Logging\DupLog;
use Exception;
use Throwable;
use wpdb;

/**
 * Abstract Generic Model
 */
abstract class AbstractGenericModel
{
    /** @var int */
    protected int $id = -1;
    /** @var string plugin version on update */
    protected string $version = DUPLICATOR_VERSION;
    /** @var string timestamp YYYY-MM-DD HH:MM:SS UTC */
    protected string $created = '';
    /** @var string timestamp YYYY-MM-DD HH:MM:SS UTC */
    protected string $updated = '';

    /**
     * Return entity id
     *
     * @return int
     */
    final public function getId(): int
    {
        return $this->id;
    }

    /**
     * Return created timestamp
     *
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created;
    }

    /**
     * Return updated timestamp
     *
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updated;
    }

    /**
     * Save entity
     *
     * @return bool True on success, or false on error.
     */
    public function save(): bool
    {
        return $this->id < 0 ? $this->insert() !== false : $this->update();
    }

    /**
     * Schedule a save at the end of the current request.
     *
     * Safe to call from contexts where save() cannot run immediately
     * (e.g. inside __unserialize, before the entity id is fully wired).
     *
     * @return void
     */
    public function saveOnShutdown(): void
    {
        $entity = $this;
        add_action('shutdown', static function () use ($entity) {
            $entity->save();
        });
    }

    /**
     * This method is called before insert new Model, can be overridden by child classes
     *
     * @return void
     */
    protected function beforeInsert(): void
    {
        $this->created = gmdate("Y-m-d H:i:s");
        $this->version = DUPLICATOR_VERSION;
    }

    /**
     * This method is called before update Model, can be overridden by child classes
     *
     * @return void
     */
    protected function beforeUpdate(): void
    {
        $this->updated = gmdate("Y-m-d H:i:s");
        $this->version = DUPLICATOR_VERSION;
    }

    /**
     * Get insert data and formats, can be overridden by child classes
     *
     * @return array{data:array<string,mixed>,formats:string[]}
     */
    protected function getInsertData(): array
    {
        return [
            'data'    => [
                'version'    => $this->version,
                'created_at' => $this->created,
                'updated_at' => $this->updated,
            ],
            'formats' =>  [
                '%s',
                '%s',
                '%s',
            ],
        ];
    }

    /**
     * Get update data and formats, can be overridden by child classes
     *
     * @return array{data:array<string,mixed>,formats:string[]}
     */
    protected function getUpdateData(): array
    {
        return [
            'data'    => [
                'version'    => $this->version,
                'updated_at' => $this->updated,
            ],
            'formats' => [
                '%s',
                '%s',
            ],
        ];
    }

    /**
     * Insert entity
     *
     * @return int|false The number of rows inserted, or false on error.
     */
    protected function insert()
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        if ($this->id > -1) {
            throw new Exception('Entity already exists');
        }

        $this->beforeInsert();
        $data = $this->getInsertData();

        $result = $wpdb->insert(
            static::getTableName(),
            $data['data'],
            $data['formats']
        );
        if ($result === false) {
            DupLog::infoTrace('Insert entity fail error: ' . $wpdb->last_error);
            return false;
        }
        $this->id = $wpdb->insert_id;

        if ($this->update() === false) {
            $this->delete();
            return false;
        }
        return $this->id;
    }

    /**
     * Update entity
     *
     * @return bool True on success, or false on error.
     */
    protected function update(): bool
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        if ($this->id < 0) {
            throw new Exception('Entity don\'t exists in database');
        }

        $this->beforeUpdate();
        $data = $this->getUpdateData();

        $result = $wpdb->update(
            static::getTableName(),
            $data['data'],
            ['id' => $this->id],
            $data['formats']
        );

        if ($result === false) {
            DupLog::infoTrace('Update entity fail error: ' . $wpdb->last_error);
            return false;
        }

        return true;
    }

    /**
     * Delete current entity
     *
     * @return bool True on success, or false on error.
     */
    public function delete(): bool
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        if ($this->id < 0) {
            return true;
        }

        if (
            $wpdb->delete(
                static::getTableName(),
                ['id' => $this->id],
                ['%d']
            ) === false
        ) {
            DupLog::infoTrace('Delete entity fail error: ' . $wpdb->last_error);
            return false;
        }

        $this->id = -1;
        return true;
    }

    /**
     * Get where clause for get from database, can be overridden by child classes
     *
     * @return string
     */
    protected static function getWhereClause(): string
    {
        return '1 = 1';
    }

    /**
     * Get Model from database row
     *
     * @param array<string,mixed> $row Database row
     *
     * @return static|false Concrete implementations may return false when the
     *                      row cannot be mapped to this model's hierarchy.
     */
    abstract protected static function getModelFromRow(array $row);

    /**
     * Get ids of current type
     *
     * @param int<0, max>                   $page           current page, if $pageSize is 0 o 1 $pase is the offset
     * @param int<0, max>                   $pageSize       page size, 0 return all entities
     * @param ?callable                     $sortCallback   sort function on items result
     * @param ?callable                     $filterCallback filter on items result
     * @param array{col:string,mode:string} $orderby        query ordder by
     *
     * @return int[]|false return entities list of false on failure
     */
    final protected static function getIdsFromDatabase(
        int $page = 0,
        int $pageSize = 0,
        ?callable $sortCallback = null,
        ?callable $filterCallback = null,
        array $orderby = [
            'col'  => 'id',
            'mode' => 'ASC',
        ]
    ) {
        try {
            /** @var wpdb $wpdb */
            global $wpdb;

            $where    = static::getWhereClause();
            $offset   = $page * max(1, $pageSize);
            $pageSize = ($pageSize ?: PHP_INT_MAX);
            $orderCol = $orderby['col'] ?? 'id';
            $order    = $orderby['mode'] ?? 'ASC';

            $query = $wpdb->prepare(
                "SELECT id FROM `" . static::getTableName(true) . "` WHERE {$where} ORDER BY {$orderCol} {$order} LIMIT %d OFFSET %d",
                $pageSize,
                $offset
            );

            if (($rows = $wpdb->get_results($query, ARRAY_A)) === null) {
                throw new Exception('Get item query fail');
            }

            $ids = [];
            foreach ($rows as $row) {
                $ids[] = (int) $row['id'];
            }

            if (is_callable($filterCallback)) {
                $ids = array_filter($ids, $filterCallback);
            }

            if (is_callable($sortCallback)) {
                usort($ids, $sortCallback);
            } else {
                $ids = array_values($ids);
            }
        } catch (Throwable $e) {
            DupLog::infoTraceException($e);
            return false;
        }

        return $ids;
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
    final protected static function getItemsFromDatabase(
        int $page = 0,
        int $pageSize = 0,
        ?callable $sortCallback = null,
        ?callable $filterCallback = null,
        array $orderby = [
            'col'  => 'id',
            'mode' => 'ASC',
        ]
    ) {
        try {
            /** @var wpdb $wpdb */
            global $wpdb;

            $where    = static::getWhereClause();
            $offset   = $page * max(1, $pageSize);
            $pageSize = ($pageSize ?: PHP_INT_MAX);
            $orderCol = $orderby['col'] ?? 'id';
            $order    = $orderby['mode'] ?? 'ASC';

            $query = $wpdb->prepare(
                "SELECT * FROM `" . static::getTableName(true) . "` WHERE {$where} ORDER BY {$orderCol} {$order} LIMIT %d OFFSET %d",
                $pageSize,
                $offset
            );

            if (($rows = $wpdb->get_results($query, ARRAY_A)) === null) {
                throw new Exception('Get item query fail');
            }

            $instances = [];
            foreach ($rows as $row) {
                $instance = static::getModelFromRow($row);
                if ($instance === false) {
                    continue;
                }
                $instances[] = $instance;
            }

            if (is_callable($filterCallback)) {
                $instances = array_filter($instances, $filterCallback);
            }

            if (is_callable($sortCallback)) {
                usort($instances, $sortCallback);
            } else {
                $instances = array_values($instances);
            }
        } catch (Throwable $e) {
            DupLog::infoTraceException($e);
            return false;
        }

        return $instances;
    }

    /**
     * Count entity items
     *
     * @return int|false
     */
    final protected static function countItemsFromDatabase()
    {
        try {
            /** @var wpdb $wpdb */
            global $wpdb;

            $where = static::getWhereClause();
            $query = "SELECT COUNT(*) FROM `" . static::getTableName(true) . "` WHERE {$where}";

            if (($count = $wpdb->get_var($query)) === null) {
                throw new Exception('Get item query fail');
            }
        } catch (Throwable $e) {
            DupLog::infoTraceException($e);
            return false;
        }

        return (int) $count;
    }

    /**
     * Entity table name
     *
     * @param bool $escape If true apply esc_sql to table name
     *
     * @return string
     */
    abstract public static function getTableName(bool $escape = false): string;

    /**
     * Init entity table
     *
     * @return string[] Strings containing the results of the various update queries.
     */
    abstract public static function initTable(): array;
}
