<?php

declare(strict_types=1);

namespace Duplicator\Models;

use Duplicator\Core\Models\AbstractEntity;
use Duplicator\Core\Models\TraitEntitySerializationEncryption;
use Duplicator\Core\Models\TraitGenericModelSingleton;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Libs\Snap\SnapServer;
use Exception;
use Throwable;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;

/**
 * Persistent collection of user-facing fixes
 *
 * @phpstan-import-type FixViewData from Fix
 */
final class FixesEntity extends AbstractEntity
{
    use TraitGenericModelSingleton;
    use TraitEntitySerializationEncryption;

    /** @var array<string, string> Action key to method map */
    private const ACTION_CALLBACKS = [
        Fix::ACTION_UPDATE_GLOBAL  => 'applyGlobalSettings',
        Fix::ACTION_SET_BASIC_AUTH => 'applyBasicAuth',
    ];


    /** @var string[] */
    protected static array $encryptedProperties = [];

    /** @var array<string, Fix> */
    private array $fixes = [];

    /**
     * Class constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array // phpcs:ignore PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__serializeFound
    {
        $data          = JsonSerialize::serializeToData(
            $this,
            JsonSerialize::JSON_SKIP_MAGIC_METHODS | JsonSerialize::JSON_SKIP_CLASS_NAME
        );
        $data['fixes'] = [];
        foreach ($this->fixes as $key => $fix) {
            $data['fixes'][$key] = $fix->toArray();
        }

        return $this->encryptSerializedProperties($data);
    }

    /**
     * @param array<string, mixed> $data Serialized data
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $data        = $this->decryptSerializedProperties($data);
        $this->fixes = [];
        $needsSave   = false;

        if (isset($data['fixes']) && is_array($data['fixes'])) {
            foreach ($data['fixes'] as $fixData) {
                $fix = is_array($fixData) ? Fix::fromArray($fixData) : null;
                if ($fix === null) {
                    $needsSave = true;
                    SnapLog::phpErr('skipped an invalid persisted fix');
                    continue;
                }

                $this->fixes[$fix->getKey()] = $fix;
            }
        } elseif (array_key_exists('fixes', $data)) {
            $needsSave = true;
            SnapLog::phpErr('reset an invalid persisted fixes collection');
        }

        if ($needsSave) {
            $this->saveOnShutdown();
        }

        unset($data['fixes']);
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * @return string Entity type identifier
     */
    public static function getType(): string
    {
        return 'Fixes_Entity';
    }

    /**
     * Add or replace a fix by its stable key.
     *
     * @param Fix $fix Fix to persist
     *
     * @return bool True on success
     */
    public function add(Fix $fix): bool
    {
        $this->fixes[$fix->getKey()] = $fix;
        return $this->save();
    }

    /**
     * Apply actionable fixes and retain notices and failed actions.
     *
     * @param ?string[] $keys Restrict the apply to these fix keys, null for all
     *
     * @return array{
     *     applied:int,
     *     appliedKeys:string[],
     *     failed:string[],
     *     changes:array<string,mixed>,
     *     remaining:int,
     *     actionableRemaining:int
     * }
     */
    public function apply(?array $keys = null): array
    {
        $applied     = 0;
        $appliedKeys = [];
        $failed      = [];
        $changes     = [];

        foreach ($this->fixes as $key => $fix) {
            if (!$fix->isAction()) {
                continue;
            }
            if ($keys !== null && !in_array($key, $keys, true)) {
                continue;
            }

            try {
                $changes = array_merge($changes, $this->execute($fix));
                unset($this->fixes[$key]);
                $appliedKeys[] = $key;
                $applied++;
            } catch (Throwable $e) {
                $failed[] = $e->getMessage();
            }
        }

        if ($applied > 0 && !$this->save()) {
            throw new Exception(__('Unable to save the applied fixes.', 'duplicator'));
        }

        return [
            'applied'             => $applied,
            'appliedKeys'         => $appliedKeys,
            'failed'              => $failed,
            'changes'             => $changes,
            'remaining'           => count($this->fixes),
            'actionableRemaining' => $this->countActionable(),
        ];
    }

    /**
     * Remove every fix.
     *
     * @return bool True on success
     */
    public function clear(): bool
    {
        $this->fixes = [];
        return $this->save();
    }

    /**
     * Attach an Activity Log event to a persisted fix.
     *
     * @param string $key   Stable fix identifier
     * @param int    $logId Activity Log event id
     *
     * @return bool True on success, false when the fix does not exist
     */
    public function setActivityLogId(string $key, int $logId): bool
    {
        if (!isset($this->fixes[$key])) {
            return false;
        }

        $this->fixes[$key]->setActivityLogId($logId);
        return $this->save();
    }

    /**
     * Remove the fixes matching the given keys.
     *
     * @param string[] $keys Stable fix identifiers
     *
     * @return bool True on success
     */
    public function remove(array $keys): bool
    {
        $removed = false;
        foreach ($keys as $key) {
            if (isset($this->fixes[$key])) {
                unset($this->fixes[$key]);
                $removed = true;
            }
        }

        return $removed ? $this->save() : true;
    }

    /**
     * @return bool True if at least one fix exists
     */
    public function hasFixes(): bool
    {
        return count($this->fixes) > 0;
    }

    /**
     * @return bool True if at least one actionable fix exists
     */
    public function hasApplicableFixes(): bool
    {
        return $this->countActionable() > 0;
    }

    /**
     * @return int Number of persisted fixes
     */
    public function count(): int
    {
        return count($this->fixes);
    }

    /**
     * Return display-only data without action keys or payloads.
     *
     * @return array<int, FixViewData>
     */
    public function getViewData(): array
    {
        return array_values($this->getKeyedViewData());
    }

    /**
     * Return display-only data keyed by stable fix identifier.
     *
     * @return array<string, FixViewData>
     */
    public function getKeyedViewData(): array
    {
        return array_map(
            static fn(Fix $fix): array => $fix->getViewData(),
            $this->fixes
        );
    }

    /**
     * Return display-only data grouped by title, each group keyed by stable
     * fix identifier. The empty-title group uses the renderer default title.
     *
     * @return array<string, array<string, FixViewData>>
     */
    public function getViewDataGroupedByTitle(): array
    {
        $groups = [];
        foreach ($this->getKeyedViewData() as $key => $viewData) {
            $groups[$viewData['title']][$key] = $viewData;
        }

        return $groups;
    }

    /**
     * @param Fix $fix Actionable fix
     *
     * @return array<string, mixed> Applied values
     */
    private function execute(Fix $fix): array
    {
        $actionKey = $fix->getActionKey();
        $method    = self::ACTION_CALLBACKS[$actionKey] ?? null;
        $callback  = $method === null ? null : [
            $this,
            $method,
        ];
        if (!is_callable($callback)) {
            throw new Exception('Unknown fix action: ' . $actionKey);
        }

        $result = call_user_func($callback, $fix->getPayload());
        if (!is_array($result)) {
            throw new Exception('Invalid fix action result: ' . $actionKey);
        }

        /** @var array<string, mixed> $result */
        return $result;
    }

    /**
     * @param array<string, mixed> $settings Settings to apply
     *
     * @return array<string, mixed> Applied values
     */
    private function applyGlobalSettings(array $settings): array
    {
        $global  = GlobalEntity::getInstance();
        $changes = [];

        if (array_key_exists(GlobalEntity::PACKAGE_MYSQLDUMP_KEY, $settings)) {
            /** @var bool $value */
            $value = $settings[GlobalEntity::PACKAGE_MYSQLDUMP_KEY];
            if ($global->isMysqldumpEnabled() !== $value) {
                if (!$global->setMysqldumpEnabled($value)) {
                    throw new Exception(__('Unable to save the database dump engine.', 'duplicator'));
                }
                $changes[GlobalEntity::PACKAGE_MYSQLDUMP_KEY] = $value;
            }
            unset($settings[GlobalEntity::PACKAGE_MYSQLDUMP_KEY]);
        }

        if (array_key_exists(GlobalEntity::PACKAGE_MYSQLDUMP_PATH_KEY, $settings)) {
            /** @var string $value */
            $value = $settings[GlobalEntity::PACKAGE_MYSQLDUMP_PATH_KEY];
            if ($global->getMysqldumpPath() !== $value) {
                if (!$global->setMysqldumpPath($value)) {
                    throw new Exception(__('Unable to save the mysqldump path.', 'duplicator'));
                }
                $changes[GlobalEntity::PACKAGE_MYSQLDUMP_PATH_KEY] = $value;
            }
            unset($settings[GlobalEntity::PACKAGE_MYSQLDUMP_PATH_KEY]);
        }

        if (array_key_exists(GlobalEntity::PACKAGE_PHPDUMP_MODE_KEY, $settings)) {
            /** @var int $value */
            $value = $settings[GlobalEntity::PACKAGE_PHPDUMP_MODE_KEY];
            if ($global->getPhpDumpMode() !== $value) {
                if (!$global->setPhpDumpMode($value)) {
                    throw new Exception(__('Unable to save the PHP dump mode.', 'duplicator'));
                }
                $changes[GlobalEntity::PACKAGE_PHPDUMP_MODE_KEY] = $value;
            }
            unset($settings[GlobalEntity::PACKAGE_PHPDUMP_MODE_KEY]);
        }

        if (array_key_exists(GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY, $settings)) {
            /** @var int $value */
            $value = $settings[GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY];
            if ($global->getMysqldumpQueryLimit() !== $value) {
                if (!$global->setMysqldumpQueryLimit($value)) {
                    throw new Exception(__('Unable to save the mysqldump query limit.', 'duplicator'));
                }
                $changes[GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY] = $value;
            }
            unset($settings[GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY]);
        }

        if (array_key_exists(GlobalEntity::ARCHIVE_BUILD_MODE_KEY, $settings)) {
            /** @var int $value */
            $value = $settings[GlobalEntity::ARCHIVE_BUILD_MODE_KEY];
            if ($global->getBuildMode() !== $value) {
                if (!$global->setBuildMode($value)) {
                    throw new Exception(__('Unable to save the archive build mode.', 'duplicator'));
                }
                $changes[GlobalEntity::ARCHIVE_BUILD_MODE_KEY] = $value;
            }
            unset($settings[GlobalEntity::ARCHIVE_BUILD_MODE_KEY]);
        }

        if (array_key_exists(GlobalEntity::MAX_PACKAGE_RUNTIME_IN_MIN_KEY, $settings)) {
            /** @var int $value */
            $value = $settings[GlobalEntity::MAX_PACKAGE_RUNTIME_IN_MIN_KEY];
            if ($global->getMaxPackageRuntime() !== $value) {
                if (!$global->setMaxPackageRuntime($value)) {
                    throw new Exception(__('Unable to save the maximum package runtime.', 'duplicator'));
                }
                $changes[GlobalEntity::MAX_PACKAGE_RUNTIME_IN_MIN_KEY] = $value;
            }
            unset($settings[GlobalEntity::MAX_PACKAGE_RUNTIME_IN_MIN_KEY]);
        }

        if (array_key_exists(GlobalEntity::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY, $settings)) {
            /** @var int $value */
            $value = $settings[GlobalEntity::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY];
            if ($global->getMaxPackageTransferTime() !== $value) {
                if (!$global->setMaxPackageTransferTime($value)) {
                    throw new Exception(__('Unable to save the maximum package transfer time.', 'duplicator'));
                }
                $changes[GlobalEntity::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY] = $value;
            }
            unset($settings[GlobalEntity::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY]);
        }

        if (count($settings) > 0) {
            throw new Exception('Unsupported global setting fix: ' . implode(', ', array_keys($settings)));
        }

        return $changes;
    }

    /**
     * @param array<string, mixed> $payload Unused action payload
     *
     * @return array{override_basic_auth:string,basic_auth_user:string,basic_auth_password:string}
     */
    private function applyBasicAuth(array $payload): array
    {
        $detected = SnapServer::detectBasicAuthCredentials();
        if ($detected === null || $detected['user'] === '' || $detected['password'] === '') {
            throw new Exception(__("Username or password were not set.", 'duplicator'));
        }

        $dynamicGlobal = DynamicGlobalEntity::getInstance();
        $dynamicGlobal->setValString(DynamicGlobalEntity::BASIC_AUTH_MODE_KEY, 'auto');
        $dynamicGlobal->setValString(DynamicGlobalEntity::BASIC_AUTH_USER_KEY, $detected['user']);
        $dynamicGlobal->setValString(DynamicGlobalEntity::BASIC_AUTH_PASSWORD_KEY, $detected['password']);
        $dynamicGlobal->save();

        return [
            'override_basic_auth' => 'auto',
            'basic_auth_user'     => $detected['user'],
            'basic_auth_password' => '**Secure Info**',
        ];
    }

    /**
     * @return int Number of actionable fixes
     */
    private function countActionable(): int
    {
        return count(array_filter(
            $this->fixes,
            static fn(Fix $fix): bool => $fix->isAction()
        ));
    }
}
