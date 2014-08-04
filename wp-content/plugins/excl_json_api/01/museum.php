<?php

namespace api\v01;

require_once(dirname(__FILE__) . '/iexcl_type.php');

class EXCL_Museum implements iExcl_Type {
	protected $slug = 'museum';
	protected $plural_slug = 'museums';
	protected $children_hierarchy = array(
		'museum' => array(
			'attributes' => array(
				array('ID' => 'id'),
				array('post_title' => 'name'),
				array('museum-information' => 'description'),
				array('prices' => 'prices'),
                array('museum-map' => 'map'),
                array('image' => 'image'),
                array('website' => 'website'),
                array('phone' => 'phone'),
                array('email' => 'email'),
                array('info-page' => 'info'),
				array('google-analytics-tracking-id' => 'tracking_id'),
				array('tailored-content-categories' => 'tailored_content_categories'),
				array('internationalization-message' => 'internationalization_message'),
                array('exhibits' => 'exhibits'),
                array('lang_options' => 'lang_options'),
                array('homepage-exhibits-label' => 'homepage_exhibits_label'),
                array('homepage-info-label' => 'homepage_info_label'),
                array('homepage-map-label' => 'homepage_map_label'),
                array('homepage-icon' => 'homepage_icon'),
                array('exhibit-label' => 'exhibit_label'),
                array('exhibit-label-plural' => 'exhibit_label_plural')
			),
			'children' => array('exhibit'),
			'force_english_translation_for_attributes' => array(
				'id', 'prices', 'phone', 'email', 'tracking_id', 'tailored_content_categories', 'exhibits',
				'lang_options', 'homepage_icon', 'exhibit_label', 'exhibit_label_plural')
		),
		'exhibit' => array(
			'attributes' => array (
                array('ID' => 'id'),
                array('sort-order' => 'sort_order'),
                array('post_title' => 'name'),
                array('long-description' => 'description'),
                array('exhibit_image' => 'exhibit_image'),
                array('components' => 'components')
			),
			'children' => array('component'),
			'force_english_translation_for_attributes' => array( 'id', 'sort_order', 'components' )
		),
		'component' => array(
			'attributes' => array (
                    array('ID' => 'id'),
                    array('sort-order'=> 'sort_order'),
                    array('post_title' => 'name'),
                    array('component_image' => 'image')
				),
			'children' => array(),
			'force_english_translation_for_attributes' => array( 'id', 'sort_order' )
		));

	public function get_slug() {
		return $this->slug;
	}

	public function get_children_hierarchy() {
		return $this->children_hierarchy;
	}
}
