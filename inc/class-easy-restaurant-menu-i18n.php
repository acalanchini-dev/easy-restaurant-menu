<?php

namespace EASY_RESTAURANT_MENU;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/inc
 */
class Easy_Restaurant_Menu_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_easy_restaurant_menu_textdomain(): void {

		load_plugin_textdomain(
			"easy-restaurant-menu",
			false,
			EASY_RESTAURANT_MENU_PLUGIN_PATH . 'languages/'
		);

	}

}
