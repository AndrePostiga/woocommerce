<?php
/**
 * Includes the composer Autoloader used for packages and classes in the src/ directory.
 *
 * @package Automattic/WooCommerce
 */

namespace Automattic\WooCommerce;

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader class.
 *
 * @since 3.7.0
 */
class Autoloader {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Require the autoloader and return the result.
	 *
	 * If the autoloader is not present, let's log the failure and display a nice admin notice.
	 *
	 * @return boolean
	 */
	public static function init() {
		$autoloader = dirname( __DIR__ ) . '/vendor/autoload_packages.php';

		if ( ! is_readable( $autoloader ) ) {
			self::missing_autoloader();
			return false;
		}

		/**
		 * In order to support local development for feature plugins the autoloader must support dev versions.
		 *
		 * - If the checked out branch cannot supply Composer with version information then it
		 *   assigns it a dev version string for the Jetpack Autoloader to use.
		 * - By default the Jetpack Autoloader will ignore these dev versions in favor of tagged versions.
		 *
		 * Due to this interaction, feature plugin files from the included packages will always be loaded instead
		 * of the versions in the feature plugin when checked out from a repository as a dev version. By setting
		 * this constant we change the behavior of the autoloader so that dev versions are prioritized over the
		 * tagged versions included in WooCommerce Core.
		 */
		define( 'JETPACK_AUTOLOAD_DEV', true );

		$autoloader_result = require $autoloader;
		if ( ! $autoloader_result ) {
			return false;
		}

		return $autoloader_result;
	}

	/**
	 * If the autoloader is missing, add an admin notice.
	 */
	protected static function missing_autoloader() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log(  // phpcs:ignore
				esc_html__( 'Your installation of WooCommerce is incomplete. If you installed WooCommerce from GitHub, please refer to this document to set up your development environment: https://github.com/woocommerce/woocommerce/wiki/How-to-set-up-WooCommerce-development-environment', 'woocommerce' )
			);
		}
		add_action(
			'admin_notices',
			function() {
				?>
				<div class="notice notice-error">
					<p>
						<?php
						printf(
							/* translators: 1: is a link to a support document. 2: closing link */
							esc_html__( 'Your installation of WooCommerce is incomplete. If you installed WooCommerce from GitHub, %1$splease refer to this document%2$s to set up your development environment.', 'woocommerce' ),
							'<a href="' . esc_url( 'https://github.com/woocommerce/woocommerce/wiki/How-to-set-up-WooCommerce-development-environment' ) . '" target="_blank" rel="noopener noreferrer">',
							'</a>'
						);
						?>
					</p>
				</div>
				<?php
			}
		);
	}
}
