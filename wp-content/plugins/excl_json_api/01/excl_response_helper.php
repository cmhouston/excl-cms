<?php
/** API Version 01 */
namespace api\v01;

/** General endpoint class that provides functions common across all api endpoints. */
class Excl_Response_Helper {
	public function prepare_posts_for_response($posts, $slug) {
		$response = new \WP_JSON_Response($this->wrap_json($posts, $slug . 's'));
		return $response;
	}

	public function prepare_post_for_response($post, $slug) {
		$response = new \WP_JSON_Response($this->wrap_json($post, $slug));
		return $response;
	}

	public function wrap_json($data, $slug, $error = "") { // make 'status, error, and data' into objects
		$json = array();
		if (is_string($error) && $error !== "") {
			$json['status'] = "error";
			$json['error'] = $error;
		} else {
			$json['status'] = "ok";
		}

		$json['data'] = array($slug => $data);
		return $json;
	}
}