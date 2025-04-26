/**
 * Script per i filtri del menu ristorante
 * 
 * Implementa la funzionalità per filtrare le sezioni del menu
 * Questo file viene caricato solo quando la funzionalità di filtro è attiva.
 *
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/public/js
 */
(function( $ ) {
	'use strict';

	/**
	 * Inizializza la funzionalità di filtro
	 */
	function initFilters() {
		// Per ogni container di menu con filtri
		$('.erm-menu-container[data-enable-filter="true"]').each(function() {
			const $menuContainer = $(this);
			const $filterButtons = $menuContainer.find('.erm-filter-button');
			const $sections = $menuContainer.find('.erm-menu-section');
			
			// Aggiungi classi iniziali
			$sections.addClass('filtered');
			
			// Se c'è un filtro "all", attivalo e mostra tutte le sezioni
			const $allFilter = $filterButtons.filter('[data-filter="all"]');
			if ($allFilter.length) {
				$allFilter.addClass('active');
				$sections.addClass('active');
			} else {
				// Altrimenti attiva il primo filtro e mostra le relative sezioni
				const $firstFilter = $filterButtons.first();
				$firstFilter.addClass('active');
				
				const firstFilterValue = $firstFilter.data('filter');
				$sections.filter('[data-section="' + firstFilterValue + '"]').addClass('active');
			}
			
			// Gestisci clic sui filtri
			$filterButtons.on('click', function() {
				const $button = $(this);
				const filterValue = $button.data('filter');
				
				// Rimuovi classe active da tutti i pulsanti
				$filterButtons.removeClass('active');
				
				// Aggiungi classe active al pulsante cliccato
				$button.addClass('active');
				
				// Nascondi tutte le sezioni
				$sections.removeClass('active');
				
				// Mostra le sezioni corrispondenti al filtro
				if (filterValue === 'all') {
					$sections.addClass('active');
				} else {
					$sections.filter('[data-section="' + filterValue + '"]').addClass('active');
				}
				
				// Scorrimento animato alla prima sezione visibile
				const $firstVisibleSection = $sections.filter('.active').first();
				if ($firstVisibleSection.length) {
					$('html, body').animate({
						scrollTop: $firstVisibleSection.offset().top - 50
					}, 500);
				}
				
				// Trigger evento personalizzato
				$(document).trigger('erm_filter_changed', [filterValue]);
			});
		});
	}

	// Quando il documento è pronto
	$(function() {
		// Inizializza i filtri
		initFilters();
	});
	
	// In alternativa, attendi l'evento erm_ready se lo script base è caricato prima
	$(document).on('erm_ready', function() {
		initFilters();
	});

})( jQuery ); 