<?php
/**
 * Plugin Custom User Meta
 */
 
/**
 * Create comment notification user meta field
 */

function cbent_macn_add_user_meta_field( $user ) { ?>

	<h3><?php _e( 'Comment Email Notification', 'cbnet-multi-author-comment-notification' ); ?></h3>

	<table class="form-table">

		<tr>
			<th><label for="comment_notify"><?php _e( 'Comment Email Notification', 'cbnet-multi-author-comment-notification' ); ?></label></th>

			<td>
				<input type="checkbox" name="cbnet_macn_comment_notify" value="true" <?php checked( true == get_the_author_meta( 'cbnet_macn_comment_notify', $user->ID ) ); ?>>
				<span class="description"><?php _e( 'Receive email notification of comments to all posts, regardless of post author', 'cbnet-multi-author-comment-notification' ); ?></span>
			</td>
		</tr>

	</table>
<?php }
add_action( 'show_user_profile', 'cbent_macn_add_user_meta_field' );
add_action( 'edit_user_profile', 'cbent_macn_add_user_meta_field' );


/**
 * Save comment notification user meta data
 */
function cbent_macn_save_user_meta_data( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	// Update user meta
	update_user_meta( $user_id, 'cbnet_macn_comment_notify', ( isset( $_POST['cbnet_macn_comment_notify'] ) ? true : false ) );
		
	// Delete transients
	delete_site_transient( 'cbnet_macn_moderation_email_addresses' );
	delete_site_transient( 'cbnet_macn_notification_email_addresses' );
	
}
add_action( 'personal_options_update', 'cbent_macn_save_user_meta_data' );
add_action( 'edit_user_profile_update', 'cbent_macn_save_user_meta_data' );

?>