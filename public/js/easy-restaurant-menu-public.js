/**
 * JavaScript per la parte pubblica
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 */

(function($) {
    'use strict';
    
    /**
     * Classe RestaurantMenu
     * 
     * Gestisce le interazioni del menu sul frontend
     */
    class RestaurantMenu {
        /**
         * Costruttore
         * 
         * @param {HTMLElement} element - Elemento DOM del menu
         * @param {Object} options - Opzioni di configurazione
         */
        constructor(element, options) {
            this.element = element;
            this.$element = $(element);
            this.options = $.extend({
                layout: 'list',
                showImages: true,
                showDescriptions: true,
                currencyPosition: 'after',
                currencySymbol: '€',
                enableFilter: false,
                enableLazyLoad: true
            }, options);
            
            this.init();
        }
        
        /**
         * Inizializza il menu
         */
        init() {
            this.setupEventListeners();
            
            if (this.options.enableLazyLoad) {
                this.setupLazyLoading();
            }
            
            if (this.options.enableFilter) {
                this.setupFilter();
            }
        }
        
        /**
         * Imposta i listener per gli eventi
         */
        setupEventListeners() {
            const self = this;
            
            // Per dispositivi mobile, gestisce la selezione delle sezioni
            this.$element.find('.erm-section-selector').on('change', function() {
                const sectionId = $(this).val();
                self.scrollToSection(sectionId);
            });
            
            // Gestisce click su ancoraggi interni
            this.$element.find('.erm-menu-nav a').on('click', function(e) {
                e.preventDefault();
                const sectionId = $(this).attr('href').replace('#', '');
                self.scrollToSection(sectionId);
            });
        }
        
        /**
         * Scorre fino alla sezione specificata
         * 
         * @param {string} sectionId - ID della sezione
         */
        scrollToSection(sectionId) {
            const $section = this.$element.find('#' + sectionId);
            
            if ($section.length) {
                $('html, body').animate({
                    scrollTop: $section.offset().top - 50
                }, 500);
            }
        }
        
        /**
         * Imposta il caricamento lazy delle immagini
         */
        setupLazyLoading() {
            const self = this;
            
            this.$element.find('.erm-item-image img').each(function() {
                const $img = $(this);
                const src = $img.attr('data-src');
                
                if (src) {
                    $img.attr('src', 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==');
                    
                    const img = new Image();
                    img.onload = function() {
                        $img.attr('src', src).addClass('erm-image-loaded');
                    };
                    img.src = src;
                    
                    // Alternativa: utilizzo di Intersection Observer
                    if ('IntersectionObserver' in window) {
                        const observer = new IntersectionObserver(function(entries) {
                            entries.forEach(function(entry) {
                                if (entry.isIntersecting) {
                                    $img.attr('src', src).addClass('erm-image-loaded');
                                    observer.unobserve(entry.target);
                                }
                            });
                        });
                        observer.observe($img[0]);
                    }
                }
            });
        }
        
        /**
         * Imposta il filtro di ricerca per gli elementi del menu
         */
        setupFilter() {
            const self = this;
            const $filterInput = this.$element.find('.erm-menu-filter-input');
            
            if ($filterInput.length) {
                $filterInput.on('input', function() {
                    const query = $(this).val().toLowerCase();
                    self.filterItems(query);
                });
            }
        }
        
        /**
         * Filtra gli elementi del menu in base alla query
         * 
         * @param {string} query - Testo di ricerca
         */
        filterItems(query) {
            const self = this;
            
            if (!query) {
                // Mostra tutti gli elementi e le sezioni
                this.$element.find('.erm-item').show();
                this.$element.find('.erm-section').show();
                return;
            }
            
            // Nascondi tutte le sezioni prima di iniziare
            this.$element.find('.erm-section').each(function() {
                const $section = $(this);
                let hasVisibleItems = false;
                
                // Per ogni elemento nella sezione, verifica se corrisponde alla query
                $section.find('.erm-item').each(function() {
                    const $item = $(this);
                    const title = $item.find('.erm-item-title').text().toLowerCase();
                    const description = $item.find('.erm-item-description').text().toLowerCase();
                    
                    if (title.includes(query) || description.includes(query)) {
                        $item.show();
                        hasVisibleItems = true;
                    } else {
                        $item.hide();
                    }
                });
                
                // Mostra la sezione solo se ha elementi visibili
                if (hasVisibleItems) {
                    $section.show();
                } else {
                    $section.hide();
                }
            });
        }
    }
    
    // Aggiungi il plugin a jQuery
    $.fn.restaurantMenu = function(options) {
        return this.each(function() {
            if (!$.data(this, 'RestaurantMenu')) {
                $.data(this, 'RestaurantMenu', new RestaurantMenu(this, options));
            }
        });
    };
    
    // Inizializza tutti i menu presenti nella pagina
    $(document).ready(function() {
        $('.easy-restaurant-menu').each(function() {
            const $this = $(this);
            const options = {
                layout: $this.data('layout') || 'list',
                showImages: $this.data('show-images') !== 'no',
                showDescriptions: $this.data('show-descriptions') !== 'no',
                currencyPosition: $this.data('currency-position') || 'after',
                currencySymbol: $this.data('currency-symbol') || '€',
                enableFilter: $this.data('enable-filter') === 'yes',
                enableLazyLoad: $this.data('enable-lazy-load') !== 'no'
            };
            
            $(this).restaurantMenu(options);
        });
    });
    
})(jQuery);
