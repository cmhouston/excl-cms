<?php
/*
Plugin Name: WP-API Custom Extension
Plugin URI: https://github.com/baylorcollegeofmedicine/corporate-care/tree/master/wordpress/plugins/wp-api-custom
Description: Adds custom endpoints to WP-API JSON API (including Ratings and Comments)
Version: 1.0
Author: Baylor College of Medicine
*/

include_once( dirname( __FILE__ ) . '/Wrappers/JsonServerWrapper.php');
include_once( dirname( __FILE__ ) . '/Wrappers/WordPressWrapper.php');
include_once( dirname( __FILE__ ) . '/Ports/DataTransfer/Rating.php');
include_once( dirname( __FILE__ ) . '/UseCases/Rater.php');
include_once( dirname( __FILE__ ) . '/UseCases/Commenter.php');
include_once(dirname(__FILE__) . '/UseCases/PartnerCommenter.php');
include_once( dirname( __FILE__ ) . '/Web/Router.php');

if (function_exists('wls_register')) {
    wls_register('ratings', 'Inside the wp-api-ratings plugin');
}

if (class_exists('WP_API_Custom\Web\Router'))
{
    $wordpress = new WP_API_Custom\Wrappers\WordPressWrapper();
    $jsonServer = new WP_API_Custom\Wrappers\JsonServerWrapper();
    $rater = new WP_API_Custom\UseCases\Rater($wordpress);
    $commenter = new WP_API_Custom\UseCases\Commenter($wordpress);
    $partnerCommenter = new WP_API_Custom\UseCases\PartnerCommenter($wordpress);
    $pluginInstance = new WP_API_Custom\Web\Router($wordpress, $jsonServer, $rater, $commenter, $partnerCommenter);
    add_action( 'wp_json_server_before_serve', array($pluginInstance, 'initialize'));
}