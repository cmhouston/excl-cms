<?php
/**********************************************************
 * General
 *********************************************************/

function remove_menus(){
  remove_menu_page( 'edit.php' );                   //Posts
  remove_menu_page( 'edit.php?post_type=page' );    //Pages
  remove_menu_page( 'link-manager.php' );			//Links
  remove_menu_page( 'tools.php' );					//Tools
  remove_menu_page( 'edit.php?post_type=part' );	//Parts
  
}
add_action( 'admin_menu', 'remove_menus' );

function custom_menu_order($menu_ord) {
	if (!$menu_ord) return true;
	 
	return array(
		'edit.php?post_type=museum',
		'edit.php?post_type=exhibit',
		'edit.php?post_type=component',
		'edit.php?post_type=component-post',

		'separator1', // First separator
		'edit-comments.php', // Comments
		'upload.php', // Media
		
		// 'separator2', // Second separator
		// 'themes.php', // Appearance
		// 'plugins.php', // Plugins
		// 'users.php', // Users
		// 'tools.php', // Tools
		// 'options-general.php', // Settings
		// 'separator-last', // Last separator
	);
}
add_filter('custom_menu_order', 'custom_menu_order'); // Activate custom_menu_order
add_filter('menu_order', 'custom_menu_order');

function edit_admin_menus() {
    global $submenu;
     
    $submenu['edit.php?post_type=component-post'][15][0] = 'Sections'; // Rename categories to meal types
}
add_action( 'admin_menu', 'edit_admin_menus' );


/**********************************************************
 * Reording the meta boxes on the content editing pages
 *********************************************************/

// Adapted from plugin "Post Meta Box Order" (http://github.com/LettoBlog/post-meta-box-order) by Mustafa Uysal, LettoBlog (http://lettoblog.com)
add_action('init', 'posts_widgets_order');

function posts_widgets_order() {
    global $wpdb, $user_ID;

	if ( !empty( $user_ID ) ) {
		$types = array('museum', 'exhibit', 'component', 'component-post');
		foreach ($types as $type) {
			/**
			 * Default meta boxes
			 * submitdiv - publish
			 * categorydiv - category
			 * tagsdiv-post_tag - tags
			 * postimagediv -  featured image
			 * authordiv - author
			 * postexcerpt -  excerpt
			 * commentstatusdiv - Discussion
			 * ........
			 *
			 * If you want to order any plugin's meta box use meta box's $id
			 */

			//Left Column
			$posts_widgets_order_left_column[] = 'wpcf-post-relationship';
			$posts_widgets_order_left_column[] = 'wpcf-group-' . $type . '-custom-fields';
			$posts_widgets_order_left_column[] = 'commentstatusdiv';

			//Right Column
			$posts_widgets_order_right_column[] = 'submitdiv';
			$posts_widgets_order_right_column[] = 'ml_box';
			$posts_widgets_order_right_column[] = 'categorydiv';

			$left_column = '';
			foreach ( $posts_widgets_order_left_column as $posts_widgets_order_left_column_widget ) {
			    $left_column .= $posts_widgets_order_left_column_widget . ',';
			}
			$left_column = rtrim($left_column, ',');

			$right_column = '';
			foreach ( $posts_widgets_order_right_column as $posts_widgets_order_right_column_widget ) {
			    $right_column .= $posts_widgets_order_right_column_widget . ',';
			}
			$right_column = rtrim($right_column, ',');

			$posts_widget_order = array();

			$posts_widget_order['side'] = $right_column;
			$posts_widget_order['normal'] = $left_column;

			delete_user_option($user_ID, 'meta-box-order_' . $type, true);
		}
	}
}