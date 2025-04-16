<?php
/**
 * Template per il rendering pubblico del menu ristorante.
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/public/partials
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Template per il rendering del menu ristorante
 * 
 * @package Easy Restaurant Menu
 */

// Definisci funzioni di escaping nel caso in cui non siamo in ambiente WordPress
if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_html_e')) {
    function esc_html_e($text, $domain = '') {
        echo esc_html($text);
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}

if (!function_exists('wp_kses_post')) {
    function wp_kses_post($content) {
        return $content; // Semplificato, non implementa il filtro completo
    }
}

// Definisco costanti di WordPress per tipi di risultato
if (!defined('OBJECT')) {
    define('OBJECT', 'OBJECT');
}

// Simulazione di wpdb se non siamo in WordPress
if (!isset($wpdb) || !is_object($wpdb)) {
    $wpdb = new stdClass();
    $wpdb->prefix = '';
    
    // Funzioni mock per wpdb
    if (!method_exists($wpdb, 'prepare')) {
        $wpdb->prepare = function($query, ...$args) {
            return sprintf($query, ...$args);
        };
    }
    
    if (!method_exists($wpdb, 'get_results')) {
        $wpdb->get_results = function($query) {
            return array(); // Ritorna un array vuoto se non siamo in WordPress
        };
    }
}

// Prepara le classi per il contenitore in base agli attributi
$container_classes = array('erm-menu-container');
$container_classes[] = isset($attributes['layout']) ? 'erm-layout-' . esc_attr($attributes['layout']) : 'erm-layout-list';

if (!empty($attributes['primaryColor']) || 
    !empty($attributes['secondaryColor']) || 
    !empty($attributes['textColor']) || 
    !empty($attributes['priceColor']) || 
    !empty($attributes['backgroundColor'])) {
    $container_classes[] = 'erm-custom-colors';
}

// Prepara gli stili in linea se ci sono colori personalizzati
$container_style = '';
if (!empty($attributes['backgroundColor'])) {
    $container_style .= 'background-color:' . esc_attr($attributes['backgroundColor']) . ';';
}

// ID univoco per questo menu
$menu_id = 'erm-menu-' . uniqid();

// Estrai gli attributi
$section_id = $attributes['section_id'] ?? '';
$display_type = $attributes['displayType'] ?? 'grid';
$columns = $attributes['columns'] ?? 2;
$show_images = $attributes['showImages'] ?? true;
$show_prices = $attributes['showPrices'] ?? true;
$show_descriptions = $attributes['showDescriptions'] ?? true;
$item_spacing = $attributes['itemSpacing'] ?? 20;
$border_radius = $attributes['borderRadius'] ?? 0;
$border_width = $attributes['borderWidth'] ?? 0;
$box_shadow = $attributes['boxShadow'] ?? false;
$hover_effect = $attributes['hoverEffect'] ?? 'none';

// Colori
$price_color = $attributes['priceColor'] ?? '';
$title_color = $attributes['titleColor'] ?? '';
$description_color = $attributes['descriptionColor'] ?? '';
$background_color = $attributes['backgroundColor'] ?? '';
$border_color = $attributes['borderColor'] ?? '';

// CSS inline
$title_style = '';
$description_style = '';
$price_style = '';
$item_style = '';

// Applica i colori personalizzati se impostati
if (!empty($title_color)) {
    $title_style .= "color: {$title_color};";
}

if (!empty($price_color)) {
    $price_style .= "color: {$price_color};";
}

if (!empty($description_color)) {
    $description_style .= "color: {$description_color};";
}

// Stile per gli elementi del menu
$item_style .= "margin-bottom: {$item_spacing}px;";
if (!empty($background_color)) {
    $item_style .= "background-color: {$background_color};";
}
if ($border_width > 0 && !empty($border_color)) {
    $item_style .= "border: {$border_width}px solid {$border_color};";
}
if ($border_radius > 0) {
    $item_style .= "border-radius: {$border_radius}px;";
}
if ($box_shadow) {
    $item_style .= "box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);";
}

// Stile per il container
if ($display_type === 'grid') {
    $container_style .= "display: grid; grid-template-columns: repeat({$columns}, 1fr); grid-gap: {$item_spacing}px;";
}

// Recupera i dati dal database
global $wpdb;
$items_table = $wpdb->prefix . 'erm_items';
$sections_table = $wpdb->prefix . 'erm_sections';

// Se nessuna sezione è selezionata, mostra un messaggio
if (empty($section_id)) {
    return '<p>' . esc_html__('Seleziona una sezione del menu per visualizzare gli elementi.', 'easy-restaurant-menu') . '</p>';
}

// Recupera il nome della sezione
$section = $wpdb->get_row(
    $wpdb->prepare("SELECT * FROM {$sections_table} WHERE id = %d", intval($section_id)),
    ARRAY_A
);

if (!$section) {
    return '<p>' . esc_html__('Sezione non trovata.', 'easy-restaurant-menu') . '</p>';
}

// Recupera gli elementi della sezione
$items = $wpdb->get_results(
    $wpdb->prepare("SELECT * FROM {$items_table} WHERE section_id = %d AND status = 'publish' ORDER BY ordine ASC", intval($section_id)),
    ARRAY_A
);

// Se non ci sono elementi, mostra un messaggio
if (empty($items)) {
    return '<p>' . esc_html__('Nessun elemento trovato in questa sezione.', 'easy-restaurant-menu') . '</p>';
}

// Genera il markup HTML
ob_start();
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
    <div class="erm-menu-container">
        <h2 class="erm-section-title" style="<?php echo esc_attr($title_style); ?>"><?php echo esc_html($section['nome']); ?></h2>
        
        <?php if ($display_type === 'grid'): ?>
            <div class="erm-menu-grid" style="<?php echo esc_attr($container_style); ?>">
        <?php endif; ?>
        
        <?php foreach ($items as $item): ?>
            <?php
            $image_url = '';
            if ($show_images && !empty($item['immagine'])) {
                $image = wp_get_attachment_image_src($item['immagine'], 'medium');
                if ($image) {
                    $image_url = $image[0];
                }
            }
            ?>
            
            <div class="erm-menu-item<?php echo ($hover_effect !== 'none') ? ' erm-hover-' . esc_attr($hover_effect) : ''; ?>" style="<?php echo esc_attr($item_style); ?>">
                <?php if ($show_images && !empty($image_url)): ?>
                    <div class="erm-item-image">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($item['titolo']); ?>">
                    </div>
                <?php endif; ?>
                
                <div class="erm-item-content">
                    <div class="erm-item-header">
                        <h3 class="erm-item-title" style="<?php echo esc_attr($title_style); ?>"><?php echo esc_html($item['titolo']); ?></h3>
                        
                        <?php if ($show_prices): ?>
                            <span class="erm-item-price" style="<?php echo esc_attr($price_style); ?>">
                                <?php echo esc_html(number_format((float)$item['prezzo'], 2, ',', ' ')); ?> €
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($show_descriptions && !empty($item['descrizione'])): ?>
                        <p class="erm-item-description" style="<?php echo esc_attr($description_style); ?>">
                            <?php echo esc_html($item['descrizione']); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if ($display_type === 'grid'): ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
return ob_get_clean();