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

}
