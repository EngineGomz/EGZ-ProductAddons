<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.linkedin.com/in/eugine-gomez-9b1b952b7
 * @since             1.0.0
 * @package           Geny_Tech_Settings
 *
 * @wordpress-plugin
 * Plugin Name:       GenyTech Settings
 * Plugin URI:        https://genyflex.com
 * Description:       GenyTech Settings
 * Version:           1.0.0
 * Author:            Eugine Gomez
 * Author URI:        https://www.linkedin.com/in/eugine-gomez-9b1b952b7/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       geny-tech-settings
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GENY_TECH_SETTINGS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-geny-tech-settings-activator.php
 */
function activate_geny_tech_settings() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-geny-tech-settings-activator.php';
	Geny_Tech_Settings_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-geny-tech-settings-deactivator.php
 */
function deactivate_geny_tech_settings() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-geny-tech-settings-deactivator.php';
	Geny_Tech_Settings_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_geny_tech_settings' );
register_deactivation_hook( __FILE__, 'deactivate_geny_tech_settings' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-geny-tech-settings.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_geny_tech_settings() {

	$plugin = new Geny_Tech_Settings();
	$plugin->run();

}
run_geny_tech_settings();
