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

			// If it's a string version of true or false, convert it to boolean true or false
			$post[$key] = ($value === "false" || $value === "0") ? false : $post[$key]; // TODO look at not using "0" here
			$post[$key] = ($value === "true" || $value === "1") ? true : $post[$key];
		}
		return $post;
	}

	public function whitelist_post($post, $whitelist)
    {
		$whitelisted_post = array();
		foreach($whitelist as &$white)
        {
            $key = array_keys( $white[0] )[0];
            $value = array_values( $white[0] )[0];
            $whitelisted_post[$value] = $this->valueOr($post[$key], false);
		}
		return $whitelisted_post;
	}

    public function translate_post($post, $translation, $whitelist)
    {
        $translated_post = $post;

        foreach($whitelist as &$white)
        {
            $key = array_values( $white[0] )[0];
            $force_inherit = $white['force_inherit'];
            $inherit = $force_inherit || $translation[$key] == false;
            $translated_post[$key] = $inherit ? $post[$key] : $translation[$key];
        }
        return $translated_post;
    }

	/* If $value doesn't exist, return $or */
	public function valueOr($value, $or) {
		return ($value) ? $value : $or;
	}
	 
	 private function sortByOrderNumber($a, $b) {
	   if (array_key_exists("sort_order", $a) && array_key_exists("sort_order", $b))
	   {
			//echo "key exists";
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
//	   echo "key does not exist";
	   //echo print_r($a);
	   return 0;
	 }
	 
	 public function sort_posts_by_order($posts){
		//$posts = array("foo" => 1, "bar" => 2, "baz" => 3);
	 
		usort($posts, array($this, 'sortByOrderNumber'));
		
		return $posts;
	 }
}