=== cbnet Multi Author Comment Notification ===
Contributors: chipbennett
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QP3N9HUSYJPK6
Tags: cbnet, multi, author, comment, comments, comment notification, notification, notify, admin, administrator, email, maxblogpress
Requires at least: 3.7
Tested up to: 3.9
Stable tag: 3.2

Send comment notification and comment moderation emails to multiple users. Select users individually or by user role, or send emails to arbitrary email addresses.

== Description ==

Easily enable email notification of new comments to users other than the post author.

Via Dashboard -> Settings -> Discussion, enable email notification to users by site admin, user role (Administrator, Editor, Author, Contributor, Subscriber ), or define arbitary email addresses to notify. Also, optionally disable email notification for comments left by registered users.

Email notification for individual users can be enabled via each user's profile.

== Installation ==

Manual installation:

1. Upload the `cbnet-multi-author-comment-notification` folder to the `/wp-content/plugins/` directory

Installation using "Add New Plugin"

1. From your Admin UI (Dashboard), use the menu to select Plugins -> Add New
2. Search for 'cbnet Multi Author Comment Notification'
3. Click the 'Install' button to open the plugin's repository listing
4. Click the 'Install' button

Activiation and Use

1. Activate the plugin through the 'Plugins' menu in WordPress
2. From your Admin UI (Dashboard), use the menu to select Settings -> Discussion
3. Configure settings, and save
4. To enable comment notification for individual users, configure "Comment Email Notification" on each user's profile

== Frequently Asked Questions ==

= No emails are being sent. Why? =

The Plugin merely filters the list of email recipients; it does not send email. Be sure that you have enabled (checked) the "Email me whenever" options for either "Anyone posts a comment" or "A comment is held for moderation". If neither of these options is enabled, no emails will be sent.

= Where did settings go? =

Plugin settings can be found under Dashboard -> Settings -> Discussion.

Comment email notification for individual users can be configured via the user profile.

Let me know what questions you have!

== Screenshots ==

Screenshots coming soon.


== Changelog ==

= 3.2 =
* Maintenance Release
 * Fix bug causing PHP notices and preventing sending of emails. Props Flick.
 * Add transients for notification/moderation email address arrays, for performance improvement
= 3.1 =
* Maintenance Release
 * Add option to disable email notification to site admin email address
= 3.0 =
* Maintenance Release
 * Incorporates core filters 'comment_notification_recipients' added to wp_notify_postauthor() and 'comment_moderation_recipients' added to wp_notify_moderator() in WordPress 3.7
 * Removes Pluggable functions wp_notify_postauthor() and wp_notify_moderator() that no longer need to be overwritten
= 2.2.1 =
* Bugfix
 * Fixed issue with bad development file merge causing PHP errors
= 2.2 =
* Feature Release
 * Added option to send comment moderation emails to multiple authors
 * Added filters to support potential core patches to Pluggable functions
= 2.1.2 =
* Bugfix. 
 * Fixed call to deprecated function update_usermeta()
= 2.1.1 =
* Bugfix. 
 * Fix bug with Plugin sending multiple emails to the same email address.
= 2.1 =
* Made Plugin translation-ready
= 2.0.2 =
* Bugfix
 * Fix bug with settings validation callback not accounting for single email address
= 2.0.1 =
* Bugfix
 * Wrap pluggable function in function_exists() conditional.
= 2.0 =
* Major update
 * Plugin completely rewritten
 * Settings API implementation
 * Move Plugin settings from custom settings page to Settings -> Discussion
 * Add custom user meta for individual user email notification
 * Implement via pluggable function wp_notify_postauthor()
 * Made Plugin parameters filterable
 * Removed all cruft code
* WARNING: Old settings will not be retained
= 1.1.2 =
* Bugfix update
* PHP shorttag fixed on line 249. Props Otto42
* isset conditional added for email on line 244. Props Otto42.
= 1.1.1 =
* Readme.txt update
* Updated Donate Link in readme.txt
= 1.1 =
* Initial Release
* Forked from MaxBlogPress Multi Author Comment Notification plugin version 1.0.5


== Upgrade Notice ==

= 3.2 =
Maintenance. Bugfix and performance improvements.
= 3.1 =
Maintenance. Add option to disable notifications for site admin email address.
= 3.0 =
Maintenance. Incorporates new core filters, and removes pluggable functions.
= 2.2.1 =
Bugfix. Fixed issues with bad development file merge.
= 2.2 =
Feature release. Option to add comment moderation emails to multiple authors. New filters.
= 2.1.2 =
Bugfix. Fixed call to deprecated function update_usermeta().
= 2.1.1 =
Bugfix. Fix bug with Plugin sending multiple emails to the same email address.
= 2.1 =
Made Plugin translation-ready
= 2.0.2 =
Bugfix. Fix bug with settings validation callback not accounting for single email address.
= 2.0.1 =
Bugfix. Wrap pluggable function wp_notify_postauthor() in function_exists() wrapper for activation.
= 2.0 =
Major update. Plugin completely rewritten. WARNING: Previous settings will not be retained.
= 1.1.2 =
Bugfix. Two minor PHP notices fixed.
= 1.1.1 =
Readme.txt update. Updated Donate Link in readme.txt.
= 1.1 =
Initial Release. Forked from MaxBlogPress Multi Author Comment Notification plugin version 1.0.5.
