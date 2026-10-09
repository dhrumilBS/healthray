<?php

namespace Duplicator\Libs\Binary;

/**
 * BinaryIO class to encode and decode length prefixed binary data.
 * Also uses a special flag to indicate optional data.
 */
class BinaryIO
{
    /**
     * The optional flag.
     * Must be multi-byte, otherwise it is too likely to occur in the binary data
     *
     * @var string
     */
    private const OPTIONAL_FLAG = "<~o>";

    /**
     * Encodes the data with the format given in the constructor. The order is important
     *
     * @param BinaryFormat[] $formats The binary format
     * @param mixed          ...$data The data
     *
     * @return string
     */
    public static function encode(array $formats, ...$data): string
    {
        if (isset($data[0]) && is_array($data[0])) {
            $data = $data[0];
        }

        if (count($data) !== count($formats)) {
            throw new \Exception("Data format mismatch. The data and the format don't match.");
        }

        $data   = array_values($data);
        $result = '';
        foreach ($formats as $i => $f) {
            $v = $data[$i];
            if ($f->isOptional() && ($f->getDefault() === $v || $v === null)) {
                $result .= self::OPTIONAL_FLAG;
            } elseif ($f->isVariableLength()) {
                $result .= pack($f->getFormat(), strlen($v));
                $result .= $v;
            } else {
                $result .= pack($f->getFormat(), $v);
            }
        }

        return $result;
    }

    /**
     * Decodes the binary string into the given format
     *
     * @param BinaryFormat[] $formats The binary format
     * @param string         $binary  The binary string
     *
     * @return array<int|string, mixed> The data in associative array format
     */
    public static function decode(array $formats, string $binary): array
    {
        $result = [];
        $offset = 0;
        foreach ($formats as $f) {
            $value = '';
            if ($f->isOptional() && substr($binary, $offset, strlen(self::OPTIONAL_FLAG)) === self::OPTIONAL_FLAG) {
                $value   = $f->getDefault();
                $offset += strlen(self::OPTIONAL_FLAG);
            } elseif ($f->isVariableLength()) {
                if (($unpacked = unpack($f->getFormat(), $binary, $offset)) === false) {
                    throw new \Exception("Error unpacking binary data at offset: $offset with format: " . $f->getFormat());
                }
                $length  = $unpacked[1];
                $offset += $f->getSize();
                $value   = substr($binary, $offset, $length);
                $offset += $length;
            } else {
                if (($unpacked = unpack($f->getFormat(), $binary, $offset)) === false) {
                    throw new \Exception("Error unpacking binary data at offset: $offset with format: " . $f->getFormat());
                }
                $value   = $unpacked[1];
                $offset += $f->getSize();
            }

            if ($f->getLabel() !== '') {
                $result[$f->getLabel()] = $value;
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }
}
