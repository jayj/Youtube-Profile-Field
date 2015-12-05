# Youtube Profile Field #
**Contributors:** Jayjdk  
**Donate link:** https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XHKUSU26XBKEU  
**Tags:** youtube, video, rss, feed, profile, field, username, user, shortcode, oembed  
**Requires at least:** 3.6  
**Tested up to:** 4.1-alpha  
**Stable tag:** 3.1.1  
**License:** GPLv2 or later  

Automatically display your latest Youtube videos on your site. Comes with the shortcode [youtube-user] to be used in posts and pages.

## Description ##

Youtube Profile Field allows you to automatically show your latest Youtube videos on your blog. Just enter your Youtube username in the profile and you're ready to show your videos to the world!

Show your videos in your posts, pages, and text widgets using a shortcode, or in your theme files using the template tag.

So, how is this different from the Youtube oEmbed feature that's already in WordPress?

The built-in feature is awesome if you want to show a specific video. But what if you always want to show your latest video?  Or 3 latest videos? You could manually update them each you upload a new video, or you could use this plugin to take care of it for you.

## Installation ##

1. Install the plugin either via the WordPress.org plugin directory, or by uploading the files to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to `Users > Your Profile` and enter your Youtube username (or channel ID) in the field called "Youtube username" under "Contact Info"
4. **Optional:** Go to the settings page `Settings > Youtube Profile Field` and change the settings to fit you.  


If you need help finding your channel ID, [here's an article from the Youtube help section](https://support.google.com/youtube/answer/3250431?hl=en)

## Frequently Asked Questions ##

### So, how is this different from the Youtube oEmbed feature that's already in WordPress?

The built-in feature is awesome if you want to show a specific video. But what if you always want to show your latest video?  Or 3 latest videos? You could manually update them each you upload a new video, or you could use this plugin to take care of it for you.


### How do I use the shortcode? ###

**The shortcode is:** `[youtube-user]`  

You can use these parameters:

* `count`    - The number of videos you want to show. You can use 'all' to show all that the Youtube feed contains. Defaults to the plugin setting. Ex:  `[youtube-user count="2"]`  
* `user`     - The ID of the user you want to show videos from. Default is the post author. Ex: `[youtube-user user="3"]`  
* `width`    - If you want a different width than the default

### How do I use the template tag? ###

You can use one of the following templates tags:

```php
// Return the videos
if ( function_exists( 'ypf_get_video' ) ) {
	echo ypf_get_video();
}

// Echo the videos
if ( function_exists( 'ypf_show_video' ) ) {
	ypf_show_video();
}
```

Both accepts the same array of parameters.

You can use these parameters:

* `user_id`       - The ID of the user you want to show videos from. Required outside the loop.
* `count`         - The number of videos you want to show. You can use 'all' to show all that the Youtube feed contains. Defaults to the plugin setting.
* `width`         - If you want a different width than the default.
* `headingStart`  - Text or HTML before the video title. Defaults to the plugin setting.
* `headingEnd`    - Text or HTML after the video title. Defaults to the plugin setting.

Example:

```php
if ( function_exists( 'ypf_get_video' ) ) {
	echo ypf_get_video( array( 'count' => 2, 'user_id' => 2 ) ); // 2 videos from user ID 2
}
```

### Can I use the native WordPress HTML5 player? =

Yes, you can use the native WordPress video player added in WordPress 3.9 to play your Youtube videos.

The option has been disabled because of issues playing the videos in Safari and Firefox. But if you still want to, you can use this line in a plugin or theme:

```php
add_filter( 'youtube_profile_field_use_native_player', '__return_true' );
```

## Screenshots ##

###1. A page with videos###

![A page with videos](http://s.wordpress.org/extend/plugins/youtube-profile-field/screenshot-1.png)

###2. The profile page with the Youtube username field###

![The profile page with the Youtube username field](http://s.wordpress.org/extend/plugins/youtube-profile-field/screenshot-2.jpg)


## Changelog ##

### 3.1.1 ###

* Bugfix: Fix broken video player in Firefox and Safari

The native WordPress video player is broken for some Firefox and Safari users.
This version uses oEmbed until [#29223](https://core.trac.wordpress.org/ticket/29223) is fixed.

### 3.1 ###
* Update to Youtube API v3 (the v2 API will stop working on April 20, 2015)
* For WordPress 3.9 and later, use the native WordPress video player
* Channel ID can be used as username
* Fix minor bug with unsafe HTML not properly being removed in the plugin settings
* Width and height settings removed. Will default to the default embed width. For old users, their old settings will still work
* Major speed and stability improvements
* Minor bug fixes
* Tested with WordPress 3.9

### 3.0.3 ###
* Use 4:3 aspect ratio for videos (same as WordPress)
* Added Serbo-Croatian translation by [Borisa Djuraskovic](http://www.webhostinghub.com/).
* Bugfix: Correctly load translations
* Tested with WordPress 3.7

### 3.0.2 ###
* Bug fixes

### 3.0 ###
* The videos are now embedded using oEmbed which means that WordPress can cache them
* Many bug fixes - mostly bugs where parameters didn't work as indended
* Code improvements
* Youtube API version 2.0
* Tested with WordPress 3.5

[...]

### 1.0 ###
* Initial Release


## Upgrade Notice ##

### 2.0 ###
2.9 support and a plugin options panel.

### 2.0.1 ###
Support for full screen

### 2.0.2 ###
show_video() has been renamed to ypf_show_video() - You'll have to update your template files if you use the template tag
**Fatal error:** Call to undefined method WP_Error::get_item_quantity() bug fixed  
Shortcode bug fix
Code clean up and other bug fixes
Passed out 2.8 support

### 2.1 ###
Plugin completely rewriten
Uses the iframe embed by default (supports both HTML5 and Flash). Added option to use the old
Requires at least 3.1
Now removes the plugin options when you uninstall the plugin
If using the template tag, make sure to have `echo` in front of it.
A lot of bug fixes

### 2.2.5 ###
Added `?wmode=transparent` to the iframe

### 3.0 ###
A lot of bug fixes. The videos as embedded and cached using Oembed. Youtube API version 2.0.

### 3.1 ###
Major speed and stability improvements. Uses the native WordPress video player.
