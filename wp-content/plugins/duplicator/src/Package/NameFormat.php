<?php

namespace Duplicator\Package;

use Duplicator\Models\TemplateEntity;

class NameFormat
{
    const FORMATS = [
        'year',
        'month',
        'day',
        'hour',
        'minute',
        'second',
        'domain',
        'sitetitle',
        'templatename',
    ];

    const FILTER_CHARS = [
        '.',
        '%',
        ':',
        '-',
        ' ',
    ];

    const DEFAULT_FORMAT = '%year%%month%%day%_%sitetitle%';

    /** @var string */
    private $format = self::DEFAULT_FORMAT;
    /** @var int */
    private $templateId = 0;
    /** @var int timestapm */
    private $timestamp = 0;

    /**
     * Class constructor
     *
     * @param string $format The format
     */
    public function __construct($format = '')
    {
        $this->setFormat($format);
        $this->timestamp = time();
    }

    /**
     * Set the format
     *
     * @param string $format The format
     *
     * @return void
     */
    public function setFormat($format): void
    {
        $this->format = (strlen($format) == 0 ? self::DEFAULT_FORMAT : (string) $format);
    }

    /**
     * Set the template id
     *
     * @param int $templateId The template id
     *
     * @return void
     */
    public function setTemplateId($templateId): void
    {
        $this->templateId = (int) $templateId;
    }

    /**
     * Set the timestamp
     *
     * @param int $timestamp The timestamp
     *
     * @return void
     */
    public function setTimestamp($timestamp): void
    {
        if ($timestamp == 0) {
            $timestamp = time();
        }
        $this->timestamp = (int) $timestamp;
    }


    /**
     * Get the name from format
     *
     * @return string
     */
    public function getName()
    {
        $parsed = $this->format;

        if (strpos($parsed, '%year%') !== false) {
            $parsed = str_replace('%year%', gmdate('Y', $this->timestamp), $parsed);
        }

        if (strpos($parsed, '%month%') !== false) {
            $parsed = str_replace('%month%', gmdate('m', $this->timestamp), $parsed);
        }

        if (strpos($parsed, '%day%') !== false) {
            $parsed = str_replace('%day%', gmdate('d', $this->timestamp), $parsed);
        }

        if (strpos($parsed, '%hour%') !== false) {
            $parsed = str_replace('%hour%', gmdate('H', $this->timestamp), $parsed);
        }

        if (strpos($parsed, '%minute%') !== false) {
            $parsed = str_replace('%minute%', gmdate('i', $this->timestamp), $parsed);
        }

        if (strpos($parsed, '%second%') !== false) {
            $parsed = str_replace('%second%', gmdate('s', $this->timestamp), $parsed);
        }

        if (strpos($parsed, '%domain%') !== false) {
            $siteUrl = get_site_url();
            $parsed  = str_replace('%domain%', parse_url($siteUrl, PHP_URL_HOST), $parsed);
        }

        if (strpos($parsed, '%sitetitle%') !== false) {
            $title  = sanitize_title(get_bloginfo('name'));
            $title  = substr(sanitize_file_name($title), 0, 40);
            $parsed = str_replace('%sitetitle%', $title, $parsed);
        }

        if (strpos($parsed, '%templatename%') !== false) {
            $templateName = '';
            if ($this->templateId > 0) {
                $template = TemplateEntity::getById($this->templateId);
                if ($template !== false) {
                    $templateName = $template->name;
                }
            }
            $parsed = str_replace('%templatename%', $templateName, $parsed);
        }

        $parsed = apply_filters('duplicator_name_format_result', $parsed);
        $parsed = str_replace(self::FILTER_CHARS, '', $parsed);

        return sanitize_file_name($parsed);
    }

    /**
     * Get tags description
     *
     * @return array<string, string>
     */
    public static function getTagsDescriptions(): array
    {
        $tags = [
            'year'         => __('Backup Year', 'duplicator'),
            'month'        => __('Backup Month', 'duplicator'),
            'day'          => __('Backup Day', 'duplicator'),
            'hour'         => __('Backup Hour', 'duplicator'),
            'minute'       => __('Backup Minute', 'duplicator'),
            'second'       => __('Backup Second', 'duplicator'),
            'domain'       => __('Site Domain', 'duplicator'),
            'sitetitle'    => __('Site Title', 'duplicator'),
            'templatename' => __('Backup Template Name', 'duplicator'),
        ];

        return apply_filters('duplicator_name_format_tags', $tags);
    }
}
