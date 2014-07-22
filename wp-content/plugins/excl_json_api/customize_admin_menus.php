<?php
function remove_menus(){
  
  remove_menu_page( 'edit.php' );                   //Posts
  remove_menu_page( 'edit.php?post_type=page' );    //Pages
  remove_menu_page( 'link-manager.php' );			//Links
  remove_menu_page( 'tools.php' );					//Tools
  
}
add_action( 'admin_menu', 'remove_menus' );

function custom_menu_order($menu_ord) {
	if (!$menu_ord) return true;
	 
	return array(
		'edit.php?post_type=museum',
		'edit.php?post_type=exhibit',
		'edit.php?post_type=component',
		'edit.php?post_type=component-post',
		'edit.php?post_type=part',

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