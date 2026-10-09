<?php

namespace Duplicator\Libs\Binary;

class BinaryFormat
{
    /** @var mixed */
    protected $default;

    /** @var string */
    protected string $label = '';

    /** @var bool */
    protected bool $isOptional = false;

    /** @var bool */
    protected bool $isVariableLength = false;

    /** @var string */
    protected string $format = '';

    /**
     * Constructor
     *
     * @param string $format The format to be used by pack()
     * @param string $label  The label for the unpacked data
     *
     * @return void
     */
    public function __construct(string $format, string $label = '')
    {
        $this->format = $format;
        $this->label  = $label;
    }

    /**
     * Parses an array of complex format
     *
     * @param string $format The pack format
     *
     * @return BinaryFormat[] The array of BinaryFormat
     */
    public static function createFromFormat(string $format): array
    {
        $result  = [];
        $matches = [];
        preg_match_all('/([a-zA-Z]?(?:\d*))/', $format, $matches);
        foreach ($matches[0] as $match) {
            if ($match === '') {
                continue;
            }

            $result[] = new self($match);
        }

        return $result;
    }

    /**
     * Returns the number of arguments the complex formats expects
     *
     * @return int
     */
    public function getFormatLength(): int
    {
        $number = substr($this->format, 1);

        //Non-strict comparison is important, on 5.6.20 substr returns false
        //instead of an empty string.
        return $number == '' ? 1 : (int) $number;
    }

    /**
     * Returns the format string
     *
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Set the value as optional
     *
     * @param mixed $default The defaul value
     *
     * @return self
     */
    public function setOptional($default = null): self
    {
        $this->default    = $default;
        $this->isOptional = true;

        return $this;
    }

    /**
     * Get the default value for optionals
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Whether the value is optional
     *
     * @return bool
     */
    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    /**
     * Set the value as optional
     *
     * @return self
     */
    public function setVariableLength(): self
    {
        $this->isVariableLength = true;

        return $this;
    }

    /**
     * Whether the value is a variable length string
     *
     * @return bool
     */
    public function isVariableLength(): bool
    {
        return $this->isVariableLength;
    }

    /**
     * Returns the mapping between formats and their binary size
     *
     * @return array<string, int<1, max>|float>
     */
    public static function getBinaryLengthMap(): array
    {
        static $lengthMap = null;
        if ($lengthMap !== null) {
            return $lengthMap;
        }

        $lengthMap = [
            'a' => 1, // NUL-padded string (1 byte per character)
            'A' => 1, // SPACE-padded string (1 byte per character)
            'h' => 0.5, // Hex string, low nibble first (0.5 byte per character)
            'H' => 0.5, // Hex string, high nibble first (0.5 byte per character)
            'c' => 1, // Signed char (1 byte)
            'C' => 1, // Unsigned char (1 byte)
            's' => 2, // Signed short (2 bytes, machine byte order)
            'S' => 2, // Unsigned short (2 bytes, machine byte order)
            'n' => 2, // Unsigned short (2 bytes, big-endian)
            'v' => 2, // Unsigned short (2 bytes, little-endian)
            'i' => strlen(pack('i', 1)), // Signed integer (machine dependent size and byte order)
            'I' => strlen(pack('I', 1)), // Unsigned integer (machine dependent size and byte order)
            'l' => 4, // Signed long (4 bytes, machine byte order)
            'L' => 4, // Unsigned long (4 bytes, machine byte order)
            'N' => 4, // Unsigned long (4 bytes, big-endian)
            'V' => 4, // Unsigned long (4 bytes, little-endian)
            'q' => 8, // Signed long long (8 bytes, machine byte order)
            'Q' => 8, // Unsigned long long (8 bytes, machine byte order)
            'J' => 8, // Unsigned long long (8 bytes, big-endian)
            'P' => 8, // Unsigned long long (8 bytes, little-endian)
            'f' => strlen(pack('f', 1.0)), // Float (machine dependent size and byte order)
            'g' => strlen(pack('g', 1.0)), // Float (machine dependent size and little-endian byte order)
            'G' => strlen(pack('G', 1.0)), // Float (machine dependent size and big-endian byte order)
            'd' => strlen(pack('d', 1.0)), // Double (machine dependent size and byte order)
            'e' => strlen(pack('e', 1.0)), // Double (machine dependent size and little-endian byte order)
            'E' => strlen(pack('E', 1.0)), // Double (machine dependent size and big-endian byte order)
            'x' => 1, // NUL byte (1 byte)
            'X' => -1, // Back up one byte
            'Z' => 1, // NUL-padded string (1 byte per character)
            '@' => 0, // NUL fill to absolute position
        ];

        return $lengthMap;
    }

    /**
     * Returns the size of the format. For variable length strings, it's the size of the string length
     *
     * @return int The size
     */
    public function getSize(): int
    {
        return (int) (self::getBinaryLengthMap()[$this->format[0]] * $this->getFormatLength());
    }

    /**
     * Returns the label for the format
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }
}
