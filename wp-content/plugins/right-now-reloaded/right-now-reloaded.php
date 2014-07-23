<?php
/**
 * Plugin Name: Right Now Reloaded
 * Description: A better version of the "Right Now" dashboard widget, tailored to show what's relevant to your site.
 * Version: 2.2
 * Author: Michael Dance
 * Author URI: http://mikedance.com
 * License: GPLv2
 */


class Right_Now_Reloaded {


	/**
	 * Initialize Right Now Reloaded. Add hooks for loading the i18n files and running
	 * dashboard_init().
	 */
	static public function init() {
		add_action( 'init',           array( __CLASS__, 'i18n' ) );
		add_action( 'load-index.php', array( __CLASS__, 'dashboard_init' ) );
	}


	/**
	 * Load language files.
	 */
	static public function i18n() {
		if ( is_admin() )
			load_plugin_textdomain( 'right-now-reloaded', false, 'right-now-reloaded/languages' );
	}


	/**
	 * Load styles and add a hook to register the dashboard widget. Runs only on the
	 * dashboard index page.
	 */
	static public function dashboard_init() {
		add_action( 'admin_print_styles', array( __CLASS__, 'enqueue_style' ) );
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'register' ) );
	}


	/**
	 * Load styles.
	 */
	static public function enqueue_style() {
		wp_enqueue_style( 'md-rnr-style', plugins_url( 'right-now-reloaded.css', __FILE__ ) );
	}


	/**
	 * Remove the old Right Now widget and replace it with Register Right Now Reloaded.
	 */
	static public function register() {

		if ( !current_user_can( 'edit_posts' ) )
			return;

		global $wp_meta_boxes;

		// Out with the old
		unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );

		// In with the new
		wp_add_dashboard_widget( 'md-rnr-widget', 'Right Now Reloaded', array( __CLASS__, 'display' ) );

		// Move the new widget to the top
		$dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		$widget_backup = array( 'md-rnr-widget' => $dashboard['md-rnr-widget'] );
		unset( $dashboard['md-rnr-widget'] );
		$reordered_dashboard = array_merge( $widget_backup, $dashboard );
		$wp_meta_boxes['dashboard']['normal']['core'] = $reordered_dashboard;

	}


	/**
	 * Build the basic HTML structure.
	 */
	static public function display() {

		?><p class="md-rnr-paragraph"><?php
			self::blurb();
		?></p>

		<div id="md-rnr-column-primary"><?php
			self::stats( 'primary' );
		?></div>

		<div id="md-rnr-column-secondary"><?php
			self::stats( 'secondary' );
		?></div>

		<div class="md-rnr-clear"></div><?php

		// Preserve actions hooked into the original Right Now
		?><div id="dashboard_right_now"><?php
			do_action( 'rightnow_end' );
			do_action( 'activity_box_end' );
		?></div><?php

	}


	/**
	 * Build the heading blurb.
	 */
	static private function blurb() {

		// Get theme info
		$theme = wp_get_theme();

		// Get active plugin count
		$all_plugins = apply_filters( 'all_plugins', get_plugins() );
		foreach ( $all_plugins as $plugin_file => $plugin_data ) {
			if ( is_plugin_active( $plugin_file ) ) {
				$active_plugins[ $plugin_file ] = $plugin_data;
			}
		}
		$plugin_amount = count( $active_plugins );

		// Get user count
		$users = count_users();
		$user_amount = $users['total_users'];

		// Check theme permissions
		if ( current_user_can( 'switch_themes') )
			$theme = '<a href="themes.php">' . $theme->title . '</a>';
		else
			$theme = $theme->title;

		// Print theme blurb
		printf( __( 'You are using the %1$s theme', 'right-now-reloaded' ), $theme );

		// Check version permissions and print version blurb
		if ( current_user_can( 'update_core' ) ) {
			$version = '<a href="update-core.php">' . get_bloginfo( 'version' ) . '</a>';
			printf( __( ' on WordPress %1$s', 'right-now-reloaded' ), $version );
		}

		// Check plugin permissions
		if ( current_user_can( 'activate_plugins' ) )
			$plugins = '<a href="plugins.php">' . number_format_i18n( $plugin_amount ) . '</a>';
		else
			$plugins = number_format_i18n( $plugin_amount );

		// Print plugin blurb
		printf( _n( ' with %1$s active plugin', ' with %1$s active plugins', $plugin_amount, 'right-now-reloaded' ), $plugins );

		// Check user permissions and print user blurb
		if ( current_user_can( 'list_users' ) ) {
			$users = '<a href="users.php">' . number_format_i18n( $user_amount ) . '</a>';
			printf( _n( ' and %1$s registered user', ' and %1$s registered users', $user_amount, 'right-now-reloaded' ), $users );
		}

		// End the sentence
		_e( '.', 'right-now-reloaded' );

	}


	/**
	 * Generate a list of stats.
	 *
	 * Two types: primary and secondary. Primary contains all post types plus Comments
	 * and Widgets. Secondary contains all taxonomies plus Links and Menus.
	 */
	static private function stats( $type = '' ) {

		switch( $type ) {

			case 'primary':

				$args_array = array(
					array(
						'singular' => __( 'Widget', 'right-now-reloaded' ),
						'plural'   => __( 'Widgets', 'right-now-reloaded' ),
						'cap'      => 'edit_theme_options',
						'link'     => 'widgets.php',
						'amount'   => self::count_active_widgets()
					),
					array(
						'singular' => __( 'Active Menu', 'right-now-reloaded' ),
						'plural'   => __( 'Active Menus', 'right-now-reloaded' ),
						'cap'      => 'edit_theme_options',
						'link'     => 'nav-menus.php',
						'amount'   => self::count_active_menus()
					)
				);

				// Get all public post types
				$post_types = get_post_types( array( 'public' => true ), 'objects' );

				// Loop through each taxonomy and store data
				foreach ( $post_types as $post_type ) {

					$args_array[] = array(
						'name'     => $post_type->name,
						'singular' => $post_type->labels->singular_name,
						'plural'   => $post_type->label,
						'cap'      => $post_type->cap->edit_posts,
						'link'     => ( $post_type->name == 'attachment' ) ? 'upload.php' : 'edit.php?post_type=' . $post_type->name,
						'amount'   => ( $post_type->name == 'attachment' ) ? self::count_posts( $post_type->name, 'inherit' ) : self::count_posts( $post_type->name ),
						'draft'    => self::count_posts( $post_type->name, 'draft' ),
						'pending'  => self::count_posts( $post_type->name, 'pending' )
					);

				}

				// Add comments
				$args_array[] = array(
					'singular' => __( 'Comment', 'right-now-reloaded' ),
					'plural'   => __( 'Comments', 'right-now-reloaded' ),
					'cap'      => 'moderate_comments',
					'link'     => 'edit-comments.php',
					'amount'   => self::count_comments(),
					'pending'  => self::count_comments( 'moderated' )
				);

				break;

			case 'secondary':

				$args_array = array(
					array(
						'singular' => __( 'Link Category', 'right-now-reloaded' ),
						'plural'   => __( 'Link Categories', 'right-now-reloaded' ),
						'cap'      => 'manage_links',
						'link'     => 'edit-tags.php?taxonomy=link_category',
						'amount'   => wp_count_terms( 'link_category' )
					),
					array(
						'name'     => 'links',
						'singular' => __( 'Link', 'right-now-reloaded' ),
						'plural'   => __( 'Links', 'right-now-reloaded' ),
						'cap'      => 'manage_links',
						'link'     => 'link-manager.php',
						'amount'   => count( get_bookmarks() )
					)
				);

				// Get all taxonomies with a public UI
				$taxonomies = get_taxonomies( array( 'show_ui' => true ), 'objects' );

				// Loop through each taxonomy and store data
				foreach ( $taxonomies as $taxonomy ) {
					$args_array[] = array(
						'singular' => $taxonomy->labels->singular_name,
						'plural'   => $taxonomy->label,
						'cap'      => $taxonomy->cap->manage_terms,
						'link'     => 'edit-tags.php?taxonomy=' . $taxonomy->name,
						'amount'   => wp_count_terms( $taxonomy->name )
					);
				}

				break;

		}

		self::column( $args_array, $type );

	}


	/**
	 * Build a full column of stats.
	 */
	static private function column( $args_array = array(), $type = '' ) {
		$column_output = array();

		// Build each row
		foreach( $args_array as $args ) {
			$column_output = self::row( $args, $column_output );
		}

		// Remove any row with no terms
		if ( isset( $column_output[0] ) ) unset( $column_output[0] );

		// Sort $output by key (which is the amount) from high to low
		ksort( $column_output );
		$column_output = array_reverse( $column_output, true );

		// Display $column_output
		?><div class="md-rnr-section">
			<?php if ( $type == 'primary' ) echo '<h5>Content</h5>';
			elseif( $type == 'secondary' ) echo '<h5>Organization</h5>'; ?>
			<table><?php
				foreach ( $column_output as $output_row ) echo $output_row;
				do_action( 'right_now_table_end' );
			?></table>
		</div><?php

	}


	/**
	 * Build one row of stats.
	 */
	static private function row( $args = array(), $column_output = array() ) {
		if ( !isset( $column_output[$args['amount']] ) ) $column_output[$args['amount']] = '';
		if ( !isset( $column_output[-1] ) ) $column_output[-1] = '';

		// Generate labels
		$amount_label = number_format_i18n( $args['amount'] );
		$name_label   = _n( $args['singular'], $args['plural'], $args['amount'], 'right-now-reloaded' );

		// Add links to labels if user has access
		if ( current_user_can( $args['cap'] ) ) {
			$amount_label = '<a href="' . $args['link'] . '">' . $amount_label . '</a>';
			$name_label   = '<a href="' . $args['link'] . '">' . $name_label   . '</a>';
		}

		// Usually we hide rows with 0 entries, but we want to show rows with drafts or pending even if they have 0 published - sticking these cases in $column_output[-1] solves this
		if ( $args['amount'] == 0 && self::actions( $args ) )
			$args['amount'] = -1;

		// Generate output
		$column_output[$args['amount']] .= '<tr class="md-rnr-' . sanitize_title( $args['singular'] ) . '"><td class="md-rnr-table-number">' . $amount_label . '</td><td class="md-rnr-table-label">' . $name_label . '</td>' . self::actions( $args ) . '</tr>';

		return $column_output;

	}


	/**
	 * Build actions (draft and pending buttons).
	 */
	static private function actions( $args = array() ) {

		$actions_output = '';
		$args['pending'] = ( isset( $args['pending'] ) ) ? $args['pending'] : 0;
		$args['draft'] = ( isset( $args['draft'] ) ) ? $args['draft'] : 0;

		if ( ( $args['pending'] || $args['draft'] ) && current_user_can( $args['cap'] ) ) {

			$actions = array(
				'pending' => array( 'pending', 'pending', $args['pending'] ),
				'draft' => array( 'draft', 'drafts', $args['draft'] )
			);

			if ( $args['name'] == 'comment' ) $link = 'edit-comments.php?comment_status=moderated';

			$actions_output .= '<td class="md-rnr-action">';

			foreach( $actions as $status => $action ) {
				$link = 'edit.php?post_status=' . $status . '&post_type=' . $args['name'];

				if ( $action[2] ) {
					$actions_output .= '<a class="button md-rnr-action-' . $status . '" href="' . $link . '">';
					$actions_output .= number_format_i18n( $action[2] ) . ' ' . _n( $action[0], $action[1], $action[2], 'right-now-reloaded' );
					$actions_output .= '</a>';
				}
			}

			$actions_output .= '</td>';

		}

		return $actions_output;

	}


	/**
	 * Return number of currently-active widgets.
	 */
	static private function count_active_widgets() {
		global $wp_registered_sidebars;
		if ( !empty( $wp_registered_sidebars ) ) {
			$sidebars_widgets = wp_get_sidebars_widgets();
			$widget_amount = 0;
			foreach ( $sidebars_widgets as $key => $value ) {
				if ( 'wp_inactive_widgets' == $key )
					continue;
				if ( is_array( $value ) )
					$widget_amount = $widget_amount + count( $value );
			}
		}
		return $widget_amount;
	}


	/**
	 * Return number of currently-active menus.
	 */
	static private function count_active_menus() {
		$locations = get_registered_nav_menus();
		$active_menu_amount = 0;
		foreach ( $locations as $slug => $description ) {
			if ( has_nav_menu( $slug ) ) $active_menu_amount++;
		}
		return $active_menu_amount;
	}


	/**
	 * Return number of posts of a particular status.
	 */
	static private function count_posts( $post_type = 'post', $status = 'publish' ) {
		$amount = wp_count_posts( $post_type );
		if ( $status == 'pending' )
			return $amount->pending;
		elseif( $status == 'draft' )
			return $amount->draft;
		elseif( $status == 'inherit' )
			return $amount->inherit;
		else
			return $amount->publish;
	}


	/**
	 * Return number of comments of a particular status.
	 */
	static private function count_comments( $status = 'approved' ) {
		$amount = wp_count_comments( $status );
		if ( $status == 'moderated' )
			return $amount->moderated;
		else
			return $amount->approved;
	}


}
Right_Now_Reloaded::init();