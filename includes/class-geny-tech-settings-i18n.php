<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.linkedin.com/in/eugine-gomez-9b1b952b7
 * @since      1.0.0
 *
 * @package    Geny_Tech_Settings
 * @subpackage Geny_Tech_Settings/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Geny_Tech_Settings
 * @subpackage Geny_Tech_Settings/includes
 * @author     Eugine Gomez <engine.gomz@gmail.com>
 */
class Geny_Tech_Settings_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'geny-tech-settings',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
