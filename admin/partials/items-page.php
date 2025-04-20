<?php
/**
 * Template per la gestione degli elementi del menu
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/admin/partials
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;

// Ottieni l'ID della sezione corrente (se specificato)
$current_section_id = isset($_GET['section']) ? intval($_GET['section']) : 0;
$current_menu_id = isset($_GET['menu']) ? intval($_GET['menu']) : 0;

// Recupera tutti i menu
$table_menus = $wpdb->prefix . 'erm_menus';
$menus = $wpdb->get_results("SELECT * FROM $table_menus WHERE status = 'publish' ORDER BY ordine ASC", ARRAY_A);

// Se c'è almeno un menu e non è stato specificato un menu corrente, usa il primo
if (empty($current_menu_id) && !empty($menus)) {
    $current_menu_id = $menus[0]['id'];
}

// Recupera tutte le sezioni del menu selezionato
$table_sections = $wpdb->prefix . 'erm_sections';
$sections = [];
$current_menu = null;

if ($current_menu_id > 0) {
    $sections = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_sections WHERE menu_id = %d AND status = 'publish' ORDER BY ordine ASC", $current_menu_id),
        ARRAY_A
    );
    
    // Recupera i dettagli del menu corrente
    $current_menu = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_menus WHERE id = %d", $current_menu_id),
        ARRAY_A
    );
    
    // Se c'è almeno una sezione e non è stata specificata una sezione corrente, usa la prima
    if (empty($current_section_id) && !empty($sections)) {
        $current_section_id = $sections[0]['id'];
    }
}

// Recupera gli elementi della sezione corrente
$table_items = $wpdb->prefix . 'erm_items';
$items = [];
$current_section = null;

if ($current_section_id > 0) {
    $items = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_items WHERE section_id = %d ORDER BY ordine ASC", $current_section_id),
        ARRAY_A
    );
    
    // Recupera i dettagli della sezione corrente
    $current_section = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_sections WHERE id = %d", $current_section_id),
        ARRAY_A
    );
}
?>

<div class="wrap erm-admin">
    <h1 class="wp-heading-inline"><?php echo esc_html__('Gestione Elementi Menu', 'easy-restaurant-menu'); ?></h1>
    
    <?php if ($current_section_id > 0) : ?>
        <a href="#" class="page-title-action add-new-item"><?php echo esc_html__('Aggiungi Nuovo Elemento', 'easy-restaurant-menu'); ?></a>
    <?php endif; ?>
    
    <hr class="wp-header-end">
    
    <?php if (empty($menus)) : ?>
        <div class="notice notice-warning">
            <p>
                <?php echo esc_html__('Non ci sono menu disponibili. ', 'easy-restaurant-menu'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=erm-menus')); ?>"><?php echo esc_html__('Crea un menu', 'easy-restaurant-menu'); ?></a> 
                <?php echo esc_html__('prima di aggiungere elementi.', 'easy-restaurant-menu'); ?>
            </p>
        </div>
    <?php else : ?>
        <!-- Selezione del menu -->
        <div class="erm-menus-tabs">
            <h2 class="screen-reader-text"><?php echo esc_html__('Filtro per menu', 'easy-restaurant-menu'); ?></h2>
            <ul class="subsubsub">
                <?php 
                $total_menus = count($menus);
                foreach ($menus as $index => $menu) : 
                    $is_current = ($menu['id'] == $current_menu_id);
                    $separator = ($index < $total_menus - 1) ? ' | ' : '';
                ?>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=erm-items&menu=' . $menu['id'])); ?>" class="<?php echo $is_current ? 'current' : ''; ?>">
                            <?php echo esc_html($menu['nome']); ?>
                        </a><?php echo esc_html($separator); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <?php if (empty($sections)) : ?>
            <div class="notice notice-warning">
                <p>
                    <?php echo esc_html__('Non ci sono sezioni disponibili in questo menu. ', 'easy-restaurant-menu'); ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=erm-sections&menu=' . $current_menu_id)); ?>"><?php echo esc_html__('Crea una sezione', 'easy-restaurant-menu'); ?></a> 
                    <?php echo esc_html__('prima di aggiungere elementi al menu.', 'easy-restaurant-menu'); ?>
                </p>
            </div>
        <?php else : ?>
            <!-- Selezione della sezione all'interno del menu -->
            <div class="erm-sections-tabs">
                <h2 class="screen-reader-text"><?php echo esc_html__('Filtro per sezione', 'easy-restaurant-menu'); ?></h2>
                <ul class="subsubsub">
                    <?php 
                    $total_sections = count($sections);
                    foreach ($sections as $index => $section) : 
                        $is_current = ($section['id'] == $current_section_id);
                        $separator = ($index < $total_sections - 1) ? ' | ' : '';
                    ?>
                        <li>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=erm-items&menu=' . $current_menu_id . '&section=' . $section['id'])); ?>" class="<?php echo $is_current ? 'current' : ''; ?>">
                                <?php echo esc_html($section['nome']); ?>
                            </a><?php echo esc_html($separator); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?php if ($current_section_id > 0) : ?>
                <div class="notice notice-info is-dismissible">
                    <p>
                        <?php 
                        echo sprintf(
                            esc_html__('Stai visualizzando gli elementi della sezione "%s" del menu "%s". Trascina gli elementi per riordinare. Clicca su un elemento per modificarlo.', 'easy-restaurant-menu'),
                            esc_html($current_section['nome']),
                            esc_html($current_menu['nome'])
                        ); 
                        ?>
                    </p>
                </div>
                
                <div class="erm-form-container" style="display: none;">
                    <div class="erm-form-header">
                        <h2 id="erm-form-title"><?php echo esc_html__('Aggiungi Nuovo Elemento', 'easy-restaurant-menu'); ?></h2>
                        <button type="button" class="erm-close-form">&times;</button>
                    </div>
                    
                    <form id="erm-item-form">
                        <input type="hidden" id="erm-item-id" name="id" value="0">
                        <input type="hidden" id="erm-item-section-id" name="section_id" value="<?php echo esc_attr($current_section_id); ?>">
                        <input type="hidden" id="erm-item-menu-id" name="menu_id" value="<?php echo esc_attr($current_menu_id); ?>">
                        
                        <div class="erm-form-group">
                            <label for="erm-item-titolo"><?php echo esc_html__('Nome Elemento', 'easy-restaurant-menu'); ?></label>
                            <input type="text" id="erm-item-titolo" name="titolo" required>
                        </div>
                        
                        <div class="erm-form-group">
                            <label for="erm-item-descrizione"><?php echo esc_html__('Descrizione', 'easy-restaurant-menu'); ?></label>
                            <textarea id="erm-item-descrizione" name="descrizione" rows="3"></textarea>
                        </div>
                        
                        <div class="erm-form-row">
                            <div class="erm-form-group">
                                <label for="erm-item-prezzo"><?php echo esc_html__('Prezzo', 'easy-restaurant-menu'); ?></label>
                                <input type="number" id="erm-item-prezzo" name="prezzo" min="0" step="0.01" value="0">
                            </div>
                            
                            <div class="erm-form-group">
                                <label for="erm-item-ordine"><?php echo esc_html__('Ordine', 'easy-restaurant-menu'); ?></label>
                                <input type="number" id="erm-item-ordine" name="ordine" min="0" step="1" value="0">
                            </div>
                            
                            <div class="erm-form-group">
                                <label for="erm-item-status"><?php echo esc_html__('Stato', 'easy-restaurant-menu'); ?></label>
                                <select id="erm-item-status" name="status">
                                    <option value="publish"><?php echo esc_html__('Pubblicato', 'easy-restaurant-menu'); ?></option>
                                    <option value="draft"><?php echo esc_html__('Bozza', 'easy-restaurant-menu'); ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="erm-form-group">
                            <label for="erm-item-immagine"><?php echo esc_html__('Immagine', 'easy-restaurant-menu'); ?></label>
                            <div class="erm-media-uploader">
                                <div id="erm-item-immagine-preview" class="erm-image-preview"></div>
                                <input type="hidden" id="erm-item-immagine" name="immagine" value="0">
                                <button type="button" class="button erm-upload-image"><?php echo esc_html__('Seleziona Immagine', 'easy-restaurant-menu'); ?></button>
                                <button type="button" class="button erm-remove-image" style="display: none;"><?php echo esc_html__('Rimuovi Immagine', 'easy-restaurant-menu'); ?></button>
                            </div>
                        </div>
                        
                        <div class="erm-form-actions">
                            <button type="submit" class="button button-primary"><?php echo esc_html__('Salva Elemento', 'easy-restaurant-menu'); ?></button>
                            <button type="button" class="button erm-delete-item" style="display: none; float: right; color: #a00;"><?php echo esc_html__('Elimina', 'easy-restaurant-menu'); ?></button>
                        </div>
                    </form>
                </div>
                
                <div class="erm-items-container">
                    <?php if (empty($items)) : ?>
                        <div class="erm-no-items">
                            <p><?php echo esc_html__('Nessun elemento trovato in questa sezione. Aggiungi un nuovo elemento per iniziare.', 'easy-restaurant-menu'); ?></p>
                        </div>
                    <?php else : ?>
                        <div class="erm-items-list sortable">
                            <?php foreach ($items as $item) : 
                                $immagine_url = '';
                                if (!empty($item['immagine'])) {
                                    $immagine = wp_get_attachment_image_src($item['immagine'], 'thumbnail');
                                    if ($immagine) {
                                        $immagine_url = $immagine[0];
                                    }
                                }
                            ?>
                                <div class="erm-item-card" data-id="<?php echo esc_attr($item['id']); ?>">
                                    <div class="erm-item-header">
                                        <div class="erm-item-title-container">
                                            <h3 class="erm-item-title"><?php echo esc_html($item['titolo']); ?></h3>
                                            <div class="erm-item-price"><?php echo esc_html(number_format((float)$item['prezzo'], 2, ',', ' ')); ?> €</div>
                                        </div>
                                        <div class="erm-item-actions">
                                            <span class="erm-status <?php echo esc_attr($item['status']); ?>"><?php echo esc_html($item['status'] === 'publish' ? __('Pubblicato', 'easy-restaurant-menu') : __('Bozza', 'easy-restaurant-menu')); ?></span>
                                            <span class="dashicons dashicons-menu erm-handle"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="erm-item-content">
                                        <?php if ($immagine_url) : ?>
                                            <div class="erm-item-image">
                                                <img src="<?php echo esc_url($immagine_url); ?>" alt="<?php echo esc_attr($item['titolo']); ?>">
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="erm-item-details">
                                            <?php if (!empty($item['descrizione'])) : ?>
                                                <p class="erm-item-description"><?php echo esc_html($item['descrizione']); ?></p>
                                            <?php endif; ?>
                                            
                                            <div class="erm-item-meta">
                                                <span class="erm-item-id"><?php echo esc_html__('ID:', 'easy-restaurant-menu'); ?> <?php echo esc_html($item['id']); ?></span>
                                                <span class="erm-item-order"><?php echo esc_html__('Ordine:', 'easy-restaurant-menu'); ?> <?php echo esc_html($item['ordine']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="erm-item-data" style="display: none;">
                                        <span class="erm-data-id"><?php echo esc_attr($item['id']); ?></span>
                                        <span class="erm-data-section-id"><?php echo esc_attr($item['section_id']); ?></span>
                                        <span class="erm-data-titolo"><?php echo esc_attr($item['titolo']); ?></span>
                                        <span class="erm-data-descrizione"><?php echo esc_attr($item['descrizione']); ?></span>
                                        <span class="erm-data-prezzo"><?php echo esc_attr($item['prezzo']); ?></span>
                                        <span class="erm-data-immagine"><?php echo esc_attr($item['immagine']); ?></span>
                                        <span class="erm-data-ordine"><?php echo esc_attr($item['ordine']); ?></span>
                                        <span class="erm-data-status"><?php echo esc_attr($item['status']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Inizializza il drag and drop per riordinare gli elementi
    $('.sortable').sortable({
        handle: '.erm-handle',
        placeholder: 'erm-item-card-placeholder',
        update: function(event, ui) {
            // Raccogli gli ID ordinati
            const items = [];
            $('.erm-item-card').each(function(index) {
                items.push($(this).data('id'));
            });
            
            // Salva il nuovo ordine tramite AJAX
            $.ajax({
                url: erm_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'erm_update_order',
                    type: 'items',
                    items: items,
                    nonce: erm_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Mostra messaggio di conferma
                        showNotice('success', response.data.message);
                    } else {
                        showNotice('error', response.data.message);
                    }
                }
            });
        }
    });
    
    // Gestione form di aggiunta/modifica
    $('.add-new-item').on('click', function(e) {
        e.preventDefault();
        resetForm();
        $('.erm-form-container').fadeIn(200);
    });
    
    $('.erm-close-form').on('click', function() {
        $('.erm-form-container').fadeOut(200);
    });
    
    // Click su un elemento per modificarlo
    $('.erm-item-card').on('click', function(e) {
        // Se il click è sulla maniglia, non apriamo il form
        if ($(e.target).hasClass('erm-handle')) {
            return;
        }
        
        const $item = $(this);
        const $data = $item.find('.erm-item-data');
        
        // Riempi il form con i dati dell'elemento
        $('#erm-form-title').text('<?php echo esc_js(__('Modifica Elemento', 'easy-restaurant-menu')); ?>');
        $('#erm-item-id').val($data.find('.erm-data-id').text());
        $('#erm-item-section-id').val($data.find('.erm-data-section-id').text());
        $('#erm-item-titolo').val($data.find('.erm-data-titolo').text());
        $('#erm-item-descrizione').val($data.find('.erm-data-descrizione').text());
        $('#erm-item-prezzo').val($data.find('.erm-data-prezzo').text());
        
        const immagineId = parseInt($data.find('.erm-data-immagine').text(), 10);
        $('#erm-item-immagine').val(immagineId);
        
        if (immagineId > 0) {
            // Carica l'anteprima dell'immagine
            $.ajax({
                url: erm_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'erm_get_image',
                    image_id: immagineId,
                    nonce: erm_admin.nonce
                },
                success: function(response) {
                    if (response.success && response.data.url) {
                        $('#erm-item-immagine-preview').html('<img src="' + response.data.url + '" alt="" />');
                        $('.erm-remove-image').show();
                    }
                }
            });
        }
        
        $('#erm-item-ordine').val($data.find('.erm-data-ordine').text());
        $('#erm-item-status').val($data.find('.erm-data-status').text());
        
        // Mostra il pulsante elimina
        $('.erm-delete-item').show();
        
        // Mostra il form
        $('.erm-form-container').fadeIn(200);
    });
    
    // Gestione caricamento immagine
    let mediaUploader;
    
    $('.erm-upload-image').on('click', function(e) {
        e.preventDefault();
        
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        mediaUploader = wp.media({
            title: '<?php echo esc_js(__('Seleziona o carica un\'immagine', 'easy-restaurant-menu')); ?>',
            button: {
                text: '<?php echo esc_js(__('Usa questa immagine', 'easy-restaurant-menu')); ?>'
            },
            multiple: false
        });
        
        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#erm-item-immagine').val(attachment.id);
            $('#erm-item-immagine-preview').html('<img src="' + attachment.url + '" alt="" />');
            $('.erm-remove-image').show();
        });
        
        mediaUploader.open();
    });
    
    $('.erm-remove-image').on('click', function() {
        $('#erm-item-immagine').val('0');
        $('#erm-item-immagine-preview').html('');
        $(this).hide();
    });
    
    // Invio form
    $('#erm-item-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            action: 'erm_save_item',
            id: $('#erm-item-id').val(),
            section_id: $('#erm-item-section-id').val(),
            titolo: $('#erm-item-titolo').val(),
            descrizione: $('#erm-item-descrizione').val(),
            prezzo: $('#erm-item-prezzo').val(),
            immagine: $('#erm-item-immagine').val(),
            ordine: $('#erm-item-ordine').val(),
            status: $('#erm-item-status').val(),
            nonce: erm_admin.nonce
        };
        
        $.ajax({
            url: erm_admin.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data.message);
                    
                    // Ricarica la pagina per mostrare le modifiche
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotice('error', response.data.message);
                }
            }
        });
    });
    
    // Elimina elemento
    $('.erm-delete-item').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('<?php echo esc_js(__('Sei sicuro di voler eliminare questo elemento? Questa azione non può essere annullata.', 'easy-restaurant-menu')); ?>')) {
            return;
        }
        
        const id = $('#erm-item-id').val();
        
        $.ajax({
            url: erm_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'erm_delete_item',
                id: id,
                nonce: erm_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data.message);
                    
                    // Ricarica la pagina per mostrare le modifiche
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotice('error', response.data.message);
                }
            }
        });
    });
    
    // Funzione helper per mostrare notifiche
    function showNotice(type, message) {
        const notice = $(`<div class="notice notice-${type} is-dismissible"><p>${message}</p></div>`);
        
        $('.wrap > .notice').remove();
        $('.wrap').prepend(notice);
        
        // Auto-dismiss dopo 3 secondi
        setTimeout(function() {
            notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    // Resetta il form
    function resetForm() {
        $('#erm-form-title').text('<?php echo esc_js(__('Aggiungi Nuovo Elemento', 'easy-restaurant-menu')); ?>');
        $('#erm-item-id').val(0);
        $('#erm-item-titolo').val('');
        $('#erm-item-descrizione').val('');
        $('#erm-item-prezzo').val('0');
        $('#erm-item-immagine').val('0');
        $('#erm-item-immagine-preview').html('');
        $('#erm-item-ordine').val(<?php echo !empty($items) ? count($items) : 0; ?>);
        $('#erm-item-status').val('publish');
        $('.erm-delete-item, .erm-remove-image').hide();
    }
});
</script>

<style>
.erm-admin {
    max-width: 1200px;
}

.erm-sections-tabs {
    margin-bottom: 20px;
}

.erm-form-container {
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    margin: 20px 0;
    padding: 20px;
    position: relative;
}

.erm-form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 10px;
}

.erm-close-form {
    background: none;
    border: none;
    color: #a00;
    cursor: pointer;
    font-size: 20px;
    line-height: 1;
}

.erm-form-group {
    margin-bottom: 15px;
}

.erm-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.erm-form-group input[type="text"],
.erm-form-group input[type="number"],
.erm-form-group textarea,
.erm-form-group select {
    width: 100%;
    padding: 8px;
}

.erm-form-row {
    display: flex;
    margin: 0 -10px;
}

.erm-form-row .erm-form-group {
    flex: 1;
    padding: 0 10px;
}

.erm-form-actions {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.erm-media-uploader {
    display: flex;
    align-items: flex-end;
}

.erm-image-preview {
    width: 80px;
    height: 80px;
    margin-right: 10px;
    background: #f0f0f0;
    border: 1px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.erm-image-preview img {
    max-width: 100%;
    max-height: 100%;
}

.erm-remove-image {
    margin-left: 5px;
}

.erm-items-list {
    margin-top: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    grid-gap: 15px;
}

.erm-item-card {
    background: #fff;
    border: 1px solid #ddd;
    cursor: pointer;
    transition: all 0.2s ease;
}

.erm-item-card:hover {
    border-color: #999;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.erm-item-header {
    padding: 12px 15px;
    background: #f9f9f9;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.erm-item-title-container {
    display: flex;
    align-items: baseline;
    flex: 1;
}

.erm-item-title {
    margin: 0;
    font-size: 16px;
    margin-right: 10px;
}

.erm-item-price {
    font-weight: bold;
    color: #444;
}

.erm-item-actions {
    display: flex;
    align-items: center;
}

.erm-status {
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 12px;
    margin-right: 10px;
}

.erm-status.publish {
    background: #edf7ed;
    color: #388e3c;
}

.erm-status.draft {
    background: #fff3e0;
    color: #f57c00;
}

.erm-handle {
    cursor: move;
    color: #777;
}

.erm-item-content {
    padding: 12px 15px;
    display: flex;
}

.erm-item-image {
    width: 80px;
    height: 80px;
    margin-right: 15px;
    overflow: hidden;
    border: 1px solid #eee;
    flex-shrink: 0;
}

.erm-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.erm-item-details {
    flex: 1;
}

.erm-item-description {
    margin: 0 0 10px;
    color: #555;
    font-size: 13px;
}

.erm-item-meta {
    display: flex;
    justify-content: space-between;
    border-top: 1px solid #f0f0f0;
    padding-top: 8px;
    margin-top: 8px;
    font-size: 12px;
    color: #777;
}

.erm-no-items {
    text-align: center;
    padding: 40px 20px;
    background: #fff;
    border: 1px solid #ddd;
    grid-column: 1 / -1;
}

.erm-item-card-placeholder {
    border: 2px dashed #ddd;
    height: 150px;
    background: #f9f9f9;
}
</style> 