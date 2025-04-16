# Istruzioni per lo sviluppo di "Easy Restaurant Menu"

## Struttura di base
- Utilizza la struttura esistente con namespace `EASY_RESTAURANT_MENU`
- Segui il pattern di sviluppo esistente con le classi Helper, Loader e Core
- Mantieni il sistema di attivazione/disattivazione già implementato

## 1. Configurazione iniziale
- Aggiorna i dettagli del plugin in `easy-restaurant-menu.php`
- Verifica che tutte le costanti siano definite correttamente
- Assicurati che i file di base siano inclusi nell'ordine corretto

## 2. Database (Classi Activator/Deactivator)
- Modifica `class-easy-restaurant-menu-activator.php` per creare:
  - Tabella `erm_sections` (id, nome, descrizione, ordine, status)
  - Tabella `erm_items` (id, section_id, titolo, descrizione, prezzo, immagine, ordine, status)
- Implementa in `class-easy-restaurant-menu-deactivator.php` la rimozione delle tabelle

## 3. Interfaccia amministrativa
- Estendi `class-easy-restaurant-menu-admin.php` con:
  - Menu amministrativo dedicato
  - Pagine per gestione sezioni e piatti
  - Script JS/CSS per interfaccia drag-and-drop
- Crea i file template in `admin/partials/`:
  - `sections-page.php`
  - `items-page.php`
  - `options-page.php`

## 4. REST API
- Crea una nuova classe `class-easy-restaurant-menu-rest.php`:
  - Endpoint per CRUD delle sezioni
  - Endpoint per CRUD degli elementi
  - Validazione e sanitizzazione dei dati
- Registra la classe REST in `Easy_Restaurant_Menu_Core`

## 5. Opzioni del plugin
- Utilizza `class-easy-restaurant-menu-options.php` per:
  - Impostazioni di visualizzazione del menu
  - Configurazione valuta e formati prezzo
  - Opzioni di layout predefinite

## 6. Blocco Gutenberg - Registrazione
- Modifica `class-easy-restaurant-menu-blocks.php`:
  - Rinomina il blocco in "Restaurant Menu"
  - Configura il metodo di callback per il rendering
- Crea il file `build/restaurant-menu/block.json` con gli attributi necessari

## 7. Blocco Gutenberg - Attributi per personalizzazione
- Implementa in `block.json` attributi per:
  - Schema colori (principale, secondario, accenti, sfondo)
  - Tipografia (font-family, dimensioni, peso, stile)
  - Spaziatura (padding, margin)
  - Layout (griglia, colonne, allineamento)
  - Stile bordi e ombre
  - Effetti di hover/transizione

## 8. Blocco Gutenberg - Implementazione React
- Sviluppa in `/src/blocks/restaurant-menu/`:
  - `edit.js` - Interfaccia editor
  - `InspectorControls` per pannello laterale
  - Componenti React per visualizzazione e personalizzazione
  - Sistema di preview in tempo reale

## 9. Rendering frontend
- Crea template in `public/partials/restaurant-menu-render.php`
- Implementa logica di rendering dinamico basata sugli attributi
- Genera CSS inline per gli stili personalizzati
- Assicura compatibilità responsive

## 10. Asset frontend
- Aggiorna `class-easy-restaurant-menu-public.php`:
  - Caricamento condizionale degli script
  - Gestione delle dipendenze
  - Implementazione delle interazioni (filtri, ordinamento)

## 11. Sicurezza e performance
- Implementa nonce in tutte le operazioni AJAX
- Aggiungi controlli di autorizzazione nelle API
- Sanitizza tutti gli input/output
- Aggiungi caching tramite transient API
- Ottimizza caricamento di JS/CSS

## 12. Internazionalizzazione
- Usa `class-easy-restaurant-menu-i18n.php`
- Applica funzioni `__()` e `_e()` a tutti i testi
- Prepara il file POT per le traduzioni

## 13. Testing
- Verifica compatibilità con temi principali
- Testa funzionamento responsive su vari dispositivi
- Controlla funzionamento corretto degli stili personalizzati

## 14. Documentazione
- Aggiorna README.md con istruzioni d'uso complete
- Documenta gli hook disponibili per sviluppatori
- Crea esempi di personalizzazione avanzata
