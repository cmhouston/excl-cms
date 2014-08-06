# ExCL Developer Documentation #

### Introduction to WordPress ###
WordPress is an open source blogging and content management system (CMS) platform. It is written in 
PHP and uses MYSQL for the underlying database. WordPress allows powerful customization through plugins. 
ExCL uses some required and some optional plugins to adapt WordPress to its needs.

### Installing WordPress ###
The first step to getting the ExCL WordPress site up and running is installing WordPress.

#### Download and Install WordPress ####
ExCL was built on WordPress 3.9.1. For each WordPress environment to set up, download a fresh copy of 
WordPress from http://www.wordpress.org. Follow the setup instructions at 
http://codex.wordpress.org/Installing_WordPress to complete the WordPress installation. We recommend 
that each wordpress environment have its own database, or at the very least its own table prefix.

#### Download and Install WordPress Plugins ####
Once WordPress is installed, install and activate the following WordPress plugins. The version number is the latest 
ExCL-tested version of the plugin. It is possible that later versions will also work, but it is not guaranteed.

* [Types - Complete Solution for Custom Fields and Types](http://wordpress.org/plugins/types/) (version 1.5.7)
* [Category Order and Taxonomy Terms Order](https://wordpress.org/plugins/taxonomy-terms-order/) (version 1.3.6)
* [cbnet Multi Author Comment Notification](http://wordpress.org/plugins/cbnet-multi-author-comment-notification/) (version 3.2)
* [JSON REST API](https://wordpress.org/plugins/json-rest-api/) (version 1.1)
* [Pending Submission Notification](https://wordpress.org/plugins/pending-submission-notifications/) (version 1.0)
* [Polylang](http://wordpress.org/plugins/polylang/) (version 1.5.3)
* [Right Now Reloaded](https://wordpress.org/plugins/right-now-reloaded/) (version 2.2)
* [Status Change Notifications](https://wordpress.org/plugins/status-change-notifications/) (version 1.0)
* [User Role Editor](https://wordpress.org/plugins/user-role-editor/) (version 4.14.2)

#### Configure WordPress and Plugins ####
After installing WordPress, customize it by following these steps after logging into the admin dashboard:

* Types Plugin - Click on “Types” on the admin sidebar
    * Types -> Import/Export
    * Under the "Import Types data file" section, choose the excl_structure.xml file provided
    	* Check all of the boxes on the entire page
    	* Click import
* Create a new page (probably called “Home Page”) and set this page to show up as the WordPress’s home page under Settings -> Reading
* Settings -> Discussion
    * Ensure that both the “email me whenever” checkboxes are checked
    * Check both the “before a comment appears” checkboxes
    * At the bottom under “cbnet Multi-Author Comment…” settings check every user role who you wish to have receive an email when a comment is created
    * At the bottom under “cbnet Multi-Author Comment…” settings add in any additional emails to receive a notification when a comment is created
    * At the bottom under “cbnet Multi-Author Comment…” settings check every box under “Miscellaneous”
* Settings -> Permalinks
    * Change to “Post Name” under “Common Settings”
    * This will require you to have mod_rewrite enabled if you are using Apache to host your WordPress instance
* Settings -> Duplicate Post
    * Check the "copy children" checkbox
* Settings -> Pending Submission Notifications
    * Enter the email address here to receive notifications when content authors submit their content for approval
* Settings -> Status Change Notifications
    * Delete the pre-existing notifications
    * Add new “Pending to Publish” notifications for each of “component”, “component-post”, “part”
    * At the bottom, enter in the sender email address for who these emails should be sent from 
* Settings -> Languages
    * Add a new language for each language your museum supports, including English
    * Under the Settings tab (at the top), make sure that all of the "Media" and "Custom Post Type" checkboxes are checked
* Settings -> Taxonomy Terms Order
    * Minimum Level to Use this plugin should be whoever you designate to reorder the sections in the app
    * Auto-sort should be ON
    * Admin sort should be checked
	
If you hit any errors during this process, simply refresh the page and try your changes again.

### Installing the ExCL WordPress Plugin ###
TODO

### Running the Unit Tests ###
TODO

### Code Overview ###
TODO

### Enhancing the ExCL WordPress Plugin ###
TODO

### Deploying WordPress ###
TODO

### Updating WordPress ###
TODO

### Contributing ###
