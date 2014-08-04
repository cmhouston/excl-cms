<?php
/**
 * Plugin Settings API Implementation
 */

/**
 * Globalize Plugin options
 */
global $cbnet_macn_options;
$cbnet_macn_options = cbnet_macn_get_options();

/**
 * Get Plugin options
 */
function cbnet_macn_get_options() {
	return wp_parse_args( get_option( 'plugin_cbnet_macn_options', array() ), cbnet_macn_get_option_defaults() );
}

/**
 * Get Plugin option defaults
 */
function cbnet_macn_get_option_defaults() {
	$defaults = array(	
		'site_admin' => true,
		'all_administrators' => false,
		'all_editors' => false,
		'all_authors' => false,
		'all_contributors' => false,
		'all_subscribers' => false,
		'additional_emails' => false,
		'notify_for_registered_users' => true,
		'notify_for_comment_moderation' => false
	);
	return apply_filters( 'cbnet_macn_option_defaults', $defaults );
}

/**
 * Get Plugin option parameters
 */
function cbnet_macn_get_option_parameters() {
	$defaults = cbnet_macn_get_option_defaults();
	$parameters = array(
		'site_admin' => array(
			'name' => 'site_admin',
			'title' => __( 'Site Admin', 'cbnet-multi-author-comment-notification' ),
			'description' => __( 'Send comment notification emails to the site admin email address', 'cbnet-multi-author-comment-notification' ),
			'type' => 'checkbox',
			'default' => $defaults['site_admin']
		),
		'all_administrators' => array(
			'name' => 'all_administrators',
			'title' => __( 'All Administrators', 'cbnet-multi-author-comment-notification' ),
			'description' => __( 'Send comment notification emails to all Administrators', 'cbnet-multi-author-comment-notification' ),
			'type' => 'checkbox',
			'default' => $defaults['all_administrators']
		),
		'all_editors' => array(
			'name' => 'all_editors',
			'title' => __( 'All Editors', 'cbnet-multi-author-comment-notification' ),
			'description' => __( 'Send comment notification emails to all Editors', 'cbnet-multi-author-comment-notification' ),
			'type' => 'checkbox',
			'default' => $defaults['all_editors']
		),
		'all_authors' => array(
			'name' => 'all_authors',
			'title' => __( 'All Authors', 'cbnet-multi-author-comment-notification' ),
			'description' => __( 'Send comment notification emails to all Authors', 'cbnet-multi-author-comment-notification' ),
			'type' => 'checkbox',
			'default' => $defaults['all_authors']
		),
		'all_contributors' => array(
			'name' => 'all_contributors',
			'title' => __( 'All Contributors', 'cbnet-multi-author-comment-notification' ),
			'description' => __( 'Send comment notification emails to all Contributors', 'cbnet-multi-author-comment-notification' ),
			'type' => 'checkbox',
			'default' => $defaults['all_contributors']
		),
		'all_subscribers' => array(
			'name' => 'all_subscribers',
			'title' => __( 'All Subscribers', 'cbnet-multi-author-comment-notification' ),
			'description' => __( 'Send comment notification emails to all Subscribers', 'cbnet-multi-author-comment-notification' ),
			'type' => 'checkbox',
			'default' => $defaults['all_subscribers']
		),
		'additional_emails' => array(
			'name' => 'additional_emails',
			'title' => __( 'Additional Email Addresses', 'cbnet-multi-author-comment-notification' ),
			'description' => __( 'Send comment notification emails to these additional email addresses', 'cbnet-multi-author-comment-notification' ),
			'type' => 'text',
			'sanitize' => 'emailarray',
			'default' => $defaults['additional_emails']
		),
		'notify_for_registered_users' => array(
			'name' => 'notify_for_registered_users',
			'title' => __( 'Registered-User Comments', 'cbnet-multi-author-comment-notification' ),
			'description' => __( 'Send notification emails for comments from registered users', 'cbnet-multi-author-comment-notification' ),
			'type' => 'checkbox',
			'default' => $defaults['notify_for_registered_users']
		),
		'notify_for_comment_moderation' => array(
			'name' => 'notify_for_comment_moderation',
			'title' => __( 'Moderated Comment Notification', 'cbnet-multi-author-comment-notification' ),
			'description' => __( 'Send notification emails for comments held for moderation', 'cbnet-multi-author-comment-notification' ),
			'type' => 'checkbox',
			'default' => $defaults['notify_for_comment_moderation']
		),
	);
	return apply_filters( 'cbnet_macn_option_parameters', $parameters );
}
 
/**
 * Register Plugin Settings
 */
function cbnet_macn_register_settings() {

	/**
	* Register Favicon setting
	* 
	* Registers Favicon setting as
	* part of core General settings
	*/
	register_setting( 'discussion', 'plugin_cbnet_macn_options', 'cbnet_macn_validate_settings' );

	/**
	 * Add settings section to Settings -> Discussion
	 */
	add_settings_section( 'cbnet-multi-author-comment-notification', __( 'cbnet Multi-Author Comment Notification Settings', 'cbnet-multi-author-comment-notification' ), 'cbnet_macn_settings_section', 'discussion' );	
	
	/**
	 * Discussion settings section callback
	 */
	function cbnet_macn_settings_section() {
		echo '<p>' . __( 'Configure cbnet Multi-Author Comment Notification settings here. These settings will only apply if the "Email me whenever" setting is enabled for either comment notification or comment modeation.', 'cbnet-multi-author-comment-notification' ) . '</p>';
	}

	/**
	* Add user roles setting field
	* 
	* Adds setting fields to 
	* Settings -> Discussion
	*/
	add_settings_field( 'cbnet_macn_user_roles', '<label for="cbnet_macn_user_roles">' . __( 'User Roles' , 'cbnet-multi-author-comment-notification' ) . '</label>', 'cbnet_macn_settings_field_user_roles', 'discussion', 'cbnet-multi-author-comment-notification' );
	
	/**
	 * User roles setting fields callback
	 */
	function cbnet_macn_settings_field_user_roles() {
		global $cbnet_macn_options;
		$option_parameters = cbnet_macn_get_option_parameters();
		?>
		<p>
			<input type="checkbox" name="plugin_cbnet_macn_options[site_admin]" value="true" <?php checked( true == $cbnet_macn_options['site_admin'] ); ?>>
			<?php echo $option_parameters['site_admin']['description']; ?>
			<br />
			<input type="checkbox" name="plugin_cbnet_macn_options[all_administrators]" value="true" <?php checked( true == $cbnet_macn_options['all_administrators'] ); ?>>
			<?php echo $option_parameters['all_administrators']['description']; ?>
			<br />
			<input type="checkbox" name="plugin_cbnet_macn_options[all_editors]" value="true" <?php checked( true == $cbnet_macn_options['all_editors'] ); ?>>
			<?php echo $option_parameters['all_editors']['description']; ?>
			<br />
			<input type="checkbox" name="plugin_cbnet_macn_options[all_authors]" value="true" <?php checked( true == $cbnet_macn_options['all_authors'] ); ?>>
			<?php echo $option_parameters['all_authors']['description']; ?>
			<br />
			<input type="checkbox" name="plugin_cbnet_macn_options[all_contributors]" value="true" <?php checked( true == $cbnet_macn_options['all_contributors'] ); ?>>
			<?php echo $option_parameters['all_contributors']['description']; ?>
			<br />
			<input type="checkbox" name="plugin_cbnet_macn_options[all_subscribers]" value="true" <?php checked( true == $cbnet_macn_options['all_subscribers'] ); ?>>
			<?php echo $option_parameters['all_subscribers']['description']; ?>
			<br />
		</p>
		<?php
	}

	/**
	* Add additional emails setting field
	* 
	* Adds setting field to 
	* Settings -> Discussion
	*/
	add_settings_field( 'cbnet_macn_additional_emails', '<label for="cbnet_macn_additional_emails">' . __( 'Additional Emails' , 'cbnet-multi-author-comment-notification' ) . '</label>', 'cbnet_macn_settings_field_additional_emails', 'discussion', 'cbnet-multi-author-comment-notification' );
	
	/**
	 * Additional emails setting fields callback
	 */
	function cbnet_macn_settings_field_additional_emails() {
		global $cbnet_macn_options;
		$option_parameters = cbnet_macn_get_option_parameters();
		$additional_emails = ( is_array( $cbnet_macn_options['additional_emails'] ) ? implode( $cbnet_macn_options['additional_emails'], ',' ) : '' );
		?>
		<p>
			<input type="text" size="80" name="plugin_cbnet_macn_options[additional_emails]" value="<?php echo esc_attr( $additional_emails ); ?>" />
			<br />
			<?php echo $option_parameters['additional_emails']['description']; ?>
		</p>
		<?php
	}

	/**
	* Add Miscellaneous setting field
	* 
	* Adds setting field to 
	* Settings -> Discussion
	*/
	add_settings_field( 'cbnet_macn_misc', '<label for="cbnet_macn_misc">' . __( 'Miscellaneous' , 'cbnet-multi-author-comment-notification' ) . '</label>', 'cbnet_macn_settings_field_misc', 'discussion', 'cbnet-multi-author-comment-notification' );
	
	/**
	 * Miscellaneous setting fields callback
	 */
	function cbnet_macn_settings_field_misc() {
		global $cbnet_macn_options;
		$option_parameters = cbnet_macn_get_option_parameters();
		?>
		<p>
			<input type="checkbox" name="plugin_cbnet_macn_options[notify_for_registered_users]" value="true" <?php checked( true == $cbnet_macn_options['notify_for_registered_users'] ); ?>>
			<?php echo $option_parameters['notify_for_registered_users']['description']; ?>
		</p>
		<p>
			<input type="checkbox" name="plugin_cbnet_macn_options[notify_for_comment_moderation]" value="true" <?php checked( true == $cbnet_macn_options['notify_for_comment_moderation'] ); ?>>
			<?php echo $option_parameters['notify_for_comment_moderation']['description']; ?>
		</p>
		<?php
	}
}
add_action( 'admin_init', 'cbnet_macn_register_settings' );



/**
 * Plugin register_setting() sanitize callback
 * 
 * Validate and whitelist user-input data before updating Plugin 
 * Options in the database. Only whitelisted options are passed
 * back to the database, and user-input data for all whitelisted
 * options are sanitized.
 * 
 * @link	http://codex.wordpress.org/Data_Validation	Codex Reference: Data Validation
 * 
 * @param	array	$input	Raw user-input data submitted via the Plugin Settings page
 * @return	array	$input	Sanitized user-input data passed to the database
 */
function cbnet_macn_validate_settings( $input ) {

	// This is the "whitelist": current settings
	global $cbnet_macn_options;
	$valid_input = $cbnet_macn_options;
	// Get the array of option parameters
	$option_parameters = cbnet_macn_get_option_parameters();
	// Get the array of option defaults
	$option_defaults = cbnet_macn_get_option_defaults();
	
	// Determine what type of submit was input
	$submittype = ( ! empty( $input['reset'] ) ? 'reset' : 'submit' );	
	
	// Loop through each setting
	foreach ( $option_defaults as $setting => $value ) {
		
		// If submit, validate/sanitize $input
		if ( 'submit' == $submittype ) {
		
			// Get the setting details from the defaults array
			$optiondetails = $option_parameters[$setting];
			// Get the array of valid options, if applicable
			$valid_options = ( isset( $optiondetails['valid_options'] ) ? $optiondetails['valid_options'] : false );
			
			// Validate checkbox fields
			if ( 'checkbox' == $optiondetails['type'] ) {
				// If input value is set and is true, return true; otherwise return false
				$valid_input[$setting] = ( ( isset( $input[$setting] ) && true == $input[$setting] ) ? true : false );
			}
			// Validate radio button fields
			else if ( 'radio' == $optiondetails['type'] ) {
				// Only update setting if input value is in the list of valid options
				$valid_input[$setting] = ( array_key_exists( $input[$setting], $valid_options ) ? $input[$setting] : $valid_input[$setting] );
			}
			// Validate select fields
			else if ( 'select' == $optiondetails['type'] ) {
				// Only update setting if input value is in the list of valid options
				$valid_input[$setting] = ( in_array( $input[$setting], $valid_options ) ? $input[$setting] : $valid_input[$setting] );
			}
			// Validate text input and textarea fields
			else if ( ( 'text' == $optiondetails['type'] || 'textarea' == $optiondetails['type'] ) ) {
				// Validate no-HTML content
				if ( 'nohtml' == $optiondetails['sanitize'] ) {
					// Pass input data through the wp_filter_nohtml_kses filter
					$valid_input[$setting] = wp_filter_nohtml_kses( $input[$setting] );
				}
				// Validate HTML content
				if ( 'html' == $optiondetails['sanitize'] ) {
					// Pass input data through the wp_filter_kses filter
					$valid_input[$setting] = wp_filter_kses( $input[$setting] );
				}
				// Validate integer content
				if ( 'integer' == $optiondetails['sanitize'] ) { 
					// Verify value is an integer
					$valid_input[$setting] = ( is_int( (int) $input[$setting] ) ? $input[$setting] : $valid_input[$setting] );
				}
				// Validate RGB content
				if ( 'rgb' == $optiondetails['sanitize'] ) { 
					// Verify value is an integer
					$valid_input[$setting] = ( ( is_int( (int) $input[$setting] ) && 0 <= (int) $input[$setting] && 255 >= (int) $input[$setting] ) ? $input[$setting] : $valid_input[$setting] );
				}
				// Validate Email Array content
				if ( 'emailarray' == $optiondetails['sanitize'] ) {
					// Create array
					if ( ! is_array( $input[$setting] ) && false == strpos( $input[$setting], ',' ) ) {
						$input_emails = array( $input[$setting] );
					} else {
						$input_emails = ( '' != $input[$setting] ? explode( ',', $input[$setting] ) : false );
					}
					// Verify values are valid email addresses
					if ( false != $input_emails ) {
						foreach ( $input_emails as $email ) {
							$email = trim( $email );
							if ( ! is_email( $email ) ) { unset( $input_emails[$email] ); }
						}
					}
					$valid_input[$setting] = ( ! empty( $input_emails ) ? $input_emails : false );
				}
			}
		} 
		// If reset, reset defaults
		elseif ( 'reset' == $submittype ) {
			// Set $setting to the default value
			$valid_input[$setting] = $option_defaults[$setting];
		}
		
		// Delete transients
		delete_site_transient( 'cbnet_macn_moderation_email_addresses' );
		delete_site_transient( 'cbnet_macn_notification_email_addresses' );
	}
	return $valid_input;		

}
?>