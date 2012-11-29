<?php

add_action( 'admin_menu', 'ypf_add_options_page' );

/**
 * Add the options page
 *
 * @since 2.0.0
 */
function ypf_add_options_page() {
	add_options_page(
		__( 'Youtube Profile Field Options', 'youtube-profile-field' ),
		__( 'Youtube Profile Field', 'youtube-profile-field' ),
		'manage_options',
		'youtube-profile-field',
		'ypf_plugin_options'
	);
}

/**
 * Display the options page.
 *
 * @since 2.0.0
 */
function ypf_plugin_options() { ?>

	<div class="wrap">

		<?php screen_icon(); ?>
		<h2><?php _e( 'Youtube Profile Field Options', 'youtube-profile-field' ); ?></h2>

		<form action="options.php" method="post">
			<?php settings_fields( 'ypf_options' ); ?>
			<?php do_settings_sections( 'ypf_options' ); ?>
			<?php submit_button(); ?>
		</form>

	</div>

	<?php
}

/**
 * Register the settings for the options page.
 *
 * @since 2.0.0
 */
function ypf_admin_init() {

	register_setting( 'ypf_options', 'ypf_options', 'ypf_validate_options' );

	add_settings_section(
		'ypf_options_main',
		__( 'Settings', 'youtube-profile-field' ),
		'ypf_section_text',
		'ypf_options'
	);

	add_settings_field(
		'ypf_count',
		__( 'Videos to show', 'youtube-profile-field' ),
		'ypf_setting_input_number',
		'ypf_options',
		'ypf_options_main'
	);

	add_settings_field(
		'ypf_heading_start',
		__( 'Heading to titles', 'youtube-profile-field' ),
		'ypf_setting_input_heading_start',
		'ypf_options',
		'ypf_options_main'
	);

	add_settings_field(
		'ypf_heading_end',
		__( 'End heading', 'youtube-profile-field' ),
		'ypf_setting_input_heading_end',
		'ypf_options',
		'ypf_options_main'
	);

	add_settings_field(
		'ypf_width',
		__( 'Width', 'youtube-profile-field' ),
		'ypf_setting_input_width',
		'ypf_options',
		'ypf_options_main'
	);

	add_settings_field(
		'ypf_height',
		__( 'Height', 'youtube-profile-field' ),
		'ypf_setting_input_height',
		'ypf_options',
		'ypf_options_main'
	);

	add_settings_section(
		'ypf_options_help',
		__( 'Help', 'youtube-profile-field' ),
		'ypf_section_help',
		'ypf_options'
	);
}

add_action( 'admin_init', 'ypf_admin_init' );

/**
 * Settings section
 *
 * @since 2.0.0
 */
function ypf_section_text() {
	return;
}

/**
 * "Videos to show" text field
 *
 * @since 2.0.0
 */
function ypf_setting_input_number() {
	$options     = get_option( 'ypf_options' );
	$text_string = $options['count'];

	echo "<input type='number' id='ypf_count' name='ypf_options[count]' class='small-text' min='1' value='$text_string' /> ";
	_e( 'Number of videos you want to show (Can be overriden with a template tag or shortcode)', 'youtube-profile-field' );
}

/**
 * "Heading start" field
 *
 * @since 2.0.0
 */
function ypf_setting_input_heading_start() {
	$options     = get_option( 'ypf_options' );
	$text_string = $options['headingStart'];

	echo "<input type='text' id='ypf_setting_input_heading_start' name='ypf_options[headingStart]' value='$text_string' /> ";
	echo esc_html_e( '<h3>, <p> or something else. Leave empty to hide titles', 'youtube-profile-field' );
}

/**
 * "Heading end" field
 *
 * @since 2.0.0
 */
function ypf_setting_input_heading_end() {
	$options     = get_option( 'ypf_options' );
	$text_string = $options['headingEnd'];

	echo "<input type='text' id='ypf_setting_input_heading_end' name='ypf_options[headingEnd]' value='$text_string' /> ";
	echo esc_html_e( '</h3>, </p>' );
}

/**
 * "Width" field
 *
 * @since 2.0.0
 */
function ypf_setting_input_width() {
	$options     = get_option( 'ypf_options' );
	$text_string = ( isset( $options['width'] ) ) ? $options['width'] : 0;

	echo "<input type='number' id='ypf_width' name='ypf_options[width]' class='small-text' value='$text_string' /> ";
	_e( 'If left blank, or 0, the width of the videos will default to your media settings.', 'youtube-profile-field' );
}

/**
 * "Height" field
 *
 * @since 2.0.0
 */
function ypf_setting_input_height() {
	$options     = get_option( 'ypf_options' );
	$text_string = ( isset( $options['height'] ) ) ? $options['height'] : 0;

	echo "<input type='number' id='ypf_height' name='ypf_options[height]' class='small-text' value='$text_string' /> ";
	_e( 'If left blank, or 0, the height of the videos will be calculated using the width', 'youtube-profile-field' );
}

/**
 * Validate user input
 *
 * @since 2.0.0
 * @param array $input The raw, untrusted input
 * @return array The validated input
 */
function ypf_validate_options( $input ) {
	$input['count']  = intval( $input['count'] );
	$input['width']  = intval( $input['width'] );
	$input['height'] = intval( $input['height'] );

	$input['headingStart'] = wp_kses_data( esc_html( $input['headingStart'] ) );
	$input['headingEnd'] = wp_kses_data( esc_html( $input['headingEnd'] ) );

	return $input;
}

/**
 * Help section
 *
 * @since 2.0.0
 */
function ypf_section_help() {

	echo '<h4>' . __( 'Helpful video sizes:', 'youtube-profile-field' ) . '</h4>';

    echo '<ul style="list-style: inside square;">
			<li>480x385</li>
			<li>560x315</li>
			<li>640x360</li>
			<li>853x480</li>
		</ul>';
}

?>
