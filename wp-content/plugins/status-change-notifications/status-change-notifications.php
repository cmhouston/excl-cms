<?php
/*
Plugin Name: Status Change Notifications
Plugin URI: http://themedojo.net/
Description: Sends email notifications to users (post author or site administrator) when the status of a post is changed. Supports custom post types.
Version: 1.0
Author: Theme Dojo
Author URI: http://themedojo.net/
License: GPL2
*/
function wpscn_initialize_options(){
	$not_first_run = get_option('wpscn_notifications');
	if($not_first_run)
		return;

	$site_name = get_bloginfo('name');
	$notifications = array(
						'post_pending_publish_author' => array(
							'subject' 		=> 'You submission has been approved',
							'content' 		=> "Hi %%AUTHOR-FNAME%%!\n\nYour article titled \"%%TITLE%%\" has been approved and is now live on our site. You can view it here:\n\n%%URL%%\n\nThanks,\n$site_name Team",
							'post_type' 	=> 'post',
							'status_from' 	=> 'pending',
							'status_to' 	=> 'publish',
							'recipient' 	=> 'author',
							'format' 		=> 'text'
						),
						'post_pending_draft_author' => array(
							'subject' 		=> 'You submission has been rejected',
							'content' 		=> "Hi %%AUTHOR-FNAME%%!\n\nThank you for submitting the article titled \"%%TITLE%%\". Unfortunately it can not be approved at this time. Please read our guidelines and try again.\n\nThanks,\n$site_name Team",
							'post_type' 	=> 'post',
							'status_from' 	=> 'pending',
							'status_to' 	=> 'draft',
							'recipient' 	=> 'author',
							'format' 		=> 'text'
						)
					);
	update_option( 'wpscn_notifications', $notifications );
}
register_activation_hook(__FILE__, 'wpscn_initialize_options');

function wpscn_rollback(){
	delete_option('wpscn_notifications');
	delete_option('wpscn_settings');
}
register_uninstall_hook(__FILE__, 'wpscn_rollback');

function wpscn_intercept_all_status_changes( $new_status, $old_status, $post ) {
    if ( $new_status != $old_status ) {
		$notifications 	= get_option('wpscn_notifications');
		$author_index 	= $post->post_type.'_'.$old_status.'_'.$new_status.'_author';
		$admin_index 	= $post->post_type.'_'.$old_status.'_'.$new_status.'_administrator';

		if(!isset($notifications[$author_index]) && !isset($notifications[$admin_index]))
			return;

		if(isset($notifications[$author_index])){
			wpscn_compose_and_send($notifications[$author_index], $post, get_the_author_meta( 'user_email', $post->post_author ));
		}
		if(isset($notifications[$admin_index])){
			wpscn_compose_and_send($notifications[$admin_index], $post, get_option( 'admin_email' ));
		}
    }
}
add_action( 'transition_post_status', 'wpscn_intercept_all_status_changes', 10, 3 );

function wpscn_compose_and_send($notification, $post, $main_recipient){
	$subject		= wpscn_insert_values($notification['subject'], $post);
	$content 		= wpscn_insert_values($notification['content'], $post);
	$recipients[] 	= $main_recipient;
	$extra_emails 	= array_map('trim', explode("\n", $notification['extra_emails']));
	$recipients 	= array_merge($recipients, $extra_emails);

	if($notification['format'] == 'html') add_filter( 'wp_mail_content_type', 'wpscn_set_html_content_type' );
	wp_mail($recipients, $subject, $content);
	if($notification['format'] == 'html') remove_filter( 'wp_mail_content_type', 'wpscn_set_html_content_type' );
}

function wpscn_insert_values($string, $post){
	$author_name 	= get_the_author_meta( 'display_name', $post->post_author );
	$author_fname 	= get_the_author_meta( 'first_name', $post->post_author );
	$author_fname 	= (!empty($author_fname))?$author_fname:$author_name;
	$author_lname 	= get_the_author_meta( 'last_name', $post->post_author );
	$author_lname 	= (!empty($author_lname))?$author_lname:$author_name;
	$post_title 	= $post->post_title;
	$post_url 		= get_site_url().'?p='.$post->ID;
	$string 		= str_replace('%%AUTHOR%%', $author_name, $string);
	$string 		= str_replace('%%AUTHOR-FNAME%%', $author_fname, $string);
	$string 		= str_replace('%%AUTHOR-LNAME%%', $author_lname, $string);
	$string 		= str_replace('%%TITLE%%', $post_title, $string);
	$string 		= str_replace('%%URL%%', $post_url, $string);
	return $string;
}

function wpscn_set_html_content_type() {
	return 'text/html';
}

function wpscn_wp_mail_from( $original_email_address ){
	$settings = get_option('wpscn_settings');
	if(isset($settings['wpscn_sender_email']) && is_email($settings['wpscn_sender_email']))
		return $settings['wpscn_sender_email'];
	return $original_email_address;
}
add_filter( 'wp_mail_from', 'wpscn_wp_mail_from' );

function wpscn_wp_mail_from_name( $original_email_from ){
	$settings = get_option('wpscn_settings');
	if(isset($settings['wpscn_sender_name']))
		return $settings['wpscn_sender_name'];
	return $original_email_from;
}
add_filter( 'wp_mail_from_name', 'wpscn_wp_mail_from_name' );

include('notifications.php');