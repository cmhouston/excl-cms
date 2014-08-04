<?php
/**
* The Code Editor
*/

function wpscn_notifications_menu_item() {
	global $wpscn_page_hook;
    $wpscn_page_hook = add_options_page(
        'Status Change Notifications',         	// The title to be displayed in the browser window for this page.
        'Status Change Notifications',			// The text to be displayed for this menu item
        'administrator',            			// Which type of users can see this menu item  
        'wpscn_post_notifications',    			// The unique ID - that is, the slug - for this menu item
        'wpscn_render_notifications_page'		// The name of the function to call when rendering this menu's page  
    );
}
add_action( 'admin_menu', 'wpscn_notifications_menu_item' );

function wpscn_notifications_scripts_styles($hook) {
	global $wpscn_page_hook;
	if( $wpscn_page_hook != $hook )
		return;
	wp_enqueue_style("wpscn_code_editor_stylesheet", plugins_url( "static/css/notifications.css" , __FILE__ ), false, "1.0", "all");
	wp_enqueue_script("wpscn_code_editor_script", plugins_url( "static/js/notifications.js" , __FILE__ ), array('jquery'), "1.0");
}
add_action( 'admin_enqueue_scripts', 'wpscn_notifications_scripts_styles' );

function wpscn_render_notifications_page() {
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"></div>
	<h2>Status Change Notifications</h2>
	<div id="wpscn-notif-adder">
		<label>Old Status: </label>
		<select class="status-from">
			<option value="pending">pending</option>
			<option value="draft">draft</option>
			<option value="publish">publish</option>
		</select>
		<label>New Status: </label>
		<select class="status-to">
			<option value="pending">pending</option>
			<option value="draft">draft</option>
			<option value="publish">publish</option>
		</select>
		<label>Post Type: </label>
		<select class="post-type">
			<?php $post_types = get_post_types(array('public'=>true)); ?>
			<?php foreach ($post_types as $type): ?>
				<option value="<?=$type?>"><?=$type?></option>
			<?php endforeach; ?>
		</select>
		<label>Recipient: </label>
		<select class="recipient">
			<option value="author">Post author</option>
			<option value="administrator">Administrator</option>
		</select>
		<label>Format: </label>
		<select class="format">
			<option value="text">Text</option>
			<option value="html">HTML</option>
		</select>
		<?php submit_button('Create', 'primary', 'wpscn-notif-adder-btn', false); ?>
		<img class="loading-img" src="<?=plugins_url('static/img/loading.gif', __FILE__)?>">
	</div>
	<form method="post" action="options.php">
		<?php settings_fields( 'wpscn_post_notifications' ); ?>
		<div id="wpscn-notif-section-container">
				<?php do_settings_fields( 'wpscn_post_notifications', 'wpscn_notifications_section' ); ?>
		</div>
		<div id="wpscn-settings-section-container">
			<h2>Settings</h2>
			<?php do_settings_fields( 'wpscn_post_notifications', 'wpscn_settings_section' ); ?>
		</div>
		<?php submit_button('Save Everything', 'primary', 'save-notifications'); ?>
	</form>
</div>
<?php }

function wpscn_create_notifications() {
	add_settings_section( 'wpscn_notifications_section', null, null, 'wpscn_post_notifications' );
	$notifications 		= get_option('wpscn_notifications');
	foreach ($notifications as $notif) {
		$post_type 		= $notif['post_type'];
		$statusFrom 	= $notif['status_from'];
		$statusTo 		= $notif['status_to'];
		$recipient 		= $notif['recipient'];
		$format 		= $notif['format'];
		add_settings_field(
	        'wpscn_'.$post_type.'_'.$statusFrom.'_'.$statusTo.'_'.$recipient, '', 'wpscn_render_notification_fields', 'wpscn_post_notifications', 'wpscn_notifications_section',
			array(
				'title' 		=> ucfirst($statusFrom).' to '.ucfirst($statusTo),
				'id' 			=> $post_type.'_'.$statusFrom.'_'.$statusTo.'_'.$recipient,
				'post_type' 	=> $post_type,
				'status_from' 	=> $statusFrom,
				'status_to' 	=> $statusTo,
				'recipient' 	=> $recipient,
				'format' 		=> $format,
				'new' 			=> false,
				'group' 		=> 'wpscn_notifications'
			)
	    );
	}
	add_settings_field(
        'wpscn_sender_name', '', 'wpscn_render_settings_field', 'wpscn_post_notifications', 'wpscn_settings_section',
		array(
			'title' => 'Sender Name',
			'id' 	=> 'wpscn_sender_name',
			'group' => 'wpscn_settings'
		)
    );
	add_settings_field(
        'wpscn_sender_email', '', 'wpscn_render_settings_field', 'wpscn_post_notifications', 'wpscn_settings_section',
		array(
			'title' => 'Sender Email',
			'id' 	=> 'wpscn_sender_email',
			'group' => 'wpscn_settings'
		)
    );
    register_setting('wpscn_post_notifications', 'wpscn_notifications', 'wpscn_post_notifications_validation');
    register_setting('wpscn_post_notifications', 'wpscn_settings', 'wpscn_post_notifications_validation');
}
add_action('admin_init', 'wpscn_create_notifications');

function wpscn_render_settings_field($args){
	$option_value = get_option($args['group']);
?>	
	<label for="<?=$args['group'].'['.$args['id'].']'?>"><?=$args['title']?>: </label>
	<input type="text" id="<?=$args['group'].'['.$args['id'].']'?>" name="<?=$args['group'].'['.$args['id'].']'?>" value="<?=((isset($option_value[$args['id']]))?$option_value[$args['id']]:'')?>"/>
<?php
}

function wpscn_post_notifications_validation($input){
	return $input;
}

function wpscn_render_notification_fields($args){
	if(!$args['new'])
		$option_value = get_option($args['group']);
?>
	<div class="wpscn-notif-form-box">
		<div class="box-head">
			<span class="title"><?=$args['title']?></span>
			<span class="meta"><strong>Post type:</strong> <?=ucfirst($args['post_type'])?> <strong>Recipient:</strong> <?=ucfirst($args['recipient'])?></span>
		</div>
		<div class="box-body">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="<?=$args['group'].'['.$args['id'].'][subject]'?>">Email Subject</label></th>
						<td><input type="text" id="<?=$args['group'].'['.$args['id'].'][subject]'?>" name="<?=$args['group'].'['.$args['id'].'][subject]'?>" value="<?php echo isset($option_value[$args['id']]['subject'])?$option_value[$args['id']]['subject']:''; ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="<?=$args['group'].'['.$args['id'].'][content]'?>">Email Content</label></th>
						<td>
							<?php 
								$content 	= isset($option_value[$args['id']]['content'])?stripslashes(esc_textarea($option_value[$args['id']]['content']) ):''; 
								$editor_id 	= $args['group'].'['.$args['id'].'][content]';
							?>
							<textarea id="<?=$editor_id?>" name="<?=$editor_id?>"><?=$content?></textarea>
							<p class="description">Content of the email. %%AUTHOR%%, %%AUTHOR-FNAME%%, %%AUTHOR-LNAME%%, %%TITLE%% and %%URL%% will be replaced with author's display name, author's first name, author's last name, post title and post url respectively.</p>
							<input type="hidden" id="<?=$args['group'].'['.$args['id'].'][post_type]'?>" name="<?=$args['group'].'['.$args['id'].'][post_type]'?>" value="<?php echo isset($args['post_type'])?$args['post_type']:''; ?>" />
							<input type="hidden" id="<?=$args['group'].'['.$args['id'].'][status_from]'?>" name="<?=$args['group'].'['.$args['id'].'][status_from]'?>" value="<?php echo isset($args['status_from'])?$args['status_from']:''; ?>" />
							<input type="hidden" id="<?=$args['group'].'['.$args['id'].'][status_to]'?>" name="<?=$args['group'].'['.$args['id'].'][status_to]'?>" value="<?php echo isset($args['status_to'])?$args['status_to']:''; ?>" />	
							<input type="hidden" id="<?=$args['group'].'['.$args['id'].'][recipient]'?>" name="<?=$args['group'].'['.$args['id'].'][recipient]'?>" value="<?php echo isset($args['recipient'])?$args['recipient']:''; ?>" />
							<input type="hidden" id="<?=$args['group'].'['.$args['id'].'][format]'?>" name="<?=$args['group'].'['.$args['id'].'][format]'?>" value="<?php echo isset($args['format'])?$args['format']:''; ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="<?=$args['group'].'['.$args['id'].'][extra_emails]'?>">Extra Emails</label></th>
						<td>
							<textarea id="<?=$args['group'].'['.$args['id'].'][extra_emails]'?>" name="<?=$args['group'].'['.$args['id'].'][extra_emails]'?>"><?=isset($option_value[$args['id']]['extra_emails'])?stripslashes(esc_textarea($option_value[$args['id']]['extra_emails']) ):''?></textarea>
							<p class="description">Other email addresses where you'd like to send this notification. One email address per line.</p>
						</td>
					</tr>
				</tbody>
			</table>
			<p>
				<?php submit_button('Save Everything', 'secondary', null, false); ?>
				<?php submit_button('Delete This Notification', 'delete-notification', null, false); ?>
			</p>
		</div>
	</div>
<?php
}

function wpscn_get_notification_fields(){
	$statusFrom 	= $_POST['status_from'];
	$statusTo 		= $_POST['status_to'];
	$post_type 		= $_POST['post_type'];
	$recipient 		= $_POST['recipient'];
	$format 		= $_POST['format'];
	$args 			= array(
						'title' 		=> ucfirst($statusFrom).' to '.ucfirst($statusTo),
						'id' 			=> $post_type.'_'.$statusFrom.'_'.$statusTo.'_'.$recipient,
						'post_type' 	=> $post_type,
						'status_from' 	=> $statusFrom,
						'status_to' 	=> $statusTo,
						'recipient' 	=> $recipient,
						'format' 		=> $format,
						'new' 			=> true,
						'group' 		=> 'wpscn_notifications'
					);
	wpscn_render_notification_fields($args);
	die();
}
add_action( 'wp_ajax_wpscn_get_notification_fields', 'wpscn_get_notification_fields' );

?>