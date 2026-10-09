<?php

declare(strict_types=1);

namespace Duplicator\Core\Options\Requirements;

use Duplicator\Controllers\SettingsPageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\MigrationMng;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\DupArchive\DupArchive;
use Duplicator\Libs\Shell\ShellZipUtils;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\WpUtils\WpArchiveUtils;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Utils\ZipArchiveExtended;

/**
 * Core requirement definitions: the environment facts consumed by the option
 * rules and by the baseline gate. Every check wraps an existing utility
 * without duplicating its logic.
 */
class RequirementDefs
{
    const REQ_ZLIB                  = 'zlib';
    const REQ_ZIPARCHIVE_EXT        = 'ziparchive_ext';
    const REQ_ZIPARCHIVE_ENCRYPTION = 'ziparchive_encryption';
    const REQ_SHELL_ZIP_BINARY      = 'shell_zip_binary';
    const REQ_OPENSSL               = 'openssl';
    const REQ_MYSQLDUMP_BINARY      = 'mysqldump_binary';
    const REQ_PHP_64BIT             = 'php_64bit';
    const REQ_HOME_READABLE         = 'home_readable';
    const REQ_SSDIR_WRITABLE        = 'ssdir_writable';
    const REQ_SSTMP_WRITABLE        = 'sstmp_writable';
    const REQ_MYSQL_MIN_VERSION     = 'mysql_min_version';
    const REQ_MYSQL_ESCAPE          = 'mysql_escape';
    const REQ_NO_INSTALLER_FILES    = 'no_installer_files';
    const REQ_PHP_FUNC_PREFIX       = 'php_func_';

    private const MYSQL_MIN_VERSION = '5.0';

    /**
     * Baseline PHP functions required to build any Backup,
     * function name => documentation URL
     *
     * @var array<string, string>
     */
    private const BASE_PHP_FUNCTIONS = [
        'json_encode'         => 'https://www.php.net/manual/en/function.json-encode.php',
        'token_get_all'       => 'https://www.php.net/manual/en/function.token-get-all',
        'file_get_contents'   => 'https://www.php.net/manual/en/function.file-get-contents.php',
        'file_put_contents'   => 'https://www.php.net/manual/en/function.file-put-contents.php',
        'rename'              => 'https://www.php.net/manual/en/function.rename.php',
        'unlink'              => 'https://www.php.net/manual/en/function.unlink.php',
        'fopen'               => 'https://www.php.net/manual/en/function.fopen.php',
        'fwrite'              => 'https://www.php.net/manual/en/function.fwrite.php',
        'fclose'              => 'https://www.php.net/manual/en/function.fclose.php',
        'mb_strlen'           => 'https://www.php.net/manual/en/mbstring.installation.php',
        'mb_detect_encoding'  => 'https://www.php.net/manual/en/mbstring.installation.php',
        'mb_convert_encoding' => 'https://www.php.net/manual/en/mbstring.installation.php',
        'gzopen'              => 'https://www.php.net/manual/en/function.gzopen.php',
    ];

    /**
     * Get the requirement id of a baseline PHP function
     *
     * @param string $function PHP function name
     *
     * @return string
     */
    private static function getPhpFuncRequirementId(string $function): string
    {
        return self::REQ_PHP_FUNC_PREFIX . $function;
    }

    /**
     * Ids of the baseline requirements: environment facts that must all pass
     * to build any Backup, whatever the configuration
     *
     * @return string[]
     */
    public static function getBaselineRequirementIds(): array
    {
        $ids = [
            self::REQ_PHP_64BIT,
            self::REQ_HOME_READABLE,
            self::REQ_SSDIR_WRITABLE,
            self::REQ_SSTMP_WRITABLE,
            self::REQ_MYSQL_MIN_VERSION,
            self::REQ_MYSQL_ESCAPE,
            self::REQ_NO_INSTALLER_FILES,
        ];

        foreach (array_keys(self::BASE_PHP_FUNCTIONS) as $function) {
            $ids[] = self::getPhpFuncRequirementId($function);
        }

        return $ids;
    }

    /**
     * Build all the core requirement definitions
     *
     * Presentation strings must be resolved lazily because upgrade corrections
     * can initialize this catalog before WordPress reaches the init action.
     * Translating them while building the catalog would trigger an early
     * just-in-time load of the plugin text domain.
     *
     * @return Requirement[]
     */
    public static function getRequirements(): array
    {
        $result = array_merge(
            self::getOptionRequirements(),
            self::getBaselineRequirements()
        );

        foreach (self::BASE_PHP_FUNCTIONS as $function => $docUrl) {
            $result[] = new Requirement(
                self::getPhpFuncRequirementId($function),
                static fn(): string => sprintf(__('PHP function %s', 'duplicator'), $function),
                fn(): bool => function_exists($function),
                static fn(): string => sprintf(__('The required PHP function %s doesn\'t exist.', 'duplicator'), $function),
                '',
                $docUrl
            );
        }

        return $result;
    }

    /**
     * Requirements referenced by the core option rules
     *
     * @return Requirement[]
     */
    private static function getOptionRequirements(): array
    {
        return [
            new Requirement(
                self::REQ_ZLIB,
                static fn(): string => __('PHP zlib compression functions', 'duplicator'),
                fn(): bool => SnapUtil::isZlibEnabled(),
                static fn(): string => __(
                    'The PHP zlib extension (gzdeflate/gzinflate functions) is not available on this server.',
                    'duplicator'
                ),
                static fn(): string => __(
                    'DupArchive requires the PHP zlib extension when compression is enabled.
                    Enable zlib, set Archive Compression to Off, or switch to the ZipArchive or Shell Zip engine if available.',
                    'duplicator'
                ),
                'https://www.php.net/manual/en/function.gzdeflate.php'
            ),
            new Requirement(
                self::REQ_ZIPARCHIVE_EXT,
                static fn(): string => __('PHP ZipArchive class', 'duplicator'),
                fn(): bool => ZipArchiveExtended::isPhpZipAvailable(),
                static fn(): string => __('The PHP ZipArchive class doesn\'t exist on this server.', 'duplicator'),
                static fn(): string => __('Enable the PHP zip extension or switch to another Archive Engine.', 'duplicator'),
                DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-work-with-the-different-zip-engines'
            ),
            new Requirement(
                self::REQ_ZIPARCHIVE_ENCRYPTION,
                static fn(): string => __('ZipArchive encryption support', 'duplicator'),
                fn(): bool => ZipArchiveExtended::isEncryptionAvaliable(),
                static fn(): string => __('This server doesn\'t support ZipArchive encryption.', 'duplicator'),
                fn(): string => self::getZipArchiveEncryptionFixHint(),
                DUPLICATOR_BLOG_URL . 'how-to-encrypt-backup/'
            ),
            new Requirement(
                self::REQ_SHELL_ZIP_BINARY,
                static fn(): string => __('Shell zip binary', 'duplicator'),
                fn(): bool => ShellZipUtils::getShellExecZipPath() != null,
                static fn(): string => __('The shell zip binary isn\'t available on this server.', 'duplicator'),
                static fn(): string => __(
                    'Enable PHP shell functions and install the zip binary, or switch to another Archive Engine.',
                    'duplicator'
                ),
                DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-work-with-the-different-zip-engines'
            ),
            new Requirement(
                self::REQ_OPENSSL,
                static fn(): string => __('PHP OpenSSL module', 'duplicator'),
                fn(): bool => DupArchive::isEncryptionAvaliable(),
                static fn(): string => __('The PHP OpenSSL module isn\'t enabled on this server.', 'duplicator'),
                fn(): string => TplMng::getInstance()->render('parts/requirements/openssl_fix_hint', [], false),
                'https://www.php.net/manual/en/book.openssl.php'
            ),
            new Requirement(
                self::REQ_MYSQLDUMP_BINARY,
                static fn(): string => __('mysqldump binary', 'duplicator'),
                fn(): bool => WpDbUtils::getMySqlDumpPath() !== false,
                static fn(): string => __('The mysqldump binary isn\'t available on this server.', 'duplicator'),
                static fn(): string => __(
                    'Set a custom mysqldump path in the database settings or switch to the PHP database dump engine.',
                    'duplicator'
                ),
                'https://dev.mysql.com/doc/refman/en/mysqldump.html'
            ),
        ];
    }

    /**
     * Fix hint for the ZipArchive encryption requirement: the options to
     * enable the feature, archive settings deep link included
     *
     * @return string HTML
     */
    private static function getZipArchiveEncryptionFixHint(): string
    {
        return TplMng::getInstance()->render(
            'parts/requirements/ziparchive_encryption_fix_hint',
            [
                'settingsLink' => ControllersManager::getMenuLink(
                    ControllersManager::SETTINGS_SUBMENU_SLUG,
                    SettingsPageController::L2_SLUG_PACKAGE
                ),
            ],
            false
        );
    }

    /**
     * Baseline requirements for the PHP runtime, filesystem and database
     *
     * @return Requirement[]
     */
    private static function getBaselineRequirements(): array
    {
        return [
            new Requirement(
                self::REQ_PHP_64BIT,
                static fn(): string => __('64-bit PHP', 'duplicator'),
                static fn(): bool => (bool) apply_filters('duplicator_requirement_php_64bit', PHP_INT_SIZE >= 8),
                static fn(): string => __(
                    'Your server is running a 32-bit version of PHP.
                    Starting with Duplicator 5.0, creating backups requires 64-bit PHP.
                    Contact your hosting provider and ask them to enable 64-bit PHP for this site before creating a backup.',
                    'duplicator'
                )
            ),
            new Requirement(
                self::REQ_HOME_READABLE,
                static fn(): string => __('Home path readable', 'duplicator'),
                function (): bool {
                    $homePath = WpArchiveUtils::getArchiveListPaths('home');
                    if (strlen($homePath) === 0) {
                        $homePath = DIRECTORY_SEPARATOR;
                    }
                    if (($handle = @opendir($homePath)) === false) {
                        return false;
                    }
                    @closedir($handle);
                    return true;
                },
                static fn(): string => __('The site home path can\'t be opened.', 'duplicator'),
                static fn(): string => __('Check the file permissions of the site root directory.', 'duplicator')
            ),
            new Requirement(
                self::REQ_SSDIR_WRITABLE,
                static fn(): string => __('Backups directory writable', 'duplicator'),
                fn(): bool => is_writable(DUPLICATOR_SSDIR_PATH),
                static fn(): string => sprintf(
                    __('The Duplicator Backups directory (%s) isn\'t writable.', 'duplicator'),
                    DUPLICATOR_SSDIR_PATH
                ),
                static fn(): string => __('Check the file permissions of the Duplicator Backups directory.', 'duplicator')
            ),
            new Requirement(
                self::REQ_SSTMP_WRITABLE,
                static fn(): string => __('Temp directory writable', 'duplicator'),
                fn(): bool => is_writable(DUPLICATOR_SSDIR_PATH_TMP),
                static fn(): string => sprintf(
                    __('The Duplicator temp directory (%s) isn\'t writable.', 'duplicator'),
                    DUPLICATOR_SSDIR_PATH_TMP
                ),
                static fn(): string => __('Check the file permissions of the Duplicator temp directory.', 'duplicator')
            ),
            new Requirement(
                self::REQ_MYSQL_MIN_VERSION,
                static fn(): string => __('MySQL minimum version', 'duplicator'),
                fn(): bool => version_compare(WpDbUtils::getVersion(), self::MYSQL_MIN_VERSION, '>='),
                static fn(): string => sprintf(
                    __('The MySQL server version is lower than the minimum required version %s.', 'duplicator'),
                    self::MYSQL_MIN_VERSION
                )
            ),
            new Requirement(
                self::REQ_MYSQL_ESCAPE,
                static fn(): string => __('MySQL string escaping', 'duplicator'),
                fn(): bool => WpDbUtils::mysqlEscapeTest(),
                static fn(): string => __('The function mysqli_real_escape_string is not escaping strings as expected.', 'duplicator')
            ),
            new Requirement(
                self::REQ_NO_INSTALLER_FILES,
                static fn(): string => __('No leftover installer files', 'duplicator'),
                fn(): bool => count(MigrationMng::checkInstallerFilesList()) === 0,
                static fn(): string => __('Installer file(s) from a previous migration exist on the server.', 'duplicator'),
                fn(): string => TplMng::getInstance()->render('parts/requirements/installer_files_fix_hint', [], false)
            ),
        ];
    }
}
