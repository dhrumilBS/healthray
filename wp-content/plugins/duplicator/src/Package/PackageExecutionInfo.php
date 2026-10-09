<?php

declare(strict_types=1);

namespace Duplicator\Package;

use UnexpectedValueException;

/**
 * Immutable classification of the context in which a Backup was created.
 */
final class PackageExecutionInfo
{
    /** @var string */
    private string $type = '';
    /** @var ?string */
    private ?string $source = null;
    /** @var ?string */
    private ?string $reason = null;

    /**
     * Class constructor
     *
     * @param string  $type   Execution type
     * @param ?string $source Detail about the creation context
     * @param ?string $reason Reason the Backup was created
     */
    public function __construct(string $type, ?string $source = null, ?string $reason = null)
    {
        if (trim($type) === '') {
            throw new UnexpectedValueException('Invalid package execution type');
        }

        $this->type   = $type;
        $this->source = ($source !== null && strlen($source) > 0) ? $source : null;
        $this->reason = ($reason !== null && strlen($reason) > 0) ? $reason : null;
    }

    /**
     * Execution type
     *
     * @return string
     */
    public function getType(): string
    {
        if (trim($this->type) === '') {
            throw new UnexpectedValueException('Invalid package execution type');
        }

        return $this->type;
    }

    /**
     * Detail about the creation context
     *
     * @return ?string
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * Reason the Backup was created
     *
     * @return ?string
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }
}
