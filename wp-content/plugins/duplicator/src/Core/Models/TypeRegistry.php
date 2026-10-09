<?php

declare(strict_types=1);

namespace Duplicator\Core\Models;

use Exception;

/**
 * Process-global registry that maps entity/package `type` strings to PHP classes.
 *
 * Used by both the entity hierarchy (AbstractEntity) and the package hierarchy
 * (AbstractPackage) so that a row's `type` column can be resolved to the concrete
 * class that should hydrate it, without either hierarchy needing to know about
 * the other.
 */
final class TypeRegistry
{
    /** @var array<string, class-string> */
    private static array $map = [];

    /**
     * Associate a type string with a PHP class.
     *
     * Idempotent: re-registering the same (type, class) pair is a no-op.
     * Throws when the same type is re-registered under a different class —
     * silent reassignment would mask a bug in the caller's registration wiring.
     *
     * @param string       $type  Value stored in the `type` column
     * @param class-string $class Class that hydrates rows of that type
     *
     * @return void
     * @throws Exception When the type is already mapped to a different class
     */
    public static function register(string $type, string $class): void
    {
        if (isset(self::$map[$type])) {
            if (self::$map[$type] === $class) {
                return;
            }

            throw new Exception(
                "Type '{$type}' already registered as " . self::$map[$type] . ", cannot re-register as {$class}"
            );
        }

        self::$map[$type] = $class;
    }

    /**
     * Return the class registered for a type, or null if the type is unknown.
     *
     * @param string $type Value stored in the `type` column
     *
     * @return class-string|null
     */
    public static function resolve(string $type): ?string
    {
        return self::$map[$type] ?? null;
    }

    /**
     * Whether a type has a registered class.
     *
     * @param string $type Value stored in the `type` column
     *
     * @return bool
     */
    public static function isRegistered(string $type): bool
    {
        return isset(self::$map[$type]);
    }
}
