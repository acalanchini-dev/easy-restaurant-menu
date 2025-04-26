/**
 * Script per il lazy loading delle immagini del menu ristorante
 * 
 * Implementa la funzionalità di lazy loading per le immagini
 * Questo file viene caricato solo quando la funzionalità di lazy loading è attiva.
 *
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/public/js
 */
(function( $ ) {
	'use strict';

	/**
	 * Implementa il lazy loading delle immagini
	 */
	function initLazyLoading() {
		// Cerca tutti i container dei menu con lazy loading abilitato
		$('.erm-menu-container[data-enable-lazy-load="true"]').each(function() {
			const $menuContainer = $(this);
			
			// Cerca tutte le immagini con attributo data-src
			const $lazyImages = $menuContainer.find('.erm-item-image[data-src]');
			
			// Se il browser supporta Intersection Observer
			if ('IntersectionObserver' in window) {
				const imageObserver = new IntersectionObserver(function(entries, observer) {
					entries.forEach(function(entry) {
						// Se l'immagine è visibile
						if (entry.isIntersecting) {
							const $image = $(entry.target);
							
							// Carica l'immagine
							$image.attr('src', $image.data('src'));
							
							// Aggiungi classe loaded quando l'immagine è caricata
							$image.on('load', function() {
								$image.addClass('loaded');
							});
							
							// Smetti di osservare questa immagine
							observer.unobserve(entry.target);
						}
					});
				}, {
					rootMargin: '50px 0px',
					threshold: 0.01
				});
				
				// Osserva tutte le immagini
				$lazyImages.each(function() {
					imageObserver.observe(this);
				});
			} else {
				// Fallback per browser che non supportano Intersection Observer
				// Carica tutte le immagini subito
				$lazyImages.each(function() {
					const $image = $(this);
					$image.attr('src', $image.data('src'));
					
					$image.on('load', function() {
						$image.addClass('loaded');
					});
				});
			}
		});
	}

	// Quando il documento è pronto
	$(function() {
		// Inizializza il lazy loading
		initLazyLoading();
	});
	
	// In alternativa, attendi l'evento erm_ready se lo script base è caricato prima
	$(document).on('erm_ready', function() {
		initLazyLoading();
	});

})( jQuery ); 