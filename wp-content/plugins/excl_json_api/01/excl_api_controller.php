<?php
/** API Version 01 */
namespace api\v01;

require_once(dirname(__FILE__). '/excl_utility.php');
require_once(dirname(__FILE__). '/excl_wp_logic.php');
require_once(dirname(__FILE__). '/excl_response_helper.php');
require_once(dirname(__FILE__). '/Commenter.php');
require_once(dirname(__FILE__). '/WordPressWrapper.php');

/** General endpoint class that provides functions common across all api endpoints. */
class EXCL_API_Controller extends \WP_JSON_CustomPostType {
	protected $plugin_name = "excl";
	protected $api_version;
	protected $custom_field_prefix = "wpcf-";
	protected $base;
	protected $server;
	protected $excl_utility;
	protected $excl_wp_logic;
	protected $response_helper;
	protected $commenter;

	/* The $api_version becomes part of the $base and is eventually used in the endpoint's URL route */
	public function __construct($server) {
		$this->server = $server;
		parent::__construct($server);
		$this->set_api_version('01');
		$this->excl_utility = new Excl_Utility();
		$this->excl_wp_logic = new Excl_WP_Logic();
		$this->response_helper = new Excl_Response_Helper();
		$wordPressWrapper = new WordPressWrapper();
		$this->commenter = new Commenter($wordPressWrapper);
	}

	public function set_api_version($version) {
		$this->api_version = 'v' . $version;
		$this->base = '/' . $this->api_version . '/' . $this->plugin_name . '/';
	}

	public function register_routes($routes) {
		$routes[$this->base . 'museum'] = array(
			array( array( $this, 'get_museums_endpoint'), \WP_JSON_Server::READABLE )
			);
		$routes[$this->base . 'museum/(?P<museum_id>\d+)'] = array(
			array( array( $this, 'get_museum_endpoint'), \WP_JSON_Server::READABLE )
			);
		// $routes[$this->base . 'museum/(?P<museum_id>\d+)/component'] = array(
		// 	array( array( $this, 'get_components_endpoint'), \WP_JSON_Server::READABLE )
		// );
		$routes[$this->base . 'museum/(?P<museum_id>\d+)/component/(?P<component_id>\d+)'] = array(
			array( array( $this, 'get_component_endpoint'), \WP_JSON_Server::READABLE )
			);
		$routes[$this->base . 'museum/(?P<museum_id>\d+)/posts/(?P<post_id>\d+)/comments'] = array(
			array( array( $this, 'create_comment'), \WP_JSON_Server::CREATABLE | \WP_JSON_Server::ACCEPT_JSON)
			);
//        $routes[$this->base . 'museum/(?P<museum_id>\d+)/posts/(?P<post_id>\d+)/comments'] = array(
//            array( array( $this, 'createComment'), \WP_JSON_Server::READABLE)
//        );
		return $routes;
	}

	public function create_comment($data, $museum_id, $post_id)
	{
		$commenter = $this->commenter;
		/*
		$newData = new \stdClass();
		$newData->data = $data;
		$newData->postId = $post_id;
		return $this->doAction($newData, function($data) use (&$commenter) {
			return $commenter->createComment($data->data, $data->post_id);
		});
		*/
		$commenter->createComment($data, $post_id);
		return "yay!!";
	}

	public function get_museums_endpoint() {
		return $this->get_posts_for_type('museum');
	}

	public function get_museum_endpoint($museum_id) {
		return $this->get_post_for_type($museum_id, 'museum');
	}

	public function get_components_endpoint() {
		return $this->get_posts_for_type('component');
	}

	public function get_component_endpoint($museum_id, $component_id) {
		return $this->get_post_for_type($component_id, 'component');
	}

	protected function get_posts_for_type($slug) {
		$post_class = $this->excl_utility->load_class_from_slug($slug);
		$posts = $this->excl_wp_logic->load_posts_from_type($post_class);
		$response = $this->response_helper->prepare_posts_for_response($posts, $slug);
		return $response;
	}

	protected function get_post_for_type($id, $slug) {
		$post_class = $this->excl_utility->load_class_from_slug($slug);
		$post = $this->excl_wp_logic->load_post_from_type($id, $post_class);
		$response = $this->response_helper->prepare_post_for_response($post, $slug);
		return $response;
	}
}