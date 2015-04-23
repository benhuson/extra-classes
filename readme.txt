=== Extra Classes ===
Contributors: husobj
Tags: styles, css, class, classes, menu, menus, nav, navigation, selected
Requires at least: 3.7
Tested up to: 4.2
Stable tag: 0.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds classes for selected menu states such as highlighting categories when viewing a blog post or parent page when viewing an attachment page.

== Description ==

= Add missing classes = 

Adds missing classes for selected menu states. Classes added include:

* `current-page-parent` and `current_page_parent`
* `current-page-ancestor` and `current_page_ancestor`
* `current-menu-ancestor`
* `current-menu-parent`

The classes are added in the following scenarios:

* **When viewing an attachment page** classes are added to any parent pages and their menu ancestors.
* **When viewing a post** classes are added to any relevant taxonomy menu items and their manu ancestors.

= Manage Menu Select States = 

Allows you to control which menu items should be selected (or deselected) when viewing certain pages of your site. You need to add classes to the menu items for this to work.

 * `ecms-archive-{$post_type}` - Select menu item when viewing a post type archive page.
 * `ecms-single-{$post_type}` - Select menu item when viewing a single post type page.
 * `ecms-taxonomy-{$taxonomy}` - Select menu item when viewing a taxonomy archive.
 * `ecms-taxonomy-{$taxonomy}-term-{$term}` - Select menu item when viewing a taxonomy term archive.
 * `ecms-404` - Select menu item when viewing a 404 page.

 * `ecms-no-archive-{$post_type}` - Deselect menu item when viewing a post type archive page.
 * `ecms-no-single-{$post_type}` - Deselect menu item when viewing a single post type page.
 * `ecms-no-taxonomy-{$taxonomy}` - Deselect menu item when viewing a taxonomy archive.
 * `ecms-no-taxonomy-{$taxonomy}-term-{$term}` - Deselect menu item when viewing a taxonomy term archive.
 * `ecms-no-404` - Deselect menu item when viewing a 404 page.

== Installation ==

To install and configure this plugin...

1. Upload or install the plugin through your WordPress admin.
2. Activate the plugin via the 'Plugins' admin menu.
3. No configuration neccessary.

= Upgrading =

If you are upgrading manually via FTP rather that through the WordPress automatic upgrade link, please de-activate and re-activate the plugin to ensure the plugin upgrades correctly.

== Frequently Asked Questions ==

= Where can I report bugs and issues? =
Please log issues and bugs on the plugin's [GitHub page](https://github.com/benhuson/extra-classes/issues).
You can also submit suggested enhancements if you like.

= How can I contribute? =
If you can, please [fork the code](https://github.com/benhuson/extra-classes) and submit a pull request via GitHub. If you're not comfortable using Git, then please just submit it to the issues link above.

== Screenshots ==

None at present.

== Changelog ==

= 0.3.1 =
Fix issue where menu selected states were not inherited correctly for non-404 pages.

= 0.3 =
Added Menu Selector support for 'ecms-404' and 'ecms-no-404' classes.

= 0.2 =
Added Menu Selector class.
Add menus module and 'extraclasses_menu_item_class' filter for easier filtering of menu item classes.
Restructure include files for modules.

= 0.1 =
First release (beta).

== Upgrade Notice ==

= 0.3 =
Added Menu Selector support for 'ecms-404' and 'ecms-no-404' classes.

= 0.2 =
Added Menu Selector class and 'extraclasses_menu_item_class' filter.

= 0.1 =
Just install and activate. No configuration required.
