<?php
namespace api\v01;

class Excl_Utility {
	public function get_slug_from_path($path) {
		$path = substr($path, strlen($this->base)); // Take off the prefix
		$slash_pos = strpos($path, '/');
		if ($slash_pos > 0) { // If there's a slash in the string
			$path = substr($path, 0, strpos($path, '/')); // Go until the next slash to get the slug
		}

		// Only return the slug if it's part of our defined endpoint slugs
		return (in_array($path, $this->endpoint_slugs)) ? $path : false;
	}

	public function load_class_from_slug($slug) {
		require_once dirname(__FILE__) . '/' . $slug . '.php';
		$class = __NAMESPACE__ . "\\EXCL_" . ucfirst($slug);
		$klass = new $class($this->server);
		return $klass;
	}

	/** Takes either a WP_Post or an array of WP_Posts and converts them all to arrays */
	public function transform_WP_object_to_array($object) {
		if (is_array($object)) {
			$new_array = array();
			foreach($object as $sub_object) {
				$new_array[] = $this->transform_WP_object_to_array($sub_object);
			}
			return $new_array;
		} else {
			return (array) $object;
		}
	}

	public function clean_post($post) {
		foreach($post as $key => $value) {
			// If it's a single-member string array, collapse it to just a string
			if (is_array($value) && count($value) == 1 && is_string($value[0])) {
				$post[$key] = $value[0];
			}
			
			// If it's a quoted boolean value, collapse it to a real boolean
			if ($value === "true")	{ $post[$key] = true; }
			if ($value === "false")	{ $post[$key] = false; }
		}
		return $post;
	}

	public function whitelist_post($post, $whitelist)
    {
		$whitelisted_post = array();
		foreach($whitelist as $white)
        {
			$whitelisted_post[current($white)] = $this->valueOr($post[key($white)], false);
		}
		return $whitelisted_post;
	}

    public function merge_original_and_translated_post($original_post, $translated_post, $attributes_to_merge)
    {
        $merged_post = $original_post;
        foreach($original_post as $post_key => $post_value)
        {
			if (in_array($post_key, $attributes_to_merge)) {
				$merged_post[$post_key] = $translated_post[$post_key];
			}
        }
        return $merged_post;
    }

	/* If $value doesn't exist, return $or */
	public function valueOr($value, $or) {
		return ($value) ? $value : $or;
	}
	 
	 private function sortByOrderNumber($a, $b) {
	   if (array_key_exists("sort_order", $a) && array_key_exists("sort_order", $b))
	   {
			$sortOrderA = intval($a['sort_order']);
			$sortOrderB = intval($b['sort_order']);
			if ($sortOrderA == $sortOrderB)
			{
				return 0;
			}
			else
			{
				return $sortOrderA < $sortOrderB ? -1 : 1;
			}
	   }
	   return 0;
	 }
	 
	 public function sort_posts_by_order($posts){
		usort($posts, array($this, 'sortByOrderNumber'));
		return $posts;
	 }
}