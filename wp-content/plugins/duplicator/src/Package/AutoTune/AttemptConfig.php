<?php

declare(strict_types=1);

namespace Duplicator\Package\AutoTune;

use InvalidArgumentException;
use Throwable;

/**
 * Immutable settings configuration for a single AutoTune attempt
 *
 * @phpstan-type AttemptConfigArrayData array{
 *     label:string,
 *     globalSettings:array<string,mixed>
 * }
 */
final class AttemptConfig
{
    private const DEFAULT_DATA = [
        'label'          => '',
        'globalSettings' => [],
    ];

    private string $label = '';

    /** @var array<string, mixed> GlobalEntity property values */
    private array $globalSettings = [];

    /**
     * @param string               $label          Human-readable label for the UI
     * @param array<string, mixed> $globalSettings GlobalEntity property values
     */
    public function __construct(string $label, array $globalSettings)
    {
        if ($label === '') {
            throw new InvalidArgumentException('Attempt config label can\'t be empty.');
        }

        $this->label          = $label;
        $this->globalSettings = $globalSettings;
    }

    /**
     * @return string Human-readable label for the UI
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return array<string, mixed> GlobalEntity property values
     */
    public function getGlobalSettings(): array
    {
        return $this->globalSettings;
    }

    /**
     * Settings identity independent of key order and label, used to compare a
     * candidate config against the attempt history.
     *
     * @return string
     */
    public function getSignature(): string
    {
        $settings = $this->globalSettings;
        ksort($settings);

        return sha1((string) json_encode($settings));
    }

    /**
     * @param self $other Config to compare against
     *
     * @return bool True when both configs apply the same settings
     */
    public function equals(self $other): bool
    {
        return $this->getSignature() === $other->getSignature();
    }

    /**
     * @return AttemptConfigArrayData
     */
    public function toArray(): array
    {
        return [
            'label'          => $this->label,
            'globalSettings' => $this->globalSettings,
        ];
    }

    /**
     * @param array<string, mixed> $data Config data
     *
     * @return ?self Null if the persisted data is invalid
     */
    public static function fromArray(array $data): ?self
    {
        $data = array_merge(self::DEFAULT_DATA, $data);

        try {
            if (!is_string($data['label']) || !is_array($data['globalSettings'])) {
                return null;
            }

            return new self($data['label'], $data['globalSettings']);
        } catch (Throwable $e) {
            return null;
        }
    }
}
