<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Libs\OneClickUpgrade;

use WP_Upgrader_Skin;

class UpgraderSkin extends WP_Upgrader_Skin
{
    /**
     * @param array<string, mixed> $args Constructor args forwarded to parent
     */
    public function __construct($args = [])
    {
        parent::__construct($args);
    }

    /**
     * @param object $upgrader Upgrader instance (passed by reference)
     *
     * @return void
     */
    public function set_upgrader(&$upgrader) // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        if (is_object($upgrader)) {
            $this->upgrader =& $upgrader;
        }
    }

    /**
     * @param object $result Install process result
     *
     * @return void
     */
    public function set_result($result) // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        $this->result = $result;
    }

    /**
     * @return void
     */
    public function header()
    {
    }

    /**
     * @return void
     */
    public function footer()
    {
    }

    /** @var string[] */
    private array $errorMessages = [];

    /**
     * @param array<int, string>|string|\WP_Error $errors Error list, message, or WP_Error
     *
     * @return void
     */
    public function error($errors)
    {
        if (is_wp_error($errors)) {
            foreach ($errors->get_error_messages() as $message) {
                $this->errorMessages[] = (string) $message;
            }
            return;
        }

        if (is_array($errors)) {
            foreach ($errors as $message) {
                if ($message === '' || $message === null) {
                    continue;
                }
                $this->errorMessages[] = (string) $message;
            }
            return;
        }

        if (is_string($errors) && $errors !== '') {
            $this->errorMessages[] = $errors;
        }
    }

    /**
     * @return string[]
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    /**
     * @param string $feedback Feedback string template
     * @param mixed  ...$args  Printf-style arguments substituted into the template
     *
     * @return void
     */
    public function feedback($feedback, ...$args)
    {
    }
}
