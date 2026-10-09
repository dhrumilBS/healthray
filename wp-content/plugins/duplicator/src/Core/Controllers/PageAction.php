<?php

namespace Duplicator\Core\Controllers;

use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Libs\Snap\SnapUtil;
use Throwable;

/**
 * Action page class
 */
class PageAction
{
    /** @var string */
    protected $key;
    /** @var callable */
    protected $callback;
    /** @var string[] */
    protected $menuSlugs = [];
    /** @var string */
    protected $innerPage = '';
    /** @var bool|string */
    protected $capatibility = true;

    /**
     * Class constructor
     *
     * @param string      $key          action key
     * @param callable    $callback     action callback
     * @param string[]    $menuSlugs    page where the action is active
     * @param string      $innerPage    current inner page if defined
     * @param bool|string $capatibility item capability, true don't check
     */
    public function __construct(
        $key,
        $callback,
        $menuSlugs = [],
        $innerPage = '',
        $capatibility = true
    ) {
        if (strlen($key) == 0) {
            throw new \Exception('action key can\'t be empty');
        }

        if (!is_callable($callback)) {
            throw new \Exception('action callback have to be callable function');
        }

        if (!is_array($menuSlugs) || count($menuSlugs) == 0) {
            throw new \Exception('menuSlugs have to be array with at least one element');
        }

        $this->key          = $key;
        $this->callback     = $callback;
        $this->menuSlugs    = $menuSlugs;
        $this->innerPage    = $innerPage;
        $this->capatibility = $capatibility;
    }

    /**
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Return action nonce key
     *
     * @return string
     */
    public function getNonceKey(): string
    {
        $result = 'dup_nonce_';
        foreach ($this->menuSlugs as $slug) {
            $result .= $slug . '_';
        }

        return str_replace(['-', '.', '\\', '/'], '_', $result . $this->key);
    }

    /**
     * Creates a cryptographic token tied to a specific action, user, user session,
     * and window of time.
     *
     * @return string The token.
     */
    public function getNonce()
    {
        return wp_create_nonce($this->getNonceKey());
    }

    /**
     * Get base action URL, without action key and nonce
     *
     * @param bool $relative if true return relative path or absolute
     *
     * @return string
     */
    public function getBaseUrl($relative = true): string
    {
        $data = [];
        if (strlen($this->innerPage) > 0) {
            $data[ControllersManager::QUERY_STRING_INNER_PAGE] = $this->innerPage;
        }

        return ControllersManager::getMenuLink(
            $this->menuSlugs[0],
            ($this->menuSlugs[1] ?? null),
            ($this->menuSlugs[2] ?? null),
            $data,
            $relative
        );
    }

    /**
     * Get action URL with action key and nonce
     *
     * @param array<string, mixed> $extraData extra value in query string key=val
     * @param bool                 $relative  if true return relative path or absolute
     *
     * @return string
     */
    public function getUrl($extraData = [], $relative = true): string
    {
        $data = [
            ControllersManager::QUERY_STRING_MENU_KEY_ACTION => $this->key,
            '_wpnonce'                                       => $this->getNonce(),
        ];
        if (strlen($this->innerPage) > 0) {
            $data[ControllersManager::QUERY_STRING_INNER_PAGE] = $this->innerPage;
        }

        $data = array_merge($data, $extraData);

        return ControllersManager::getMenuLink(
            $this->menuSlugs[0],
            ($this->menuSlugs[1] ?? null),
            ($this->menuSlugs[2] ?? null),
            $data,
            $relative
        );
    }

    /**
     * Get input hidden element with nonce action field
     *
     * @param bool $echo if true echo nonce field else return string
     *
     * @return string
     */
    public function getActionNonceFileds($echo = true)
    {
        ob_start();
        wp_nonce_field($this->getNonceKey());
        echo '<input type="hidden" class="dup-action-form-action" name="' . esc_attr(ControllersManager::QUERY_STRING_MENU_KEY_ACTION)
            . '" value="' . esc_attr($this->key) . '" >';
        if ($echo) {
            ob_end_flush();
            return '';
        } else {
            return ob_get_clean();
        }
    }

    /**
     * Return true if current page is the page of current action
     *
     * @param string[] $currentMenuSlugs Current page menu levels slugs
     *
     * @return boolean
     */
    public function isPageOfCurrentAction($currentMenuSlugs): bool
    {
        foreach ($this->menuSlugs as $index => $slug) {
            if (!isset($currentMenuSlugs[$index]) || $currentMenuSlugs[$index] != $slug) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return true if current action is called
     *
     * @param string[] $currentMenuSlugs Current page menu levels slugs
     * @param string   $currentInnerPage Current inner page
     * @param string   $action           Action to check
     *
     * @return boolean
     */
    public function isCurrentAction($currentMenuSlugs, $currentInnerPage, $action): bool
    {
        if ($action !== $this->key) {
            return false;
        }

        foreach ($this->menuSlugs as $index => $slug) {
            if (!isset($currentMenuSlugs[$index]) || $currentMenuSlugs[$index] != $slug) {
                return false;
            }
        }

        if (strlen($this->innerPage) > 0 && $this->innerPage !== $currentInnerPage) {
            return false;
        }

        return true;
    }

    /**
     * Verify action nonce
     *
     * @see wp_verify_nonce WordPress function
     *
     * @return int|false 1 if the nonce is valid and generated between 0-12 hours ago,
     *                   2 if the nonce is valid and generated between 12-24 hours ago.
     *                   False if the nonce is invalid.
     */
    protected function verifyNonce()
    {
        $nonce = SnapUtil::sanitizeDefaultInput(SnapUtil::INPUT_REQUEST, '_wpnonce', false);
        return wp_verify_nonce($nonce, $this->getNonceKey());
    }

    /**
     * Exect callback action
     *
     * @param array<string,mixed> $resultData generic allaray where put addtional action data
     *
     * @return bool
     */
    public function exec(&$resultData = [])
    {
        $result = true;
        try {
            if (!$this->verifyNonce() || !$this->userCan()) {
                throw new \Exception('Security issue on action ' . $this->key);
            }
            /** @var array<string,mixed> */
            $funcResultData = call_user_func($this->callback);
            $resultData     = array_merge($resultData, $funcResultData);
        } catch (Throwable $e) {
            $resultData['actionsError']  = true;
            $resultData['errorMessage'] .= '<b>' . $e->getMessage() . '</b><pre>' . SnapLog::getTextException($e, false) . '</pre>';
            $result                      = false;
        }
        return $result;
    }

    /**
     * Check if user can see this item
     *
     * @return bool
     */
    public function userCan()
    {
        if ($this->capatibility === true) {
            return true;
        }
        return CapMng::can($this->capatibility, false);
    }
}
