<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.bharatx.tech
 * @since             1.2.0
 * @package           Bharatx_Pay_In_3_Feature_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       BharatX Pay In 3 
 * Plugin URI:        https://wordpress.org/plugins/bharatx-pay-in-3/
 * Description:       Split orders into 3 interest-free payments
 * Version:           1.5.4
 * Author:            BharatX
 * Author URI:        https://www.bharatx.tech
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bharatx-pay-in-3-feature-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.2.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BHARATX_PAY_IN_3_FEATURE_PLUGIN_VERSION', '1.5.4' );

/**
 * Currently plugin slug.
 */
define( 'BHARATX_PAY_IN_3_FEATURE_PLUGIN_SLUG', 'bharatx-pay-in-3-feature-plugin' );

/**
 * Currently plugin file.
 */
define( 'BHARATX_PAY_IN_3_FEATURE_PLUGIN_FILE', __FILE__ );

/**
 * Currently plugin basename.
 */
define( 'BHARATX_PAY_IN_3_FEATURE_PLUGIN_BASENAME', plugin_basename( BHARATX_PAY_IN_3_FEATURE_PLUGIN_FILE ) );

/**
 * Currently plugin dir.
 */
define( 'BHARATX_PAY_IN_3_FEATURE_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bharatx pay in 3 feature plugin-activator.php
 */
function activate_bharatx_pay_in_3_feature_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bharatx-pay-in-3-feature-plugin-activator.php';
	Bharatx_Pay_In_3_Feature_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bharatx pay in 3 feature plugin-deactivator.php
 */
function deactivate_bharatx_pay_in_3_feature_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bharatx-pay-in-3-feature-plugin-deactivator.php';
	Bharatx_Pay_In_3_Feature_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bharatx_pay_in_3_feature_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_bharatx_pay_in_3_feature_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bharatx-pay-in-3-feature-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.2.0
 */
function run_bharatx_pay_in_3_feature_plugin() {

	$plugin = new Bharatx_Pay_In_3_Feature_Plugin();
	$plugin->run();

}

/**
* Check if WooCommerce is active
*/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	run_bharatx_pay_in_3_feature_plugin();
} else {
	add_action( 'admin_notices', 'bharatx_pay_in_3_feature_plugin_installed_notice' );
}

/**
 * Display Woocommerce Activation notice.
 */
function bharatx_pay_in_3_feature_plugin_installed_notice() {     ?>
	<div class="error">
	  <p><?php echo esc_html__( 'BharatX Pay-in-3 feature requires WooCommerce Plugin. Please install or activate WooCommerce', 'bharatx-pay-in-3-feature-plugin' ); ?></p>
	</div>
	<?php
}