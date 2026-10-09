<?php

namespace Duplicator\Libs\Index;

use Duplicator\Libs\Snap\SnapIO;

/**
 * FileTree class
 *
 * This class provides a tree structure for file nodes.
 */
class FileTree
{
    /** @var FileIndexManager */
    protected FileIndexManager $indexManager;

    /** @var FileTreeNode */
    protected ?FileTreeNode $root = null;

    /**
     * Constructor
     *
     * @param FileIndexManager $indexManager The file index manager
     * @param string           $rootName     The name of the root node
     *
     * @return void
     */
    public function __construct(FileIndexManager $indexManager, string $rootName = 'root')
    {
        $this->indexManager = $indexManager;
        $this->root         = new FileTreeNode($rootName);
        $this->init();
    }

    /**
     * Get the file tree node by path
     *
     * @param string $path The path to the node
     *
     * @return ?FileTreeNode The file tree node or null
     */
    public function getNodeByPath(string $path): ?FileTreeNode
    {
        $path = $this->normalizePath($path);
        if ($path === $this->root->getName()) {
            return $this->root;
        }

        $current    = $this->root;
        $components = explode('/', trim($path, '/'));
        $depth      = count($components);

        foreach ($components as $i => $component) {
            if ($i === 0) {
                continue; // Skip the root name
            }

            if ($current->hasChild($component)) {
                $current = $current->getChild($component);
            } else {
                return null;
            }

            if ($i === $depth - 1) {
                return $current;
            }
        }

        return null;
    }

    /**
     * Get the root path
     *
     * @return void
     */
    private function init(): void
    {
        // Process directories first
        foreach ($this->indexManager->iterate(FileIndexManager::LIST_TYPE_DIRS) as $node) {
            $this->addNodeToTree($node);
        }

        // Then process files
        foreach ($this->indexManager->iterate(FileIndexManager::LIST_TYPE_FILES) as $node) {
            $this->addNodeToTree($node);
        }
    }

    /**
     * Add a node to the tree structure
     *
     * @param FileNodeInfo $node The node to add
     *
     * @return void
     */
    private function addNodeToTree(FileNodeInfo $node): void
    {
        $current    = $this->root;
        $path       = $this->normalizePath($node->getPath());
        $components = explode('/', trim($path, '/'));
        $depth      = count($components);

        if ($path === $this->root->getName()) {
            $current->setInfo($node);
            return;
        }

        // root/wp-content/uploads/test.php
        foreach ($components as $i => $component) {
            if ($i === 0) {
                continue; // Skip the root name
            }

            if (!$current->hasChild($component)) {
                $current->addChild(new FileTreeNode($component));
            }

            if (($current = $current->getChild($component)) === null) {
                throw new \Exception("Node not found: $component");
            }

            if ($i === $depth - 1) {
                $current->setInfo($node);
            }
        }
    }

    /**
     * Normalize the path to ensure it has a root name
     *
     * @param string $path The path to normalize
     *
     * @return string Normalized path with root name
     */
    private function normalizePath(string $path): string
    {
        $rootName = $this->root->getName();
        $path     = trim($path, '/');
        if ($path === '') {
            return $rootName;
        }

        return SnapIO::trailingslashit($rootName) . $path;
    }
}
