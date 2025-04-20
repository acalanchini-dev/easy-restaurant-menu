<?php
/**
 * Template per la gestione dei menu
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/admin/partials
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Recupera i menu esistenti
global $wpdb;
$table_name = $wpdb->prefix . 'erm_menus';
$menus = $wpdb->get_results("SELECT * FROM $table_name ORDER BY ordine ASC", ARRAY_A);
?>

<div class="wrap erm-admin">
    <h1 class="wp-heading-inline"><?php echo esc_html__('Gestione Menu', 'easy-restaurant-menu'); ?></h1>
    <a href="#" class="page-title-action add-new-menu"><?php echo esc_html__('Aggiungi Nuovo Menu', 'easy-restaurant-menu'); ?></a>
    
    <hr class="wp-header-end">
    
    <div class="notice notice-info is-dismissible">
        <p><?php echo esc_html__('Trascina i menu per riordinare. Clicca su un menu per modificarlo.', 'easy-restaurant-menu'); ?></p>
    </div>
    
    <div class="erm-form-container" style="display: none;">
        <div class="erm-form-header">
            <h2 id="erm-form-title"><?php echo esc_html__('Aggiungi Nuovo Menu', 'easy-restaurant-menu'); ?></h2>
            <button type="button" class="erm-close-form">&times;</button>
        </div>
        
        <form id="erm-menu-form">
            <input type="hidden" id="erm-menu-id" name="id" value="0">
            
            <div class="erm-form-group">
                <label for="erm-menu-nome"><?php echo esc_html__('Nome Menu', 'easy-restaurant-menu'); ?></label>
                <input type="text" id="erm-menu-nome" name="nome" required>
            </div>
            
            <div class="erm-form-group">
                <label for="erm-menu-descrizione"><?php echo esc_html__('Descrizione', 'easy-restaurant-menu'); ?></label>
                <textarea id="erm-menu-descrizione" name="descrizione" rows="4"></textarea>
            </div>
            
            <div class="erm-form-row">
                <div class="erm-form-group">
                    <label for="erm-menu-ordine"><?php echo esc_html__('Ordine', 'easy-restaurant-menu'); ?></label>
                    <input type="number" id="erm-menu-ordine" name="ordine" min="0" step="1" value="0">
                </div>
                
                <div class="erm-form-group">
                    <label for="erm-menu-status"><?php echo esc_html__('Stato', 'easy-restaurant-menu'); ?></label>
                    <select id="erm-menu-status" name="status">
                        <option value="publish"><?php echo esc_html__('Pubblicato', 'easy-restaurant-menu'); ?></option>
                        <option value="draft"><?php echo esc_html__('Bozza', 'easy-restaurant-menu'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="erm-form-actions">
                <button type="submit" class="button button-primary"><?php echo esc_html__('Salva Menu', 'easy-restaurant-menu'); ?></button>
                <button type="button" class="button erm-delete-menu" style="display: none; float: right; color: #a00;"><?php echo esc_html__('Elimina', 'easy-restaurant-menu'); ?></button>
            </div>
        </form>
    </div>
    
    <div class="erm-menus-container">
        <?php if (empty($menus)) : ?>
            <div class="erm-no-items">
                <p><?php echo esc_html__('Nessun menu trovato. Aggiungi un nuovo menu per iniziare.', 'easy-restaurant-menu'); ?></p>
            </div>
        <?php else : ?>
            <div class="erm-menus-list sortable">
                <?php foreach ($menus as $menu) : ?>
                    <div class="erm-menu-item" data-id="<?php echo esc_attr($menu['id']); ?>">
                        <div class="erm-menu-header">
                            <h3 class="erm-menu-title"><?php echo esc_html($menu['nome']); ?></h3>
                            <div class="erm-menu-actions">
                                <span class="erm-status <?php echo esc_attr($menu['status']); ?>"><?php echo esc_html($menu['status'] === 'publish' ? __('Pubblicato', 'easy-restaurant-menu') : __('Bozza', 'easy-restaurant-menu')); ?></span>
                                <span class="dashicons dashicons-menu erm-handle"></span>
                            </div>
                        </div>
                        <div class="erm-menu-details">
                            <?php if (!empty($menu['descrizione'])) : ?>
                                <p class="erm-menu-description"><?php echo esc_html($menu['descrizione']); ?></p>
                            <?php endif; ?>
                            <div class="erm-menu-meta">
                                <span class="erm-menu-id"><?php echo esc_html__('ID:', 'easy-restaurant-menu'); ?> <?php echo esc_html($menu['id']); ?></span>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=erm-sections&menu=' . $menu['id'])); ?>" class="erm-view-sections"><?php echo esc_html__('Visualizza Sezioni', 'easy-restaurant-menu'); ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Inizializza il drag and drop per riordinare i menu
    $('.sortable').sortable({
        handle: '.erm-handle',
        update: function(event, ui) {
            // Raccogli gli ID ordinati
            const items = [];
            $('.erm-menu-item').each(function(index) {
                items.push($(this).data('id'));
            });
            
            // Salva il nuovo ordine tramite AJAX
            $.ajax({
                url: erm_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'erm_update_order',
                    type: 'menus',
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
    $('.add-new-menu').on('click', function(e) {
        e.preventDefault();
        resetForm();
        $('.erm-form-container').fadeIn(200);
    });
    
    $('.erm-close-form').on('click', function() {
        $('.erm-form-container').fadeOut(200);
    });
    
    // Click su un menu per modificarlo
    $('.erm-menu-item').on('click', function(e) {
        // Se il click è sulla maniglia o sul pulsante "Visualizza Sezioni", non apriamo il form
        if ($(e.target).hasClass('erm-handle') || $(e.target).hasClass('erm-view-sections')) {
            return;
        }
        
        const id = $(this).data('id');
        
        // Riempi il form con i dati del menu
        $('#erm-form-title').text('<?php echo esc_js(__('Modifica Menu', 'easy-restaurant-menu')); ?>');
        $('#erm-menu-id').val(id);
        $('#erm-menu-nome').val($(this).find('.erm-menu-title').text());
        $('#erm-menu-descrizione').val($(this).find('.erm-menu-description').text());
        $('#erm-menu-ordine').val($(this).index());
        $('#erm-menu-status').val($(this).find('.erm-status').hasClass('publish') ? 'publish' : 'draft');
        
        // Mostra il pulsante elimina
        $('.erm-delete-menu').show();
        
        // Mostra il form
        $('.erm-form-container').fadeIn(200);
    });
    
    // Invio form
    $('#erm-menu-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            action: 'erm_save_menu',
            id: $('#erm-menu-id').val(),
            nome: $('#erm-menu-nome').val(),
            descrizione: $('#erm-menu-descrizione').val(),
            ordine: $('#erm-menu-ordine').val(),
            status: $('#erm-menu-status').val(),
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
    
    // Elimina menu
    $('.erm-delete-menu').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('<?php echo esc_js(__('Sei sicuro di voler eliminare questo menu? Anche tutte le sezioni e gli elementi associati verranno eliminati. Questa azione non può essere annullata.', 'easy-restaurant-menu')); ?>')) {
            return;
        }
        
        const id = $('#erm-menu-id').val();
        
        $.ajax({
            url: erm_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'erm_delete_menu',
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
        $('#erm-form-title').text('<?php echo esc_js(__('Aggiungi Nuovo Menu', 'easy-restaurant-menu')); ?>');
        $('#erm-menu-id').val(0);
        $('#erm-menu-nome').val('');
        $('#erm-menu-descrizione').val('');
        $('#erm-menu-ordine').val(0);
        $('#erm-menu-status').val('publish');
        $('.erm-delete-menu').hide();
    }
});
</script> 