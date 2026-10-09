<?php

namespace Duplicator\Installer\Utils;

use Duplicator\Installer\Core\Security;
use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Installer\Package\InstallerDescriptors;

/**
 * Descriptors Manager class for installer
 *
 * singleton class
 */
final class InstDescMng extends InstallerDescriptors
{
    /** @var ?self */
    private static $instance;

    /**
     * Get instance
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
    public function __construct()
    {
        if (($nameInfo = ArchiveDescriptor::getArchiveNameParts(Security::getInstance()->getArchivePath())) === false) {
            throw new \Exception('PACKAGE ERROR: can\'t read archive name parts');
        }
        parent::__construct($nameInfo['packageHash'], $nameInfo['date']);
    }
}
