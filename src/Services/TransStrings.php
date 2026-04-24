<?php

namespace LazytasksPremium\Services;

/**
 * Central registry of user-facing translatable strings for the LazyTasks
 * Premium addon. Loaded into React via wp_localize_script as
 * window.appLocalizerPremium.i18n; callers use the translate() helper
 * at admin/frontend/src/utils/i18n.js.
 */
class TransStrings {

	public static function getStrings() {
		return [
			// License form
			'License'                                      => __( 'License', 'lazytasks-premium' ),
			'License key is required'                      => __( 'License key is required', 'lazytasks-premium' ),
			'License key verified successfully!'           => __( 'License key verified successfully!', 'lazytasks-premium' ),
			'Activate Your Plugin License'                 => __( 'Activate Your Plugin License', 'lazytasks-premium' ),
			'Enter your license key to activate the LazyTasks plugin.' => __( 'Enter your license key to activate the LazyTasks plugin.', 'lazytasks-premium' ),
			'Insert your License key here which you will find to your lazycoders.co site profile.' => __( 'Insert your License key here which you will find to your lazycoders.co site profile.', 'lazytasks-premium' ),
			'Enter your license key'                       => __( 'Enter your license key', 'lazytasks-premium' ),
			'Verify'                                       => __( 'Verify', 'lazytasks-premium' ),
			'Verified'                                     => __( 'Verified', 'lazytasks-premium' ),
			'Delete'                                       => __( 'Delete', 'lazytasks-premium' ),

			// Trial / upgrade CTA
			'Your trial is about to expire'                => __( 'Your trial is about to expire', 'lazytasks-premium' ),
			'Upgrade your experience with the Lazytasks mobile App and get even more out of your Tasks Management!' => __( 'Upgrade your experience with the Lazytasks mobile App and get even more out of your Tasks Management!', 'lazytasks-premium' ),
			'Purchase Now'                                 => __( 'Purchase Now', 'lazytasks-premium' ),

			// Mobile app section
			'How to connect "LazyTasks" mobile app with installed plugin' => __( 'How to connect "LazyTasks" mobile app with installed plugin', 'lazytasks-premium' ),
			'Download LazyTasks Mobile App'                => __( 'Download LazyTasks Mobile App', 'lazytasks-premium' ),
			'Connect Your LazyTasks Mobile App'            => __( 'Connect Your LazyTasks Mobile App', 'lazytasks-premium' ),
			'Mobile App'                                   => __( 'Mobile App', 'lazytasks-premium' ),
			'Direction here'                               => __( 'Direction here', 'lazytasks-premium' ),
		];
	}
}
