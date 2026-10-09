<?php

declare(strict_types=1);

namespace Duplicator\Core\Options\Rules;

use Duplicator\Core\Options\Requirements\RequirementDefs;
use Duplicator\Models\GlobalEntity;
use Exception;

/**
 * Database dump engine option: mysqldump shell binary or PHP-code dump.
 */
class DbDumpEngineRule extends AbstractOptionRule
{
    const OPTION_KEY = 'db_dump_engine';

    const VALUE_PHP       = 'php';
    const VALUE_MYSQLDUMP = 'mysqldump';

    /**
     * Unique option key
     *
     * @return string
     */
    public function getKey(): string
    {
        return self::OPTION_KEY;
    }

    /**
     * Human readable option label, as named in the settings UI
     *
     * @return string
     */
    public function getLabel(): string
    {
        return __('Database Dump Engine', 'duplicator');
    }

    /**
     * Human readable label of a single dump engine value, as named in the settings UI
     *
     * @param scalar $value Dump engine, enum self::VALUE_*
     *
     * @return string
     */
    public function getValueLabel($value): string
    {
        switch ($value) {
            case self::VALUE_MYSQLDUMP:
                return __('Mysqldump', 'duplicator');
            case self::VALUE_PHP:
                return __('PHP Code', 'duplicator');
            default:
                return parent::getValueLabel($value);
        }
    }

    /**
     * All the values the option can assume
     *
     * @return string[]
     */
    public function getValues(): array
    {
        return [
            self::VALUE_PHP,
            self::VALUE_MYSQLDUMP,
        ];
    }

    /**
     * The PHP dump is the current install default and the always-available fallback
     *
     * @return string[]
     */
    public function getPreference(): array
    {
        return [
            self::VALUE_PHP,
            self::VALUE_MYSQLDUMP,
        ];
    }

    /**
     * Requirement ids needed by the given dump engine
     *
     * @param scalar                $value     Dump engine, enum self::VALUE_*
     * @param array<string, scalar> $depValues Values of the declared dependencies (none for this rule)
     *
     * @return string[] Requirement ids
     */
    public function requirementsFor($value, array $depValues): array
    {
        if ($value === self::VALUE_MYSQLDUMP) {
            return [RequirementDefs::REQ_MYSQLDUMP_BINARY];
        }

        return [];
    }

    /**
     * The currently stored dump engine
     *
     * @return string enum self::VALUE_*
     */
    public function getCurrentValue()
    {
        return GlobalEntity::getInstance()->isMysqldumpEnabled() ? self::VALUE_MYSQLDUMP : self::VALUE_PHP;
    }

    /**
     * Store and persist the dump engine.
     *
     * @param scalar $value Dump engine, enum self::VALUE_*
     *
     * @return void
     */
    public function setValue($value): void
    {
        $global    = GlobalEntity::getInstance();
        $mysqldump = ($value === self::VALUE_MYSQLDUMP);
        if ($global->isMysqldumpEnabled() === $mysqldump) {
            return;
        }
        if (!$global->setMysqldumpEnabled($mysqldump)) {
            throw new Exception('Unable to save the database dump engine.');
        }
    }
}
