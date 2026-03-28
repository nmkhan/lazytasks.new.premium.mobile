<?php

namespace LazytasksPremium\Routes;

use LazytasksPremium\Controller\Lazytask_Default_Controller;
use LazytasksPremium\Controller\v3\Lazytask_Default_Controller as Lazytask_Default_ControllerV3;
use WP_REST_Server;

class Lazytask_Premium_Api_V3 {

	const PREMIUM_ROUTE_NAMESPACE = 'lazytasks/api/v3';

	public function admin_routes() {
		// v3 controller — self-contained JWT validation
		register_rest_route(
			self::PREMIUM_ROUTE_NAMESPACE,
			'/premium/qr-code',
			array(
				'method' => WP_REST_Server::READABLE,
				'callback' => array( new Lazytask_Default_ControllerV3(), 'getQRCode' ),
				'permission_callback' => '__return_true',
				'args' => array()
			)
		);

		// Facade — delegates to v1 controller (unchanged)
		register_rest_route(
			self::PREMIUM_ROUTE_NAMESPACE,
			'/premium/valid-qr-code-scan-check',
			array(
				'method' => WP_REST_Server::READABLE,
				'callback' => array( new Lazytask_Default_Controller(), 'validQrCodeScanCheck' ),
				'permission_callback' => '__return_true',
				'args' => array()
			)
		);

		// Facade — delegates to v1 controller (unchanged)
		register_rest_route(
			self::PREMIUM_ROUTE_NAMESPACE,
			'/premium/license-validation',
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array( new Lazytask_Default_Controller(), 'lazytask_license_validation' ),
				'permission_callback' => '__return_true'
			)
		);

		// Facade — delegates to v1 controller (unchanged)
		register_rest_route(
			self::PREMIUM_ROUTE_NAMESPACE,
			'/premium/license-delete',
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array( new Lazytask_Default_Controller(), 'lazytask_license_delete' ),
				'permission_callback' => '__return_true'
			)
		);

		// Facade — delegates to v1 controller (unchanged)
		register_rest_route(
			self::PREMIUM_ROUTE_NAMESPACE,
			'/premium/get-license-key',
			array(
				'method' => WP_REST_Server::READABLE,
				'callback' => array( new Lazytask_Default_Controller(), 'getLicenseKey' ),
				'permission_callback' => '__return_true',
				'args' => array()
			)
		);
	}
}
