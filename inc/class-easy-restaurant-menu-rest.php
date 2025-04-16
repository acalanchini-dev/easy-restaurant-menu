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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Easy_Restaurant_Menu_REST {

    /**
     * Namespace dell'API
     *
     * @var string
     */
    private $namespace = 'easy-restaurant-menu/v1';

    /**
     * Inizializza la REST API
     *
     * @since    1.0.0
     */
    public function initialize(): void {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Registra le rotte per la REST API
     *
     * @since    1.0.0
     */
    public function register_routes(): void {
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
        global $wpdb;
        $table_name = $wpdb->prefix . 'erm_sections';
        
        // Parametri opzionali
        $status = $request->get_param('status') ?: 'publish';
        $orderby = $request->get_param('orderby') ?: 'ordine';
        $order = $request->get_param('order') ?: 'ASC';
        
        // Sanitizzazione
        $orderby = sanitize_sql_orderby("$orderby $order") ?: 'ordine ASC';
        $status = sanitize_text_field($status);
        
        // Query
        $query = "SELECT * FROM $table_name WHERE status = %s ORDER BY $orderby";
        $sections = $wpdb->get_results($wpdb->prepare($query, $status), ARRAY_A);
        
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
        $descrizione = sanitize_textarea_field($request['descrizione'] ?: '');
        $ordine = isset($request['ordine']) ? (int) $request['ordine'] : 0;
        $status = sanitize_text_field($request['status'] ?: 'publish');
        
        $data = [
            'nome' => $nome,
            'descrizione' => $descrizione,
            'ordine' => $ordine,
            'status' => $status,
            'data_creazione' => current_time('mysql')
        ];
        
        $result = $wpdb->insert($table_name, $data, ['%s', '%s', '%d', '%s', '%s']);
        
        if (!$result) {
            return new WP_Error('db_error', __('Errore durante la creazione della sezione', 'easy-restaurant-menu'), ['status' => 500]);
        }
        
        $data['id'] = $wpdb->insert_id;
        
        return new WP_REST_Response($data, 201);
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
        
        $id = (int) $request['id'];
        
        // Verifica che la sezione esista
        $section = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
        
        if (!$section) {
            return new WP_Error('no_section', __('Sezione non trovata', 'easy-restaurant-menu'), ['status' => 404]);
        }
        
        $nome = sanitize_text_field($request['nome']);
        $descrizione = sanitize_textarea_field($request['descrizione'] ?: '');
        $ordine = isset($request['ordine']) ? (int) $request['ordine'] : $section['ordine'];
        $status = sanitize_text_field($request['status'] ?: $section['status']);
        
        $data = [
            'nome' => $nome,
            'descrizione' => $descrizione,
            'ordine' => $ordine,
            'status' => $status
        ];
        
        $result = $wpdb->update($table_name, $data, ['id' => $id], ['%s', '%s', '%d', '%s'], ['%d']);
        
        if ($result === false) {
            return new WP_Error('db_error', __('Errore durante l\'aggiornamento della sezione', 'easy-restaurant-menu'), ['status' => 500]);
        }
        
        $data['id'] = $id;
        
        return new WP_REST_Response($data, 200);
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
     * Ottiene tutti gli elementi
     *
     * @param WP_REST_Request $request Richiesta REST
     * @return WP_REST_Response
     * @since    1.0.0
     */
    public function get_items(WP_REST_Request $request): WP_REST_Response {
        global $wpdb;
        $table_name = $wpdb->prefix . 'erm_items';
        
        // Parametri opzionali
        $status = $request->get_param('status') ?: 'publish';
        $orderby = $request->get_param('orderby') ?: 'ordine';
        $order = $request->get_param('order') ?: 'ASC';
        
        // Sanitizzazione
        $orderby = sanitize_sql_orderby("$orderby $order") ?: 'ordine ASC';
        $status = sanitize_text_field($status);
        
        // Query
        $query = "SELECT * FROM $table_name WHERE status = %s ORDER BY $orderby";
        $items = $wpdb->get_results($wpdb->prepare($query, $status), ARRAY_A);
        
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
                'sanitize_callback' => 'sanitize_text_field'
            ],
            'descrizione' => [
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field'
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
} 