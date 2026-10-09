<?php

namespace Duplicator\Libs\Index\Header;

use Exception;

/**
 * IndexHeaderHandler class.
 */
class IndexHeaderHandler
{
    /** @var int */
    const VERSION_SIZE = 3;

    /** @var string */
    const VERSION_0_0_1 = '0.0.1';

    /** @var string */
    const VERSION_0_0_2 = '0.0.2';

    /**
     * Returns a new header object based on the version
     *
     * @param resource $handle  The file handle to the combined index file
     * @param int[]    $listIds The list ids, in case of creating a new index file
     * @param string   $type    The type of the index file
     *
     * @return IndexHeaderInterface The new header object
     */
    public static function getIndexHeader($handle, array $listIds, string $type): IndexHeaderInterface
    {
        if (!is_resource($handle)) {
            throw new Exception('Invalid file handle');
        }

        $version = self::getVersion($handle);
        switch ($version) {
            case self::VERSION_0_0_1:
                if (($binary = fread($handle, IndexHeaderV001::HEADER_SIZE)) === false) {
                    throw new Exception('Error reading index file');
                }

                $header = IndexHeaderV001::fromBinary($binary);
                $header->setHandle($handle);
                return $header;
            case self::VERSION_0_0_2:
                // If file is empty initialize with the latest header
                return new IndexHeader($handle, $listIds, $type);
            default:
                throw new Exception('Unsupported version: ' . $version);
        }
    }

    /**
     * Returns the version of the index file
     *
     * @param resource $handle The file handle to the combined index file
     *
     * @return string The version
     */
    private static function getVersion($handle): string
    {
        // Get file stats to check if file is empty
        $stats = fstat($handle);
        if ($stats['size'] === 0) {
            // If file is empty, consider it as the latest version
            return self::VERSION_0_0_2;
        }

        if (rewind($handle) === false) {
            throw new Exception('Error rewinding index file');
        }

        $binaryVersion = fread($handle, self::VERSION_SIZE);
        if (strlen($binaryVersion) !== self::VERSION_SIZE) {
            throw new Exception('Error reading index file version size: ' . strlen($binaryVersion));
        }

        $version = unpack('C*', $binaryVersion);
        if (count($version) !== self::VERSION_SIZE) {
            throw new Exception('Error unpacking index file version data format: C*');
        }

        return implode('.', $version);
    }
}
