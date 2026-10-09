<?php

namespace Duplicator\Core\Models;

use Duplicator\Libs\Snap\SnapLog;
use Exception;
use ReflectionClass;
use Throwable;
use wpdb;

/**
 * Trait for generic model singleton
 */
trait TraitGenericModelSingleton
{
    /** @var static[] */
    private static $instances = [];

    /**
     * Get instance
     *
     * @return static
     */
    public static function getInstance()
    {
        $class = static::class;
        if (isset(self::$instances[$class])) {
            return self::$instances[$class];
        }

        /** @var wpdb $wpdb */
        global $wpdb;
        $prevSuppress = $wpdb->suppress_errors(true);
        ob_start();
        try {
            $items = static::getItemsFromDatabase();
            if ($items === false) {
                throw new Exception('Failed to load items for ' . $class);
            }
            if (empty($items)) {
                self::$instances[$class] = new static(); // @phpstan-ignore-line Unsafe usage of new static() is false positive
                self::$instances[$class]->firstIstanceInit();
                // I save the instance before initializing the values in case they require ajax calls
                // that would otherwise re-initialize the singletom object
                self::deleteExcessRows(self::$instances[$class]->getId()); // Make sure to delete all duplicate rows
                self::$instances[$class]->save();
            } else {
                if (count($items) > 1) {
                    self::deleteExcessRows($items[0]->getId());
                }
                self::$instances[$class] = $items[0];
            }
        } catch (Throwable $e) {
            self::$instances[$class] = new static(); // @phpstan-ignore-line Unsafe usage of new static() is false positive
            self::logInitFailureIfUnexpected();
        } finally {
            ob_end_clean();
            $wpdb->suppress_errors($prevSuppress);
        }

        return self::$instances[$class];
    }

    /**
     * Discard the cached instance and reload it from the database.
     *
     * getInstance() caches the instance per process and never refreshes it:
     * use this inside lock-protected critical sections to act on the state
     * persisted by other processes instead of a stale copy. References to the
     * previous instance held elsewhere keep pointing to the old object.
     *
     * @return static
     */
    public static function reloadInstance()
    {
        unset(self::$instances[static::class]);
        return static::getInstance();
    }

    /**
     * Log an init failure unless the table does not exist yet (activation runs before dbDelta).
     * Primitive logging only: this runs on the entity singleton load path.
     *
     * @return void
     */
    private static function logInitFailureIfUnexpected(): void
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        $table = self::getTableName(true);
        if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) === $table) {
            SnapLog::phpErr('entity singleton init failed, using defaults');
        }
    }

    /**
     * Trait constructor, if is defnined in class is overrite and this method is not called
     */
    protected function __construct()
    {
    }

    /**
     * This function is called on first istance of singletion object
     * Can be extended and used to set dynamic properties values
     *
     * @return void
     */
    protected function firstIstanceInit()
    {
    }

    /**
     * Delete all row except id is set
     *
     * @param int $id Exclude id, if < 0 delete all rows
     *
     * @return bool True on success, or false on error.
     */
    protected static function deleteExcessRows($id)
    {
        try {
            /** @var wpdb $wpdb */
            global $wpdb;
            $where = static::getWhereClause();
            $query = $wpdb->prepare(
                "DELETE FROM `" . self::getTableName(true) . "` WHERE id != %d AND {$where}",
                $id
            );
            return $wpdb->query($query) !== false;
        } catch (Throwable $e) {
            // Prevent save error on cron events edge cases
            return false;
        }
    }

    /**
     * Delete current entity
     *
     * @return bool True on success, or false on error.
     */
    public function delete(): bool
    {
        throw new Exception('Isn\'t possibile delete singleton entity, use reset to reset values');
    }

    /**
     * Reset entity values
     *
     * @param string[]  $skipProps     the list of props to maintain
     * @param ?callable $setCallback   set callback function ($propName, $propValue): mixed
     * @param ?callable $afterCallback callaback called before save
     *
     * @return bool True on success, or false on error.
     */
    public function reset($skipProps = [], $setCallback = null, $afterCallback = null): bool
    {
        // Clean singleton instance
        $newIstance = new static(); // @phpstan-ignore-line Unsafe usage of new static() is false positive
        $reflect    = new ReflectionClass($newIstance);
        foreach ($reflect->getProperties() as $prop) {
            if ($prop->getName() === 'id') {
                continue;
            }
            if (in_array($prop->getName(), $skipProps)) {
                continue;
            }
            if (PHP_VERSION_ID < 80100) {
                $prop->setAccessible(true);
            }
            $newVal = (
                is_callable($setCallback) ?
                call_user_func($setCallback, $prop->getName(), $prop->getValue($newIstance)) :
                $prop->getValue($newIstance)
            );
            $prop->setValue($this, $newVal);
        }
        if (is_callable($afterCallback)) {
            call_user_func($afterCallback);
        }
        return $this->save();
    }
}
