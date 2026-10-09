<?php

namespace Duplicator\Views;

class UserUIOptions
{
    const USER_UI_OPTION_META_KEY = 'dupli_opt_user_ui_option';

    // Packages options
    const VAL_PACKAGES_PER_PAGE   = 'num_packages_list';
    const VAL_CREATED_DATE_FORMAT = 'created_date_format';
    const VAL_SHOW_COL_NOTE       = 'show_note_column';
    const VAL_SHOW_COL_SIZE       = 'show_size_column';
    const VAL_SHOW_COL_CREATED    = 'show_created_column';
    const VAL_SHOW_COL_AGE        = 'show_age_column';

    // Activity Log options
    const VAL_ACTIVITY_LOG_PER_PAGE = 'activity_log_per_page';

    /** @var ?self */
    private static $instance;

    /** @var int */
    private $userId = 0;
    /** @var array<string,scalar> */
    private $options = [
        self::VAL_PACKAGES_PER_PAGE     => 10,
        self::VAL_CREATED_DATE_FORMAT   => 1,
        self::VAL_SHOW_COL_NOTE         => false,
        self::VAL_SHOW_COL_SIZE         => true,
        self::VAL_SHOW_COL_CREATED      => true,
        self::VAL_SHOW_COL_AGE          => false,
        self::VAL_ACTIVITY_LOG_PER_PAGE => 50,
    ];

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
     * CLass constructor
     */
    protected function __construct()
    {
        $this->userId = get_current_user_id();
        $this->load();
    }

    /**
     * Get the value of an option
     *
     * @param string $option the option name
     *
     * @return scalar
     */
    public function get($option)
    {
        return $this->options[$option] ?? false;
    }

    /**
     * Set the value of an option
     *
     * @param string $option the option name
     * @param scalar $value  the option value
     *
     * @return void
     */
    public function set($option, $value): void
    {
        if (!isset($this->options[$option])) {
            // don't set unknown options
            return;
        }
        $this->options[$option] = $value;
    }

    /**
     * Load the option from meta user table
     *
     * @return void
     */
    protected function load()
    {
        if ($this->userId == 0) {
            return;
        }

        $options = get_user_meta($this->userId, self::USER_UI_OPTION_META_KEY, true);
        if (is_array($options)) {
            foreach (array_keys($this->options) as $option) {
                if (isset($options[$option])) {
                    $this->options[$option] = $options[$option];
                }
            }
        }
    }

    /**
     * Save the option to meta user table
     *
     * @return void
     */
    public function save(): void
    {
        if ($this->userId == 0) {
            return;
        }
        update_user_meta($this->userId, self::USER_UI_OPTION_META_KEY, $this->options);
    }
}
