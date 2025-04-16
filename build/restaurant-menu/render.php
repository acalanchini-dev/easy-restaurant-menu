<?php
/**
 * Rendering del blocco Restaurant Menu.
 *
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/blocks
 */

// Fallback per funzioni di escape se non esistono
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
    function esc_html_e($text, $domain = 'default') {
        echo esc_html($text);
    }
}
if (!function_exists('esc_url')) {
    function esc_url($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}
if (!function_exists('wp_kses_post')) {
    function wp_kses_post($text) {
        return $text; // Implementazione semplificata, solo per fallback
    }
}
if (!function_exists('esc_html__')) {
    function esc_html__($text, $domain = 'default') {
        return esc_html($text);
    }
}

// Funzione helper per generare le stringhe CSS di spaziatura
if (!function_exists('erm_spacing_value')) {
    function erm_spacing_value($spacing_obj) {
        if (empty($spacing_obj) || !is_array($spacing_obj)) {
            return '';
        }
        
        $top = isset($spacing_obj['top']) ? intval($spacing_obj['top']) : 0;
        $right = isset($spacing_obj['right']) ? intval($spacing_obj['right']) : 0;
        $bottom = isset($spacing_obj['bottom']) ? intval($spacing_obj['bottom']) : 0;
        $left = isset($spacing_obj['left']) ? intval($spacing_obj['left']) : 0;
        
        return "{$top}px {$right}px {$bottom}px {$left}px";
    }
}

// Funzione per verificare se un oggetto di spaziatura ha valori diversi da zero
if (!function_exists('erm_has_spacing')) {
    function erm_has_spacing($spacing_obj) {
        if (empty($spacing_obj) || !is_array($spacing_obj)) {
            return false;
        }
        
        return (
            (isset($spacing_obj['top']) && $spacing_obj['top'] != 0) ||
            (isset($spacing_obj['right']) && $spacing_obj['right'] != 0) ||
            (isset($spacing_obj['bottom']) && $spacing_obj['bottom'] != 0) ||
            (isset($spacing_obj['left']) && $spacing_obj['left'] != 0)
        );
    }
}

// Attributi del blocco
$section_id           = $attributes['section_id'] ?? '';
$display_type         = $attributes['displayType'] ?? 'grid';
$columns              = $attributes['columns'] ?? 2;
$item_spacing         = $attributes['itemSpacing'] ?? 20;
$title_color          = $attributes['titleColor'] ?? '#000000';
$price_color          = $attributes['priceColor'] ?? '#000000';
$description_color    = $attributes['descriptionColor'] ?? '#757575';
$background_color     = $attributes['backgroundColor'] ?? '';
$border_radius        = $attributes['borderRadius'] ?? 0;
$border_width         = $attributes['borderWidth'] ?? 0;
$border_color         = $attributes['borderColor'] ?? '';
$box_shadow           = $attributes['boxShadow'] ?? false;
$hover_effect         = $attributes['hoverEffect'] ?? 'none';
$show_images          = $attributes['showImages'] ?? true;
$show_prices          = $attributes['showPrices'] ?? true;
$show_descriptions    = $attributes['showDescriptions'] ?? true;
$image_size_grid      = $attributes['imageSizeGrid'] ?? 200;
$image_size_list      = $attributes['imageSizeList'] ?? 90;
$image_square         = $attributes['imageSquare'] ?? true;
$class_name           = $attributes['className'] ?? '';

// Attributi di spaziatura
$image_margin         = $attributes['imageMargin'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
$image_padding        = $attributes['imagePadding'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
$title_margin         = $attributes['titleMargin'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
$price_margin         = $attributes['priceMargin'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
$description_margin   = $attributes['descriptionMargin'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
$content_padding      = $attributes['contentPadding'] ?? ['top' => 15, 'right' => 15, 'bottom' => 15, 'left' => 15];

// Prepara le classi per il container del menu
$container_class = 'erm-menu-container';
if ($display_type) {
    $container_class .= ' erm-display-' . $display_type;
}
if (!empty($class_name)) {
    $container_class .= ' ' . $class_name;
}

// Ottieni l'oggetto wpdb globale
global $wpdb;

// Output iniziale
$output = '';

// Verifica che l'oggetto database sia disponibile e che il section_id sia impostato
if (!is_object($wpdb) || empty($section_id)) {
    if (empty($section_id)) {
        return '<p>' . esc_html__('Seleziona una sezione dal menu a tendina nel pannello laterale.', 'easy-restaurant-menu') . '</p>';
    }
    return '<p>' . esc_html__('Database non disponibile.', 'easy-restaurant-menu') . '</p>';
}

// Ottieni il prefisso della tabella
$table_name_items = $wpdb->prefix . 'erm_items';
$table_name_sections = $wpdb->prefix . 'erm_sections';

// Verifica se le tabelle esistono
$items_table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name_items}'") === $table_name_items;
$sections_table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name_sections}'") === $table_name_sections;

if (!$items_table_exists || !$sections_table_exists) {
    return '<p>' . esc_html__('Le tabelle del database necessarie non esistono.', 'easy-restaurant-menu') . '</p>';
}

// Ottieni il nome della sezione selezionata
$section_name = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT nome FROM {$table_name_sections} WHERE id = %d AND status = %s",
        $section_id,
        'publish'
    )
);

// Ottieni gli elementi del menu dalla sezione selezionata
$items = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$table_name_items} WHERE section_id = %d AND status = %s ORDER BY ordine ASC",
        $section_id,
        'publish'
    )
);

// Stile inline per layout
$style = '';
if ($display_type === 'grid') {
    $style .= "
        .erm-menu-container.erm-display-grid .erm-menu-items {
            display: grid;
            grid-template-columns: repeat({$columns}, 1fr);
            gap: {$item_spacing}px;
        }
    ";
} else {
    $style .= "
        .erm-menu-container.erm-display-list .erm-menu-item + .erm-menu-item {
            margin-top: {$item_spacing}px;
        }
    ";
}

// Stili per i colori dei testi
$style .= "
    .erm-menu-container .erm-item-title {
        color: {$title_color};
    }
    .erm-menu-container .erm-item-price {
        color: {$price_color};
    }
    .erm-menu-container .erm-item-description {
        color: {$description_color};
    }
";

// Genera un ID univoco per questo blocco per evitare conflitti con altri blocchi sulla stessa pagina
$block_id = 'erm-menu-' . uniqid();

// Aggiungi stili di spaziatura specifici per il blocco corrente
$style .= "
    #{$block_id} .erm-item-content {
        padding: " . erm_spacing_value($content_padding) . ";
    }
";

if (erm_has_spacing($image_margin)) {
    $style .= "
        #{$block_id} .erm-item-image {
            margin: " . erm_spacing_value($image_margin) . ";
        }
    ";
}

if (erm_has_spacing($image_padding)) {
    $style .= "
        #{$block_id} .erm-item-image {
            padding: " . erm_spacing_value($image_padding) . ";
        }
    ";
}

if (erm_has_spacing($title_margin)) {
    $style .= "
        #{$block_id} .erm-item-title {
            margin: " . erm_spacing_value($title_margin) . ";
        }
    ";
}

if (erm_has_spacing($price_margin)) {
    $style .= "
        #{$block_id} .erm-item-price {
            margin: " . erm_spacing_value($price_margin) . ";
        }
    ";
}

if (erm_has_spacing($description_margin)) {
    $style .= "
        #{$block_id} .erm-item-description {
            margin: " . erm_spacing_value($description_margin) . ";
        }
    ";
}

// Inizio del contenitore del menu
$output .= '<div id="' . esc_attr($block_id) . '" class="' . esc_attr($container_class) . '">';
$output .= '<style>' . $style . '</style>';

if (!empty($section_name)) {
    $output .= '<h2 class="erm-section-title">' . esc_html($section_name) . '</h2>';
}

// Verifica se ci sono elementi
if (empty($items)) {
    $output .= '<p>' . esc_html__('Nessun elemento trovato in questa sezione.', 'easy-restaurant-menu') . '</p>';
} else {
    $output .= '<div class="erm-menu-items">';

    foreach ($items as $item) {
        // Classe e stile per l'elemento del menu
        $item_class = 'erm-menu-item';
        if ($hover_effect !== 'none') {
            $item_class .= ' erm-hover-' . esc_attr($hover_effect);
        }
        
        $item_style = '';
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
        
        $output .= '<div class="' . esc_attr($item_class) . '"' . (!empty($item_style) ? ' style="' . esc_attr($item_style) . '"' : '') . '>';

        // Immagine dell'elemento
        if ($show_images && !empty($item->immagine)) {
            $image_url = wp_get_attachment_image_src($item->immagine, 'medium');
            if ($image_url) {
                // Imposta dimensioni in base al layout (griglia o lista)
                $img_width = null;
                $img_height = null;
                $img_class = 'erm-item-image';
                $img_style = '';
                
                if ($display_type === 'grid') {
                    if ($image_square) {
                        $img_width = '100%';
                        $img_height = $image_size_grid . 'px';
                        $img_class .= ' erm-image-square erm-image-grid';
                    } else {
                        $img_width = '100%';
                        $img_height = $image_size_grid . 'px';
                        $img_class .= ' erm-image-rect erm-image-grid';
                    }
                } else { // list display
                    if ($image_square) {
                        $img_width = $image_size_list . 'px';
                        $img_height = $image_size_list . 'px';
                        $img_class .= ' erm-image-square erm-image-list';
                    } else {
                        $img_width = 'auto';
                        $img_height = $image_size_list . 'px';
                        $img_class .= ' erm-image-rect erm-image-list';
                    }
                }
                
                $img_style = "width: {$img_width}; height: {$img_height};";
                
                // Genera il markup HTML per l'immagine
                $output .= '<div class="' . esc_attr($img_class) . '" style="' . esc_attr($img_style) . '">';
                
                // Aggiungi attributo loading="lazy" per il lazy loading
                $output .= '<img src="' . esc_url($image_url[0]) . '" alt="' . esc_attr($item->titolo) . '" style="width: 100%; height: 100%; object-fit: ' . ($image_square ? 'cover' : 'contain') . ';" loading="lazy" />';
                
                $output .= '</div>';
            } else {
                // Fallback per immagini mancanti
                $output .= '<div class="erm-item-image erm-image-missing"></div>';
            }
        }

        $output .= '<div class="erm-item-content">';
        
        // Intestazione con titolo e prezzo
        $output .= '<div class="erm-item-header">';
        $output .= '<h3 class="erm-item-title">' . esc_html($item->titolo) . '</h3>';
        
        if ($show_prices && !empty($item->prezzo)) {
            $output .= '<span class="erm-item-price">' . esc_html(number_format((float)$item->prezzo, 2, ',', ' ')) . ' €</span>';
        }
        
        $output .= '</div>';

        // Descrizione dell'elemento
        if ($show_descriptions && !empty($item->descrizione)) {
            $output .= '<div class="erm-item-description">' . wp_kses_post($item->descrizione) . '</div>';
        }

        $output .= '</div>'; // Fine content
        $output .= '</div>'; // Fine item
    }

    $output .= '</div>'; // Fine items
}

$output .= '</div>'; // Fine container

// Aggiungi funzione di fallback per wp_get_attachment_image_src se non esiste
if (!function_exists('wp_get_attachment_image_src')) {
    function wp_get_attachment_image_src($attachment_id, $size = 'thumbnail') {
        return null; // Implementazione semplificata
    }
}

return $output; 