<?php

namespace api\v01;

require_once(dirname(__FILE__) . '/iexcl_type.php');

class EXCL_Museum implements iExcl_Type {
	protected $slug = 'museum';
	protected $plural_slug = 'museums';
	protected $children_hierarchy = array(
		'museum' => array(
			'attributes' => array(
				array( array('ID' => 'id'), 'force_inherit' => true ),
				array( array('post_title' => 'name'), 'force_inherit' => false ),
				array( array('museum-information' => 'description'), 'force_inherit' => false ),
				array( array('prices' => 'prices'), 'force_inherit' => true ),
                array( array('map' => 'map'), 'force_inherit' => false ),
                array( array('image' => 'image'), 'force_inherit' => false ),
                array( array('website' => 'website'), 'force_inherit' => false ),
                array( array('phone' => 'phone'), 'force_inherit' => true ),
                array( array('email' => 'email'), 'force_inherit' => true ),
                array( array('info-page' => 'info'), 'force_inherit' => false),
				array( array('google-analytics-tracking-id' => 'tracking_id', 'force_inherit' => true) ),
				array( array('tailored-content-categories' => 'tailored_content_categories'), 'force_inherit' => true),
				array( array('internationalization-message' => 'internationalization_message'), 'force_inherit' => false),
                array( array('exhibits' => 'exhibits'), 'force_inherit' => true ),
                array( array('lang_options' => 'lang_options'), 'force_inherit' => true ),
                array( array('homepage-exhibits-label' => 'homepage_exhibits_label'), 'force_inherit' => false ),
                array( array('homepage-info-label' => 'homepage_info_label'), 'force_inherit' => false ),
                array( array('homepage-map-label' => 'homepage_map_label'), 'force_inherit' => false ),
                array( array('homepage-icon' => 'homepage_icon'), 'force_inherit' => true )
			),
			'children' => array('exhibit')
		),
		'exhibit' => array(
			'attributes' => array (
                    array( array('ID' => 'id'), 'force_inherit' => true ),
                    array( array('sort-order' => 'sort_order'), 'force_inherit' => true ),
                    array( array('post_title' => 'name'), 'force_inherit' => false ),
                    array( array('description' => 'description'), 'force_inherit' => false ),
                    array( array('long-description' => 'long_description'), 'force_inherit' => false ),
                    array( array('exhibit_image' => 'exhibit_image'), 'force_inherit' => false ),
                    array( array('components' => 'components'), 'force_inherit' => true )
				),
			'children' => array('component')
		),
		'component' => array(
			'attributes' => array (
                    array( array('ID' => 'id'), 'force_inherit' => true ),
                    array( array('sort-order'=> 'sort_order'), 'force_inherit' => true ),
                    array( array('post_title' => 'name'), 'force_inherit' => false ),
                    array( array('component_image' => 'image'), 'force_inherit' => false )
				),
			'children' => array()
		));

	public function get_slug() {
		return $this->slug;
	}

	public function get_children_hierarchy() {
		return $this->children_hierarchy;
	}
}
