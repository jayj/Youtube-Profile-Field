<?php
/**
 * Adds the options page.
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

add_action( 'admin_menu', 'ypf_add_options_page' );

/**
 * Displays the options page.
 *
 * @since 2.0.0
 */
function ypf_plugin_options() { ?>

	<div class="wrap">

		<h1><?php _e( 'Youtube Profile Field Options', 'youtube-profile-field' ); ?></h1>

		<form action="options.php" method="post">
			<?php settings_fields( 'ypf_options' ); ?>
			<?php do_settings_sections( 'ypf_options' ); ?>
			<?php submit_button(); ?>
		</form>

	</div>

	<?php
}

/**
 * Registers the settings for the options page.
 *
 * @since 2.0.0
 */
function ypf_register_settings_fields() {

	register_setting( 'ypf_options', 'ypf_options', 'ypf_validate_options' );

	add_settings_section(
		'ypf_options_main',
		__( 'Settings', 'youtube-profile-field' ),
		'ypf_section_text',
		'ypf_options'
	);

	add_settings_field(
		'ypf_count',
		__( 'Default video count', 'youtube-profile-field' ),
		'ypf_setting_input_number',
		'ypf_options',
		'ypf_options_main'
	);

	// @todo Consider to add an option to disable using the native WordPress video style.

	add_settings_field(
		'ypf_heading_start',
		__( 'Before video title', 'youtube-profile-field' ),
		'ypf_setting_input_heading_start',
		'ypf_options',
		'ypf_options_main'
	);

	add_settings_field(
		'ypf_heading_end',
		__( 'After video title', 'youtube-profile-field' ),
		'ypf_setting_input_heading_end',
		'ypf_options',
		'ypf_options_main'
	);
}

add_action( 'admin_init', 'ypf_register_settings_fields' );

/**
 * Settings section.
 *
 * @since 2.0.0
 */
function ypf_section_text() {
	$options     = get_option( 'ypf_options' );

	// Add width and height as hidden fields
	$width  = ( isset( $options['width'] ) )  ? $options['width']  : 0;
	$height = ( isset( $options['height'] ) ) ? $options['height'] : 0;

	echo "<input type='hidden' name='ypf_options[width]'  value='{$width}' /> ";
	echo "<input type='hidden' name='ypf_options[height]' value='{$height}' /> ";
}

/**
 * "Default video count" text field.
 *
 * @since 2.0.0
 */
function ypf_setting_input_number() {
	$options = get_option( 'ypf_options' );
	$value   = $options['count'];

	echo "<input type='number' id='ypf_count' name='ypf_options[count]' class='small-text' min='1' value='{$value}' /> ";

	_e( 'Number of videos you want shown (can be overridden in the shortcode or template tag)', 'youtube-profile-field' );
}

/**
 * "Before video title" field.
 *
 * @since 2.0.0
 */
function ypf_setting_input_heading_start() {
	$options = get_option( 'ypf_options' );
	$value   = $options['headingStart'];

	echo "<input type='text' id='ypf_setting_input_heading_start' name='ypf_options[headingStart]' value='{$value}' /> ";

	esc_html_e( 'Text or HTML to show before the video title. Leave empty to hide the title.', 'youtube-profile-field' );
}

/**
 * "After video title" field.
 *
 * @since 2.0.0
 */
function ypf_setting_input_heading_end() {
	$options = get_option( 'ypf_options' );
	$value   = $options['headingEnd'];

	echo "<input type='text' id='ypf_setting_input_heading_end' name='ypf_options[headingEnd]' value='{$value}' /> ";

	esc_html_e( 'Text or HTML to show after the video title.' );
}

/**
 * Validates the user input
 *
 * @since  2.0.0
 *
 * @param  array $input  The raw, untrusted input.
 * @return array         The validated input.
 */
function ypf_validate_options( $input ) {

	// Make sure the numbers are integers.
	$input['count']  = intval( $input['count'] );
	$input['width']  = intval( $input['width'] );
	$input['height'] = intval( $input['height'] );

	// Sanitize inputs for untrusted HTML.
	$input['headingStart'] = esc_html( wp_kses_post( $input['headingStart'] ) );
	$input['headingEnd']   = esc_html( wp_kses_post( $input['headingEnd'] ) );

	return $input;
}

?>
