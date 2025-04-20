# Easy Restaurant Menu

Un plugin WordPress per creare e gestire menu di ristoranti con blocchi Gutenberg personalizzabili.

## Descrizione

Easy Restaurant Menu è un plugin WordPress che permette di creare sezioni di menu per ristoranti e di visualizzarle tramite un blocco Gutenberg completamente personalizzabile. Il plugin è progettato per essere semplice da usare ma potente nelle funzionalità.

## Caratteristiche

- Interfaccia amministrativa intuitiva per gestire sezioni e piatti
- Blocco Gutenberg personalizzabile con numerose opzioni di stile
- Supporto per immagini, prezzi e descrizioni per ogni elemento del menu
- Possibilità di visualizzare il menu in formato griglia o lista
- Personalizzazione completa dei colori, bordi, ombre e spaziature
- Effetti hover per una migliore esperienza utente
- Responsive design per una corretta visualizzazione su tutti i dispositivi
- Sistema di caching avanzato tramite Transient API per prestazioni ottimali
- Statistiche di cache e opzioni di configurazione personalizzabili

## Installazione

1. Carica la cartella `easy-restaurant-menu` nella directory `/wp-content/plugins/`
2. Attiva il plugin dal menu 'Plugin' in WordPress
3. Vai a "Menu Ristorante" nel pannello admin per iniziare a creare le tue sezioni di menu e i piatti

## Utilizzo

### Panoramica della struttura dei menu

Il plugin organizza i menu di ristorante in tre livelli gerarchici:
- **Menu**: contenitore principale che può raggruppare più sezioni
- **Sezioni**: categorie all'interno di un menu (es. antipasti, primi, secondi)
- **Piatti**: singoli elementi all'interno di una sezione

### Creazione e gestione dei menu

1. Dal pannello amministrativo, vai su "Menu Ristorante" → "Menu"
2. Clicca su "Aggiungi nuovo menu" per creare un menu principale
3. Assegna un nome descrittivo (es. "Menu del giorno", "Menu serale", "Menu festivo")
4. Puoi creare più menu e attivarli/disattivarli secondo necessità

### Creazione e gestione delle sezioni

1. Dal pannello amministrativo, vai su "Menu Ristorante" → "Sezioni"
2. Clicca su "Aggiungi nuova sezione" per creare una categoria
3. Compila i campi:
   - Nome della sezione (es. "Antipasti", "Primi piatti")
   - Descrizione (opzionale)
   - Menu di appartenenza
   - Ordine di visualizzazione
   - Immagine rappresentativa (opzionale)

### Creazione e gestione dei piatti

1. Dal pannello amministrativo, vai su "Menu Ristorante" → "Piatti"
2. Clicca su "Aggiungi nuovo piatto" per inserire un elemento
3. Compila i campi:
   - Titolo del piatto
   - Descrizione dettagliata
   - Prezzo (con possibilità di specificare prezzi variabili)
   - Immagine del piatto
   - Sezione di appartenenza
   - Ordine di visualizzazione
   - Attributi speciali (vegetariano, piccante, ecc.)
   - Stato di disponibilità

### Inserimento del menu in una pagina

1. Crea o modifica una pagina dove vuoi mostrare il menu
2. Aggiungi il blocco "Menu Ristorante" 
3. Configura le opzioni nel pannello laterale:
   - Seleziona il menu completo o specifiche sezioni da visualizzare
   - Scegli l'ordinamento degli elementi (manuale, alfabetico, per prezzo)
   - Imposta i filtri di visualizzazione (es. solo piatti disponibili)

### Personalizzazione dell'aspetto

Personalizza lo stile secondo le tue preferenze:
- **Layout**:
  - Tipo di visualizzazione (griglia o lista)
  - Numero di colonne (per la visualizzazione a griglia)
  - Densità degli elementi (compatta, normale, spaziosa)
- **Contenuti**:
  - Mostra/nascondi immagini, prezzi, descrizioni
  - Lunghezza massima delle descrizioni
  - Formato di visualizzazione dei prezzi
- **Stile**:
  - Colori per titoli, prezzi, descrizioni e sfondi
  - Tipografia (famiglia di font, dimensione, peso)
  - Bordi, raggi degli angoli e ombre
  - Spaziatura tra gli elementi
- **Interattività**:
  - Effetti al passaggio del mouse
  - Animazioni di caricamento
  - Comportamento responsive

### Configurazione del caching

1. Dal pannello amministrativo, vai su "Menu Ristorante" → "Impostazioni" → tab "Cache"
2. Configura le opzioni di caching:
   - Abilita/disabilita il sistema di caching
   - Imposta la durata della cache (da 5 minuti a 1 settimana)
   - Visualizza le statistiche di utilizzo:
     - Hit ratio (percentuale di richieste servite dalla cache)
     - Cache hits (richieste servite dalla cache)
     - Cache misses (richieste non trovate in cache)
   - Gestisci la cache:
     - Svuota manualmente la cache quando necessario
     - Abilita/disabilita l'invalidazione automatica
     - Configura gli eventi che invalidano la cache

### Utilizzo avanzato

1. **Shortcode**: Puoi utilizzare lo shortcode `[restaurant_menu id="X" section="Y"]` per inserire menu in qualsiasi posizione supportata
2. **Template PHP**: Per gli sviluppatori di temi, è possibile utilizzare la funzione `erm_display_menu($args)` nei template
3. **REST API**: Accedi ai dati dei menu tramite gli endpoint REST per integrazioni personalizzate
4. **Hook e filtri**: Personalizza il comportamento del plugin utilizzando i numerosi hook disponibili

## Prestazioni e Ottimizzazioni

Il plugin include un sistema di caching avanzato basato sulla Transient API di WordPress per garantire prestazioni ottimali anche con menu complessi o siti ad alto traffico:

- Riduzione significativa del carico sul database
- Miglioramento dei tempi di risposta dell'applicazione
- Caching intelligente con invalidazione automatica quando i dati vengono modificati
- Monitoraggio delle prestazioni tramite hit ratio e statistiche
- Supporto per installazioni con volumi elevati di menu e articoli

## Requisiti

- WordPress 5.9 o superiore
- PHP 7.0 o superiore

## Sviluppo

Il plugin è sviluppato utilizzando:
- PHP per la logica lato server
- React e JavaScript per l'interfaccia del blocco Gutenberg
- SCSS per gli stili
- WordPress Block API
- Transient API per il sistema di caching

Per gli sviluppatori che desiderano contribuire:

1. Clona il repository
2. Installa le dipendenze: `npm i`
3. Per lo sviluppo: `npm start`
4. Per la build di produzione: `npm run build`

## Licenza

Il plugin Easy Restaurant Menu è rilasciato sotto licenza GPL v2 o successiva.

## Crediti

Sviluppato da [Il tuo nome]

Basato sul boilerplate creato da Kadim Gültekin
* https://github.com/Arkenon
* https://www.linkedin.com/in/kadim-gültekin-86320a198/


