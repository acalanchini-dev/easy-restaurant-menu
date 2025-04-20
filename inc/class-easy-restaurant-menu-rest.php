<?php
/**
 * Gestisce la REST API del plugin.
 *
 * Definisce gli endpoint per la gestione delle sezioni e degli elementi del menu.
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/inc
 */

namespace EASY_RESTAURANT_MENU;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use Exception;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Easy_Restaurant_Menu_REST {

    /**
     * Namespace dell'API
     *
     * @var string
     */
    private $namespace = 'easy-restaurant-menu/v1';
    
    /**
     * Flag per l'ambiente di sviluppo
     * 
     * @var bool
     */
    private $is_dev_env = WP_DEBUG;

    /**
     * Inizializza la REST API
     *
     * @since    1.0.0
     */
    public function initialize(): void {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    
    /**
     * Valida un parametro di richiesta in base alle regole dello schema
     *
     * @param mixed $param Valore del parametro
     * @param array $rules Regole di validazione
     * @return true|WP_Error Restituisce true se il parametro è valido, altrimenti un oggetto WP_Error
     */
    private function validate_request_param($param, $rules) {
        $type = $rules['type'] ?? 'string';
        $name = $rules['name'] ?? __('Parametro', 'easy-restaurant-menu');
        
        // Valida che il parametro sia presente se richiesto
        if (($rules['required'] ?? false) && (is_null($param) || $param === '')) {
            return new WP_Error(
                'erm_missing_required_param',
                sprintf(__('%s è richiesto', 'easy-restaurant-menu'), $name),
                ['status' => 400]
            );
        }
        
        // Se il parametro è null e non è richiesto, non effettuare ulteriori validazioni
        if (is_null($param) || $param === '') {
            return true;
        }
        
        // Valida il tipo
        switch ($type) {
            case 'string':
                if (!is_string($param)) {
                    return new WP_Error(
                        'erm_invalid_param_type',
                        sprintf(__('%s deve essere una stringa', 'easy-restaurant-menu'), $name),
                        ['status' => 400]
                    );
                }
                
                // Valida lunghezza minima
                if (isset($rules['min_length']) && strlen($param) < $rules['min_length']) {
                    return new WP_Error(
                        'erm_param_too_short',
                        sprintf(__('%s deve contenere almeno %d caratteri', 'easy-restaurant-menu'), $name, $rules['min_length']),
                        ['status' => 400]
                    );
                }
                
                // Valida lunghezza massima
                if (isset($rules['max_length']) && strlen($param) > $rules['max_length']) {
                    return new WP_Error(
                        'erm_param_too_long',
                        sprintf(__('%s non può contenere più di %d caratteri', 'easy-restaurant-menu'), $name, $rules['max_length']),
                        ['status' => 400]
                    );
                }
                break;
                
            case 'integer':
                if (!is_numeric($param) || intval($param) != $param) {
                    return new WP_Error(
                        'erm_invalid_param_type',
                        sprintf(__('%s deve essere un numero intero', 'easy-restaurant-menu'), $name),
                        ['status' => 400]
                    );
                }
                
                // Converte in intero per le successive validazioni
                $param = intval($param);
                
                // Valida valore minimo
                if (isset($rules['min']) && $param < $rules['min']) {
                    return new WP_Error(
                        'erm_param_too_small',
                        sprintf(__('%s non può essere inferiore a %d', 'easy-restaurant-menu'), $name, $rules['min']),
                        ['status' => 400]
                    );
                }
                
                // Valida valore massimo
                if (isset($rules['max']) && $param > $rules['max']) {
                    return new WP_Error(
                        'erm_param_too_large',
                        sprintf(__('%s non può essere superiore a %d', 'easy-restaurant-menu'), $name, $rules['max']),
                        ['status' => 400]
                    );
                }
                break;
                
            case 'number':
            case 'float':
                if (!is_numeric($param)) {
                    return new WP_Error(
                        'erm_invalid_param_type',
                        sprintf(__('%s deve essere un numero', 'easy-restaurant-menu'), $name),
                        ['status' => 400]
                    );
                }
                
                // Converte in float per le successive validazioni
                $param = floatval($param);
                
                // Valida valore minimo
                if (isset($rules['min']) && $param < $rules['min']) {
                    return new WP_Error(
                        'erm_param_too_small',
                        sprintf(__('%s non può essere inferiore a %f', 'easy-restaurant-menu'), $name, $rules['min']),
                        ['status' => 400]
                    );
                }
                
                // Valida valore massimo
                if (isset($rules['max']) && $param > $rules['max']) {
                    return new WP_Error(
                        'erm_param_too_large',
                        sprintf(__('%s non può essere superiore a %f', 'easy-restaurant-menu'), $name, $rules['max']),
                        ['status' => 400]
                    );
                }
                break;
                
            case 'boolean':
                if (!is_bool($param) && $param !== 'true' && $param !== 'false' && $param !== '1' && $param !== '0' && $param !== 1 && $param !== 0) {
                    return new WP_Error(
                        'erm_invalid_param_type',
                        sprintf(__('%s deve essere un valore booleano', 'easy-restaurant-menu'), $name),
                        ['status' => 400]
                    );
                }
                break;
                
            case 'enum':
                if (!isset($rules['enum']) || !is_array($rules['enum'])) {
                    return new WP_Error(
                        'erm_invalid_schema',
                        __('Schema non valido: mancano i valori enum', 'easy-restaurant-menu'),
                        ['status' => 500]
                    );
                }
                
                if (!in_array($param, $rules['enum'])) {
                    return new WP_Error(
                        'erm_invalid_enum_value',
                        sprintf(__('%s deve essere uno dei seguenti valori: %s', 'easy-restaurant-menu'), $name, implode(', ', $rules['enum'])),
                        ['status' => 400]
                    );
                }
                break;
                
            default:
                return new WP_Error(
                    'erm_invalid_schema_type',
                    sprintf(__('Tipo di schema non supportato: %s', 'easy-restaurant-menu'), $type),
                    ['status' => 500]
                );
        }
        
        // Esegui callback di validazione personalizzata se presente
        if (isset($rules['validate_callback']) && is_callable($rules['validate_callback'])) {
            $validation_result = call_user_func($rules['validate_callback'], $param);
            if ($validation_result !== true) {
                return $validation_result;
            }
        }
        
        return true;
    }
    
    /**
     * Sanitizza i dati della richiesta in base al contesto
     *
     * @param mixed $data I dati da sanitizzare
     * @param string $context Il contesto di sanitizzazione (es. 'menu', 'section', 'item')
     * @return mixed I dati sanitizzati
     * @since    1.1.0
     */
    private function sanitize_request_data($data, $context = 'default') {
        if (!is_array($data)) {
            return $this->sanitize_single_value($data);
        }
        
        $sanitized = [];
        
        switch ($context) {
            case 'menu':
                $sanitized['nome'] = isset($data['nome']) ? sanitize_text_field($data['nome']) : null;
                $sanitized['descrizione'] = isset($data['descrizione']) ? wp_kses_post($data['descrizione']) : null;
                $sanitized['ordine'] = isset($data['ordine']) ? intval($data['ordine']) : null;
                $sanitized['status'] = isset($data['status']) ? sanitize_text_field($data['status']) : null;
                break;
                
            case 'section':
                $sanitized['nome'] = isset($data['nome']) ? sanitize_text_field($data['nome']) : null;
                $sanitized['menu_id'] = isset($data['menu_id']) ? intval($data['menu_id']) : null;
                $sanitized['descrizione'] = isset($data['descrizione']) ? wp_kses_post($data['descrizione']) : null;
                $sanitized['ordine'] = isset($data['ordine']) ? intval($data['ordine']) : null;
                $sanitized['status'] = isset($data['status']) ? sanitize_text_field($data['status']) : null;
                break;
                
            case 'item':
                $sanitized['titolo'] = isset($data['titolo']) ? sanitize_text_field($data['titolo']) : null;
                $sanitized['section_id'] = isset($data['section_id']) ? intval($data['section_id']) : null;
                $sanitized['descrizione'] = isset($data['descrizione']) ? wp_kses_post($data['descrizione']) : null;
                $sanitized['prezzo'] = isset($data['prezzo']) ? $this->sanitize_price($data['prezzo']) : null;
                $sanitized['immagine'] = isset($data['immagine']) ? intval($data['immagine']) : null;
                $sanitized['ordine'] = isset($data['ordine']) ? intval($data['ordine']) : null;
                $sanitized['status'] = isset($data['status']) ? sanitize_text_field($data['status']) : null;
                break;
                
            default:
                // Sanitizzazione generica per ogni campo
                foreach ($data as $key => $value) {
                    $sanitized[$key] = $this->sanitize_single_value($value);
                }
                break;
        }
        
        // Rimuovi i campi null (non presenti nei dati originali)
        return array_filter($sanitized, function($value) {
            return $value !== null;
        });
    }
    
    /**
     * Sanitizza un singolo valore in base al suo tipo
     *
     * @param mixed $value Il valore da sanitizzare
     * @return mixed Il valore sanitizzato
     * @since    1.1.0
     */
    private function sanitize_single_value($value) {
        if (is_array($value)) {
            $sanitized = [];
            foreach ($value as $k => $v) {
                $sanitized[$k] = $this->sanitize_single_value($v);
            }
            return $sanitized;
        }
        
        if (is_numeric($value)) {
            // Mantieni i numeri come sono
            return $value;
        }
        
        if (is_bool($value)) {
            // Mantieni i booleani come sono
            return $value;
        }
        
        if (is_string($value)) {
            // Rimuovi tag HTML non consentiti
            return sanitize_text_field($value);
        }
        
        // Default
        return $value;
    }
    
    /**
     * Sanitizza un prezzo
     *
     * @param mixed $price Il prezzo da sanitizzare
     * @return float Il prezzo sanitizzato
     * @since    1.1.0
     */
    private function sanitize_price($price) {
        // Rimuovi caratteri non numerici eccetto il punto e la virgola
        $price = preg_replace('/[^\d.,]/', '', $price);
        
        // Sostituisci la virgola con il punto
        $price = str_replace(',', '.', $price);
        
        // Converti in float
        return round(floatval($price), 2);
    }
    
    /**
     * Verifica i permessi dell'utente per un'azione specifica
     *
     * @param string $action L'azione da verificare (es. 'read', 'create', 'update', 'delete')
     * @param int|null $resource_id ID della risorsa (opzionale)
     * @return bool|WP_Error True se l'utente ha i permessi, WP_Error altrimenti
     * @since    1.1.0
     */
    private function check_user_permissions($action, $resource_id = null) {
        // Controlla se l'utente è loggato
        if (!is_user_logged_in()) {
            if ($action === 'read' && !$resource_id) {
                // Consenti la lettura pubblica degli elenchi
                return true;
            }
            
            return new WP_Error(
                'erm_auth_required',
                __('Autenticazione richiesta per questa operazione', 'easy-restaurant-menu'),
                ['status' => 401]
            );
        }
        
        // Controlla le capacità in base all'azione
        switch ($action) {
            case 'read':
                // Tutti gli utenti loggati possono leggere
                return true;
                
            case 'create':
            case 'update':
            case 'delete':
                // Solo gli amministratori possono modificare i dati
                if (current_user_can('manage_options')) {
                    return true;
                }
                
                return new WP_Error(
                    'erm_insufficient_permissions',
                    __('Non hai i permessi necessari per eseguire questa operazione', 'easy-restaurant-menu'),
                    ['status' => 403]
                );
                
            default:
                return new WP_Error(
                    'erm_invalid_action',
                    __('Azione non valida', 'easy-restaurant-menu'),
                    ['status' => 400]
                );
        }
    }
    
    /**
     * Gestisce un'eccezione e la converte in una risposta di errore appropriata
     *
     * @param Exception $exception L'eccezione da gestire
     * @param string $context Contesto dell'eccezione (opzionale)
     * @return WP_Error
     * @since    1.1.0
     */
    private function handle_exception($exception, $context = '') {
        // Log dell'errore
        error_log(sprintf('ERM API Error (%s): %s in %s:%d', 
            $context,
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        ));
        
        // Crea una risposta di errore
        $error_data = [
            'status' => 500
        ];
        
        // Includi dettagli tecnici solo in ambiente di sviluppo
        if ($this->is_dev_env) {
            $error_data['details'] = sprintf(
                'Exception: %s in %s:%d. Trace: %s',
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString()
            );
        }
        
        return new WP_Error(
            'erm_server_error',
            __('Si è verificato un errore durante l\'elaborazione della richiesta', 'easy-restaurant-menu'),
            $error_data
        );
    }
    
    /**
     * Formatta una risposta di errore in modo standardizzato
     *
     * @param string $code Codice di errore
     * @param string $message Messaggio di errore
     * @param array $data Dati aggiuntivi
     * @return WP_Error
     * @since    1.1.0
     */
    private function format_error_response($code, $message, $data = []) {
        $default_data = [
            'status' => 400
        ];
        
        $data = array_merge($default_data, $data);
        
        return new WP_Error($code, $message, $data);
    }

    /**
     * Registra le rotte per la REST API
     *
     * @since    1.0.0
     */
    public function register_routes(): void {
        // Endpoint per i menu
        register_rest_route($this->namespace, '/menus', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_menus'],
                'permission_callback' => [$this, 'public_permissions_check']
            ],
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'create_menu'],
                'permission_callback' => [$this, 'admin_permissions_check'],
                'args'                => $this->get_menu_schema()
            ]
        ]);

        register_rest_route($this->namespace, '/menus/(?P<id>\d+)', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_menu'],
                'permission_callback' => [$this, 'public_permissions_check'],
                'args'                => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        }
                    ]
                ]
            ],
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'update_menu'],
                'permission_callback' => [$this, 'admin_permissions_check'],
                'args'                => $this->get_menu_schema()
            ],
            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [$this, 'delete_menu'],
                'permission_callback' => [$this, 'admin_permissions_check']
            ]
        ]);
        
        // Endpoint per ottenere un menu completo con tutte le sezioni ed elementi
        register_rest_route($this->namespace, '/menus/(?P<id>\d+)/complete', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_complete_menu'],
                'permission_callback' => [$this, 'public_permissions_check'],
                'args'                => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        }
                    ]
                ]
            ]
        ]);
        
        // Endpoint per le sezioni di un menu
        register_rest_route($this->namespace, '/menus/(?P<id>\d+)/sections', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_menu_sections'],
                'permission_callback' => [$this, 'public_permissions_check'],
                'args'                => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        }
                    ]
                ]
            ]
        ]);

        // Endpoint per le sezioni
        register_rest_route($this->namespace, '/sections', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_sections'],
                'permission_callback' => [$this, 'public_permissions_check']
            ],
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'create_section'],
                'permission_callback' => [$this, 'admin_permissions_check'],
                'args'                => $this->get_section_schema()
            ]
        ]);

        register_rest_route($this->namespace, '/sections/(?P<id>\d+)', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_section'],
                'permission_callback' => [$this, 'public_permissions_check'],
                'args'                => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        }
                    ]
                ]
            ],
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'update_section'],
                'permission_callback' => [$this, 'admin_permissions_check'],
                'args'                => $this->get_section_schema()
            ],
            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [$this, 'delete_section'],
                'permission_callback' => [$this, 'admin_permissions_check']
            ]
        ]);

        // Endpoint per gli elementi
        register_rest_route($this->namespace, '/items', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_items'],
                'permission_callback' => [$this, 'public_permissions_check']
            ],
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'create_item'],
                'permission_callback' => [$this, 'admin_permissions_check'],
                'args'                => $this->get_item_schema()
            ]
        ]);

        register_rest_route($this->namespace, '/items/(?P<id>\d+)', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_item'],
                'permission_callback' => [$this, 'public_permissions_check'],
                'args'                => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        }
                    ]
                ]
            ],
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'update_item'],
                'permission_callback' => [$this, 'admin_permissions_check'],
                'args'                => $this->get_item_schema()
            ],
            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [$this, 'delete_item'],
                'permission_callback' => [$this, 'admin_permissions_check']
            ]
        ]);

        // Endpoint per gli elementi di una sezione
        register_rest_route($this->namespace, '/sections/(?P<id>\d+)/items', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_section_items'],
                'permission_callback' => [$this, 'public_permissions_check'],
                'args'                => [
                    'id' => [
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        }
                    ]
                ]
            ]
        ]);

        // Endpoint per le opzioni globali del plugin
        register_rest_route($this->namespace, '/options', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_global_options'],
                'permission_callback' => [$this, 'public_permissions_check']
            ]
        ]);
    }

    /**
     * Controllo dei permessi per operazioni pubbliche
     *
     * @since    1.0.0
     * @return bool
     */
    public function public_permissions_check(): bool {
        return true;
    }

    /**
     * Controllo dei permessi per operazioni riservate agli amministratori
     *
     * @since    1.0.0
     * @return bool
     */
    public function admin_permissions_check(): bool {
        return current_user_can('manage_options');
    }

    /**
     * Ottiene tutte le sezioni
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response
     * @since    1.0.0
     */
    public function get_sections(WP_REST_Request $request): WP_REST_Response {
        // Prepara i parametri per la chiave di cache
        $cache_params = [
            'menu_id' => $request->get_param('menu_id'),
            'status' => $request->get_param('status') ?: 'publish',
            'orderby' => $request->get_param('orderby') ?: 'ordine',
            'order' => $request->get_param('order') ?: 'ASC'
        ];
        
        // Controlla se i dati sono in cache
        $sections = Easy_Restaurant_Menu_Cache::get('sections', $cache_params);
        
        if ($sections === false) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'erm_sections';
            
            // Parametri della richiesta
            $menu_id = $cache_params['menu_id'] ? intval($cache_params['menu_id']) : null;
            $status = $cache_params['status'];
            $orderby = $cache_params['orderby'];
            $order = $cache_params['order'];
            
            // Sanitizzazione
            $orderby = sanitize_sql_orderby("$orderby $order") ?: 'ordine ASC';
            $status = sanitize_text_field($status);
            
            // Query
            $query = "SELECT * FROM $table_name WHERE status = %s";
            $params = [$status];
            
            // Aggiunge il filtro per menu_id se specificato
            if ($menu_id !== null) {
                $query .= " AND menu_id = %d";
                $params[] = $menu_id;
            }
            
            $query .= " ORDER BY $orderby";
            $sections = $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
            
            // Salva i dati nella cache
            Easy_Restaurant_Menu_Cache::set('sections', $sections, $cache_params);
        }
        
        return new WP_REST_Response($sections, 200);
    }

    /**
     * Ottiene una sezione specifica
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function get_section(WP_REST_Request $request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'erm_sections';
        
        $id = (int) $request['id'];
        $section = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
        
        if (!$section) {
            return new WP_Error('no_section', __('Sezione non trovata', 'easy-restaurant-menu'), ['status' => 404]);
        }
        
        return new WP_REST_Response($section, 200);
    }

    /**
     * Crea una nuova sezione
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function create_section(WP_REST_Request $request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'erm_sections';
        
        $nome = sanitize_text_field($request['nome']);
        $menu_id = isset($request['menu_id']) ? intval($request['menu_id']) : 0;
        $descrizione = sanitize_textarea_field($request['descrizione'] ?? '');
        $ordine = isset($request['ordine']) ? intval($request['ordine']) : 0;
        $status = sanitize_text_field($request['status'] ?? 'publish');
        
        // Verifica che il menu esista
        if ($menu_id > 0) {
            $table_menus = $wpdb->prefix . 'erm_menus';
            $menu_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_menus WHERE id = %d", $menu_id));
            
            if (!$menu_exists) {
                return new WP_Error(
                    'erm_menu_not_found',
                    __('Menu specificato non trovato', 'easy-restaurant-menu'),
                    ['status' => 404]
                );
            }
        }
        
        $result = $wpdb->insert(
            $table_name,
            [
                'menu_id' => $menu_id,
                'nome' => $nome,
                'descrizione' => $descrizione,
                'ordine' => $ordine,
                'status' => $status
            ],
            ['%d', '%s', '%s', '%d', '%s']
        );
        
        if ($result === false) {
            return new WP_Error(
                'erm_section_creation_error',
                __('Errore durante la creazione della sezione', 'easy-restaurant-menu'),
                ['status' => 500]
            );
        }
        
        $section_id = $wpdb->insert_id;
        
        $section = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $section_id),
            ARRAY_A
        );
        
        return new WP_REST_Response($section, 201);
    }

    /**
     * Aggiorna una sezione esistente
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function update_section(WP_REST_Request $request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'erm_sections';
        $section_id = $request['id'];
        
        // Verifica se la sezione esiste
        $section = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $section_id),
            ARRAY_A
        );
        
        if (empty($section)) {
            return new WP_Error(
                'erm_section_not_found',
                __('Sezione non trovata', 'easy-restaurant-menu'),
                ['status' => 404]
            );
        }
        
        $nome = sanitize_text_field($request['nome'] ?? $section['nome']);
        $menu_id = isset($request['menu_id']) ? intval($request['menu_id']) : $section['menu_id'];
        $descrizione = sanitize_textarea_field($request['descrizione'] ?? $section['descrizione']);
        $ordine = isset($request['ordine']) ? intval($request['ordine']) : $section['ordine'];
        $status = sanitize_text_field($request['status'] ?? $section['status']);
        
        // Verifica che il menu esista se è stato cambiato
        if ($menu_id > 0 && $menu_id != $section['menu_id']) {
            $table_menus = $wpdb->prefix . 'erm_menus';
            $menu_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_menus WHERE id = %d", $menu_id));
            
            if (!$menu_exists) {
                return new WP_Error(
                    'erm_menu_not_found',
                    __('Menu specificato non trovato', 'easy-restaurant-menu'),
                    ['status' => 404]
                );
            }
        }
        
        $result = $wpdb->update(
            $table_name,
            [
                'menu_id' => $menu_id,
                'nome' => $nome,
                'descrizione' => $descrizione,
                'ordine' => $ordine,
                'status' => $status
            ],
            ['id' => $section_id],
            ['%d', '%s', '%s', '%d', '%s'],
            ['%d']
        );
        
        if ($result === false) {
            return new WP_Error(
                'erm_section_update_error',
                __('Errore durante l\'aggiornamento della sezione', 'easy-restaurant-menu'),
                ['status' => 500]
            );
        }
        
        $updated_section = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $section_id),
            ARRAY_A
        );
        
        return new WP_REST_Response($updated_section, 200);
    }

    /**
     * Elimina una sezione
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function delete_section(WP_REST_Request $request) {
        global $wpdb;
        $table_sections = $wpdb->prefix . 'erm_sections';
        $table_items = $wpdb->prefix . 'erm_items';
        
        $id = (int) $request['id'];
        
        // Verifica che la sezione esista
        $section = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_sections WHERE id = %d", $id), ARRAY_A);
        
        if (!$section) {
            return new WP_Error('no_section', __('Sezione non trovata', 'easy-restaurant-menu'), ['status' => 404]);
        }
        
        // Elimina prima gli elementi associati
        $wpdb->delete($table_items, ['section_id' => $id], ['%d']);
        
        // Poi elimina la sezione
        $result = $wpdb->delete($table_sections, ['id' => $id], ['%d']);
        
        if (!$result) {
            return new WP_Error('db_error', __('Errore durante l\'eliminazione della sezione', 'easy-restaurant-menu'), ['status' => 500]);
        }
        
        return new WP_REST_Response(true, 200);
    }

    /**
     * Ottiene tutti gli elementi (piatti)
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response
     * @since    1.0.0
     */
    public function get_items(WP_REST_Request $request): WP_REST_Response {
        // Prepara i parametri per la chiave di cache
        $cache_params = [
            'section_id' => $request->get_param('section_id'),
            'status' => $request->get_param('status') ?: 'publish',
            'orderby' => $request->get_param('orderby') ?: 'ordine',
            'order' => $request->get_param('order') ?: 'ASC'
        ];
        
        // Controlla se i dati sono in cache
        $items = Easy_Restaurant_Menu_Cache::get('items', $cache_params);
        
        if ($items === false) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'erm_items';
            
            // Parametri della richiesta
            $section_id = $cache_params['section_id'] ? intval($cache_params['section_id']) : null;
            $status = $cache_params['status'];
            $orderby = $cache_params['orderby'];
            $order = $cache_params['order'];
            
            // Sanitizzazione
            $orderby = sanitize_sql_orderby("$orderby $order") ?: 'ordine ASC';
            $status = sanitize_text_field($status);
            
            // Query
            $query = "SELECT * FROM $table_name WHERE status = %s";
            $params = [$status];
            
            // Aggiunge il filtro per section_id se specificato
            if ($section_id !== null) {
                $query .= " AND section_id = %d";
                $params[] = $section_id;
            }
            
            $query .= " ORDER BY $orderby";
            $items = $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
            
            // Memorizza i risultati nella cache
            Easy_Restaurant_Menu_Cache::set('items', $items, $cache_params);
        }
        
        return new WP_REST_Response($items, 200);
    }

    /**
     * Ottiene un elemento specifico
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function get_item(WP_REST_Request $request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'erm_items';
        
        $id = (int) $request['id'];
        $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
        
        if (!$item) {
            return new WP_Error('no_item', __('Elemento non trovato', 'easy-restaurant-menu'), ['status' => 404]);
        }
        
        // Aggiungi URL immagine
        if (!empty($item['immagine'])) {
            $item['immagine_url'] = wp_get_attachment_url($item['immagine']);
            $item['immagine_thumb'] = wp_get_attachment_thumb_url($item['immagine']);
        }
        
        return new WP_REST_Response($item, 200);
    }

    /**
     * Ottiene gli elementi di una sezione specifica
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function get_section_items(WP_REST_Request $request) {
        global $wpdb;
        $table_sections = $wpdb->prefix . 'erm_sections';
        $table_items = $wpdb->prefix . 'erm_items';
        
        $section_id = (int) $request['id'];
        
        // Verifica che la sezione esista
        $section = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_sections WHERE id = %d", $section_id), ARRAY_A);
        
        if (!$section) {
            return new WP_Error('no_section', __('Sezione non trovata', 'easy-restaurant-menu'), ['status' => 404]);
        }
        
        // Parametri opzionali
        $status = $request->get_param('status') ?: 'publish';
        
        // Sanitizzazione
        $status = sanitize_text_field($status);
        
        // Query
        $query = "SELECT * FROM $table_items WHERE section_id = %d AND status = %s ORDER BY ordine ASC";
        $items = $wpdb->get_results($wpdb->prepare($query, $section_id, $status), ARRAY_A);
        
        // Recupera e formatta i dati delle immagini
        foreach ($items as &$item) {
            if (!empty($item['immagine'])) {
                $item['immagine_url'] = wp_get_attachment_url($item['immagine']);
                $item['immagine_thumb'] = wp_get_attachment_thumb_url($item['immagine']);
            }
        }
        
        return new WP_REST_Response($items, 200);
    }

    /**
     * Crea un nuovo elemento
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function create_item(WP_REST_Request $request) {
        global $wpdb;
        $table_sections = $wpdb->prefix . 'erm_sections';
        $table_items = $wpdb->prefix . 'erm_items';
        
        $section_id = (int) $request['section_id'];
        
        // Verifica che la sezione esista
        $section = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_sections WHERE id = %d", $section_id), ARRAY_A);
        
        if (!$section) {
            return new WP_Error('no_section', __('Sezione non trovata', 'easy-restaurant-menu'), ['status' => 404]);
        }
        
        $titolo = sanitize_text_field($request['titolo']);
        $descrizione = sanitize_textarea_field($request['descrizione'] ?: '');
        $prezzo = (float) $request['prezzo'];
        $immagine = isset($request['immagine']) ? (int) $request['immagine'] : 0;
        $ordine = isset($request['ordine']) ? (int) $request['ordine'] : 0;
        $status = sanitize_text_field($request['status'] ?: 'publish');
        
        $data = [
            'section_id' => $section_id,
            'titolo' => $titolo,
            'descrizione' => $descrizione,
            'prezzo' => $prezzo,
            'immagine' => $immagine,
            'ordine' => $ordine,
            'status' => $status,
            'data_creazione' => current_time('mysql')
        ];
        
        $result = $wpdb->insert($table_items, $data, ['%d', '%s', '%s', '%f', '%d', '%d', '%s', '%s']);
        
        if (!$result) {
            return new WP_Error('db_error', __('Errore durante la creazione dell\'elemento', 'easy-restaurant-menu'), ['status' => 500]);
        }
        
        $data['id'] = $wpdb->insert_id;
        
        // Aggiungi URL immagine
        if (!empty($data['immagine'])) {
            $data['immagine_url'] = wp_get_attachment_url($data['immagine']);
            $data['immagine_thumb'] = wp_get_attachment_thumb_url($data['immagine']);
        }
        
        return new WP_REST_Response($data, 201);
    }

    /**
     * Aggiorna un elemento esistente
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function update_item(WP_REST_Request $request) {
        global $wpdb;
        $table_items = $wpdb->prefix . 'erm_items';
        $table_sections = $wpdb->prefix . 'erm_sections';
        
        $id = (int) $request['id'];
        
        // Verifica che l'elemento esista
        $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_items WHERE id = %d", $id), ARRAY_A);
        
        if (!$item) {
            return new WP_Error('no_item', __('Elemento non trovato', 'easy-restaurant-menu'), ['status' => 404]);
        }
        
        $section_id = isset($request['section_id']) ? (int) $request['section_id'] : $item['section_id'];
        
        // Se è stata modificata la sezione, verifica che esista
        if ($section_id !== $item['section_id']) {
            $section = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_sections WHERE id = %d", $section_id), ARRAY_A);
            
            if (!$section) {
                return new WP_Error('no_section', __('Sezione non trovata', 'easy-restaurant-menu'), ['status' => 404]);
            }
        }
        
        $titolo = sanitize_text_field($request['titolo'] ?: $item['titolo']);
        $descrizione = sanitize_textarea_field($request['descrizione'] ?: $item['descrizione']);
        $prezzo = isset($request['prezzo']) ? (float) $request['prezzo'] : $item['prezzo'];
        $immagine = isset($request['immagine']) ? (int) $request['immagine'] : $item['immagine'];
        $ordine = isset($request['ordine']) ? (int) $request['ordine'] : $item['ordine'];
        $status = sanitize_text_field($request['status'] ?: $item['status']);
        
        $data = [
            'section_id' => $section_id,
            'titolo' => $titolo,
            'descrizione' => $descrizione,
            'prezzo' => $prezzo,
            'immagine' => $immagine,
            'ordine' => $ordine,
            'status' => $status
        ];
        
        $result = $wpdb->update($table_items, $data, ['id' => $id], ['%d', '%s', '%s', '%f', '%d', '%d', '%s'], ['%d']);
        
        if ($result === false) {
            return new WP_Error('db_error', __('Errore durante l\'aggiornamento dell\'elemento', 'easy-restaurant-menu'), ['status' => 500]);
        }
        
        $data['id'] = $id;
        
        // Aggiungi URL immagine
        if (!empty($data['immagine'])) {
            $data['immagine_url'] = wp_get_attachment_url($data['immagine']);
            $data['immagine_thumb'] = wp_get_attachment_thumb_url($data['immagine']);
        }
        
        return new WP_REST_Response($data, 200);
    }

    /**
     * Elimina un elemento
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function delete_item(WP_REST_Request $request) {
        global $wpdb;
        $table_items = $wpdb->prefix . 'erm_items';
        
        $id = (int) $request['id'];
        
        // Verifica che l'elemento esista
        $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_items WHERE id = %d", $id), ARRAY_A);
        
        if (!$item) {
            return new WP_Error('no_item', __('Elemento non trovato', 'easy-restaurant-menu'), ['status' => 404]);
        }
        
        $result = $wpdb->delete($table_items, ['id' => $id], ['%d']);
        
        if (!$result) {
            return new WP_Error('db_error', __('Errore durante l\'eliminazione dell\'elemento', 'easy-restaurant-menu'), ['status' => 500]);
        }
        
        return new WP_REST_Response(true, 200);
    }

    /**
     * Schema per la validazione delle sezioni
     *
     * @return array
     * @since    1.0.0
     */
    private function get_section_schema(): array {
        return [
            'nome' => [
                'required' => true,
                'type' => 'string',
                'name' => __('Nome della sezione', 'easy-restaurant-menu'),
                'min_length' => 2,
                'max_length' => 100,
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => function($param) {
                    // Verifica che il nome non sia vuoto dopo la sanitizzazione
                    return !empty(trim($param));
                }
            ],
            'menu_id' => [
                'type' => 'integer',
                'name' => __('ID del menu', 'easy-restaurant-menu'),
                'min' => 1,
                'default' => 0,
                'sanitize_callback' => 'absint',
                'validate_callback' => function($param) {
                    global $wpdb;
                    if ($param <= 0) {
                        return true; // Consenti 0 come valore speciale
                    }
                    
                    // Verifica che il menu esista
                    $table_menus = $wpdb->prefix . 'erm_menus';
                    $menu_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_menus WHERE id = %d", $param));
                    
                    if (!$menu_exists) {
                        return new WP_Error(
                            'erm_menu_not_found',
                            __('Menu specificato non trovato', 'easy-restaurant-menu'),
                            ['status' => 404]
                        );
                    }
                    
                    return true;
                }
            ],
            'descrizione' => [
                'type' => 'string',
                'name' => __('Descrizione della sezione', 'easy-restaurant-menu'),
                'max_length' => 1000,
                'sanitize_callback' => 'wp_kses_post'
            ],
            'ordine' => [
                'type' => 'integer',
                'name' => __('Ordine di visualizzazione', 'easy-restaurant-menu'),
                'min' => 0,
                'max' => 999,
                'default' => 0,
                'sanitize_callback' => 'absint'
            ],
            'status' => [
                'type' => 'enum',
                'name' => __('Stato della pubblicazione', 'easy-restaurant-menu'),
                'enum' => ['publish', 'draft'],
                'default' => 'publish',
                'sanitize_callback' => 'sanitize_text_field'
            ]
        ];
    }

    /**
     * Schema per la validazione degli elementi
     *
     * @return array
     * @since    1.0.0
     */
    private function get_item_schema(): array {
        return [
            'section_id' => [
                'required' => true,
                'type' => 'integer'
            ],
            'titolo' => [
                'required' => true,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field'
            ],
            'descrizione' => [
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field'
            ],
            'prezzo' => [
                'type' => 'number',
                'default' => 0
            ],
            'immagine' => [
                'type' => 'integer'
            ],
            'ordine' => [
                'type' => 'integer'
            ],
            'status' => [
                'type' => 'string',
                'enum' => ['publish', 'draft'],
                'default' => 'publish',
                'sanitize_callback' => 'sanitize_text_field'
            ]
        ];
    }

    /**
     * Ottiene tutti i menu
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response
     * @since    1.0.0
     */
    public function get_menus(WP_REST_Request $request): WP_REST_Response {
        global $wpdb;
        $table_name = $wpdb->prefix . 'erm_menus';
        
        // Parametri opzionali
        $status = $request->get_param('status') ?: 'publish';
        $orderby = $request->get_param('orderby') ?: 'ordine';
        $order = $request->get_param('order') ?: 'ASC';
        
        // Sanitizzazione
        $orderby = sanitize_sql_orderby("$orderby $order") ?: 'ordine ASC';
        $status = sanitize_text_field($status);
        
        // Query
        $query = "SELECT * FROM $table_name WHERE status = %s ORDER BY $orderby";
        $menus = $wpdb->get_results($wpdb->prepare($query, $status), ARRAY_A);
        
        return new WP_REST_Response($menus, 200);
    }

    /**
     * Ottiene un menu specifico
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function get_menu(WP_REST_Request $request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'erm_menus';
        $menu_id = $request['id'];
        
        $menu = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $menu_id),
            ARRAY_A
        );
        
        if (empty($menu)) {
            return new WP_Error(
                'erm_menu_not_found',
                __('Menu non trovato', 'easy-restaurant-menu'),
                ['status' => 404]
            );
        }
        
        return new WP_REST_Response($menu, 200);
    }

    /**
     * Crea un nuovo menu
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function create_menu(WP_REST_Request $request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'erm_menus';
        
        $nome = sanitize_text_field($request['nome']);
        $descrizione = sanitize_textarea_field($request['descrizione'] ?? '');
        $ordine = isset($request['ordine']) ? intval($request['ordine']) : 0;
        $status = sanitize_text_field($request['status'] ?? 'publish');
        
        $result = $wpdb->insert(
            $table_name,
            [
                'nome' => $nome,
                'descrizione' => $descrizione,
                'ordine' => $ordine,
                'status' => $status
            ],
            ['%s', '%s', '%d', '%s']
        );
        
        if ($result === false) {
            return new WP_Error(
                'erm_menu_creation_error',
                __('Errore durante la creazione del menu', 'easy-restaurant-menu'),
                ['status' => 500]
            );
        }
        
        $menu_id = $wpdb->insert_id;
        
        $menu = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $menu_id),
            ARRAY_A
        );
        
        return new WP_REST_Response($menu, 201);
    }

    /**
     * Aggiorna un menu esistente
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function update_menu(WP_REST_Request $request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'erm_menus';
        $menu_id = $request['id'];
        
        // Verifica se il menu esiste
        $menu = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $menu_id),
            ARRAY_A
        );
        
        if (empty($menu)) {
            return new WP_Error(
                'erm_menu_not_found',
                __('Menu non trovato', 'easy-restaurant-menu'),
                ['status' => 404]
            );
        }
        
        $nome = sanitize_text_field($request['nome'] ?? $menu['nome']);
        $descrizione = sanitize_textarea_field($request['descrizione'] ?? $menu['descrizione']);
        $ordine = isset($request['ordine']) ? intval($request['ordine']) : $menu['ordine'];
        $status = sanitize_text_field($request['status'] ?? $menu['status']);
        
        $result = $wpdb->update(
            $table_name,
            [
                'nome' => $nome,
                'descrizione' => $descrizione,
                'ordine' => $ordine,
                'status' => $status
            ],
            ['id' => $menu_id],
            ['%s', '%s', '%d', '%s'],
            ['%d']
        );
        
        if ($result === false) {
            return new WP_Error(
                'erm_menu_update_error',
                __('Errore durante l\'aggiornamento del menu', 'easy-restaurant-menu'),
                ['status' => 500]
            );
        }
        
        $updated_menu = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $menu_id),
            ARRAY_A
        );
        
        return new WP_REST_Response($updated_menu, 200);
    }

    /**
     * Elimina un menu
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function delete_menu(WP_REST_Request $request) {
        global $wpdb;
        $menu_id = $request['id'];
        
        // Verifica se il menu esiste
        $table_menus = $wpdb->prefix . 'erm_menus';
        $menu = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_menus WHERE id = %d", $menu_id),
            ARRAY_A
        );
        
        if (empty($menu)) {
            return new WP_Error(
                'erm_menu_not_found',
                __('Menu non trovato', 'easy-restaurant-menu'),
                ['status' => 404]
            );
        }
        
        // Prima eliminiamo tutte le sezioni di questo menu
        $table_sections = $wpdb->prefix . 'erm_sections';
        $table_items = $wpdb->prefix . 'erm_items';
        
        // Ottieni tutte le sezioni di questo menu
        $sections = $wpdb->get_col($wpdb->prepare("SELECT id FROM $table_sections WHERE menu_id = %d", $menu_id));
        
        // Se ci sono sezioni, elimina tutti gli elementi associati
        if (!empty($sections)) {
            $sections_placeholders = implode(',', array_fill(0, count($sections), '%d'));
            $wpdb->query($wpdb->prepare("DELETE FROM $table_items WHERE section_id IN ($sections_placeholders)", $sections));
            
            // Ora elimina le sezioni
            $wpdb->delete($table_sections, ['menu_id' => $menu_id], ['%d']);
        }
        
        // Infine elimina il menu
        $result = $wpdb->delete($table_menus, ['id' => $menu_id], ['%d']);
        
        if ($result === false) {
            return new WP_Error(
                'erm_menu_deletion_error',
                __('Errore durante l\'eliminazione del menu', 'easy-restaurant-menu'),
                ['status' => 500]
            );
        }
        
        return new WP_REST_Response(['message' => __('Menu eliminato con successo', 'easy-restaurant-menu')], 200);
    }

    /**
     * Ottiene tutte le sezioni di un menu specifico
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function get_menu_sections(WP_REST_Request $request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'erm_sections';
        $menu_id = $request['id'];
        
        // Verifica se il menu esiste
        $table_menus = $wpdb->prefix . 'erm_menus';
        $menu = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_menus WHERE id = %d", $menu_id),
            ARRAY_A
        );
        
        if (empty($menu)) {
            return new WP_Error(
                'erm_menu_not_found',
                __('Menu non trovato', 'easy-restaurant-menu'),
                ['status' => 404]
            );
        }
        
        // Parametri opzionali
        $status = $request->get_param('status') ?: 'publish';
        $orderby = $request->get_param('orderby') ?: 'ordine';
        $order = $request->get_param('order') ?: 'ASC';
        
        // Sanitizzazione
        $orderby = sanitize_sql_orderby("$orderby $order") ?: 'ordine ASC';
        $status = sanitize_text_field($status);
        
        // Query
        $query = "SELECT * FROM $table_name WHERE menu_id = %d AND status = %s ORDER BY $orderby";
        $sections = $wpdb->get_results($wpdb->prepare($query, $menu_id, $status), ARRAY_A);
        
        return new WP_REST_Response($sections, 200);
    }

    /**
     * Schema per la validazione dei menu
     *
     * @return array
     * @since    1.0.0
     */
    private function get_menu_schema(): array {
        return [
            'nome' => [
                'required' => true,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => function($param) {
                    return !empty($param);
                }
            ],
            'descrizione' => [
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field'
            ],
            'ordine' => [
                'type' => 'integer',
                'default' => 0,
                'sanitize_callback' => 'absint'
            ],
            'status' => [
                'type' => 'string',
                'enum' => ['publish', 'draft'],
                'default' => 'publish',
                'sanitize_callback' => 'sanitize_text_field'
            ]
        ];
    }

    /**
     * Ottiene la struttura completa del menu con tutte le sezioni ed elementi
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response|WP_Error
     * @since    1.0.0
     */
    public function get_complete_menu(WP_REST_Request $request) {
        $menu_id = $request->get_param('id');
        
        // Prepara i parametri per la chiave di cache
        $cache_params = [
            'menu_id' => $menu_id,
            'with_items' => $request->get_param('with_items') !== 'false' // Default: true
        ];
        
        // Controlla se i dati sono in cache
        $menu_data = Easy_Restaurant_Menu_Cache::get('complete_menu', $cache_params);
        
        if ($menu_data === false) {
            global $wpdb;
            $table_menus = $wpdb->prefix . 'erm_menus';
            
            try {
                // Ottieni i dati del menu
                if ($menu_id) {
                    $menu = $wpdb->get_row(
                        $wpdb->prepare("SELECT * FROM $table_menus WHERE id = %d", intval($menu_id)),
                        ARRAY_A
                    );
                    
                    if (!$menu) {
                        return new WP_Error(
                            'erm_menu_not_found',
                            __('Menu non trovato', 'easy-restaurant-menu'),
                            ['status' => 404]
                        );
                    }
                } else {
                    // Se non viene specificato un ID menu, ottieni il primo menu disponibile
                    $menu = $wpdb->get_row(
                        "SELECT * FROM $table_menus WHERE status = 'publish' ORDER BY ordine ASC LIMIT 1",
                        ARRAY_A
                    );
                    
                    if (!$menu) {
                        return new WP_Error(
                            'erm_no_menus',
                            __('Nessun menu disponibile', 'easy-restaurant-menu'),
                            ['status' => 404]
                        );
                    }
                    
                    $menu_id = $menu['id'];
                }
                
                // Ottieni le sezioni del menu
                $table_sections = $wpdb->prefix . 'erm_sections';
                $sections = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM $table_sections WHERE menu_id = %d AND status = 'publish' ORDER BY ordine ASC", 
                        intval($menu_id)
                    ),
                    ARRAY_A
                );
                
                // Se richiesto, ottieni anche gli elementi per ogni sezione
                if ($cache_params['with_items']) {
                    $table_items = $wpdb->prefix . 'erm_items';
                    
                    foreach ($sections as &$section) {
                        $section['items'] = $wpdb->get_results(
                            $wpdb->prepare(
                                "SELECT * FROM $table_items WHERE section_id = %d AND status = 'publish' ORDER BY ordine ASC",
                                $section['id']
                            ),
                            ARRAY_A
                        );
                        
                        // Aggiungi le URL delle immagini
                        foreach ($section['items'] as &$item) {
                            if (!empty($item['immagine'])) {
                                $item['immagine_url'] = wp_get_attachment_url($item['immagine']);
                                $item['immagine_thumb'] = wp_get_attachment_thumb_url($item['immagine']);
                            }
                        }
                    }
                }
                
                $menu_data = [
                    'menu' => $menu,
                    'sections' => $sections
                ];
                
                // Salva nella cache con una durata più lunga (2 ore) poiché è una query complessa
                Easy_Restaurant_Menu_Cache::set('complete_menu', $menu_data, $cache_params, 7200);
                
            } catch (Exception $e) {
                return new WP_Error(
                    'erm_db_error',
                    $e->getMessage(),
                    ['status' => 500]
                );
            }
        }
        
        return new WP_REST_Response($menu_data, 200);
    }

    /**
     * Valida i parametri di una richiesta di sezione
     *
     * @param array $params Parametri della richiesta
     * @return true|WP_Error Restituisce true se tutti i parametri sono validi, altrimenti un oggetto WP_Error
     */
    public function validate_section_request($params) {
        $schema = $this->get_section_schema();
        $errors = new WP_Error();
        
        foreach ($schema as $field => $rules) {
            // Ottiene il valore del parametro dalla richiesta
            $param_value = isset($params[$field]) ? $params[$field] : null;
            
            // Valida il parametro
            $result = $this->validate_request_param($param_value, $rules);
            
            // Se la validazione fallisce, aggiungi l'errore
            if (is_wp_error($result)) {
                $errors->add(
                    $result->get_error_code(),
                    $result->get_error_message(),
                    ['field' => $field]
                );
            }
            
            // Applica la funzione di sanitizzazione se presente
            if (!is_wp_error($result) && isset($rules['sanitize_callback']) && is_callable($rules['sanitize_callback'])) {
                $params[$field] = call_user_func($rules['sanitize_callback'], $param_value);
            }
        }
        
        // Se ci sono errori, restituisci l'oggetto WP_Error
        if ($errors->has_errors()) {
            return $errors;
        }
        
        return true;
    }

    /**
     * Recupera le opzioni globali del plugin per il blocco Gutenberg
     *
     * @param WP_REST_Request $request La richiesta REST API
     * @return WP_REST_Response La risposta contenente le opzioni globali
     * @since    1.1.0
     */
    public function get_global_options(WP_REST_Request $request) {
        $options = [
            'currency_symbol' => get_option('erm_currency_symbol', '€'),
            'currency_position' => get_option('erm_currency_position', 'after'),
            'price_decimal_separator' => get_option('erm_price_decimal_separator', ','),
            'price_thousand_separator' => get_option('erm_price_thousand_separator', '.'),
            'price_decimals' => (int)get_option('erm_price_decimals', 2),
            'price_format_template' => get_option('erm_price_format_template', '%s'),
            'default_layout' => get_option('erm_default_layout', 'list'),
            'style_preset' => get_option('erm_style_preset', 'elegante')
        ];
        
        // Carica e aggiungi i dettagli del preset di stile selezionato
        if (!class_exists('EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Options')) {
            Easy_Restaurant_Menu_Helper::using('inc/class-easy-restaurant-menu-options.php');
        }
        
        $available_presets = Easy_Restaurant_Menu_Options::get_style_presets();
        $selected_preset = $options['style_preset'];
        
        if (isset($available_presets[$selected_preset])) {
            $options['preset_details'] = $available_presets[$selected_preset];
        }
        
        return new WP_REST_Response($options, 200);
    }
} 