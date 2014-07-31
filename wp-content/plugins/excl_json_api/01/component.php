<?php
namespace api\v01;

require_once(dirname(__FILE__) . '/iexcl_type.php');

class EXCL_Component implements iExcl_Type {
    protected $slug = 'component';
    protected $plural_slug = 'components';
	// force_inherit means whether to force an English translation instead of allowing translations
    protected $children_hierarchy = array(
        'component' => array(
            'attributes' => array (
                    array( array('ID' => 'id'), 'force_inherit' => true ),
                    array( array('sort-order'=> 'sort_order'), 'force_inherit' => true ),
                    array( array('post_title' => 'name'), 'force_inherit' => false ),
                    array( array('posts'=> 'posts'), 'force_inherit' => true )
                ),
            'children' => array('component-post')
        ),
        'component-post' => array(
            'attributes' => array(
                array( array('ID' => 'id'), 'force_inherit' => true ),
                
                array( array('post_title' => 'name'), 'force_inherit' => false ),
				array( array('categories' => 'section'), 'force_inherit' => false ),
                array( array('term-order' => 'section_order'), 'force_inherit' => false ),
				array( array('hide-in-kiosk-mode' => 'hide_in_kiosk_mode'), 'force_inherit' => true ),
                array( array('age-range' => 'age_range'), 'force_inherit' => true ),
				array( array('sort-order' => 'sort_order'), 'force_inherit' => true ),
                array( array('social-liking' => 'liking'), 'force_inherit' => true ),
				array( array('like_count' => 'like_count'), 'force_inherit' => true ),
                array( array('social-sharing-image' => 'image_sharing'), 'force_inherit' => true ),
                array( array('social-commenting' => 'commenting'), 'force_inherit' => true ),
				array( array('social-sharing-text' => 'text_sharing'), 'force_inherit' => true ),
                array( array('default-social-media-message' => 'social_media_message'), 'force_inherit' => false ),
                array( array('post-image' => 'image'), 'force_inherit' => false ),
				array( array('parts' => 'parts'), 'force_inherit' => true ),
                array( array('comments' => 'comments'), 'force_inherit' => true ),
				array( array('post-preview-text' => 'post_preview_text'), 'force_inherit' => false)
				array( array('post-body' => 'post_body'), 'force_inherit' => false ),
				array( array('post-header-type' => 'post_header_type'), 'force_inherit' => true ),
				array( array('post-header-url' => 'post_header_url'), 'force_inherit' => true )
            ),
            'children' => array('part'),
            'name' => 'posts'
        ),
        'part' => array(
            'attributes' => array(
                array( array('ID' => 'id'), 'force_inherit' => true ),
                array( array('sort-order' => 'sort_order'), 'force_inherit' => true ),
                array( array('post_title' => 'name'), 'force_inherit' => false ),
                array( array('part-type' => 'type'), 'force_inherit' => true ),
                array( array('part-video' => 'video'), 'force_inherit' => false ),
                array( array('part-image' => 'image'), 'force_inherit' => false ),
                array( array('part-body' => 'body'), 'force_inherit' => false ),
                array( array('part-rich' => 'rich'), 'force_inherit' => false )
            ),
            'children' => array()
        )
    );

    public function get_slug() {
        return $this->slug;
    }

    public function get_children_hierarchy() {
        return $this->children_hierarchy;
    }
}