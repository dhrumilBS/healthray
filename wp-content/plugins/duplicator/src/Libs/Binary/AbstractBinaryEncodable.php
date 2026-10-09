<?php

namespace Duplicator\Libs\Binary;

/**
 * Abstract class for binary encodable objects
 */
abstract class AbstractBinaryEncodable
{
    /**
     * Returns the object encoded in binary format as defined by the binary formats.
     *
     * @return string The object data in binary format
     */
    public function toBinary(): string
    {
        return BinaryIO::encode(static::getBinaryFormats(), $this->getBinaryValues());
    }

    /**
     * Returns an instance of the object from the encoded binary data.
     *
     * @param string $binary The binary encoded object
     *
     * @return static
     */
    public static function fromBinary(string $binary)
    {
        $data = BinaryIO::decode(static::getBinaryFormats(), $binary);

        return static::objectFromData($data);
    }

    /**
     * Creates an instance of the object from the decoded binary data.
     * The order of the values is the same as the binary formats.
     *
     * @param array<int|string, mixed> $binaryData The deserialized binary data
     *
     * @return static
     */
    abstract public static function objectFromData(array $binaryData);

    /**
     * Returns the values that will be encoded in array format.
     * Has to be in the same order as the binary formats.
     *
     * @return array<int|string, mixed>
     */
    abstract public function getBinaryValues(): array;

    /**
     * The binary formats to be used for serialization.
     *
     * @return BinaryFormat[]
     */
    abstract public static function getBinaryFormats(): array;
}
