<?php

/**
 * Validation object
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Params\Items\ParamFormSitesOwrMap;
use Duplicator\Installer\Models\ScanInfo;
use Duplicator\Installer\Package\PComponents;

class DUPX_Validation_test_importable extends DUPX_Validation_abstract_item
{
    /** @var string[] */
    protected $failMessages = [];
    /** @var bool true when the backend import fails because no subsite can be imported */
    protected $noImportableSubsites = false;

    /**
     * Run test
     *
     * @return int  test status enum
     */
    protected function runTest(): int
    {
        $archiveConf = DUPX_ArchiveConfig::getInstance();

        $coreFoldersCheck     = false;
        $subsitesCheck        = false;
        $globalTablesCheck    = false;
        $partialSubsitesCheck = false;
        $backendImportCheck   = false;

        switch (InstState::getInstType()) {
            case InstState::TYPE_SINGLE:
            case InstState::TYPE_RBACKUP_SINGLE:
            case InstState::TYPE_RECOVERY_SINGLE:
                $coreFoldersCheck  = true;
                $globalTablesCheck = true;
                break;
            case InstState::TYPE_MSUBDOMAIN:
            case InstState::TYPE_MSUBFOLDER:
            case InstState::TYPE_RBACKUP_MSUBDOMAIN:
            case InstState::TYPE_RBACKUP_MSUBFOLDER:
            case InstState::TYPE_RECOVERY_MSUBDOMAIN:
            case InstState::TYPE_RECOVERY_MSUBFOLDER:
                $coreFoldersCheck     = true;
                $subsitesCheck        = true;
                $globalTablesCheck    = true;
                $partialSubsitesCheck = true;
                break;
            case InstState::TYPE_STANDALONE:
                $coreFoldersCheck   = true;
                $subsitesCheck      = true;
                $backendImportCheck = true;
                break;
            case InstState::TYPE_SINGLE_ON_SUBDOMAIN:
            case InstState::TYPE_SINGLE_ON_SUBFOLDER:
                $globalTablesCheck  = true;
                $backendImportCheck = true;
                break;
            case InstState::TYPE_SUBSITE_ON_SUBDOMAIN:
            case InstState::TYPE_SUBSITE_ON_SUBFOLDER:
                $subsitesCheck      = true;
                $backendImportCheck = true;
                break;
            case InstState::TYPE_NOT_SET:
            default:
                throw new Exception('Unknown mode');
        }

        $result = self::LV_PASS;

        foreach (PComponents::COMPONENTS_DEFAULT as $component) {
            if (
                in_array($component, $archiveConf->components)
            ) {
                $this->failMessages[] = 'Component <b>' . PComponents::getLabel($component) . '</b> ' .
                    '<i class="fas fa-check-circle green"></i>' . ' included.';
            } else {
                $this->failMessages[] = 'Component <b>' . PComponents::getLabel($component) . '</b> ' .
                    '<i class="fas fa-times-circle maroon"></i>' . ' excluded.';
                if ($component != PComponents::COMP_OTHER) {
                    $result = self::LV_HARD_WARNING;
                }
            }
        }

        if ($coreFoldersCheck) {
            if (ScanInfo::getInstance()->hasFilteredCoreFolders()) {
                $this->failMessages[] = '<i class="fas fa-times-circle maroon"></i>' .
                    ' Some Wordpress core folders are missing. (e.g. wp-admin, wp-content, wp-includes, uploads, plugins, and themes folders)';
                $result               = self::LV_HARD_WARNING;
            }
        }

        if ($partialSubsitesCheck) {
            if ($archiveConf->mu_is_filtered) {
                $this->failMessages[] = '<i class="fas fa-times-circle maroon"></i>' .
                    ' In This Backup, some sub-sites of the multisite installation have been excluded.';
                $result               = self::LV_HARD_WARNING;
            }
        }

        $backendImportBlocked = (
            $backendImportCheck &&
            InstState::isImportFromBackendMode() &&
            !self::haveImportableSubsite($archiveConf)
        );

        if ($subsitesCheck && !$backendImportBlocked) {
            for ($i = 0; $i < count($archiveConf->subsites); $i++) {
                if (
                    empty($archiveConf->subsites[$i]->filteredTables) &&
                    empty($archiveConf->subsites[$i]->filteredPaths)
                ) {
                    break;
                }
            }

            if ($i >= count($archiveConf->subsites)) {
                $this->failMessages[] = '<i class="fas fa-times-circle maroon"></i>' .
                    ' The Backup does not have any importable subsite.';
                $result               = self::LV_HARD_WARNING;
            }
        }

        if ($globalTablesCheck && !InstState::dbDoNothing()) {
            if ($archiveConf->dbInfo->tablesBaseCount != $archiveConf->dbInfo->tablesFinalCount) {
                $this->failMessages[] = '<i class="fas fa-times-circle maroon"></i>' .
                    ' The Backup is missing some of the site tables.';
                $result               = self::LV_HARD_WARNING;
            }
        }

        if ($backendImportBlocked) {
            $this->failMessages[] = '<i class="fas fa-times-circle maroon"></i>' .
                ' None of the sites in this Backup can be imported because some of their' .
                ' database tables were excluded when the Backup was created.';
            $this->noImportableSubsites = true;
            $result                     = self::LV_FAIL;
        }

        return $result;
    }

    /**
     * Check if at least one subsite qualifies for backend import,
     * using the same rule that enables options in the source site dropdown
     *
     * @param DUPX_ArchiveConfig $archiveConf archive config
     *
     * @return bool
     */
    protected static function haveImportableSubsite(DUPX_ArchiveConfig $archiveConf): bool
    {
        foreach ($archiveConf->subsites as $subsite) {
            if (ParamFormSitesOwrMap::isQualifiedSourceIdForImport($subsite)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get test title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Partial Backup Check';
    }

    /**
     * Render hard warning content
     *
     * @return string
     */
    protected function hwarnContent()
    {
        return dupxTplRender(
            'parts/validation/tests/importable-package',
            [
                'testResult'           => $this->testResult,
                'failMessages'         => $this->failMessages,
                'noImportableSubsites' => $this->noImportableSubsites,
            ],
            false
        );
    }

    /**
     * Render pass content
     *
     * @return string
     */
    protected function passContent()
    {
        return $this->hwarnContent();
    }

    /**
     * Render fail content
     *
     * @return string
     */
    protected function failContent()
    {
        if ($this->noImportableSubsites) {
            return $this->hwarnContent();
        }
        return parent::failContent();
    }
}
