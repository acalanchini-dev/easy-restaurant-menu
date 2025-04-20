<?php
/**
 * Template per la gestione delle sezioni del menu
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/admin/partials
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Ottieni l'ID del menu corrente (se specificato)
$current_menu_id = isset($_GET['menu']) ? intval($_GET['menu']) : 0;

// Recupera tutti i menu
global $wpdb;
$table_menus = $wpdb->prefix . 'erm_menus';
$menus = $wpdb->get_results("SELECT * FROM $table_menus WHERE status = 'publish' ORDER BY ordine ASC", ARRAY_A);

// Se c'è almeno un menu e non è stato specificato un menu corrente, usa il primo
if (empty($current_menu_id) && !empty($menus)) {
    $current_menu_id = $menus[0]['id'];
}

// Recupera le sezioni del menu corrente
$table_sections = $wpdb->prefix . 'erm_sections';
$sections = [];
$current_menu = null;

if ($current_menu_id > 0) {
    $sections = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_sections WHERE menu_id = %d ORDER BY ordine ASC", $current_menu_id),
        ARRAY_A
    );
    
    // Recupera i dettagli del menu corrente
    $current_menu = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_menus WHERE id = %d", $current_menu_id),
        ARRAY_A
    );
}
?>

<div class="wrap erm-admin">
    <h1 class="wp-heading-inline"><?php echo esc_html__('Gestione Sezioni Menu', 'easy-restaurant-menu'); ?></h1>
    
    <?php if ($current_menu_id > 0) : ?>
        <a href="#" class="page-title-action add-new-section"><?php echo esc_html__('Aggiungi Nuova Sezione', 'easy-restaurant-menu'); ?></a>
    <?php endif; ?>
    
    <hr class="wp-header-end">
    
    <?php if (empty($menus)) : ?>
        <div class="notice notice-warning">
            <p>
                <?php echo esc_html__('Non ci sono menu disponibili. ', 'easy-restaurant-menu'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=erm-menus')); ?>"><?php echo esc_html__('Crea un menu', 'easy-restaurant-menu'); ?></a> 
                <?php echo esc_html__('prima di aggiungere sezioni.', 'easy-restaurant-menu'); ?>
            </p>
        </div>
    <?php else : ?>
        <div class="erm-menus-tabs">
            <ul class="subsubsub">
                <?php 
                $total_menus = count($menus);
                foreach ($menus as $index => $menu) : 
                    $is_current = ($menu['id'] == $current_menu_id);
                    $separator = ($index < $total_menus - 1) ? ' | ' : '';
                ?>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=erm-sections&menu=' . $menu['id'])); ?>" class="<?php echo $is_current ? 'current' : ''; ?>">
                            <?php echo esc_html($menu['nome']); ?>
                        </a><?php echo esc_html($separator); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if ($current_menu_id > 0) : ?>
            <div class="notice notice-info is-dismissible">
                <p>
                    <?php 
                    echo sprintf(
                        esc_html__('Stai visualizzando le sezioni del menu "%s". Trascina le sezioni per riordinare. Clicca su una sezione per modificarla.', 'easy-restaurant-menu'),
                        esc_html($current_menu['nome'])
                    ); 
                    ?>
                </p>
            </div>
            
            <div class="erm-form-container" style="display: none;">
                <div class="erm-form-header">
                    <h2 id="erm-form-title"><?php echo esc_html__('Aggiungi Nuova Sezione', 'easy-restaurant-menu'); ?></h2>
                    <button type="button" class="erm-close-form">&times;</button>
                </div>
                
                <form id="erm-section-form">
                    <input type="hidden" id="erm-section-id" name="id" value="0">
                    <input type="hidden" id="erm-section-menu-id" name="menu_id" value="<?php echo esc_attr($current_menu_id); ?>">
                    
                    <div class="erm-form-group">
                        <label for="erm-section-nome"><?php echo esc_html__('Nome Sezione', 'easy-restaurant-menu'); ?></label>
                        <input type="text" id="erm-section-nome" name="nome" required>
                    </div>
                    
                    <div class="erm-form-group">
                        <label for="erm-section-descrizione"><?php echo esc_html__('Descrizione', 'easy-restaurant-menu'); ?></label>
                        <textarea id="erm-section-descrizione" name="descrizione" rows="4"></textarea>
                    </div>
                    
                    <div class="erm-form-row">
                        <div class="erm-form-group">
                            <label for="erm-section-ordine"><?php echo esc_html__('Ordine', 'easy-restaurant-menu'); ?></label>
                            <input type="number" id="erm-section-ordine" name="ordine" min="0" step="1" value="0">
                        </div>
                        
                        <div class="erm-form-group">
                            <label for="erm-section-status"><?php echo esc_html__('Stato', 'easy-restaurant-menu'); ?></label>
                            <select id="erm-section-status" name="status">
                                <option value="publish"><?php echo esc_html__('Pubblicato', 'easy-restaurant-menu'); ?></option>
                                <option value="draft"><?php echo esc_html__('Bozza', 'easy-restaurant-menu'); ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="erm-form-actions">
                        <button type="submit" class="button button-primary"><?php echo esc_html__('Salva Sezione', 'easy-restaurant-menu'); ?></button>
                        <button type="button" class="button erm-delete-section" style="display: none; float: right; color: #a00;"><?php echo esc_html__('Elimina', 'easy-restaurant-menu'); ?></button>
                    </div>
                </form>
            </div>
            
            <div class="erm-sections-container">
                <?php if (empty($sections)) : ?>
                    <div class="erm-no-items">
                        <p><?php echo esc_html__('Nessuna sezione trovata in questo menu. Aggiungi una nuova sezione per iniziare.', 'easy-restaurant-menu'); ?></p>
                    </div>
                <?php else : ?>
                    <div class="erm-sections-list sortable">
                        <?php foreach ($sections as $section) : ?>
                            <div class="erm-section-item" data-id="<?php echo esc_attr($section['id']); ?>">
                                <div class="erm-section-header">
                                    <h3 class="erm-section-title"><?php echo esc_html($section['nome']); ?></h3>
                                    <div class="erm-section-actions">
                                        <span class="erm-status <?php echo esc_attr($section['status']); ?>"><?php echo esc_html($section['status'] === 'publish' ? __('Pubblicato', 'easy-restaurant-menu') : __('Bozza', 'easy-restaurant-menu')); ?></span>
                                        <span class="dashicons dashicons-menu erm-handle"></span>
                                    </div>
                                </div>
                                <div class="erm-section-details">
                                    <?php if (!empty($section['descrizione'])) : ?>
                                        <p class="erm-section-description"><?php echo esc_html($section['descrizione']); ?></p>
                                    <?php endif; ?>
                                    <div class="erm-section-meta">
                                        <span class="erm-section-id"><?php echo esc_html__('ID:', 'easy-restaurant-menu'); ?> <?php echo esc_html($section['id']); ?></span>
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=erm-items&section=' . $section['id'])); ?>" class="erm-view-items"><?php echo esc_html__('Visualizza Elementi', 'easy-restaurant-menu'); ?></a>
                                    </div>
                                </div>
                                <div class="erm-section-data" style="display: none;">
                                    <span class="erm-data-id"><?php echo esc_attr($section['id']); ?></span>
                                    <span class="erm-data-menu-id"><?php echo esc_attr($section['menu_id']); ?></span>
                                    <span class="erm-data-nome"><?php echo esc_attr($section['nome']); ?></span>
                                    <span class="erm-data-descrizione"><?php echo esc_attr($section['descrizione']); ?></span>
                                    <span class="erm-data-ordine"><?php echo esc_attr($section['ordine']); ?></span>
                                    <span class="erm-data-status"><?php echo esc_attr($section['status']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <div class="notice notice-warning">
                <p><?php echo esc_html__('Seleziona un menu per visualizzare le sue sezioni.', 'easy-restaurant-menu'); ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Inizializza il drag and drop per riordinare le sezioni
    $('.sortable').sortable({
        handle: '.erm-handle',
        update: function(event, ui) {
            // Raccogli gli ID ordinati
            const items = [];
            $('.erm-section-item').each(function(index) {
                items.push($(this).data('id'));
            });
            
            // Salva il nuovo ordine tramite AJAX
            $.ajax({
                url: erm_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'erm_update_order',
                    type: 'sections',
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
    $('.add-new-section').on('click', function(e) {
        e.preventDefault();
        resetForm();
        $('.erm-form-container').fadeIn(200);
    });
    
    $('.erm-close-form').on('click', function() {
        $('.erm-form-container').fadeOut(200);
    });
    
    // Click su una sezione per modificarla
    $('.erm-section-item').on('click', function(e) {
        // Se il click è sulla maniglia o sul pulsante "Visualizza Elementi", non apriamo il form
        if ($(e.target).hasClass('erm-handle') || $(e.target).hasClass('erm-view-items')) {
            return;
        }
        
        const id = $(this).data('id');
        
        // Riempi il form con i dati della sezione
        $('#erm-form-title').text('<?php echo esc_js(__('Modifica Sezione', 'easy-restaurant-menu')); ?>');
        $('#erm-section-id').val(id);
        $('#erm-section-menu-id').val($(this).find('.erm-data-menu-id').text());
        $('#erm-section-nome').val($(this).find('.erm-section-title').text());
        $('#erm-section-descrizione').val($(this).find('.erm-section-description').text());
        $('#erm-section-ordine').val($(this).find('.erm-data-ordine').text() || $(this).index());
        $('#erm-section-status').val($(this).find('.erm-status').hasClass('publish') ? 'publish' : 'draft');
        
        // Mostra il pulsante elimina
        $('.erm-delete-section').show();
        
        // Mostra il form
        $('.erm-form-container').fadeIn(200);
    });
    
    // Invio form
    $('#erm-section-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            action: 'erm_save_section',
            id: $('#erm-section-id').val(),
            menu_id: $('#erm-section-menu-id').val(),
            nome: $('#erm-section-nome').val(),
            descrizione: $('#erm-section-descrizione').val(),
            ordine: $('#erm-section-ordine').val(),
            status: $('#erm-section-status').val(),
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
    
    // Elimina sezione
    $('.erm-delete-section').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('<?php echo esc_js(__('Sei sicuro di voler eliminare questa sezione? Anche tutti gli elementi associati verranno eliminati. Questa azione non può essere annullata.', 'easy-restaurant-menu')); ?>')) {
            return;
        }
        
        const id = $('#erm-section-id').val();
        
        $.ajax({
            url: erm_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'erm_delete_section',
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

    // Funzione per reset del form
    function resetForm() {
        $('#erm-form-title').text('<?php echo esc_js(__('Aggiungi Nuova Sezione', 'easy-restaurant-menu')); ?>');
        $('#erm-section-id').val(0);
        $('#erm-section-nome').val('');
        $('#erm-section-descrizione').val('');
        $('#erm-section-ordine').val(0);
        $('#erm-section-status').val('publish');
        $('.erm-delete-section').hide();
    }
});
</script>

<style>
.erm-admin {
    max-width: 1200px;
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

.erm-sections-list {
    margin-top: 20px;
}

.erm-section-item {
    background: #fff;
    border: 1px solid #ddd;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.erm-section-item:hover {
    border-color: #999;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.erm-section-header {
    padding: 12px 15px;
    background: #f9f9f9;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.erm-section-title {
    margin: 0;
    font-size: 16px;
}

.erm-section-actions {
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

.erm-section-details {
    padding: 12px 15px;
}

.erm-section-description {
    margin: 0 0 10px;
    color: #555;
}

.erm-section-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #f0f0f0;
    padding-top: 10px;
    margin-top: 10px;
    font-size: 13px;
    color: #777;
}

.erm-view-items {
    text-decoration: none;
}

.erm-no-items {
    text-align: center;
    padding: 40px 20px;
    background: #fff;
    border: 1px solid #ddd;
}
</style> 