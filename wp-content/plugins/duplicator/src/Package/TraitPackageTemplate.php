<?php

/**
 * Trait for package template operations
 */

declare(strict_types=1);

namespace Duplicator\Package;

use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\TemplateEntity;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\Database\DatabasePkg;
use Exception;

/**
 * Trait TraitPackageTemplate
 *
 * Handles package template operations including setting properties from templates
 * and generating names from template formats.
 *
 * @phpstan-require-extends AbstractPackage
 */
trait TraitPackageTemplate
{
    /**
     * Set properties by template
     *
     * @param TemplateEntity $template template
     *
     * @return void
     */
    protected function setByTemplate(?TemplateEntity $template = null)
    {
        if ($template === null) {
            return;
        }

        $this->notes = $template->notes;

        //ARCHIVE
        $this->components           = $template->components;
        $this->Archive->FilterOn    = $template->archive_filter_on;
        $this->Archive->FilterDirs  = $template->archive_filter_dirs;
        $this->Archive->FilterExts  = $template->archive_filter_exts;
        $this->Archive->FilterFiles = $template->archive_filter_files;
        $this->Archive->FilterNames = $template->archive_filter_names;

        //INSTALLER
        $this->Installer->OptsDBHost   = $template->installer_opts_db_host;
        $this->Installer->OptsDBName   = $template->installer_opts_db_name;
        $this->Installer->OptsDBUser   = $template->installer_opts_db_user;
        $this->Installer->OptsSecureOn = $template->installer_opts_secure_on;
        $this->Installer->passowrd     = $template->installerPassowrd;
        $this->Installer->OptsSkipScan = $template->installer_opts_skip_scan;

        // CPANEL
        $this->Installer->OptsCPNLEnable   = $template->installer_opts_cpnl_enable;
        $this->Installer->OptsCPNLHost     = $template->installer_opts_cpnl_host;
        $this->Installer->OptsCPNLUser     = $template->installer_opts_cpnl_user;
        $this->Installer->OptsCPNLDBAction = $template->installer_opts_cpnl_db_action;
        $this->Installer->OptsCPNLDBHost   = $template->installer_opts_cpnl_db_host;
        $this->Installer->OptsCPNLDBName   = $template->installer_opts_cpnl_db_name;
        $this->Installer->OptsCPNLDBUser   = $template->installer_opts_cpnl_db_user;

        //DATABASE
        $this->Database->FilterOn        = $template->database_filter_on;
        $this->Database->prefixSubFilter = $template->databasePrefixSubFilter;
        $this->Database->FilterTables    = $template->database_filter_tables;
        $this->Database->Compatible      = $template->database_compatibility_modes;
        $this->Database->setPrefixFilter((bool) $template->databasePrefixFilter);

        do_action('duplicator_package_set_from_template', $this, $template);
    }

    /**
     * Generate a Backup name from a template
     *
     * @param ?TemplateEntity $template  Template to use
     * @param int             $timestamp Timestamp
     *
     * @return string
     */
    protected function getNameFromFormat(
        ?TemplateEntity $template = null,
        $timestamp = 0
    ) {
        $nameFormat = new NameFormat();
        $nameFormat->setTimestamp($timestamp);
        if ($template instanceof TemplateEntity) {
            $nameFormat->setFormat($template->package_name_format);
            $nameFormat->setTemplateId($template->getId());
        }
        return $nameFormat->getName();
    }

    /**
     * Saves the active options associated with the active(latest) package.
     *
     * @param ?array<string,mixed> $post The _POST server object
     *
     * @return void
     */
    public static function setManualTemplateFromPost(?array $post = null): void
    {
        if (!isset($post)) {
            return;
        }

        $post                  = stripslashes_deep($post);
        $mtemplate             = TemplateEntity::getManualTemplate();
        $mtemplate->components = BuildComponents::getFromInput($post);

        if (isset($post['package_name_format'])) {
            $mtemplate->package_name_format = SnapUtil::sanitize($post['package_name_format']);
        }

        if (isset($post['filter-paths'])) {
            $post_filter_paths               = SnapUtil::sanitizeNSChars($post['filter-paths']);
            $mtemplate->archive_filter_dirs  = PackageArchive::parseDirectoryFilter($post_filter_paths);
            $mtemplate->archive_filter_files = PackageArchive::parseFileFilter($post_filter_paths);
        } else {
            $mtemplate->archive_filter_dirs  = '';
            $mtemplate->archive_filter_files = '';
        }

        if (isset($post['filter-exts'])) {
            $post_filter_exts               = sanitize_text_field($post['filter-exts']);
            $mtemplate->archive_filter_exts = PackageArchive::parseExtensionFilter($post_filter_exts);
        } else {
            $mtemplate->archive_filter_exts = '';
        }

        $tablelist  = isset($post['dbtables-list']) ? SnapUtil::sanitizeNSCharsNewlineTrim($post['dbtables-list']) : '';
        $compatlist = isset($post['dbcompat']) ? DatabasePkg::sanitizeCompatibilityMode($post['dbcompat']) : '';
        // PACKAGE
        // Replaces any \n \r or \n\r from the Backup notes
        if (isset($post['package-notes'])) {
            $mtemplate->notes = SnapUtil::sanitizeNSCharsNewlineTrim($post['package-notes']);
        } else {
            $mtemplate->notes = '';
        }

        //ARCHIVE
        $mtemplate->archive_filter_on    = isset($post['filter-on']);
        $mtemplate->archive_filter_names = isset($post['filter-names']);
        //INSTALLER
        $secureOn = (isset($post['secure-on']) ? (int) $post['secure-on'] : ArchiveDescriptor::SECURE_MODE_NONE);
        switch ($secureOn) {
            case ArchiveDescriptor::SECURE_MODE_NONE:
            case ArchiveDescriptor::SECURE_MODE_INST_PWD:
            case ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT:
                $mtemplate->installer_opts_secure_on = $secureOn;
                break;
            default:
                throw new Exception(__('Select valid secure mode', 'duplicator'));
        }

        $mtemplate->installerPassowrd        = isset($post['secure-pass']) ? SnapUtil::sanitizeNSCharsNewlineTrim($post['secure-pass']) : '';
        $mtemplate->installer_opts_skip_scan = (isset($post['skipscan']) && 1 == $post['skipscan']);
        //cPanel
        $mtemplate->installer_opts_cpnl_enable    = (isset($post['installer_opts_cpnl_enable']) && 1 == $post['installer_opts_cpnl_enable']);
        $mtemplate->installer_opts_cpnl_host      = sanitize_text_field($post['installer_opts_cpnl_host'] ?? '');
        $mtemplate->installer_opts_cpnl_user      = sanitize_text_field($post['installer_opts_cpnl_user'] ?? '');
        $mtemplate->installer_opts_cpnl_db_action = sanitize_text_field($post['installer_opts_cpnl_db_action'] ?? '');
        $mtemplate->installer_opts_cpnl_db_host   = sanitize_text_field($post['installer_opts_cpnl_db_host'] ?? '');
        $mtemplate->installer_opts_cpnl_db_name   = sanitize_text_field($post['installer_opts_cpnl_db_name'] ?? '');
        $mtemplate->installer_opts_cpnl_db_user   = sanitize_text_field($post['installer_opts_cpnl_db_user'] ?? '');
        //Basic
        $mtemplate->installer_opts_db_host = sanitize_text_field($post['installer_opts_db_host'] ?? '');
        $mtemplate->installer_opts_db_name = sanitize_text_field($post['installer_opts_db_name'] ?? '');
        $mtemplate->installer_opts_db_user = sanitize_text_field($post['installer_opts_db_user'] ?? '');
        // DATABASE
        $mtemplate->database_filter_on      = isset($post['dbfilter-on']);
        $mtemplate->databasePrefixFilter    = isset($post['db-prefix-filter']);
        $mtemplate->databasePrefixSubFilter = isset($post['db-prefix-sub-filter']);
        $mtemplate->database_filter_tables  = sanitize_text_field($tablelist);

        $mtemplate->database_compatibility_modes = $compatlist;

        do_action('duplicator_template_after_set_from_post', $mtemplate, $post);

        $mtemplate->save();
    }
}
