<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://https://lazycoders.co
 * @since      1.0.0
 *
 * @package    Lazytasks_Premium
 * @subpackage Lazytasks_Premium/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Lazytasks_Premium
 * @subpackage Lazytasks_Premium/includes
 * @author     Lazycoders <info@lazycoders.co>
 */
class Lazytasks_Premium_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'lazytasks-premium',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
