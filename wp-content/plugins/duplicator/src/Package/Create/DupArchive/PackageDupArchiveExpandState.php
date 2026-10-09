<?php

namespace Duplicator\Package\Create\DupArchive;

use Duplicator\Models\GlobalEntity;
use Duplicator\Package\DupPackage;
use Duplicator\Libs\DupArchive\Headers\DupArchiveHeader;
use Duplicator\Libs\DupArchive\States\DupArchiveExpandState;
use Duplicator\Package\AbstractPackage;
use Duplicator\Core\Exceptions\DupliException;

/**
 * Dup archive expand state
 */
class PackageDupArchiveExpandState extends DupArchiveExpandState
{
    /** @var AbstractPackage */
    private $package;

    /**
     * Class constructor
     *
     * @param DupArchiveHeader $archiveHeader archive header
     * @param AbstractPackage  $package       package
     */
    public function __construct(DupArchiveHeader $archiveHeader, ?AbstractPackage $package = null)
    {
        if ($package == null) {
            throw new DupliException('Package required');
        }
        $this->package = $package;
        parent::__construct($archiveHeader);
        $global                  = GlobalEntity::getInstance();
        $this->throttleDelayInUs = $global->getMicrosecLoadReduction();
    }

    /**
     * Filter props on json encode
     *
     * @return string[]
     */
    public function __sleep()
    {
        $props = array_keys(get_object_vars($this));
        return array_diff($props, ['package']);
    }

    /**
     * Set Backup
     *
     * @param DupPackage $package packge archive
     *
     * @return void
     */
    public function setPackage(DupPackage $package): void
    {
        $this->package = $package;
    }

    /**
     * Save state functon
     *
     * @return void
     */
    public function save(): void
    {
        $this->package->build_progress->dupExpand = $this;
        $this->package->save();
    }
}
