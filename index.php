<?php
/**
 * Plugin Name: Youtube Profile Field
 * Plugin URI: http://jayj.dk/plugins/youtube-profile-field/
 * Description: Adds an Youtube username field to the user profile page and allow you to show your videos on your website.
 * Author: Jesper Johansen
 * Author URI: http://jayj.dk
 * Version: 3.0.3
 * License: GPLv2 or later
 * Text Domain: youtube-profile-field
 * Domain Path: /languages
 */

class Youtube_Profile_Field {

	/**
	 * Constructor method.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		/* Set the constants needed by the plugin. */
		add_action( 'plugins_loaded', array( &$this, 'constants' ), 1 );

		/* Internationalize the text strings used. */
		add_action( 'plugins_loaded', array( &$this, 'i18n' ), 2 );

		/* Load the functions files. */
		add_action( 'plugins_loaded', array( &$this, 'includes' ), 3 );

		/* Load the admin files. */
		add_action( 'plugins_loaded', array( &$this, 'admin' ), 4 );

		/* Register activation hook. */
		register_activation_hook( __FILE__, array( &$this, 'activation' ) );

		/* Register activation hook. */
		register_uninstall_hook( __FILE__, 'uninstall' );
	}

	/**
	 * Defines constants used by the plugin
	 *
	 * @since 3.0.0
	 */
	public function constants() {
		define( 'YPF_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
	}

	/**
	 * Loads the initial files needed by the plugin
	 *
	 * @since 3.0.0
	 */
	public function includes() {
		require_once( YPF_DIR . 'functions.php' );
	}

	/**
	 * Loads the translation files
	 *
	 * @since 3.0.0
	 */
	public function i18n() {
		load_plugin_textdomain( 'youtube-profile-field', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Loads the admin functions and files.
	 *
	 * @since 3.0.0
	 */
	public function admin() {
		if ( is_admin() ) {
			require_once( YPF_DIR . 'admin.php' );
		}
	}

	/**
	 * Method that runs only when the plugin is activated
	 *
	 * @since 3.0.0
	 */
	function activation() {
		$default_options = array(
			'count'        => 1,
			'headingStart' => esc_attr( '<h4>' ),
			'headingEnd'   => esc_attr( '</h4>' ),
			'width'        => 0,
			'height'       => 0
		);

		update_option( 'ypf_options', $default_options );
	}

	/**
	 * Method that runs only when the plugin is uninstalled
	 *
	 * @since 3.0.0
	 */
	function uninstall() {
		delete_option( 'ypf_options' );
	}
}

$youtube_profile_field = new Youtube_Profile_Field();

?>
