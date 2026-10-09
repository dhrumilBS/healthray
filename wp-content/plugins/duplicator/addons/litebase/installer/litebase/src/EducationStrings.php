<?php

namespace Duplicator\Installer\Addons\LiteBase;

class EducationStrings
{
    const UPGRADE_URL        = 'https://duplicator.com/lite-upgrade/?utm_source=duplicator_lite&utm_medium=installer&utm_campaign=installer-footer-callout';
    const HEADER_UPGRADE_URL = 'https://duplicator.com/lite-upgrade/?utm_source=duplicator_lite&utm_medium=installer&utm_campaign=installer-header-callout';
    const DEFAULT_DISCOUNT   = 50;

    /**
     * Long-form "Did you know" lines: name + short description.
     * Used by the plugin packages bottom bar and the installer did-you-know box.
     *
     * @return string[]
     */
    public static function getDidYouKnowList(): array
    {
        return [
            __(
                'Scheduled Backups - Ensure that important data is regularly and consistently backed up, 
                allowing for quick and efficient recovery in case of data loss.',
                'duplicator'
            ),
            __(
                'Cloud Backups - Back up to Dropbox, FTP, Google Drive, OneDrive, 
                or Amazon S3 and more for safe storage.',
                'duplicator'
            ),
            __(
                'Recovery Points - Recovery Points provide protection against mistakes and
                bad updates by letting you quickly rollback your system to a known, good state.',
                'duplicator'
            ),
            __(
                'Backup Templates - Save multiple backup configurations as reusable templates
                and pick the right one for each backup or schedule.',
                'duplicator'
            ),
            __(
                'Server to Server Import - Direct Backup import
                from source server or cloud storage using URL. No need to download the Backup to your desktop machine first.',
                'duplicator'
            ),
            __(
                'Custom Backup Types - Media Only backups and fully custom component selection:
                back up only the plugins, themes, media or database you need.',
                'duplicator'
            ),
            __(
                'Installer Branding - Create your own custom-configured WordPress site
                and brand the installer with your look and feel.',
                'duplicator'
            ),
            __(
                'Multisite Support - Duplicator Pro supports multisite network backup & migration. You can even install a subsite as a standalone site.',
                'duplicator'
            ),
            __(
                'Staging Sites - Test changes safely on a private clone of your live site before going public.',
                'duplicator'
            ),
            __(
                'Drag & Drop Imports - Migrate any backup by simply dropping the archive into a new WordPress install.',
                'duplicator'
            ),
        ];
    }

    /**
     * Short feature labels for footer CTA bullet lists (plugin settings callout + installer footer).
     *
     * @return string[]
     */
    public static function getFooterFeatureList(): array
    {
        return [
            __('Scheduled Backups', 'duplicator'),
            __('Hourly Backups', 'duplicator'),
            __('Recovery Points', 'duplicator'),
            __('Server to Server Import', 'duplicator'),
            __('Drag & Drop Installs', 'duplicator'),
            __('Third-Party Cloud Storage', 'duplicator'),
            __('Staging Sites', 'duplicator'),
            __('Backup Templates', 'duplicator'),
            __('Custom Backup Types', 'duplicator'),
            __('Installer Branding', 'duplicator'),
            __('Multisite Network', 'duplicator'),
            __('Email Alerts', 'duplicator'),
            __('WP-CLI Commands', 'duplicator'),
            __('Advanced Backup Permissions', 'duplicator'),
            __('Priority Support', 'duplicator'),
        ];
    }
}
