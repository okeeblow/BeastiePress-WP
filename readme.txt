=== Plugin Name ===
Contributors: okeeblow
Donate link: http://cooltrainer.org/
Tags: freebsd
Requires at least: 2.8
Tested up to: 3.0.1
Stable tag: 1.0

BeastiePress adds several shortcodes for interaction with the BSD community.

== Description ==

BeastiePress adds several shortcodes for interaction with the BSD community, including linking to ports, manual pages, and problem reports.

This plugin should be compatible with WP 2.8, but I've only tested it on my 3.0 site. Let me know!

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= Is this FreeBSD-specific? =
For now, yes. FreeBSD is my go-to version. The [man] tag, however, links to the freebsd man page browser which has pages from other BSD flavors.

== Screenshots ==

1. The BeastiePress admin page.

== Changelog ==

= 1.0 =
* First release

== Upgrade Notice ==

= 1.0 =
Cleanup from development version, mostly regarding saving and loading options.