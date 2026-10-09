<?php

/**
 * Abstract class to extend in order to use the maximum potentialities of JsonSerialize
 *
 * @package VendorDuplicator\Amk\JsonSerialize
 */
namespace VendorDuplicator\Amk\JsonSerialize;

/**
 * Abstract class to extend in order to use the maximum potentialities of JsonSerialize
 */
abstract class AbstractJsonSerializable extends AbstractJsonSerializeObjData implements \JsonSerializable
{
    /**
     * Prepared json serialized object
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    final public function jsonSerialize()
    {
        return self::objectToJsonData($this, 0, []);
    }
}
