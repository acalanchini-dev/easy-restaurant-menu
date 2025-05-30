<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/inc
 */

namespace EASY_RESTAURANT_MENU;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Easy_Restaurant_Menu_Core extends Easy_Restaurant_Menu_Loader {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( ) {

		/** Run the constructor of parent class (Loader) **/
		parent::__construct();

		//Include required files (required)
		$this->load_required_dependencies();

		//Load block types (required)
		$this->set_block_types();
		
		//Load internationalization functionality (required)
		$this->set_locale();
		
		//Load admin options functionality (optional)
		$this->set_options();

		//Defines all hooks for the admin area (optional)
		$this->define_admin_hooks();

		//Defines all hooks for the public area (optional)
		$this->define_public_hooks();
		
		//Defines REST API endpoints (optional)
		$this->define_rest_api();
		
		//Load caching funzionality
		$this->load_caching();
		
		//Load privacy functionality
		$this->load_privacy();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_required_dependencies(): void {

		/**
		 * The class responsible for registering block types
		 * side of the site.
		 */
		Easy_Restaurant_Menu_Helper::using('inc/class-easy-restaurant-menu-blocks.php');
		
		/**
		 * The class responsible for defining internationalization functionality
		 */
		Easy_Restaurant_Menu_Helper::using('inc/class-easy-restaurant-menu-i18n.php');
		
		/**
		 * La classe responsabile della gestione ottimizzata degli asset
		 */
		Easy_Restaurant_Menu_Helper::using('inc/class-easy-restaurant-menu-assets.php');
		
		/**
		 * The class responsible for privacy functionality
		 */
		Easy_Restaurant_Menu_Helper::using('inc/class-easy-restaurant-menu-privacy.php');

	}

	/**
	 * Get admin dashboard options page
	 *
	 * Creates a menu item in admin dashboard and prints an options page
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_options(): void {

		/**
		 * The class responsible for admin options.
		 */
		Easy_Restaurant_Menu_Helper::using('inc/class-easy-restaurant-menu-options.php');

		$plugin_options = new Easy_Restaurant_Menu_Options();

		$this->add_action( 'plugins_loaded', $plugin_options, 'load_easy_restaurant_menu_options' );
		
		// Registra gli handler AJAX per le opzioni
		$this->add_action( 'admin_init', $plugin_options, 'register_ajax_handlers' );

	}

	/**
	 * Get block types
	 *
	 * Block types registered at class \EASY_RESTAURANT_MENU\Blocks
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_block_types(): void {

		$plugin_blocks= new Easy_Restaurant_Menu_Blocks();

		$this->add_action( 'plugins_loaded', $plugin_blocks, 'load_easy_restaurant_menu_blocks' );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Easy_Restaurant_Menu_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale(): void {

		/**
		 * The class responsible for defining internationalization functionality
		 */

		$plugin_i18n = new Easy_Restaurant_Menu_I18n();

		$this->add_action( 'plugins_loaded', $plugin_i18n, 'load_easy_restaurant_menu_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks(): void {

		/**
		 * The class responsible for defining all actions that occur in the admin area and block editor
		 * Editor styles for only common css rules of blocks.
		 */
		Easy_Restaurant_Menu_Helper::using('admin/class-easy-restaurant-menu-admin.php');

		$plugin_admin = new Easy_Restaurant_Menu_Admin();

		$this->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->add_action( 'init', $plugin_admin, 'editor_styles' );
		$this->add_action( 'pre_get_posts', $plugin_admin, 'editor_styles' );
		$this->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		// Aggiungi l'azione per registrare il menu di amministrazione
		$this->add_action( 'admin_menu', $plugin_admin, 'register_admin_menu' );
		
		// Registra gli handler AJAX
		$this->add_action( 'admin_init', $plugin_admin, 'register_ajax_handlers' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks(): void {

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 */
		Easy_Restaurant_Menu_Helper::using('public/class-easy-restaurant-menu-public.php');
		$plugin_public = new Easy_Restaurant_Menu_Public();

		// Registra ma non carica gli stili e gli script pubblici
		$this->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		// Inizializza la gestione degli asset ottimizzata
		Easy_Restaurant_Menu_Assets::init();

	}

	/**
	 * Register all of the REST API endpoints for this plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_rest_api(): void {
		/**
		 * The class responsible for defining REST API endpoints
		 */
		Easy_Restaurant_Menu_Helper::using('inc/class-easy-restaurant-menu-rest.php');
		
		$plugin_rest = new Easy_Restaurant_Menu_REST();
		
		$this->add_action( 'init', $plugin_rest, 'initialize' );
	}
	
	/**
	 * Initialize caching functionality
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function load_caching(): void {
		/**
		 * The class responsible for caching
		 */
		Easy_Restaurant_Menu_Helper::using('inc/class-easy-restaurant-menu-cache.php');
		
		// Non è necessario creare un'istanza poiché la classe Cache utilizza metodi statici
		// Ma registriamo hook per invalidare la cache quando dati rilevanti vengono modificati
		
		// Svuota la cache quando vengono aggiornate le opzioni del plugin
		$this->add_action('update_option_erm_currency_symbol', $this, 'flush_display_cache');
		$this->add_action('update_option_erm_currency_position', $this, 'flush_display_cache');
		$this->add_action('update_option_erm_price_decimal_separator', $this, 'flush_display_cache');
		$this->add_action('update_option_erm_price_thousand_separator', $this, 'flush_display_cache');
		$this->add_action('update_option_erm_price_decimals', $this, 'flush_display_cache');
		$this->add_action('update_option_erm_price_format_template', $this, 'flush_display_cache');
		$this->add_action('update_option_erm_style_preset', $this, 'flush_display_cache');
		
		// Svuota le relative cache quando vengono modificati i dati
		$this->add_action('wp_ajax_erm_save_menu', $this, 'flush_menus_cache');
		$this->add_action('wp_ajax_erm_delete_menu', $this, 'flush_menus_cache');
		$this->add_action('wp_ajax_erm_save_section', $this, 'flush_sections_cache');
		$this->add_action('wp_ajax_erm_delete_section', $this, 'flush_sections_cache');
		$this->add_action('wp_ajax_erm_save_item', $this, 'flush_items_cache');
		$this->add_action('wp_ajax_erm_delete_item', $this, 'flush_items_cache');
		$this->add_action('wp_ajax_erm_update_order', $this, 'flush_display_cache');
	}
	
	/**
	 * Svuota la cache dei menu
	 *
	 * @since    1.1.0
	 */
	public function flush_menus_cache(): void {
		if (class_exists('EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Cache')) {
			Easy_Restaurant_Menu_Cache::delete_group('menus');
			Easy_Restaurant_Menu_Cache::delete_group('complete_menu');
			$this->flush_display_cache();
		}
	}
	
	/**
	 * Svuota la cache delle sezioni
	 *
	 * @since    1.1.0
	 */
	public function flush_sections_cache(): void {
		if (class_exists('EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Cache')) {
			Easy_Restaurant_Menu_Cache::delete_group('sections');
			Easy_Restaurant_Menu_Cache::delete_group('complete_menu');
			$this->flush_display_cache();
		}
	}
	
	/**
	 * Svuota la cache degli elementi
	 *
	 * @since    1.1.0
	 */
	public function flush_items_cache(): void {
		if (class_exists('EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Cache')) {
			Easy_Restaurant_Menu_Cache::delete_group('items');
			Easy_Restaurant_Menu_Cache::delete_group('complete_menu');
			$this->flush_display_cache();
		}
	}
	
	/**
	 * Svuota la cache di visualizzazione (frontend)
	 *
	 * @since    1.1.0
	 */
	public function flush_display_cache(): void {
		if (class_exists('EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Cache')) {
			Easy_Restaurant_Menu_Cache::delete_group('render');
		}
	}

	/**
	 * Load the privacy functionality.
	 *
	 * Initialize privacy policy content and data handlers.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_privacy(): void {
		$plugin_privacy = new Easy_Restaurant_Menu_Privacy();
		$plugin_privacy->initialize();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run(): void {

		$this->run_plugin();

	}

}
