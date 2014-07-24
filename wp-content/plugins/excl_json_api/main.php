<?php
/*
Plugin Name: ExCl JSON API
Plugin URI: 
Description: Custom JSON endpoints for the ExCL app
Version: 01
Author: Pariveda Solutions
Author URI: http://www.parivedasolutions.com
License: GPLv3
*/

namespace api;

$active_apis = array('01');

foreach($active_apis as $version) {
	include_once dirname(__FILE__) . '/' . $version . '/main.php';
	add_action('wp_json_server_before_serve', array(__NAMESPACE__ . "\\v" . $version . '\EXCL_API', 'api_init'));
}

include_once dirname(__FILE__) . '/customize_admin_menus.php';

function add_query_vars_filter( $vars ){
    $vars[] = "language";
	$vars[] = "view_unpublished_posts";
	
    return $vars;
}
add_filter( 'query_vars', __NAMESPACE__ . "\\" . 'add_query_vars_filter' );