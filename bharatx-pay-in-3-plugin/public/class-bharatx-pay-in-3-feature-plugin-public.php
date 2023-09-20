<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.bharatx.tech
 * @since      1.2.0
 *
 * @package    Bharatx_Pay_In_3_Feature_Plugin
 * @subpackage Bharatx_Pay_In_3_Feature_Plugin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bharatx_Pay_In_3_Feature_Plugin
 * @subpackage Bharatx_Pay_In_3_Feature_Plugin/public
 * @author     BharatX <Karan@bharatx.tech>
 */
class Bharatx_Pay_In_3_Feature_Plugin_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.2.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	/**
	 * The settings of this plugin.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      array    $settings    Settings of this plugin.
	 */
	private $settings;

	/**
	 * The strings of this plugin.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      array    $strings    Strings of this plugin.
	 */

	private $strings;

	/**
	 * The supported countries of this plugin.
	 *
	 * @since    1.2.0
	 * @access   private
	 * @var      array    $supported_countries  Supported countries of this plugin.
	 */

	private $supported_countries;
	private $max_limit;

	private $category_ids;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.2.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */

	public function __construct($plugin_name, $version)
	{

		$this->plugin_name         = $plugin_name;
		$this->version             = $version;
		$this->supported_countries = array('IN');
		$this->max_limit	       = 15000;
		//	$this->category_ids		   = $this->settings['category_ids'];


		$this->strings = array(
			'price_string'               => 'Or 3 interest free payments of {{ amount }} with {{ logo }} {{ info_icon }}',
			'price_string_for_checkout'               => 'or just pay {{ amount }} with 0% interest {{ info_icon }}',
			'payment_method_title'       => 'Pay In 3 via ',
			'payment_method_description' => '<div>
			<div class="bharatx-explanation-item"><div style="margin-right: 8px; padding-top: 2px;" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 28 28" fill="none"><path d="M20.0734 10.2587C19.6184 9.8031 18.8787 9.8031 18.4237 10.2587L13.4152 15.2666L11.3234 13.1753C10.8684 12.7198 10.1287 12.7198 9.67372 13.1753C9.21814 13.6309 9.21814 14.3694 9.67372 14.825L12.5904 17.7417C12.8179 17.9698 13.1166 18.0835 13.4152 18.0835C13.7139 18.0835 14.0126 17.9698 14.2401 17.7417L20.0734 11.9083C20.529 11.4528 20.529 10.7143 20.0734 10.2587Z" fill="#515151"></path><path d="M26.8333 12.8333C26.1893 12.8333 25.6667 13.356 25.6667 14C25.6667 20.433 20.433 25.6667 14 25.6667C7.567 25.6667 2.33333 20.433 2.33333 14C2.33333 7.567 7.567 2.33333 14 2.33333C17.1319 2.33333 20.0719 3.55717 22.2792 5.77967C22.7325 6.23758 23.4716 6.23992 23.9289 5.7855C24.3862 5.33167 24.3886 4.59317 23.9347 4.13583C21.2864 1.46883 17.7578 0 14 0C6.28017 0 0 6.28017 0 14C0 21.7198 6.28017 28 14 28C21.7198 28 28 21.7198 28 14C28 13.356 27.4773 12.8333 26.8333 12.8333Z" fill="#515151"></path></svg></div><div> Split payments into <strong>3 easy interest-free</strong> parts</div></div>
			<div class="bharatx-explanation-item"><div style="margin-right: 8px; padding-top: 2px;" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 28 28" fill="none"><path d="M20.0734 10.2587C19.6184 9.8031 18.8787 9.8031 18.4237 10.2587L13.4152 15.2666L11.3234 13.1753C10.8684 12.7198 10.1287 12.7198 9.67372 13.1753C9.21814 13.6309 9.21814 14.3694 9.67372 14.825L12.5904 17.7417C12.8179 17.9698 13.1166 18.0835 13.4152 18.0835C13.7139 18.0835 14.0126 17.9698 14.2401 17.7417L20.0734 11.9083C20.529 11.4528 20.529 10.7143 20.0734 10.2587Z" fill="#515151"></path><path d="M26.8333 12.8333C26.1893 12.8333 25.6667 13.356 25.6667 14C25.6667 20.433 20.433 25.6667 14 25.6667C7.567 25.6667 2.33333 20.433 2.33333 14C2.33333 7.567 7.567 2.33333 14 2.33333C17.1319 2.33333 20.0719 3.55717 22.2792 5.77967C22.7325 6.23758 23.4716 6.23992 23.9289 5.7855C24.3862 5.33167 24.3886 4.59317 23.9347 4.13583C21.2864 1.46883 17.7578 0 14 0C6.28017 0 0 6.28017 0 14C0 21.7198 6.28017 28 14 28C21.7198 28 28 21.7198 28 14C28 13.356 27.4773 12.8333 26.8333 12.8333Z" fill="#515151"></path></svg></div><div> Get credit approved with <strong>zero documentation</strong></div></div>
			<div class="bharatx-explanation-item"><div style="margin-right: 8px; padding-top: 2px;" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 28 28" fill="none"><path d="M20.0734 10.2587C19.6184 9.8031 18.8787 9.8031 18.4237 10.2587L13.4152 15.2666L11.3234 13.1753C10.8684 12.7198 10.1287 12.7198 9.67372 13.1753C9.21814 13.6309 9.21814 14.3694 9.67372 14.825L12.5904 17.7417C12.8179 17.9698 13.1166 18.0835 13.4152 18.0835C13.7139 18.0835 14.0126 17.9698 14.2401 17.7417L20.0734 11.9083C20.529 11.4528 20.529 10.7143 20.0734 10.2587Z" fill="#515151"></path><path d="M26.8333 12.8333C26.1893 12.8333 25.6667 13.356 25.6667 14C25.6667 20.433 20.433 25.6667 14 25.6667C7.567 25.6667 2.33333 20.433 2.33333 14C2.33333 7.567 7.567 2.33333 14 2.33333C17.1319 2.33333 20.0719 3.55717 22.2792 5.77967C22.7325 6.23758 23.4716 6.23992 23.9289 5.7855C24.3862 5.33167 24.3886 4.59317 23.9347 4.13583C21.2864 1.46883 17.7578 0 14 0C6.28017 0 0 6.28017 0 14C0 21.7198 6.28017 28 14 28C21.7198 28 28 21.7198 28 14C28 13.356 27.4773 12.8333 26.8333 12.8333Z" fill="#515151"></path></svg></div><div> Get <strong>timely reminders</strong> before the next payment date</div></div>
			</div>
			',
			'varying_product_payment_description' => '3 interest free payments starting with {{ amount }} on {{ logo }} {{ info_icon }}'
		);



		add_action('init', array($this, 'init'));
	}

	public function init()
	{

		$available_payment_methods = WC()->payment_gateways->get_available_payment_gateways();
		if (isset($available_payment_methods['bharatx-pay-in-3-feature-plugin'])) {
			$this->settings = $available_payment_methods['bharatx-pay-in-3-feature-plugin']->settings;

			try {
				$response = wp_remote_get("https://web-v2.bharatx.tech/api/merchant/configuration", array(
					"headers" => array(
						"authorization" => "Basic " . base64_encode(
							$this->settings["merchant_partner_id"] . ":" . $this->settings["merchant_private_key"]
							)
					)
				));

				if (is_a($response, "WP_Error")) {
					throw new Exception($response -> get_error_message() );
				}

				if ($response["response"]["code"] != 200) {
					throw new Exception($response["body"]);
				}

				$configuration = json_decode($response["body"]);
				$this->max_limit = $configuration->maxOrderAmount/100;
			} catch (Exception $e) {
				$errorMessage = "error applying bharatx partner configuration: " . $e->getMessage();

				error_log($errorMessage, 0);
				do_action("qm/error", $errorMessage);
			}

			add_filter('woocommerce_available_payment_gateways', array($this, 'remove_gateway_based_on_billing_total'), 10, 2);
			add_filter('woocommerce_available_payment_gateways', array($this, 'remove_gateway_based_on_billing_country'), 10, 2);
			add_filter('woocommerce_available_payment_gateways', array($this, 'remove_gateway_based_on_category_id'), 10, 2);


			add_filter('woocommerce_gateway_title', array($this, 'checkout_gateway_title'), 10, 2);
			add_filter('woocommerce_gateway_icon', array($this, 'checkout_gateway_icon'), 10, 2);
			add_filter('woocommerce_gateway_description', array($this, 'checkout_gateway_description'), 10, 2);
			add_action('woocommerce_order_status_changed',  array($this, 'custom_order_status_changed_function'), 10, 3);
			add_filter("woocommerce_general_settings" , array($this, 'add_custom_url_setting'));
			add_action('rest_api_init', array($this, 'register_routes'), 10, 2);
			if ( class_exists( 'QM' ) ) {
				do_action( 'qm/debug', "init" );
			}
		}
	}
	public function register_routes() {
		if ( class_exists( 'QM' ) ) {
			do_action( 'qm/debug', "route" );
		}
		$namespace = 'v1';
		$base = 'route';
		register_rest_route( $namespace, '/' . $base . '/orders', array(
		  array(
			'methods'             => "GET",
			'callback'            => array( $this, 'get_orders' ),
			'permission_callback' => array( $this, 'get_orders_permissions_check' ),
			'args'                => array(
				'last_order_id' => array(
					'default' => 0,
					'validate_callback' => function($param, $request, $key) {
						return is_numeric($param);
					}
				),
			)
		  ),
		) );
		// i will send the last card id as args
		// register_rest_route( $namespace, '/' . $base . '/abandoned-carts', array(
		// 	array(
		// 	  'methods'             => "GET",
		// 	  'callback'            => array( $this, 'get_abandoned_carts' ),
		// 	  'permission_callback' => array( $this, 'get_abandoned_carts_permissions_check' ),
		// 	  'args'                => array(
		// 		'last_cart_id' => array(
		// 			'default' => 0,
		// 			'validate_callback' => function($param, $request, $key) {
		// 				return is_numeric($param);
		// 			}
		// 		),
		// 	)),
		//   ));
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.2.0
	 */

	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bharatx_Pay_In_3_Feature_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bharatx_Pay_In_3_Feature_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/bharatx-pay-in-3-feature-plugin-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.2.0
	 */
	public function enqueue_scripts()
	{
		$partner_id = $this->settings['merchant_partner_id'];
		$custom_script_url = 'https://websdk-assets.s3.ap-south-1.amazonaws.com/shopify-messaging-app/' . $partner_id . '.js';

		wp_enqueue_script('featherlight', plugin_dir_url(__FILE__) . 'js/featherlight.js', array('jquery'), $this->version, false);
		wp_enqueue_script('autoselect_bharatx', plugin_dir_url(__FILE__) . 'js/autoselect.js', array('jquery'), $this->version, false);
		if($partner_id){wp_enqueue_script('custom_bharatx', $custom_script_url, array('jquery'), $this->version, false);}
	}

	/**
	 * Remove payment gateway based on country.
	 *
	 * @since    1.2.0
	 * @param      array $available_gateways       Available Payment Gateways.
	 * @return     array $available_gateways       Available Payment Gateways.
	 */
	public function remove_gateway_based_on_billing_country($available_gateways)
	{
		if (is_admin()) {
			return $available_gateways;
		}
		if (!WC()->customer) {
			return $available_gateways;
		}
		$country_code = WC()->customer->get_billing_country();
		if ($country_code) {
			if (!in_array($country_code, $this->supported_countries, true)) {
				unset($available_gateways['bharatx-pay-in-3-feature-plugin']);
				unset($this->strings['price_string']);
				unset($this->strings['price_string_for_checkout']);
			}
		}
		return $available_gateways;
	}

	function get_order_details ($order){ 
		return array(
			'id' => $order->get_id(),
			'parent_id' => $order->get_parent_id(),
			'number' => $order->get_order_number(),
			'order_key' => $order->get_order_key(),
			'created_via' => $order->get_created_via(),
			'version' => $order->get_version(),
			'status' => $order->get_status(),
			'currency' => $order->get_currency(),
			'date_created' => $order->get_date_created()->date('Y-m-d H:i:s'),
			'date_created_gmt' => $order->get_date_created()->date('Y-m-d H:i:s', true),
			'date_modified' => $order->get_date_modified()->date('Y-m-d H:i:s'),
			'date_modified_gmt' => $order->get_date_modified()->date('Y-m-d H:i:s', true),
			'discount_total' => $order->get_discount_total(),
			'discount_tax' => $order->get_discount_tax(),
			'shipping_total' => $order->get_shipping_total(),
			'shipping_tax' => $order->get_shipping_tax(),
			'cart_tax' => $order->get_cart_tax(),
			'total' => $order->get_total(),
			'total_tax' => $order->get_total_tax(),
			'prices_include_tax' => $order->get_prices_include_tax(),
			'customer_id' => $order->get_customer_id(),
			'customer_ip_address' => $order->get_customer_ip_address(),
			'customer_user_agent' => $order->get_customer_user_agent(),
			'customer_note' => $order->get_customer_note(),
			'billing' => $order->get_address('billing'),
			'shipping' => $order->get_address('shipping'),
			'payment_method' => $order->get_payment_method(),
			'payment_method_title' => $order->get_payment_method_title(),
			'transaction_id' => $order->get_transaction_id(),
			'date_paid' => $order->get_date_paid() ? $order->get_date_paid()->date('Y-m-d H:i:s') : null,
			'date_paid_gmt' => $order->get_date_paid() ? $order->get_date_paid()->date('Y-m-d H:i:s', true) : null,
			'date_completed' => $order->get_date_completed() ? $order->get_date_completed()->date('Y-m-d H:i:s') : null,
			'date_completed_gmt' => $order->get_date_completed() ? $order->get_date_completed()->date('Y-m-d H:i:s', true) : null,
			'cart_hash' => $order->get_cart_hash(),
			'meta_data' => $order->get_meta_data(),
			'line_items' => array_map(function($item) {
				return [
					'id' => $item->get_id(),
					'name' => $item->get_name(),
					'product_id' => $item->get_product_id(),
					'variation_id' => $item->get_variation_id(),
					'quantity' => $item->get_quantity(),
					'tax_class' => $item->get_tax_class(),
					'subtotal' => $item->get_subtotal(),
					'subtotal_tax' => $item->get_subtotal_tax(),
					'total' => $item->get_total(),
					'total_tax' => $item->get_total_tax(),
					'taxes' => $item->get_taxes(),
					'meta_data' => $item->get_meta_data(),
					'sku' => $item->get_product()->get_sku(),
					'price' => $item->get_total() / $item->get_quantity()
				];
			}, $order->get_items()),
			'tax_lines' => array_map(function($item) {
				return [
					'id' => $item->get_id(),
					'rate_code' => $item->get_rate_code(),
					'rate_id' => $item->get_rate_id(),
					'label' => $item->get_label(),
					'compound' => $item->is_compound(),
					'tax_total' => $item->get_tax_total(),
					'shipping_tax_total' => $item->get_shipping_tax_total(),
					'meta_data' => $item->get_meta_data()
				];
			}, $order->get_items('tax')),
			'shipping_lines' => array_map(function($item) {
				return [
					'id' => $item->get_id(),
					'method_title' => $item->get_method_title(),
					'method_id' => $item->get_method_id(),
					'total' => $item->get_total(),
					'total_tax' => $item->get_total_tax(),
					'taxes' => $item->get_taxes(),
					'meta_data' => $item->get_meta_data()
				];
			}, $order->get_items('shipping')),
			'fee_lines' => array_map(function($item) {
				return [
					'id' => $item->get_id(),
					'name' => $item->get_name(),
					'tax_class' => $item->get_tax_class(),
					'tax_status' => $item->get_tax_status(),
					'total' => $item->get_total(),
					'total_tax' => $item->get_total_tax(),
					'taxes' => $item->get_taxes(),
					'meta_data' => $item->get_meta_data()
				];
			}, $order->get_items('fee')),
		
			'coupon_lines' => array_map(function($item) {
				return [
					'id' => $item->get_id(),
					'code' => $item->get_code(),
					'discount' => $item->get_discount(),
					'discount_tax' => $item->get_discount_tax(),
					'meta_data' => $item->get_meta_data()
				];
			}, $order->get_items('coupon')),		

		);
	}

	function add_custom_url_setting($settings) {
		$settings[] = array(
			'title'    => __('Custom URL', 'woocommerce'), // Title of the field.
			'desc'     => __('Enter the custom URL.', 'woocommerce'), // Description.
			'id'       => 'wc_order_update_url', // Unique ID for the setting.
			'type'     => 'text', // Type of the setting (text, checkbox, etc.)
			'default'  => 'https://webhook.site/a80c49af-4f6d-4ee5-b785-5fde355bb8e7', // Default value.
			'desc_tip' => true, // Optional tooltip description field.
			'autoload' => true, // Whether to load the value on the Options page.
		);
		if ( class_exists( 'QM' ) ) {
			do_action( 'qm/debug', $settings);
		}

		return $settings; // Return the new setting.

	}
	function custom_order_status_changed_function($order_id, $old_status, $new_status) {
		if (empty($order_id)) {
			return;
		}

		$api_url = get_option('wc_order_update_url', 'https://webhook.site/a80c49af-4f6d-4ee5-b785-5fde355bb8e7');
		if (empty($api_url)) {
			error_log('WC Order Update URL is not set.');
		}
		// get the order and extract the data
		$order = wc_get_order($order_id);


		$response = wp_remote_post($api_url, array(
			'method' => 'POST',
			'headers' => array(
				'Content-Type' => 'application/json',
				// 'Authorization' => 'Bearer YOUR_API_KEY_HERE',  // Uncomment and set API key if required.
			),
			'body' => json_encode($this->get_order_details($order))
		));

		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			error_log("Error sending order update for Order ID {$order_id}: {$error_message}");
		} else {
		}
	}

	public function get_orders_permissions_check( $request ) {
		$received_key = $request->get_header('x-admin-key');
		$stored_key = 'test';  // Replace with your actual admin key
	
		if ( $received_key === $stored_key ) {
			return true;
		}
		
		return false;
	}
	
	function get_orders($request) {

		global $wpdb;
	
		$last_order_id = (int) $request->get_param('last_order_id');
	
		// Get post IDs greater than $last_cart_id
		$query = "
			SELECT ID FROM $wpdb->posts 
			WHERE post_type = 'shop_order' 
			AND (post_status = 'wc-completed' OR post_status = 'wc-processing' OR post_status = 'wc-pending' )
			AND ID > %d
		";
		$post_ids = $wpdb->get_col($wpdb->prepare($query, $last_order_id));
	
		// // If no matching post IDs, return an empty array
		if (empty($post_ids)) {
			return array();
		}

		$args = array(
			'post_type' => 'shop_order',
			'post_status' => array('wc-completed', 'wc-processing', 'wc-pending'),
			'post__in' => $post_ids,
			'orderby' => 'ID',
			'order' => 'ASC'
		);

		$orders = get_posts($args);
		
		$order_data = $this->extract_order_data($orders);
		return $order_data;
	}
	private function extract_order_data($orders) {
		$order_data = [];
	
		foreach ($orders as $order_post) {
			$order = wc_get_order($order_post);
			$order_data[] = $this->get_order_details($order);
		}
		return $order_data;
	}	


	public function remove_gateway_based_on_billing_total($available_gateways)
	{
		if (!WC()->cart) {
			return $available_gateways;
		}

		$total = WC()->cart->get_total('edit');
		$totals = intval($total);
		if ($totals >= $this->max_limit) {
			if (isset($available_gateways['bharatx-pay-in-3-feature-plugin'])) {
				unset($available_gateways['bharatx-pay-in-3-feature-plugin']);
				unset($this->strings['price_string']);
				unset($this->strings['price_string_for_checkout']);
			}
		}
		return $available_gateways;
	}


	/**
	 * @snippet       Disable Payment Method for Specific Category
	 */
	public function remove_gateway_based_on_category_id($available_gateways)
	{
		if (!WC()->cart) {
			return $available_gateways;
		}

		$cart_product_id = array();
		foreach (WC()->cart->get_cart() as $cart_item) {
			$product_id = $cart_item['product_id'];
			array_push($cart_product_id, $product_id);
		}

		$category_ids = $this->settings['category_ids'];
		$str		  = preg_split("/\,/", $category_ids);
		$result 	  = array_diff($str, $cart_product_id);

		if (count($result) < count($str)) {
			if (isset($available_gateways['bharatx-pay-in-3-feature-plugin'])) {
				unset($available_gateways['bharatx-pay-in-3-feature-plugin']);
				unset($this->strings['price_string']);
				unset($this->strings['price_string_for_checkout']);
			}
		}
		return $available_gateways;
	}





	/**
	 * Display bharatx text on single product.
	 *
	 * @since    1.2.0
	 */
	public function bharatx_price_text()
	{
		global $product;
		if ('simple' === $product->get_type()) {
			$category_ids = $this->settings['category_ids'];
			$str		  = preg_split("/\,/", $category_ids);
			$price = $product->get_price();
			$actual_price = intval($price);
			$id = $product->get_id();
			if ($actual_price <= $this->max_limit && !in_array($id, $str)) {
				echo wp_kses_post($this->get_bharatx_price_text($price, 'product'));
			}
		}
	}

	public function variation_price_text()
	{
		global $product;
		if ('variable' === $product->get_type()) {
				echo '<div class="bharatx-price-variation-default-text"></div>';
?>
				<script type="text/javascript">
					jQuery(document).ready(function($) {
						if ($(".single_variation_wrap").length > 0) {
							$(".single_variation_wrap").on("show_variation", function(event, variation) {
								if ($('.bharatx-price-variation-default-text').length > 0) {
									$('.bharatx-price-variation-default-text').html(variation.bharatx_price_text);
								}
							});

							$(".single_variation_wrap").on("hide_variation", function(event, variation) {
								if ($('.bharatx-price-variation-default-text').length > 0) {
									$('.bharatx-price-variation-default-text').html("");
								}
							});
						}
					});
				</script>
		<?php
		}
	}

	/**
	 *
	 * @since    1.2.0
	 * @param array  $value Value.
	 * @param Object $product Product.
	 * @param Object $variation Variation.
	 * @return array $value Value.
	 */
	public function bharatx_price_text_variation($value, $product = null, $variation = null)
	{
		if (null != $variation) {
			$price                = $variation->get_price();
			$value['bharatx_price_text'] = $this->get_bharatx_price_text($price, 'product');
		}
		return $value;
	}

	/**
	 * Display BharatX text on Checkout Page.
	 *
	 * @since    1.2.0
	 */
	public function bharatx_price_text_checkout($value)
	{
		$total = WC()->cart->get_total('edit');
		$totals = intval($total);
		if ($totals < $this->max_limit) {
			$value .= wp_kses_post($this->get_bharatx_price_text($total, 'checkout'));
		}

		return $value;
	}

	/**
	 * Display Bharatx text on Cart Page.
	 *
	 * @since    1.2.0
	 */
	public function after_cart_totals()
	{
		$total = WC()->cart->get_total('edit');
		$totals = intval($total);
		?>
		<tr class="bharatx-cart-text">
			<td colspan="2">
				<?php
				if ($totals < $this->max_limit) {
					echo wp_kses_post($this->get_bharatx_price_text($total, 'cart'));
				}
				?>
			</td>
		</tr>
	<?php
	}

	/**
	 * Get bharatx text.
	 *
	 * @since    1.2.0
	 * @param float  $price Price.
	 * @param string $page Page Name.
	 */
	public function get_bharatx_price_text($price, $page, $type = 'simple')
	{
		if ($price > $this->max_limit) {
			return "";
		}

		$featherlight    = '';
		$amount_in_paise =  round($price, 2);
		$div             = (float) ($amount_in_paise / 3);
		$amount_in_rs    = round($div, 2);
		$part            = wc_price($amount_in_rs);
		$args            = array(
			'decimals' => 2,
		);
		$part            = wc_price($amount_in_rs, $args);

		$pdp_image_url = 'https://d30flbpbaljuso.cloudfront.net/img/partner/logo/light/' .  esc_html($this->settings['merchant_partner_id']);
		if (isset($this->settings["pdp_popup_logo"]) && $this->settings["pdp_popup_logo"] && "" != $this->settings["pdp_popup_logo"]) {
			$pdp_image_url = $this->settings["pdp_popup_logo"];
		}

		$image           = '<img class="bharatx-brand-logo" src="' . $pdp_image_url . '"/>';
		$info_icon       = '<img src="' . esc_html(plugin_dir_url(__FILE__) . 'images/info.svg') . '"/>';

		$featherlight = 'data-featherlight="' . 'https://d30flbpbaljuso.cloudfront.net/img/partner/woocommerce/popups/' . $this->settings['merchant_partner_id'] .  '.png' . '"';
		$interstitial_options = ' data-page="' . $page . '" ';

		if (is_singular('product')) {
			$object                = get_queried_object();
			$product_id            = $object->ID;
			$interstitial_options .= ' data-product-id="' . $product_id . '"';
		} else {
			$interstitial_options .= ' data-product-id=""';
		}

		ob_start();

		if ($type == 'simple') {
			$string = $this->strings['price_string'];
			if ($page == 'checkout') {
				$string = $this->strings['price_string_for_checkout'];
			}
		} else {
			$string = $this->strings['varying_product_payment_description'];
		}

		$placeholders = array(
			'{{ amount }}'   => $part,
			'{{ logo }}'     => sprintf('<a class="bharatx-popup-link" href="#" %s %s><span class="product-bharatx-logo-text">%s</span></a>', $interstitial_options, $featherlight, $image),
			'{{ info_icon }}' => sprintf('<a class="bharatx-popup-link" href="#" %s %s>%s</a>', $interstitial_options, $featherlight, $info_icon),
		);

		$string = str_replace(array_keys($placeholders), $placeholders, $string);
		?>
		<p class="product-bharatx-text-note"><?php echo wp_kses_post($string); ?></p>
<?php
		return ob_get_clean();
	}

	/**
	 * Return payment gateway title.
	 *
	 * @since    1.2.0
	 * @param string $title Title.
	 * @param string $id Gateway Id.
	 * @return string $title Title.
	 */
	public function checkout_gateway_title($title, $id)
	{
		if (BHARATX_PAY_IN_3_FEATURE_PLUGIN_SLUG === $id) {
			if (isset($this->settings['checkout_page_payment_method_title'])) {
				$title = $this->settings['checkout_page_payment_method_title'];
			} else {
				$title = 'BharatX Pay in 3';
			}
		}
		return $title;
	}

	/**
	 * Return payment gateway icon.
	 *
	 * @since    1.2.0
	 * @param string $icon Icon.
	 * @param string $id Gateway Id.
	 * @return string $icon Icon.
	 */
	public function checkout_gateway_icon($icon, $id)
	{
		if (BHARATX_PAY_IN_3_FEATURE_PLUGIN_SLUG === $id) {
			$partner_id = "testPartnerId";

			if (isset($this->settings['merchant_partner_id'])) {
				$partner_id = $this->settings['merchant_partner_id'];
			}

			$url = "https://d30flbpbaljuso.cloudfront.net/img/partner/logo/light/" . $partner_id;
			$icon = '<img src="' . $url . '" class="bharatx-merchant-checkout-gateway-logo" />';
		}
		return $icon;
	}

	/**
	 * Return payment gateway description.
	 *
	 * @since    1.2.0
	 * @param string $description description.
	 * @param string $id Gateway Id.
	 * @return string $description description.
	 */
	public function checkout_gateway_description($description, $id)
	{
		if (BHARATX_PAY_IN_3_FEATURE_PLUGIN_SLUG === $id) {
			$description = $this->strings['payment_method_description'];
		}
		return $description;
	}
}
