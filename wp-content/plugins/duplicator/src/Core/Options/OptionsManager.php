<?php

declare(strict_types=1);

namespace Duplicator\Core\Options;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Core\Options\Requirements\Availability;
use Duplicator\Core\Options\Requirements\ConfigValidation;
use Duplicator\Core\Options\Requirements\OptionValidationFailure;
use Duplicator\Core\Options\Requirements\Requirement;
use Duplicator\Core\Options\Requirements\RequirementDefs;
use Duplicator\Core\Options\Rules\AbstractOptionRule;
use Duplicator\Core\Options\Rules\ArchiveEngineRule;
use Duplicator\Core\Options\Rules\CompressionRule;
use Duplicator\Core\Options\Rules\DbDumpEngineRule;
use Duplicator\Core\Options\Rules\EncryptionRule;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\PackageBuildOptions;
use Duplicator\Utils\Logging\DupLog;

/**
 * Single entry point of the backup options requirements system.
 *
 * Owns the requirement catalog and the option rules; answers availability
 * queries (defaults resolver, UI, AutoTune) on the current
 * or a hypothetical configuration (configOverride) and applies the resolved
 * defaults/corrections to the rules' own storage.
 *
 * Registrations are static: the core ones happen at getInstance() time, addons
 * call registerRequirement()/registerOption() directly at load time. Duplicate
 * identifiers, missing dependencies and dependency cycles fail at runtime.
 */
class OptionsManager
{
    /**
     * Filter applied once per option availability evaluation. Receives the
     * option Availability (which carries the option key and the per-value
     * outcomes), the effective config and the package being evaluated (null
     * on global evaluations: settings UI, defaults resolver): hooking it, a
     * managed host or an addon can turn specific values unavailable attaching
     * the reason that explains the exclusion both in the UI and in the gate,
     * globally or for the single package (e.g. incremental backups excluding
     * Shell Zip).
     */
    const FILTER_OPTION_AVAILABILITY = 'duplicator_option_availability';

    /** @var ?self */
    private static $instance;

    /** @var array<string, Requirement> requirement id => requirement */
    private array $requirements = [];
    /** @var string[] ids of the requirements that must all pass to build any Backup */
    private array $baselineIds = [];
    /** @var array<string, AbstractOptionRule> option key => rule */
    private array $rules = [];
    /** @var ?string[] lazy cache of the option keys in dependency resolution order */
    private ?array $resolutionOrder = null;

    /**
     * Get the manager instance with the core definitions registered
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->registerCore();
        }
        return self::$instance;
    }

    /**
     * Class constructor. Protected: use getInstance()
     */
    protected function __construct()
    {
    }

    /**
     * Register the core requirement definitions and option rules
     *
     * @return void
     */
    protected function registerCore(): void
    {
        foreach (RequirementDefs::getRequirements() as $requirement) {
            $this->registerRequirement($requirement);
        }
        $this->registerBaseline(RequirementDefs::getBaselineRequirementIds());

        $this->registerOption(new ArchiveEngineRule());
        $this->registerOption(new CompressionRule());
        $this->registerOption(new EncryptionRule());
        $this->registerOption(new DbDumpEngineRule());
    }

    /**
     * Register an environment requirement
     *
     * @param Requirement $requirement The requirement to register
     *
     * @return void
     */
    public function registerRequirement(Requirement $requirement): void
    {
        $requirementId = $requirement->getId();
        if (isset($this->requirements[$requirementId])) {
            throw new DupliException(
                "Duplicate backup requirement registration.\nRequirement: " . $requirementId,
                DupliException::CODE_OPTIONS_INVALID_CONFIGURATION,
                __('The backup requirements configuration is invalid. Check the backup log for details.', 'duplicator')
            );
        }
        $this->requirements[$requirementId] = $requirement;
    }

    /**
     * Flag registered requirements as baseline: they must all pass to build
     * any Backup, whatever the configuration. An id without a matching
     * registered requirement throws when validateConfig() resolves it.
     *
     * @param string[] $requirementIds The requirement ids to flag
     *
     * @return void
     */
    public function registerBaseline(array $requirementIds): void
    {
        foreach ($requirementIds as $requirementId) {
            if (!in_array($requirementId, $this->baselineIds, true)) {
                $this->baselineIds[] = $requirementId;
            }
        }
    }

    /**
     * Register an option rule
     *
     * @param AbstractOptionRule $rule The option rule to register
     *
     * @return void
     */
    public function registerOption(AbstractOptionRule $rule): void
    {
        $optionKey = $rule->getKey();
        if (isset($this->rules[$optionKey])) {
            throw new DupliException(
                "Duplicate backup option registration.\nOption: " . $optionKey,
                DupliException::CODE_OPTIONS_INVALID_CONFIGURATION,
                __('The backup options configuration is invalid. Check the backup log for details.', 'duplicator')
            );
        }
        $this->rules[$optionKey] = $rule;
        $this->resolutionOrder   = null;
    }

    /**
     * Get a registered requirement
     *
     * @param string $requirementId The requirement id
     *
     * @return Requirement
     */
    private function getRequirement(string $requirementId): Requirement
    {
        if (!isset($this->requirements[$requirementId])) {
            throw new DupliException(
                "Unknown backup requirement.\nRequirement: " . $requirementId,
                DupliException::CODE_OPTIONS_INVALID_CONFIGURATION,
                __('The backup requirements configuration is invalid. Check the backup log for details.', 'duplicator')
            );
        }
        return $this->requirements[$requirementId];
    }

    /**
     * Get a registered option rule
     *
     * @param string $optionKey The option key
     *
     * @return AbstractOptionRule
     */
    private function getOption(string $optionKey): AbstractOptionRule
    {
        if (!isset($this->rules[$optionKey])) {
            throw new DupliException(
                "Unknown backup option.\nOption: " . $optionKey,
                DupliException::CODE_OPTIONS_INVALID_CONFIGURATION,
                __('The backup options configuration is invalid. Check the backup log for details.', 'duplicator')
            );
        }
        return $this->rules[$optionKey];
    }

    /**
     * Get the registered option keys in dependency resolution order
     * (parents before children, registration order as tie-break).
     * Public for the tooling that enumerates the gated options (debug UI).
     *
     * @return string[]
     */
    public function getOptionKeys(): array
    {
        if ($this->resolutionOrder === null) {
            $this->resolutionOrder = $this->computeResolutionOrder();
        }
        return $this->resolutionOrder;
    }

    /**
     * Effective configuration: current stored values overridden by configOverride.
     * A key of configOverride that doesn't match a registered option is ignored.
     *
     * @param array<string, scalar> $configOverride Option key => hypothetical value
     *
     * @return array<string, scalar> Option key => effective value
     */
    private function getEffectiveConfig(array $configOverride = []): array
    {
        $config = [];
        foreach ($this->rules as $key => $rule) {
            $config[$key] = array_key_exists($key, $configOverride) ? $configOverride[$key] : $rule->getCurrentValue();
        }
        return $config;
    }

    /**
     * Evaluate the availability of every declared value of an option against
     * the effective configuration, in a single pass. The result passes once
     * through the duplicator_option_availability filter (the exclusion point
     * for managed hosts and addons), which receives the package when the
     * evaluation targets a specific backup.
     *
     * @param string                $optionKey      The option key
     * @param array<string, scalar> $configOverride Option key => hypothetical value
     * @param ?AbstractPackage      $package        The package being evaluated, null on global evaluations
     *
     * @return Availability
     */
    public function availability(string $optionKey, array $configOverride = [], ?AbstractPackage $package = null): Availability
    {
        return $this->evaluateAvailability($optionKey, $configOverride, null, $package);
    }

    /**
     * Evaluate all declared values or only a selected subset. The filtered
     * result always contains every declared value so extension filters keep a
     * stable contract; values outside the subset skip requirement checks.
     *
     * @param string                  $optionKey        The option key
     * @param array<string, scalar>   $configOverride   Option key => hypothetical value
     * @param null|array<int, scalar> $valuesToEvaluate Values whose requirements must be checked, all when null
     * @param ?AbstractPackage        $package          The package being evaluated, null on global evaluations
     *
     * @return Availability
     */
    private function evaluateAvailability(
        string $optionKey,
        array $configOverride = [],
        ?array $valuesToEvaluate = null,
        ?AbstractPackage $package = null
    ): Availability {
        $rule         = $this->getOption($optionKey);
        $config       = $this->getEffectiveConfig($configOverride);
        $availability = new Availability($optionKey, $rule->getValues());

        $depValues = [];
        foreach ($rule->getDependsOn() as $depKey) {
            $depValues[$depKey] = $config[$depKey];
        }

        $valuesToEvaluate ??= $rule->getValues();
        foreach ($valuesToEvaluate as $value) {
            foreach ($rule->requirementsFor($value, $depValues) as $requirementId) {
                $requirement = $this->getRequirement($requirementId);
                if (!$requirement->isMet()) {
                    $availability->addFailedRequirement($value, $requirement);
                }
            }
        }

        $availability = apply_filters(self::FILTER_OPTION_AVAILABILITY, $availability, $config, $package);
        if (!($availability instanceof Availability) || $availability->getOptionKey() !== $optionKey) {
            throw new DupliException(
                "Options availability filter returned an invalid value.\nFilter: " . self::FILTER_OPTION_AVAILABILITY,
                DupliException::CODE_OPTIONS_INVALID_FILTER_RESULT,
                __('A backup options extension returned invalid data. Check the backup log for details.', 'duplicator')
            );
        }

        return $availability;
    }

    /**
     * Validate the whole current configuration (the pre-backup gate): every
     * baseline requirement must pass and every stored option value must be
     * available. A stored value outside the declared ones is reported
     * unavailable with the invalid-value reason, so a broken or legacy stored
     * configuration is flagged instead of silently accepted.
     *
     * @return ConfigValidation
     */
    public function validateConfig(): ConfigValidation
    {
        $validation = new ConfigValidation();
        $this->validateBaseline($validation);

        foreach ($this->getOptionKeys() as $optionKey) {
            $rule    = $this->getOption($optionKey);
            $current = $rule->getCurrentValue();
            $values  = in_array($current, $rule->getValues(), true) ? [$current] : [];

            // The gate needs only the stored value. Settings/default resolution
            // uses availability(), which evaluates every alternative.
            $availability = $this->evaluateAvailability($optionKey, [], $values);
            if (!$availability->isAvailable($current)) {
                $validation->addOptionFailure(new OptionValidationFailure($rule->getLabel(), $current, $availability));
            }
        }

        return $validation;
    }

    /**
     * The one-time gate pass that runs when a backup starts: validate the
     * configuration for the given package and freeze the resolved values
     * into it.
     *
     * The package is forwarded to the availability filter and the options
     * resolve in dependency order against the partial configuration being
     * built:
     * - the stored value is kept when it is available for this package;
     * - a stored value excluded only by the availability filter for this
     *   package (no failed requirement) falls back to the first available
     *   value in preference order (e.g. incremental backups excluding
     *   Shell Zip);
     * - package encryption is read from the package itself and never silently
     *   replaced with the disabled fallback;
     * - a stored value rejected by a failed environment requirement (or outside
     *   the declared values) falls back to the first available value AND the
     *   stored setting is corrected, so an engine that became unavailable
     *   (e.g. a shell binary that exists only in another PHP context) never
     *   blocks the backup;
     * - an option with no available fallback is a gate failure.
     *
     * On success the resolved configuration is frozen into the package (see
     * AbstractPackage::freezeBuildOptions()) and becomes the immutable source
     * of truth of the build. A package already frozen was validated when its
     * backup started and is taken as good without re-running any check.
     * The only write to the rules' storage is the environment-failure
     * correction described above; every other resolution is side-effect free.
     *
     * @param AbstractPackage $package The package the gate runs for
     *
     * @return ConfigValidation
     */
    public function validatePackageConfig(AbstractPackage $package): ConfigValidation
    {
        $validation = new ConfigValidation();

        if ($package->getBuildOptions() !== null) {
            return $validation;
        }

        $this->validateBaseline($validation);

        $config  = [];
        $skipped = [];

        foreach ($this->getOptionKeys() as $optionKey) {
            if ($this->hasSkippedParent($optionKey, $skipped)) {
                $skipped[] = $optionKey;
                continue;
            }

            $rule = $this->getOption($optionKey);

            // Encryption is selected on the package/template, not globally.
            $current = $optionKey === EncryptionRule::OPTION_KEY ?
                $package->Archive->isArchiveEncrypt() :
                $rule->getCurrentValue();

            $evaluationConfig = $config;
            if ($optionKey === EncryptionRule::OPTION_KEY) {
                $evaluationConfig[$optionKey] = $current;
            }

            $availability = $this->availability($optionKey, $evaluationConfig, $package);
            if ($availability->isAvailable($current)) {
                $config[$optionKey] = $current;
                continue;
            }

            // A package-local encryption choice cannot be silently replaced
            // with the rule's disabled fallback.
            $fallback = $optionKey === EncryptionRule::OPTION_KEY ?
                null :
                $this->firstAvailable($optionKey, $config, $package);
            if ($fallback !== null) {
                $isPackageExclusionOnly = in_array($current, $rule->getValues(), true) &&
                    count($availability->getFailedRequirements($current)) === 0;
                if (!$isPackageExclusionOnly) {
                    // Environment requirement failure or invalid stored value:
                    // correct the stored setting too, so the settings reflect
                    // what the builds actually use.
                    $rule->setValue($fallback);
                    DupLog::info(sprintf(
                        'BACKUP OPTION "%1$s" CORRECTED AT BACKUP START: "%2$s" IS NOT AVAILABLE, SWITCHED TO "%3$s"',
                        $rule->getLabel(),
                        $rule->getValueLabel($current),
                        $rule->getValueLabel($fallback)
                    ));
                }
                $config[$optionKey] = $fallback;
                continue;
            }

            $validation->addOptionFailure(new OptionValidationFailure($rule->getLabel(), $current, $availability));
            $skipped[] = $optionKey;
        }

        if ($validation->isValid()) {
            $global = GlobalEntity::getInstance();
            $package->freezeBuildOptions(new PackageBuildOptions(
                (int) $config[ArchiveEngineRule::OPTION_KEY],
                (bool) $config[CompressionRule::OPTION_KEY],
                (string) $config[DbDumpEngineRule::OPTION_KEY],
                $global->getZipArchiveMode(),
                $global->getPhpDumpMode()
            ));
            DupLog::trace(sprintf(
                'OPTIONS: config FROZEN | Backup %d | archive %d | compression %s | db %s | zip mode %d | php dump %d',
                $package->getId(),
                (int) $config[ArchiveEngineRule::OPTION_KEY],
                ((bool) $config[CompressionRule::OPTION_KEY] ? 'on' : 'off'),
                (string) $config[DbDumpEngineRule::OPTION_KEY],
                $global->getZipArchiveMode(),
                $global->getPhpDumpMode()
            ));
        }

        return $validation;
    }

    /**
     * Check every baseline requirement, recording the failures
     *
     * @param ConfigValidation $validation Collects the failed baseline requirements
     *
     * @return void
     */
    private function validateBaseline(ConfigValidation $validation): void
    {
        foreach ($this->baselineIds as $requirementId) {
            $requirement = $this->getRequirement($requirementId);
            if (!$requirement->isMet()) {
                $validation->addFailedBaseline($requirement);
            }
        }
    }

    /**
     * True if the option key is registered in the system. The settings UI
     * treats an unregistered key as always valid, so templates can query any
     * option uniformly and the gating follows the registrations alone.
     *
     * @param string $optionKey The option key
     *
     * @return bool
     */
    public function hasOption(string $optionKey): bool
    {
        return isset($this->rules[$optionKey]);
    }

    /**
     * Human readable label of a registered option
     *
     * @param string $optionKey The option key
     *
     * @return string
     */
    public function getOptionLabel(string $optionKey): string
    {
        return $this->getOption($optionKey)->getLabel();
    }

    /**
     * Human readable label of a single value of a registered option
     *
     * @param string $optionKey The option key
     * @param scalar $value     The option value
     *
     * @return string
     */
    public function getValueLabel(string $optionKey, $value): string
    {
        return $this->getOption($optionKey)->getValueLabel($value);
    }

    /**
     * Declared values of a registered option, without evaluating their
     * availability (enumeration for label maps and tooling)
     *
     * @param string $optionKey The option key
     *
     * @return scalar[]
     */
    public function getOptionValues(string $optionKey): array
    {
        return $this->getOption($optionKey)->getValues();
    }

    /**
     * Declared dependencies of a registered option (parent option keys)
     *
     * @param string $optionKey The option key
     *
     * @return string[]
     */
    public function getOptionDependencies(string $optionKey): array
    {
        return $this->getOption($optionKey)->getDependsOn();
    }

    /**
     * Every combination of the declared dependencies' values of an option, in
     * dependency declaration order. An option without dependencies produces
     * the single empty combination. Shared by the availability matrix and the
     * tooling that reasons per parent combination.
     *
     * @param string $optionKey The option key
     *
     * @return array<int, array<string, scalar>> List of parent option key => value maps
     */
    public function getDependencyCombinations(string $optionKey): array
    {
        $combinations = [[]];
        foreach ($this->getOption($optionKey)->getDependsOn() as $depKey) {
            $expanded = [];
            foreach ($combinations as $combination) {
                foreach ($this->getOption($depKey)->getValues() as $depValue) {
                    $expanded[] = array_merge($combination, [$depKey => $depValue]);
                }
            }
            $combinations = $expanded;
        }
        return $combinations;
    }

    /**
     * First available value of an option in preference order: the resolver
     * fallback for defaults, corrections and the settings UI selection
     *
     * @param string                $optionKey      The option key
     * @param array<string, scalar> $configOverride Option key => hypothetical value
     * @param ?AbstractPackage      $package        The package being evaluated, null on global evaluations
     *
     * @return scalar|null null if no value is available
     */
    public function firstAvailable(string $optionKey, array $configOverride = [], ?AbstractPackage $package = null)
    {
        $availability = $this->availability($optionKey, $configOverride, $package);
        foreach ($this->getOption($optionKey)->getPreference() as $value) {
            if ($availability->isAvailable($value)) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Resolve the default configuration: for every option in dependency order
     * pick the first available value in preference order, evaluated against the
     * partial configuration being built. An option with no available value is
     * left out of the result, together with its children.
     *
     * @return array<string, scalar> Option key => resolved value
     */
    private function resolveDefaults(): array
    {
        $config  = [];
        $skipped = [];

        foreach ($this->getOptionKeys() as $optionKey) {
            if ($this->hasSkippedParent($optionKey, $skipped)) {
                $skipped[] = $optionKey;
                continue;
            }

            $value = $this->firstAvailable($optionKey, $config);
            if ($value === null) {
                $skipped[] = $optionKey;
                continue;
            }
            $config[$optionKey] = $value;
        }

        return $config;
    }

    /**
     * Validate and store a set of new option values in a single pass.
     * Every pair is validated against the effective configuration merged with
     * ALL the submitted values, so children are validated against the new
     * parent values. All-or-nothing: if any value is unavailable nothing is
     * stored and the failures — each binding the submitted value to its
     * reasons — are returned to the caller (a settings save shows them,
     * internal callers treat them as developer errors).
     *
     * @param array<string, scalar> $values Option key => new value
     *
     * @return array<string, OptionValidationFailure> Failing option key => failure, empty when the values are stored
     */
    public function setValues(array $values): array
    {
        $failures = [];
        foreach ($values as $optionKey => $value) {
            $availability = $this->availability($optionKey, $values);
            if (!$availability->isAvailable($value)) {
                $failures[$optionKey] = new OptionValidationFailure($this->getOption($optionKey)->getLabel(), $value, $availability);
            }
        }
        if (count($failures) > 0) {
            return $failures;
        }

        foreach ($values as $optionKey => $value) {
            $this->getOption($optionKey)->setValue($value);
        }
        return [];
    }

    /**
     * Store a set of submitted option values, guaranteeing that only valid
     * values reach the storage: a submitted value that is not available is
     * replaced by the first available value in preference order (the same
     * fallback shown by the settings UI), and the replacement is reported so
     * the save flow can surface it. An option with no available value at all
     * is left untouched and reported too (the gate flags it at backup time).
     * The batch resolves in dependency order, so children are validated
     * against the values actually being stored for their parents.
     *
     * @param array<string, scalar> $values Option key => submitted value
     *
     * @return array<string, OptionValidationFailure> Submitted values not stored as-is (option key => failure)
     */
    public function applyValues(array $values): array
    {
        foreach (array_keys($values) as $optionKey) {
            $this->getOption($optionKey); // Unknown keys are developer errors
        }

        $resolved = [];
        $failures = [];
        foreach ($this->getOptionKeys() as $optionKey) {
            if (!array_key_exists($optionKey, $values)) {
                continue;
            }

            $value        = $values[$optionKey];
            $availability = $this->availability($optionKey, $resolved);
            if (!$availability->isAvailable($value)) {
                $failures[$optionKey] = new OptionValidationFailure($this->getOption($optionKey)->getLabel(), $value, $availability);

                $value = $this->firstAvailable($optionKey, $resolved);
                if ($value === null) {
                    continue;
                }
            }
            $resolved[$optionKey] = $value;
        }

        foreach ($resolved as $optionKey => $value) {
            $this->getOption($optionKey)->setValue($value);
        }

        return $failures;
    }

    /**
     * Availability of every declared value of an option for every combination
     * of its declared dependencies' values, in dependency declaration order.
     * This is the settings UI data source for the dynamic update on parent
     * change: the same evaluation the backend runs, precomputed per
     * combination. An option without dependencies produces the single empty
     * combination key.
     *
     * @param string $optionKey The option key
     *
     * @return array{
     *     dependsOn: string[],
     *     entries: array<string|int, array<string|int, array{available: bool, reasons: string[]}>>
     * } entries: combination key (parent value keys joined by "|") => value key => outcome.
     *   The keys are built as strings (booleans map to "1"/"0", matching the
     *   HTML input values); PHP canonicalizes the numeric ones to int, the
     *   JSON export restores them as strings for the JS consumer.
     */
    public function availabilityMatrix(string $optionKey): array
    {
        $rule    = $this->getOption($optionKey);
        $depKeys = $rule->getDependsOn();

        $combinations = $this->getDependencyCombinations($optionKey);

        $entries = [];
        foreach ($combinations as $combination) {
            $availability = $this->availability($optionKey, $combination);
            $outcomes     = [];
            foreach ($availability->getValues() as $value) {
                $outcomes[self::valueKey($value)] = [
                    'available' => $availability->isAvailable($value),
                    'reasons'   => $availability->getReasons($value),
                ];
            }
            $entries[implode('|', array_map([self::class, 'valueKey'], $combination))] = $outcomes;
        }

        return [
            'dependsOn' => $depKeys,
            'entries'   => $entries,
        ];
    }

    /**
     * Clear the cached check result of the given requirements, forcing the
     * next evaluation to re-run their checks. Needed by the few flows that
     * change the environment a requirement reads within the same request
     * (e.g. the mysqldump custom path saved right before the dump engine).
     *
     * @param string[] $requirementIds The requirement ids to reset
     *
     * @return void
     */
    public function resetRequirementResults(array $requirementIds): void
    {
        foreach ($requirementIds as $requirementId) {
            $this->getRequirement($requirementId)->resetResult();
        }
    }

    /**
     * String form of an option value, stable across PHP/JS: booleans map to
     * "1"/"0" (matching the HTML input values), other scalars are cast.
     * The shared convention of the availability matrix, the UI decorations
     * and the debug tooling.
     *
     * @param scalar $value The option value
     *
     * @return string
     */
    public static function valueKey($value): string
    {
        return is_bool($value) ? (string) (int) $value : (string) $value;
    }

    /**
     * Resolve the default configuration and store it through every resolved
     * option's setValue(). First install and explicit reset only.
     *
     * @return array<string, scalar> The applied configuration
     */
    public function applyDefaults(): array
    {
        $config = $this->resolveDefaults();
        $this->setValues($config);
        return $config;
    }

    /**
     * Re-check the stored values and correct only where needed (plugin update):
     * every current value whose requirements pass is kept, the failing ones are
     * re-resolved via the preference list against the corrected parent values.
     * If the re-resolution finds no available value the stored value stays
     * untouched (no hidden repair, enforcement is the gate) and the children
     * are left untouched too.
     *
     * @return array<string, scalar> The corrected options only (option key => new value)
     */
    public function applyCorrections(): array
    {
        $config      = [];
        $skipped     = [];
        $corrections = [];

        foreach ($this->getOptionKeys() as $optionKey) {
            if ($this->hasSkippedParent($optionKey, $skipped)) {
                $skipped[] = $optionKey;
                continue;
            }

            $rule    = $this->getOption($optionKey);
            $current = $rule->getCurrentValue();
            if ($this->availability($optionKey, $config)->isAvailable($current)) {
                $config[$optionKey] = $current;
                continue;
            }

            $value = $this->firstAvailable($optionKey, $config);
            if ($value === null) {
                $skipped[] = $optionKey;
                continue;
            }
            $rule->setValue($value);
            $config[$optionKey]      = $value;
            $corrections[$optionKey] = $value;
        }

        return $corrections;
    }

    /**
     * True if any declared dependency of the option was skipped by the resolver
     *
     * @param string   $optionKey The option key
     * @param string[] $skipped   The already skipped option keys
     *
     * @return bool
     */
    private function hasSkippedParent(string $optionKey, array $skipped): bool
    {
        foreach ($this->getOption($optionKey)->getDependsOn() as $depKey) {
            if (in_array($depKey, $skipped, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Compute the option keys in dependency order (parents before children,
     * registration order as a stable tie-break). A dependency on an
     * unregistered option or a cycle throws: the unit tests keep the static
     * registrations consistent.
     *
     * @return string[]
     */
    private function computeResolutionOrder(): array
    {
        $order  = [];
        $status = []; // option key => 1 visiting, 2 done

        $visit = function (string $optionKey) use (&$visit, &$order, &$status): void {
            if (isset($status[$optionKey])) {
                if ($status[$optionKey] === 1) {
                    throw new DupliException(
                        "Options dependency cycle detected.\nOption: " . $optionKey,
                        DupliException::CODE_OPTIONS_INVALID_CONFIGURATION,
                        __('The backup options configuration is invalid. Check the backup log for details.', 'duplicator')
                    );
                }
                return;
            }
            $status[$optionKey] = 1;
            foreach ($this->getOption($optionKey)->getDependsOn() as $depKey) {
                $visit($depKey);
            }
            $status[$optionKey] = 2;
            $order[]            = $optionKey;
        };

        foreach (array_keys($this->rules) as $optionKey) {
            $visit($optionKey);
        }

        return $order;
    }
}
