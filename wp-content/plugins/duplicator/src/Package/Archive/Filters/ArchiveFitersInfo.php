<?php

namespace Duplicator\Package\Archive\Filters;

use Duplicator\Package\Create\Scan\Tree\Tree;
use ReflectionClass;

class ArchiveFitersInfo
{
    /** @var ?ScopeDirectory Contains all folder filter info */
    public $Dirs;
    /** @var ?ScopeFile Contains all folder filter info */
    public $Files;
    /** @var ?ScopeBase Contains all folder filter info */
    public $Exts;
    /** @var null|array<string,mixed>|Tree tree size structure for client jstree */
    public $TreeSize;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->reset(true);
    }

    /**
     * Clone
     *
     * @return void
     */
    public function __clone()
    {
        if (is_object($this->Dirs)) {
            $this->Dirs = clone $this->Dirs;
        }
        if (is_object($this->Files)) {
            $this->Files = clone $this->Files;
        }
        if (is_object($this->Exts)) {
            $this->Exts = clone $this->Exts;
        }
        if (is_object($this->TreeSize)) {
            $this->TreeSize = clone $this->TreeSize;
        }
    }

    /**
     * reset and clean all object
     *
     * @param bool $initTreeObjs if true then init tree size object
     *
     * @return void
     */
    public function reset(bool $initTreeObjs = false): void
    {
        $exclude = [
            "Unreadable",
            "Instance",
        ];
        if (is_null($this->Dirs)) {
            $this->Dirs = new ScopeDirectory();
        } else {
            $this->resetMember($this->Dirs, $exclude);
        }

        if (is_null($this->Files)) {
            $this->Files = new ScopeFile();
        } else {
            $this->resetMember($this->Files, $exclude);
        }

        $this->Exts     = new ScopeBase();
        $this->TreeSize = $initTreeObjs ? new Tree(ABSPATH, false) : null;
    }

    /**
     * Resets all properties of $member to their default values except the ones in $exclude
     *
     * @param object   $member  Object to reset
     * @param string[] $exclude Properties to exclude from resetting
     *
     * @return void
     */
    private function resetMember(object $member, array $exclude = []): void
    {
        $refClass = new ReflectionClass($member);
        $defaults = $refClass->getDefaultProperties();
        foreach ($member as $key => $value) {
            if (!in_array($key, $exclude)) {
                $member->$key = $defaults[$key] ?? null;
            }
        }
    }
}
