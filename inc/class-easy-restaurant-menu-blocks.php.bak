<?php
/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/inc
 */

namespace EASY_RESTAURANT_MENU;

use WP_Block;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Easy_Restaurant_Menu_Blocks {

	/**
	 * Loader function for blocks
	 *
	 * @since    1.0.0
	 */
	public function load_easy_restaurant_menu_blocks(): void {

		add_action( 'init', [ $this, 'register_easy_restaurant_menu_blocks' ] );

	}

	/**
	 * Register Block Types
	 *
	 * @since    1.0.0
	 */
	public function register_easy_restaurant_menu_blocks(): void {

		//First Block (or whatever your block name is)
		register_block_type(
			EASY_RESTAURANT_MENU_PLUGIN_PATH . '/build/first-block'/*,
			[
				//Callback function for your block (optional, use this callback if you want to make server side rendering)
				'render_callback' => [ $this, 'first_block_render_callback' ]
			]*/
		);
		
		// Restaurant Menu Block
		register_block_type(
			EASY_RESTAURANT_MENU_PLUGIN_PATH . '/build/restaurant-menu',
			[
				// Callback per il rendering lato server
				'render_callback' => [ $this, 'restaurant_menu_render_callback' ]
			]
		);

	}

	/**
	 * Callback function for the first block (or whatever your block name is)
	 *
	 * @param array $block_attributes Get block content
	 * @param string $content Get block content
	 * @param WP_Block $block Get block instance
	 *
	 * @return string First block template html
	 * @since    1.0.0
	 */
	public function first_block_render_callback( array $block_attributes, string $content, WP_Block $block ): string {

		Easy_Restaurant_Menu_Helper::using( 'public/class-easy-restaurant-menu-public.php' );

		$public = new Easy_Restaurant_Menu_Public();

		return $public->get_rendered_block( 'public/partials/first-block-render.php', $block_attributes, $content, $block );

	}

	/**
	 * Callback function per il blocco Restaurant Menu
	 *
	 * @param array $block_attributes Get block content
	 * @param string $content Get block content
	 * @param WP_Block $block Get block instance
	 *
	 * @return string Restaurant Menu block template html
	 * @since    1.0.0
	 */
	public function restaurant_menu_render_callback( array $block_attributes, string $content, WP_Block $block ): string {
		// Utilizzo direttamente il file render.php dalla directory build
		$file_path = EASY_RESTAURANT_MENU_PLUGIN_PATH . 'build/restaurant-menu/render.php';
		
		if (file_exists($file_path)) {
			try {
				// Includi il file in un contesto isolato per evitare variabili globali
				$attributes = $block_attributes; // Passaggio degli attributi al file render.php
				ob_start();
				include $file_path;
				$output = ob_get_clean();
				
				// Se output è vuoto, mostra un messaggio di errore
				if (empty($output)) {
					// Logga l'errore se la funzione è disponibile
					if (function_exists('error_log')) {
						error_log('Easy Restaurant Menu: Rendering del menu fallito - output vuoto');
					}
					return '<p>' . __('Errore nel rendering del menu. Per favore controlla che le tabelle del database esistano.', 'easy-restaurant-menu') . '</p>';
				}
				
				return $output;
			} catch (\Throwable $e) {
				// Cattura qualsiasi eccezione o errore e registralo
				if (function_exists('error_log')) {
					error_log('Easy Restaurant Menu: Errore nel rendering del menu - ' . $e->getMessage());
				}
				return '<p>' . __('Si è verificato un errore durante il rendering del menu.', 'easy-restaurant-menu') . '</p>';
			}
		}
		
		// Fallback se il file non esiste
		if (function_exists('error_log')) {
			error_log('Easy Restaurant Menu: File di rendering non trovato in: ' . $file_path);
		}
		return '<p>' . __('File di rendering non trovato.', 'easy-restaurant-menu') . '</p>';
	}

}
