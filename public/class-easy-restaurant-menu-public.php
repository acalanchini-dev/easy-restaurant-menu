<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/public
 */

namespace EASY_RESTAURANT_MENU;

use WP_Block;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Easy_Restaurant_Menu_Public {

	/**
	 * Registra ma non carica immediatamente gli stili per il frontend
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles(): void {
		// Non facciamo nulla qui, tutti gli stili vengono gestiti dalla classe Assets
		// Per compatibilità con vecchie implementazioni, possiamo registrare lo stile ma non caricarlo
		wp_register_style(
			"easy-restaurant-menu-public", 
			EASY_RESTAURANT_MENU_PLUGIN_URL . 'public/css/easy-restaurant-menu-public.css', 
			array(), 
			EASY_RESTAURANT_MENU_VERSION, 
			'all'
		);
	}

	/**
	 * Registra ma non carica immediatamente gli script per il frontend
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts(): void {
		// Non facciamo nulla qui, tutti gli script vengono gestiti dalla classe Assets
		// Per compatibilità con vecchie implementazioni, possiamo registrare lo script ma non caricarlo
		wp_register_script(
			"easy-restaurant-menu-public", 
			EASY_RESTAURANT_MENU_PLUGIN_URL . 'public/js/easy-restaurant-menu-public.js', 
			array( 'jquery' ), 
			EASY_RESTAURANT_MENU_VERSION, 
			true
		);
	}

	/**
	 * Html render for a block
	 *
	 * @param string $path Path for view page
	 * @param array $block_attributes Get block content
	 * @param string $content Get block content
	 * @param WP_Block|null $block Get block instance
	 *
	 * @return string Html output of a block
	 * @since    1.0.0
	 */
	public function get_rendered_block(string $path, array $block_attributes = [], string $content = '', WP_Block $block = null) : string  {
		// Assicuriamoci che la classe Assets sia caricata
		if (!class_exists('EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Assets')) {
			Easy_Restaurant_Menu_Helper::using('inc/class-easy-restaurant-menu-assets.php');
		}
		
		// Notifica alla classe Assets che stiamo renderizzando un contenuto che richiede gli asset
		Easy_Restaurant_Menu_Assets::set_has_menu_content($block_attributes);

		//Return html output of the block
		return Easy_Restaurant_Menu_Helper::return_view( $path, $block_attributes, $content, $block );
	}

}
