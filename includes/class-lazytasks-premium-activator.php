<?php

/**
 * Fired during plugin activation
 *
 * @link       https://https://lazycoders.co
 * @since      1.0.0
 *
 * @package    Lazytasks_Premium
 * @subpackage Lazytasks_Premium/includes
 */

use LazytasksPremium\Helper\Lazytask_Helper_QR_Code;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Lazytasks_Premium
 * @subpackage Lazytasks_Premium/includes
 * @author     Lazycoders <info@lazycoders.co>
 */
class Lazytask_Lazytasks_Premium_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if (!defined('LAZYTASK_VERSION')) {
			deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate our plugin
			wp_die('The "Lazytasks Premium Mobile App" requires the "LazyTasks - Project & Task Management with Collaboration, Kanban and Gantt Chart" plugin to be installed and active. <a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">Return to Plugins page</a>' );
		}

		// Lazytask_Helper_QR_Code::lazytask_preview_app_qrcode_generator();

		update_option('lazytask_do_activation_redirect', 1);

		$installed = get_option('lazytask_premium_installed');

		if ($installed) return;

		$response = wp_remote_request(
			LAZYTASK_PREMIUM_APP_BUILDER_RESOURCE_URL."/api/appza/v1/lead/store/lazy_task",
			array(
				'method' => 'POST',
				'headers' => array(
					'Accept' => 'application/json',
					'Content-Type' => 'application/json',
					'Access-Control-Allow-Origin' => '*',
				),
				'body' => wp_json_encode(
					array(
						'domain' => get_site_url(),
						'email' => wp_get_current_user()->user_email ?: '',
						'nickname' => wp_get_current_user()->nickname ?: '',
						'first_name' => wp_get_current_user()->nickname ?: '',
						'last_name' => wp_get_current_user()->nickname ?: '',
						'note' => 'Installed in new domain',
					)
				)
			)
		);

		if ( is_wp_error( $response ) ) {
			return ;
		}
		if (wp_remote_retrieve_response_code($response) !== 200) {
			return;
		}

		$body     = json_decode(wp_remote_retrieve_body( $response ), true);
		if ( !isset($body['data']) ) {
			return;
		}
		if (get_option('lazytask_hash')) {
			update_option('lazytask_hash', $body['data']['lazy_task_hash'] ? $body['data']['lazy_task_hash'] : '');
		}else{
			add_option('lazytask_hash', $body['data']['lazy_task_hash'] ? $body['data']['lazy_task_hash'] : '');
		}

		add_option('lazytask_premium_installed', '1.0.0');

		//only add first activation date. not update on re-activation
		if ( !get_option('lazytask_premium_activated_date') ) {
			add_option('lazytask_premium_activated_date', date('Y-m-d H:i:s'));
		}

		self::lazytasks_firebase_settings();

	}

	public static function lazytasks_firebase_settings() {

		$response = wp_remote_request(
			LAZYTASK_PREMIUM_APP_BUILDER_RESOURCE_URL."/api/appza/v1/firebase/credential/lazy_task",
			array(
				'method' => 'GET',
				'headers' => array(
					'Lazy-Task-Hash' => get_option('lazytask_hash') ?? '',
					'Accept' => 'application/json',
					'Content-Type' => 'application/json',
					'Access-Control-Allow-Origin' => '*',
				)
			)
		);

		//check for error
		if (is_wp_error($response)) {
			error_log('Firebase settings fetch failed: ' . $response->get_error_message());
			return;
		}

		//response code
		if (wp_remote_retrieve_response_code($response) === 200) {
			//response message
			$body = json_decode( wp_remote_retrieve_body( $response ), true );

			if (!isset($body['data'])) {
				return;
			}

			$settings = get_option('lazytask_settings');
			if (!is_array($settings)) {
				$settings = [];
			}

			$firebase_config = isset($settings['firebase_configuration']) ? json_decode($settings['firebase_configuration'], true) : [];
			$firebase_config['wordpress_client_email'] = $body['data']['client_email'];
    		$firebase_config['wordpress_private_key']  = $body['data']['private_key'];

			$settings['firebase_configuration'] = json_encode($firebase_config);

			update_option('lazytask_settings', $settings);

		}
	}

}
