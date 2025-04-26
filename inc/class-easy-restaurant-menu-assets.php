<?php
/**
 * La classe per la gestione ottimizzata degli asset del plugin.
 *
 * Responsabile del caricamento condizionale degli script e degli stili
 * sia nella parte pubblica che nell'area di amministrazione.
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/inc
 */

namespace EASY_RESTAURANT_MENU;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Easy_Restaurant_Menu_Assets {

    /**
     * Flag per tenere traccia se il contenuto del menu è presente nella pagina
     *
     * @since    1.0.0
     * @access   private
     * @var      boolean $has_restaurant_menu_content
     */
    private static $has_restaurant_menu_content = false;
    
    /**
     * Flag per tenere traccia se gli asset del menu sono già stati registrati
     *
     * @since    1.0.0
     * @access   private
     * @var      boolean $assets_registered
     */
    private static $assets_registered = false;
    
    /**
     * Attributi del blocco usati nella pagina corrente
     *
     * @since    1.0.0
     * @access   private
     * @var      array $current_block_attributes
     */
    private static $current_block_attributes = [];

    /**
     * Inizializza la classe
     * 
     * @since    1.0.0
     */
    public static function init(): void {
        // Registrare gli assets ma non caricarli immediatamente
        add_action('wp_enqueue_scripts', [self::class, 'register_public_assets'], 10);
        
        // Azione per verificare contenuto prima del rendering finale
        add_action('wp_head', [self::class, 'detect_menu_in_content'], 99);
        
        // Carica gli assets in modo condizionale nel footer
        add_action('wp_footer', [self::class, 'maybe_enqueue_assets'], 10);
        
        // Aggiungi CSS critico inline se l'opzione è attiva
        if (get_option('erm_enable_critical_css', true)) {
            add_action('wp_head', [self::class, 'inline_critical_css'], 100);
        }
        
        // Registra assets admin solo nelle pagine necessarie
        add_action('admin_enqueue_scripts', [self::class, 'register_admin_assets'], 10);
    }
    
    /**
     * Registra gli asset pubblici senza caricarli immediatamente
     *
     * @since    1.0.0
     */
    public static function register_public_assets(): void {
        if (self::$assets_registered) {
            return;
        }
        
        // Verifica se l'ottimizzazione degli asset è attiva
        $asset_optimization = get_option('erm_enable_asset_optimization', true);
        
        if ($asset_optimization) {
            // Registra stili base
            wp_register_style(
                'easy-restaurant-menu-public-base',
                EASY_RESTAURANT_MENU_PLUGIN_URL . 'public/css/easy-restaurant-menu-public-base.css',
                [],
                EASY_RESTAURANT_MENU_VERSION
            );
            
            // Registra stili completi
            wp_register_style(
                'easy-restaurant-menu-public',
                EASY_RESTAURANT_MENU_PLUGIN_URL . 'public/css/easy-restaurant-menu-public.css',
                ['easy-restaurant-menu-public-base'],
                EASY_RESTAURANT_MENU_VERSION
            );
            
            // Registra stili per i filtri (caricati solo se necessario)
            wp_register_style(
                'easy-restaurant-menu-public-filters',
                EASY_RESTAURANT_MENU_PLUGIN_URL . 'public/css/easy-restaurant-menu-public-filters.css',
                ['easy-restaurant-menu-public-base'],
                EASY_RESTAURANT_MENU_VERSION
            );
            
            // Registra JS base
            wp_register_script(
                'easy-restaurant-menu-public-base',
                EASY_RESTAURANT_MENU_PLUGIN_URL . 'public/js/easy-restaurant-menu-public-base.js',
                ['jquery'],
                EASY_RESTAURANT_MENU_VERSION,
                true
            );
            
            // Registra JS completo
            wp_register_script(
                'easy-restaurant-menu-public',
                EASY_RESTAURANT_MENU_PLUGIN_URL . 'public/js/easy-restaurant-menu-public.js',
                ['easy-restaurant-menu-public-base'],
                EASY_RESTAURANT_MENU_VERSION,
                true
            );
            
            // Registra JS per filtri (caricato solo se necessario)
            wp_register_script(
                'easy-restaurant-menu-public-filters',
                EASY_RESTAURANT_MENU_PLUGIN_URL . 'public/js/easy-restaurant-menu-public-filters.js',
                ['easy-restaurant-menu-public-base'],
                EASY_RESTAURANT_MENU_VERSION,
                true
            );
            
            // Registra JS per lazy loading (caricato solo se necessario)
            wp_register_script(
                'easy-restaurant-menu-public-lazy',
                EASY_RESTAURANT_MENU_PLUGIN_URL . 'public/js/easy-restaurant-menu-public-lazy.js',
                ['easy-restaurant-menu-public-base'],
                EASY_RESTAURANT_MENU_VERSION,
                true
            );
        } else {
            // Fallback al caricamento tradizionale senza ottimizzazione
            wp_register_style(
                'easy-restaurant-menu-public',
                EASY_RESTAURANT_MENU_PLUGIN_URL . 'public/css/easy-restaurant-menu-public.css',
                [],
                EASY_RESTAURANT_MENU_VERSION
            );
            
            wp_register_script(
                'easy-restaurant-menu-public',
                EASY_RESTAURANT_MENU_PLUGIN_URL . 'public/js/easy-restaurant-menu-public.js',
                ['jquery'],
                EASY_RESTAURANT_MENU_VERSION,
                true
            );
        }
        
        self::$assets_registered = true;
    }
    
    /**
     * Registra gli asset di amministrazione solo nelle pagine necessarie
     *
     * @since    1.0.0
     * @param    string    $hook    L'hook della pagina admin corrente
     */
    public static function register_admin_assets(string $hook): void {
        // Carica gli asset admin solo nelle pagine del plugin
        $plugin_pages = [
            'toplevel_page_erm-dashboard',
            'menu-ristorante_page_erm-menus',
            'menu-ristorante_page_erm-sections',
            'menu-ristorante_page_erm-items',
            'menu-ristorante_page_erm-options'
        ];
        
        if (!in_array($hook, $plugin_pages)) {
            return;
        }
        
        // Carica il media uploader di WordPress
        wp_enqueue_media();

        // Carica CSS e JS admin
        wp_enqueue_style(
            'easy-restaurant-menu-admin-styles',
            EASY_RESTAURANT_MENU_PLUGIN_URL . 'admin/css/easy-restaurant-menu-admin.css',
            [],
            EASY_RESTAURANT_MENU_VERSION
        );
        
        wp_enqueue_script(
            'easy-restaurant-menu-admin-scripts',
            EASY_RESTAURANT_MENU_PLUGIN_URL . 'admin/js/easy-restaurant-menu-admin.js',
            ['jquery', 'jquery-ui-sortable'],
            EASY_RESTAURANT_MENU_VERSION,
            true
        );
        
        // Localizza lo script per AJAX
        wp_localize_script(
            'easy-restaurant-menu-admin-scripts',
            'erm_admin',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('erm_admin_nonce'),
                'text' => [
                    'select_image' => __('Seleziona o carica un\'immagine', 'easy-restaurant-menu'),
                    'use_image' => __('Usa questa immagine', 'easy-restaurant-menu'),
                    'media_library_unavailable' => __('Media Library non disponibile', 'easy-restaurant-menu')
                ]
            ]
        );
    }
    
    /**
     * Imposta il flag che indica che il contenuto del menu è presente nella pagina
     *
     * @since    1.0.0
     * @param    array     $attributes    Attributi del blocco o dello shortcode
     */
    public static function set_has_menu_content(array $attributes = []): void {
        self::$has_restaurant_menu_content = true;
        
        // Salva gli attributi del blocco per caricare asset in modo selettivo
        if (!empty($attributes)) {
            self::$current_block_attributes = $attributes;
        }
    }
    
    /**
     * Verifica se il contenuto della pagina contiene blocchi o shortcode del menu
     *
     * @since    1.0.0
     */
    public static function detect_menu_in_content(): void {
        if (self::$has_restaurant_menu_content) {
            return; // Già impostato dal callback di rendering
        }
        
        global $post;
        
        if (!is_singular() || !is_a($post, 'WP_Post')) {
            return;
        }
        
        // Verifica la presenza del blocco Gutenberg
        if (function_exists('has_block') && has_block('easy-restaurant-menu/restaurant-menu', $post)) {
            self::$has_restaurant_menu_content = true;
        }
    }
    
    /**
     * Carica gli asset in modo condizionale se necessario
     *
     * @since    1.0.0
     */
    public static function maybe_enqueue_assets(): void {
        // Verifica se è attivo il caricamento condizionale
        $conditional_loading = get_option('erm_enable_conditional_loading', true);
        
        // Se il caricamento condizionale è disattivato, carica sempre gli asset
        if (!$conditional_loading) {
            self::enqueue_all_assets();
            return;
        }
        
        // Altrimenti, carica solo se la pagina contiene un menu
        if (self::$has_restaurant_menu_content) {
            self::enqueue_all_assets();
        }
    }
    
    /**
     * Carica tutti gli asset necessari in base alle opzioni configurate
     *
     * @since    1.0.0
     * @access   private
     */
    private static function enqueue_all_assets(): void {
        // Verifica se l'ottimizzazione degli asset è attiva
        $asset_optimization = get_option('erm_enable_asset_optimization', true);
        
        if ($asset_optimization) {
            // Carica sempre gli stili base
            wp_enqueue_style('easy-restaurant-menu-public-base');
            
            // Carica gli script base
            wp_enqueue_script('easy-restaurant-menu-public-base');
            
            // Carica gli stili completi se necessario
            wp_enqueue_style('easy-restaurant-menu-public');
            
            // Carica gli script completi
            wp_enqueue_script('easy-restaurant-menu-public');
            
            // Carica asset condizionali in base agli attributi del blocco
            if (!empty(self::$current_block_attributes)) {
                // Carica CSS e JS per filtri se abilitati
                if (!empty(self::$current_block_attributes['enableFilter']) && self::$current_block_attributes['enableFilter']) {
                    wp_enqueue_style('easy-restaurant-menu-public-filters');
                    wp_enqueue_script('easy-restaurant-menu-public-filters');
                }
                
                // Carica JS per lazy loading se abilitato
                if (!empty(self::$current_block_attributes['enableLazyLoad']) && self::$current_block_attributes['enableLazyLoad']) {
                    wp_enqueue_script('easy-restaurant-menu-public-lazy');
                }
            }
        } else {
            // Caricamento tradizionale senza ottimizzazione
            wp_enqueue_style('easy-restaurant-menu-public');
            wp_enqueue_script('easy-restaurant-menu-public');
        }
    }
    
    /**
     * Inlinea gli stili critici necessari per evitare FOUC
     *
     * @since    1.0.0 
     */
    public static function inline_critical_css(): void {
        // Ottieni l'opzione per il CSS critico
        $enable_critical_css = get_option('erm_enable_critical_css', true);
        
        // Se l'opzione è disattivata o non è richiesto il menu, non fare nulla
        if (!$enable_critical_css) {
            return;
        }
        
        // Se è attivo il caricamento condizionale, controlla se la pagina contiene un menu
        $conditional_loading = get_option('erm_enable_conditional_loading', true);
        if ($conditional_loading && !self::$has_restaurant_menu_content) {
            // Controllo aggiuntivo per le pagine singole con has_block
            global $post;
            if (is_singular() && is_a($post, 'WP_Post')) {
                if (function_exists('has_block') && has_block('easy-restaurant-menu/restaurant-menu', $post)) {
                    self::$has_restaurant_menu_content = true;
                } else if (has_shortcode($post->post_content, 'restaurant_menu')) {
                    self::$has_restaurant_menu_content = true;
                } else {
                    return;
                }
            } else {
                return;
            }
        }
        
        // Stili critici da mettere inline
        $critical_css = "
        .erm-menu-container {
            display: block;
            width: 100%;
            box-sizing: border-box;
        }
        .erm-menu-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .erm-menu-item {
            margin-bottom: 20px;
        }";
        
        // Stampa direttamente nello stream di output
        echo '<style id="erm-critical-css">' . $critical_css . '</style>';
    }
} 