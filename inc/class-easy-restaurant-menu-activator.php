<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/inc
 */

namespace EASY_RESTAURANT_MENU;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Easy_Restaurant_Menu_Activator {

	/**
	 * This is plugin activator.
	 *
	 * Includes all code necessary to run during the plugin's activation
	 *
	 * @since    1.0.0
	 */
	public static function activate(): void {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		
		// Tabella dei menu
		$table_menus = $wpdb->prefix . 'erm_menus';
		$sql_menus = "CREATE TABLE $table_menus (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			nome VARCHAR(255) NOT NULL,
			descrizione TEXT,
			ordine INT(11) DEFAULT 0 NOT NULL,
			status VARCHAR(20) DEFAULT 'publish' NOT NULL,
			data_creazione DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		
		// Tabella delle sezioni del menu
		$table_sections = $wpdb->prefix . 'erm_sections';
		$sql_sections = "CREATE TABLE $table_sections (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			menu_id mediumint(9) DEFAULT 0 NOT NULL,
			nome VARCHAR(255) NOT NULL,
			descrizione TEXT,
			ordine INT(11) DEFAULT 0 NOT NULL,
			status VARCHAR(20) DEFAULT 'publish' NOT NULL,
			data_creazione DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY menu_id (menu_id)
		) $charset_collate;";
		
		// Tabella degli elementi del menu
		$table_items = $wpdb->prefix . 'erm_items';
		$sql_items = "CREATE TABLE $table_items (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			section_id mediumint(9) NOT NULL,
			titolo VARCHAR(255) NOT NULL,
			descrizione TEXT,
			prezzo DECIMAL(10,2) DEFAULT 0.00 NOT NULL,
			immagine BIGINT(20),
			ordine INT(11) DEFAULT 0 NOT NULL,
			status VARCHAR(20) DEFAULT 'publish' NOT NULL,
			data_creazione DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY section_id (section_id)
		) $charset_collate;";
		
		require_once(\ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql_menus);
		dbDelta($sql_sections);
		dbDelta($sql_items);
		
		// Verifica se il plugin è già stato attivato in precedenza e ha già dati
		$count_sections = $wpdb->get_var("SELECT COUNT(*) FROM $table_sections");
		$count_menus = $wpdb->get_var("SELECT COUNT(*) FROM $table_menus");
		
		// Migrazione: se ci sono sezioni ma non menu, creiamo un menu predefinito
		if ($count_sections > 0 && $count_menus == 0) {
			// Inserisci il menu principale predefinito
			$wpdb->insert(
				$table_menus,
				array(
					'nome' => 'Menu Principale',
					'descrizione' => 'Menu predefinito creato durante la migrazione',
					'ordine' => 1,
					'status' => 'publish'
				)
			);
			
			$default_menu_id = $wpdb->insert_id;
			
			// Associa tutte le sezioni esistenti al menu predefinito
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE $table_sections SET menu_id = %d WHERE menu_id = 0",
					$default_menu_id
				)
			);
			
			// Registra l'operazione di migrazione nel log
			if (function_exists('error_log')) {
				error_log('Easy Restaurant Menu: Migrazione completata. Menu predefinito creato con ID: ' . $default_menu_id . ' e tutte le sezioni esistenti collegate ad esso.');
			}
		} else if ($count_menus == 0) {
			// Se non ci sono menu e nemmeno sezioni, creiamo i dati di esempio
			
			// Crea il menu principale predefinito
			$wpdb->insert(
				$table_menus,
				array(
					'nome' => 'Menu Principale',
					'descrizione' => 'Il nostro menu completo',
					'ordine' => 1,
					'status' => 'publish'
				)
			);
			
			$main_menu_id = $wpdb->insert_id;
			
			// Crea un secondo menu di esempio
			$wpdb->insert(
				$table_menus,
				array(
					'nome' => 'Menu del Giorno',
					'descrizione' => 'Specialità disponibili oggi',
					'ordine' => 2,
					'status' => 'publish'
				)
			);
			
			// Prima inseriamo tutte le sezioni per avere tutti gli ID generati
			$wpdb->insert(
				$table_sections,
				array(
					'menu_id' => $main_menu_id,
					'nome' => 'Antipasti',
					'descrizione' => 'Una selezione dei nostri migliori antipasti',
					'ordine' => 1,
					'status' => 'publish'
				)
			);
			
			$first_section_id = $wpdb->insert_id;
			
			$wpdb->insert(
				$table_sections,
				array(
					'menu_id' => $main_menu_id,
					'nome' => 'Primi Piatti',
					'descrizione' => 'Pasta e risotti della tradizione italiana',
					'ordine' => 2,
					'status' => 'publish'
				)
			);
			
			$second_section_id = $wpdb->insert_id;
			
			$wpdb->insert(
				$table_sections,
				array(
					'menu_id' => $main_menu_id,
					'nome' => 'Secondi Piatti',
					'descrizione' => 'Carni e pesci selezionati',
					'ordine' => 3,
					'status' => 'publish'
				)
			);
			
			$wpdb->insert(
				$table_sections,
				array(
					'menu_id' => $main_menu_id,
					'nome' => 'Dessert',
					'descrizione' => 'Dolci fatti in casa',
					'ordine' => 4,
					'status' => 'publish'
				)
			);
			
			// Registriamo gli ID delle sezioni nel log per debug
			if (function_exists('error_log')) {
				error_log('Easy Restaurant Menu: Sezioni create. Prima sezione ID: ' . $first_section_id . ', Seconda sezione ID: ' . $second_section_id);
			}
			
			// Ora inseriamo gli elementi nel primo gruppo
			$wpdb->insert(
				$table_items,
				array(
					'section_id' => $first_section_id,
					'titolo' => 'Bruschetta',
					'descrizione' => 'Pane tostato con pomodorini, basilico e olio EVO',
					'prezzo' => 5.50,
					'ordine' => 1,
					'status' => 'publish'
				)
			);
			
			$wpdb->insert(
				$table_items,
				array(
					'section_id' => $first_section_id,
					'titolo' => 'Tagliere di salumi',
					'descrizione' => 'Selezione di salumi tipici con crostini',
					'prezzo' => 12.00,
					'ordine' => 2,
					'status' => 'publish'
				)
			);
			
			// Inseriamo elementi nel secondo gruppo
			$wpdb->insert(
				$table_items,
				array(
					'section_id' => $second_section_id,
					'titolo' => 'Spaghetti alla carbonara',
					'descrizione' => 'Spaghetti con uovo, guanciale, pecorino e pepe nero',
					'prezzo' => 10.00,
					'ordine' => 1,
					'status' => 'publish'
				)
			);
			
			$wpdb->insert(
				$table_items,
				array(
					'section_id' => $second_section_id,
					'titolo' => 'Risotto ai funghi porcini',
					'descrizione' => 'Risotto con funghi porcini freschi e parmigiano',
					'prezzo' => 12.50,
					'ordine' => 2,
					'status' => 'publish'
				)
			);
		}
	}
}
