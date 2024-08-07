<?php

namespace FileBird\Controller;

use FileBird\Classes\Helpers;
use FileBird\Model\SettingModel;

defined( 'ABSPATH' ) || exit;

class SettingController {
    public function setSettings( \WP_REST_Request $request ) {
        $params = Helpers::sanitize_array( $request->get_params() );

        SettingModel::getInstance()->setSettings( $params );

		return rest_ensure_response( true );
    }
}
