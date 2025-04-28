<?php
/**
 * Plugin Name:       Easy Restaurant Menu
 * Plugin URI:        https://github.com/acalanchini-dev/easy-restaurant-menu
 * Description:       A simple plugin to create and manage restaurant menus with a customizable Gutenberg block.
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            Alessio Calanchini
 * Author URI:        https://github.com/acalanchini-dev
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       easy-restaurant-menu
 * Domain Path:       /languages
 * @package           Easy_Restaurant_Menu
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) or die;

use EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Activator;
use EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Deactivator;
use EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Core;
use EASY_RESTAURANT_MENU\Easy_Restaurant_Menu_Helper;

// Check WordPress version
if ( version_compare( $GLOBALS['wp_version'], '5.9', '<' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="error"><p>';
        echo sprintf(
            /* translators: %s: WordPress version */
            esc_html__( 'Easy Restaurant Menu requires WordPress version %s or higher. Please upgrade WordPress and try again.', 'easy-restaurant-menu' ),
            '5.9'
        );
        echo '</p></div>';
    });
    return;
}

// Check PHP version
if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="error"><p>';
        echo sprintf(
            /* translators: %s: PHP version */
            esc_html__( 'Easy Restaurant Menu requires PHP version %s or higher. Please upgrade PHP and try again.', 'easy-restaurant-menu' ),
            '7.0'
        );
        echo '</p></div>';
    });
    return;
}

// Define constants
$plugin_data = get_file_data( __FILE__, array( 'version' => 'Version' ) );
define( 'EASY_RESTAURANT_MENU_VERSION', $plugin_data['version'] );
define( 'EASY_RESTAURANT_MENU_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'EASY_RESTAURANT_MENU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

//Get helper functions at first.
require EASY_RESTAURANT_MENU_PLUGIN_PATH . 'inc/class-easy-restaurant-menu-helper.php';


/**
 * The code that runs during plugin activation.
 * This action is documented in inc/Activator.php
 * @since    1.0.0
 */
function easy_restaurant_menu_activate() {

	Easy_Restaurant_Menu_Helper::using( 'inc/class-easy-restaurant-menu-activator.php' );

	Easy_Restaurant_Menu_Activator::activate();

}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in inc/Deactivator.php
 * @since    1.0.0
 */
function easy_restaurant_menu_deactivate() {

	Easy_Restaurant_Menu_Helper::using( 'inc/class-easy-restaurant-menu-deactivator.php' );

	Easy_Restaurant_Menu_Deactivator::deactivate();

}

/**
 * Register activation and deactivation hooks
 * @since    1.0.0
 */
register_activation_hook( __FILE__, 'easy_restaurant_menu_activate' );
register_deactivation_hook( __FILE__, 'easy_restaurant_menu_deactivate' );

/**
 * Carica la classe Loader prima della classe Core
 * @since    1.0.0
 */
Easy_Restaurant_Menu_Helper::using( 'inc/class-easy-restaurant-menu-loader.php' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, public-facing site hooks and more...
 */
Easy_Restaurant_Menu_Helper::using( 'inc/class-easy-restaurant-menu-core.php' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
$plugin = new Easy_Restaurant_Menu_Core();

$plugin->run();
