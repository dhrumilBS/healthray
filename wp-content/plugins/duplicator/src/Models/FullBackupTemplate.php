<?php

/**
 * Template for plugin-requested Backups.
 *
 * @package   Duplicator
 * @copyright (c) 2026, Snap Creek LLC
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 */

declare(strict_types=1);

namespace Duplicator\Models;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Package\Create\BuildComponents;
use Exception;

/**
 * Runtime copy of the default template with the full component set and the template's own
 * exclusions removed. The prefix filter keeps the default template's value, so a Backup
 * covers the tables carrying the site prefix.
 */
final class FullBackupTemplate extends TemplateEntity
{
    /**
     * Derive the template from the current default template, without its exclusions.
     *
     * @throws DupliException When the default template is missing
     */
    public function __construct()
    {
        parent::__construct();

        if (($default = TemplateEntity::getDefaultTemplate()) === null) {
            throw new DupliException(
                'Default backup template missing.',
                DupliException::CODE_ERROR,
                __('The default backup template is missing. Deactivate and reactivate Duplicator to restore it.', 'duplicator')
            );
        }
        $this->copyFrom($default);

        $this->components              = BuildComponents::COMPONENTS_DEFAULT;
        $this->database_filter_on      = false;
        $this->database_filter_tables  = '';
        $this->databasePrefixSubFilter = false;
        $this->archive_filter_on       = false;
        $this->archive_filter_dirs     = '';
        $this->archive_filter_files    = '';
        $this->archive_filter_exts     = '';
        $this->archive_filter_names    = false;
        $this->setExtraData('filter_sites', []);
    }

    /**
     * @return string
     */
    public static function getType(): string
    {
        return 'Full_Backup_Template';
    }

    /**
     * The template exists only for the duration of a request.
     *
     * @return bool
     * @throws Exception Always
     */
    public function save(): bool
    {
        throw new Exception('The full-backup template is built at runtime and cannot be saved.');
    }
}
