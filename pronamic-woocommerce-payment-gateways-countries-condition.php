<?php
/**
 * Pronamic WooCommerce Payment Gateways Countries Condition
 *
 * @package   PronamicWooCommercePaymentGatewaysCountriesCondition
 * @author    Pronamic
 * @copyright 2023 Pronamic
 * 
 * @wordpress-plugin
 * Plugin Name: Pronamic WooCommerce Payment Gateways Countries Condition
 * Description: This plugin allows you to specify the countries in which each WooCommerce payment gateway is available.
 * Version:     1.0.0
 * Author:      Pronamic
 * Author URI:  https://www.pronamic.eu/
 * Text Domain: pronamic-woocommerce-payment-gateways-countries-condition
 * Domain Path: /languages/
 * License:     Proprietary
 * License URI: https://www.pronamic.shop/product/pronamic-woocommerce-payment-gateways-countries-condition/
 * Update URI:  https://wp.pronamic.directory/plugins/pronamic-woocommerce-payment-gateways-countries-condition/
 */

/**
 * Autoload.
 */
require_once __DIR__ . '/vendor/autoload_packages.php';

/**
 * Bootstrap.
 */
\Pronamic\WooCommercePaymentGatewaysCountriesCondition\Plugin::instance( __FILE__ )->setup();
