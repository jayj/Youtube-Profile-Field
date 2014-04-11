<?php
/**
 * Refreshes the Youtube information in the user meta, and returns the new playlist ID.
 *
 * @since  3.1.0
 *
 * @param  int $user_id      The user ID.
 * @param  string $username  The requested Youtube username.
 * @return string            The new playlist ID.
 */
function ypf_get_playlist_id( $user_id, $username ) {

	// Get channel ID from Youtube.
	$channel_id = ypf_get_channel_id( $username );

	// Get playlist ID from Youtube.
	$playlist_id = ypf_get_channel_upload_playlist( $channel_id );

	// Update user meta with the new information.
	update_user_meta( $user_id, 'ypf_channel_id', $channel_id );
	update_user_meta( $user_id, 'ypf_upload_playlist_id', $playlist_id );
	update_user_meta( $user_id, 'ypf_previous_username', $username );

	return $playlist_id;
}

/**
 * Gets the Youtube channel ID based on the username.
 *
 * @since  3.1.0
 *
 * @param  string $username  The requested Youtube username.
 * @return string|bool       The channel ID on success. False on error.
 */
function ypf_get_channel_id( $username ) {

	$api_key = Youtube_Profile_Field::$api_key;

	// Build the query.
	$query_args = array(
		'forUsername' => $username,
		'part'        => 'id',
		'key'         => $api_key
	);

	// Request to the Youtube API.
	$url = 'https://www.googleapis.com/youtube/v3/channels?' . http_build_query( $query_args );
	$response = wp_remote_get( $url, array( 'sslverify' => false ) );

	if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	// Get the data.
	$data = json_decode( wp_remote_retrieve_body( $response ) );

	// No channel ID found.
	if ( empty( $data->items[0] ) ) {
		return false;
	}

	return $data->items[0]->id;
}

/**
 * Gets the Youtube channel upload playlist ID, based on the channel ID.
 *
 * @since  3.1.0
 *
 * @param  string $channel_id  The requested Youtube channel ID.
 * @return string|bool         The playlist ID on success. False on error.
 */
function ypf_get_channel_upload_playlist( $channel_id ) {

	$api_key = Youtube_Profile_Field::$api_key;

	// Build the query.
	$query_args = array(
		'part' => 'contentDetails',
		'id'   => $channel_id,
		'key'  => $api_key
	);

	// Request to the Youtube API.
	$url = 'https://www.googleapis.com/youtube/v3/channels?' . http_build_query( $query_args );

	$response = wp_remote_get( $url, array( 'sslverify' => false ) );

	if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	// Get the data.
	$data = json_decode( wp_remote_retrieve_body( $response ) );

	// No playlist ID found.
	if ( empty( $data->items[0]->contentDetails->relatedPlaylists->uploads ) ) {
		return false;
	}

	return $data->items[0]->contentDetails->relatedPlaylists->uploads;
}

/**
 * Gets the channel videos from the Youtube API.
 *
 * @since  3.1.0
 *
 * @param  string $playlist_id  The channel upload playlist ID.
 * @param  int $count           Number of videos to get.
 * @return object|bool          Object of videos on success. False on error.
 */
function ypf_get_channel_videos( $playlist_id, $count ) {

	$api_key = Youtube_Profile_Field::$api_key;

	// Build the query.
	$query_args = array(
		'part'       => 'snippet, contentDetails',
		'playlistId' => $playlist_id,
		'key'        => $api_key
	);

	if ( 'all' != $count ) {
		$query_args['maxResults'] = $count;
	}

	// Request to the Youtube API.
	$url = 'https://www.googleapis.com/youtube/v3/playlistItems?' . http_build_query( $query_args );

	$response = wp_remote_get( $url, array( 'sslverify' => false ) );

	if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	// Get the data.
	$data = json_decode( wp_remote_retrieve_body( $response ) );

	// No videos found.
	if ( empty( $data->items ) ) {
		return false;
	}

	return $data->items;
}

?>
