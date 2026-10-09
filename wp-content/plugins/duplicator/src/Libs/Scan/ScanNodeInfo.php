<?php

namespace Duplicator\Package\Create\Scan;
namespace Duplicator\Libs\Scan;

use Duplicator\Libs\Index\FileNodeInfo;
use Duplicator\Libs\Snap\SnapIO;

class ScanNodeInfo extends FileNodeInfo
{
    /** @var bool */
    protected $isCyclicLink = false;

    /**
     * Class constructor
     *
     * @param string $path Path
     */
    public function __construct(string $path)
    {
        $size  = 0;
        $nodes = 1;
        $type  = self::TYPE_UNKNOWN;
        $path  = untrailingslashit(wp_normalize_path($path));
        $mtime = -1;
        $hash  = '';

        if (is_link($path)) {
            if (is_dir($path)) {
                $type = self::TYPE_LINK_DIR;
            } elseif (is_file($path)) {
                $type = self::TYPE_LINK_FILE;
            } else {
                $nodes = 0;
            }
        } elseif (is_file($path)) {
            $type  = self::TYPE_FILE;
            $size  = (int) @filesize($path);
            $mtime = filemtime($path);
        } elseif (is_dir($path)) {
            $type = self::TYPE_DIR;
        } else {
            $type  = self::TYPE_UNKNOWN;
            $nodes = 0;
        }

        parent::__construct($path, $type, $size, $nodes, $mtime, $hash);
    }

    /**
     * Add child from node
     *
     * @param ScanNodeInfo $node Node to add
     *
     * @return void
     */
    public function addChildFromNode(ScanNodeInfo $node): void
    {
        if (!$this->isDir()) {
            return;
        }
        $this->size  += $node->getSize();
        $this->nodes += $node->getNodes();
    }

    /**
     * Get target symlink path
     * If is not a symlink return false
     *
     * @return false|string
     */
    public function getLinkTarget()
    {

        if (!$this->isLink()) {
            return false;
        }

        return SnapIO::readlinkReal($this->path);
    }

    /**
     * Is recursive link
     *
     * @return bool
     */
    public function isCyclicLink()
    {
        return $this->isCyclicLink;
    }

    /**
     * Set is cyclic link
     *
     * @param bool $isCyclicLink Is cyclic link
     *
     * @return void
     */
    public function setIsCycleLink($isCyclicLink): void
    {
        $this->isCyclicLink = ($this->type === self::TYPE_LINK_DIR && $isCyclicLink);
    }

    /**
     * Is unreadable
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        return is_readable($this->path);
    }
}
