/**
 * JavaScript per il frontend del blocco Restaurant Menu
 */

document.addEventListener('DOMContentLoaded', function() {
    // Ottieni tutti gli elementi del menu
    const menuItems = document.querySelectorAll('.erm-menu-item');
    
    // Aggiungi effetto hover quando l'utente passa sopra gli elementi
    menuItems.forEach(function(item) {
        if (item.classList.contains('erm-hover-zoom') || 
            item.classList.contains('erm-hover-shadow') ||
            item.classList.contains('erm-hover-border')) {
            
            item.addEventListener('mouseenter', function() {
                this.classList.add('erm-active');
            });
            
            item.addEventListener('mouseleave', function() {
                this.classList.remove('erm-active');
            });
        }
    });
}); 