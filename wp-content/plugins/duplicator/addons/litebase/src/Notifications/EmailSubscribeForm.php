<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Notifications;

use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapWP;
use Exception;

class EmailSubscribeForm
{
    const SUBSCRIBED_OPT_KEY   = 'dupli_opt_litebase_email_subscribed';
    const SUBSCRIBE_NONCE_KEY  = 'duplicator_litebase_email_subscribe';
    const REMOTE_SUBSCRIBE_URL = 'https://duplicator.com/?lite_email_signup=1';

    /**
     * @return void
     */
    public static function init(): void
    {
        add_action('duplicator_scan_footer', [self::class, 'display']);
    }

    /**
     * @return void
     */
    public static function display(): void
    {
        $user              = wp_get_current_user();
        $alreadySubscribed = self::isSubscribed();

        TplMng::getInstance()->render('litebase/scan/education-box', [
            'subscribed'     => $alreadySubscribed,
            'email'          => is_object($user) ? (string) $user->user_email : '',
            'subscribeNonce' => wp_create_nonce(self::SUBSCRIBE_NONCE_KEY),
        ]);
    }

    /**
     * @return bool
     */
    public static function isSubscribed(): bool
    {
        return (bool) get_user_meta(get_current_user_id(), self::SUBSCRIBED_OPT_KEY, true);
    }

    /**
     * Subscribe the current user's email to the duplicator.com mailing list.
     *
     * @param string|null $email when null, reads `email` from POST
     *
     * @return bool
     *
     * @throws Exception when the email is invalid or the remote endpoint fails
     */
    public static function subscribe(?string $email = null): bool
    {
        if (self::isSubscribed()) {
            return true;
        }

        if ($email === null) {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE);
        } else {
            $email = filter_var($email, FILTER_VALIDATE_EMAIL) ?: null;
        }
        if ($email === null) {
            throw new Exception('Invalid email');
        }

        $response = wp_remote_post(self::REMOTE_SUBSCRIBE_URL, [
            'method'  => 'POST',
            'timeout' => 45,
            'body'    => ['email' => $email],
        ]);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            $errorMsg = is_wp_error($response)
                ? $response->get_error_code() . ': ' . $response->get_error_message()
                : 'HTTP ' . wp_remote_retrieve_response_code($response);
            throw new Exception($errorMsg);
        }

        return update_user_meta(get_current_user_id(), self::SUBSCRIBED_OPT_KEY, true) !== false;
    }

    /**
     * @return bool
     */
    public static function resetSubscribedState(): bool
    {
        return SnapWP::deleteUserMetaKey(self::SUBSCRIBED_OPT_KEY);
    }
}
