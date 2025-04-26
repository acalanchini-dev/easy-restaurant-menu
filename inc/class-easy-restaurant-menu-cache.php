<?php
/**
 * Gestisce il caching del plugin utilizzando la Transient API di WordPress.
 *
 * Questa classe fornisce metodi per memorizzare, recuperare e invalidare la cache
 * per migliorare le prestazioni del plugin Easy Restaurant Menu.
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/inc
 */

namespace EASY_RESTAURANT_MENU;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Easy_Restaurant_Menu_Cache {

    /**
     * Prefisso per le chiavi di cache
     *
     * @var string
     */
    private static $prefix = 'erm_cache_';
    
    /**
     * Durata predefinita della cache in secondi (1 ora)
     *
     * @var int
     */
    private static $default_expiration = 3600;
    
    /**
     * Controlla se il caching è abilitato
     *
     * @return bool
     */
    public static function is_enabled(): bool {
        return \get_option('erm_enable_caching', true);
    }
    
    /**
     * Ottiene il tempo di scadenza dalla configurazione
     *
     * @return int Tempo di scadenza in secondi
     */
    public static function get_expiration_time(): int {
        $expiration = \get_option('erm_cache_expiration', self::$default_expiration);
        return (int) $expiration;
    }
    
    /**
     * Genera una chiave di cache unica
     *
     * @param string $key_base Base della chiave
     * @param array $params Parametri da includere nella chiave
     * @return string
     */
    public static function generate_key(string $key_base, array $params = []): string {
        // Ordina i parametri per garantire che la stessa query produca la stessa chiave
        if (!empty($params)) {
            ksort($params);
            $param_string = md5(wp_json_encode($params));
            return self::$prefix . $key_base . '_' . $param_string;
        }
        
        return self::$prefix . $key_base;
    }
    
    /**
     * Ottiene un elemento dalla cache
     *
     * @param string $key_base Base della chiave
     * @param array $params Parametri usati per generare la chiave completa
     * @return mixed|false Dati dalla cache o false se non trovati
     */
    public static function get(string $key_base, array $params = []) {
        if (!self::is_enabled()) {
            return false;
        }
        
        $key = self::generate_key($key_base, $params);
        $data = \get_transient($key);
        
        // Incrementa il contatore di hit sulla cache se i dati sono trovati
        if ($data !== false) {
            self::increment_hit_counter();
        } else {
            self::increment_miss_counter();
        }
        
        return $data;
    }
    
    /**
     * Memorizza dati nella cache
     *
     * @param string $key_base Base della chiave
     * @param mixed $data Dati da memorizzare
     * @param array $params Parametri usati per generare la chiave completa
     * @param int|null $expiration Tempo di scadenza in secondi (null per usare il valore predefinito)
     * @return bool Successo o fallimento
     */
    public static function set(string $key_base, $data, array $params = [], ?int $expiration = null): bool {
        if (!self::is_enabled()) {
            return false;
        }
        
        $key = self::generate_key($key_base, $params);
        
        // Usa il tempo di scadenza specificato o quello predefinito dalle impostazioni
        $expiration = $expiration ?? self::get_expiration_time();
        
        return \set_transient($key, $data, $expiration);
    }
    
    /**
     * Elimina un elemento specifico dalla cache
     *
     * @param string $key_base Base della chiave
     * @param array $params Parametri usati per generare la chiave completa
     * @return bool Successo o fallimento
     */
    public static function delete(string $key_base, array $params = []): bool {
        $key = self::generate_key($key_base, $params);
        return \delete_transient($key);
    }
    
    /**
     * Elimina tutti gli elementi della cache relativi a una base specifica
     * Utilizza la ricerca nel database poiché WordPress non offre un metodo diretto
     *
     * @param string $key_base Base della chiave per gli elementi da eliminare
     * @return int Numero di elementi eliminati
     */
    public static function delete_group(string $key_base): int {
        global $wpdb;
        
        $prefix = self::$prefix . $key_base . '_';
        $option_name_like = '_transient_' . $prefix . '%';
        
        // Trova tutte le transient che corrispondono al pattern
        $transients = $wpdb->get_col($wpdb->prepare(
            "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
            $option_name_like
        ));
        
        $count = 0;
        
        foreach ($transients as $transient) {
            $transient_name = str_replace('_transient_', '', $transient);
            if (\delete_transient($transient_name)) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Svuota completamente la cache del plugin
     *
     * @return int Numero di elementi eliminati
     */
    public static function flush_all(): int {
        global $wpdb;
        
        $option_name_like = '_transient_' . self::$prefix . '%';
        
        // Trova tutte le transient che corrispondono al pattern
        $transients = $wpdb->get_col($wpdb->prepare(
            "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
            $option_name_like
        ));
        
        $count = 0;
        
        foreach ($transients as $transient) {
            $transient_name = str_replace('_transient_', '', $transient);
            if (\delete_transient($transient_name)) {
                $count++;
            }
        }
        
        // Resetta le statistiche
        \update_option('erm_cache_hits', 0);
        \update_option('erm_cache_misses', 0);
        \update_option('erm_cache_last_flush', time());
        
        return $count;
    }
    
    /**
     * Incrementa il contatore di hit
     */
    private static function increment_hit_counter(): void {
        $hits = (int) \get_option('erm_cache_hits', 0);
        \update_option('erm_cache_hits', $hits + 1);
    }
    
    /**
     * Incrementa il contatore di miss
     */
    private static function increment_miss_counter(): void {
        $misses = (int) \get_option('erm_cache_misses', 0);
        \update_option('erm_cache_misses', $misses + 1);
    }
    
    /**
     * Ottiene le statistiche della cache
     *
     * @return array Statistiche della cache
     */
    public static function get_stats(): array {
        $hits = (int) \get_option('erm_cache_hits', 0);
        $misses = (int) \get_option('erm_cache_misses', 0);
        $total = $hits + $misses;
        $hit_ratio = $total > 0 ? round(($hits / $total) * 100, 2) : 0;
        $last_flush = (int) \get_option('erm_cache_last_flush', 0);
        
        return [
            'hits' => $hits,
            'misses' => $misses,
            'total' => $total,
            'hit_ratio' => $hit_ratio,
            'last_flush' => $last_flush ? date('Y-m-d H:i:s', $last_flush) : __('Mai', 'easy-restaurant-menu')
        ];
    }
} 