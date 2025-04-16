<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/inc
 */

namespace EASY_RESTAURANT_MENU;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Easy_Restaurant_Menu_Deactivator {

	/**
	 * This is plugin deactivator.
	 *
	 * Includes all code necessary to run during the plugin's deactivation
	 *
	 * @since    1.0.0
	 */
	public static function deactivate(): void {
		global $wpdb;
		
		// Elimina le tabelle se l'opzione di rimozione completa è attivata
		$remove_tables = \get_option('erm_remove_data_on_uninstall', false);
		
		if ($remove_tables) {
			$table_sections = $wpdb->prefix . 'erm_sections';
			$table_items = $wpdb->prefix . 'erm_items';
			
			$wpdb->query("DROP TABLE IF EXISTS $table_items");
			$wpdb->query("DROP TABLE IF EXISTS $table_sections");
		}
	}
} 