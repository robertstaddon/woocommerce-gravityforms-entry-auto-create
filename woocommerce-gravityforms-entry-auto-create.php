<?php
/**
 * Plugin Name: WooCommerce Gravity Forms Entry Auto-Create
 * Description: Automatically create one or more Gravity Form entries with a WooCommerce purchase
 * Version: 1.0
 * Author: Abundant Designs LLC
 * Author URI: https://www.abundantdesigns.com/
 * License: GPLv2 or later
 */
namespace WooCommerceGravityFormsEntryAutoCreate;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main plugin class
 */
class Plugin {

    /**
     * The Main instance
     *
     * @access private
     * @var object $instance
     */
    private static $instance;       

    /**
     * The Admin Settings instance
     *
     * @access private
     * @var object $settings
     */
    private static $settings;
    
    /**
     * The Form instance
     *
     * @access private
     * @var object $api
     */
    private static $form;

    /**
     * Instantiate the main class
     *
     * This function instantiates the class, initialize all functions and return the object.
     * 
     * @return object The LearnDash_Muvi instance.
     */
    public static function instance() {

        if ( ! isset( self::$instance ) && ( ! self::$instance instanceof Plugin ) ) {

            self::$instance = new Plugin();
            self::$instance->setup_constants();
            
            add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
            add_action( 'after_plugin_row_' . plugin_basename( PLUGIN_FILE ), array( self::$instance, 'dependency_warning' ), 10, 2 );

            self::$instance->includes();
            
        }

        return self::$instance;
    }

    /**
     * Function for setting up constants
     *
     * This function is used to set up constants used throughout the plugin.
     */
    public function setup_constants() {

        // Plugin version
        define( __NAMESPACE__ . '\VERSION', '1.0' );

        // Plugin text domain
        define( __NAMESPACE__ . '\TEXT_DOMAIN', 'woocommerce-gravityforms-entry-auto-create' );

        // Plugin file
        define( __NAMESPACE__ . '\PLUGIN_FILE', __FILE__ );

        // Plugin folder path
        define( __NAMESPACE__ . '\PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

        // Plugin folder URL
        define( __NAMESPACE__ . '\PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Load text domain used for translation
     *
     * This function loads mo and po files used to translate text strings used throughout the 
     * plugin.
     */
    public function load_textdomain() {

        // Set filter for plugin language directory
        $lang_dir = dirname( plugin_basename( PLUGIN_FILE ) ) . '/languages/';
        $lang_dir = apply_filters( 'learndash_muvi_languages_directory', $lang_dir );

        // Load plugin translation file
        load_plugin_textdomain( TEXT_DOMAIN, false, $lang_dir );
    }     


    /**
     * Display dependency warning after plugin row
     */
    public function dependency_warning( $plugin_file, $plugin_data ) {

        if ( class_exists( 'GFAPI' ) && class_exists( 'WooCommerce' ) ) {
            return;
        }
    
        $is_activated = ! is_network_admin() && is_plugin_active( plugin_basename( $plugin_file ) );
        $is_network_activated = is_network_admin() && is_plugin_active_for_network( plugin_basename( $plugin_file ) );
    
        if ( $is_activated || $is_network_activated ): ?>
    
            <style type="text/css" scoped>
                <?php printf( '#%1$s td, #%1$s th', sanitize_title( $plugin_data['Name'] ) ); ?>,
                <?php printf( 'tr[data-slug="%1$s"] td, tr[data-slug="%1$s"] th', sanitize_title( $plugin_data['Name'] ) ); ?> { border-bottom: 0; box-shadow: none !important; -webkit-box-shadow: none !important; }
                .gwp-plugin-notice td { padding: 0 !important; }
                .gwp-plugin-notice .update-message p:before { content: '\f534'; font-size: 18px; }
            </style>
    
            <tr class="plugin-update-tr active gwp-plugin-notice">
                <td colspan="3" class="colspanchange">
                    <div class="update-message notice inline notice-error notice-alt"><p><?php printf( __( 'This plugin requires WooCommerce and Gravity Forms to be activated.' ) ); ?></p></div>
                </td>
            </tr>
    
        <?php endif;
    
    }


    /**
     * Includes all necessary PHP files
     *
     * This function is responsible for including all necessary PHP files.
     */
    public function includes() {	

        if ( is_admin() ) {
            include PLUGIN_PATH . '/includes/admin/class-settings.php';
            self::$settings = new Settings();
        }
        
        include PLUGIN_PATH . '/includes/class-form.php';
        self::$form = new Form();
    }


}


/**
 * The main function for returning instance
 */
function launch_plugin() {
    return Plugin::instance();
}

// Run plugin
launch_plugin();
