<?php

defined('ABSPATH') || exit;

define('DUPLICATOR_VERSION', '5.0.5');
define('DUPLICATOR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DUPLICATOR_IMG_URL', DUPLICATOR_PLUGIN_URL . 'assets/img');

if (!defined("DUPLICATOR_DEBUG")) {
    define("DUPLICATOR_DEBUG", false);
}

if (!defined("DUPLICATOR_DEBUG_TPL_OUTPUT_INVALID")) {
    define("DUPLICATOR_DEBUG_TPL_OUTPUT_INVALID", false);
}

if (!defined("DUPLICATOR_DEBUG_TPL_DATA")) {
    define("DUPLICATOR_DEBUG_TPL_DATA", false);
}

if (!defined("DUPLICATOR_FORCE_IMPORT_BRIDGE_MODE")) {
    define("DUPLICATOR_FORCE_IMPORT_BRIDGE_MODE", false);
}

// PATHS
$contentPath = untrailingslashit(wp_normalize_path(realpath(WP_CONTENT_DIR)));

if (!defined("DUPLICATOR_SSDIR_NAME")) {
    define("DUPLICATOR_SSDIR_NAME", 'duplicator-backups');
}
define("DUPLICATOR_SSDIR_PATH", $contentPath . '/' . DUPLICATOR_SSDIR_NAME);
define("DUPLICATOR_SSDIR_URL", content_url() . '/' . DUPLICATOR_SSDIR_NAME);
// Value kept as 'imports' for backward compatibility with existing on-disk directories
define("DUPLICATOR_DEPLOY_DIR_NAME", 'imports');
define("DUPLICATOR_LOGS_DIR_NAME", 'logs');

define("DUPLICATOR_SSDIR_PATH_TMP", DUPLICATOR_SSDIR_PATH . '/tmp');
define("DUPLICATOR_DEPLOY_PATH", DUPLICATOR_SSDIR_PATH . '/' . DUPLICATOR_DEPLOY_DIR_NAME);
define("DUPLICATOR_DEPLOY_URL", DUPLICATOR_SSDIR_URL . '/' . DUPLICATOR_DEPLOY_DIR_NAME);
define("DUPLICATOR_SSDIR_PATH_TMP_IMPORT", DUPLICATOR_SSDIR_PATH_TMP . '/import');
define("DUPLICATOR_SSDIR_PATH_ADDONS", DUPLICATOR_SSDIR_PATH . '/addons');

define("DUPLICATOR_LOGS_PATH", DUPLICATOR_SSDIR_PATH . '/' . DUPLICATOR_LOGS_DIR_NAME);
define("DUPLICATOR_LOGS_URL", DUPLICATOR_SSDIR_URL . '/' . DUPLICATOR_LOGS_DIR_NAME);

define("DUPLICATOR_SSDIR_PATH_INSTALLER", DUPLICATOR_SSDIR_PATH . '/installer');
define('DUPLICATOR_LOCAL_OVERWRITE_PARAMS', 'duplicator_params_overwrite');

// MATCH generic archive pattern, matches[1] is archive name and hash
define('DUPLICATOR_GEN_FILE_REGEX_PATTERN', '/^(.+_[a-z0-9]{7,}_[0-9]{14})_.+\\.(?:zip|daf|php|bak)$/');
// MATCH all file related to the package, archive installer and log
define('DUPLICATOR_FULL_GEN_BACKUP_FILE_REGEX_PATTERN', '/^(.+_[a-z0-9]{20}_[0-9]{14})_.+\\.(?:zip|daf|php|bak|txt|sql|json)$/');
// MATCH archive pattern, matches[1] is archive name and hash
define('DUPLICATOR_ARCHIVE_REGEX_PATTERN', '/^(.+_[a-z0-9]{7,}_[0-9]{14})_archive\\.(?:zip|daf)$/');
// MATCH installer.php installer-backup.php and full installer with hash
define('DUPLICATOR_INSTALLER_REGEX_PATTERN', '/^(?:.+_[a-z0-9]{7,}_[0-9]{14}_)?installer(?:-backup)?\\.php$/');
// MATCH dup-installer and dup-installer-[HASH]
define('DUPLICATOR_INSTALLER_FOLDER_REGEX_PATTERN', '/^dup-installer(?:-[a-z0-9]{7,}-[0-9]{8})?$/');
define('DUPLICATOR_INSTALLER_BOOTLOG_REGEX_PATTERN', '/^dup-installer-bootlog__[a-z0-9]{7,}-[0-9]{8}.txt$/');
define('DUPLICATOR_INSTALLER_OWRPARAM_REGEX_PATTERN', '/^' . DUPLICATOR_LOCAL_OVERWRITE_PARAMS . '_[a-z0-9]{7,}-[0-9]{8}.json$/');
define('DUPLICATOR_CLOUD_DOWNLOADER_REGEX_PATTERN', '/^dup_cloud_downloader_[0-9]{12}_[a-zA-Z0-9]{8,}\.php$/');
define('DUPLICATOR_CLOUD_DOWNLOADER_FOLDER_REGEX_PATTERN', '/^dup_cloud_downloader_data_[0-9]{12}_[a-zA-Z0-9]{8,}$/');
define("DUPLICATOR_DUMP_PATH", DUPLICATOR_SSDIR_PATH . '/dump');
define("DUPLICATOR_ORIG_FOLDER_PREFIX", 'original_files_');
define('DUPLICATOR_LIB_PATH', DUPLICATOR____PATH . '/lib');
define('DUPLICATOR_CERT_PATH', apply_filters('duplicator_certificate_path', DUPLICATOR____PATH . '/src/Libs/Certificates/cacert.pem'));

// For compatibility to an older WP
if (!defined('KB_IN_BYTES')) {
    define('KB_IN_BYTES', 1024);
}
if (!defined('MB_IN_BYTES')) {
    define('MB_IN_BYTES', 1024 * KB_IN_BYTES);
}
if (!defined('GB_IN_BYTES')) {
    define('GB_IN_BYTES', 1024 * MB_IN_BYTES);
}

if (!defined('MONTH_IN_SECONDS')) {
    define('MONTH_IN_SECONDS', 30 * DAY_IN_SECONDS);
}

if (!defined('SECONDS_IN_MICROSECONDS')) {
    define('SECONDS_IN_MICROSECONDS', 1000000);
}

//RESTRAINT CONSTANTS
define('DUPLICATOR_PHP_MAX_MEMORY', 4 * GB_IN_BYTES);
define("DUPLICATOR_MIN_MEMORY_LIMIT", '256M');
define("DUPLICATOR_DB_MAX_TIME", 5000);
define("DUPLICATOR_DB_EOF_MARKER", 'DUPLICATOR_MYSQLDUMP_EOF');
define("DUPLICATOR_INSTALLER_EOF_MARKER", 'DUPLICATOR_INSTALLER_EOF');
define("DUPLICATOR_DB_MYSQLDUMP_ERROR_CONTAINING_LINE_COUNT", 10);
define("DUPLICATOR_DB_MYSQLDUMP_ERROR_CHARS_IN_LINE_COUNT", 1000);
define("DUPLICATOR_SCAN_SITE_ZIP_ARCHIVE_WARNING_SIZE", 350 * MB_IN_BYTES);
define("DUPLICATOR_SCAN_SITE_WARNING_SIZE", 1.5 * GB_IN_BYTES);

define("DUPLICATOR_SCAN_WARN_FILE_SIZE", 4 * MB_IN_BYTES);
define("DUPLICATOR_SCAN_WARN_DIR_SIZE", 100 * MB_IN_BYTES);
define("DUPLICATOR_SCAN_CACHESIZE", 1 * MB_IN_BYTES);
define("DUPLICATOR_SCAN_DB_ALL_SIZE", 100 * MB_IN_BYTES);
define("DUPLICATOR_SCAN_DB_ALL_ROWS", 1000000); //1 million rows
define('DUPLICATOR_SCAN_DB_TBL_ROWS', 100000); //100K rows per table
define('DUPLICATOR_SCAN_DB_TBL_SIZE', 10 * MB_IN_BYTES);
define("DUPLICATOR_SCAN_TIMEOUT", 25); //Seconds
define("DUPLICATOR_SCAN_MAX_UNREADABLE_COUNT", 1000);
define("DUPLICATOR_MAX_FAILURE_COUNT", 1000);
define("DUPLICATOR_BUFFER_DOWNLOAD_SIZE", 4377); // BYTES
define("DUPLICATOR_DEFAULT_CHUNK_UPLOAD_SIZE", 1024); // KB
define('DUPLICATOR_SQL_SCRIPT_PHP_CODE_MULTI_THREADED_MAX_RETRIES', 10);
define("DUPLICATOR_FTP_CURL_CHUNK_SIZE", 2 * MB_IN_BYTES);

define("DUPLICATOR_MIN_SIZE_DBFILE_WITHOUT_FILTERS", 5120); // SQL CHECK:  File should be at minimum 5K.
// A base WP install with only Create tables is about 9K
define("DUPLICATOR_MIN_SIZE_DBFILE_WITH_FILTERS", 800);

define('DUPLICATOR_OPTS_DELETE', [
    'dupli_opt_ui_view_state',
    'dupli_opt_settings',
]);

define("DUPLICATOR_GLOBAL_FILE_FILTERS_ON", true);
define("DUPLICATOR_GLOBAL_DIR_FILTERS_ON", true);

/* TRANSIENT OPTIONS */
define('DUPLICATOR_FRONTEND_TRANSIENT', 'dupli_opt_frotend_delay');
define('DUPLICATOR_FRONTEND_ACTION_DELAY', 1 * MINUTE_IN_SECONDS);

define('DUPLICATOR_INSTALLER_RENAME_KEY', 'rename_delay');
define('DUPLICATOR_INSTALLER_RENAME_DELAY', 12 * HOUR_IN_SECONDS);

define('DUPLICATOR_PENDING_CANCELLATION_TRANSIENT', 'dupli_opt_pending_cancellations');
define('DUPLICATOR_PENDING_CANCELLATION_TIMEOUT', 1 * DAY_IN_SECONDS);

define('DUPLICATOR_TMP_CLEANUP_CHECK_KEY', 'tmp_cleanup_check');
define('DUPLICATOR_TMP_CLEANUP_CHECK_DELAY', 1 * DAY_IN_SECONDS);

/* TODO: Replace all target opening up in different target with the common help target */
define('DUPLICATOR_HELP_TARGET', '_sc-help');

/* Help URLs */
/* TODO: search for these URLs throughout the code and replace with the corresponding define */
define('DUPLICATOR_BLOG_URL', 'https://duplicator.com/');
define('DUPLICATOR_DUPLICATOR_DOCS_URL', DUPLICATOR_BLOG_URL . 'knowledge-base/');
define('DUPLICATOR_USER_GUIDE_URL', DUPLICATOR_DUPLICATOR_DOCS_URL);
define('DUPLICATOR_TECH_FAQ_URL', DUPLICATOR_BLOG_URL . 'knowledge-base-article-categories/troubleshooting/');
define('DUPLICATOR_DRAG_DROP_GUIDE_URL', DUPLICATOR_BLOG_URL . 'how-to-migrate-wordpress-site/');

if (!defined('DUPLICATOR_STORE_URL')) {
    define('DUPLICATOR_STORE_URL', 'https://duplicator.com');
}

if (!defined('DUPLICATOR_INDEX_INCLUDE_HASH')) {
    define('DUPLICATOR_INDEX_INCLUDE_HASH', false);
}

if (!defined('DUPLICATOR_INDEX_INCLUDE_INSTALLER_FILES')) {
    define('DUPLICATOR_INDEX_INCLUDE_INSTALLER_FILES', true);
}

if (!defined('DUPLICATOR_AUTH_KEY')) {
    define('DUPLICATOR_AUTH_KEY', '');
}

if (!defined('DUPLICATOR_CAPABILITIES_RESET')) {
    define('DUPLICATOR_CAPABILITIES_RESET', false);
}

if (!defined('DUPLICATOR_CUSTOM_STATS_REMOTE_HOST')) {
    define('DUPLICATOR_CUSTOM_STATS_REMOTE_HOST', '');
}

if (!defined('DUPLICATOR_USTATS_DISALLOW')) {
    define('DUPLICATOR_USTATS_DISALLOW', false);
}
if (!defined('DUPLICATOR_USTATS_URL')) {
    define('DUPLICATOR_USTATS_URL', 'https://duplicator-telemetry.seedprod-account.workers.dev');
}
if (!defined('DUPLICATOR_PRIMARY_OAUTH_SERVER')) {
    define('DUPLICATOR_PRIMARY_OAUTH_SERVER', 'https://connect.duplicator.com');
}
if (!defined('DUPLICATOR_SECONDARY_OAUTH_SERVER')) {
    define('DUPLICATOR_SECONDARY_OAUTH_SERVER', 'https://connect2.duplicator.com');
}
if (!defined('DUPLICATOR_NOTIFICATIONS_DOMAIN')) {
    define('DUPLICATOR_NOTIFICATIONS_DOMAIN', 'https://notifications.duplicator.com');
}
if (!defined('DUPLICATOR_DISABLE_CAP_BASIC')) {
    define('DUPLICATOR_DISABLE_CAP_BASIC', false);
}
if (!defined('DUPLICATOR_DISABLE_CAP_CREATE')) {
    define('DUPLICATOR_DISABLE_CAP_CREATE', false);
}
if (!defined('DUPLICATOR_DISABLE_CAP_STORAGE')) {
    define('DUPLICATOR_DISABLE_CAP_STORAGE', false);
}
if (!defined('DUPLICATOR_DISABLE_CAP_BACKUP_RESTORE')) {
    define('DUPLICATOR_DISABLE_CAP_BACKUP_RESTORE', false);
}
if (!defined('DUPLICATOR_DISABLE_CAP_EXPORT')) {
    define('DUPLICATOR_DISABLE_CAP_EXPORT', false);
}
if (!defined('DUPLICATOR_DISABLE_CAP_SETTINGS')) {
    define('DUPLICATOR_DISABLE_CAP_SETTINGS', false);
}
if (!defined('DUPLICATOR_TABLE_VALIDATION_FILTER_LIST')) {
    // It could be a string for single table or an array for multiple tables
    define('DUPLICATOR_TABLE_VALIDATION_FILTER_LIST', []);
}

if (!defined('DUPLICATOR_FORCE_TRACE_LOG_ENABLED')) {
    define('DUPLICATOR_FORCE_TRACE_LOG_ENABLED', false);
}
