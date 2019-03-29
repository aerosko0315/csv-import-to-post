<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.smashstack.com/
 * @since             1.0.1
 * @package           Smashstack_Csv_Importer
 *
 * @wordpress-plugin
 * Plugin Name:       Smashstack CSV Importer
 * Plugin URI:        https://www.smashstack.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.2.0
 * Author:            smashstack-aeros
 * Author URI:        https://www.smashstack.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smashstack-csv-importer
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
define( 'SMASHSTACK_CSV_IMPORTER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-smashstack-csv-importer-activator.php
 */
function activate_smashstack_csv_importer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smashstack-csv-importer-activator.php';
	Smashstack_Csv_Importer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-smashstack-csv-importer-deactivator.php
 */
function deactivate_smashstack_csv_importer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smashstack-csv-importer-deactivator.php';
	Smashstack_Csv_Importer_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_smashstack_csv_importer' );
register_deactivation_hook( __FILE__, 'deactivate_smashstack_csv_importer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-smashstack-csv-importer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_smashstack_csv_importer() {

	$plugin = new Smashstack_Csv_Importer();
	$plugin->run();

}
run_smashstack_csv_importer();
