# ExCL Developer Documentation #
An Overview of how to install/configure WordPress, and a look into our custom WordPress plugin

### Contents ###

1. [Introduction to ExCL](#exclIntro)
- [WordPress](#wordpress)
	1. [Introduction to WordPress](#intro)
	- [Setting up a Server](#setup)
	- [Getting Started With WordPress](#getStarted)
	- [Code Overview](#overview)
	- [Enhancing the ExCL WordPress Plugin](#enhancements)
	- [Deploying WordPress](#deployment)
	- [Updating WordPress](#updating)
	- [Helpful Tools](#tools)
- [Titanium](https://github.com/cmhouston/excl-mobile#titanium)
- [Contributing](#contributing)

# <a name="exclIntro"></a> Introduction to ExCL #
ExCL is a platform that enables museums to engage visitors at museum activities through the use of 
a mobile application. Content is managed through a WordPress content management system by museum 
staff, and visitors will download the customized ExCL app, written using [Appcelerator Titanium](http://www.appcelerator.com/titanium/), 
to their mobile device. ExCL is also intended to be used by museums on kiosk devices and provides 
a kiosk mode for this purpose.

ExCL is divided into two parts: the content management system and the Appcelerator Titanium mobile application. This repository is for the WordPress content management system. [Click here to go to the Titanium project](https://github.com/cmhouston/excl-mobile).

This documentation is intended for ExCL developers and details the steps to setup and enhance  
the content management system, which uses WordPress.

If you are a developer, see the [developer documentation](docs/developerDocs.md) for the ExCL Wordpress technical documentation.

# <a name="wordpress"></a> WordPress #

## <a name="intro"></a> Introduction to WordPress ##
WordPress is an open source blogging and content management system (CMS) platform. It is written in 
[PHP](https://php.net/) and uses [MYSQL](http://www.mysql.com/) for the underlying database. WordPress allows powerful customization through plugins. 
ExCL uses some required and some optional plugins to adapt WordPress to its needs.

## <a name="setup"></a> Setting up a Server ##

To allow your app to pull data from your Content Management System it must be hosted online. WordPress suggests a few options for hosting, and they can be found through the [WordPress Web Hosting](http://wordpress.org/hosting/) page.

Once your host is created, created a database for ExCL and a MySQL user.

## <a name="getStarted"></a> Getting Started With Wordpress ##

### Download and Install WordPress ###
ExCL was built on WordPress 3.9.1. For each WordPress environment to set up, download a fresh copy of 
WordPress from [wordpress.org](http://www.wordpress.org). Follow WordPress's [setup instructions](http://codex.wordpress.org/Installing_WordPress) to complete the WordPress installation. We recommend 
that each wordpress environment have its own database, or at the very least its own table prefix.

### Download and Install WordPress Plugins ###
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

### Configure WordPress and Plugins ###
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

## <a name="overview"></a> Code Overview ##

The current plugin used to create the JSON relevant to the ExCL  is the [excl_json_api](wp-content/plugins/excl_json_api). Here are two example JSON files which show what JSON WordPress exports, one for each endpoint:

- Museum
- Component

Here is a brief overview of the files contained in the plugin and the purposes they serve:

- Commenter.php – Used to translate current comments to a JSON format and to transfer comments from the user interface to the content management system
- component.php – Used to implement the component level of the hierarchy. It also contains the Post level (the child of the Component level)
- excl\_api\_controller – General endpoint class that provides functions common across all api endpoints
- excl\_response\_helper.php – packages the JSON and wraps it in a status code
- excl\_utility.php – Contains all functions used to pull and translate data from WordPress. 
- excl\_wp\_logic.php – Contains functions used to manage WordPress content.
- iexcl\_type.php – an interface that each endpoint level inherits from.
- Main.php – the file that “bundles” the other files and creates the WordPress plugin
- Museum.php – Used to implement the Museum level of the hierarchy. It also contains the Exhibit Level (the child of the Museum level)
- WordPressWrapper.php – a header file that excl\_api\_controller builds off of


## <a name="enhancements"></a> Enhancing the ExCL WordPress Plugin ##

As features are added to the mobile app through the [Titanium code](https://github.com/cmhouston/excl-mobile) the WordPress ExCL plugin may have to be updated to accommodate that. However there are a couple enhancements that are already on the road map for ExCL: 

- Remove Quick Edit and Save all Options because they do not function correctly
- Error handling if an ID given does not the post type requested

## <a name="deployment"></a> Deploying WordPress ##

Whenever you make changes to your local copy of excl-cms, you must push those changes to your WordPress server before they will take effect. To do so:

1. Connect to your server using [FileZilla](http://sourceforge.net/projects/filezilla/) (or some other FTP service) 
2. Copy the `wp-content/plugins/excl_json_api` folder to the remote `wp-content/plugins/` directory.
	- This will overwrite the plugin already on your website. 

## <a name="updating"></a> Updating WordPress ##

Periodically WordPress will come out with updates. These can be found on the WordPress website on the [WordPress Updates](https://codex.wordpress.org/Updating_WordPress) page.

One thing to keep in mind when updating WordPress is that plugins may work slightly differently under different versions. This can cause errors between other plugins that are installed so it is recommended to thoroughly test your WordPress CMS system after you update.

## <a name="tools"></a> Helpful Tools ##
Some of the tools that have been helpful to the initial ExCL Developers are:

- [PhpStorm](http://www.jetbrains.com/phpstorm/)
- [XDebug](http://xdebug.org/)
- [FileZilla](http://sourceforge.net/projects/filezilla/)

# <a name="contributing"></a> Contributing #

We welcome contributions to this code. In order to contribute, please follow these steps:

1. Fork this repository
2. Make your changes
3. Submit a pull request to have your changes merged in
4. The pull request will be reviewed by our core team and possibly merged into the master branch
