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

### Creazione di sezioni e piatti

1. Dal pannello amministrativo, vai su "Menu Ristorante" → "Sezioni" per creare le categorie del tuo menu (antipasti, primi, secondi, etc.)
2. Vai su "Menu Ristorante" → "Piatti" per aggiungere i singoli elementi alle sezioni
3. Per ogni piatto puoi specificare:
   - Titolo
   - Descrizione
   - Prezzo
   - Immagine
   - Sezione di appartenenza
   - Ordine di visualizzazione

### Inserimento del menu in una pagina

1. Crea o modifica una pagina dove vuoi mostrare il menu
2. Aggiungi il blocco "Menu Ristorante" 
3. Seleziona la sezione da visualizzare dal pannello laterale
4. Personalizza lo stile secondo le tue preferenze:
   - Tipo di visualizzazione (griglia o lista)
   - Numero di colonne (per la visualizzazione a griglia)
   - Mostra/nascondi immagini, prezzi, descrizioni
   - Colori per titoli, prezzi, descrizioni e sfondi
   - Bordi, raggi degli angoli e ombre
   - Spaziatura tra gli elementi
   - Effetti al passaggio del mouse

### Configurazione del caching

1. Dal pannello amministrativo, vai su "Menu Ristorante" → "Impostazioni" → tab "Cache"
2. Puoi:
   - Abilitare/disabilitare il sistema di caching
   - Configurare la durata della cache (da 5 minuti a 1 settimana)
   - Visualizzare le statistiche di utilizzo della cache
   - Svuotare manualmente la cache quando necessario

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


