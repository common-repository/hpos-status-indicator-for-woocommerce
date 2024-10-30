<?php
/**
 * HPOS Status Indicator for WooCommerce
 *
 * @package         hpos-status-indicator-for-woocommerce
 * @author          YMMV LLC
 * @copyright       2024 YMMV LLC
 * @license         GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:             HPOS Status Indicator for WooCommerce
 * Description:             Adds a High Performance Order Storage (HPOS) status indicator to the admin bar that shows the status of HPOS on the site.
 * Author:                  YMMV LLC
 * Contributors:            ymmvplugins
 * Author URI:              https://www.ymmv.co
 * Text Domain:             hpos-status-indicator-for-woocommerce
 * Domain Path:             /languages
 * Version:                 1.0.0
 * Requires PHP:            7.4
 * Requires at least:       6.0
 * Tested up to:            6.6.2
 * WC requires at least:    8.2.0
 * WC tested up to:         9.3.2
 * Requires Plugins:        woocommerce
 * License:                 GPL-2.0-or-later
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Declare constants.
define( 'HPOS_STATUS_PLUGIN_FILE', __FILE__ );
define( 'HPOS_STATUS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'HPOS_STATUS_INCLUDES_PATH', HPOS_STATUS_PLUGIN_PATH . 'includes/' );
define( 'HPOS_STATUS_LANGUAGES_PATH', HPOS_STATUS_PLUGIN_PATH . 'languages/' );

require_once HPOS_STATUS_INCLUDES_PATH . '/Hpos_Status_Activation.php';
require_once HPOS_STATUS_INCLUDES_PATH . '/Hpos_Status_Output.php';

new \HPOS_STATUS\Hpos_Status_Activation();

// Only load the plugin if WooCommerce is active.
if ( \HPOS_STATUS\Hpos_Status_Activation::is_woocommerce_active() ) {
	new \HPOS_STATUS\Hpos_Status_Output();
}
