<?php

namespace Duplicator\Installer\REST;

use VendorDuplicator\WpOrg\Requests\Auth;
use VendorDuplicator\WpOrg\Requests\Auth\Basic;
use VendorDuplicator\WpOrg\Requests\Hooks;

class RESTAuth implements Auth
{
    /** @var string */
    protected $nonce = '';
    /** @var string */
    protected $basicAuthUser = '';
    /** @var string */
    protected $basicAuthPassword = '';

    /**
     * Class constructor
     *
     * @param string $nonce             nonce user
     * @param string $basicAuthUser     auth user
     * @param string $basicAuthPassword auth password
     */
    public function __construct($nonce, $basicAuthUser = "", $basicAuthPassword = "")
    {
        $this->nonce             = $nonce;
        $this->basicAuthUser     = $basicAuthUser;
        $this->basicAuthPassword = $basicAuthPassword;
    }

    /**
     * Register auth hooks
     *
     * @param Hooks $hooks hooks
     *
     * @return void
     */
    public function register(Hooks $hooks): void
    {
        if (strlen($this->basicAuthUser) > 0) {
            $basicAuth = new Basic([
                $this->basicAuthUser,
                $this->basicAuthPassword,
            ]);
            $basicAuth->register($hooks);
        }

        $hooks->register('requests.before_request', [$this, 'beforeRequest']);
    }

    /**
     * Before request hook
     *
     * @param string  $url     request URL
     * @param mixed[] $headers headers
     * @param mixed[] $data    data
     * @param string  $type    type
     * @param mixed[] $options options
     *
     * @return void
     */
    public function beforeRequest(&$url, &$headers, &$data, &$type, &$options): void
    {
        $data['_wpnonce'] = $this->nonce;
        foreach ($_COOKIE as $key => $val) {
            $options['cookies'][$key] = $val;
        }
    }
}
