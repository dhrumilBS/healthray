<?php

namespace Duplicator\Utils;

use ReflectionClass;

class GroupOptions
{
    /** @var string */
    protected $option = '';
    /** @var string */
    protected $inputGroupPrefix = '';
    /** @var string[] */
    protected $possibleArguments = [];
    /** @var bool */
    protected $enabled = false;
    /** @var string[] */
    protected $arguments = [];

    /**
     * @param string   $option            name of the option parameter
     * @param string   $inputGroupPrefix  Input that will be used for the html output
     * @param bool     $enabled           status of the option
     * @param string[] $possibleArguments possible sub options
     * @param string[] $arguments         active sub option
     */
    public function __construct(
        $option,
        $inputGroupPrefix,
        $enabled,
        $possibleArguments = [],
        $arguments = []
    ) {
        $this->option            = $option;
        $this->inputGroupPrefix  = $inputGroupPrefix;
        $this->possibleArguments = $possibleArguments;
        $this->enabled           = $enabled;
        $this->arguments         = $arguments;
    }

    /**
     *  Retrives all possible Arguments of a group
     *
     * @return string[]
     */
    public function getPossibleArguments()
    {
        return $this->possibleArguments;
    }

    /**
     * Returns back the input option status
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Sets the input option to disabled
     *
     * @return void
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * Sets the input option to enabled
     *
     * @return void
     */
    public function enabled(): void
    {
        $this->enabled = true;
    }

    /**
     * Get's the option name
     *
     * @return string
     */
    public function getOptionName()
    {
        return $this->option;
    }

    /**
     * Checks if the input is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Gets the input field name
     *
     * @return string
     */
    public function getInputName(): string
    {
        return $this->inputGroupPrefix . $this->getOptionName();
    }

    /**
     * Updated the input option status based on the request
     *
     * @return void
     */
    public function update(): void
    {
        if (filter_input(INPUT_POST, $this->getInputName(), FILTER_VALIDATE_BOOLEAN)) {
            $this->enabled();
        } else {
            $this->disable();
        }
    }

    /**
     * Search an option if exists
     *
     * @param self[] $options options list
     * @param string $search  option name to search
     *
     * @return false|int|string return array index or false if option don't exists
     */
    public static function optionExists($options, $search)
    {
        foreach ($options as $index => $val) {
            if ($val->getOptionName() === $search) {
                return $index;
            }
        }

        return false;
    }

    /**
     * Apply options to mysqldump command
     *
     * @param self[] $options Group Options Array
     *
     * @return string
     */
    public static function getShellOptions($options): string
    {
        $resultOptions = [];

        foreach ($options as $option) {
            if (!$option->isEnabled()) {
                continue;
            }

            $resultOptions[] = ' --' . $option->getOptionName();
        }

        return implode(' ', $resultOptions);
    }

    /**
     * Return JSON-serializable option data.
     *
     * @return array{option:string,inputGroupPrefix:string,possibleArguments:string[],enabled:bool,arguments:string[]}
     */
    public function toArray(): array
    {
        return [
            'option'            => $this->option,
            'inputGroupPrefix'  => $this->inputGroupPrefix,
            'possibleArguments' => $this->possibleArguments,
            'enabled'           => $this->enabled,
            'arguments'         => $this->arguments,
        ];
    }

    /**
     * Get init object from data
     *
     * @param mixed[] $data array data
     *
     * @return self
     */
    public static function getObjectFromArray($data)
    {
        $reflect = new ReflectionClass(self::class);
        $obj     = $reflect->newInstanceWithoutConstructor();
        foreach ($reflect->getProperties() as $prop) {
            if (!isset($data[$prop->getName()])) {
                continue;
            }
            if (PHP_VERSION_ID < 80100) {
                $prop->setAccessible(true);
            }
            $prop->setValue($obj, $data[$prop->getName()]);
        }
        return $obj;
    }
}
