=== Right Now Reloaded ===
Contributors: SeventhSteel
Donate link: http://mikedance.com
Tags: Right Now, Dashboard, Dashboard widget, widget, widgets, admin, backend, stats, info, statistics, information
Requires at least: 3.4
Tested up to: 3.5.2
Stable tag: 2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A more relevant and dynamic version of the "Right Now" dashboard widget.

== Description ==

Sick of the "Right Now" dashboard widget not showing useful information about your site? "Right Now Reloaded" solves that.

The default Right Now widget shows only information on posts, pages, categories, tags, widgets, and comments. That's great if you're running a standard blog, but what if you don't use comments? What if you have a bunch of custom post types?

That's where Right Now Reloaded comes in. It displays an accurate snapshot of your site: all your public post types and taxonomies, plus active plugins, registered users, links, widgets, and menus, all ordered by importance. Don't use one of those? It won't show up.

Some extra notes:

* No configuration required
* Translation-ready
* Strict permissions - users only see what they should be able to see
* Easily customizable with dynamic CSS classes and IDs
* Retains the `right_now_table_end`, `rightnow_end`, and `activity_box_end` hooks so that other plugins can still hook into the widget

== Installation ==

= Automatic Install =

1. Log into your WordPress dashboard and go to Plugins &rarr; Add New
2. Search for "Right Now Reloaded"
3. Click "Install Now"
4. Click "Activate Now"

= Manual Install =

1. Download the plugin from the download button on this page
2. Unzip the file, and upload the resulting `right-now-reloaded` folder to your `/wp-content/plugins` directory
3. Log into your WordPress dashboard and go to Plugins
4. Activate Right Now Reloaded

By design, Right Now Reloaded has no additional configuration - it will automatically replace the original Right Now widget.

== Changelog ==

= 2.2 =
* Fixed wp_get_theme error.
* Fixed load order so i18n files now load properly.
* Class now uses static methods.

= 2.1 =
* Updated POT.
* Fixed screenshot order.

= 2.0 =
* Complete code overhaul. Object-oriented, smaller footprint.
* Added dynamic CSS classes and IDs to table elements.
* Moved Links to right column.

= 1.0 =
* Initial release.

== Screenshots ==

1. Right Now Reloaded on a simple site
2. What a Contributor sees on the same site as above
3. Right Now Reloaded on a large site with lots of custom content types