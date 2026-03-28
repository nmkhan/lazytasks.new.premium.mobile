<?php

namespace LazytasksPremium\Routes;

use LazytasksPremium\Controller\Lazytask_Default_Controller;
use WP_REST_Server;

class Lazytask_Premium_Api {

	const PREMIUM_ROUTE_NAMESPACE = 'lazytasks/api/v1';
	public function admin_routes(){
		register_rest_route(
			self::PREMIUM_ROUTE_NAMESPACE,
			'/premium/qr-code',
			array(
				'method' => WP_REST_Server::READABLE,
				'callback' => array(new Lazytask_Default_Controller(), 'getQRCode'),
				'permission_callback' => '__return_true',
				'args' => array()
			)
		);
		register_rest_route(
			self::PREMIUM_ROUTE_NAMESPACE,
			'/premium/valid-qr-code-scan-check',
			array(
				'method' => WP_REST_Server::READABLE,
				'callback' => array(new Lazytask_Default_Controller(), 'validQrCodeScanCheck'),
				'permission_callback' => '__return_true',
				'args' => array()
			)
		);
		register_rest_route(
			self::PREMIUM_ROUTE_NAMESPACE,
			'/premium/license-validation',
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array(new Lazytask_Default_Controller(), 'lazytask_license_validation'),
				'permission_callback' => '__return_true'
			)
		);
		register_rest_route(
			self::PREMIUM_ROUTE_NAMESPACE,
			'/premium/license-delete',
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array(new Lazytask_Default_Controller(), 'lazytask_license_delete'),
				'permission_callback' => '__return_true'
			)
		);
//		getLicenseKey api
		register_rest_route(
			self::PREMIUM_ROUTE_NAMESPACE,
			'/premium/get-license-key',
			array(
				'method' => WP_REST_Server::READABLE,
				'callback' => array(new Lazytask_Default_Controller(), 'getLicenseKey'),
				'permission_callback' => '__return_true',
				'args' => array()
			)
		);
	}

}