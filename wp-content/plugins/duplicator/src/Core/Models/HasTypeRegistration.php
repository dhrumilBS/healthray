<?php

declare(strict_types=1);

namespace Duplicator\Core\Models;

use Exception;

/**
 * Shared registration API for hierarchy roots that want to resolve the
 * concrete PHP class for a row's `type` column via {@see TypeRegistry}.
 *
 * Adopted by both {@see AbstractEntity} and the package hierarchy so the two
 * stay structurally independent while sharing a single registry.
 *
 * PHP static properties declared inside a trait are duplicated per using
 * class and would produce two disjoint registries — the underlying map
 * therefore lives on {@see TypeRegistry}, not on the trait itself.
 */
trait HasTypeRegistration
{
    /**
     * Return the string stored in the row's `type` column for this class.
     *
     * @return string
     */
    abstract public static function getType(): string;

    /**
     * Register this class under its own type string in {@see TypeRegistry}.
     *
     * Uses `static::` so subclasses register themselves under their own
     * type, not the parent's.
     *
     * @return void
     */
    public static function registerType(): void
    {
        TypeRegistry::register(static::getType(), static::class);
    }

    /**
     * Guard for write paths: ensure the calling class is registered so that
     * no row is persisted with a `type` that no loader can resolve later.
     *
     * @return void
     * @throws Exception When the class is not registered in {@see TypeRegistry}
     */
    protected static function assertRegistered(): void
    {
        if (!TypeRegistry::isRegistered(static::getType())) {
            throw new Exception(static::class . " must be registered in TypeRegistry before save()");
        }
    }
}
