<?php
/**
 * Privacy related functionality.
 *
 * Handles privacy policy text and data export/erasure.
 *
 * @since      1.0.0
 * @package    Easy_Restaurant_Menu
 * @subpackage Easy_Restaurant_Menu/inc
 */

namespace EASY_RESTAURANT_MENU;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Easy_Restaurant_Menu_Privacy {

    /**
     * Initialize the privacy functionality
     *
     * @since    1.0.0
     */
    public function initialize(): void {
        // Register the privacy policy content
        add_action('admin_init', [$this, 'add_privacy_policy_content']);
        
        // Register the data exporter
        add_filter('wp_privacy_personal_data_exporters', [$this, 'register_data_exporter']);
        
        // Register the data eraser
        add_filter('wp_privacy_personal_data_erasers', [$this, 'register_data_eraser']);
    }

    /**
     * Add the privacy policy text to the privacy policy page
     *
     * @since    1.0.0
     */
    public function add_privacy_policy_content(): void {
        if (!function_exists('wp_add_privacy_policy_content')) {
            return;
        }

        $content = sprintf(
            '<h2>%s</h2><p>%s</p>',
            __('Easy Restaurant Menu Plugin', 'easy-restaurant-menu'),
            __('This plugin does not collect or store any personal data from visitors. All menu data is stored in your WordPress database and is not shared with any external services.', 'easy-restaurant-menu')
        );

        wp_add_privacy_policy_content('Easy Restaurant Menu', wp_kses_post($content));
    }

    /**
     * Register the data exporter function
     *
     * @param array $exporters Array of existing exporters
     * @return array Modified array of exporters
     * @since    1.0.0
     */
    public function register_data_exporter(array $exporters): array {
        $exporters['easy-restaurant-menu'] = array(
            'exporter_friendly_name' => __('Easy Restaurant Menu Data', 'easy-restaurant-menu'),
            'callback'               => [$this, 'personal_data_exporter'],
        );
        return $exporters;
    }

    /**
     * Register the data eraser function
     *
     * @param array $erasers Array of existing erasers
     * @return array Modified array of erasers
     * @since    1.0.0
     */
    public function register_data_eraser(array $erasers): array {
        $erasers['easy-restaurant-menu'] = array(
            'eraser_friendly_name' => __('Easy Restaurant Menu Data', 'easy-restaurant-menu'),
            'callback'            => [$this, 'personal_data_eraser'],
        );
        return $erasers;
    }

    /**
     * Export personal data
     * 
     * Note: This plugin doesn't store personal data, but the function is 
     * included for completeness and to allow future extensions
     *
     * @param string $email_address Email address of the user
     * @param int    $page          Page number
     * @return array Export data
     * @since    1.0.0
     */
    public function personal_data_exporter(string $email_address, int $page = 1): array {
        $export_items = [];
        $user = get_user_by('email', $email_address);
        
        // If no user found, return empty data
        if (!$user) {
            return [
                'data' => [],
                'done' => true,
            ];
        }
        
        // This plugin doesn't currently store any personal data, 
        // but this function is structured to support future extensions
        
        return [
            'data' => $export_items,
            'done' => true,
        ];
    }

    /**
     * Erase personal data
     * 
     * Note: This plugin doesn't store personal data, but the function is 
     * included for completeness and to allow future extensions
     *
     * @param string $email_address Email address of the user
     * @param int    $page          Page number
     * @return array Status of the erasure
     * @since    1.0.0
     */
    public function personal_data_eraser(string $email_address, int $page = 1): array {
        $user = get_user_by('email', $email_address);
        
        // If no user found, return empty data
        if (!$user) {
            return [
                'items_removed'  => false,
                'items_retained' => false,
                'messages'       => [],
                'done'           => true,
            ];
        }
        
        // This plugin doesn't currently store any personal data, 
        // so nothing needs to be erased
        
        return [
            'items_removed'  => false,
            'items_retained' => false,
            'messages'       => [
                __('Easy Restaurant Menu plugin does not store any personal data.', 'easy-restaurant-menu')
            ],
            'done'           => true,
        ];
    }
} 