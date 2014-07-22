<?php
/**
 *
 */
namespace api\v01;

class EXCL_API {
    function api_init( $server ) {
    	$api_version = "01";
        require_once dirname( __FILE__ ) . '/excl_api_controller.php';
        $endpoint_class_name = __NAMESPACE__ . "\\EXCL_API_Controller";
        $endpoint = new $endpoint_class_name( $server );
        $endpoint->set_api_version($api_version);
        add_filter( 'json_endpoints', array( $endpoint, 'register_routes' ) );
    }
}


