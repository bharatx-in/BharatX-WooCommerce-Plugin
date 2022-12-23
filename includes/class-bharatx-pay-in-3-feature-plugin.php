<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.bharatx.tech
 * @since      1.2.0
 *
 * @package    Bharatx_Pay_In_3_Feature_Plugin
 * @subpackage Bharatx_Pay_In_3_Feature_Plugin/includes
 */

/**
 * BharatX API Client Class
 *
 * Utility class to perform BharatX API calls
 * 
 * @since 1.6.1
 */
class Bharatx_Api_Client {
	private  $partnerId;
	private $apiKey;
	private $version = null;

	public function __construct($partnerId, $apiKey, $version = null) {
		$this -> partnerId = $partnerId;
		$this -> apiKey = $apiKey;	

		$this -> version = $version;
	}

	public function get_bharatx_auth_header() {
		$str = $this->partnerId . ":" . $this->apiKey;
		return "Basic " . base64_encode($str);
	}

	private function format_response($response, $version) {
		$response_code = wp_remote_retrieve_response_code($response);
		$response_body = wp_remote_retrieve_body($response);

		if ($response_code >= 200 && $response_code < 400) {
			$response_body = json_decode($response_body);
		}

		return array(
			"status_code" => $response_code,
			"data" => $response_body,
			"version" => $version
		);
	}

	private function get_version() {
		if (!is_null($this->version)) {
			return $this->version;
		}

		$response = $this->get_merchant_configuration();
		if ($response["status_code"] == 200) {
			$this->version = $response["data"]->version;
			return $this->version;
		} 

		return 1;
	}

	public function get_merchant_configuration() {
		$response = wp_remote_get("https://web-v2.bharatx.tech/api/merchant/configuration", array(
			'headers' => array(
				"content-type" => "application/json",
				"authorization" => $this->get_bharatx_auth_header(),
			),
			"timeout" => 60,
		));

		return $this->format_response($response, 2);
	}

	private function generate_auth_signature($payload, $endpoint) {
		$content = $payload . $endpoint . $this->apiKey;
		$shasignature = hash('sha256', $content, true);
		return base64_encode($shasignature);
	}

	private function sanitize_amount($amount) {
		return floor($amount * 100);
	}

	public function refund_transaction($transactionId, $amount = null, $reason = null) {
		$version = $this->get_version();
		if (is_null($amount)) {
			$amount = 0;
		}

		$amount = $this->sanitize_amount($amount);

		$url = "https://web-v2.bharatx.tech/api/merchant/transaction/$transactionId/refund";
		if ($version == 1) {
			$url = "https://web.bharatx.tech/api/refund";
		}

		$body = array();
		if ($version == 1) {
			$body = array(
				"transactionId" => $transactionId,
			);

			if ($amount != 0) {
				$body["amount"] = $amount;
			}
		} else {
			$body = array (
				"refund" => array(
					"amount" => $amount,
				),
			);
		}

		$json_body = json_encode($body);
		$headers = array(
			"content-type" => "application/json",
		);

		if ($version == 1) {
			$headers["x-partner-id"] = $this->partnerId;
			$headers["x-signature"] = $this->generate_auth_signature($json_body, "/api/refund");
		} else {
			$headers["authorization"] = $this->get_bharatx_auth_header();
		}

		$response = wp_remote_post($url, array(
			"headers" => $headers,
			"body" => $json_body,
			"timeout" => 60,
		));

		return $this->format_response($response, $version);
	}

	public function get_transaction($transactionId) {
		$version = $this->get_version();

		$url = "https://web-v2.bharatx.tech/api/merchant/transaction/$transactionId";
		if ($version == 1) {
			$url = "https://web.bharatx.tech/api/transaction/status?id=$transactionId";
		}

		$headers = array();
		if ($version == 1) {
			$headers["x-partner-id"] = $this->partnerId;
		} else {
			$headers["authorization"] = $this->get_bharatx_auth_header();
		}

		$response = wp_remote_get($url, array(
			"headers" => $headers,
			"timeout" => 60,
		));

		return $this->format_response($response, $version);
	}

	public function create_transaction(
		$transactionId,
		$amount,
		$user,
		$notes,
		$redirectUrl,
		$cancelUrl,
		$webhookUrl,
		$logoOverride,
		$colorOverride
	) {
		$version = $this->get_version();
		$amount = $this->sanitize_amount($amount);

		$url = "https://web-v2.bharatx.tech/api/merchant/transaction";
		if ($version == 1) {
			$url = "https://web.bharatx.tech/api/transaction";
		}

		$body = array();
		if ($version == 1) {
			$body = array(
				'id'              => $transactionId,
				'amount' 	      => $amount,
				'user'			  => $user,
				'notes'			  => $notes,
				'redirect'		  => array(
					'url'			 => $redirectUrl,
					'logoOverride'	 => $logoOverride,
					'colorOverride'  => $colorOverride,
				)
			);
		} else {
			$body = array(
				"transaction" => array (
					"id" => $transactionId,
					"amount" => $amount,
					"notes" => $notes,
				),
				"createConfiguration" => array(
					"successRedirectUrl" => $redirectUrl,
					"failureRedirectUrl" => $redirectUrl,
					"cancelRedirectUrl" => $cancelUrl,
					"webhookUrl" => $webhookUrl,
					"logoOverride" => $logoOverride,
					"colorOverride" => $colorOverride,
				),
				"user" => $user,
			);
		}

		$json_body = json_encode($body);
		$headers = array(
			"content-type" => "application/json",
		);

		if ($version == 1) {
			$headers["x-partner-id"] = $this->partnerId;
			$headers["x-signature"] = $this->generate_auth_signature($json_body, "/api/transaction");
		} else {
			$headers["authorization"] = $this->get_bharatx_auth_header();
		}

		$response = wp_remote_post($url, array(
			"headers" => $headers,
			"body" => $json_body,
			"timeout" => 60,
		));

		return $this->format_response($response, $version);
	}
}


/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.2.0
 * @package    Bharatx_Pay_In_3_Feature_Plugin
 * @subpackage Bharatx_Pay_In_3_Feature_Plugin/includes
 * @author     BharatX <Karan@bharatx.tech>
 */

class Bharatx_Pay_In_3_Feature_Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.2.0
	 * @access   protected
	 * @var      Bharatx_Pay_In_3_Feature_Plugin_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.2.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.2.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.2.0
	 */
	public function __construct() {
		if ( defined( 'BHARATX_PAY_IN_3_FEATURE_PLUGIN_VERSION' ) ) {
			$this->version = BHARATX_PAY_IN_3_FEATURE_PLUGIN_VERSION;
		} else {
			$this->version = '1.2.0';
		}
		
		if ( defined( 'BHARATX_PAY_IN_3_FEATURE_PLUGIN_SLUG' ) ) {
			$this->plugin_name = BHARATX_PAY_IN_3_FEATURE_PLUGIN_SLUG;
		} else {
			$this->plugin_name = 'Bharatx-pay-in-3-feature-plugin';
		}

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		add_action( 'plugins_loaded', array( $this, 'init_gateway_class' ) );
		add_filter( 'plugin_action_links_' . BHARATX_PAY_IN_3_FEATURE_PLUGIN_BASENAME, array( $this, 'plugin_page_settings_link' ), 10, 1 );

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Bharatx_Pay_In_3_Feature_Plugin_Loader. Orchestrates the hooks of the plugin.
	 * - Bharatx_Pay_In_3_Feature_Plugin_i18n. Defines internationalization functionality.
	 * - Bharatx_Pay_In_3_Feature_Plugin_Admin. Defines all hooks for the admin area.
	 * - Bharatx_Pay_In_3_Feature_Plugin_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.2.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bharatx-pay-in-3-feature-plugin-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bharatx-pay-in-3-feature-plugin-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bharatx-pay-in-3-feature-plugin-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bharatx-pay-in-3-feature-plugin-public.php';

		$this->loader = new Bharatx_Pay_In_3_Feature_Plugin_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Bharatx_Pay_In_3_Feature_Plugin_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.2.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Bharatx_Pay_In_3_Feature_Plugin_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.2.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Bharatx_Pay_In_3_Feature_Plugin_Admin( $this->get_plugin_name(), $this->get_version() );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.2.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Bharatx_Pay_In_3_Feature_Plugin_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.2.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.2.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.2.0
	 * @return    Bharatx_Pay_In_3_Feature_Plugin_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.2.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Include plugin gateway class file
	 *
	 * @since    1.2.0
	 */
	public function init_gateway_class() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bharatx-pay-in-3-feature-plugin-gateway.php';
	}

	/**
	 * Plugin page settings.
	 *
	 * @since   1.2.0
	 * @param       Array $links  Plugin Settings page link.
	 * @return      Array $links       Plugin Settings page link.
	 */
	public function plugin_page_settings_link( $links ) {

		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=bharatx-pay-in-3-feature-plugin' ) . '" aria-label="' . esc_attr__( 'View settings', 'bharatx-pay-in-3-feature-plugin' ) . '">' . esc_html__( 'Settings', 'bharatx-pay-in-3-feature-plugin' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Get the transactions table name managed by BharatX
	 * 
	 * @since 1.6.0
	 */
	public static function get_transactions_table_name() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'bharatx_pay_in_3_transactions';
		return $table_name;
	}

	/**
	 * Set the transaction id for the given order key
	 * 
	 * @since 1.6.0
	 */
	public static function set_bharatx_transaction_id_for_order($orderKey, $transactionId) {
		global $wpdb;

		$table_name = Bharatx_Pay_In_3_Feature_Plugin::get_transactions_table_name();
		$wpdb -> query("
			INSERT INTO $table_name (orderKey, bharatxTransactionId) VALUES('$orderKey', '$transactionId') 
			ON DUPLICATE KEY UPDATE  bharatxTransactionId = '$transactionId'
		");
	}

	/**
	 * Get the transaction id for the given order key
	 * 
	 * @since 1.6.0
	 */
	public static function get_bharatx_transaction_id_for_order($orderKey) {
		global $wpdb;

		$table_name = Bharatx_Pay_In_3_Feature_Plugin::get_transactions_table_name();
		$transactionId = $wpdb->get_var( "
			SELECT 
				bharatxTransactionId
			FROM  $table_name
			where orderKey = '$orderKey' ;
		");

		return $transactionId;
	}

	public static function get_bharatx_merchant_configuration() {

	}
}
