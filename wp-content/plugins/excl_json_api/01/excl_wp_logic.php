<?php
namespace api\v01;

require_once(dirname(__FILE__) . '/excl_utility.php');

class Excl_WP_Logic {
    protected $wp_query;
	protected $excl_utility;
	protected $custom_field_prefix = "wpcf-";

	public function __construct() {
        $this->wp_query = &$GLOBALS['wp_query'];
		$this->excl_utility = new Excl_Utility();
	}

	public function load_posts_from_type($type_class) {
		$args = array('post_type' => $type_class->get_slug());
		$posts = $this->excl_utility->transform_WP_object_to_array($this->get_posts_unlimited($args));
		foreach ($posts as &$post) {
			$post = $this->recursive_get_post_with_hierarchy($post, $type_class->get_children_hierarchy());
		}
		return $posts;
	}

	public function load_post_from_type($id, $type_class) {
		$args = array('post_type' => $type_class->get_slug());
		$post = $this->load_post_with_id($id, $args);
		$post = $this->recursive_get_post_with_hierarchy($post, $type_class->get_children_hierarchy());
		return $post;
	}

	public function recursive_get_post_with_hierarchy($post, $hierarchy, $depth = 100) {
		if ($depth <= 0) {
			return new \WP_Error( 'wp_json_too_deep', __( 'Too many levels deep' ), array( 'status' => 404 ) );
		}
		$this_hierarchy = $hierarchy[$post['post_type']];
		foreach($this_hierarchy['children'] as $post_type) {
			$children_posts = $this->get_post_children($post, $post_type);
			$child_name = ($hierarchy[$post_type]['name']) ? $hierarchy[$post_type]['name'] : $post_type . 's';
			foreach($children_posts as $sub_post) {
				$post[$child_name][] = $this->recursive_get_post_with_hierarchy($sub_post, $hierarchy, --$depth);
			}
			if (array_key_exists($child_name, $post))
			{
				$post[$child_name] = $this->excl_utility->sort_posts_by_order($post[$child_name]);
			}
		}

		$post = $this->attach_excl_fields($post);

        // backup post type info before it is stripped and attach language options to museum
        $post_type = $post['post_type'];
        if($post_type == 'museum') $post['lang_options'] = pll_languages_list( array('fields' => 'locale') );

		$post = $this->excl_utility->whitelist_post($post, $this_hierarchy['attributes']);
		$post = $this->excl_utility->clean_post($post);

        $translation = $this->retrieve_translation($post, $post_type, $hierarchy);
        if($translation !== false) $post = $this->excl_utility->translate_post( $post, $translation, $this_hierarchy['attributes'] );

        return $post;
    }

    protected function retrieve_translation($post, $post_type, $hierarchy)
    {
        $lang_slug = $this->wp_query->get('language');

        $translation = false;

        if( !empty($lang_slug) )
        {
            $translation_id = pll_get_post($post['id'], $lang_slug);

            if( $translation_id != 0 && $translation_id != $post['id'] )
            {
                $args = array('post_type' => $post_type);
                $translation = $this->load_post_with_id($translation_id, $args);
                $translation = $this->recursive_get_post_with_hierarchy($translation, $hierarchy);
            }
        }
        return $translation;
    }

	protected function attach_excl_fields($post) {
		$post = $this->attach_post_categories($post);
		$post = array_merge($post, $this->collapse_custom_fields($this->get_custom_fields($post))); //break these into seperate lines
		$post = $this->attach_comments($post);
		$post = $this->merge_types_fields($post);
		return $post;
	}

	public function attach_comments($post) {
		$comments = $this->clean_comments($this->excl_utility->transform_WP_object_to_array($this->get_comments($post)));
		$post = array_merge($post, array('comments' => $comments));
		return $post;
	}

	public function clean_comments($comments) {
		foreach($comments as &$comment) {
			$comment = $this->excl_utility->whitelist_post($comment, array(
                array( array('comment_ID' => 'id'), 'force_inherit' => false),
                array( array('comment_content' => 'body'), 'force_inherit' => false),
                array( array('comment_date' => 'date'), 'force_inherit' => false)
            ));
		}
		return $comments;
	}

	public function attach_post_categories($post) {
		$post_categories = wp_get_post_categories( $post['ID'] );
		$cat_names = array();
		$cat_orders = array();
	
		$cat = null;
		foreach($post_categories as $c){
			$cat = get_category( $c );
			$cat_names[] = $cat->name;
			$cat_orders[] = $cat->term_order;
		}

		if (!empty($cat_names)) {
			$post['categories'] = implode(',', $cat_names);
			$post['term-order'] = implode(',', $cat_orders);
		}
		return $post;
	}

	public function merge_types_fields($post) {
		foreach($post as $key => $value) {
			$possible_types_field = types_render_field( "$key", array('post_id' => $post['ID'], 'output' => 'raw', 'separator' => '|') );
			if ($possible_types_field != "") {
				$post[$key] = $possible_types_field;
			}
		}
		return $post;
	}

	public function load_post_with_id($id, $args = null) {
		return $this->excl_utility->transform_WP_object_to_array(get_post($id, $args));
	}

	public function get_custom_fields($post) {
		$custom_fields = get_post_custom($post['ID']);
		return $custom_fields;
	}

	public function collapse_custom_fields($custom_fields) {
		$custom_keys = array_keys($custom_fields);
		$processed_custom_keys = array();
		$processed_custom_fields = array();

		// Clean up the custom fields to get us the keys for our custom fields
		foreach($custom_keys as $custom_key) {
			// If the custom field starts with our custom prefix
			if (substr($custom_key, 0, strlen($this->custom_field_prefix)) === $this->custom_field_prefix) {
				$processed_custom_keys[$custom_key] = substr($custom_key, strlen($this->custom_field_prefix));
			}
		}

		// Build the custom fields object we want
		foreach($processed_custom_keys as $oldKey => $newKey) {
			$processed_custom_fields[$newKey] = $custom_fields[$oldKey];
		}
		return $processed_custom_fields;
	}

	public function get_comments($post) {
		// Only get approved comments
		$comments = get_comments(array('post_id' => $post['ID'], 'status' => 'approve'));
		return $comments;
	}

	public function get_post_children($post, $child_type) {
		$args = array('post_type' => $child_type, 'meta_query' => array(array('key' => '_wpcf_belongs_' . $post['post_type'] . '_id', 'value' => $post['ID'])));
		$posts = $this->get_posts_unlimited($args);
		$posts = $this->excl_utility->transform_WP_object_to_array($posts);
		return $posts;
	}

	public function get_posts_unlimited($args) {
		$args['numberposts'] = -1;
		$args['order'] = 'ASC';

		$args = $this->add_unpublished_posts_if_flagged($args);
		return get_posts($args);
	}
	
	private function add_unpublished_posts_if_flagged($args){
		
		$view_unpublished_posts_slug = $this->wp_query->get( "view_unpublished_posts");
		
		if( !empty( $view_unpublished_posts_slug ) )
		{
            if($view_unpublished_posts_slug == "true")
			{
				$args['post_status'] = 'any';
			}
		}
		return $args;
	}
}