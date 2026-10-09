<?php

namespace Duplicator\Utils\CachesPurge;

use Duplicator\Utils\Logging\DupLog;
use Exception;
use Throwable;

class CacheItem
{
    /**
     * name of purge element (usualli plugin name)
     *
     * @var string
     */
    protected $name = '';

    /**
     * check function, returns true if the element is to be purged
     *
     * @var callable|bool
     */
    protected $checkCallback;

    /**
     * Purge cache callback
     *
     * @var callable
     */
    protected $purgeCallback;

    /**
     * Message when cache is purged
     */
    protected string $purgedMessage;

    /**
     * Construnctor
     *
     * @param string        $name          item name
     * @param bool|callable $checkCallback check callback, return true if cache of current item have to removed
     * @param callable      $purgeCallback purge cache callback
     */
    public function __construct($name, $checkCallback, $purgeCallback)
    {
        if (strlen($name) == 0) {
            throw new Exception('name can\'t be empty');
        }
        $this->name = $name;
        if (!is_bool($checkCallback) && !is_callable($checkCallback)) {
            throw new Exception('checkCallback must be boolean or callable');
        }
        $this->checkCallback = $checkCallback;

        /* purge callback may not exist if the referenced plugin is not initialized.
         * That's why the check is performed only if you actually purge the plugin
         */
        $this->purgeCallback = $purgeCallback;
        $this->purgedMessage = sprintf(__('All caches on <b>%s</b> have been purged.', 'duplicator'), $this->name);
    }

    /**
     * overwrite default purged message
     *
     * @param string $message message if item have benn purged
     *
     * @return void
     */
    public function setPurgedMessage(string $message): void
    {
        $this->purgedMessage = $message;
    }

    /**
     * purge caches item
     *
     * @param string $message message if item have benn purged
     *
     * @return bool
     */
    public function purge(&$message): bool
    {
        try {
            if (
                (is_bool($this->checkCallback) && $this->checkCallback) ||
                (is_callable($this->checkCallback) && call_user_func($this->checkCallback) == true)
            ) {
                DupLog::trace('Purge ' . $this->name);
                if (!is_callable($this->purgeCallback)) {
                    throw new Exception('purgeCallback must be callable');
                }
                call_user_func($this->purgeCallback);
                $message = $this->purgedMessage;
            }
            return true;
        } catch (Throwable $e) {
            DupLog::trace('Error purge ' . $this->name . ' message:' . $e->getMessage());
            $message = sprintf(__('Error on caches purge of <b>%s</b>.', 'duplicator'), $this->name);
            return false;
        }
    }
}
