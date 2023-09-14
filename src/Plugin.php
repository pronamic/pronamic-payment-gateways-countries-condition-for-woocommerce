<?php
/**
 * Pronamic WooCommerce Payment Gateways Countries Condition Plugin
 *
 * @package   PronamicWooCommercePaymentGatewaysCountriesCondition
 * @author    Pronamic
 * @copyright 2023 Pronamic
 */

namespace Pronamic\WooCommercePaymentGatewaysCountriesCondition;

/**
 * Pronamic WooCommerce Payment Gateways Countries Condition class
 */
class Plugin {
	/**
	 * Instance of this class.
	 *
	 * @since 4.7.1
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * Plugin file.
	 *
	 * @var string
	 */
	private $file;

	/**
	 * Return an instance of this class.
	 *
	 * @param string $file File.
	 * @return self A single instance of this class.
	 */
	public static function instance( $file = '' ) {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self( $file );
		}

		return self::$instance;
	}

	/**
	 * Construct plugin.
	 * 
	 * @param string $file File.
	 */
	public function __construct( $file ) {
		$this->file = $file;
	}

	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		if ( \has_action(  'plugins_loaded', [ $this, 'plugins_loaded' ] ) ) {
			return;
		}

		\add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ] );
	}

	/**
	 * Plugins loaded.
	 * 
	 * @return void
	 */
	public function plugins_loaded() {
		if ( ! \function_exists( 'WC' ) ) {
			return;
		}

		\load_plugin_textdomain( 'pronamic-woocommerce-payment-gateways-countries-condition', false, \dirname( \plugin_basename( __FILE__ ) ) . '/languages' ); 
		
		\add_action( 'init', [ $this, 'init' ], 1000 );

		\add_filter( 'woocommerce_available_payment_gateways', [ $this, 'woocommerce_available_payment_gateways' ] );
	}

	/**
	 * Init.
	 * 
	 * @return void
	 */
	public function init() {
		$payment_gateways = \WC()->payment_gateways()->payment_gateways();

		foreach ( $payment_gateways as $payment_gateway ) {
			\add_filter( 'woocommerce_settings_api_form_fields_' . $payment_gateway->id, [ $this, 'add_countries_setting' ] );
		}
	}

	/**
	 * Add countries setting field the specified fields.
	 * 
	 * @link https://woocommerce.com/document/settings-api/
	 * @link https://github.com/woocommerce/woocommerce/blob/473a53d54243c6b749a4532112eea4ac8667447f/plugins/woocommerce/includes/shipping/legacy-local-pickup/class-wc-shipping-legacy-local-pickup.php#L133-L143
	 * @link https://github.com/woocommerce/woocommerce/blob/473a53d54243c6b749a4532112eea4ac8667447f/plugins/woocommerce/includes/shipping/legacy-flat-rate/includes/settings-flat-rate.php#L41-L51
	 * @link https://github.com/woocommerce/woocommerce/blob/473a53d54243c6b749a4532112eea4ac8667447f/plugins/woocommerce/includes/gateways/cod/class-wc-gateway-cod.php#L118-L130
	 * @param array $fields Fields.
	 * @return array
	 */
	public function add_countries_setting( $fields ) {
		$fields['pronamic_countries_title'] = [
			'title' => \__( 'Countries condition', 'pronamic-woocommerce-payment-gateways-countries-condition' ),
			'type'  => 'title',
		];

		$fields['pronamic_countries'] = [
			'title'             => \__( 'Countries', 'pronamic-woocommerce-payment-gateways-countries-condition' ),
			'type'              => 'multiselect',
			'class'             => 'wc-enhanced-select',
			'css'               => 'width: 400px;',
			'options'           => \WC()->countries->get_countries(),
			'default'           => '',
			'description'       => \__( 'If gateway is only available for certain countries, set it up here. Leave blank to enable for all countries.', 'pronamic-woocommerce-payment-gateways-countries-condition' ),
			'desc_tip'          => true,
			'custom_attributes' => [
				'data-placeholder' => \__( 'Select some countries', 'pronamic-woocommerce-payment-gateways-countries-condition' ),
			],
		];

		return $fields;
	}

	/**
	 * WooCommerce available payment gateways.
	 * 
	 * @link https://github.com/woocommerce/woocommerce/blob/36c644a1c4af908b5c1b2060c215b5643dd07648/plugins/woocommerce/includes/class-wc-payment-gateways.php#L145-L164
	 * @param array $gateways Gateways.
	 * @return array
	 */
	public function woocommerce_available_payment_gateways( $gateways ) {
		$country = $this->get_country();

		if ( '' === $country ) {
			return $gateways;
		}

		$gateways = \array_filter(
			$gateways,
			function ( $gateway ) use ( $country ) {
				$countries = $gateway->get_option( 'pronamic_countries' );

				if ( ! \is_array( $countries ) ) {
					return true;
				}

				if ( [] === $countries ) {
					return true;
				}

				return \in_array( $country, $countries, true );
			}
		);

		return $gateways;
	}

	/**
	 * Get country.
	 * 
	 * @link https://github.com/mollie/WooCommerce/blob/0b8635a335ce201a00ebf125fb4795e62a315760/src/Gateway/MolliePaymentGateway.php#L1056-L1073
	 * @link https://github.com/woocommerce/woocommerce/blob/36c644a1c4af908b5c1b2060c215b5643dd07648/plugins/woocommerce/includes/class-wc-countries.php#L242-L250
	 * @return string
	 */
	private function get_country() {
		$country = $this->get_customer_billing_country();

		if ( '' === $country ) {
			$country = $this->get_base_country();
		}

		return $country;
	}

	/**
	 * Get customer billing country.
	 * 
	 * @link https://github.com/mollie/WooCommerce/blob/0b8635a335ce201a00ebf125fb4795e62a315760/src/Gateway/MolliePaymentGateway.php#L1056-L1073
	 * @link https://github.com/woocommerce/woocommerce/blob/36c644a1c4af908b5c1b2060c215b5643dd07648/plugins/woocommerce/includes/class-woocommerce.php#L105-L110
	 * @link https://github.com/woocommerce/woocommerce/blob/36c644a1c4af908b5c1b2060c215b5643dd07648/plugins/woocommerce/includes/class-wc-customer.php#L587-L595
	 * @return string
	 */
	private function get_customer_billing_country() {
		if ( null === \WC()->customer ) {
			return '';
		}

		return \WC()->customer->get_billing_country();
	}

	/**
	 * Get base country.
	 * 
	 * @link https://github.com/mollie/WooCommerce/blob/0b8635a335ce201a00ebf125fb4795e62a315760/src/Gateway/MolliePaymentGateway.php#L1056-L1073
	 * @link https://github.com/woocommerce/woocommerce/blob/36c644a1c4af908b5c1b2060c215b5643dd07648/plugins/woocommerce/includes/class-woocommerce.php#L105-L110
	 * @link https://github.com/woocommerce/woocommerce/blob/36c644a1c4af908b5c1b2060c215b5643dd07648/plugins/woocommerce/includes/class-wc-customer.php#L587-L595
	 * @return string
	 */
	private function get_base_country() {
		if ( null === \WC()->countries ) {
			return '';
		}

		return \WC()->countries->get_base_country();
	}
}
