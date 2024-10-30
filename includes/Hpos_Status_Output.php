<?php
/**
 * HPOS Status Indicator for WooCommerce - Output.
 *
 * @package hpos-status-indicator-for-woocommerce
 */

namespace HPOS_STATUS;

use Automattic\WooCommerce\Utilities\OrderUtil;
use WP_Admin_Bar;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hpos_Status_Output
 */
class Hpos_Status_Output {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_bar_menu', array( $this, 'add_hpos_status_indicator_to_admin_bar' ), 100 );
		add_action( 'wp_head', array( $this, 'output_css' ) );
		add_action( 'admin_head', array( $this, 'output_css' ) );
	}

	/**
	 * Check whether the HPOS status is enabled or not.
	 *
	 * @return bool True if HPOS is enabled, false otherwise.
	 */
	public function check_hpos_status() {
		if ( class_exists( OrderUtil::class ) ) {
			return OrderUtil::custom_orders_table_usage_is_enabled();
		}
		return false;
	}

	/**
	 * Output CSS
	 *
	 * @return void
	 */
	public function output_css() {
		if ( is_admin_bar_showing() ) {
			wp_register_style( 'hpos_status_indicator_style', false ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			wp_enqueue_style( 'hpos_status_indicator_style' );
			wp_add_inline_style(
				'hpos_status_indicator_style',
				'
				#wpadminbar .quicklinks #wp-admin-bar-hpos-status-indicator {
					padding: 7px 0;
				}
				#wpadminbar .quicklinks #wp-admin-bar-hpos-status-indicator a.ab-item {
					/* Layout  */
					background-color: #F6F7F7;
					border-radius: 2px;
					display: flex;
					height: 18px;
					padding: 0px 6px;
					align-items: center;
					gap: 8px;

					/* Typography  */
					color: #3C434A;
					font-size: 12px;
					font-style: normal;
					font-weight: 500;
					line-height: 16px;
				}
				#wpadminbar .quicklinks #wp-admin-bar-hpos-status-indicator a.ab-item .active {
					color: #007414;
				}
				#wpadminbar .quicklinks #wp-admin-bar-hpos-status-indicator a.ab-item .inactive {
					color: #D63638;
				}
				#wpadminbar .quicklinks #wp-admin-bar-hpos-status-indicator a.ab-item:hover,
				#wpadminbar .quicklinks #wp-admin-bar-hpos-status-indicator a.ab-item:focus {
					background-color: #f1f1f1;
				}
				#wpadminbar .quicklinks #wp-admin-bar-hpos-status-indicator a.ab-item:focus {
					outline: var(--wp-admin-border-width-focus) solid var(--wp-admin-theme-color-darker-20);
				}'
			);
		}
	}

	/**
	 * Add HPOS status indicator to the WordPress admin bar.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The WordPress admin bar object.
	 */
	public function add_hpos_status_indicator_to_admin_bar( $wp_admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$hpos_active = $this->check_hpos_status();
		$class_name  = $hpos_active ? 'active' : 'inactive';
		$status_text = $hpos_active ? __( 'Active', 'hpos-status-indicator-for-woocommerce' ) : __( 'Inactive', 'hpos-status-indicator-for-woocommerce' );
		$status_span = '<span class="woocommerce-site-status-hpos-status">' . __( 'HPOS: ', 'hpos-status-indicator-for-woocommerce' ) . '<span class="' . esc_attr( $class_name ) . '">' . esc_html( $status_text ) . '</span></span>';
		$wp_admin_bar->add_node(
			array(
				'id'    => 'hpos-status-indicator',
				'title' => $status_span,
				'href'  => esc_url(
					admin_url( 'admin.php?page=wc-settings&tab=advanced&section=features' )
				),
			)
		);
	}
}
