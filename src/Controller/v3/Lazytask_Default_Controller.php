<?php

namespace LazytasksPremium\Controller\v3;

use LazytasksPremium\Controller\Lazytask_Default_Controller as Lazytask_Default_ControllerV1;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Lazytask_Default_Controller extends Lazytask_Default_ControllerV1 {

	/**
	 * Self-contained JWT validation — no coupling to main plugin controller classes.
	 * Uses Firebase\JWT from the main plugin's vendor autoloader.
	 */
	protected function validate_token( WP_REST_Request $request ) {
		$auth_header = $request->get_header( 'Authorization' );

		if ( ! $auth_header ) {
			return new WP_Error( 'jwt_auth_no_auth_header', 'Authorization header not found.', [ 'status' => 403 ] );
		}

		[ $token ] = sscanf( $auth_header, 'Bearer %s' );

		if ( ! $token ) {
			return new WP_Error( 'jwt_auth_bad_auth_header', 'Authorization header is required.', [ 'status' => 403 ] );
		}

		$secret_key = defined( 'LAZYTASK_JWT_SECRET_KEY' ) ? LAZYTASK_JWT_SECRET_KEY : false;
		if ( ! $secret_key ) {
			return new WP_Error( 'jwt_auth_bad_config', 'JWT is not configured properly.', [ 'status' => 403 ] );
		}

		try {
			$decoded = JWT::decode( $token, new Key( $secret_key, 'HS256' ) );

			if ( $decoded->iss !== get_bloginfo( 'url' ) ) {
				return new WP_Error( 'jwt_auth_bad_iss', 'The iss does not match this server.', [ 'status' => 403 ] );
			}

			if ( ! isset( $decoded->data->user_id ) ) {
				return new WP_Error( 'jwt_auth_bad_request', 'User ID not found in the token.', [ 'status' => 403 ] );
			}

			if ( time() > $decoded->exp ) {
				return new WP_Error( 'jwt_auth_bad_request', 'Token has expired.', [ 'status' => 408 ] );
			}

			return [
				'code'   => 'jwt_auth_valid_token',
				'status' => 200,
				'data'   => [ 'token' => $decoded, 'status' => 200 ],
			];

		} catch ( Exception $e ) {
			return new WP_Error( 'jwt_auth_invalid_token', $e->getMessage(), [ 'status' => 403 ] );
		}
	}

	/**
	 * Override getQRCode to use self-contained JWT validation
	 * instead of the broken Lazytask_UserController::decode() import.
	 */
	public function getQRCode( WP_REST_Request $request ) {
		$response = $this->validate_token( $request );

		if ( is_wp_error( $response ) ) {
			$error_data = $response->get_error_data();
			$status     = $error_data['status'] ?? 403;
			return new WP_REST_Response( [
				'code'    => $response->get_error_code(),
				'status'  => $status,
				'message' => $response->get_error_message(),
				'data'    => $error_data,
			], $status );
		}

		$qrCodeImage = get_option( 'lazytask_premium_qr_code', '' );
		if ( $qrCodeImage ) {
			return new WP_REST_Response( [ 'status' => 200, 'data' => [ 'path' => $qrCodeImage ] ], 200 );
		}
		return new WP_REST_Response( [ 'status' => 404, 'data' => '' ], 200 );
	}
}
