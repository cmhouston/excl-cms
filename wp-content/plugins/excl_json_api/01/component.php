<?php
namespace api\v01;

require_once(dirname(__FILE__) . '/iexcl_type.php');

class EXCL_Component implements iExcl_Type {
    protected $slug = 'component';
    protected $plural_slug = 'components';
    protected $children_hierarchy = array(
        'component' => array(
            'attributes' => array (
                     array('ID' => 'id'),
                     array('sort-order'=> 'sort_order'),
                     array('post_title' => 'name'),
                	 array('posts'=> 'posts')
                ),
            'children' => array('component-post'),
			'force_english_translation_for_attributes' => array( 'id', 'sort_order', 'posts' ),
			'force_retrieved_as_normal' => array()
        ),
        'component-post' => array(
            'attributes' => array(
                array('ID' => 'id'),
                array('post_title' => 'name'),
				array('categories' => 'section'),
                array('term-order' => 'section_order'),
				array('hide-in-kiosk-mode' => 'hide_in_kiosk_mode'),
                array('age-range' => 'age_range'),
				array('sort-order' => 'sort_order'),
                array('social-sharing-image' => 'image_sharing'),
                array('social-commenting' => 'commenting'),
				array('social-sharing-text' => 'text_sharing'),
                array('default-social-media-message' => 'social_media_message'),
                array('post-image' => 'image'),
                array('comments' => 'comments'),
				array('post-preview-text' => 'post_preview_text'),
				array('post-body' => 'post_body'),
				array('post-header-type' => 'post_header_type'),
				array('post-header-url' => 'post_header_url'),
            ),
            'children' => array(),
            'name' => 'posts',
			'force_english_translation_for_attributes' => array( 'id', 'hide_in_kiosk_mode', 'age_range', 'sort_order', 'image_sharing', 'commenting', 'text_sharing', 'comments', 'post_header_type', 'post_header_url' ),
			'force_retrieved_as_normal' => array( 'social-sharing-image', 'social-commenting', 'social-sharing-text', 'hide-in-kiosk-mode' )
        )
    );

    public function get_slug() {
        return $this->slug;
    }

    public function get_children_hierarchy() {
        return $this->children_hierarchy;
    }
}