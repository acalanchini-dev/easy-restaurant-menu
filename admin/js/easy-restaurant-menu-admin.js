/**
 * JavaScript principale per l'interfaccia amministrativa
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 */

(function($) {
    'use strict';

    // Variabili globali
    let mediaUploader;

    /**
     * Funzione per mostrare notifiche
     *
     * @param {string} type - Tipo di notifica (success, error, warning, info)
     * @param {string} message - Messaggio da mostrare
     */
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

    /**
     * Inizializza il Media Uploader di WordPress
     */
    function initMediaUploader() {
        $(document).on('click', '.erm-upload-image', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const previewContainer = button.siblings('.erm-image-preview');
            const inputField = button.siblings('input[type="hidden"]');
            const removeButton = button.siblings('.erm-remove-image');
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: erm_admin.text.select_image,
                button: {
                    text: erm_admin.text.use_image
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                inputField.val(attachment.id);
                previewContainer.html(`<img src="${attachment.url}" alt="">`);
                removeButton.show();
            });
            
            mediaUploader.open();
        });
        
        $(document).on('click', '.erm-remove-image', function() {
            const button = $(this);
            const previewContainer = button.siblings('.erm-image-preview');
            const inputField = button.siblings('input[type="hidden"]');
            
            inputField.val('0');
            previewContainer.html('');
            button.hide();
        });
    }

    /**
     * Inizializza il sortable per il drag and drop
     */
    function initSortable() {
        if ($('.sortable').length) {
            $('.sortable').sortable({
                handle: '.erm-handle',
                placeholder: 'erm-item-placeholder',
                update: function(event, ui) {
                    const items = [];
                    const type = $(this).hasClass('erm-sections-list') ? 'sections' : 'items';
                    
                    $(this).children().each(function(index) {
                        items.push($(this).data('id'));
                    });
                    
                    // Salva il nuovo ordine tramite AJAX
                    $.ajax({
                        url: erm_admin.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'erm_update_order',
                            type: type,
                            items: items,
                            nonce: erm_admin.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                showNotice('success', response.data.message);
                            } else {
                                showNotice('error', response.data.message);
                            }
                        }
                    });
                }
            });
        }
    }

    /**
     * Gestione AJAX per ottenere un'immagine
     *
     * @param {number} imageId - ID dell'immagine
     * @param {function} callback - Funzione da chiamare con la risposta
     */
    function getImage(imageId, callback) {
        if (!imageId || imageId <= 0) {
            callback(null);
            return;
        }
        
        $.ajax({
            url: erm_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'erm_get_image',
                image_id: imageId,
                nonce: erm_admin.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    callback(response.data);
                } else {
                    callback(null);
                }
            },
            error: function() {
                callback(null);
            }
        });
    }

    /**
     * Gestione della duplicazione degli elementi
     */
    function initDuplication() {
        $(document).on('click', '.erm-duplicate-item', function() {
            const itemId = $(this).data('id');
            
            if (!itemId) {
                showNotice('error', 'ID elemento non valido');
                return;
            }
            
            // Mostra un messaggio di conferma
            if (!confirm('Sei sicuro di voler duplicare questo elemento?')) {
                return;
            }
            
            // Effettua la richiesta AJAX per duplicare l'elemento
            $.ajax({
                url: erm_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'erm_duplicate_item',
                    id: itemId,
                    nonce: erm_admin.nonce
                },
                beforeSend: function() {
                    // Disabilita il pulsante durante la richiesta
                    $(this).prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        showNotice('success', response.data.message);
                        // Ricarica la pagina per mostrare il nuovo elemento duplicato
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotice('error', response.data.message);
                    }
                },
                error: function() {
                    showNotice('error', 'Errore durante la duplicazione dell\'elemento');
                },
                complete: function() {
                    // Riabilita il pulsante
                    $(this).prop('disabled', false);
                }
            });
        });
    }

    /**
     * Inizializza l'applicazione
     */
    function init() {
        initMediaUploader();
        initSortable();
        initDuplication();
        
        // Se WP non ha caricato il media uploader, disabilita i relativi bottoni
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            $('.erm-upload-image').prop('disabled', true)
                .after('<p class="description">' + erm_admin.text.media_library_unavailable + '</p>');
        }
    }

    // Inizializza quando il DOM è pronto
    $(document).ready(init);

})(jQuery);