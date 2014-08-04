<?php
/*
 * Plugin Name:	cbnet Multi Author Comment Notification
 * Plugin URI:	https://github.com/chipbennett/cbnet-multi-author-comment-notification
 * Description:	Send comment notification emails to multiple users. Select users individually or by user role, or send emails to arbitrary email addresses.
 * Version:		3.2
 * Author:		chipbennett
 * Author URI:	http://www.chipbennett.net/
 * Text Domain:	cbnet-multi-author-comment-notification
 * Domain Path:	/languages
 *
 * License:       GNU General Public License, v2 (or newer)
 * License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * Version 2.0 and later of this Plugin: Copyright (C) 2012 Chip Bennett,
 * Released under the GNU General Public License, version 2.0 (or newer)
 * 
 * Previous versions of this program were modified from MaxBlogPress Multi Author Comment Notification plugin, version 1.0.5, 
 * Copyright (C) 2007 www.maxblogpress.com, released under the GNU General Public License.
 */
 
/**
 * Load Plugin textdomain
 */
function cbnet_macn_load_textdomain() {
	load_plugin_textdomain( 'cbnet-multi-author-comment-notification' ); 
}
// Load Plugin textdomain
add_action( 'plugins_loaded', 'cbnet_macn_load_textdomain' );

 
/**
 * Bootstrap Plugin settings
 */
include( plugin_dir_path( __FILE__ ) . 'settings.php' );

/**
 * Globalize Plugin options
 */
global $cbnet_rscc_options;
$cbnet_macn_options = cbnet_macn_get_options();
 
/**
 * Bootstrap Plugin custom user meta
 */
include( plugin_dir_path( __FILE__ ) . 'custom-user-meta.php' );


/**
 * Build array of notification email addresses
 */
function cbnet_macn_get_notification_email_addresses( $type = 'moderation' ) {

	// Determine email type
	$email_type = ( 'notification' == $type ? 'notification' : 'moderation' );

	// Fetch transient
	$cbnet_macn_email_addresses_transient = get_site_transient( 'cbnet_macn_' . $email_type . '_email_addresses' );
	
	// Return transient if it exists
	if ( $cbnet_macn_email_addresses_transient ) {
		return $cbnet_macn_email_addresses_transient;
	}
	
	// Globalize options
	global $cbnet_macn_options;
	
	// Instantiate array
	$email_addresses = array();
	
	// Add email addresses from user meta
	$user_email_addresses = array();
	$users_email_notify = get_users( array( 'meta_key' => 'cbnet_macn_comment_notify', 'meta_value' => true ) );
	if ( ! empty( $users_email_notify ) ) {
		foreach ( $users_email_notify as $user ) {
			$user_email_addresses[] = $user->user_email;
		}
		$email_addresses = array_merge( $email_addresses, $user_email_addresses );
	}
	
	// Add email addresses for User Roles
	$moderation_roles = array( 'administrator', 'editor' );
	$notification_roles = array( 'author', 'contributor', 'subscriber' );
	$roles = ( 'moderation' == $type ? $moderation_roles : array_merge( $moderation_roles, $notification_roles ) );
	$role_email_addresses = array();
	foreach ( $roles as $role ) {
		if ( true == $cbnet_macn_options['all_' . $role . 's'] ) {
			$role_email_notify = get_users( array( 'role' => $role ) );
			if ( ! empty( $role_email_notify ) ) {
				foreach ( $role_email_notify as $user ) {
					$role_email_addresses[] = $user->user_email;
				}
			}
		}
	}
	if ( ! empty( $role_email_addresses ) ) {
		$email_addresses = array_merge( $email_addresses, $role_email_addresses );
	}
	
	// Add additional email addresses
	if ( false != $cbnet_macn_options['additional_emails'] ) {
		$email_addresses = array_merge( $email_addresses, $cbnet_macn_options['additional_emails'] );
	}

	// Remove site admin email addresses
	$site_admin_email = get_bloginfo( 'admin_email' );
	if ( false == $cbnet_macn_options['site_admin'] )  {
		if ( in_array( $site_admin_email, $email_addresses ) ) {
			unset( $email_addresses[$site_admin_email] );
		}
	} else {
		$email_addresses = array_merge( $email_addresses, array( $site_admin_email ) );
	}
	
	// Set transient
	set_site_transient( 'cbnet_macn_' . $email_type . '_email_addresses', $email_addresses, 60*60*24*7 );
	
	// Return array
	return apply_filters( 'cbnet_macn_notify_email_addresses', array_unique( $email_addresses ) );
}

/**
 * Filter array of comment notification email addresses
 */
function cbnet_macn_filter_comment_notification_email_to( $email_to ) {	
	global $cbnet_macn_options;
	if ( false == $cbnet_macn_options['notify_for_registered_users'] && is_user_logged_in() ) {
		return $email_to;
	}
	return array_unique( array_merge( $email_to, cbnet_macn_get_notification_email_addresses( 'notification' ) ) );
}
add_filter( 'comment_notification_recipients', 'cbnet_macn_filter_comment_notification_email_to' );

/**
 * Filter array of moderation notification email addresses
 */
function cbnet_macn_filter_comment_moderation_email_to( $email_to ) {
	global $cbnet_macn_options;
	if ( false == $cbnet_macn_options['notify_for_comment_moderation'] || ( true == $cbnet_macn_options['notify_for_registered_users'] && is_user_logged_in() ) ) {
		return $email_to;
	}
	return array_unique( array_merge( $email_to, cbnet_macn_get_notification_email_addresses( 'moderation' ) ) );
}
add_filter( 'comment_moderation_recipients', 'cbnet_macn_filter_comment_moderation_email_to' );


?>