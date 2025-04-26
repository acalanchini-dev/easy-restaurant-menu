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
		// Ottieni la pagina corrente usando la funzione globale di WordPress
		$screen = function_exists('get_current_screen') ? \get_current_screen() : null;
		
		// Carica gli stili solo nelle pagine del plugin o nell'editor quando necessario
		if (!$screen) {
			return;
		}
		
		// Array delle pagine admin del plugin
		$plugin_pages = [
			'toplevel_page_erm-dashboard',
			'menu-ristorante_page_erm-menus',
			'menu-ristorante_page_erm-sections',
			'menu-ristorante_page_erm-items',
			'menu-ristorante_page_erm-options'
		];
		
		// Carica gli stili solo nelle nostre pagine admin
		if (in_array($screen->id, $plugin_pages)) {
			wp_enqueue_style("easy-restaurant-menu-admin-styles", EASY_RESTAURANT_MENU_PLUGIN_URL . 'admin/css/easy-restaurant-menu-admin.css', array(), EASY_RESTAURANT_MENU_VERSION, 'all');
		}
	}

	/**
	 * Register the stylesheets for the block editor (Common styles).
	 *
	 * @since    1.0.0
	 */
	public function editor_styles(): void {
		// Ottieni la pagina corrente usando la funzione globale di WordPress
		$screen = function_exists('get_current_screen') ? \get_current_screen() : null;
		
		// Carica gli stili solo nell'editor
		if (!$screen || !$screen->is_block_editor) {
			return;
		}
		
		// Verifica se l'editor ha blocchi del nostro plugin
		$has_restaurant_menu_block = false;
		
		// Se siamo nell'editor, controlliamo il contenuto del post
		if (isset($GLOBALS['post'])) {
			$post_content = $GLOBALS['post']->post_content;
			
			// Controlla se il post contiene un blocco del nostro plugin
			if (function_exists('has_block') && has_block('easy-restaurant-menu/restaurant-menu', $GLOBALS['post'])) {
				$has_restaurant_menu_block = true;
			} elseif (strpos($post_content, '<!-- wp:easy-restaurant-menu/') !== false) {
				$has_restaurant_menu_block = true;
			}
		}
		
		// Se non abbiamo blocchi, registriamo comunque gli stili ma non li carichiamo
		if (!$has_restaurant_menu_block) {
			wp_register_style("easy-restaurant-menu-editor", EASY_RESTAURANT_MENU_PLUGIN_URL . 'admin/css/easy-restaurant-menu-editor.css', array(), EASY_RESTAURANT_MENU_VERSION, 'all');
		} else {
			// Carica gli stili nell'editor
			add_editor_style(array(EASY_RESTAURANT_MENU_PLUGIN_URL . 'admin/css/easy-restaurant-menu-editor.css'));
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts(): void {
		// Ottieni la pagina corrente usando la funzione globale di WordPress
		$screen = function_exists('get_current_screen') ? \get_current_screen() : null;
		
		// Carica gli script solo nelle pagine del plugin
		if (!$screen) {
			return;
		}
		
		// Array delle pagine admin del plugin
		$plugin_pages = [
			'toplevel_page_erm-dashboard',
			'menu-ristorante_page_erm-menus',
			'menu-ristorante_page_erm-sections',
			'menu-ristorante_page_erm-items',
			'menu-ristorante_page_erm-options'
		];
		
		// Carica gli script solo nelle nostre pagine admin
		if (in_array($screen->id, $plugin_pages)) {
			// Assicurati che il media uploader di WordPress sia caricato
			wp_enqueue_media();
			
			wp_enqueue_script("easy-restaurant-menu-admin-scripts", EASY_RESTAURANT_MENU_PLUGIN_URL . 'admin/js/easy-restaurant-menu-admin.js', array('jquery', 'jquery-ui-sortable'), EASY_RESTAURANT_MENU_VERSION, true);
			
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
			__('Menu', 'easy-restaurant-menu'),
			__('Menu', 'easy-restaurant-menu'),
			'manage_options',
			'erm-menus',
			[$this, 'display_menus_page']
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
	 * Visualizza la pagina dei menu
	 *
	 * @since    1.0.0
	 */
	public function display_menus_page(): void {
		Easy_Restaurant_Menu_Helper::print_view('admin/partials/menus-page.php');
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
		add_action('wp_ajax_erm_save_menu', [$this, 'ajax_save_menu']);
		add_action('wp_ajax_erm_delete_menu', [$this, 'ajax_delete_menu']);
		add_action('wp_ajax_erm_save_section', [$this, 'ajax_save_section']);
		add_action('wp_ajax_erm_delete_section', [$this, 'ajax_delete_section']);
		add_action('wp_ajax_erm_save_item', [$this, 'ajax_save_item']);
		add_action('wp_ajax_erm_delete_item', [$this, 'ajax_delete_item']);
		add_action('wp_ajax_erm_update_order', [$this, 'ajax_update_order']);
		add_action('wp_ajax_erm_get_image', [$this, 'ajax_get_image']);
	}
	
	/**
	 * Salva un menu (AJAX)
	 *
	 * @since    1.0.0
	 */
	public function ajax_save_menu(): void {
		// Controllo di sicurezza
		check_ajax_referer('erm_admin_nonce', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permessi insufficienti', 'easy-restaurant-menu')]);
			return;
		}
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'erm_menus';
		
		$menu_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
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
		
		if ($menu_id > 0) {
			// Aggiornamento
			$wpdb->update($table_name, $data, ['id' => $menu_id], $format, ['%d']);
			$message = __('Menu aggiornato con successo', 'easy-restaurant-menu');
		} else {
			// Inserimento
			$wpdb->insert($table_name, $data, $format);
			$menu_id = $wpdb->insert_id;
			$message = __('Menu creato con successo', 'easy-restaurant-menu');
		}
		
		wp_send_json_success([
			'id' => $menu_id,
			'message' => $message
		]);
	}
	
	/**
	 * Elimina un menu (AJAX)
	 *
	 * @since    1.0.0
	 */
	public function ajax_delete_menu(): void {
		// Controllo di sicurezza
		check_ajax_referer('erm_admin_nonce', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => __('Permessi insufficienti', 'easy-restaurant-menu')]);
			return;
		}
		
		global $wpdb;
		$menu_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		
		if ($menu_id <= 0) {
			wp_send_json_error(['message' => __('ID menu non valido', 'easy-restaurant-menu')]);
			return;
		}
		
		// Prima eliminiamo tutti gli elementi associati alle sezioni di questo menu
		$table_sections = $wpdb->prefix . 'erm_sections';
		$table_items = $wpdb->prefix . 'erm_items';
		
		// Ottieni tutte le sezioni di questo menu
		$sections = $wpdb->get_col($wpdb->prepare("SELECT id FROM $table_sections WHERE menu_id = %d", $menu_id));
		
		// Se ci sono sezioni, elimina tutti gli elementi associati
		if (!empty($sections)) {
			$sections_placeholders = implode(',', array_fill(0, count($sections), '%d'));
			$wpdb->query($wpdb->prepare("DELETE FROM $table_items WHERE section_id IN ($sections_placeholders)", $sections));
			
			// Ora elimina le sezioni
			$wpdb->delete($table_sections, ['menu_id' => $menu_id], ['%d']);
		}
		
		// Infine elimina il menu
		$table_menus = $wpdb->prefix . 'erm_menus';
		$result = $wpdb->delete($table_menus, ['id' => $menu_id], ['%d']);
		
		if ($result === false) {
			wp_send_json_error(['message' => __('Errore durante l\'eliminazione del menu', 'easy-restaurant-menu')]);
			return;
		}
		
		wp_send_json_success(['message' => __('Menu eliminato con successo', 'easy-restaurant-menu')]);
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
		$menu_id = isset($_POST['menu_id']) ? intval($_POST['menu_id']) : 0;
		$nome = sanitize_text_field($_POST['nome']);
		$descrizione = sanitize_textarea_field($_POST['descrizione']);
		$ordine = isset($_POST['ordine']) ? intval($_POST['ordine']) : 0;
		$status = sanitize_text_field($_POST['status']);
		
		$data = [
			'menu_id' => $menu_id,
			'nome' => $nome,
			'descrizione' => $descrizione,
			'ordine' => $ordine,
			'status' => $status
		];
		
		$format = ['%d', '%s', '%s', '%d', '%s'];
		
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
	 * Aggiorna l'ordine (AJAX)
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
		
		// Verifica che siano stati forniti gli elementi
		if (!isset($_POST['items']) || !is_array($_POST['items'])) {
			wp_send_json_error(['message' => __('Nessun elemento fornito', 'easy-restaurant-menu')]);
			return;
		}
		
		// Determina quale tabella aggiornare in base al tipo
		$type = sanitize_text_field($_POST['type']);
		$table_name = '';
		
		switch ($type) {
			case 'menus':
				$table_name = $wpdb->prefix . 'erm_menus';
				$success_message = __('Ordine dei menu aggiornato', 'easy-restaurant-menu');
				break;
			case 'sections':
				$table_name = $wpdb->prefix . 'erm_sections';
				$success_message = __('Ordine delle sezioni aggiornato', 'easy-restaurant-menu');
				break;
			case 'items':
				$table_name = $wpdb->prefix . 'erm_items';
				$success_message = __('Ordine degli elementi aggiornato', 'easy-restaurant-menu');
				break;
			default:
				wp_send_json_error(['message' => __('Tipo di ordinamento non valido', 'easy-restaurant-menu')]);
				return;
		}
		
		// Aggiorna l'ordine degli elementi
		$items = array_map('intval', $_POST['items']);
		$success = true;
		
		foreach ($items as $index => $id) {
			$result = $wpdb->update(
				$table_name,
				['ordine' => $index],
				['id' => $id],
				['%d'],
				['%d']
			);
			
			if ($result === false) {
				$success = false;
			}
		}
		
		if (!$success) {
			wp_send_json_error(['message' => __('Errore durante l\'aggiornamento dell\'ordine', 'easy-restaurant-menu')]);
			return;
		}
		
		wp_send_json_success(['message' => $success_message]);
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
