<?php

namespace Duplicator\Package\Create\Scan\Tree;

use Duplicator\Libs\Snap\SnapIO;

class Tree
{
    /**
     * All props must be public for json encode
     *
     * @todo this isn't needed anymore. we could use json serialize
     */
    /**
     *
     * @var TreeNode[]
     */
    protected $treeList = [];

    /**
     * Class contructor
     *
     * @param string|string[]            $rootPaths    root paths
     * @param bool                       $addAllChilds if true add all childs
     * @param null|false|string|string[] $excludeList  if null or false no exclude else exclude list
     */
    public function __construct($rootPaths, $addAllChilds = true, $excludeList = [])
    {
        if (is_null($excludeList) || $excludeList === false) {
            $excludeList = [];
        } elseif (!is_array($excludeList)) {
            $excludeList = [$excludeList];
        }

        if (!is_array($rootPaths)) {
            $rootPaths = [$rootPaths];
        }
        $rootPaths = array_map([SnapIO::class, 'safePathUntrailingslashit'], $rootPaths);
        foreach ($rootPaths as $path) {
            $this->treeList[$path] = new TreeNode($path);
            if ($addAllChilds) {
                $this->treeList[$path]->addAllChilds($excludeList);
            }
        }
    }

    /**
     *
     * @param string              $path full path
     * @param array<string,mixed> $data optiona data associated at node
     *
     * @return bool|TreeNode
     */
    public function addElement($path, $data = [])
    {
        foreach ($this->treeList as $rootPath => $tree) {
            if (strpos($path, trailingslashit($rootPath)) === 0) {
                $newElem = $tree->addChild($path, true, false);
                if ($newElem) {
                    $newElem->data = $data;
                }
                return $newElem;
            }
        }
        return false;
    }

    /**
     * Sort child list with callback function of all trees root nodes
     *
     * @param callable $callback function to call
     *
     * @return void
     */
    public function uasort($callback): void
    {
        foreach ($this->treeList as $tree) {
            $tree->uasort($callback);
        }
    }

    /**
     * traverse tree anche call callback function of all trees root nodes
     *
     * @param callable $callback function to call
     *
     * @return void
     */
    public function treeTraverseCallback($callback): void
    {
        foreach ($this->treeList as $tree) {
            $tree->treeTraverseCallback($callback);
        }
    }

    /**
     *
     * @return TreeNode[]
     */
    public function getTreeList()
    {
        return $this->treeList;
    }
}
