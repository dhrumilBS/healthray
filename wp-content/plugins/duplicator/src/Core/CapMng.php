<?php

declare(strict_types=1);

namespace Duplicator\Core;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Libs\Snap\SnapLog;
use Exception;
use Throwable;

/**
 * Duplicator Capabilites
 */
class CapMng
{
    const OPTION_KEY         = 'dupli_opt_capabilities';
    const CAP_PREFIX         = 'duplicator_';
    const CAP_BASIC          = self::CAP_PREFIX . 'basic';
    const CAP_CREATE         = self::CAP_PREFIX . 'create';
    const CAP_STORAGE        = self::CAP_PREFIX . 'storage';
    const CAP_EXPORT         = self::CAP_PREFIX . 'export';
    const CAP_BACKUP_RESTORE = self::CAP_PREFIX . 'backup_restore';
    const CAP_SETTINGS       = self::CAP_PREFIX . 'settings';

    const ROLE_SUPERADMIN = 'dup_role_superadmin';

    /** @var ?self */
    private static $instance;

    /** @var array<string, array{roles: string[], users: int[]}> */
    private $capabilities = [];

    /** @var bool */
    private $mustBeNormalized = false;

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Class contructor
     */
    private function __construct()
    {
        if (($cap = get_option(self::OPTION_KEY)) == false) {
            // Never persist here: on legacy upgrades the stored capabilities are renamed by a
            // migration that runs after this point. First persistence is done by the upgrade
            // flow (UpgradeFunctions::initCapabilities) once the migrations have run.
            $this->capabilities = self::getDefaultCaps();
        } else {
            $this->capabilities = $cap;
        }
        // Normalization is deferred to hookNormalizeCapabilities so addons can register their capability filters first
        $this->mustBeNormalized = true;
    }

    /**
     * Normalize capabilities once all addons have registered their capability filters.
     * Attached to the duplicator_addons_loaded hook as its last callback.
     *
     * @return void
     */
    public static function hookNormalizeCapabilities(): void
    {
        $instance = self::getInstance();
        if ($instance->mustBeNormalized) {
            $instance->mustBeNormalized = false;
            $instance->normalizeCapabilities();
        }
    }

    /**
     * Ensure all required capability keys exist with valid structure
     * and remove any unknown capabilities
     *
     * @return void
     */
    private function normalizeCapabilities(): void
    {
        $defaultCaps = self::getDefaultCaps();
        $updated     = false;

        // Remove unknown capabilities
        foreach (array_keys($this->capabilities) as $cap) {
            if (!isset($defaultCaps[$cap])) {
                unset($this->capabilities[$cap]);
                $updated = true;
            }
        }

        // Add missing or fix invalid capabilities
        foreach ($defaultCaps as $cap => $default) {
            if (
                !isset($this->capabilities[$cap]) ||
                !is_array($this->capabilities[$cap]['roles'] ?? null) ||
                !is_array($this->capabilities[$cap]['users'] ?? null)
            ) {
                $this->capabilities[$cap] = $default;
                $updated                  = true;
            }
        }

        if ($updated) {
            // Check if WordPress user functions are available
            // During early plugin loading (e.g., updates), get_user_by may not exist yet
            if (!function_exists('get_user_by')) {
                // Defer save to when WordPress is fully loaded
                add_action('init', function (): void {
                    $this->save();
                }, 1);
            } else {
                $this->save();
            }
        }
    }

    /**
     * Save capabilities
     *
     * @return bool true if success false otherwise
     */
    private function save(): bool
    {
        $roles         = wp_roles();
        $origUseDb     = $roles->use_db;
        $roles->use_db = false;
        $updateRoles   = false;

        foreach ($this->capabilities as $cap => $data) {
            foreach ($data['roles'] as $role) {
                $updateRoles = true;
                $roles->add_cap($role, $cap);
            }
            foreach ($data['users'] as $user) {
                $user = get_user_by('id', $user);
                if ($user) {
                    $user->add_cap($cap);
                }
            }
        }
        $roles->use_db = $origUseDb;

        // This logic was created to perform one and only one role saving
        if ($updateRoles) {
            update_option($roles->role_key, $roles->roles);
        }
        update_option(self::OPTION_KEY, $this->capabilities);
        return true;
    }

    /**
     * Update capabilities, Only the capabilities in the list are overwritten.
     * On any failure the default capabilities are restored, so the site is never
     * left without capabilities.
     *
     * @param array<string, array{roles: string[], users: int[]}> $capabilities capabilities
     *
     * @return bool true if success false otherwise
     */
    public function update($capabilities): bool
    {
        // user can must be check before capabitilies update
        $protectedCaps = self::getProtectedCaps();
        $userCanCaps   = [];
        foreach ($protectedCaps as $cap) {
            $userCanCaps[$cap] = self::realCanCheck($cap, false);
        }

        try {
            return $this->applyUpdate($capabilities, $protectedCaps, $userCanCaps);
        } catch (Throwable $e) {
            DupLog::trace('Capabilities update failed, restoring defaults: ' . $e->getMessage());
            $this->reset();
            return false;
        }
    }

    /**
     * Apply the capabilities update and persist it
     *
     * @param array<string, array{roles: string[], users: int[]}> $capabilities  capabilities
     * @param string[]                                            $protectedCaps protected capability keys
     * @param array<string, bool>                                 $userCanCaps   current user check per protected cap
     *
     * @return bool true if success false otherwise
     */
    private function applyUpdate(array $capabilities, array $protectedCaps, array $userCanCaps): bool
    {
        $this->removeAll();

        $selectableRoles = array_keys(self::getSelectableRoles());

        foreach ($capabilities as $cap => $data) {
            if (!isset($this->capabilities[$cap])) {
                continue;
            }

            if (in_array($cap, $protectedCaps, true) && empty($userCanCaps[$cap])) {
                // Don't edit a protected cap if user can't edit it
                continue;
            }

            $this->capabilities[$cap] = [
                'roles' => [],
                'users' => [],
            ];

            foreach ($data['roles'] as $role) {
                if (!in_array($role, $selectableRoles)) {
                    continue;
                }
                $this->capabilities[$cap]['roles'][] = $role;
            }

            /** @var array{roles: string[], users: int[]} $filteredCap */
            $filteredCap              = apply_filters(
                'duplicator_capability_update_users',
                $this->capabilities[$cap],
                $data,
                $cap
            );
            $this->capabilities[$cap] = $filteredCap;
        }

        $this->addCapatibiliesDependecies();

        foreach ($protectedCaps as $cap) {
            if (!empty($userCanCaps[$cap]) && isset($this->capabilities[$cap])) {
                $this->capabilities[$cap] = $this->addCurrentUserRole($this->capabilities[$cap]);
            }
        }

        // The second time to be sure to add the user roles
        $this->addCapatibiliesDependecies();

        return $this->save();
    }

    /**
     * Get protected capabilities — capabilities that must remain assigned to the current user
     * during update() if they already have them, so an admin can't accidentally lock themselves out.
     *
     * @return string[]
     */
    public static function getProtectedCaps(): array
    {
        $protected = [self::CAP_SETTINGS];

        /**
         * Filter to register additional protected capabilities. Addons that introduce sensitive caps
         * hook here to preserve user access during cap updates.
         *
         * @param string[] $protected Protected capability keys
         */
        return (array) apply_filters('duplicator_capabilities_protected', $protected);
    }

    /**
     * Update capabitibiles after migration
     *
     * @return bool true if success false otherwise
     */
    public function migrationUpdate()
    {
        $update = false;
        if (!is_multisite()) {
            foreach ($this->capabilities as $cap => $data) {
                if (in_array(self::ROLE_SUPERADMIN, $data['roles'])) {
                    $newRoles   = array_values(array_diff($data['roles'], [self::ROLE_SUPERADMIN]));
                    $newRoles[] = 'administrator';

                    $this->capabilities[$cap]['roles'] = $newRoles;
                    $update                            = true;
                }
            }
        }

        if ($update) {
            return $this->update($this->capabilities);
        }

        return true;
    }

    /**
     * Add capatbilies dependecies follow parents
     *
     * @return void
     */
    protected function addCapatibiliesDependecies()
    {
        $cInfo = self::getCapsInfo();

        foreach ($cInfo as $cap => $capInfo) {
            $roles     = $this->capabilities[$cap]['roles'];
            $users     = $this->capabilities[$cap]['users'];
            $parentCap = $capInfo['parent'];
            while ($parentCap != '') {
                $this->capabilities[$parentCap]['roles'] = array_values(array_unique(array_merge($this->capabilities[$parentCap]['roles'], $roles)));
                $this->capabilities[$parentCap]['users'] = array_values(array_unique(array_merge($this->capabilities[$parentCap]['users'], $users)));
                $parentCap = $cInfo[$parentCap]['parent'];
            }
        }
    }

    /**
     * Returns capabilities with the current user permissions to prevent the user from blocking himself by mistake.
     *
     * Core always adds the current user's role. Addons can hook to use user-level assignment instead.
     *
     * @param array{roles: string[], users: int[]} $capData roles or users
     *
     * @return array{roles: string[], users: int[]}
     */
    protected function addCurrentUserRole(array $capData): array
    {
        $user = wp_get_current_user();
        if (is_multisite() && is_super_admin() && in_array(self::ROLE_SUPERADMIN, $capData['roles'])) {
            return $capData;
        }

        if (count(array_intersect($capData['roles'], (array) $user->roles)) > 0) {
            return $capData;
        }

        if (in_array($user->ID, $capData['users'])) {
            return $capData;
        }

        /**
         * Filter to customize how the current user is ensured to keep capabilities.
         * Core adds the user's role. Addons can override to add user ID instead.
         *
         * @param array{roles: string[], users: int[]} $capData  Capability data
         * @param \WP_User                             $user     Current user
         */
        $result = apply_filters('duplicator_add_current_user_role', $capData, $user);

        // If an addon handled the assignment, return its result
        if ($result !== $capData) {
            return $result;
        }

        // Default core behavior: add by role
        $capData['roles'] = array_merge($capData['roles'], (array) $user->roles);
        if (is_multisite() && is_super_admin()) {
            $capData['roles'][] = self::ROLE_SUPERADMIN;
        }
        $capData['roles'] = array_values(array_unique($capData['roles']));

        return $capData;
    }

    /**
     * Get capability roles
     *
     * @param string $cap capability
     *
     * @return string[]
     */
    public function getCapRoles($cap)
    {
        if (!isset($this->capabilities[$cap])) {
            return [];
        }
        return $this->capabilities[$cap]['roles'];
    }

    /**
     * Get capability users
     *
     * @param string $cap capability
     *
     * @return int[]
     */
    public function getCapUsers($cap)
    {
        if (!isset($this->capabilities[$cap])) {
            return [];
        }
        return $this->capabilities[$cap]['users'];
    }

    /**
     * Get all current capabilities
     *
     * @return array<string, array{roles: string[], users: int[]}>
     */
    public function getAllCapabilities(): array
    {
        return $this->capabilities;
    }

    /**
     * Check if capabilities are the default ones
     *
     * @return bool true if default false otherwise
     */
    public function isDefault(): bool
    {
        return $this->capabilities == self::getDefaultCaps();
    }

    /**
     * Check if cababilitise have users capabilities set
     *
     * @return bool true if users capabilities are set false otherwise
     */
    public function hasUsersCapabilities(): bool
    {
        foreach ($this->capabilities as $cap => $data) {
            if (count($data['users']) > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reset default capabilities
     *
     * NOTE: This method updates the WP_Roles singleton in-memory and persists to DB,
     * but does NOT refresh the current WP_User object. The current user's allcaps
     * remain stale (reflecting the state after removeAll removed them).
     * Callers that need the current user to have the updated capabilities must
     * reload the user manually (e.g. wp_set_current_user(0) + wp_set_current_user($id),
     * or add caps directly to the user object).
     *
     * @return void
     */
    public function reset(): void
    {
        $this->removeAll();
        $this->capabilities = self::getDefaultCaps();
        $this->save();
    }

    /**
     * Remoe all capabilities
     *
     * @return bool true on success false otherwise
     */
    private function removeAll()
    {
        $roles     = wp_roles();
        $origUseDb = $roles->use_db;

        try {
            $roles->use_db = false;

            foreach ($this->capabilities as $cap => $data) {
                foreach ($data['roles'] as $role) {
                    $wpRole = get_role($role);
                    if ($wpRole) {
                        $wpRole->remove_cap($cap);
                    }
                }
                foreach ($data['users'] as $user) {
                    $user = get_user_by('id', $user);
                    if ($user) {
                        $user->remove_cap($cap);
                    }
                }
            }

            update_option($roles->role_key, $roles->roles);

            return delete_option(self::OPTION_KEY);
        } catch (Throwable $e) {
            DupLog::trace('Capabilities removeAll failed: ' . $e->getMessage());
            return false;
        } finally {
            $roles->use_db = $origUseDb;
        }
    }

    /**
     * Capabilities hard reset, check all users and roles and remove all capabilities
     *
     * @return bool
     */
    public function hardReset()
    {
        $roles     = null;
        $origUseDb = null;

        try {
            $ids     = get_users(['fields' => 'ID']);
            $capList = self::getCapsList();

            foreach ($ids as $id) {
                $user = get_user_by('id', $id);
                if (!$user) {
                    continue;
                }
                foreach ($capList as $cap) {
                    $user->remove_cap($cap);
                }
            }

            $roles         = wp_roles();
            $origUseDb     = $roles->use_db;
            $roles->use_db = false;

            foreach (self::getEditableRoles() as $roleName => $info) {
                $role = get_role($roleName);
                if ($role === null) {
                    continue;
                }
                foreach ($capList as $cap) {
                    $role->remove_cap($cap);
                }
            }

            update_option($roles->role_key, $roles->roles);

            delete_option(self::OPTION_KEY);
            $this->capabilities = self::getDefaultCaps();
            return $this->save();
        } catch (Throwable $e) {
            DupLog::trace('Capabilites hard reset failed: ' . $e->getMessage());
        } finally {
            if ($roles !== null) {
                $roles->use_db = $origUseDb;
            }
        }

        return false;
    }

    /**
     * Check if current user have the capability
     *
     * @param string $cap  capability
     * @param bool   $thow throw exception if the user don't have the capability
     *
     * @return bool return true if the user have the capability or throw an exception
     */
    protected static function realCanCheck($cap, $thow = true): bool
    {
        /**
         * @var string[] $super_admins (array) An array of user IDs that should be granted super admin privileges (multisite).
         *                              This global is only set by the site owner (e.g., in wp-config.php),
         *                              and contains an array of IDs of users who should have super admin privileges.
         *                              If set it will override the list of super admins in the database.
         * @see https://codex.wordpress.org/Global_Variables
         */
        global $super_admins;
        $originalSuperAdmins = $super_admins;
        $restoreSuperAdmins  = false;

        try {
            $user = wp_get_current_user();

            if (strpos($cap, self::CAP_PREFIX) === 0 && is_multisite()) {
                if (!is_super_admin()) {
                    throw new Exception('User is not super admin');
                }

                $capRoles = self::getInstance()->capabilities[$cap]['roles'] ?? [];
                if (!in_array(self::ROLE_SUPERADMIN, $capRoles)) {
                    // The default super_admin users have all the capabilities so
                    // it temporarily removes the current user from the super admins to do the check
                    $tempSuperAdmins = get_super_admins();
                    if (($key = array_search($user->user_login, $tempSuperAdmins)) !== false) {
                        unset($tempSuperAdmins[$key]);
                        $super_admins       = array_values($tempSuperAdmins);
                        $restoreSuperAdmins = true;
                    }
                }
            }

            if (!$user->has_cap($cap)) {
                throw new Exception('User don\'t have the capability');
            }
        } catch (Exception $e) {
            if ($thow) {
                DupLog::trace('SECUTIRY ISSUE: USER ID ' . get_current_user_id() . ' cap: ' . $cap);
                DupLog::trace(SnapLog::getTextException($e));
                throw new Exception('Security issue.');
            } else {
                return false;
            }
        } finally {
            if ($restoreSuperAdmins) {
                $super_admins = $originalSuperAdmins;
            }
        }

        return true;
    }

    /**
     * Whether the capability's hard-disable constant, or an ancestor's, is set.
     *
     * @param string $cap capability enum self::CAP_*
     *
     * @return bool
     */
    private static function isHardDisabled($cap): bool
    {
        $info        = self::getCapsInfo();
        $currentInfo = ($info[$cap] ?? null);
        while ($currentInfo !== null) {
            if (constant($currentInfo['disConstName']) === true) {
                return true;
            }
            $currentInfo = ($info[$currentInfo['parent']] ?? null);
        }

        return false;
    }

    /**
     * Whether the site environment allows the capability at all, independent of any user:
     * hard-disable constants, environmental restrictions and the capability filter.
     *
     * @param string $cap capability enum self::CAP_*
     *
     * @return bool
     */
    public static function isCapAvailableOnSite(string $cap): bool
    {
        if (self::isHardDisabled($cap)) {
            return false;
        }

        $result = !(is_multisite() && $cap === self::CAP_CREATE);

        // No user in the question: userHasCap=true so addons judge the site, not a user.
        return (bool) apply_filters('duplicator_cap_enabled', $result, $cap, true);
    }

    /**
     * Check if current user have the capability
     *
     * @param string $cap  capability enum self::CAP_*
     * @param bool   $thow throw exception if the user don't have the capability
     *
     * @return bool return true if the user have the capability or throw an exception
     */
    public static function can($cap, $thow = true)
    {
        if (self::isHardDisabled($cap)) {
            if ($thow) {
                DupLog::trace('SECUTIRY ISSUE HARDCODED cap: ' . $cap);
                throw new Exception('Security issue.');
            } else {
                return false;
            }
        }

        $userHasCap = self::realCanCheck($cap, false);
        $result     = $userHasCap;

        // Environmental restriction: core does not support multisite backup creation.
        // Without MultisiteAddon this ensures CAP_CREATE is always denied on multisite.
        if ($result && is_multisite() && $cap === self::CAP_CREATE) {
            $result = false;
        }

        /**
         * Filter to dynamically override any capability result.
         *
         * Addons use this to disable capabilities in specific contexts (e.g., StagingAddon
         * disables CAP_STORAGE on staging sites) or re-enable capabilities that core denies
         * by default (e.g., MultisiteAddon re-enables CAP_CREATE on licensed multisite).
         *
         * @param bool   $result     Current capability check result (may be false due to environmental restriction)
         * @param string $cap        Capability being checked
         * @param bool   $userHasCap Whether the user passed the base WordPress capability check.
         *                           Addons that re-enable capabilities should verify this is true
         *                           to avoid overriding explicit per-user capability denials.
         */
        $result = (bool) apply_filters('duplicator_cap_enabled', $result, $cap, $userHasCap);

        if (!$result && $thow) {
            DupLog::trace('SECUTIRY ISSUE: USER ID ' . get_current_user_id() . ' cap: ' . $cap);
            throw new Exception('Security issue.');
        }

        return $result;
    }

    /**
     * Get the editable roles honoring the WordPress 'editable_roles' filter.
     * Safe to call before the wp-admin functions are loaded (e.g. during the upgrade flow).
     *
     * @return array<string, array{name: string, capabilities: array<string, bool>}>
     */
    private static function getEditableRoles(): array
    {
        if (function_exists('get_editable_roles')) {
            return get_editable_roles();
        }

        /** @var array<string, array{name: string, capabilities: array<string, bool>}> $roles */
        $roles = apply_filters('editable_roles', wp_roles()->roles);
        return $roles;
    }

    /**
     * Get selectable roles
     *
     * @return array<string, string>
     */
    public static function getSelectableRoles()
    {
        if (is_multisite()) {
            return [self::ROLE_SUPERADMIN => 'Super Admin'];
        } else {
            $result = [];
            foreach (self::getEditableRoles() as $role => $roleInfo) {
                $result[$role] = $roleInfo['name'];
            }
            return $result;
        }
    }

    /**
     * Get capabilities list
     *
     * @return string[]
     */
    public static function getCapsList()
    {
        static $list = null;
        if (is_null($list)) {
            $list = array_keys(self::getDefaultCaps());
        }
        return $list;
    }

    /**
     * Get default roles for capabilities
     *
     * @return string[]
     */
    public static function getDefaultRoles(): array
    {
        return (is_multisite() ? [self::ROLE_SUPERADMIN] : ['administrator']);
    }

    /**
     * Get default capabilities
     *
     * @return array<string, array{roles: string[], users: int[]}>
     */
    public static function getDefaultCaps(): array
    {
        $defRoles = self::getDefaultRoles();

        $caps = [
            self::CAP_BASIC          => [
                'roles' => $defRoles,
                'users' => [],
            ],
            self::CAP_CREATE         => [
                'roles' => $defRoles,
                'users' => [],
            ],
            self::CAP_STORAGE        => [
                'roles' => $defRoles,
                'users' => [],
            ],
            self::CAP_BACKUP_RESTORE => [
                'roles' => $defRoles,
                'users' => [],
            ],
            self::CAP_EXPORT         => [
                'roles' => $defRoles,
                'users' => [],
            ],
            self::CAP_SETTINGS       => [
                'roles' => $defRoles,
                'users' => [],
            ],
        ];

        /**
         * Filter to allow addons to register their capabilities
         *
         * @param array<string, array{roles: string[], users: int[]}> $caps Default capabilities
         */
        return apply_filters('duplicator_capabilities_default', $caps);
    }

    /**
     * Get capabilities info
     *
     * @return array<string,array{parent:string,label:string,desc:string,disConstName:string}>
     */
    public static function getCapsInfo(): array
    {
        $capsInfo = [
            self::CAP_BASIC          => [
                'parent'       => '',
                'label'        => __('Backup Read', 'duplicator'),
                'desc'         => __(
                    'The capability to read the list of Backups and their characteristics.',
                    'duplicator'
                ) . ' ' . __(
                    'Without this capability, Duplicator is not visible. This is the basis of all the other capabilities listed below.',
                    'duplicator'
                ),
                'disConstName' => 'DUPLICATOR_DISABLE_CAP_BASIC',
            ],
            self::CAP_CREATE         => [
                'parent'       => self::CAP_BASIC,
                'label'        => __('Backup Create', 'duplicator'),
                'desc'         => __(
                    'The capability to create and delete Backups.',
                    'duplicator'
                ),
                'disConstName' => 'DUPLICATOR_DISABLE_CAP_CREATE',
            ],
            self::CAP_STORAGE        => [
                'parent'       => self::CAP_CREATE,
                'label'        => __('Manage Storage', 'duplicator'),
                'desc'         => __(
                    'The capability to create and modify storage. Those with the "Backup Create" capability can select existing storage but cannot edit it',
                    'duplicator'
                ),
                'disConstName' => 'DUPLICATOR_DISABLE_CAP_STORAGE',
            ],
            self::CAP_BACKUP_RESTORE => [
                'parent'       => self::CAP_BASIC,
                'label'        => __('Restore Backup', 'duplicator'),
                'desc'         => __(
                    'The capability to set up and execute a recovery point',
                    'duplicator'
                ),
                'disConstName' => 'DUPLICATOR_DISABLE_CAP_BACKUP_RESTORE',
            ],
            self::CAP_EXPORT         => [
                'parent'       => self::CAP_BASIC,
                'label'        => __('Backup Export', 'duplicator'),
                'desc'         => __(
                    'The capability to download an existing Backup.',
                    'duplicator'
                ),
                'disConstName' => 'DUPLICATOR_DISABLE_CAP_EXPORT',
            ],
            self::CAP_SETTINGS       => [
                'parent'       => self::CAP_BASIC,
                'label'        => __('Manage Settings', 'duplicator'),
                'desc'         => __(
                    'The capability to change settings.',
                    'duplicator'
                ),
                'disConstName' => 'DUPLICATOR_DISABLE_CAP_SETTINGS',
            ],
        ];

        /**
         * Filter to allow addons to register their capabilities info
         *
         * @param array<string,array{parent:string,label:string,desc:string,disConstName:string}> $capsInfo Capabilities info
         */
        $capsInfo = apply_filters('duplicator_capabilities_info', $capsInfo);

        return self::sortCapsByHierarchy($capsInfo);
    }

    /**
     * Sort capabilities by hierarchy (parent-child relationship)
     * Children are placed immediately after their parent
     *
     * @param array<string,array{parent:string,label:string,desc:string,disConstName:string}> $capsInfo Capabilities info
     *
     * @return array<string,array{parent:string,label:string,desc:string,disConstName:string}>
     */
    private static function sortCapsByHierarchy(array $capsInfo): array
    {
        $sorted = [];
        $added  = [];

        // Recursive function to add capability and its children
        $addWithChildren = function (string $cap) use (&$sorted, &$added, &$addWithChildren, $capsInfo): void {
            if (isset($added[$cap])) {
                return;
            }

            $sorted[$cap] = $capsInfo[$cap];
            $added[$cap]  = true;

            // Find and add all children of this capability
            foreach ($capsInfo as $childCap => $childInfo) {
                if ($childInfo['parent'] === $cap) {
                    $addWithChildren($childCap);
                }
            }
        };

        // Start with root capabilities (no parent)
        foreach ($capsInfo as $cap => $info) {
            if ($info['parent'] === '') {
                $addWithChildren($cap);
            }
        }

        // Add any remaining capabilities (orphans or circular references)
        foreach ($capsInfo as $cap => $info) {
            if (!isset($added[$cap])) {
                $sorted[$cap] = $info;
            }
        }

        return $sorted;
    }
}
