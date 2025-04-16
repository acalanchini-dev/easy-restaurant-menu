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
		
		// Tabella delle sezioni del menu
		$table_sections = $wpdb->prefix . 'erm_sections';
		$sql_sections = "CREATE TABLE $table_sections (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			nome VARCHAR(255) NOT NULL,
			descrizione TEXT,
			ordine INT(11) DEFAULT 0 NOT NULL,
			status VARCHAR(20) DEFAULT 'publish' NOT NULL,
			data_creazione DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id)
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
		dbDelta($sql_sections);
		dbDelta($sql_items);
		
		// Inserisci dati di esempio nella tabella delle sezioni solo se è vuota
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $table_sections");
		
		if ($count == 0) {
			// Prima inseriamo tutte le sezioni per avere tutti gli ID generati
			$wpdb->insert(
				$table_sections,
				array(
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
					'nome' => 'Secondi Piatti',
					'descrizione' => 'Carni e pesci selezionati',
					'ordine' => 3,
					'status' => 'publish'
				)
			);
			
			$wpdb->insert(
				$table_sections,
				array(
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
