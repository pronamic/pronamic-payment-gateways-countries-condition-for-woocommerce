<?php
/**
 * Pronamic Payment Gateways Countries Condition for WooCommerce
 *
 * @package   PronamicWooCommercePaymentGatewaysCountriesCondition
 * @author    Pronamic
 * @copyright 2023 Pronamic
 * 
 * @wordpress-plugin
 * Plugin Name: Pronamic Payment Gateways Countries Condition for WooCommerce
 * Plugin URI: https://www.pronamic.shop/product/pronamic-payment-gateways-countries-condition-for-woocommerce/
 * Description: This plugin allows you to specify the countries in which each WooCommerce payment gateway is available.
 * Version: 1.0.0
 * Requires at least: 6.1
 * Requires PHP: 8.0
 * Author: Pronamic
 * Author URI: https://www.pronamic.eu/
 * License: Proprietary
 * License URI: https://www.pronamic.shop/product/pronamic-payment-gateways-countries-condition-for-woocommerce/
 * Text Domain: pronamic-payment-gateways-countries-condition-for-woocommerce
 * Domain Path: /languages/
 * Update URI: https://wp.pronamic.directory/plugins/pronamic-payment-gateways-countries-condition-for-woocommerce/
 * WC requires at least: 8.0
 * WC tested up to: 8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoload.
 */
require_once __DIR__ . '/vendor/autoload_packages.php';

/**
 * Bootstrap.
 */
add_action(
	'plugins_loaded',
	function () {
		load_plugin_textdomain(
			'pronamic-payment-gateways-countries-condition-for-woocommerce',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		); 
	}
);

\Pronamic\WooCommercePaymentGatewaysCountriesCondition\Plugin::instance()->setup();

\Pronamic\WordPress\Updater\Plugin::instance()->setup();
