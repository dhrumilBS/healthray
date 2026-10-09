<?php

/**
 * REST point to get duplicator and WordPress versions
 */

namespace Duplicator\RESTPoints;

use Duplicator\Core\CapMng;

class Versions extends \Duplicator\Core\REST\AbstractRESTPoint
{
    /**
     * return REST point route string
     *
     * @return string
     */
    protected function getRoute(): string
    {
        return '/versions';
    }

    /**
     *
     * @param \WP_REST_Request $request      request data
     * @param mixed[]          $responseBase response base data
     *
     * @return \WP_REST_Response
     */
    protected function respond(\WP_REST_Request $request, $responseBase): \WP_REST_Response // @phpstan-ignore missingType.generics
    {
        global $wp_version;

        $response = $responseBase;

        $response['wp']  = $wp_version;
        $response['dup'] = DUPLICATOR_VERSION;

        return new \WP_REST_Response($response, 200);
    }

    /**
     *
     * @param \WP_REST_Request $request request data
     *
     * @return \WP_Error|boolean
     */
    public function permission(\WP_REST_Request $request) // @phpstan-ignore missingType.generics
    {
        if (!CapMng::can(CapMng::CAP_BASIC, false) || !check_ajax_referer('wp_rest', false, false)) {
            return new \WP_Error('rest_forbidden', esc_html__('You cannot execute this action.', 'duplicator'));
        }
        return true;
    }
}
