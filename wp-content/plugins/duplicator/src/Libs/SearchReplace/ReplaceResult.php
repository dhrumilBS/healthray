<?php

declare(strict_types=1);

namespace Duplicator\Libs\SearchReplace;

/**
 * Result of the replacement of a single value
 */
final class ReplaceResult
{
    private string $value;
    private bool $changed;
    private string $error;

    /**
     * Class constructor
     *
     * @param string $value   Resulting value
     * @param bool   $changed True if the value differs from the original one
     * @param string $error   Rejection reason, empty when the value is not rejected
     */
    private function __construct(string $value, bool $changed, string $error)
    {
        $this->value   = $value;
        $this->changed = $changed;
        $this->error   = $error;
    }

    /**
     * Creates the result of a replacement
     *
     * @param string $original Original value
     * @param string $value    Value after the replacement
     *
     * @return self
     */
    public static function replaced(string $original, string $value): self
    {
        return new self($value, $value !== $original, '');
    }

    /**
     * Creates the result of a value left untouched because it cannot be replaced safely
     *
     * @param string $original Original value
     * @param string $error    Rejection reason
     *
     * @return self
     */
    public static function rejected(string $original, string $error): self
    {
        return new self($original, false, $error);
    }

    /**
     * Returns the new value, the original one when unchanged or rejected
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Returns true if the value has been changed
     *
     * @return bool
     */
    public function isChanged(): bool
    {
        return $this->changed;
    }

    /**
     * Returns true if the value cannot be replaced safely and has been left untouched
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->error !== '';
    }

    /**
     * Returns the rejection reason, empty when the value is not rejected
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}
