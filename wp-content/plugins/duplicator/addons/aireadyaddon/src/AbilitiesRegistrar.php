<?php

/**
 * DUPLICATOR AI READY ADDON - ABILITIES REGISTRAR
 *
 * @package   Duplicator
 * @copyright (c) 2026, Snap Creek LLC
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

declare(strict_types=1);

namespace Duplicator\Addons\AiReadyAddon;

use Duplicator\Addons\StagingAddon\StagingAddon;
use Duplicator\Core\CapMng;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\BackupRequestService;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\DupPackage;
use stdClass;
use WP_Error;

/**
 * Registers the Duplicator abilities as a thin adapter over BackupRequestService.
 */
class AbilitiesRegistrar
{
    const CATEGORY_SLUG = 'duplicator';

    const LIST_LIMIT = 20;

    /** @var string Requester attribution of ability-created Backups */
    const REQUESTER = 'AI Assistant';

    private BackupRequestService $service;

    /**
     * @param BackupRequestService|null $service Injectable for testing
     */
    public function __construct(?BackupRequestService $service = null)
    {
        $this->service = $service ?? new BackupRequestService();
    }

    /**
     * @return void
     */
    public function registerCategory(): void
    {
        wp_register_ability_category(self::CATEGORY_SLUG, [
            'label'       => __('Backups', 'duplicator'),
            'description' => __('Manage Duplicator backup packages.', 'duplicator'),
        ]);
    }

    /**
     * @return void
     */
    public function registerAbilities(): void
    {
        wp_register_ability('duplicator/list-backups', [
            'label'               => __('List Backups', 'duplicator'),
            'description'         => sprintf(
                /* translators: %d: maximum number of backups returned */
                __('Returns the %d most recent backups: id, name, status, date, size. Use id with get-backup-status to check progress.', 'duplicator'),
                self::LIST_LIMIT
            ),
            'category'            => self::CATEGORY_SLUG,
            'execute_callback'    => [
                $this,
                'handleListBackups',
            ],
            'permission_callback' => [
                $this,
                'readPermissionCallback',
            ],
            'input_schema'        => [
                'type'       => 'object',
                'properties' => new stdClass(),
                'default'    => new stdClass(),
            ],
            'output_schema'       => [
                'type'       => 'object',
                'properties' => [
                    'count'   => [
                        'type'        => 'integer',
                        'description' => 'Total number of backups returned.',
                    ],
                    'backups' => [
                        'type'  => 'array',
                        'items' => [
                            'type'       => 'object',
                            'properties' => [
                                'id'      => [
                                    'type'        => 'integer',
                                    'description' => 'Unique backup id. Pass to get-backup-status to poll progress.',
                                ],
                                'name'    => [
                                    'type'        => 'string',
                                    'description' => 'Human-readable backup name.',
                                ],
                                'created' => [
                                    'type'        => 'string',
                                    'description' => 'Creation timestamp.',
                                ],
                                'status'  => [
                                    'type'        => 'string',
                                    'enum'        => BackupRequestService::STATUS_KEYS,
                                    'description' => 'Current status of the backup.',
                                ],
                                'size'    => [
                                    'type'        => 'string',
                                    'description' => 'Human-readable backup archive size (e.g. "214 MB"); a placeholder while still building.',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'meta'                => [
                'show_in_rest' => true,
                'mcp'          => ['public' => true],
                'annotations'  => ['readonly' => true],
            ],
        ]);

        wp_register_ability('duplicator/create-backup', [
            'label'               => __('Create Backup', 'duplicator'),
            'description'         => __(
                'Starts a new backup using the default template, ignoring its file and table filters.
                Returns an id for polling with get-backup-status. When the backup completes, the storage
                retention setting may remove the oldest backups.',
                'duplicator'
            ),
            'category'            => self::CATEGORY_SLUG,
            'execute_callback'    => [
                $this,
                'handleCreateBackup',
            ],
            'permission_callback' => [
                $this,
                'writePermissionCallback',
            ],
            'input_schema'        => [
                'type'       => 'object',
                'properties' => [
                    'scope'  => [
                        'type'        => 'string',
                        'enum'        => BuildComponents::COMPONENTS_ACTIONS,
                        'default'     => BuildComponents::COMP_ACTION_ALL,
                        'description' => 'What the backup contains: "all" for the whole site, "database" for the database only.',
                    ],
                    'reason' => [
                        'type'        => 'string',
                        'default'     => '',
                        'maxLength'   => BackupRequestService::REASON_MAX_LENGTH,
                        'description' => 'Why the backup is being created; shown to the user as attribution.',
                    ],
                ],
            ],
            'output_schema'       => [
                'type'       => 'object',
                'properties' => [
                    'id'      => [
                        'type'        => 'integer',
                        'description' => 'Unique backup id. Pass to get-backup-status to poll progress.',
                    ],
                    'message' => [
                        'type'        => 'string',
                        'description' => 'Confirmation message.',
                    ],
                ],
            ],
            'meta'                => [
                'show_in_rest' => true,
                'mcp'          => ['public' => true],
                'annotations'  => [
                    'readonly'    => false,
                    'destructive' => false,
                ],
            ],
        ]);

        wp_register_ability('duplicator/get-backup-status', [
            'label'               => __('Get Backup Status', 'duplicator'),
            'description'         => __('Returns backup status by id. Poll until status is complete, failed, cancelled, or missing.', 'duplicator'),
            'category'            => self::CATEGORY_SLUG,
            'execute_callback'    => [
                $this,
                'handleGetBackupStatus',
            ],
            'permission_callback' => [
                $this,
                'readPermissionCallback',
            ],
            'input_schema'        => [
                'type'       => 'object',
                'required'   => ['id'],
                'properties' => [
                    'id' => [
                        'type'        => 'integer',
                        'minimum'     => 1,
                        'description' => 'The backup id from list-backups or create-backup.',
                    ],
                ],
            ],
            'output_schema'       => [
                'type'       => 'object',
                'properties' => [
                    'id'             => [
                        'type'        => 'integer',
                        'description' => 'Backup id.',
                    ],
                    'status'         => [
                        'type'        => 'string',
                        'enum'        => array_merge([BackupRequestService::STATUS_MISSING], BackupRequestService::STATUS_KEYS),
                        'description' => 'Current status of the backup; poll until it reaches a terminal value.',
                    ],
                    'can_proceed'    => [
                        'type'        => 'boolean',
                        'description' => 'True when a usable backup created through this API exists locally and no warning '
                            . 'reports files missing from the archive. Other build warnings do not clear it.',
                    ],
                    'message'        => [
                        'type'        => 'string',
                        'description' => 'Detailed status message for the current operation.',
                    ],
                    'details_url'    => [
                        'type'        => 'string',
                        'description' => 'Admin URL of the backup details page.',
                    ],
                    'is_full_backup' => [
                        'type'        => 'boolean',
                        'description' => 'True when no configured component, table, subsite or archive filter excludes anything from the backup.',
                    ],
                    'exclusions'     => [
                        'type'        => 'array',
                        'items'       => ['type' => 'string'],
                        'description' => 'What the backup does not contain; empty when is_full_backup is true.',
                    ],
                    'warnings'       => [
                        'type'        => 'array',
                        'description' => 'Non-critical conditions detected during the build. The backup completed; '
                            . 'report them to the user rather than treating them as a failure.',
                        'items'       => [
                            'type'       => 'object',
                            'properties' => [
                                'code'    => [
                                    'type'        => 'integer',
                                    'description' => 'Duplicator warning code, matching an AbstractPackage::WARNING_* constant.',
                                ],
                                'label'   => [
                                    'type'        => 'string',
                                    'description' => 'Short summary of the condition.',
                                ],
                                'message' => [
                                    'type'        => 'string',
                                    'description' => 'Full explanation for the user.',
                                ],
                            ],
                        ],
                    ],
                    'failure'        => [
                        'type'        => 'object',
                        'description' => 'Why a failed or cancelled backup stopped and how to fix it, as shown in the Activity Log. '
                            . 'Relay the reason and the fixes to the user; a fix may belong to an earlier failure, so present the fixes '
                            . 'as suggestions. Empty when no failure was recorded.',
                        'properties'  => [
                            'reason' => [
                                'type'        => 'string',
                                'description' => 'Failure cause; may be empty even when fixes are listed.',
                            ],
                            'fixes'  => [
                                'type'  => 'array',
                                'items' => [
                                    'type'       => 'object',
                                    'properties' => [
                                        'error'           => [
                                            'type'        => 'string',
                                            'description' => 'What went wrong.',
                                        ],
                                        'troubleshooting' => [
                                            'type'        => 'array',
                                            'items'       => ['type' => 'string'],
                                            'description' => 'Steps the user can take to fix it.',
                                        ],
                                        'code_snippet'    => [
                                            'type'        => 'string',
                                            'description' => 'Configuration example for the fix; empty when none.',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'meta'                => [
                'show_in_rest' => true,
                'mcp'          => ['public' => true],
                'annotations'  => ['readonly' => true],
            ],
        ]);
    }

    /**
     * Permission check for read-only abilities (list, status). Requires CAP_BASIC.
     *
     * @param mixed $input Ability input (unused).
     *
     * @return bool|WP_Error
     */
    public function readPermissionCallback($input)
    {
        if (!CapMng::can(CapMng::CAP_BASIC, false)) {
            return new WP_Error(
                'duplicator_permission_denied',
                __('You do not have permission to use Duplicator. Contact your site administrator.', 'duplicator')
            );
        }

        if (($staging = $this->stagingGuard()) !== null) {
            return $staging;
        }

        return true;
    }

    /**
     * Permission check for create-backup. Requires the create capability; staging-guarded.
     *
     * @param mixed $input Ability input (unused).
     *
     * @return bool|WP_Error
     */
    public function writePermissionCallback($input)
    {
        if (!CapMng::can(CapMng::CAP_CREATE, false)) {
            return new WP_Error(
                'duplicator_permission_denied',
                __('You do not have permission to create backups on this site. Contact your site administrator.', 'duplicator')
            );
        }

        if (($staging = $this->stagingGuard()) !== null) {
            return $staging;
        }

        return true;
    }

    /**
     * Called after the capability check, so an uncapable caller learns nothing about the site.
     *
     * @return WP_Error|null Error when on a staging site, null otherwise
     */
    private function stagingGuard(): ?WP_Error
    {
        if (!$this->isStagingSite()) {
            return null;
        }

        return new WP_Error(
            'duplicator_staging_site',
            __('Backup management is disabled on staging sites. Manage backups on your production site instead.', 'duplicator')
        );
    }

    /**
     * @param mixed $input Ability input (no parameters for this ability).
     *
     * @return array{count: int, backups: list<array{id: int, name: string, created: string, status: string, size: string}>}
     */
    public function handleListBackups($input): array
    {
        // Full objects for the stored size; standard type only, as the Backups list does.
        /** @var AbstractPackage[] $packages */
        $packages = DupPackage::getPackagesByStatus([], self::LIST_LIMIT, 0, '`id` DESC', 'objs', [DupPackage::getType()]);

        $backups = [];
        foreach ($packages as $package) {
            $backups[] = [
                'id'      => $package->getId(),
                'name'    => $package->getName(),
                'created' => $package->getCreated(),
                'status'  => BackupRequestService::getStatusKey($package),
                'size'    => $package->getBuildSize(),
            ];
        }

        return [
            'count'   => count($backups),
            'backups' => $backups,
        ];
    }

    /**
     * @param mixed $input Ability input; accepts optional `scope` and `reason` strings.
     *
     * @return array{id: int, message: string}|WP_Error
     */
    public function handleCreateBackup($input)
    {
        $input  = (array) $input;
        $scope  = is_string($input['scope'] ?? null) ? $input['scope'] : BuildComponents::COMP_ACTION_ALL;
        $reason = is_string($input['reason'] ?? null) ? $input['reason'] : '';

        $id = $this->service->request(self::REQUESTER, $reason, $scope);
        if (is_wp_error($id)) {
            return $id;
        }

        return [
            'id'      => $id,
            'message' => __('Backup started. Poll get-backup-status with the id to track progress.', 'duplicator'),
        ];
    }

    /**
     * @param mixed $input Ability input; requires a positive integer `id`.
     *
     * @return array{
     *   id: int, status: string, can_proceed: bool, message: string,
     *   details_url: string, is_full_backup: bool, exclusions: string[]
     * }|WP_Error
     */
    public function handleGetBackupStatus($input)
    {
        $input = (array) $input;
        $id    = is_numeric($input['id'] ?? null) ? (int) $input['id'] : 0;

        $status = $this->service->getStatus($id);
        if (is_wp_error($status)) {
            return $status;
        }

        return ['id' => $id] + $status;
    }

    /**
     * @return bool
     */
    private function isStagingSite(): bool
    {
        return class_exists(StagingAddon::class) &&
            StagingAddon::isStagingSite();
    }
}
