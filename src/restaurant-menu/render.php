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

// Funzione per formattare il prezzo in base alle impostazioni configurate
if (!function_exists('erm_format_price')) {
    function erm_format_price($price) {
        // Recupera le opzioni del formato prezzo
        $decimal_separator = get_option('erm_price_decimal_separator', ',');
        $thousand_separator = get_option('erm_price_thousand_separator', '.');
        $decimals = get_option('erm_price_decimals', 2);
        $format_template = get_option('erm_price_format_template', '%s');
        
        // Formatta il prezzo in base alle opzioni
        $formatted_price = number_format((float)$price, $decimals, $decimal_separator, $thousand_separator);
        
        // Applica il template del formato se esiste
        if (!empty($format_template) && $format_template !== '%s') {
            $formatted_price = sprintf($format_template, $formatted_price);
        }
        
        // Applica il simbolo valuta in base alla posizione
        $currency_symbol = get_option('erm_currency_symbol', '€');
        $currency_position = get_option('erm_currency_position', 'after');
        
        if ($currency_position === 'before') {
            $final_price = $currency_symbol . ' ' . $formatted_price;
        } else {
            $final_price = $formatted_price . ' ' . $currency_symbol;
        }
        
        return $final_price;
    }
}

// Funzione per ottenere le impostazioni del preset di stile selezionato
if (!function_exists('erm_get_style_preset')) {
    function erm_get_style_preset() {
        // Carica la classe delle opzioni se non è già disponibile
        if (!class_exists('EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Options')) {
            if (function_exists('Easy_Restaurant_Menu_Helper::using')) {
                Easy_Restaurant_Menu_Helper::using('inc/class-easy-restaurant-menu-options.php');
            } else {
                return null;
            }
        }
        
        // Ottieni il preset selezionato
        $selected_preset = get_option('erm_style_preset', 'elegante');
        $available_presets = EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Options::get_style_presets();
        
        // Restituisci il preset se esiste, altrimenti null
        return isset($available_presets[$selected_preset]) ? $available_presets[$selected_preset] : null;
    }
}

// Attributi del blocco
$menu_id                    = $attributes['menu_id'] ?? '';
$section_id                 = $attributes['section_id'] ?? '';
$show_all_sections          = $attributes['showAllSections'] ?? true;
$display_type               = $attributes['displayType'] ?? 'grid';
$columns                    = $attributes['columns'] ?? 2;
$item_spacing               = $attributes['itemSpacing'] ?? 20;
$title_color                = $attributes['titleColor'] ?? '#000000';
$section_title_color        = $attributes['sectionTitleColor'] ?? '#000000';
$section_title_underline_color = $attributes['sectionTitleUnderlineColor'] ?? '';
$price_color                = $attributes['priceColor'] ?? '#000000';
$description_color          = $attributes['descriptionColor'] ?? '#757575';
$background_color           = $attributes['backgroundColor'] ?? '';
$border_radius              = $attributes['borderRadius'] ?? 0;
$border_width               = $attributes['borderWidth'] ?? 0;
$border_color               = $attributes['borderColor'] ?? '';
$box_shadow                 = $attributes['boxShadow'] ?? false;
$hover_effect               = $attributes['hoverEffect'] ?? 'none';
$show_images                = $attributes['showImages'] ?? true;
$show_prices                = $attributes['showPrices'] ?? true;
$show_descriptions          = $attributes['showDescriptions'] ?? true;
$show_menu_description      = $attributes['showMenuDescription'] ?? true;
$show_section_descriptions  = $attributes['showSectionDescriptions'] ?? true;
$show_menu_title            = $attributes['showMenuTitle'] ?? true;
$menu_title_alignment       = $attributes['menuTitleAlignment'] ?? 'center';
$section_title_alignment    = $attributes['sectionTitleAlignment'] ?? 'center';
$menu_description_alignment = $attributes['menuDescriptionAlignment'] ?? 'center';
$section_description_alignment = $attributes['sectionDescriptionAlignment'] ?? 'center';
$image_size_grid            = $attributes['imageSizeGrid'] ?? 200;
$image_size_list            = $attributes['imageSizeList'] ?? 90;
$image_square               = $attributes['imageSquare'] ?? true;
$list_image_alignment       = $attributes['listImageAlignment'] ?? 'center';
$class_name                 = $attributes['className'] ?? '';
$align                      = $attributes['align'] ?? '';

// Attributi di spaziatura
$image_margin         = $attributes['imageMargin'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
$image_padding        = $attributes['imagePadding'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
$title_margin         = $attributes['titleMargin'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
$price_margin         = $attributes['priceMargin'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
$description_margin   = $attributes['descriptionMargin'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
$content_padding      = $attributes['contentPadding'] ?? ['top' => 15, 'right' => 15, 'bottom' => 15, 'left' => 15];

// Prepara le classi per il container del menu
$container_class = 'erm-menu-container';

// Aggiungi la classe wp-block per garantire compatibilità con Gutenberg
$container_class .= ' wp-block-easy-restaurant-menu-restaurant-menu';

// Aggiungi classe di allineamento se specificata
if (!empty($align) && in_array($align, ['wide', 'full'])) {
    $container_class .= ' align' . $align;
}

if ($display_type) {
    $container_class .= ' erm-display-' . $display_type;
}
if (!empty($class_name)) {
    $container_class .= ' ' . $class_name;
}

// Aggiungi classe per l'allineamento delle immagini nella vista lista
if ($display_type === 'list' && $show_images) {
    $container_class .= ' erm-list-align-' . $list_image_alignment;
}

// Ottieni l'oggetto wpdb globale
global $wpdb;

// Output iniziale
$output = '';

// Verifica che l'oggetto database sia disponibile e che il menu_id sia impostato
if (!is_object($wpdb) || empty($menu_id)) {
    if (empty($menu_id)) {
        return '<p>' . esc_html__('Seleziona un menu dal pannello laterale.', 'easy-restaurant-menu') . '</p>';
    }
    return '<p>' . esc_html__('Database non disponibile.', 'easy-restaurant-menu') . '</p>';
}

// Ottieni il prefisso della tabella
$table_name_menus = $wpdb->prefix . 'erm_menus';
$table_name_sections = $wpdb->prefix . 'erm_sections';
$table_name_items = $wpdb->prefix . 'erm_items';

// Verifica se le tabelle esistono
$menus_table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name_menus}'") === $table_name_menus;
$sections_table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name_sections}'") === $table_name_sections;
$items_table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name_items}'") === $table_name_items;

if (!$menus_table_exists || !$sections_table_exists || !$items_table_exists) {
    return '<p>' . esc_html__('Le tabelle del database necessarie non esistono.', 'easy-restaurant-menu') . '</p>';
}

// Verifica che il menu specificato esista ed è pubblicato
$menu = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT * FROM {$table_name_menus} WHERE id = %d AND status = %s",
        $menu_id,
        'publish'
    )
);

if (!$menu) {
    return '<p>' . esc_html__('Il menu selezionato non esiste o non è pubblicato.', 'easy-restaurant-menu') . '</p>';
}

// Genera un ID univoco per questo blocco per evitare conflitti con altri blocchi sulla stessa pagina
$block_id = 'erm-menu-' . uniqid();

// PRIMA FASE: Ottieni i valori del preset se disponibile
$preset = erm_get_style_preset();
$preset_values = [];

if ($preset) {
    $preset_values = [
        'title_color' => $preset['menu_title_color'],
        'section_title_color' => $preset['section_title_color'],
        'price_color' => $preset['price_color'],
        'description_color' => $preset['description_color'],
        'background_color' => $preset['background_color'],
        'border_color' => $preset['border_color'],
        'border_radius' => $preset['border_radius'],
        'text_alignment' => $preset['text_alignment'],
        'font_size_title' => $preset['font_size_title'],
        'font_size_description' => $preset['font_size_description'],
        'spacing' => $preset['spacing']
    ];
}

// SECONDA FASE: Utilizza i valori del blocco se specificati, altrimenti usa quelli del preset
$title_color = !empty($attributes['titleColor']) ? $attributes['titleColor'] : ($preset_values['title_color'] ?? '#1f2937');
$section_title_color = !empty($attributes['sectionTitleColor']) ? $attributes['sectionTitleColor'] : ($preset_values['section_title_color'] ?? '#1f2937');
$section_title_underline_color = !empty($attributes['sectionTitleUnderlineColor']) ? $attributes['sectionTitleUnderlineColor'] : $section_title_color;
$price_color = !empty($attributes['priceColor']) ? $attributes['priceColor'] : ($preset_values['price_color'] ?? '#2e7d32');
$description_color = !empty($attributes['descriptionColor']) ? $attributes['descriptionColor'] : ($preset_values['description_color'] ?? '#6b7280');
$background_color = !empty($attributes['backgroundColor']) ? $attributes['backgroundColor'] : ($preset_values['background_color'] ?? '#ffffff');
$border_color = !empty($attributes['borderColor']) ? $attributes['borderColor'] : ($preset_values['border_color'] ?? '');
$border_radius = isset($attributes['borderRadius']) && $attributes['borderRadius'] > 0 ? $attributes['borderRadius'] : ($preset_values['border_radius'] ?? 8);

// Gestisci esplicitamente gli allineamenti per dare priorità alle impostazioni del blocco
$menu_title_alignment = $attributes['menuTitleAlignment'] ?? ($preset_values['text_alignment'] ?? 'center');
$section_title_alignment = $attributes['sectionTitleAlignment'] ?? ($preset_values['text_alignment'] ?? 'center');
$menu_description_alignment = $attributes['menuDescriptionAlignment'] ?? ($preset_values['text_alignment'] ?? 'center');
$section_description_alignment = $attributes['sectionDescriptionAlignment'] ?? ($preset_values['text_alignment'] ?? 'center');

// TERZA FASE: Generazione CSS con priorità corretta
$style = '';

// Stile base per il layout
if ($display_type === 'grid') {
    $style .= "

    @media screen and (max-width: 480px) {
     #{$block_id}.erm-menu-container.erm-display-grid .erm-menu-items {
            display: grid;
            grid-template-columns:  1fr !important;
            gap: {$item_spacing}px;
        }
    }
        #{$block_id}.erm-menu-container.erm-display-grid .erm-menu-items {
            display: grid;
            grid-template-columns: repeat({$columns}, 1fr);
            gap: {$item_spacing}px;
        }
    ";
} else {
    $style .= "
        #{$block_id}.erm-menu-container.erm-display-list .erm-menu-item + .erm-menu-item {
            margin-top: {$item_spacing}px;
        }
        
        #{$block_id}.erm-menu-container.erm-display-list .erm-menu-item {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
        }
        
        #{$block_id}.erm-menu-container.erm-display-list.erm-list-align-center .erm-menu-item {
            align-items: center;
        }
        
        #{$block_id}.erm-menu-container.erm-display-list.erm-list-align-top .erm-menu-item {
            align-items: flex-start;
        }
        
        #{$block_id}.erm-menu-container.erm-display-list .erm-item-image {
            min-width: {$image_size_list}px;
            margin-right: 20px;
        }
    ";
}

// Aggiungi stile per distanziare le sezioni nel layout completo
$style .= "
    #{$block_id} .erm-section {
        margin-bottom: 50px;
    }
    #{$block_id} .erm-section:last-child {
        margin-bottom: 0;
    }
";

// Stili per i colori dei testi - con specificità migliorata e applicazione diretta degli allineamenti
$style .= "
    #{$block_id} .erm-item-title {
        color: {$title_color};
    }
    
    /* Stili per il titolo della sezione - con specificità aumentata */
    #{$block_id} .erm-section-title {
        color: {$section_title_color};
        text-align: {$section_title_alignment} !important; /* Forza l'allineamento */
        position: relative;
        padding-bottom: 0.8em;
        font-size: 2em;
        font-weight: 700;
        margin-bottom: 1.5em;
    }
    
    /* Pseudo-elemento con regole specifiche per ogni allineamento */
    #{$block_id} .erm-section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        height: 3px;
        width: 60px;
        background-color: {$section_title_underline_color};
    }
    
    /* Gestisci separatamente ogni caso di allineamento per lo pseudo-elemento */
    #{$block_id} .erm-section-title[style*='text-align: left']::after,
    #{$block_id} .erm-section-title[style*='text-align:left']::after {
        left: 0;
        transform: none;
    }
    
    #{$block_id} .erm-section-title[style*='text-align: right']::after,
    #{$block_id} .erm-section-title[style*='text-align:right']::after {
        right: 0;
        left: auto;
        transform: none;
    }
    
    #{$block_id} .erm-section-title[style*='text-align: center']::after,
    #{$block_id} .erm-section-title[style*='text-align:center']::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    #{$block_id} .erm-item-price {
        color: {$price_color};
    }
    
    #{$block_id} .erm-item-description, 
    #{$block_id} .erm-section-description {
        color: {$description_color};
    }
    
    /* Stili predefiniti per garantire un aspetto professionale */
    #{$block_id} {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
    }
    
    #{$block_id} .erm-menu-title {
        font-size: 2.5em;
        font-weight: 700;
        text-align: {$menu_title_alignment} !important; /* Forza l'allineamento */
        margin-bottom: 0.5em;
        line-height: 1.2;
    }
    
    #{$block_id} .erm-menu-description {
        text-align: {$menu_description_alignment} !important; /* Forza l'allineamento */
        font-size: 1.1em;
        max-width: 800px;
        margin: 0 auto 2.5em;
        line-height: 1.6;
    }
    
    #{$block_id} .erm-section-description {
        text-align: {$section_description_alignment} !important; /* Forza l'allineamento */
        font-size: 1.05em;
        max-width: 800px;
        margin: -1em auto 2em;
        line-height: 1.6;
    }
";

// Continua con il resto degli stili...
$style .= "
    #{$block_id} .erm-menu-item {
        background-color: {$background_color};
        border-radius: {$border_radius}px;
        " . ($box_shadow ? "box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);" : "") . "
        transition: all 0.3s ease;
        overflow: hidden;
        " . ($border_width > 0 && !empty($border_color) ? "border: {$border_width}px solid {$border_color};" : "") . "
    }
    
    " . ($hover_effect !== 'none' ? "#{$block_id} .erm-menu-item:hover {
        " . ($hover_effect === 'shadow' ? "box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-3px);" : "") . "
        " . ($hover_effect === 'border' ? "border-color: {$title_color} !important;
        border-width: 1px;
        border-style: solid;" : "") . "
    }" : "") . "
    
    #{$block_id} .erm-item-image {
        overflow: hidden;
        border-radius: {$border_radius}px {$border_radius}px 0 0;
    }
    
    #{$block_id} .erm-item-image img {
        transition: transform 0.3s ease;
    }
    
    " . ($hover_effect === 'zoom' ? "#{$block_id} .erm-hover-zoom:hover .erm-item-image img {
        transform: scale(1.08);
    }" : "") . "
    
    #{$block_id} .erm-item-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }
    
    #{$block_id} .erm-item-title {
        font-weight: 600;
        font-size: 1.25em;
        line-height: 1.3;
        margin: 0;
    }
    
    #{$block_id} .erm-item-price {
        font-weight: 700;
        white-space: nowrap;
        font-size: 1.1em;
        margin-left: 1.5em;
    }
    
    #{$block_id} .erm-item-description {
        font-size: 0.95em;
        line-height: 1.6;
    }
    
    #{$block_id} .erm-no-items {
        text-align: center;
        padding: 30px;
        background-color: #f9f9f9;
        border-radius: {$border_radius}px;
        font-style: italic;
    }
";

// Stile specifico per il layout a griglia
$style .= "
    #{$block_id}.erm-display-grid .erm-menu-items {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: {$item_spacing}px;
    }
    
    #{$block_id}.erm-display-grid .erm-menu-item {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    #{$block_id}.erm-display-grid .erm-item-image {
        width: 100%;
        height: {$image_size_grid}px;
    }
    
    #{$block_id}.erm-display-grid .erm-item-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    #{$block_id}.erm-display-grid .erm-item-description {
        flex: 1;
    }
";

// Stile specifico per il layout a lista
$style .= "
    #{$block_id}.erm-display-list .erm-menu-items {
        display: flex;
        flex-direction: column;
        gap: {$item_spacing}px;
    }
    
    #{$block_id}.erm-display-list .erm-menu-item {
        display: flex;
        border-radius: {$border_radius}px;
    }
    
    #{$block_id}.erm-display-list .erm-item-image {
        border-radius: {$border_radius}px 0 0 {$border_radius}px;
        width: {$image_size_list}px;
        height: {$image_size_list}px;
        margin-right: 15px;
    }
    
    #{$block_id}.erm-display-list .erm-item-content {
        width: 100%;
        flex: 1;
    }
    
    #{$block_id}.erm-display-list .erm-item-header {
        justify-content: space-between;
        width: 100%;
    }
    
    #{$block_id}.erm-display-list .erm-item-title {
        padding-right: 15px;
        margin-right: auto;
        flex: 1;
    }
    
    #{$block_id}.erm-display-list .erm-item-price {
        margin-left: 30px;
        text-align: right;
    }
";

// Aggiungi stili di spaziatura specifici
if (erm_has_spacing($content_padding)) {
    $style .= "
        #{$block_id} .erm-item-content {
            padding: " . erm_spacing_value($content_padding) . ";
        }
    ";
}

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
        #{$block_id} .erm-item-title, #{$block_id} .erm-section-title {
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
        #{$block_id} .erm-item-description, #{$block_id} .erm-section-description {
            margin: " . erm_spacing_value($description_margin) . ";
        }
    ";
}

// Stili responsive per dispositivi mobili
$style .= "
    @media screen and (max-width: 576px) {
        #{$block_id}.erm-menu-container.erm-display-list .erm-menu-item {
            flex-direction: column;
        }
        
        #{$block_id}.erm-menu-container.erm-display-list .erm-item-image {
            width: 100%;
            margin-right: 0;
            margin-bottom: 15px;
        }
        
        #{$block_id}.erm-menu-container.erm-display-list .erm-item-content {
            width: 100%;
        }
    }
";

// Aggiungi stili specifici dal preset se disponibile
if ($preset) {
    $style .= "
        #{$block_id} {
            --erm-title-font-size: {$preset['font_size_title']}em;
            --erm-description-font-size: {$preset['font_size_description']}em;
        }
        
        #{$block_id} .erm-item-title, 
        #{$block_id} .erm-menu-title {
            font-size: var(--erm-title-font-size);
        }
        
        #{$block_id} .erm-item-description,
        #{$block_id} .erm-section-description,
        #{$block_id} .erm-menu-description {
            font-size: var(--erm-description-font-size);
        }
    ";
}

// Inizio del contenitore del menu con stili inline
$output .= '<div id="' . esc_attr($block_id) . '" class="' . esc_attr($container_class) . '">';
$output .= '<style>' . $style . '</style>';

// Se l'opzione showAllSections è attiva o non c'è section_id specificato
if ($show_all_sections || empty($section_id)) {
    // Recupera tutte le sezioni per questo menu
    $sections = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$table_name_sections} WHERE menu_id = %d AND status = %s ORDER BY ordine ASC",
            $menu_id,
            'publish'
        )
    );

    if (empty($sections)) {
        $output .= '<p>' . esc_html__('Questo menu non ha sezioni.', 'easy-restaurant-menu') . '</p>';
    } else {
        // Titolo del menu
        if ($show_menu_title && !empty($menu->nome)) {
            $output .= '<h2 class="erm-menu-title">' . esc_html($menu->nome) . '</h2>';
        }
        
        // Descrizione del menu
        if ($show_menu_description && !empty($menu->descrizione)) {
            $output .= '<div class="erm-menu-description">' . wp_kses_post($menu->descrizione) . '</div>';
        }
        
        // Itera sulle sezioni
        foreach ($sections as $current_section) {
            $output .= '<div class="erm-section">';
            
            // Titolo della sezione
            if (!empty($current_section->nome)) {
                $output .= '<h3 class="erm-section-title" style="text-align: ' . esc_attr($section_title_alignment) . ';">' . esc_html($current_section->nome) . '</h3>';
            }
            
            // Descrizione della sezione
            if ($show_section_descriptions && !empty($current_section->descrizione)) {
                $output .= '<div class="erm-section-description" style="text-align: ' . esc_attr($section_description_alignment) . ';">' . wp_kses_post($current_section->descrizione) . '</div>';
            }
            
            // Recupera gli elementi per questa sezione
            $items = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$table_name_items} WHERE section_id = %d AND status = %s ORDER BY ordine ASC",
                    $current_section->id,
                    'publish'
                )
            );

            if (empty($items)) {
                $output .= '<div class="erm-no-items">' . esc_html__('Questa sezione non ha elementi.', 'easy-restaurant-menu') . '</div>';
            } else {
                $output .= '<div class="erm-menu-items">';
                
                // Itera sugli elementi
                foreach ($items as $item) {
                    $item_classes = ['erm-menu-item'];
                    if ($hover_effect !== 'none') {
                        $item_classes[] = 'erm-hover-' . $hover_effect;
                    }
                    
                    $output .= '<div class="' . esc_attr(implode(' ', $item_classes)) . '">';
                    
                    // Immagine dell'elemento
                    if ($show_images && !empty($item->immagine)) {
                        $image_url = wp_get_attachment_image_src($item->immagine, 'medium');
                        if ($image_url) {
                            $img_class = 'erm-item-image';
                            $img_style = '';
                            
                            if ($display_type === 'grid') {
                                $img_class .= $image_square ? ' erm-image-square' : ' erm-image-rect';
                                $img_style = "height: {$image_size_grid}px;";
                            } else {
                                $img_class .= $image_square ? ' erm-image-square' : ' erm-image-rect';
                                $img_style = "height: {$image_size_list}px; width: {$image_size_list}px;";
                            }
                            
                            $output .= '<div class="' . esc_attr($img_class) . '" style="' . esc_attr($img_style) . '">';
                            $output .= '<img src="' . esc_url($image_url[0]) . '" alt="' . esc_attr($item->titolo) . '" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">';
                            $output .= '</div>';
                        }
                    }
                    
                    $output .= '<div class="erm-item-content">';
                    $output .= '<div class="erm-item-header">';
                    
                    // Titolo dell'elemento
                    $output .= '<h4 class="erm-item-title">' . esc_html($item->titolo) . '</h4>';
                    
                    // Prezzo dell'elemento
                    if ($show_prices && !empty($item->prezzo)) {
                        $formatted_price = erm_format_price($item->prezzo);
                        $output .= '<span class="erm-item-price">' . $formatted_price . '</span>';
                    }
                    
                    $output .= '</div>'; // Fine header
                    
                    // Descrizione dell'elemento
                    if ($show_descriptions && !empty($item->descrizione)) {
                        $output .= '<div class="erm-item-description">' . wp_kses_post($item->descrizione) . '</div>';
                    }
                    
                    $output .= '</div>'; // Fine content
                    $output .= '</div>'; // Fine item
                }
                
                $output .= '</div>'; // Fine items
            }
            
            $output .= '</div>'; // Fine section
        }
    }
} else {
    // Visualizza solo una sezione specifica
    $section = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$table_name_sections} WHERE id = %d AND status = %s",
            $section_id,
            'publish'
        )
    );
    
    if (empty($section)) {
        $output .= '<p>' . esc_html__('Sezione non trovata.', 'easy-restaurant-menu') . '</p>';
    } else {
        // Titolo del menu
        if ($show_menu_title && !empty($menu->nome)) {
            $output .= '<h2 class="erm-menu-title" style="text-align: ' . esc_attr($menu_title_alignment) . ';">' . esc_html($menu->nome) . '</h2>';
        }
        
        // Descrizione del menu
        if ($show_menu_description && !empty($menu->descrizione)) {
            $output .= '<div class="erm-menu-description" style="text-align: ' . esc_attr($menu_description_alignment) . ';">' . wp_kses_post($menu->descrizione) . '</div>';
        }
        
        $output .= '<div class="erm-section">';
        
        // Titolo della sezione
        if (!empty($section->nome)) {
            $output .= '<h3 class="erm-section-title" style="text-align: ' . esc_attr($section_title_alignment) . ';">' . esc_html($section->nome) . '</h3>';
        }
        
        // Descrizione della sezione
        if ($show_section_descriptions && !empty($section->descrizione)) {
            $output .= '<div class="erm-section-description" style="text-align: ' . esc_attr($section_description_alignment) . ';">' . wp_kses_post($section->descrizione) . '</div>';
        }
        
        // Recupera gli elementi per questa sezione
        $items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name_items} WHERE section_id = %d AND status = %s ORDER BY ordine ASC",
                $section_id,
                'publish'
            )
        );
        
        if (empty($items)) {
            $output .= '<div class="erm-no-items">' . esc_html__('Questa sezione non ha elementi.', 'easy-restaurant-menu') . '</div>';
        } else {
            $output .= '<div class="erm-menu-items">';
            
            // Itera sugli elementi
            foreach ($items as $item) {
                $item_classes = ['erm-menu-item'];
                if ($hover_effect !== 'none') {
                    $item_classes[] = 'erm-hover-' . $hover_effect;
                }
                
                $output .= '<div class="' . esc_attr(implode(' ', $item_classes)) . '">';
                
                // Immagine dell'elemento
                if ($show_images && !empty($item->immagine)) {
                    $image_url = wp_get_attachment_image_src($item->immagine, 'medium');
                    if ($image_url) {
                        $img_class = 'erm-item-image';
                        $img_style = '';
                        
                        if ($display_type === 'grid') {
                            $img_class .= $image_square ? ' erm-image-square' : ' erm-image-rect';
                            $img_style = "height: {$image_size_grid}px;";
                        } else {
                            $img_class .= $image_square ? ' erm-image-square' : ' erm-image-rect';
                            $img_style = "height: {$image_size_list}px; width: {$image_size_list}px;";
                        }
                        
                        $output .= '<div class="' . esc_attr($img_class) . '" style="' . esc_attr($img_style) . '">';
                        $output .= '<img src="' . esc_url($image_url[0]) . '" alt="' . esc_attr($item->titolo) . '" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">';
                        $output .= '</div>';
                    }
                }
                
                $output .= '<div class="erm-item-content">';
                $output .= '<div class="erm-item-header">';
                
                // Titolo dell'elemento
                $output .= '<h4 class="erm-item-title">' . esc_html($item->titolo) . '</h4>';
                
                // Prezzo dell'elemento
                if ($show_prices && !empty($item->prezzo)) {
                    $formatted_price = erm_format_price($item->prezzo);
                    $output .= '<span class="erm-item-price">' . $formatted_price . '</span>';
                }
                
                $output .= '</div>'; // Fine header
                
                // Descrizione dell'elemento
                if ($show_descriptions && !empty($item->descrizione)) {
                    $output .= '<div class="erm-item-description">' . wp_kses_post($item->descrizione) . '</div>';
                }
                
                $output .= '</div>'; // Fine content
                $output .= '</div>'; // Fine item
            }
            
            $output .= '</div>'; // Fine items
        }
        
        $output .= '</div>'; // Fine section
    }
}

$output .= '</div>'; // Fine container

// Aggiungi funzione di fallback per wp_get_attachment_image_src se non esiste
if (!function_exists('wp_get_attachment_image_src')) {
    function wp_get_attachment_image_src($attachment_id, $size = 'thumbnail') {
        return null; // Implementazione semplificata
    }
}

return $output; 