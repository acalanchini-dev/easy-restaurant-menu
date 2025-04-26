/**
 * Script di base per il menu ristorante
 * 
 * Contiene solo le funzionalità essenziali necessarie per tutti i menu
 *
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/public/js
 */
(function( $ ) {
	'use strict';

	/**
	 * Inizializzazione dei menu nella pagina
	 */
	function initRestaurantMenus() {
		// Inizializza tutti i container dei menu
		$('.erm-menu-container').each(function() {
			const $menuContainer = $(this);
			
			// Aggiungi classi interattive agli elementi
			$menuContainer.find('.erm-menu-item').addClass('erm-interactive');
			
			// Gestisci hover sugli elementi
			$menuContainer.on('mouseenter', '.erm-menu-item', function() {
				$(this).addClass('erm-hover');
			}).on('mouseleave', '.erm-menu-item', function() {
				$(this).removeClass('erm-hover');
			});
		});
	}

	// Quando il DOM è pronto
	$(function() {
		// Inizializza i menu
		initRestaurantMenus();
		
		// Evento personalizzato che può essere utilizzato da altri script
		$(document).trigger('erm_ready');
	});

})( jQuery ); 