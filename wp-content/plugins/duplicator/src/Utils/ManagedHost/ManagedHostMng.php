<?php

namespace Duplicator\Utils\ManagedHost;

use Exception;

class ManagedHostMng
{
    const HOST_GODADDY      = 'godaddy';
    const HOST_WPENGINE     = 'wpengine';
    const HOST_CLOUDWAYS    = 'cloudways';
    const HOST_WORDPRESSCOM = 'wordpresscom';
    const HOST_LIQUIDWEB    = 'liquidweb';
    const HOST_PANTHEON     = 'pantheon';
    const HOST_FLYWHEEL     = 'flywheel';

    /** @var ?self */
    protected static $instance;
    /** @var bool */
    private $initialized = false;
    /** @var ManagedHostInterface[] */
    private $customHostings = [];
    /** @var string[] */
    private $activeHostings = [];

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Class constructor
     */
    private function __construct()
    {
        $this->customHostings[HostWPEngine::getIdentifier()]     = new HostWPEngine();
        $this->customHostings[HostCloudways::getIdentifier()]    = new HostCloudways();
        $this->customHostings[HostGoDaddy::getIdentifier()]      = new HostGoDaddy();
        $this->customHostings[HostWordpressCom::getIdentifier()] = new HostWordpressCom();
        $this->customHostings[HostLiquidweb::getIdentifier()]    = new HostLiquidweb();
        $this->customHostings[HostPantheon::getIdentifier()]     = new HostPantheon();
        $this->customHostings[HostFlywheel::getIdentifier()]     = new HostFlywheel();
    }

    /**
     * Initialize custom hostings
     *
     * @return bool
     */
    public function init(): bool
    {
        if ($this->initialized) {
            return true;
        }
        foreach ($this->customHostings as $cHost) {
            if (!($cHost instanceof ManagedHostInterface)) {
                throw new Exception('Host must implement ' . ManagedHostInterface::class);
            }
            if ($cHost->isHosting()) {
                $this->activeHostings[] = $cHost->getIdentifier();
                $cHost->init();
            }
        }
        $this->initialized = true;
        return true;
    }

    /**
     * Return active hostings
     *
     * @return string[]
     */
    public function getActiveHostings()
    {
        return $this->activeHostings;
    }

    /**
     * Returns true if the current site is hosted on identified host
     *
     * @param string $identifier Host identifier
     *
     * @return bool
     */
    public function isHosting($identifier): bool
    {
        return in_array($identifier, $this->activeHostings);
    }

    /**
     * Returns true if the current site is hosted on a managed host
     *
     * @return bool
     */
    public function isManaged(): bool
    {
        if ($this->isHosting(self::HOST_WORDPRESSCOM)) {
            return true;
        }

        if ($this->isHosting(self::HOST_GODADDY)) {
            return true;
        }

        if ($this->isHosting(self::HOST_WPENGINE)) {
            return true;
        }

        if ($this->isHosting(self::HOST_CLOUDWAYS)) {
            return true;
        }

        if ($this->isHosting(self::HOST_LIQUIDWEB)) {
            return true;
        }

        if ($this->isHosting(self::HOST_PANTHEON)) {
            return true;
        }

        if ($this->isHosting(self::HOST_FLYWHEEL)) {
            return true;
        }

        return false;
    }

    /**
     * Get hostg object
     *
     * @param string $identifier Host identifier
     *
     * @return ManagedHostInterface|false
     */
    public function getHosting($identifier)
    {
        if ($this->isHosting($identifier)) {
            return $this->customHostings[$identifier];
        } else {
            return false;
        }
    }
}
