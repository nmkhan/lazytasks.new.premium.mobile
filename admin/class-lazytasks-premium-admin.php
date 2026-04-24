<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://lazycoders.co
 * @since      1.0.0
 *
 * @package    Lazytasks_Premium
 * @subpackage Lazytasks_Premium/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Lazytasks_Premium
 * @subpackage Lazytasks_Premium/admin
 * @author     Lazycoders <info@lazycoders.co>
 */
class Lazytasks_Premium_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Lazytasks_Premium_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Lazytasks_Premium_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if (isset($_REQUEST['page']) && str_contains($_REQUEST['page'], 'lazytasks-page')){
			// phpcs:ignore WordPress.WP.EnqueuedStylesScope
			wp_enqueue_style( 'lazytasks-premium-style', plugin_dir_url( __DIR__ ) . 'admin/frontend/build/index.css', array(), $this->version, 'all');
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lazytasks-premium-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Lazytasks_Premium_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Lazytasks_Premium_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lazytasks-premium-admin.js', array( 'jquery' ), $this->version, false );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if (isset($_REQUEST['page']) && str_contains($_REQUEST['page'], 'lazytasks-page')) {
			// phpcs:ignore WordPress.WP.EnqueuedScriptsScope
			wp_enqueue_script('lazytasks-premium-script', plugin_dir_url( __DIR__ ) . 'admin/frontend/build/index.js', array('lazytasks-script', 'wp-element'), $this->version, true);
			wp_set_script_translations(
				'lazytasks-premium-script',
				'lazytasks-premium',
				plugin_dir_path( __DIR__ ) . 'languages'
			);
			wp_localize_script('lazytasks-premium-script', 'appLocalizerPremium', [
				'apiUrl' => home_url('/wp-json'),
				'homeUrl' => home_url(''),
				'nonce' => wp_create_nonce('wp_rest'),
                'qrCode' => get_option('lazytask_free_qr_code', ''),
				'i18n' => \LazytasksPremium\Services\TransStrings::getStrings(),
			]);
		}

	}

	//api routes
	public function lazytask_premium_admin_routes() {
		(new \LazytasksPremium\Routes\Lazytask_Premium_Api())->admin_routes();
		(new \LazytasksPremium\Routes\Lazytask_Premium_Api_V3())->admin_routes();
	}

	public function lazytask_premium_redirect()
	{
		if ( (int)get_option( 'lazytask_do_activation_redirect' ) === 1 ) {
			update_option('lazytask_do_activation_redirect', 0 );
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			exit(esc_url(wp_safe_redirect(admin_url('admin.php?page=lazytasks-page#/dashboard'))));
		}

	}

	public function lazytask_premium_license_check() {

		$lazytask_license_key  = get_option('lazytask_license_key');
		if (empty($lazytask_license_key)) {
			return;
		}

		$siteUrl = get_site_url();

		$params = array(
			'site_url' => $siteUrl,
			'license_key' => $lazytask_license_key,
			'email' => wp_get_current_user()->user_email,
		);

		$url = LAZYTASK_PREMIUM_APP_BUILDER_RESOURCE_URL."/api/appza/v1/license/check";

		$args = array(
			'method' => 'GET',
			'headers' => array(
				'Lazy-Task-Hash' => get_option('lazytask_hash') ?? '',
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
				'Access-Control-Allow-Origin' => '*',
			),
			'body' => $params
		);

		$response = wp_remote_request( $url,  $args);

		if (is_wp_error($response)) {
			return;
		}

		$body     = json_decode(wp_remote_retrieve_body( $response ), true);

		if(200 === $body['status'] && $body['data']['status'] === 'valid' && $body['data']['success'] === true ){
			$expire_date = $body['data']['expiration_date'] ?? '';
            update_option('lazytask_license_expire_date', $expire_date);
            update_option('lazytask_license_key', $lazytask_license_key);
            update_option('lazytask_license_activate', true);
            update_option('lazytask_license_response', $body['data']);
			\LazytasksPremium\Helper\Lazytask_Helper_QR_Code::lazytask_preview_app_qrcode_generator();

		} else {
			delete_option('lazytask_license_expire_date');
			update_option('lazytask_license_activate', false);
			update_option('lazytask_license_response', $body['data']);
		}

	}

	public function lazytask_premium_license_expiration_notice() {

		$license_response = get_option( 'lazytask_license_response' );
		$expire_date = $license_response['expiration_date'] ?? null;
		$status  = $license_response['status'] ?? 'invalid';

		if ( ! $expire_date || $expire_date=='lifetime' || !in_array( $status, ['valid', 'expired']) ) {
			return;
		}

		$today      = new DateTime( current_time( 'Y-m-d h:i:s' ) );
		$expiryDate = new DateTime( $expire_date );
		$interval   = $today->diff( $expiryDate )->days;
		if ( $expiryDate < $today ) {
			?>
			<div class="notice notice-error is-dismissible lazytasks-premium-license-notice">
				<p><?php _e( 'Your Lazytasks Premium plugin has expired! Please renew to continue using premium features.', 'lazytasks-premium' ); ?></p>
			</div>
			<?php
			return;
		}

		if ( $interval <= 7 ) {
			?>
			<div class="notice notice-warning is-dismissible lazytasks-premium-license-notice">
				<p>
					<?php
					printf(
						__( 'Your Lazytasks Premium plugin will expire in %s. ', 'lazytasks-premium' ),
						sprintf(
							_n( '%d day', '%d days', $interval, 'lazytasks-premium' ),
							$interval
						)
					);
					?>
				</p>
			</div>
			<?php
		}
	}


    public function lazytask_premium_database_migrate()
    {

        if( !defined('LAZYTASKS_PREMIUM_DB_VERSION') || get_option('lazytask_premium_db_version')==='' || version_compare(get_option('lazytask_premium_db_version'), LAZYTASKS_PREMIUM_DB_VERSION, '<') ) {

            $hook = 'lazytasks_premium_daily_event';
            $recurrence = 'daily';
            $next_scheduled = wp_next_scheduled($hook);

            $current_schedule = wp_get_schedule($hook);

            if ( ! $next_scheduled || $current_schedule !== $recurrence ) {
                wp_clear_scheduled_hook($hook);

                wp_schedule_event(time(), $recurrence, $hook);
            }

            update_option('lazytask_premium_db_version', LAZYTASKS_PREMIUM_DB_VERSION, 'no');

        }

    }



}
