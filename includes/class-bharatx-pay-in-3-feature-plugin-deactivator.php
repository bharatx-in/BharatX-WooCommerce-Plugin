<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.bharatx.tech
 * @since      1.2.0
 *
 * @package    Bharatx_Pay_In_3_Feature_Plugin
 * @subpackage Bharatx_Pay_In_3_Feature_Plugin/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.2.0
 * @package    Bharatx_Pay_In_3_Feature_Plugin
 * @subpackage Bharatx_Pay_In_3_Feature_Plugin/includes
 * @author     BharatX <Karan@bharatx.tech>
 */
class Bharatx_Pay_In_3_Feature_Plugin_Deactivator {

	/**
	 * Nothing to do
	 *
	 * @since    1.2.0
	 */
	public static function deactivate() {

	 }

	/**
	 * Removes database data for BharatX Pay In 3
	 *
	 * @since    1.6.0
	 */
	public static function uninstall() {
		require_once plugin_dir_path( __FILE__ ) . 'class-bharatx-pay-in-3-feature-plugin.php';
		$tableName = Bharatx_Pay_In_3_Feature_Plugin::get_transactions_table_name();

		global $wpdb;

		$wpdb->query("
			drop table if exists $tableName;
		");
	}
}
