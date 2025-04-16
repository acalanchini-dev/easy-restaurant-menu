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

// Recupera le sezioni esistenti
global $wpdb;
$table_name = $wpdb->prefix . 'erm_sections';
$sections = $wpdb->get_results("SELECT * FROM $table_name ORDER BY ordine ASC", ARRAY_A);
?>

<div class="wrap erm-admin">
    <h1 class="wp-heading-inline"><?php echo esc_html__('Gestione Sezioni Menu', 'easy-restaurant-menu'); ?></h1>
    <a href="#" class="page-title-action add-new-section"><?php echo esc_html__('Aggiungi Nuova Sezione', 'easy-restaurant-menu'); ?></a>
    
    <hr class="wp-header-end">
    
    <div class="notice notice-info is-dismissible">
        <p><?php echo esc_html__('Trascina le sezioni per riordinare. Clicca su una sezione per modificarla.', 'easy-restaurant-menu'); ?></p>
    </div>
    
    <div class="erm-form-container" style="display: none;">
        <div class="erm-form-header">
            <h2 id="erm-form-title"><?php echo esc_html__('Aggiungi Nuova Sezione', 'easy-restaurant-menu'); ?></h2>
            <button type="button" class="erm-close-form">&times;</button>
        </div>
        
        <form id="erm-section-form">
            <input type="hidden" id="erm-section-id" name="id" value="0">
            
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
                <p><?php echo esc_html__('Nessuna sezione trovata. Aggiungi una nuova sezione per iniziare.', 'easy-restaurant-menu'); ?></p>
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
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
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
        $('#erm-section-nome').val($(this).find('.erm-section-title').text());
        $('#erm-section-descrizione').val($(this).find('.erm-section-description').text());
        $('#erm-section-ordine').val($(this).index());
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
    
    // Resetta il form
    function resetForm() {
        $('#erm-form-title').text('<?php echo esc_js(__('Aggiungi Nuova Sezione', 'easy-restaurant-menu')); ?>');
        $('#erm-section-id').val(0);
        $('#erm-section-nome').val('');
        $('#erm-section-descrizione').val('');
        $('#erm-section-ordine').val(<?php echo count($sections); ?>);
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