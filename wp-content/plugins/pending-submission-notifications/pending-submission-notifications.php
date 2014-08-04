<?php
/**
 * @package Pending_Submission_Notification
 * @version 1.0
 */
/*
Plugin Name: Pending Submission Notification
Plugin URI: http://lifeofadesigner.com
Description: Send email notifications to the admin whenever a new article is submitted for review by a contributor
Author: Razvan Horeanga
Version: 1.0
Author URI: http://lifeofadesigner.com
*/



if ( is_admin() ){
add_action( 'admin_menu', 'pending_submission_notification_menu' );
}

function pending_submission_notification_menu() {
	add_options_page( 'Pending Submission Notifications Options', 'Pending Submission Notifications', 'manage_options', 'pending-submissions-notifications-settings', 'pending_submission_notification_options' );
	add_action( 'admin_init', 'register_pending_submission_notifications_settings' );
}



function register_pending_submission_notifications_settings() {
	//register our settings
	register_setting( 'pending-submission-notification-group', 'pending_submission_notification_admin_email' );
}


function pending_submission_notification_options() {

?>
	<div class="wrap">
	<h2>Pending Submission Notifications</h2>
	<p>Who should receive an email notification for new submissions?</p>
	<form method="post" action="options.php">
		<?php settings_fields( 'pending-submission-notification-group' ); ?>
		<?php do_settings_sections( 'pending-submission-notification-group' ); ?>
		<table class="form-table">
			<tr valign="top">
        	<th scope="row">Email Address:</th>
        	<td><input type="text" name="pending_submission_notification_admin_email" class="regular-text" value="<?php echo get_option('pending_submission_notification_admin_email'); ?>" /></td>
        	</tr>
		</table>
		<?php submit_button(); ?>
	</form>
	</div>
<?php
}

add_action('transition_post_status','pending_submission_send_email', 10, 3 );
function pending_submission_send_email( $new_status, $old_status, $post ) {

// Notifiy Admin that Contributor has writen a post
if ($new_status == 'pending' && user_can($post->post_author, 'edit_posts') && !user_can($post->post_author, 'publish_posts')) {
	$pending_submission_email = get_option('pending_submission_notification_admin_email');
	$admins = (empty($pending_submission_email)) ? get_option('admin_email') : $pending_submission_email;
	$url = get_permalink($post->ID);
	$edit_link = get_edit_post_link($post->ID, '');
	$preview_link = get_permalink($post->ID) . '&preview=true';
	$username = get_userdata($post->post_author);
	$subject = 'New submission pending review: "' . $post->post_title . '"';
	$message = 'A new submission is pending review.';
	$message .= "\r\n\r\n";
	$message .= "Author: $username->user_login\r\n";
	$message .= "Title: $post->post_title";
	$message .= "\r\n\r\n";
	$message .= "Edit the submission: $edit_link\r\n";
	$message .= "Preview it: $preview_link";
	$result = wp_mail($admins, $subject, $message);
	}

// Notifiy Contributor that Admin has published their post

else if ($old_status == 'pending' && $new_status == 'publish' && user_can($post->post_author, 'edit_posts') && !user_can($post->post_author, 'publish_posts')) {
    $username = get_userdata($post->post_author);
    $url = get_permalink($post->ID);
	$subject = "Your submission is now live:" . " " . $post->post_title;
	$message = '"' . $post->post_title . '"' . " was just published!. \r\n";
	$message .= $url;
	$result = wp_mail($username->user_email, $subject, $message);
	}
}

?>