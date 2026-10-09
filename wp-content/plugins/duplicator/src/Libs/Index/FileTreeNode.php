<?php

namespace Duplicator\Libs\Index;

/**
 * FileTreeNode class
 *
 * A single node in the file tree.
 */
class FileTreeNode
{
    /** @var string */
    private string $name;

    /** @var FileNodeInfo */
    private ?FileNodeInfo $info = null;

    /** @var array<string, FileTreeNode> */
    private array $children = [];

    /**
     * Constructor
     *
     * @param string $name The name of the node
     *
     * @return void
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get the name of the node
     *
     * @return string The name of the node
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Check if the current node has a child with the specified name
     *
     * @param string $name The name to check for
     *
     * @return bool true if the current node has a child with the specified name
     */
    public function hasChild(string $name): bool
    {
        return isset($this->children[$name]);
    }

    /**
     * Get the child node with the specified name
     *
     * @param string $name The name of the child node
     *
     * @return ?FileTreeNode The child node or null
     */
    public function getChild(string $name): ?FileTreeNode
    {
        return $this->children[$name] ?? null;
    }

    /**
     * Set the file node info
     *
     * @param FileNodeInfo $info The file node info
     *
     * @return void
     */
    public function setInfo(FileNodeInfo $info): void
    {
        $this->info = $info;
    }

    /**
     * Get the file node info
     *
     * @return ?FileNodeInfo The file node info or null
     */
    public function getInfo(): ?FileNodeInfo
    {
        return $this->info;
    }

    /**
     * Add a child node
     *
     * @param FileTreeNode $child The child node
     *
     * @return void
     */
    public function addChild(FileTreeNode $child): void
    {
        $this->children[$child->getName()] = $child;
    }

    /**
     * Get all child nodes
     *
     * @return array<string, FileTreeNode> The child nodes
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
