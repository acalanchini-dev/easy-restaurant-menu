<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * An instance of this class should be passed to the run_plugin() function
 * defined in Loader as all of the hooks are defined
 * in that particular class.
 *
 * The Loader will then create the relationship
 * between the defined hooks and the functions defined in this class.
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/admin
 */

namespace EASY_RESTAURANT_MENU;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Easy_Restaurant_Menu_Admin {
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles(): void {

		wp_enqueue_style( "easy-restaurant-menu-admin-styles", EASY_RESTAURANT_MENU_PLUGIN_URL . 'admin/css/easy-restaurant-menu-admin.css', array(), EASY_RESTAURANT_MENU_VERSION, 'all' );

	}

	/**
	 * Register the stylesheets for the block editor (Common styles).
	 *
	 * @since    1.0.0
	 */
	public function editor_styles(): void {

		add_editor_style( array( EASY_RESTAURANT_MENU_PLUGIN_URL . 'admin/css/easy-restaurant-menu-editor.css' ) );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts(): void {
		// Assicurati che il media uploader di WordPress sia caricato
		wp_enqueue_media();

		wp_enqueue_script( "easy-restaurant-menu-admin-scripts", EASY_RESTAURANT_MENU_PLUGIN_URL . 'admin/js/easy-restaurant-menu-admin.js', array( 'jquery', 'jquery-ui-sortable' ), EASY_RESTAURANT_MENU_VERSION, false );

		// Localizza script per AJAX
		wp_localize_script(
			'easy-restaurant-menu-admin-scripts',
			'erm_admin',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('erm_admin_nonce'),
				'text' => array(
					'select_image' => __('Seleziona o carica un\'immagine', 'easy-restaurant-menu'),
					'use_image' => __('Usa questa immagine', 'easy-restaurant-menu'),
					'media_library_unavailable' => __('Media Library non disponibile', 'easy-restaurant-menu')
				)
			)
		);
	}

	/**
	 * Registra i menu dell'amministrazione
	 *
	 * @since    1.0.0
	 */
	public function register_admin_menu(): void {
		add_menu_page(
			__('Menu Ristorante', 'easy-restaurant-menu'),
			__('Menu Ristorante', 'easy-restaurant-menu'),
			'manage_options',
			'erm-dashboard',
			[$this, 'display_dashboard_page'],
			'dashicons-food',
			25
		);
		
		add_submenu_page(
			'erm-dashboard',
			__('Dashboard', 'easy-restaurant-menu'),
			__('Dashboard', 'easy-restaurant-menu'),
			'manage_options',
			'erm-dashboard',
			[$this, 'display_dashboard_page']
		);
		
		add_submenu_page(
			'erm-dashboard',
			__('Sezioni', 'easy-restaurant-menu'),
			__('Sezioni', 'easy-restaurant-menu'),
			'manage_options',
			'erm-sections',
			[$this, 'display_sections_page']
		);
		
		add_submenu_page(
			'erm-dashboard',
			__('Elementi', 'easy-restaurant-menu'),
			__('Elementi', 'easy-restaurant-menu'),
			'manage_options',
			'erm-items',
			[$this, 'display_items_page']
		);
		
		add_submenu_page(
			'erm-dashboard',
			__('Impostazioni', 'easy-restaurant-menu'),
			__('Impostazioni', 'easy-restaurant-menu'),
			'manage_options',
			'erm-options',
			[$this, 'display_options_page']
		);
	}

	/**
	 * Visualizza la pagina dashboard
	 *
	 * @since    1.0.0
	 */
	public function display_dashboard_page(): void {
		Easy_Restaurant_Menu_Helper::print_view('admin/partials/dashboard-page.php');
	}

	/**
	 * Visualizza la pagina delle sezioni
	 *
	 * @since    1.0.0
	 */
	public function display_sections_page(): void {
		Easy_Restaurant_Menu_Helper::print_view('admin/partials/sections-page.php');
	}

	/**
	 * Visualizza la pagina degli elementi
	 *
	 * @since    1.0.0
	 */
	public function display_items_page(): void {
		Easy_Restaurant_Menu_Helper::print_view('admin/partials/items-page.php');
	}

	/**
	 * Visualizza la pagina delle opzioni
	 *
	 * @since    1.0.0
	 */
	public function display_options_page(): void {
		Easy_Restaurant_Menu_Helper::print_view('admin/partials/options-page.php');
	}

	/**
	 * Ottiene la pagina delle opzioni
	 * 
	 * Utilizzato dalla classe Easy_Restaurant_Menu_Options per mostrare la pagina delle opzioni
	 *
	 * @since    1.0.0
	 */
	public function get_options_page(): void {
		$this->display_options_page();
	}
	
	/**
	 * Registra gli hook per AJAX
	 *
	 * @since    1.0.0
	 */
	public function register_ajax_handlers(): void {
		add_action('wp_ajax_erm_save_section', [$this, 'ajax_save_section']);
		add_action('wp_ajax_erm_delete_section', [$this, 'ajax_delete_section']);
		add_action('wp_ajax_erm_save_item', [$this, 'ajax_save_item']);
		add_action('wp_ajax_erm_delete_item', [$this, 'ajax_delete_item']);
		add_action('wp_ajax_erm_update_order', [$this, 'ajax_update_order']);
		add_action('wp_ajax_erm_get_image', [$this, 'ajax_get_image']);
	}
	
	/**
	 * Salva una sezione (AJAX)
	 *
	 * @since    1.0.0
	 */
	public function ajax_save_section(): void {
		// Controllo di sicurezza
		check_ajax_referer('erm_admin_nonce', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permessi insufficienti', 'easy-restaurant-menu')]);
			return;
		}
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'erm_sections';
		
		$section_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$nome = sanitize_text_field($_POST['nome']);
		$descrizione = sanitize_textarea_field($_POST['descrizione']);
		$ordine = isset($_POST['ordine']) ? intval($_POST['ordine']) : 0;
		$status = sanitize_text_field($_POST['status']);
		
		$data = [
			'nome' => $nome,
			'descrizione' => $descrizione,
			'ordine' => $ordine,
			'status' => $status
		];
		
		$format = ['%s', '%s', '%d', '%s'];
		
		if ($section_id > 0) {
			// Aggiornamento
			$wpdb->update($table_name, $data, ['id' => $section_id], $format, ['%d']);
			$message = __('Sezione aggiornata con successo', 'easy-restaurant-menu');
		} else {
			// Inserimento
			$wpdb->insert($table_name, $data, $format);
			$section_id = $wpdb->insert_id;
			$message = __('Sezione creata con successo', 'easy-restaurant-menu');
		}
		
		wp_send_json_success([
			'id' => $section_id,
			'message' => $message
		]);
	}
	
	/**
	 * Elimina una sezione (AJAX)
	 *
	 * @since    1.0.0
	 */
	public function ajax_delete_section(): void {
		// Controllo di sicurezza
		check_ajax_referer('erm_admin_nonce', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permessi insufficienti', 'easy-restaurant-menu')]);
			return;
		}
		
		global $wpdb;
		$table_sections = $wpdb->prefix . 'erm_sections';
		$table_items = $wpdb->prefix . 'erm_items';
		
		$section_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		
		if ($section_id <= 0) {
			wp_send_json_error(['message' => __('ID sezione non valido', 'easy-restaurant-menu')]);
			return;
		}
		
		// Elimina prima gli elementi associati
		$wpdb->delete($table_items, ['section_id' => $section_id], ['%d']);
		
		// Poi elimina la sezione
		$result = $wpdb->delete($table_sections, ['id' => $section_id], ['%d']);
		
		if ($result === false) {
			wp_send_json_error(['message' => __('Errore durante l\'eliminazione', 'easy-restaurant-menu')]);
			return;
		}
		
		wp_send_json_success(['message' => __('Sezione eliminata con successo', 'easy-restaurant-menu')]);
	}
	
	/**
	 * Salva un elemento (AJAX)
	 *
	 * @since    1.0.0
	 */
	public function ajax_save_item(): void {
		// Controllo di sicurezza
		check_ajax_referer('erm_admin_nonce', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permessi insufficienti', 'easy-restaurant-menu')]);
			return;
		}
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'erm_items';
		
		$item_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$section_id = intval($_POST['section_id']);
		$titolo = sanitize_text_field($_POST['titolo']);
		$descrizione = sanitize_textarea_field($_POST['descrizione']);
		$prezzo = floatval($_POST['prezzo']);
		$immagine = isset($_POST['immagine']) ? intval($_POST['immagine']) : 0;
		$ordine = isset($_POST['ordine']) ? intval($_POST['ordine']) : 0;
		$status = sanitize_text_field($_POST['status']);
		
		$data = [
			'section_id' => $section_id,
			'titolo' => $titolo,
			'descrizione' => $descrizione,
			'prezzo' => $prezzo,
			'immagine' => $immagine,
			'ordine' => $ordine,
			'status' => $status
		];
		
		$format = ['%d', '%s', '%s', '%f', '%d', '%d', '%s'];
		
		if ($item_id > 0) {
			// Aggiornamento
			$wpdb->update($table_name, $data, ['id' => $item_id], $format, ['%d']);
			$message = __('Elemento aggiornato con successo', 'easy-restaurant-menu');
		} else {
			// Inserimento
			$wpdb->insert($table_name, $data, $format);
			$item_id = $wpdb->insert_id;
			$message = __('Elemento creato con successo', 'easy-restaurant-menu');
		}
		
		wp_send_json_success([
			'id' => $item_id,
			'message' => $message
		]);
	}
	
	/**
	 * Elimina un elemento (AJAX)
	 *
	 * @since    1.0.0
	 */
	public function ajax_delete_item(): void {
		// Controllo di sicurezza
		check_ajax_referer('erm_admin_nonce', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permessi insufficienti', 'easy-restaurant-menu')]);
			return;
		}
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'erm_items';
		
		$item_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		
		if ($item_id <= 0) {
			wp_send_json_error(['message' => __('ID elemento non valido', 'easy-restaurant-menu')]);
			return;
		}
		
		$result = $wpdb->delete($table_name, ['id' => $item_id], ['%d']);
		
		if ($result === false) {
			wp_send_json_error(['message' => __('Errore durante l\'eliminazione', 'easy-restaurant-menu')]);
			return;
		}
		
		wp_send_json_success(['message' => __('Elemento eliminato con successo', 'easy-restaurant-menu')]);
	}
	
	/**
	 * Aggiorna l'ordine degli elementi (AJAX)
	 *
	 * @since    1.0.0
	 */
	public function ajax_update_order(): void {
		// Controllo di sicurezza
		check_ajax_referer('erm_admin_nonce', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permessi insufficienti', 'easy-restaurant-menu')]);
			return;
		}
		
		global $wpdb;
		
		$items = isset($_POST['items']) ? $_POST['items'] : [];
		$type = sanitize_text_field($_POST['type']); // 'sections' o 'items'
		
		if (empty($items) || !is_array($items)) {
			wp_send_json_error(['message' => __('Nessun elemento da ordinare', 'easy-restaurant-menu')]);
			return;
		}
		
		$table_name = $wpdb->prefix . ($type === 'sections' ? 'erm_sections' : 'erm_items');
		
		// Aggiorna l'ordine di ciascun elemento
		foreach ($items as $order => $id) {
			$wpdb->update(
				$table_name,
				['ordine' => intval($order)],
				['id' => intval($id)],
				['%d'],
				['%d']
			);
		}
		
		wp_send_json_success(['message' => __('Ordine aggiornato con successo', 'easy-restaurant-menu')]);
	}

	/**
	 * Ottiene un'immagine tramite il suo ID (AJAX)
	 *
	 * @since    1.0.0
	 */
	public function ajax_get_image(): void {
		// Controllo di sicurezza
		check_ajax_referer('erm_admin_nonce', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permessi insufficienti', 'easy-restaurant-menu')]);
			return;
		}
		
		$image_id = isset($_POST['image_id']) ? intval($_POST['image_id']) : 0;
		
		if ($image_id <= 0) {
			wp_send_json_error(['message' => __('ID immagine non valido', 'easy-restaurant-menu')]);
			return;
		}
		
		$image = wp_get_attachment_image_src($image_id, 'medium');
		
		if (!$image) {
			wp_send_json_error(['message' => __('Immagine non trovata', 'easy-restaurant-menu')]);
			return;
		}
		
		wp_send_json_success([
			'id' => $image_id,
			'url' => $image[0],
			'width' => $image[1],
			'height' => $image[2]
		]);
	}
}
