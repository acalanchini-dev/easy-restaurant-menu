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
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles(): void {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run_plugin() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( "easy-restaurant-menu-public-stles", EASY_RESTAURANT_MENU_PLUGIN_URL . 'public/css/easy-restaurant-menu-public.css', array(), EASY_RESTAURANT_MENU_VERSION, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts(): void {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run_plugin() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( "easy-restaurant-menu-public-scripts", EASY_RESTAURANT_MENU_PLUGIN_URL . 'public/js/easy-restaurant-menu-public.js', array( 'jquery' ), EASY_RESTAURANT_MENU_VERSION, false );

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

		//Return html output of the block
		return Easy_Restaurant_Menu_Helper::return_view( $path,  $block_attributes, $content, $block );

	}

}
