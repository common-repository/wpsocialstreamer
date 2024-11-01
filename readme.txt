=== WPSocialStreamer Lite ===
Contributors: MyArcadePlugin
Donate link: http://myarcadeplugin.com/
Tags: Facebook, Autopost, Facebook Timeline, FB User Wall, Social, Social Network
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Post automatically to user's Facebook Timeline when the user is active on your site!

== Description ==

WPSocialStreamer will automatically post to user's Facebook Timeline when the user is active on your website. We have integrated several events, which can be triggered by users. On each event, a new post will be created on user's Facebook Timeline. WPSocialStreamer Lite support the following activities / events:

= WordPress =

* Publish Posts - When you publish a new post on your site, WPSocialStreamer will send a message with the title and the link of the new post directly to your FB timeline.
* Comments - When a logged in user posts a comment on your site a new message will be posted on his Facebook Timeline.

= GD Star Rating =

* Every user rating will be posted immediately on Facebook!

= WP Favorite Posts =

* When a new post has been added to user's favorites his Facebook Timeline will be updated.

WPSocialStreamer Lite has integrated Facebook Login Feature! A Facebook login button will be added on your WordPress Login Page!

= Do you need more supported Plugins? =

If you want to offer <a href='http://buddypress.org/'>BuddyPress</a>, <a href='http://bbpress.org/'>bbPress</a>, <a href='http://myarcadeplugin.com' title='WP Arcade Plugin'>MyArcadePlugin</a> or  <a href='http://exells.com/shop/arcade-plugins/myarcadecontest/' title='WordPress Contest Plugin'>MyArcadeContest</a> events to you visitors, check our extended <a href='http://exells.com/shop/community/wpsocialstreamer/' title='Autopost to Facbook Timeline'>WPSocialStreamer</a> version. 

In addition to the Lite version WPSocialStreamer supports following events:

= BuddyPress =

* Update Activity Status - If the user posts a new activity on his BuddyPress profile all of his Facebook Friends will see it on his Timeline.
* Update the avatar - If the user updates his avatar a new message will be created.
* Adding Friends - If the user adds a friend the Facebook Friends will see a message.
* Creating Groups -If the user creates a group a new message will be created.
* Joining Groups - If the user joins a group a new Facebook Timeline message will be created.

= BBPress =

* WPSocialStreamer posts automatically to Facebook when a user creates new topic or reply on existing topics.

== Installation ==
= To Install: =

1. Download the ZIP package from exells and unzip it.
2. Within the unzipped folder you will see a ZIP file named "wpsocialstreamer".
3. Log into your WordPress Dashboard and click on Plugins > Add New.
4. Click the "Upload" link, choose to upload the file "wpsocialstreamer.zip"
5. After the upload is finished click on "Activate Plugin" and you are all set! WPSocialStreamer is now installed.

= Usage =
After the installation you have to setup the plugin. Click on "Social Streamer" -> "Admin Settings" and enable desired events.

WPSocialStreamer is able to generate dynamic messages. On the administration panel you will see several placeholders that you can use to generate messages.

On Appearance -> Widgets you will see a new widget "WPSS Facebook Login Button". The widget will display Facebook Login / Logout button.

If you want to add a FB button anywhere in your theme then you can use this function:

<code><?php wpss_display_fb_buttons(); ?></code>

== Upgrade Notice ==

Use WordPress update feature

== Frequently Asked Questions ==

No questions or answers yet. Everything seems to be fine :)

== Screenshots ==

1. Facbook Settings
2. WordPress Settings
3. GD Star Rating
4. WP Favorite Posts
5. User Settings on the WP Dashboard
6. User Settings on BuddyPress Settings Page
7. Integrated Facebook Login

== CHANGELOG ==

= Version: 1.1.0 - 2012-12-24 =
* New: Facebook Login Widget
* New: Function wpss_display_fb_buttons will display the Facebook login button anywhere in your theme
* Fix: Warning on post publishing

= Version: 1.0.0 - 2012-11-17 =
* Initial Release