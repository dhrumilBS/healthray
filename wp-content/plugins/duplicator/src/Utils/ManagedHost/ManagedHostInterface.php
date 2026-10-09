<?php

namespace Duplicator\Utils\ManagedHost;

interface ManagedHostInterface
{
    /**
     * return the current host itentifier
     *
     * @return string
     */
    public static function getIdentifier(): string;

    /**
     * @return bool true if is current host
     */
    public function isHosting(): bool;

    /**
     * the init function.
     * is called only if isHosting is true
     *
     * @return void
     */
    public function init(): void;
}
