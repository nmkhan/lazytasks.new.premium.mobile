<?php

namespace LazytasksPremium\Controller;

use Lazytask\Controller\Lazytask_UserController;
use WP_REST_Request;
use WP_REST_Response;

class Lazytask_Default_Controller {

	public function getQRCode( WP_REST_Request $request ) {

		// headers check
		$token = $request->get_header('Authorization');
		$token = str_replace('Bearer ', '', $token);
		$token = str_replace('bearer ', '', $token);
		$token = str_replace('Token ', '', $token);
		$token = str_replace('token ', '', $token);

		// decode token
		$userController = new Lazytask_UserController();
		$decodedToken = $userController->decode($token);
		if($decodedToken && isset($decodedToken['status']) && $decodedToken['status'] == 403 && isset($decodedToken['message']) && $decodedToken['message'] == 'Expired token'){
			return new WP_REST_Response(['code'=> 'jwt_auth_invalid_token', 'status'=>403, 'message'=>$decodedToken['message'], 'data'=>$decodedToken], 403);
		}

		$qrCodeImage = get_option('lazytask_premium_qr_code', '');
		if($qrCodeImage) {
			return new WP_REST_Response( [ 'status' => 200, 'data' => [ 'path' => $qrCodeImage ] ], 200 );
		}
		return new WP_REST_Response( [ 'status' => 404, 'data' => '' ], 200 );
	}

	// check qr code value site url
	public function validQrCodeScanCheck(WP_REST_Request $request) {

		$requestData = $request->get_param('domain');

		$siteUrl = get_site_url();

		if($requestData && $requestData == $siteUrl) {
			return new WP_REST_Response( [ 'status' => 200, 'data' => true ], 200 );
		}
		return new WP_REST_Response( [ 'status' => 404, 'data' => false ], 404 );
	}

	public function lazytask_license_validation(WP_REST_Request $request): array
	{

		$license_key = $request->get_param('license_key');

		$domain = $request->get_param('domain');

		if ( ! $license_key ) {
			$license_key = get_option('lazytask_license_key', '');
		}

		if ( ! $license_key ) {
			return array(
				'status' => 404,
				'message' => __('License key is not found!', 'lazytasks-premium'),
				'value' => 'not-found',
				'data' => [
					"license_status" => "false",
					"purchase_date" => null,
					"expire_date" => null
				]
			);
		}

		$siteUrl = get_site_url();

		if ( $domain && $domain !== $siteUrl ) {
			return array(
				'status' => 404,
				'message' => __('License key is not valid for this domain!', 'lazytasks-premium'),
				'value' => 'invalid-domain',
				'data' => [
					"license_status" => "false",
					"purchase_date" => null,
					"expire_date" => null
				]
			);
		}

		$params = array(
			'site_url' => $siteUrl,
			'license_key' => $license_key,
			'email' => wp_get_current_user()->user_email,
		);

		if ( get_option('lazytask_license_activate') ) {
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
		} else {
			$url = LAZYTASK_PREMIUM_APP_BUILDER_RESOURCE_URL."/api/appza/v1/license/activate";

			$args = array(
				'method' => 'POST',
				'headers' => array(
					'Lazy-Task-Hash' => get_option('lazytask_hash') ?? '',
					'Accept' => 'application/json',
					'Content-Type' => 'application/json',
					'Access-Control-Allow-Origin' => '*',
				),
				'body' => wp_json_encode( $params )
			);
		}


		$response = wp_remote_request( $url,  $args);

		if ( is_wp_error( $response ) ) {
			return array(
				'status' => 400,
				'message' => __('Something went wrong! Please try again later.', 'lazytasks-premium'),
				'value' => 'error',
				'data' => [
					"license_status" => false,
					"purchase_date" => null,
					"expire_date" => null
				]
			);
		}
		$body     = json_decode(wp_remote_retrieve_body( $response ), true);

		if(200 === $body['status']){
			$expire_date = $body['data']['expiration_date'] ?? '';
			$returnResponse = $body['data'];
			$returnResponse['license_status'] = 'true';
			$returnResponse['purchase_date'] =  null;
			$returnResponse['expire_date'] = $expire_date;
			if (get_option('lazytask_license_key')) {
				update_option('lazytask_license_expire_date', $expire_date);
				update_option('lazytask_license_key', $license_key);
				update_option('lazytask_license_activate', true);
				update_option('lazytask_license_response', $body['data']);
			} else {
				add_option('lazytask_license_expire_date', $expire_date);
				add_option('lazytask_license_key', $license_key);
				add_option('lazytask_license_activate', true);
				add_option('lazytask_license_response', $body['data']);
			}
			return array(
				'status' => 200,
				'message' => __('License key verified successfully!', 'lazytasks-premium'),
				'value' => 'valid',
				'data' => $returnResponse,
			);
		}
		update_option('lazytask_license_response', $body['data']);

		$returnResponse['license_status'] = 'false';
		$returnResponse['purchase_date'] = null;
		$returnResponse['expire_date'] = null;

		return array(
			'status' => $body['status'] ?? 400,
			'message' => $body['message'] ?? 'License key is invalid!',
			'value' => 'invalid',
			'data' => $returnResponse,
		);
	}

	public function lazytask_license_delete( WP_REST_Request $request )
	{
		$domain = $request->get_param('domain');

		if ( ! get_option('lazytask_license_activate') ) {
			return [
				'status' => 400,
				'message' => __('License is not active.', 'lazytasks-premium'),
				'data' => [
					"license_status" => false,
					"purchase_date" => null,
					"expire_date" => null
				]
			];
		}
		$siteUrl = get_site_url();

		if ( $domain && $domain !== $siteUrl ) {
			return new WP_REST_Response( [
				'status' => 404,
				'message' => __('Domain is not valid for this license key!', 'lazytasks-premium'),
				'data' => [
					"license_status" => false,
					"purchase_date" => null,
					"expire_date" => null
				]
			], 404 );
		}


		$license_key  = get_option('lazytask_license_key');
		$params = array(
			'site_url' => $siteUrl,
			'license_key' => $license_key,
			'product' => 'lazy_task',
			'appza_action' => 'license_deactivate',
		);
		$url = LAZYTASK_PREMIUM_APP_BUILDER_RESOURCE_URL."/api/appza/v1/license/deactivate";

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

		if ( is_wp_error( $response ) ) {
			return new WP_REST_Response( array(
				'status' => 500,
				'message' => __('Server error: ', 'lazytasks-premium') . $response->get_error_message(),
				'data' => [
					"license_status" => false,
					"purchase_date" => null,
					"expire_date" => null
				]
			), 500 );
		}

		$body     = json_decode(wp_remote_retrieve_body( $response ), true);

		if(200 === $body['status']){

			delete_option('lazytask_license_expire_date');
			delete_option('lazytask_license_key');
			delete_option('lazytask_license_activate');
			delete_option('lazytask_license_response');

			return new WP_REST_Response(
				[
					'status' => 200,
					'message' => __('License deactivated successfully.', 'lazytasks-premium'),
					'data' => [
						"license_status" => false,
						"purchase_date" => null,
						"expire_date" => null
					]
				]
			);
		}

		return new WP_REST_Response(
			[
				'status' => $body['status'] ?? 400,
				'message' => $body['message'] ?? 'Something went wrong! Please try again later.',
				'data' => [
					"license_status" => false,
					"purchase_date" => null,
					"expire_date" => null
				]
			]
		);

	}

	//get license key
	public function getLicenseKey() {
		//check Lazytask_Lazytasks_Premium class
		if(!class_exists('Lazytask_Lazytasks_Premium')) {
			return new WP_REST_Response( [ 'status' => 404, 'message'=> __('Premium plugin is not active', 'lazytasks-premium'), 'data' => '' ], 404 );
		}

		$license_key = get_option('lazytask_license_key', '');
		if($license_key) {
			return new WP_REST_Response( [ 'status' => 200, 'data' => $license_key ], 200 );
		}
		return new WP_REST_Response( [ 'status' => 404, 'message'=> __('License key is not found', 'lazytasks-premium'), 'data' => '' ], 404 );
	}

}