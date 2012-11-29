=== Youtube Profile Field ===
Contributors: Jayjdk
Donate link: none
Tags: youtube, video, rss, feed, profile, field, username, user, shortcode, oembed
Requires at least: 3.1
Tested up to: 3.5
Stable tag: trunk

Adds an additional field to the user profile page and allows you to show your videos on your website.

== Description ==

Youtube Profile Field allows you to show your latest Youtube videos on your blog. Just enter your Youtube username in the profile and you're ready to show your videos to the world!

You can show your videos either by using a template tag or by using a shortcode, that you can use in posts, pages, and text widgets.

== Installation ==

1. Install the plugin either via the WordPress.org plugin directory, or by uploading the files to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to `Users > Your Profile` and enter your Youtube username in the field called "Youtube username" under "Contact Info"
4. Optional: Go to the settings page `Settings > Youtube Profile Field` and change the settings to fit you.


== Frequently Asked Questions ==

= How do I use the shortcode? =

The shortcode is: `[youtube-user]`

You can use these parameters:

* count		- The number of videos you want to show. Ex: `[youtube-user count="2"]`
* user_id	- The ID of the user you want to show videos from. Default is the post author. Ex:  `[youtube-user user="3"]`
* width 	- If you want a different width than the default
* height	- If you want a different height than the default. If you leave it empty, the plugin will calculate a height using the width
* ID 		- If you want to show a specific video you can use this. You **shouldn't** really use this since WordPress now supports oEmbed.

= How do I use the template tag? =

The template tag is 

<code>
if ( function_exists( 'ypf_show_video' ) ) {
	echo ypf_show_video();
}
</code>

It accepts an array of parameters.

You can use these parameters:

* count 		- The number of videos you want to show. You can use 'all' to show all that the Youtube RSS contains
* user_id		- The ID of the user you want to show videos from. Default is the post author.
* width 		- If you want a different width than the default
* height		- If you want a different height than the default. If you leave it empty, the plugin will calculate a height using the width
* headingStart		- If you want to overwrite the default.
* headingEnd		- If you want to overwrite the default.

Example:
<code>
if ( function_exists( 'ypf_show_video' ) ) {
	echo ypf_show_video( array( 'count' => 2, 'user_id' => 2 ) ); // 2 videos from user ID 2
}
</code>


= The video size is strange - What's wrong? =
If you don't enter video sizes in the plugin options, it will use the width from 'Media' and calculate the height using the 16:9 aspect ratio.

Here's some video sizes:

* 480x295
* 560x340
* 640x385
* 853x505

= After upgrading to 2.0.2 my videos won't show anymore - What's wrong? =
In 2.0.2 has the <code>show_video()</code> function been renamed to <code>ypf_show_video()</code> to prevent possible conflicts with other plugins/themes.

<code>
if ( function_exists( 'ypf_show_video' ) ) {
	echo ypf_show_video();
}
</code>


== Screenshots ==

1. A page with videos
2. The profile page with the Youtube username field

== Changelog ==

= 3.0 =
* The videos are now embedded using oEmbed which means that WordPress can cache them
* Many bug fixes - mostly bugs where parameters didn't work as indended
* Code improvements
* Youtube API version 2.0
* Tested with WordPress 3.5

= 2.2.5 =
* Add `?wmode=transparent` to the iframe

= 2.2 =
* HUGE code improvements
* The `ypf_show_video()` function now takes an array of options instead of parameters
* Both the shortcode and template tag now accepts more arguments (see FAQ)
* You can no longer use the old embed code
* Small bug fixes

= 2.1 =
* Plugin completely rewritten
* Uses the iframe embed by default (supports both HTML5 and Flash). Added option to use the old
* Requires at least 3.1
* Now removes the plugin options when you uninstall the plugin
* If using the template tag, make sure to have <code>echo</code> in front of it.
* A lot of bug fixes

= 2.0.3 =
* Shortcode bug fix

= 2.0.2 =
* <code>show_video()</code> has been renamed to <code>ypf_show_video()</code> - You'll have to update your template files if you use the template tag
* <code>Fatal error: Call to undefined method WP_Error::get_item_quantity()</code> bug fixed
* Shortcode bug fix
* Code clean up and other bug fixes
* No more 2.8 support

= 2.0.1 =
* Support for full screen

= 2.0 =
* 2.9 support
* The plugin now uses the SimplePie included in the WordPress core
* Plugin options panel
* Can now be translated

= 1.0.5 =
* Shortcode: Added the ID parameter to the shortcode

= 1.0 =
* Initial Release


== Upgrade Notice ==

= 2.0 =
2.9 support and a plugin options panel.

= 2.0.1 =
Support for full screen

= 2.0.2 =
show_video() has been renamed to ypf_show_video() - You'll have to update your template files if you use the template tag
Fatal error: Call to undefined method WP_Error::get_item_quantity() bug fixed
Shortcode bug fix
Code clean up and other bug fixes
Passed out 2.8 support

= 2.1 =
Plugin completely rewriten
Uses the iframe embed by default (supports both HTML5 and Flash). Added option to use the old
Requires at least 3.1
Now removes the plugin options when you uninstall the plugin
If using the template tag, make sure to have <code>echo</code> in front of it.
A lot of bug fixes

= 2.2.5 =
Added `?wmode=transparent` to the iframe

= 3.0 =
A lot of bug fixes. The videos as embedded and cached using Oembed. Youtube API version 2.0.