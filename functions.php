<?php
/**
 * Gets the requested Youtube videos.
 *
 * @since 2.1.0
 *
 * @param array $args {
 *     An array of arguments. Optional.
 *
 *     @type int $user_id          The user ID. Required outside the loop.
 *     @type int $count            Videos to be shown. Optional. Will default to
 *                                 the count selected in the settings.
 *     @type int $width            Video width. Optional.
 *     @type int $height           Video height. Optional.
 *     @type string $headingStart  Text or HTML before the heading. Optional.
 *                                 Will default to the text selected in the settings.
 *     @type string $headingEnd    Text or HTML after the heading. Optional.
 *                                 Will default to the text selected in the settings.
 * }
 */
function ypf_get_video( $args = array() ) {

	// Get plugin settings.
	$defaults = get_option( 'ypf_options' );

	// Merge them with the arguments.
	$options = wp_parse_args( $args, $defaults );

	// Get default width
	if ( empty( $options['width'] ) || 0 == $options['width'] ) {
		$options['width'] = ypf_get_video_width();
	}

	// Get default height. Not needed for the [video] shortcode but it still is for oEmbed
	if ( empty( $options['height'] ) || 0 == $options['height'] ) {
		$options['height'] = ypf_get_video_height( $options['width'] );
	}

	// Get user ID.
	if ( isset( $options['user_id'] ) ) {
		$user_id = $options['user_id'];
	} else {
		$user_id = get_the_author_meta( 'ID' );
	}

	// Get the Youtube channel information.
	$username     = get_user_meta( $user_id, 'youtube', true );
	$old_username = get_user_meta( $user_id, 'ypf_previous_username', true );
	$playlist_id  = get_user_meta( $user_id, 'ypf_upload_playlist_id', true );

	// Refresh playlist ID if the username has changed.
	if ( empty( $playlist_id ) || $username != $old_username ) {
		$playlist_id = ypf_get_playlist_id( $user_id, $username );
	}

	// Generate a unique ID, based on the playlist ID and count.
	$cache_name = md5( $playlist_id . $options['count'] );

	// Try to get a cached version of the videos.
	$videos = get_transient( "ypf_videos_{$cache_name}" );

	// If no cached version exists, get the videos from the Youtube API.
	if ( empty( $videos ) ) {
		$videos = ypf_get_channel_videos( $playlist_id, $options['count'] );
		set_transient( "ypf_videos_{$cache_name}", $videos, DAY_IN_SECONDS );
	}

	// Error, no videos found. Abort.
	if ( ! $videos ) {
		return __( 'No videos found.', 'youtube-profile-field' );
	}

	/*
	 * Videos found.
	 */
	global $wp_embed;

	// Get heading options.
	$headingStart = html_entity_decode( $options['headingStart'] );
	$headingEnd   = html_entity_decode( $options['headingEnd'] );

	/**
	 * Filter the default video wrapper class.
	 *
	 * @since 3.1.1
	 *
	 * @param array $class The default classes.
	 * @param int   $count The total number of videos shown.
	 */
	$class = apply_filters( 'youtube_profile_field_wrapper_class', array( 'youtube-video' ), count( $videos ) );

	ob_start();

	// Loop through each video.
	foreach ( $videos as $video ) :

		// Get video info.
		$title = $video->snippet->title;
		$id    = $video->contentDetails->videoId;
		$url   = "https://www.youtube.com/watch?v={$id}";
	?>
		<div class="<?php echo esc_attr( join( ' ', $class ) ); ?>">
			<?php
				// Display the video title.
				if ( ! empty( $headingStart ) ) {
					printf( '%s <a href="%s">%s</a> %s',
						$headingStart,
						esc_url( $url ),
						$title,
						$headingEnd
					);
				}
			?>

			<?php
				/**
				 * Display the video with the [video] shortcode for 3.9 or later.
				 *
				 * Turned off since v3.1.1 until #29223 is fixed
				 * (Video Shortcode with Youtube source broken in some browsers)
				 * https://core.trac.wordpress.org/ticket/29223
				 */
				if ( function_exists( 'wp_playlist_shortcode' ) && apply_filters( 'youtube_profile_field_use_native_player', false ) ) {

					echo wp_video_shortcode( array(
						'src'    => $url,
						'width'  => $options['width'],
						'height' => $options['height'] // Not used by the [video] shortcode
					));

				// Embed the video through oEmbed.
				} else {
					echo $wp_embed->shortcode( array(
						'width'  => $options['width'],
						'height' => $options['height']
					), $url );
				}
			?>

		</div> <!-- /.youtube-video --> <?php

	endforeach;

	return ob_get_clean();
}

/**
 * Shows the requested Youtube videos.
 *
 * @since 2.0.2
 *
 * @param array $args  Same as ypf_get_video()
 */
function ypf_show_video( $args = array() ) {

	// Backward compatibility for earlier plugin versions than 2.2.0
	if ( ! is_array( $args ) ) {
		$args = array();
		$numargs = func_num_args();

		$args['count'] = func_get_arg(0);

		if ( $numargs >= 2 ) {
			$args['user_id'] = func_get_arg(1);
		}
	}

	echo ypf_get_video( $args );
}

/**
 * The [youtube-user] shortcode.
 *
 * @param array $args {
 *     An array of arguments. Optional.
 *
 *     @type int $user_id          The user ID. Required.
 *     @type int $count            Videos to be shown. Optional. Will default to
 *                                 the count selected in the settings.
 *     @type int $width            Video width. Optional.
 *     @type int $height           Video height. Optional.
 * }
 *
 * @since 1.0.0
 */
function ypf_youtube_user_shortcode( $args ) {

	// Get default options.
	$defaults = get_option( 'ypf_options' );

	// Get shortcode attributes.
	$args = shortcode_atts(
		array(
			'user'    => get_the_author_meta( 'ID' ),
			'count'   => $defaults['count'],
			'width'   => '',
			'height'  => '',
			//'id'      => '',
			'user_id' => null, // The shortcode paramenter is 'user'
			'display' => null // Renamed to count
		),
		$args,
		'youtube_profile_field'
	);

	// Rename 'user' to 'user_id' if 'user_id' is not set.
	if ( ! $args['user_id'] ) {
		$args['user_id'] = $args['user'];
	}

	// Rename 'display' to 'count'.
	if ( $args['display'] ) {
		$args['count'] = $args['display'];
	}

	// Arguments not needed anymore.
	unset( $args['display'], $args['user'] );

	/*
	 * Grab the videos from the user feed.
	 */
	return ypf_get_video( $args );
}

add_shortcode( 'youtube-user', 'ypf_youtube_user_shortcode' );

/**
 * Gets the default embed width, or the theme content width.
 *
 * @since  3.0.0
 *
 * @return int    The width.
 */
function ypf_get_video_width() {
	$embed_width = get_option( 'embed_size_w' );
	$theme_width = (int) $GLOBALS['content_width'];

	return ( $embed_width ) ? $embed_width : $theme_width;
}

/**
 * Gets the video height, based on the width.
 *
 * @since  3.0.0
 *
 * @param  int    $width The video width.
 * @return int           The calculated height.
 */
function ypf_get_video_height( $width ) {
	/**
	 * Filter the default video aspect video.
	 *
	 * @since 3.1.0
	 *
	 * @param string|int $ratio The default 4:3 aspect ratio.
	 */
	$aspect_ratio = apply_filters( 'youtube_profile_field_aspect_ratio', 4/3 );

	return (int) round( $width / $aspect_ratio );
}

/**
 * Creates the Youtube Username profile field.
 *
 * @since 1.0.0
 */
function ypf_youtube_contact_field( $contactmethods ) {
	$contactmethods['youtube'] = __( 'Youtube username', 'youtube-profile-field' );

	return $contactmethods;
}

add_filter( 'user_contactmethods', 'ypf_youtube_contact_field' );

/**
 * Saves the old Youtube username to the user meta.
 *
 * @since  3.1.0
 * @param  int $user_id  The user ID.
 */
function ypf_add_old_youtube_username( $user_id ) {
	if ( current_user_can( 'edit_user', $user_id ) ) {
		add_user_meta( $user_id, 'ypf_previous_username', $_POST['youtube'], true );
	}
}

add_action( 'edit_user_profile_update', 'ypf_add_old_youtube_username' );
add_action( 'personal_options_update', 'ypf_add_old_youtube_username' );

/**
 * Earlier versions of the plugin used the function 'show_video' - Let's not break any thing
 */
if ( ! function_exists( 'show_video' ) ) {
	function show_video( $count, $user_id ) {
		echo ypf_get_video( array( 'count' => $count, 'user_id' => $user_id ) );
	}
}
