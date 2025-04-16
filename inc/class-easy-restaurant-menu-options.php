<?php
/**
 * Class for plugin settings.
 *
 * This is used to register settings, create an options page at admin dashboard.
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/inc
 */

namespace EASY_RESTAURANT_MENU;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Easy_Restaurant_Menu_Options {

	/**
	 * Load options page
	 *
	 * @since    1.0.0
	 */
	public function load_easy_restaurant_menu_options(): void {
		// L'opzione del menu è già registrata nella classe Easy_Restaurant_Menu_Admin
		// Quindi qui registriamo solo le impostazioni
		add_action( 'admin_init', [ $this, 'easy_restaurant_menu_register_settings' ] );
	}

	/**
	 * Register settings for the options page
	 *
	 * @since    1.0.0
	 */
	public function easy_restaurant_menu_register_settings(): void {
		// Registrazione delle impostazioni per la rimozione dei dati
		register_setting(
			'erm_settings_group',
			'erm_remove_data_on_uninstall'
		);
		
		// Registrazione delle impostazioni per la valuta
		register_setting(
			'erm_settings_group',
			'erm_currency_symbol'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_currency_position' 
		);
		
		// Registrazione delle impostazioni per il layout
		register_setting(
			'erm_settings_group',
			'erm_default_layout'
		);
	}

	/**
	 * Get html output of the options page
	 *
	 * @since    1.0.0
	 */
	public function easy_restaurant_menu_settings_page_html(): void {
		// Se questa funzione viene chiamata direttamente da un hook di WordPress
		// crea una nuova istanza della classe Admin e mostra la pagina delle opzioni
		if (!class_exists('EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Admin')) {
			Easy_Restaurant_Menu_Helper::using('admin/class-easy-restaurant-menu-admin.php');
		}
		
		$admin = new Easy_Restaurant_Menu_Admin();
		$admin->get_options_page();
	}
}

