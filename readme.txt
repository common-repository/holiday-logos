=== Holiday Logos ===
Contributors: CONCEPTiNET™
Donate link: http://holidaylogos.com
Tags: Holiday Logos, Predefined Holiday
Requires at least: 4.0
License: GPLv2 or later
Tested up to: 4.5
Stable tag: 1.1.6

Automatically changes your logo, image, background, or video based on the date.

== Description ==

Includes some pre-loaded US holidays. Unlimited number of days available. Make custom holidays like sporting events, current events, sales, birthdays, and more. Holiday logos may be easily shown before or after the actual date. Make your site dynamic by changing the logo, background, or video based on events.

= Features =

* Option to change up to 2 image locations and 1 video location based on date.
* Alt descriptions for images
* Fixed dates or range of dates
* Option to repeat event every year
* Default placeholder when no event is selected
* Reporting of all events. Sort by Name or Start Date.
* Export report to CSV
* Works on iOS, Android and other mobile devices

Please install the plugin and take it for a spin.


== Screenshots ==

1. Individual Holiday Logos Settings
2. Holiday Logos General Settings
3. Holiday Logos Report


= Technical support =

This plugin is available for free download. If you have any questions or recommendations regarding the functionality of  this plugin (existing options, new options, current issues), please feel free to contact us: http://holidaylogos.com. Please note that we accept requests in English only. All messages in another languages won't be accepted.


If this has helped you and saved you time please consider a donation via PayPal to:
paypal@conceptinet.com

== Installation ==

1. Upload and extract the zip file downloaded to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place <strong>[holidayevent types="Logo"]</strong>  in your widget where you want the show logo.
4. If you want to add Logo shortcode  directly into file then please use it in php tag. do_shortcode('[holidayevent types="Logo"]').
5. Place <strong>[holidayevent types="Image"]</strong>  in your widget where you want the show Image.
6. If you want to add Image shortcode  directly into file then please uuse it in php tag. do_shortcode('[holidayevent types="Image"]').
7. Place <strong>[holidayevent types="Video"]</strong>  in your widget where you want the show Video. do_shortcode('[holidayevent types="Video"]').
	

== Frequently Asked Questions ==


= How can I use this plugin in template file?  =
If you want to add Holiday Logos shortcode  directly into template then please use 
`<?php do_shortcode('[holidayevent types="Logo"]'); ?>`
				




== Changelog ==

= 1.1.6 =
Bugfix: correct report date format

= 1.1.5 =
Bugfix: Image change triggers propelry
Feature: Store dates as timestamp
Feature: Floating rules options for floating holidays
Feature: Dynamic date creation for predefined events

= 1.1.4 =
Bugfix: backwards-compatibilty with php 5.3 and earlier

= 1.1.2 =
Bugfix: correct callbacks for activate/deactivate
Bugfix: preserve user data on deactivate
Bugfix: install sample data only if no holidays are published or present

= 1.1.0 =
Feature: client-side validation
Security: sanitize user input
Security: prevent direct calls to files
Bugfix: fall back to default video correctly
Bugfix: use admin_url to get the bashboard URL
Bugfix: enqueue scripts correctly


= 1.0.2 =
Bugfix: remove checks for unused $_POST vars in elements/holiday-custom.php

= 1.0.1 =
Feature: switch to modern media upload
Security: add nonce check to settings form

= 1.0 =
Added report sorting and export csv



== Upgrade Notice ==

= 1.0 =
Added reporting feature


== Translations ==

* English - default, always included


== Credits ==
* Thanks to Chris M., Dani K., Shanti Infotech, Alex D., Raju R., and Rajesh T. for your assistance.
