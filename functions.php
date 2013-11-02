<?php
/**
 * Show the video
 *
 * @since 2.0.2
 * @uses ypf_get_video() Get the requested videos
 * @param array $args
 */
function ypf_show_video( $args = array() ) {

	/* Backward compatibility for earlier plugin versions than 2.2.0 */
	if ( ! is_array( $args ) ) {
		$args = array();
		$numargs = func_num_args();

		$args['count'] = func_get_arg(0);

		if ( $numargs >= 2 )
			$args['user_id'] = func_get_arg(1);
	}

	/* Get default options. */
	$defaults = get_option( 'ypf_options' );
	$defaults['user_id'] = get_the_author_meta( 'ID' );

	/* Merge the parameters and default options. */
	$options = wp_parse_args( $args, $defaults );

	return ypf_get_video( $options );
}

/**
 * Get the requested video
 *
 * @since 2.1.0
 * @param array	$options
 */
function ypf_get_video( $options ) {

	/* Get width and height. */
	$options['width']  = ypf_get_video_width( $options );
	$options['height'] = ypf_get_video_height( $options );

	/* Get the username. */
	$username = get_the_author_meta( 'youtube', $options['user_id'] );

	/*
	 * Get the videos from the Youtube user feed
	 */
	include_once( ABSPATH . WPINC . '/feed.php' );

	$rss = fetch_feed( "http://gdata.youtube.com/feeds/base/users/{$username}/uploads?v=2" );

	/* Is count set to 0? (why would some one do that??) */
	if ( 0 === $options['count'] ) {
		return;
	}

	/* Is count set to all? */
	if ( 'all' == $options['count'] ) {
		$options['count'] = '0'; // 0 will return all items in the RSS feed
	}

	/* Checks that the object is created correctly. */
	if ( ! is_wp_error( $rss ) ) {
		$maxitems  = $rss->get_item_quantity( intval( $options['count'] ) );
		$rss_items = $rss->get_items( 0, $maxitems );
	}

	if ( isset( $maxitems ) && $maxitems != 0 ) :

		global $wp_embed;

		ob_start();

		/* Loop through each found video. */
		foreach ( $rss_items as $item ) :

			/* Get video info. */
			$url   = $item->get_permalink();
			$title = $item->get_title();

			/* Get video ID. */
			if ( preg_match( '/(?<=video:).+/', $item->get_id(), $match ) ) {
				$id = $match[0];
			} else {
				return __( 'Error: No video ID found.', 'youtube-profile-field' );
			}
	?>

			<div class="youtube-video">
				<?php
					/* Get the video title. */
					if ( ! empty( $options['headingStart'] ) ) {
						printf( '%s <a href="%s">%s</a> %s',
							html_entity_decode( $options['headingStart'] ),
							esc_url( $url ),
							$title,
							( ! empty( $options['headingEnd'] ) ) ? html_entity_decode( $options['headingEnd'] ) : ''
						);
					}
				?>

				<?php
					/* Embed the video through oEmbed. */
					echo $wp_embed->shortcode( array(
						'width'  => $options['width'],
						'height' => $options['height']
					), 'http://www.youtube.com/watch?v=' . $id );
				?>

			</div> <?php

		endforeach;

		return ob_get_clean();

	endif; // $maxitems

	return;
}

/**
 * Get video width. Either from the options, the default embed width
 * or the theme content width
 *
 * @since 3.0.0
 * @param array $options Array with the plugin options
 * @return int The width
 */
function ypf_get_video_width( $options ) {

	/* No width, get the default. */
	if ( ! $options['width'] ) {
		$options['width'] = ( get_option( 'embed_size_w' ) ) ? get_option( 'embed_size_w' ) : (int) $GLOBALS['content_width'];
	}

	return $options['width'];
}

/**
 * Get video height. Either from the options, or based on the width
 * with the 16:9 aspect ratio
 *
 * @since 3.0.0
 * @uses apply_filters() Filter the default aspect ratio with the `youtube_profile_field_aspect_ratio` filter
 * @param array $options Array with the database options
 * @return int The height
 */
function ypf_get_video_height( $options ) {

	/* No height, generate it from the width. */
	if ( ! $options['height'] ) {
		$options['height'] = round( $options['width'] / ( apply_filters( 'youtube_profile_field_aspect_ratio', 4/3 ) ) ); // 4:3 aspect ratio
	}

	return $options['height'];
}

/**
 * The [youtube-user] shortcode
 *
 * Parameter you can use: count/display, user/user_id, ID, width, height, headingStart, headingEnd
 *
 * @since 1.0.0
 */
function ypf_youtube_user_shortcode( $args ) {

	/* Get default options. */
	$defaults = get_option( 'ypf_options' );

	/* Get shortcode attributes. */
	$args = shortcode_atts(
		array(
			'display' => $defaults['count'],
			'user'    => get_the_author_meta( 'ID' ),
			'id'      => '', // If you just want to display a video, there's no really need to use this shortcode as WordPress now supports oEmbed
			'count'   => '',
			'user_id' => '',
			'width'   => '',
			'height'  => ''
		),
		$args
	);

	/* Rename display to count. */
	if ( empty( $args['count'] ) ) {
		$args['count'] = $args['display'];
	}

	/* Rename user to user_id. */
	if ( empty( $args['user_id'] ) ) {
		$args['user_id'] = $args['user'];
	}

	unset( $args['display'], $args['user'] );

	/* Merge the parameters and default options */
	$options = wp_parse_args( $args, $defaults );

	/* There's an ID! Let's grab that single video. */
	if ( ! empty( $args['id'] ) ) :

		/* Get width and height. */
		$options['width']  = ypf_get_video_width( $options );
		$options['height'] = ypf_get_video_height( $options );

		ob_start(); ?>

		<div class="youtube-video">
			<?php
				global $wp_embed;

				/* Embed the video through oEmbed */
				echo $wp_embed->shortcode( array(
					'width'  => $options['width'],
					'height' => $options['height']
				), 'http://www.youtube.com/watch?v=' . $args['id'] );
			?>
		</div>

	<?php
		return ob_get_clean();
	endif;

	/*
	 * Grab videos from the user feed.
	 */
	return ypf_get_video( $options );
}

add_shortcode( 'youtube-user', 'ypf_youtube_user_shortcode' );

/**
 * Create the Youtube Username profile field
 *
 * @since 1.0.0
 */
function ypf_youtube_contact_field( $contactmethods ) {
	$contactmethods['youtube'] = __( 'Youtube username', 'youtube-profile-field' );

	return $contactmethods;
}

add_filter( 'user_contactmethods', 'ypf_youtube_contact_field' );

/**
 * Add a link to the settings
 *
 * @since 1.0.0
 */
function ypf_plugin_action_links( $links, $file ) {
	static $this_plugin;

	if ( ! $this_plugin ) {
		$this_plugin = plugin_basename( __FILE__ );
	}

	if ( $file == $this_plugin ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=youtube-profile-field' ) . '">' . __( 'Settings', 'youtube-profile-field' ) . '</a>';
		array_unshift( $links, $settings_link );
	}

	return $links;
}

add_filter( 'plugin_action_links', 'ypf_plugin_action_links', 10, 2 );

/**
 * Earlier versions of the plugin used the function 'show_video' - Let's not break any thing
 */
if ( ! function_exists( 'show_video' ) ) {
	function show_video( $count = '', $userid ) {
		ypf_show_video( array( 'count' => $count, $user_id = $userid ) );
	}
}
