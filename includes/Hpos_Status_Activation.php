<?php
/**
 * HPOS Status Indicator for WooCommerce - Activation.
 *
 * @package hpos-status-indicator-for-woocommerce
 */

namespace HPOS_STATUS;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Activation.
 */
class Hpos_Status_Activation {
	/**
	 * Construct.
	 */
	public function __construct() {
		register_activation_hook( HPOS_STATUS_PLUGIN_FILE, array( $this, 'activate' ) );
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );

		if ( ! $this->is_woocommerce_installed() || ! $this->is_woocommerce_active() ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_not_available' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'thankyou_message' ) );
		}
	}

	/**
	 * Load textdomain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'hpos-status-indicator-for-woocommerce',
			false,
			HPOS_STATUS_LANGUAGES_PATH
		);
	}

	/**
	 * Check if WooCommerce is installed.
	 *
	 * @return bool
	 */
	public static function is_woocommerce_installed() {
		$plugin_path = WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';
		return file_exists( $plugin_path );
	}

	/**
	 * Check if WooCommerce is active.
	 *
	 * @return bool
	 */
	public static function is_woocommerce_active() {
		if ( ! self::is_woocommerce_installed() ) {
			return false;
		}

		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$plugin                 = 'woocommerce/woocommerce.php';
		$single_site_activation = is_plugin_active( $plugin );
		$network_activation     = is_multisite() && is_plugin_active_for_network( $plugin );
		return $single_site_activation || $network_activation;
	}

	/**
	 * Admin notice if WooCommerce is not active or not installed.
	 */
	public function woocommerce_not_available() {
		if ( ! $this->is_woocommerce_installed() ) {
			// WooCommerce is not installed.
			$woocommerce_install_url = wp_nonce_url(
				self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ),
				'install-plugin_woocommerce'
			);

			echo '<div class="notice notice-error is-dismissible">
			<p>' . sprintf(
				/* translators: %1$s is the opening <a> tag with a link to install WooCommerce, and %2$s is the closing </a> tag. */
				esc_html__( 'HPOS Status Indicator for WooCommerce requires WooCommerce to be installed and active. Please %1$s Install WooCommerce%2$s.', 'hpos-status-indicator-for-woocommerce' ),
				'<a href="' . esc_url( $woocommerce_install_url ) . '">',
				'</a>'
			) . '</p>
			</div>';
		}

		if ( ! $this->is_woocommerce_active() ) {
			// WooCommerce is installed but not activated.
			$woocommerce_activate_url = wp_nonce_url(
				admin_url( 'plugins.php?action=activate&plugin=woocommerce/woocommerce.php' ),
				'activate-plugin_woocommerce/woocommerce.php'
			);

			echo '<div class="notice notice-error is-dismissible">
			<p>' . sprintf(
				/* translators: %1$s is the opening <a> tag with a link to activate WooCommerce, and %2$s is the closing </a> tag. */
				esc_html__( 'HPOS Status Indicator for WooCommerce requires WooCommerce to be active. Please %1$s Activate WooCommerce %2$s.', 'hpos-status-indicator-for-woocommerce' ),
				'<a href="' . esc_url( $woocommerce_activate_url ) . '">',
				'</a>'
			) . '</p>
			</div>';
		}
	}

	/**
	 * Thank you for installing admin notice.
	 */
	public function thankyou_message() {
		if ( get_transient( 'hpos_status_activated_plugin' ) ) {
			echo '<div class="notice notice-success is-dismissible">
						<p>' . esc_html__( 'Thanks for activating HPOS Status Indicator for WooCommerce.', 'hpos-status-indicator-for-woocommerce' ) . '</p>
					  </div>';

			delete_transient( 'hpos_status_activated_plugin' );
		}
	}

	/**
	 * Declare HPOS compatibility for this plugin.
	 */
	public function declare_hpos_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', HPOS_STATUS_PLUGIN_FILE, true );
		}
	}

	/**
	 * Set transient when activate.
	 */
	public function activate() {
		set_transient( 'hpos_status_activated_plugin', true, 5 );
	}
}
