<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://https://lazycoders.co
 * @since      1.0.0
 *
 * @package    Lazytasks_Premium
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$siteUrl     = get_site_url();
$license_key = get_option('lazytask_license_key', null);

$params = array(
	'site_url'     => $siteUrl,
	'license_key'  => $license_key,
	'product'      => 'lazy_task',
	'appza_action' => 'plugin_delete',
);

//$url = "https://dev-app.appza.net/api/appza/v1/license/deactivate";
$url = "https://live.appza.net/api/appza/v1/license/deactivate";

$args = array(
	'method'  => 'GET',
	'headers' => array(
		'Lazy-Task-Hash' => get_option('lazytask_hash') ?? '',
		'Accept'           => 'application/json',
		'Content-Type'     => 'application/json',
		'Access-Control-Allow-Origin' => '*',
	),
	'body' => $params,
);

// Fire API
$response = wp_remote_request( $url, $args );

if ( ! is_wp_error( $response ) ) {
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	delete_option( 'lazytask_premium_installed' );
	delete_option( 'lazytask_hash' );
	delete_option( 'lazytask_license_key' );
	delete_option( 'lazytask_license_expire_date' );
	delete_option( 'lazytask_license_activate' );
	delete_option( 'lazytask_license_response' );
	delete_option('external_updates-lazytasks-premium');
}

wp_clear_scheduled_hook( 'lazytasks_premium_daily_event' );





