<?php
/**
 * @package Adminimize
 * @subpackage Global Options
 * @author Frank Bültge
 */
if ( ! function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a part of plugin, not much I can do when called directly.";
	exit;
}
?>

		<div id="poststuff" class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<div class="handlediv" title="<?php _e('Click to toggle'); ?>"><br/></div>
				<h3 class="hndle" id="global_options"><?php _e('Global options', FB_ADMINIMIZE_TEXTDOMAIN ); ?></h3>
				<div class="inside">
					<br class="clear" />

					<table summary="config_edit_post" class="widefat">
						<thead>
							<tr>
								<th><?php _e('Option', FB_ADMINIMIZE_TEXTDOMAIN ); ?></th>
								<?php
									foreach ($user_roles_names as $role_name) { ?>
										<th><?php _e('Deactivate for', FB_ADMINIMIZE_TEXTDOMAIN ); echo '<br/>' . $role_name; ?></th>
								<?php } ?>
							</tr>
						</thead>

						<tbody>
						<?php
							foreach ($user_roles as $role) {
								$disabled_global_option_[$role]  = _mw_adminimize_get_option_value('mw_adminimize_disabled_global_option_'. $role .'_items');
							}
								
							$global_options = array(
																			'.show-admin-bar',
																			'#favorite-actions',
																			'#screen-meta',
																			'#screen-options, #screen-options-link-wrap',
																			'#contextual-help-link-wrap',
																			'#your-profile .form-table fieldset'
																			);
							
							$global_options_names = array(
																			__('Admin Bar', FB_ADMINIMIZE_TEXTDOMAIN),
																			__('Favorite Actions', FB_ADMINIMIZE_TEXTDOMAIN),
																			__('Screen-Meta', FB_ADMINIMIZE_TEXTDOMAIN),
																			__('Screen Options', FB_ADMINIMIZE_TEXTDOMAIN),
																			__('Contextual Help', FB_ADMINIMIZE_TEXTDOMAIN),
																			__('Admin Color Scheme', FB_ADMINIMIZE_TEXTDOMAIN)
																			);
							
							$_mw_adminimize_own_values = _mw_adminimize_get_option_value('_mw_adminimize_own_values');
							$_mw_adminimize_own_values = preg_split( "/\r\n/", $_mw_adminimize_own_values );
							foreach ( (array) $_mw_adminimize_own_values as $key => $_mw_adminimize_own_value ) {
								$_mw_adminimize_own_value = trim($_mw_adminimize_own_value);
								array_push($global_options, $_mw_adminimize_own_value);
							}
							
							$_mw_adminimize_own_options = _mw_adminimize_get_option_value('_mw_adminimize_own_options');
							$_mw_adminimize_own_options = preg_split( "/\r\n/", $_mw_adminimize_own_options );
							foreach ( (array) $_mw_adminimize_own_options as $key => $_mw_adminimize_own_option ) {
								$_mw_adminimize_own_option = trim($_mw_adminimize_own_option);
								array_push($global_options_names, $_mw_adminimize_own_option);
							}
							
							$x = 0;
							foreach ($global_options as $index => $global_option) {
								if ( $global_option != '') {
									$checked_user_role_ = array();
									foreach ($user_roles as $role) {
										$checked_user_role_[$role]  = ( isset($disabled_global_option_[$role]) && in_array($global_option, $disabled_global_option_[$role]) ) ? ' checked="checked"' : '';
									}
									echo '<tr>' . "\n";
									echo '<td>' . $global_options_names[$index] . ' <span style="color:#ccc; font-weight: 400;">(' . $global_option . ')</span> </td>' . "\n";
									foreach ($user_roles as $role) {
										echo '<td class="num"><input id="check_post'. $role . $x .'" type="checkbox"' . $checked_user_role_[$role] . ' name="mw_adminimize_disabled_global_option_'. $role .'_items[]" value="' . $global_option . '" /></td>' . "\n";
									}
									echo '</tr>' . "\n";
									$x++;
								}
							}
						?>
						</tbody>
					</table>
					
					<?php
					//your own global options
					?>
					<br style="margin-top: 10px;" />
					<table summary="config_edit_post" class="widefat">
						<thead>
							<tr>
								<th><?php _e('Your own options', FB_ADMINIMIZE_TEXTDOMAIN ); echo '<br />'; _e('ID or class', FB_ADMINIMIZE_TEXTDOMAIN ); ?></th>
								<th><?php echo '<br />'; _e('Option', FB_ADMINIMIZE_TEXTDOMAIN ); ?></th>
							</tr>
						</thead>

						<tbody>
							<tr valign="top">
								<td colspan="2"><?php _e('It is possible to add your own IDs or classes from elements and tags. You can find IDs and classes with the FireBug Add-on for Firefox. Assign a value and the associate name per line.', FB_ADMINIMIZE_TEXTDOMAIN ); ?></td>
							</tr>
							<tr valign="top">
								<td>
									<textarea name="_mw_adminimize_own_options" cols="60" rows="3" id="_mw_adminimize_own_options" style="width: 95%;" ><?php echo _mw_adminimize_get_option_value('_mw_adminimize_own_options'); ?></textarea>
									<br />
									<?php _e('Possible nomination for ID or class. Separate multiple nominations through a carriage return.', FB_ADMINIMIZE_TEXTDOMAIN ); ?>
								</td>
								<td>
									<textarea class="code" name="_mw_adminimize_own_values" cols="60" rows="3" id="_mw_adminimize_own_values" style="width: 95%;" ><?php echo _mw_adminimize_get_option_value('_mw_adminimize_own_values'); ?></textarea>
									<br />
									<?php _e('Possible IDs or classes. Separate multiple values through a carriage return.', FB_ADMINIMIZE_TEXTDOMAIN ); ?>
								</td>
							</tr>
						</tbody>
					</table>
					
					<p id="submitbutton">
						<input type="hidden" name="_mw_adminimize_action" value="_mw_adminimize_insert" />
						<input class="button button-primary" type="submit" name="_mw_adminimize_save" value="<?php _e('Update Options', FB_ADMINIMIZE_TEXTDOMAIN ); ?> &raquo;" /><input type="hidden" name="page_options" value="'dofollow_timeout'" />
					</p>
					<p><a class="alignright button" href="javascript:void(0);" onclick="window.scrollTo(0,0);" style="margin:3px 0 0 30px;"><?php _e('scroll to top', FB_ADMINIMIZE_TEXTDOMAIN); ?></a><br class="clear" /></p>

				</div>
			</div>
		</div>
