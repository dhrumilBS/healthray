<?php

namespace Duplicator\Package\Archive\Filters;

class ScopeBase
{
    /** @var string[] All internal storage items that we decide to filter */
    public $Core = [];
    //TODO: Enable with Settings UI

    /** @var string[] Global filter items added from settings */
    public array $Global = [];
    /** @var string[] Items when creating a Backup or template */
    public array $Instance = [];
    /** @var string[] Items that are not readable */
    public array $Unreadable = [];
    /** @var string[] Unkonwn item path */
    public array $Unknown = [];
    /** @var int Number of unreadable items */
    private int $unreadableCount = 0;

    /**
     * Filter props on json encode
     *
     * @return string[]
     */
    public function __sleep()
    {
        $props = array_keys(get_object_vars($this));
        return array_diff($props, ['unreadableCount']);
    }

    /**
     * @param string $item A path to an unreadable item
     *
     * @return void
     */
    public function addUnreadableItem($item): void
    {
        $this->unreadableCount++;
        if ($this->unreadableCount <= DUPLICATOR_SCAN_MAX_UNREADABLE_COUNT) {
            $this->Unreadable[] = $item;
        }
    }

    /**
     * @return int returns number of unreadable items
     */
    public function getUnreadableCount(): int
    {
        return $this->unreadableCount;
    }
}
