<?php

namespace Duplicator\Package;

use Duplicator\Package\PackageUtils;

/**
 * Class used to store and process all Backup logic
 */
class DupPackage extends AbstractPackage
{
    /**
     * Get backup type
     *
     * @return string
     */
    public static function getType(): string
    {
        return PackageUtils::DEFAULT_BACKUP_TYPE;
    }

    /**
     * Return Backup life
     *
     * @param string $type can be hours,human,timestamp
     *
     * @return int|string Backup life in hours, timestamp or human readable format
     */
    public function getPackageLife($type = 'timestamp')
    {
        $created = strtotime($this->created);
        $current = strtotime(gmdate("Y-m-d H:i:s"));
        $delta   = $current - $created;

        switch ($type) {
            case 'hours':
                return max(0, floor($delta / 60 / 60));
            case 'human':
                return human_time_diff($created, $current);
            case 'timestamp':
            default:
                return $delta;
        }
    }
}
