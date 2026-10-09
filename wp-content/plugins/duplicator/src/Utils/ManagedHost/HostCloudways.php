<?php

namespace Duplicator\Utils\ManagedHost;

use Duplicator\Libs\Snap\SnapUtil;

class HostCloudways implements ManagedHostInterface
{
    /**
     * Get the identifier for this host
     *
     * @return string
     */
    public static function getIdentifier(): string
    {
        return ManagedHostMng::HOST_CLOUDWAYS;
    }

    /**
     * Check if the current host is Cloudways
     *
     * @return bool true if is current host
     */
    public function isHosting(): bool
    {
        ob_start();
        SnapUtil::phpinfo();
        $serverinfo = ob_get_clean();
        return (strpos($serverinfo, "cloudwaysapps") !== false);
    }

    /**
     * Initialize the host
     *
     * @return void
     */
    public function init(): void
    {
    }
}
