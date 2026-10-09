<?php

namespace Duplicator\Models\ActivityLog;

use Duplicator\Core\CapMng;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Core\Models\AbstractGenericModel;
use Duplicator\Core\Models\TraitGenericModelList;
use Duplicator\Libs\Snap\SnapWP;
use Exception;
use ReflectionClass;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;
use wpdb;

abstract class AbstractLogEvent extends AbstractGenericModel
{
    use TraitGenericModelList;

    const SEVERITY_INFO    = 10;
    const SEVERITY_WARNING = 20;
    const SEVERITY_ERROR   = 30;

    /** @var int */
    protected int $severity = self::SEVERITY_INFO;
    /** @var string */
    protected string $subType = '';
    /** @var string */
    protected string $title = '';
    /** @var string */
    protected string $shortDescription = '';
    /** @var array<string,mixed> Extra data */
    protected array $data = [];
    /** @var int */
    protected int $parentId = 0;

    /**
     * Return entity type identifier
     *
     * @return string
     */
    abstract public static function getType(): string;

    /**
     * Return entity type label
     *
     * @return string
     */
    abstract public static function getTypeLabel(): string;

    /**
     * Return required capability for this log event
     *
     * @return string
     */
    abstract public static function getCapability(): string;

    /**
     * Return object type label, can be overridden by child classes
     * by default it returns the same as static::getTypeLabel() but can change in base of object properties
     *
     * @return string
     */
    public function getObjectTypeLabel(): string
    {
        return static::getTypeLabel();
    }

    /**
     * Entity table name
     *
     * @param bool $escape If true apply esc_sql to table name
     *
     * @return string
     */
    final public static function getTableName(bool $escape = false): string
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        $res = $wpdb->base_prefix . 'duplicator_activity_logs';
        return ($escape ? esc_sql($res) : $res);
    }

    /**
     * Get severity
     *
     * @return int
     */
    public function getSeverity(): int
    {
        return $this->severity;
    }

    /**
     * Set severity and update the database row if the severity is different
     *
     * @param int $severity New severity
     *
     * @return void
     */
    public function setSeverity(int $severity): void
    {
        if ($severity != $this->severity) {
            $this->severity = $severity;
            $this->save();
        }
    }

    /**
     * Get severity label
     *
     * @return string
     */
    public function getSeverityLabel(): string
    {
        return LogUtils::getSeverityLabels()[$this->severity] ?? 'Unknown';
    }

    /**
     * Get sub type
     *
     * @return string
     */
    public function getSubType(): string
    {
        return $this->subType;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get short description
     *
     * @return string
     */
    abstract public function getShortDescription(): string;

    /**
     * Get data
     *
     * @return array<string,mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Display detailed information in html format
     *
     * @return void
     */
    abstract public function detailHtml(): void;

    /**
     * Get parent ID
     *
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }

    /**
     * Get the Duplicator version that created this log event
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get insert data and formats, can be overridden by child classes
     *
     * @return array{data:array<string,mixed>,formats:string[]}
     */
    protected function getInsertData(): array
    {
        $data                      = parent::getInsertData();
        $data['data']['type']      = static::getType();
        $data['data']['severity']  = $this->severity;
        $data['data']['sub_type']  = $this->subType;
        $data['data']['title']     = $this->title;
        $data['data']['data']      = ''; // First I create a row without an object to generate the id, and then I update the row create
        $data['data']['parent_id'] = $this->parentId;
        $data['formats'][]         = '%s';
        $data['formats'][]         = '%d';
        $data['formats'][]         = '%s';
        $data['formats'][]         = '%s';
        $data['formats'][]         = '%s';
        $data['formats'][]         = '%d';
        return $data;
    }

    /**
     * Get update data and formats, can be overridden by child classes
     *
     * @return array{data:array<string,mixed>,formats:string[]}
     */
    protected function getUpdateData(): array
    {
        $data                      = parent::getUpdateData();
        $data['data']['type']      = static::getType();
        $data['data']['severity']  = $this->severity;
        $data['data']['sub_type']  = $this->subType;
        $data['data']['title']     = $this->title;
        $data['data']['data']      = JsonSerialize::serialize($this->data, JSON_PRETTY_PRINT);
        $data['data']['parent_id'] = $this->parentId;
        $data['formats'][]         = '%s';
        $data['formats'][]         = '%d';
        $data['formats'][]         = '%s';
        $data['formats'][]         = '%s';
        $data['formats'][]         = '%s';
        $data['formats'][]         = '%s';
        $data['formats'][]         = '%d';
        return $data;
    }

    /**
     * Get Model from database row
     *
     * @param array<string,mixed> $row Database row
     *
     * @return self
     */
    protected static function getModelFromRow(array $row)
    {
        $data = JsonSerialize::unserialize($row['data']);
        if (!is_array($data)) {
            throw new Exception('Invalid database json data, data: ' . substr($row['data'], 0, 1000));
        }
        $dbValuesToProps = [
            'id'       => (int) $row['id'],
            'severity' => (int) $row['severity'],
            'subType'  => (string) $row['sub_type'],
            'title'    => (string) $row['title'],
            'data'     => $data,
            'parentId' => (int) $row['parent_id'],
            'version'  => (string) $row['version'],
            'created'  => (string) $row['created_at'],
            'updated'  => (string) $row['updated_at'],
        ];

        $dbValuesToProps['data'] = $data;
        $reflect                 = new ReflectionClass(LogUtils::getClassByType($row['type']));
        $obj                     = $reflect->newInstanceWithoutConstructor();

        foreach ($dbValuesToProps as $propName => $value) {
            $prop = $reflect->getProperty($propName);
            if (PHP_VERSION_ID < 80100) {
                $prop->setAccessible(true);
            }
            $prop->setValue($obj, $value);
        }

        return $obj;
    }

    /**
     * Init entity table
     *
     * @return string[] Strings containing the results of the various update queries.
     */
    final public static function initTable(): array
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name      = static::getTableName(true);

        // PRIMARY KEY must have 2 spaces before for dbDelta to work
        // Mysql 5.5 can't have more than 1 DEFAULT CURRENT_TIMESTAMP
        $sql = <<<SQL
CREATE TABLE `{$table_name}` (
    `id` bigint(20) unsigned NOT null AUTO_INCREMENT,
    `type` varchar(100) NOT NULL,
    `sub_type` varchar(100) NOT NULL,
    `severity` int(8) NOT NULL,
    `title` text NOT NULL,
    `data` longtext NOT null,
    `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0',
    `version` varchar(30) NOT NULL DEFAULT '',
    `created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (`id`),
    KEY `type_idx` (`type`),
    KEY `sub_type_idx` (`sub_type`),
    KEY `severity_idx` (`severity`),
    KEY `created_at` (`created_at`),
    KEY `updated_at` (`updated_at`),
    KEY `version` (`version`)
) {$charset_collate};
SQL;

        return SnapWP::dbDelta($sql);
    }

    /**
     * Get all log types that the current user has capability to view
     *
     * @return string[]
     */
    protected static function getAllowedLogTypes(): array
    {
        $allowedTypes = [];
        $allLogTypes  = LogUtils::getAllLogTypes();

        foreach ($allLogTypes as $type => $label) {
            $logClass = LogUtils::getClassByType($type);
            if (CapMng::can($logClass::getCapability(), false)) {
                $allowedTypes[] = $type;
            }
        }

        return $allowedTypes;
    }

    /**
     * Get list of logs
     *
     * @param array<string,mixed> $args Query arguments
     *
     * @return self[]
     */
    public static function getList(array $args = []): array
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        $defaults = [
            'page'      => 1,
            'per_page'  => 50,
            'type'      => '',
            'severity'  => -1,
            'date_from' => '',
            'date_to'   => '',
            'search'    => '',
            'parent_id' => -1,
            'orderby'   => 'created_at',
            'order'     => 'DESC',
        ];

        $args = wp_parse_args($args, $defaults);

        $table = static::getTableName(true);
        $where = ['1=1'];

        if (!empty($args['type'])) {
            $where[] = $wpdb->prepare('type = %s', $args['type']);
        }

        if ($args['severity'] >= 0) {
            $where[] = $wpdb->prepare('severity = %d', $args['severity']);
        }

        if (!empty($args['date_from'])) {
            $where[] = $wpdb->prepare('created_at >= %s', $args['date_from'] . ' 00:00:00');
        }

        if (!empty($args['date_to'])) {
            $where[] = $wpdb->prepare('created_at <= %s', $args['date_to'] . ' 23:59:59');
        }

        if (!empty($args['search'])) {
            $search  = '%' . $wpdb->esc_like($args['search']) . '%';
            $where[] = $wpdb->prepare('(title LIKE %s)', $search);
        }

        if ($args['parent_id'] >= 0) {
            $where[] = $wpdb->prepare('parent_id = %d', $args['parent_id']);
        }

        // Add capability filtering at SQL level
        $allowedTypes = self::getAllowedLogTypes();
        if (!empty($allowedTypes)) {
            $typePlaceholders = implode(',', array_fill(0, count($allowedTypes), '%s'));
            $where[]          = $wpdb->prepare("type IN ({$typePlaceholders})", ...$allowedTypes);
        } else {
            // If user has no capabilities, return empty result
            return [];
        }

        $whereClause = implode(' AND ', $where);

        $orderby       = (in_array($args['orderby'], ['id', 'created_at', 'updated_at', 'severity']) ? $args['orderby'] : 'created_at');
        $order         = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';
        $orderbyString = $orderby . ' ' . $order;
        if ($args['orderby'] !== 'id') {
            // Sort by id in case of equals timestamps or severity
            $orderbyString .= ', id ' . $order;
        }

        $offset = ($args['page'] - 1) * $args['per_page'];
        $limit  = $args['per_page'];

        $sql     = "SELECT * FROM `{$table}` WHERE {$whereClause} ORDER BY {$orderbyString} LIMIT %d OFFSET %d";
        $results = $wpdb->get_results($wpdb->prepare($sql, $limit, $offset), ARRAY_A);

        if (!is_array($results)) {
            return [];
        }

        $logs = [];
        foreach ($results as $row) {
            try {
                $logs[] = static::getModelFromRow($row);
            } catch (Exception $e) {
                DupLog::traceException($e, "Activity log list query error");
                // Skip invalid rows
                continue;
            }
        }

        return $logs;
    }

    /**
     * Count logs
     *
     * @param array<string,mixed> $args Query arguments
     *
     * @return int
     */
    public static function count(array $args = []): int
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        $defaults = [
            'type'      => '',
            'severity'  => -1,
            'date_from' => '',
            'date_to'   => '',
            'search'    => '',
            'parent_id' => -1,
        ];

        $args = wp_parse_args($args, $defaults);

        $table = static::getTableName(true);
        $where = ['1=1'];

        if (!empty($args['type'])) {
            $where[] = $wpdb->prepare('type = %s', $args['type']);
        }

        if ($args['severity'] >= 0) {
            $where[] = $wpdb->prepare('severity = %d', $args['severity']);
        }

        if (!empty($args['date_from'])) {
            $where[] = $wpdb->prepare('created_at >= %s', $args['date_from'] . ' 00:00:00');
        }

        if (!empty($args['date_to'])) {
            $where[] = $wpdb->prepare('created_at <= %s', $args['date_to'] . ' 23:59:59');
        }

        if (!empty($args['search'])) {
            $search  = '%' . $wpdb->esc_like($args['search']) . '%';
            $where[] = $wpdb->prepare('(title LIKE %s)', $search);
        }

        if ($args['parent_id'] >= 0) {
            $where[] = $wpdb->prepare('parent_id = %d', $args['parent_id']);
        }

        // Add capability filtering at SQL level
        $allowedTypes = self::getAllowedLogTypes();
        if (!empty($allowedTypes)) {
            $typePlaceholders = implode(',', array_fill(0, count($allowedTypes), '%s'));
            $where[]          = $wpdb->prepare("type IN ({$typePlaceholders})", ...$allowedTypes);
        } else {
            // If user has no capabilities, return 0
            return 0;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT COUNT(*) FROM `{$table}` WHERE {$whereClause}";
        return (int) $wpdb->get_var($sql);
    }

    /**
     * Get client IP address with support for proxies and load balancers
     *
     * @return string Client IP address or 'Unknown' if not determinable
     */
    public static function getClientIP(): string
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_REAL_IP',            // Nginx proxy
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR',               // Standard
        ];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                // Handle comma-separated IPs (forwarded for)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                // Validate IP and exclude private/reserved ranges for security
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        // Fallback to REMOTE_ADDR even if it's private (better than nothing)
        return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }

    /**
     * Resolve the current admin screen id for storage.
     *
     * get_current_screen() is only defined in an admin request, so it is
     * unavailable on cron/WP-CLI builds. Falls back to the sanitized request URI.
     *
     * @return string
     */
    protected static function getCurrentScreenId(): string
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        return (string) ($screen ? $screen->id : esc_url_raw($_SERVER['REQUEST_URI'] ?? ''));
    }
}
