<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://lazycoders.co
 * @since             1.0.0
 * @package           Lazytasks_Premium
 *
 * @wordpress-plugin
 * Plugin Name:       Lazytasks Premium Mobile App
 * Plugin URI:        https://lazycoders.co/lazytasks
 * Description:       Pro Addon for LazyTasks FREE Plugin. Unleash the full potential of LazyTasks iOS and Android mobile app. Get things done on the go… Happy Tasking!
 * Version:           1.0.41
 * Author:            Lazycoders
 * Author URI:        https://lazycoders.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lazytasks-premium
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LAZYTASKS_PREMIUM_VERSION', '1.0.41' );

define('LAZYTASKS_PREMIUM_DB_VERSION', '1.0.0');

global $wpdb;
define( 'LAZYTASK_PREMIUM_TABLE_PREFIX', $wpdb->prefix .'pms_premium_' );

define('LAZYTASK_PREMIUM_APP_BUILDER_RESOURCE_URL', 'https://live.appza.net');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lazytasks-premium-activator.php
 */
function lazytask_activate_lazytasks_premium() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lazytasks-premium-activator.php';
	if ( ! wp_next_scheduled ( 'lazytasks_premium_daily_event' ) ) {
		wp_schedule_event( time(), 'daily', 'lazytasks_premium_daily_event' );
	}
	Lazytask_Lazytasks_Premium_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lazytasks-premium-deactivator.php
 */
function lazytask_deactivate_lazytasks_premium() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lazytasks-premium-deactivator.php';
	wp_clear_scheduled_hook( 'lazytasks_premium_daily_event' );
	Lazytask_Lazytasks_Premium_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'lazytask_activate_lazytasks_premium' );
register_deactivation_hook( __FILE__, 'lazytask_deactivate_lazytasks_premium' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-lazytasks-premium.php';


add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'lazytask_action_links');

function lazytask_action_links($links) {

	$settings_link = '<a href="' . admin_url('admin.php?page=lazytasks-page#/settings') . '">Settings</a>';

	array_push($links, $settings_link);

	return $links;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lazytasks_premium() {

	$plugin = new Lazytask_Lazytasks_Premium();
	$plugin->run();

}
run_lazytasks_premium();

require_once "vendor/autoload.php";

$url = LAZYTASK_PREMIUM_APP_BUILDER_RESOURCE_URL . "/api/appza/v1/plugin/version-check";
$plugin_update_check = PucFactory::buildUpdateChecker(
	$url,
	__FILE__, //Full path to the main plugin file or functions.php.
	'lazytasks-premium'
);

$hash = get_option('lazytask_hash', '');
$plugin_update_check->addHttpRequestArgFilter(function ($args) use ($hash) {
	$args['headers']['Lazy-Task-Hash'] =  $hash;
	error_log('Headers:'.print_r($args, true));

	return $args;
});

$plugin_update_check->addQueryArgFilter(function ($queryArgs) {
	$queryArgs['plugin_slug'] = 'lazytasks-premium';
	error_log('Query:'. print_r($queryArgs, true));

	return $queryArgs;
});

