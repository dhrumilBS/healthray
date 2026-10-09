<?php

namespace Duplicator\Package\Create\Scan\Tree;

use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Core\Exceptions\DupliException;

class TreeNode
{
    const MAX_CHILDS_FOR_FOLDER = 250;
    const MAX_TREE_NODES        = 5000;

    /**
     * All props must be public for json encode
     *
     * @todo this isn't needed anymore. we could use json serialize
     */
    /**
     *
     * @var string unique id l0_l1_l2....
     */
    public $id = '';

    /**
     *
     * @var string parent id l0_l1_...
     */
    public $parentId = '';

    /**
     *
     * @var string file basename
     */
    public $name = '';

    /**
     *
     * @var string full path
     */
    public $fullPath = '';

    /**
     *
     * @var bool is directory
     */
    public $isDir = false;

    /**
     *
     * @var self[] childs nodes
     */
    public $childs = [];

    /**
     *
     * @var array<string,mixed> optiona data associated ad node
     */
    public $data = [];

    /**
     *
     * @var bool true if folder have a childs
     */
    public $haveChildren;

    /**
     *
     * @var bool
     */
    private $traversed = false;

    /**
     *
     * @var int if can't add a child increment exceeede count
     */
    private $nodesExceeded = 0;

    /**
     *
     * @var int
     */
    private $numTreeNodes = 1;

    /**
     *
     * @param string $path      file path
     * @param string $id        current level unique id
     * @param string $parent_id parent id
     */
    public function __construct($path, $id = '0', $parent_id = '')
    {
        $safePath       = SnapIO::safePathUntrailingslashit($path);
        $this->id       = (strlen($parent_id) == 0 ? '' : $parent_id . '_') . $id;
        $this->parentId = $parent_id;
        $this->name     = basename($safePath);
        $this->fullPath = $safePath;
        $this->isDir    = @is_dir($this->fullPath);
        $this->haveChildrenCheck();
    }

    /**
     * create tree tructure until at basename
     *
     * @param string $path         file path
     * @param bool   $fullPath     if true is considered a full path and must be a child of a current node else is a relative path
     * @param bool   $loadTraverse if true, add the files and folders present at the level of each node
     *
     * @return boolean|self if fails terurn false ellse return the leaf child added
     */
    public function addChild($path, $fullPath = true, $loadTraverse = false)
    {
        if (empty($path)) {
            return false;
        }

        $safePath = SnapIO::safePathUntrailingslashit($path);
        if ($fullPath) {
            if (strpos($safePath, trailingslashit($this->fullPath)) !== 0) {
                throw new DupliException('Can\'t add no child on tree; file: "' . $safePath . '" || fullpath: "' . $this->fullPath . '"');
            }
            $childPath = substr($safePath, strlen($this->fullPath));
        } else {
            $childPath = $safePath;
        }

        $tree_list = explode('/', $childPath);
        if (empty($tree_list[0])) {
            array_shift($tree_list);
        }

        if (($child = $this->checkAndAddChild($tree_list[0])) === false) {
            return false;
        }

        if ($loadTraverse) {
            $child->addAllChilds();
        }

        if (count($tree_list) > 1) {
            array_shift($tree_list);
            $nodesBefore         = $child->getNumTreeNodes();
            $result              = $child->addChild(implode('/', $tree_list), false, $loadTraverse);
            $this->numTreeNodes += $child->getNumTreeNodes() - $nodesBefore;
            return $result;
        } else {
            return $child;
        }
    }

    /**
     * If is dir scan all children files and add on childs list
     *
     * @param string[] $excludeList child to add
     *
     * @return void
     */
    public function addAllChilds($excludeList = []): void
    {
        if ($this->traversed === false) {
            $this->traversed = true;

            if ($this->isDir) {
                if ($dh = @opendir($this->fullPath)) {
                    while (($childName = readdir($dh)) !== false) {
                        if ($childName == '.' || $childName == '..' || in_array($childName, $excludeList)) {
                            continue;
                        }

                        $this->checkAndAddChild($childName);
                    }
                    closedir($dh);
                }
            }
        }
    }

    /**
     * check if current dir have children without load nodes
     *
     * @return void
     */
    private function haveChildrenCheck(): void
    {
        if ($this->isDir) {
            $this->haveChildren = false;

            if ($dh = opendir($this->fullPath)) {
                while (!$this->haveChildren && ($file = readdir($dh)) !== false) {
                    $this->haveChildren = $file !== "." && $file !== "..";
                }
                closedir($dh);
            }
        }
    }

    /**
     *
     * @param string $name child name
     *
     * @return false|self if notes exceeded return false
     */
    private function checkAndAddChild(string $name)
    {
        if (!array_key_exists($name, $this->childs)) {
            if ($this->numTreeNodes > self::MAX_TREE_NODES || count($this->childs) >= self::MAX_CHILDS_FOR_FOLDER) {
                $this->nodesExceeded++;
                return false;
            } else {
                $child               = new self($this->fullPath . '/' . $name, (string) count($this->childs), $this->id);
                $this->childs[$name] = $child;
                $this->numTreeNodes++;
            }
        }
        return $this->childs[$name];
    }

    /**
     * sort child list with callback function
     *
     * @param callable $value_compare_func function to call
     *
     * @return void
     */
    public function uasort($value_compare_func): void
    {
        if (!is_callable($value_compare_func)) {
            return;
        }

        foreach ($this->childs as $child) {
            $child->uasort($value_compare_func);
        }

        uasort($this->childs, $value_compare_func);
    }

    /**
     * traverse tree anche call callback function
     *
     * @param callable $callback function to call
     *
     * @return void
     */
    public function treeTraverseCallback($callback): void
    {
        if (!is_callable($callback)) {
            return;
        }

        foreach ($this->childs as $child) {
            $child->treeTraverseCallback($callback);
        }

        call_user_func($callback, $this);
    }

    /**
     * Get the value of nodesExceeded
     *
     * @return int
     */
    public function getNodesExceeded()
    {
        return $this->nodesExceeded;
    }

    /**
     * Get the value of numTreeNodes
     *
     * @return int
     */
    public function getNumTreeNodes()
    {
        return $this->numTreeNodes;
    }
}
