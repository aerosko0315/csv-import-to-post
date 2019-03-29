<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.smashstack.com/
 * @since      1.0.0
 *
 * @package    Smashstack_Csv_Importer
 * @subpackage Smashstack_Csv_Importer/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Smashstack_Csv_Importer
 * @subpackage Smashstack_Csv_Importer/includes
 * @author     smashstack-aeros <aeros.andrews@smashstack.com>
 */
class Smashstack_Csv_Importer_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'smashstack-csv-importer',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
