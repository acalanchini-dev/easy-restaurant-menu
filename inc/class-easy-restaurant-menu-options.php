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
		add_action( 'admin_init', [ $this, 'register_performance_options' ] );
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
		
		// Nuove impostazioni per formato prezzo
		register_setting(
			'erm_settings_group',
			'erm_price_decimal_separator'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_price_thousand_separator'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_price_decimals'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_price_format_template'
		);
		
		// Registrazione delle impostazioni per il layout
		register_setting(
			'erm_settings_group',
			'erm_default_layout'
		);
		
		// Registrazione preset di stile
		register_setting(
			'erm_settings_group',
			'erm_style_preset'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_preset_menu_title_color'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_preset_section_title_color'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_preset_price_color'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_preset_description_color'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_preset_background_color'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_preset_border_color'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_preset_border_radius'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_preset_text_alignment'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_preset_font_size_title'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_preset_font_size_description'
		);
		
		register_setting(
			'erm_settings_group',
			'erm_preset_spacing'
		);
		
		// Nuove impostazioni per il caching
		register_setting(
			'erm_settings_group',
			'erm_enable_caching',
			[
				'type' => 'boolean',
				'default' => true,
				'sanitize_callback' => 'rest_sanitize_boolean'
			]
		);
		
		register_setting(
			'erm_settings_group',
			'erm_cache_expiration',
			[
				'type' => 'integer',
				'default' => 3600,
				'sanitize_callback' => 'absint'
			]
		);
		
		// Statistiche della cache (sola lettura)
		register_setting(
			'erm_settings_group',
			'erm_cache_hits',
			[
				'type' => 'integer',
				'default' => 0
			]
		);
		
		register_setting(
			'erm_settings_group',
			'erm_cache_misses',
			[
				'type' => 'integer',
				'default' => 0
			]
		);
		
		register_setting(
			'erm_settings_group',
			'erm_cache_last_flush',
			[
				'type' => 'integer',
				'default' => 0
			]
		);
		
		// Nuove impostazioni per l'ottimizzazione delle performance
		register_setting(
			'erm_settings_group',
			'erm_enable_asset_optimization',
			[
				'type' => 'boolean',
				'default' => true,
				'sanitize_callback' => 'rest_sanitize_boolean'
			]
		);
		
		register_setting(
			'erm_settings_group',
			'erm_enable_critical_css',
			[
				'type' => 'boolean',
				'default' => true,
				'sanitize_callback' => 'rest_sanitize_boolean'
			]
		);
		
		register_setting(
			'erm_settings_group',
			'erm_enable_conditional_loading',
			[
				'type' => 'boolean',
				'default' => true,
				'sanitize_callback' => 'rest_sanitize_boolean'
			]
		);
		
		// Aggiungi sezione per le impostazioni di ottimizzazione
		add_settings_section(
			'erm_performance_section',
			__('Ottimizzazione Performance', 'easy-restaurant-menu'),
			[$this, 'performance_section_callback'],
			'erm_settings_group'
		);
		
		// Campo per attivare/disattivare l'ottimizzazione degli asset
		add_settings_field(
			'erm_enable_asset_optimization',
			__('Ottimizzazione Asset', 'easy-restaurant-menu'),
			[$this, 'asset_optimization_callback'],
			'erm_settings_group',
			'erm_performance_section'
		);
		
		// Campo per attivare/disattivare il CSS critico inline
		add_settings_field(
			'erm_enable_critical_css',
			__('CSS Critico Inline', 'easy-restaurant-menu'),
			[$this, 'critical_css_callback'],
			'erm_settings_group',
			'erm_performance_section'
		);
		
		// Campo per attivare/disattivare il caricamento condizionale
		add_settings_field(
			'erm_enable_conditional_loading',
			__('Caricamento Condizionale', 'easy-restaurant-menu'),
			[$this, 'conditional_loading_callback'],
			'erm_settings_group',
			'erm_performance_section'
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
	
	/**
	 * Restituisce i preset di stile predefiniti
	 *
	 * @since    1.1.0
	 * @return array Array di preset con le rispettive configurazioni
	 */
	public static function get_style_presets(): array {
		return [
			'elegante' => [
				'name' => __('Elegante', 'easy-restaurant-menu'),
				'menu_title_color' => '#2c3e50',
				'section_title_color' => '#34495e',
				'price_color' => '#c0392b',
				'description_color' => '#7f8c8d',
				'background_color' => '#ffffff',
				'border_color' => '#ecf0f1',
				'border_radius' => 0,
				'text_alignment' => 'center',
				'font_size_title' => '1.4',
				'font_size_description' => '1.0',
				'spacing' => 20
			],
			'casual' => [
				'name' => __('Casual', 'easy-restaurant-menu'),
				'menu_title_color' => '#3498db',
				'section_title_color' => '#2980b9',
				'price_color' => '#27ae60',
				'description_color' => '#95a5a6',
				'background_color' => '#f9f9f9',
				'border_color' => '#e0e0e0',
				'border_radius' => 8,
				'text_alignment' => 'left',
				'font_size_title' => '1.3',
				'font_size_description' => '0.95',
				'spacing' => 15
			],
			'minimalista' => [
				'name' => __('Minimalista', 'easy-restaurant-menu'),
				'menu_title_color' => '#000000',
				'section_title_color' => '#333333',
				'price_color' => '#000000',
				'description_color' => '#666666',
				'background_color' => '#ffffff',
				'border_color' => '#f2f2f2',
				'border_radius' => 0,
				'text_alignment' => 'left',
				'font_size_title' => '1.2',
				'font_size_description' => '0.9',
				'spacing' => 10
			],
			'rustico' => [
				'name' => __('Rustico', 'easy-restaurant-menu'),
				'menu_title_color' => '#5d4037',
				'section_title_color' => '#795548',
				'price_color' => '#8d6e63',
				'description_color' => '#a1887f',
				'background_color' => '#efebe9',
				'border_color' => '#d7ccc8',
				'border_radius' => 4,
				'text_alignment' => 'center',
				'font_size_title' => '1.4',
				'font_size_description' => '1.0',
				'spacing' => 20
			]
		];
	}
	
	/**
	 * Gestisce la pulizia manuale della cache
	 * 
	 * Registra un'azione AJAX per svuotare la cache
	 *
	 * @since    1.1.0
	 */
	public function register_ajax_handlers(): void {
		add_action('wp_ajax_erm_flush_cache', [$this, 'ajax_flush_cache']);
	}
	
	/**
	 * Handler AJAX per svuotare la cache
	 *
	 * @since    1.1.0
	 */
	public function ajax_flush_cache(): void {
		// Verifica che la richiesta sia valida
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Non hai i permessi per eseguire questa operazione.', 'easy-restaurant-menu')], 403);
			return;
		}
		
		// Verifica nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'erm_admin_nonce')) {
			wp_send_json_error(['message' => __('Verifica di sicurezza fallita.', 'easy-restaurant-menu')], 400);
			return;
		}
		
		// Carica la classe cache se non lo è già
		if (!class_exists('EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Cache')) {
			Easy_Restaurant_Menu_Helper::using('inc/class-easy-restaurant-menu-cache.php');
		}
		
		// Svuota la cache
		$count = Easy_Restaurant_Menu_Cache::flush_all();
		
		// Restituisci il risultato
		wp_send_json_success([
			'message' => sprintf(
				__('Cache svuotata con successo. %d elementi rimossi.', 'easy-restaurant-menu'),
				$count
			),
			'count' => $count,
			'stats' => Easy_Restaurant_Menu_Cache::get_stats()
		]);
	}
	
	/**
	 * Restituisce i tempi di scadenza disponibili per la cache
	 *
	 * @since    1.1.0
	 * @return array Array di opzioni per il tempo di scadenza
	 */
	public static function get_cache_expiration_options(): array {
		return [
			300 => __('5 minuti', 'easy-restaurant-menu'),
			900 => __('15 minuti', 'easy-restaurant-menu'),
			1800 => __('30 minuti', 'easy-restaurant-menu'),
			3600 => __('1 ora', 'easy-restaurant-menu'),
			7200 => __('2 ore', 'easy-restaurant-menu'),
			14400 => __('4 ore', 'easy-restaurant-menu'),
			43200 => __('12 ore', 'easy-restaurant-menu'),
			86400 => __('1 giorno', 'easy-restaurant-menu'),
			604800 => __('1 settimana', 'easy-restaurant-menu')
		];
	}

	/**
	 * Registra le opzioni per le ottimizzazioni delle performance
	 * 
	 * @since    1.0.0
	 */
	public function register_performance_options(): void {
		// Opzioni per l'ottimizzazione delle performance
		register_setting('erm_settings_group', 'erm_enable_asset_optimization', [
			'type' => 'boolean',
			'default' => true,
			'sanitize_callback' => 'rest_sanitize_boolean'
		]);
		
		register_setting('erm_settings_group', 'erm_enable_critical_css', [
			'type' => 'boolean',
			'default' => true,
			'sanitize_callback' => 'rest_sanitize_boolean'
		]);
		
		register_setting('erm_settings_group', 'erm_enable_conditional_loading', [
			'type' => 'boolean',
			'default' => true,
			'sanitize_callback' => 'rest_sanitize_boolean'
		]);
		
		// Aggiungi sezione per le impostazioni di ottimizzazione
		add_settings_section(
			'erm_performance_section',
			__('Ottimizzazione Performance', 'easy-restaurant-menu'),
			[$this, 'performance_section_callback'],
			'erm_settings_group'
		);
		
		// Campo per attivare/disattivare l'ottimizzazione degli asset
		add_settings_field(
			'erm_enable_asset_optimization',
			__('Ottimizzazione Asset', 'easy-restaurant-menu'),
			[$this, 'asset_optimization_callback'],
			'erm_settings_group',
			'erm_performance_section'
		);
		
		// Campo per attivare/disattivare il CSS critico inline
		add_settings_field(
			'erm_enable_critical_css',
			__('CSS Critico Inline', 'easy-restaurant-menu'),
			[$this, 'critical_css_callback'],
			'erm_settings_group',
			'erm_performance_section'
		);
		
		// Campo per attivare/disattivare il caricamento condizionale
		add_settings_field(
			'erm_enable_conditional_loading',
			__('Caricamento Condizionale', 'easy-restaurant-menu'),
			[$this, 'conditional_loading_callback'],
			'erm_settings_group',
			'erm_performance_section'
		);
	}
	
	/**
	 * Callback per la sezione performance
	 * 
	 * @since    1.0.0
	 */
	public function performance_section_callback(): void {
		echo '<p>' . __('Configura le impostazioni di ottimizzazione delle performance del plugin. Queste opzioni ti permettono di migliorare i tempi di caricamento del tuo sito.', 'easy-restaurant-menu') . '</p>';
	}
	
	/**
	 * Callback per l'opzione di ottimizzazione degli asset
	 * 
	 * @since    1.0.0
	 */
	public function asset_optimization_callback(): void {
		$option = get_option('erm_enable_asset_optimization', true);
		?>
		<label>
			<input type="checkbox" name="erm_enable_asset_optimization" value="1" <?php checked(1, $option); ?> />
			<?php _e('Attiva l\'ottimizzazione degli asset (script e stili separati per funzionalità)', 'easy-restaurant-menu'); ?>
		</label>
		<p class="description">
			<?php _e('Questa opzione divide gli script e gli stili in componenti più piccoli e specifici per caricare solo ciò che è necessario.', 'easy-restaurant-menu'); ?>
		</p>
		<?php
	}
	
	/**
	 * Callback per l'opzione CSS critico inline
	 * 
	 * @since    1.0.0
	 */
	public function critical_css_callback(): void {
		$option = get_option('erm_enable_critical_css', true);
		?>
		<label>
			<input type="checkbox" name="erm_enable_critical_css" value="1" <?php checked(1, $option); ?> />
			<?php _e('Inserisci il CSS critico inline nell\'head della pagina', 'easy-restaurant-menu'); ?>
		</label>
		<p class="description">
			<?php _e('Questa opzione inserisce gli stili critici necessari per il rendering iniziale direttamente nell\'head della pagina per evitare il FOUC (Flash of Unstyled Content).', 'easy-restaurant-menu'); ?>
		</p>
		<?php
	}
	
	/**
	 * Callback per l'opzione di caricamento condizionale
	 * 
	 * @since    1.0.0
	 */
	public function conditional_loading_callback(): void {
		$option = get_option('erm_enable_conditional_loading', true);
		?>
		<label>
			<input type="checkbox" name="erm_enable_conditional_loading" value="1" <?php checked(1, $option); ?> />
			<?php _e('Carica gli asset solo nelle pagine che contengono menu', 'easy-restaurant-menu'); ?>
		</label>
		<p class="description">
			<?php _e('Questa opzione carica gli script e gli stili solo nelle pagine che contengono blocchi o shortcode del menu ristorante.', 'easy-restaurant-menu'); ?>
		</p>
		<?php
	}
}

