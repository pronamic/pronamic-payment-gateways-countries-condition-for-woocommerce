<?php
/**
 * Pronamic WooCommerce Conditional Payments Suggest
 *
 * @package   PronamicWooCommerceConditionalPaymentsSuggest
 * @author    Pronamic
 * @copyright 2023 Pronamic
 * 
 * @wordpress-plugin
 * Plugin Name: Pronamic WooCommerce Conditional Payments Suggest
 * Description: This WordPress plugin adds a suggestion for the "Conditional Payment Methods for WooCommerce" plugin to all WooCommerce gateways to restrict payment methods to specific countries.
 * Version:     1.0.0
 * Author:      Pronamic
 * Author URI:  https://www.pronamic.eu/
 * Text Domain: pronamic-woocommerce-conditional-payments-suggest
 * Domain Path: /languages/
 * License:     Proprietary
 * License URI: https://www.pronamic.shop/product/pronamic-woocommerce-conditional-payments-suggest/
 * Update URI:  https://wp.pronamic.directory/plugins/pronamic-woocommerce-conditional-payments-suggest/
 */

/**
 * Pronamic WooCommerce Conditional Payments Suggest Plugin class
 */
class PronamicWooCommerceConditionalPaymentsSuggestPlugin {
	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		add_action( 'init', [ $this, 'init' ], 1000 );

		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );
	}

	/**
	 * Init.
	 * 
	 * @return void
	 */
	public function init() {
		if ( ! function_exists( 'wc' ) ) {
			return;
		}

		$payment_gateways = wc()->payment_gateways()->payment_gateways();

		foreach ( $payment_gateways as $payment_gateway ) {
			add_filter( 'woocommerce_settings_api_form_fields_' . $payment_gateway->id, [ $this, 'add_order_button_text_setting' ] );

			$order_button_text = (string) $payment_gateway->get_option( 'pronamic_order_button_text' );

			if ( '' !== $order_button_text ) {
				$payment_gateway->order_button_text = $order_button_text;
			}
		}
	}

	/**
	 * Add order button text setting field the specified fields.
	 * 
	 * @param array $fields Fields.
	 * @return array
	 */
	public function add_order_button_text_setting( $fields ) {
		$fields['pronamic_order_button_text'] = [
			'title'       => __( 'Order Button Text', 'pronamic-woocommerce-gateway-order-button-text-setting' ),
			'type'        => 'text',
			'default'     => '',
			'description' => __( 'This setting is added by the "Pronamic WooCommerce Gateway Order Button Text Setting" plugin and affects what text visitors see on the WooCommerce order button, leave blank to use the default WooCommerce text.', 'pronamic-woocommerce-gateway-order-button-text-setting' ),
			'desc_tip'    => true,
			'placeholder' => __( 'Pay for order', 'pronamic-woocommerce-gateway-order-button-text-setting' ),
		];

		return $fields;
	}

	/**
	 * Plugins loaded.
	 * 
	 * @return void
	 */
	public function plugins_loaded() {
		load_plugin_textdomain( 'pronamic-woocommerce-gateway-order-button-text-setting', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	}
}

( new PronamicWooCommerceConditionalPaymentsSuggestPlugin() )->setup();
