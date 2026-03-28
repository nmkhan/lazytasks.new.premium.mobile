<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://https://lazycoders.co
 * @since      1.0.0
 *
 * @package    Lazytasks_Premium
 * @subpackage Lazytasks_Premium/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Lazytasks_Premium
 * @subpackage Lazytasks_Premium/includes
 * @author     Lazycoders <info@lazycoders.co>
 */
class Lazytask_Lazytasks_Premium_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		delete_option('lazytask_premium_installed');

	}

}
