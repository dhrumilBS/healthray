<?php

namespace Duplicator\Views;

use Duplicator\Controllers\ActivityLogPageController;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapUtil;
use WP_Screen;

class ActivityLogScreen extends ScreenBase
{
    /**
     * Class contructor
     *
     * @param string $page Page
     *
     * @return void
     */
    public function __construct($page)
    {
        add_action('load-' . $page, [$this, 'init']);
        add_filter('screen_settings', [$this, 'showOptions'], 10, 2);
    }

    /**
     * Init Activity Log screen
     *
     * @return void
     */
    public function init(): void
    {
        // Optional: Add any initialization logic here
    }

    /**
     * Activity Log List: Screen Options Tab
     *
     * @param string    $screen_settings Screen settings
     * @param WP_Screen $args            Screen args
     *
     * @return string
     */
    public function showOptions($screen_settings, WP_Screen $args)
    {
        // Only display on Activity Log screen and list page
        if (
            !ActivityLogPageController::getInstance()->isCurrentPage() ||
            ActivityLogPageController::getCurrentInnerPage(ActivityLogPageController::INNER_PAGE_LIST) !== ActivityLogPageController::INNER_PAGE_LIST
        ) {
            return $screen_settings;
        }

        return TplMng::getInstance()->render('admin_pages/activity_log/screen_options', [], false);
    }

    /**
     * Set duplicator activity log screen option
     *
     * @param mixed  $screen_option The value to save instead of the option value. Default false (to skip saving the current option).
     * @param string $option        The option name.
     * @param int    $value         The option value.
     *
     * @return bool
     */
    public static function setScreenOptions($screen_option, $option, $value): bool
    {
        $uiOpts = UserUIOptions::getInstance();

        $perPage = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'activity_log_per_page', 50);
        $perPage = max(50, $perPage);  // Minimum 50 entries per page

        $uiOpts->set(UserUIOptions::VAL_ACTIVITY_LOG_PER_PAGE, $perPage);

        $uiOpts->save();

        // Returning false from the filter will skip saving the current option
        return false;
    }
}
