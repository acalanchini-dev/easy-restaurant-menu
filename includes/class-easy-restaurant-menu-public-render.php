<?php
/**
 * Gestisce il rendering pubblico del menu ristorante
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/includes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Classe che gestisce il rendering pubblico del menu ristorante
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/includes
 * @author     Il tuo nome
 */
class Easy_Restaurant_Menu_Public_Render {

    /**
     * Inizializza la classe e definisce hooks e proprietà
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Assicuriamoci che le funzioni di WordPress siano disponibili
        if (function_exists('add_action')) {
            add_action('init', array($this, 'register_restaurant_menu_block'));
        }
        
        // Registra lo shortcode se la funzione è disponibile
        if (function_exists('add_shortcode')) {
            $this->register_shortcode();
        }
    }

    /**
     * Registra il blocco Gutenberg del menu ristorante
     *
     * @since    1.0.0
     */
    public function register_restaurant_menu_block() {
        // Registra il blocco solo se la funzione exists (controllo di compatibilità)
        if (function_exists('register_block_type')) {
            register_block_type('easy-restaurant-menu/restaurant-menu', array(
                'attributes' => array(
                    'layout' => array(
                        'type' => 'string',
                        'default' => 'list'
                    ),
                    'showImages' => array(
                        'type' => 'boolean',
                        'default' => true
                    ),
                    'showDescriptions' => array(
                        'type' => 'boolean',
                        'default' => true
                    ),
                    'sections' => array(
                        'type' => 'string',
                        'default' => 'all'
                    ),
                    'enableFilter' => array(
                        'type' => 'boolean',
                        'default' => false
                    ),
                    'enableLazyLoad' => array(
                        'type' => 'boolean',
                        'default' => true
                    ),
                    'primaryColor' => array(
                        'type' => 'string',
                        'default' => ''
                    ),
                    'secondaryColor' => array(
                        'type' => 'string',
                        'default' => ''
                    ),
                    'textColor' => array(
                        'type' => 'string',
                        'default' => ''
                    ),
                    'priceColor' => array(
                        'type' => 'string',
                        'default' => ''
                    ),
                    'backgroundColor' => array(
                        'type' => 'string',
                        'default' => ''
                    )
                ),
                'render_callback' => array($this, 'render_restaurant_menu_block')
            ));
        }
    }

    /**
     * Renderizza il blocco Gutenberg del menu ristorante
     *
     * @since    1.0.0
     * @param    array    $attributes    Attributi del blocco.
     * @param    string   $content       Contenuto del blocco.
     * @param    WP_Block $block         Istanza del blocco.
     * @return   string                  HTML renderizzato.
     */
    public function render_restaurant_menu_block($attributes, $content, $block) {
        // Carica gli script e i CSS necessari se le funzioni esistono
        if (function_exists('wp_enqueue_style')) {
            wp_enqueue_style('easy-restaurant-menu-public');
        }
        
        if (function_exists('wp_enqueue_script')) {
            wp_enqueue_script('easy-restaurant-menu-public');
        }
        
        // Recupera tutte le sezioni del menu dal database
        global $wpdb;
        $table_sections = $wpdb->prefix . 'erm_sections';
        
        $sections = $wpdb->get_results(
            "SELECT * FROM $table_sections WHERE status = 'publish' ORDER BY ordine ASC",
            OBJECT // Usiamo OBJECT invece di ARRAY_A per compatibilità
        );
        
        // Inizia l'output buffering
        ob_start();
        
        // Includi il template
        if (function_exists('plugin_dir_path')) {
            include plugin_dir_path(dirname(__FILE__)) . 'public/partials/restaurant-menu-render.php';
        } else {
            include dirname(dirname(__FILE__)) . '/public/partials/restaurant-menu-render.php';
        }
        
        // Restituisci l'output buffered
        return ob_get_clean();
    }

    /**
     * Renderizza il menu ristorante tramite shortcode
     *
     * @since    1.0.0
     * @param    array    $atts    Attributi dello shortcode.
     * @return   string            HTML renderizzato.
     */
    public function render_restaurant_menu_shortcode($atts) {
        // Imposta gli attributi predefiniti
        $default_layout = 'list';
        if (function_exists('get_option')) {
            $default_layout = get_option('erm_default_layout', 'list');
        }
        
        // Usa shortcode_atts con controllo di disponibilità
        if (function_exists('shortcode_atts')) {
            $attributes = shortcode_atts(array(
                'layout' => $default_layout,
                'show_images' => 'true',
                'show_descriptions' => 'true',
                'sections' => 'all',
                'enable_filter' => 'false',
                'enable_lazy_load' => 'true',
                'primary_color' => '',
                'secondary_color' => '',
                'text_color' => '',
                'price_color' => '',
                'background_color' => ''
            ), $atts);
        } else {
            // Fallback se shortcode_atts non è disponibile
            $attributes = array(
                'layout' => $default_layout,
                'show_images' => 'true',
                'show_descriptions' => 'true',
                'sections' => 'all',
                'enable_filter' => 'false',
                'enable_lazy_load' => 'true',
                'primary_color' => '',
                'secondary_color' => '',
                'text_color' => '',
                'price_color' => '',
                'background_color' => ''
            );
            
            // Combina con gli attributi forniti
            if (is_array($atts)) {
                $attributes = array_merge($attributes, $atts);
            }
        }
        
        // Converti le stringhe booleane in booleani reali
        $attributes['showImages'] = $attributes['show_images'] === 'true';
        $attributes['showDescriptions'] = $attributes['show_descriptions'] === 'true';
        $attributes['enableFilter'] = $attributes['enable_filter'] === 'true';
        $attributes['enableLazyLoad'] = $attributes['enable_lazy_load'] === 'true';
        
        // Pulisci gli attributi
        unset($attributes['show_images']);
        unset($attributes['show_descriptions']);
        unset($attributes['enable_filter']);
        unset($attributes['enable_lazy_load']);
        
        // Assegna i colori
        $attributes['primaryColor'] = $attributes['primary_color'];
        $attributes['secondaryColor'] = $attributes['secondary_color'];
        $attributes['textColor'] = $attributes['text_color'];
        $attributes['priceColor'] = $attributes['price_color'];
        $attributes['backgroundColor'] = $attributes['background_color'];
        
        // Pulisci gli attributi dei colori
        unset($attributes['primary_color']);
        unset($attributes['secondary_color']);
        unset($attributes['text_color']);
        unset($attributes['price_color']);
        unset($attributes['background_color']);
        
        // Utilizza il metodo di rendering del blocco
        return $this->render_restaurant_menu_block($attributes, '', null);
    }

    /**
     * Registra lo shortcode per il menu ristorante
     *
     * @since    1.0.0
     */
    public function register_shortcode() {
        if (function_exists('add_shortcode')) {
            add_shortcode('restaurant_menu', array($this, 'render_restaurant_menu_shortcode'));
        }
    }
} 