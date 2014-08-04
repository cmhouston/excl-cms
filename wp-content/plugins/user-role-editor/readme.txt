=== User Role Editor ===
Contributors: shinephp
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=vladimir%40shinephp%2ecom&lc=RU&item_name=ShinePHP%2ecom&item_number=User%20Role%20Editor%20WordPress%20plugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: user, role, editor, security, access, permission, capability
Requires at least: 3.5
Tested up to: 4.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

User Role Editor WordPress plugin makes the role capabilities changing easy. You can change any WordPress user role.

== Description ==

With User Role Editor WordPress plugin you can change user role (except Administrator) capabilities easy, with a few clicks.
Just turn on check boxes of capabilities you wish to add to the selected role and click "Update" button to save your changes. That's done. 
Add new roles and customize its capabilities according to your needs, from scratch of as a copy of other existing role. 
Unnecessary self-made role can be deleted if there are no users whom such role is assigned.
Role assigned every new created user by default may be changed too.
Capabilities could be assigned on per user basis. Multiple roles could be assigned to user simultaneously.
You can add new capabilities and remove unnecessary capabilities which could be left from uninstalled plugins.
Multi-site support is provided.

To read more about 'User Role Editor' visit [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/) at [shinephp.com](http://shinephp.com)
Русская версия этой статьи доступна по адресу [ru.shinephp.com](http://ru.shinephp.com/user-role-editor-wordpress-plugin-rus/)

Do you need more functionality with quality support in real time? Do you wish remove advertisements from User Role Editor pages? 
Buy [Pro version](htpp://role-editor.com). 
Pro version includes extra modules:
<ul>
<li>Block selected admin menu items for role.</li>
<li>"Export/Import" module. You can export user roles to the local file and import them then to any WordPress site or other sites of the multi-site WordPress network.</li> 
<li>Roles and Users permissions management via Network Admin  for multisite configuration. One click Synchronization to the whole network.</li>
<li>Per posts/pages users access management to post/page editing functionality.</li>
<li>Per plugin users access management for plugins activate/deactivate operations.</li>
<li>Per form users access management for Gravity Forms plugin.</li>
<li>Shortcode to show enclosed content to the users with selected roles only.</li>
<li>Posts and pages view restrictions for selected roles.</li>
</ul>
Pro version is advertisement free. Premium support is included. It is provided by User Role Editor plugin developer Vladimir Garagulya. You will get an answer on your question not once a week, but in 24 hours or quicker.

== Installation ==

Installation procedure:

1. Deactivate plugin if you have the previous version installed.
2. Extract "user-role-editor.zip" archive content to the "/wp-content/plugins/user-role-editor" directory.
3. Activate "User Role Editor" plugin via 'Plugins' menu in WordPress admin menu. 
4. Go to the "Users"-"User Role Editor" menu item and change your WordPress standard roles capabilities according to your needs.

== Frequently Asked Questions ==
- Does it work with WordPress in multi-site environment?
Yes, it works with WordPress multi-site. By default plugin works for every blog from your multi-site network as for locally installed blog.
To update selected role globally for the Network you should turn on the "Apply to All Sites" checkbox. You should have superadmin privileges to use User Role Editor under WordPress multi-site.
Pro version allows to manage roles of the whole network from the Netwok Admin.

To read full FAQ section visit [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/#faq) at [shinephp.com](shinephp.com).

== Screenshots ==
1. screenshot-1.png User Role Editor main form
2. screenshot-2.png Add/Remove roles or capabilities
3. screenshot-3.png User Capabilities link
4. screenshot-4.png User Capabilities Editor
5. screenshot-5.png Bulk change role for users without roles

To read more about 'User Role Editor' visit [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/) at [shinephp.com](shinephp.com).

= Translations =
* Catalan: [Efraim Bayarri](http://replicantsfactory.com/);
* Hebrew: [atar4u](http://atar4u.com)
* Korean: [Taek Yoon](http://www.ajinsys.com)
* Persian: Morteza
* Russian: [Vladimir Garagulya](http://role-editor.com)
* Spanish: [Dario Ferrer](http://darioferrer.com/);
* Turkish: [Muhammed YILDIRIM](http://ben.muhammed.im);

Information for translators: All translations (except Russian) are outdated and need update for new added text.

Dear plugin User!
If you wish to help me with this plugin translation I very appreciate it. Please send your language .po and .mo files to vladimir[at-sign]shinephp.com email. Do not forget include you site link in order I can show it with greetings for the translation help at shinephp.com, plugin settings page and in this readme.txt file.
If you have better translation for some phrases, send it to me and it will be taken into consideration. You are welcome!
Share with me new ideas about plugin further development and link to your site will appear here.


== Changelog ==
= 4.14.3 =
* 25.07.2014
* Integer "1" as default capability value for new added empty role was excluded for the better compatibility with WordPress core. Boolean "true" is used instead as WordPress itself does.
* Integration with Gravity Forms permissions system was enhanced for WordPress multisite.

= 4.14.2 =
* 18.07.2014
* The instance of main plugin class User_Role_Editor is available for other developers now via $GLOBALS['user_role_editor']
* Compatibility issue with the theme ["WD TechGoStore"](http://wpdance.com) is resolved. This theme loads its JS and CSS stuff for admin backend uncoditionally - for all pages. While the problem is caused just by CSS URE unloads all this theme JS and CSS for optimizaiton purpose for WP admin backend pages where conflict is possible.

= 4.14.1 =
* 13.06.2014
* MySQL query optimizing to reduce memory consumption. Thanks to [SebastiaanO](http://wordpress.org/support/topic/allowed-memory-size-exhausted-fixed).
* Extra WordPress nonce field was removed from the post at main role editor page to exclude nonce duplication.
* Minor code enhancements.
* Fixes for some missed translations.

= 4.14 =
* 16.05.2014
* Persian translation was added. Thanks to Morteza.

= 4.12 =
* 22.04.2014
* Bug was fixed. It had prevented bulk move users without role (--No role for this site--) to the selected role in case such users were shown more than at one WordPress Users page.
* Korean translation was added. Thanks to [Taek Yoon](http://www.ajinsys.com).
* Pro version update notes:
* Use new "Admin Menu" button to block selected admin menu items for role. You need to activate this module at the "Additional Modules". This feature is useful when some of submenu items are restricted by the same user capability,
e.g. "Settings" submenu, but you wish allow to user work just with part of it. You may use "Admin Menu" dialog as the reference for your work with roles and capabilities as "Admin Menu" shows 
what user capability restrict access to what admin menu item.
* Posts/Pages edit restriction feature does not prohibit to add new post/page now. Now it should be managed via 'create_posts' or 'create_pages' user capabilities.
* If you use Posts/Pages edit restriction by author IDs, there is no need to add user ID to allow him edit his own posts or page. Current user is added to the allowed authors list automatically.
* New tab "Additional Modules" was added to the User Role Editor options page. As per name all options related to additional modules were moved there.

= 4.11 =
* 06.04.2014
* Single-site: It is possible to bulk move users without role (--No role for this site--) to the selected role or automatically created role "No rights" without any capabilities. Get more details at http://role-editor.com/no-role-for-this-site/
* Plugin uses for dialogs jQuery UI CSS included into WordPress package.
* Pro version: It is possible to restrict editing posts/pages by its authors user ID (targeted user should have edit_others_posts or edit_others_pages capability).
* Pro version, multi-site: Superadmin can setup individual lists of themes available for activation to selected sites administrators.
* Pro version, Gravity Forms access restriction module was tested and compatible with Gravity Forms version 1.8.5

= 4.10 =
* 15.02.2014
* Security enhancement: WordPress text translation functions were replaced with more secure esc_html__() and esc_html_e() variants.
* Pro version: It is possible to restrict access to the post or page content view for selected roles. Activate the option at plugin "Settings" page and use new "Content View Restrictions" metabox at post/page editor to setup content view access restrictions.
* Pro version: Gravity Forms access management module was updated for compatibility with Gravity Forms version 1.8.3. If you need compatibility with earlier Gravity Forms versions, e.g. 1.7.9, use User Role Editor version 4.9.


= 4.9 =
* 19.01.2014
* New tab "Default Roles" was added to the User Role Editor settings page. It is possible to select multiple default roles to assign them automatically to the new registered user.
* CSS and dialog windows layout various enhancements.
* 'members_get_capabilities' filter was applied to provide better compatibility with themes and plugins which may use it to add its own user capabilities.
* jQuery UI CSS was updated to version 1.10.4.
* Pro version: Option was added to download jQuery UI CSS from the jQuery CDN.
* Pro version: Bug was fixed: Plugins activation assess restriction section was not shown for selected user under multi-site environment.


= 4.8 =
* 10.12.2013
* Role ID validation rule was added to prohibit numeric role ID - WordPress does not support them.
* Plugin "Options" page was divided into sections (tabs): General, Multisite, About. Section with information about plugin author, his site, etc. was moved from User Role Editor main page to its "Options" page - "About" tab.
* HTML markup was updated to provide compatibility with upcoming WordPress 3.8 new administrator backend theme "MP6".
* Restore previous blog 'switch_to_blog($old_blog_id)' call was replaced to 'restore_current_blog()' where it is possible to provide better compatibility with WordPress API.
After use 'switch_to_blog()' in cycle, URE clears '_wp_switched_stack' global variable directly instead of call 'restore_current_blog()' inside the cycle to work faster.
* Pro version: It is possible to restrict access of single sites administrators to the selected user capabilities and Add/Delete role operations inside User Role Editor.
* Pro version:  Shortcode [user_role_editor roles="none"]text for not logged in users[/user_role_editor] is available. Recursive processing of other shortcodes inside enclosed text is available now.
* Pro version: Gravity Forms available at "Export Entries", "Export Forms" pages is under URE access restriction now, if such one was set for the user.
* Pro version: Gravity Forms import was set under "gravityforms_import" user capability control.
* Pro version: Option was added to show/hide help links (question signs) near the capabilities from single site administrators.

= 4.7 =
* 04.11.2013
* "Delete Role" menu has "Delete All Unused Roles" menu item now.
* More detailed warning was added before fulfill "Reset" roles command in order to reduce accident use of this critical operation.
* Bug was fixed at Ure_Lib::reset_user_roles() method. Method did not work correctly for the rest sites of the network except the main blog.
* Pro version: Post/Pages editing restriction could be setup for the user by one of two modes: 'Allow' or 'Prohibit'.
* Pro version: Shortcode [user_role_editor roles="role1, role2, ..."]bla-bla[/user_role_editor] for posts and pages was added. 
You may restrict access to content inside this shortcode tags this way to the users only who have one of the roles noted at the "roles" attribute.
* Pro version: If license key was installed it is shown as asterisks at the input field.
* Pro version: In case site domain change you should input license key at the Settings page again.

= 4.6 =
* 21.10.2013
* Multi-site: 'unfiltered_html' capability marked as deprecated one. Read this post for more information (http://shinephp.com/is-unfiltered_html-capability-deprecated/).
* Multi-site: 'manage_network%' capabilities were included into WordPress core capabilities list.
* On screen help was added to the "User Role Editor Options" page - click "Help" at the top right corner to read it.
* Bug fix: turning off capability at the Administrator role fully removed that capability from capabilities list.
* Various internal code enhancements.
* Information about GPLv2 license was added to show apparently - "User Role Editor" is licensed under GPLv2 or later.
* Pro version only: Multi-site: Assign roles and capabilities to the users from one point at the Network Admin. Add user with his permissions together to all sites of your network with one click.
* Pro version only: 'wp-content/uploads' folder is used now instead of plugin's own one to process file with importing roles data.
* Pro version only: Bug fix: Nonexistent method was called to notify user about folder write permission error during roles import.


Click [here](http://role-editor.com/changelog)</a> to look at [the full list of changes](http://role-editor.com/changelog) of User Role Editor plugin.


== Additional Documentation ==

You can find more information about "User Role Editor" plugin at [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/)

I am ready to answer on your questions about plugin usage. Use [ShinePHP forum](http://shinephp.com/community/forum/user-role-editor/) or [plugin page comments](http://www.shinephp.com/user-role-editor-wordpress-plugin/) for it please.
