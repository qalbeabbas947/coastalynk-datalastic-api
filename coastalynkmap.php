<?php
/**
 * Plugin Name:       Coastalynk Map
 * Description:       Displays a map for the end user.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       castalynkmap
 *
 * @package CreateBlock
 */

/**
 * Class Coastalynk_Sea_Vessel_Map
 */
class Coastalynk_Sea_Vessel_Map {

    const VERSION = '1.0';

    /**
     * @var self
     */
    private static $instance = null;

    /**
     * @since 1.0
     * @return $this
     */
    public static function instance() {

        if ( is_null( self::$instance ) && ! ( self::$instance instanceof Coastalynk_Sea_Vessel_Map ) ) {
            self::$instance = new self;

            load_plugin_textdomain( 'castalynkmap' );
            self::$instance->setup_constants();
            self::$instance->includes();
        }

        return self::$instance;
    }

    /**
     * defining constants for plugin
     */
    public function setup_constants() {

        /**
         * Directory
         */
        define( 'CSM_DIR', plugin_dir_path ( __FILE__ ) );
        define( 'CSM_DIR_FILE', CSM_DIR . basename ( __FILE__ ) );
        define( 'CSM_INCLUDES_DIR', trailingslashit ( CSM_DIR . 'includes' ) );
        define( 'CSM_TEMPLATES_DIR', trailingslashit ( CSM_DIR . 'templates' ) );
        define( 'CSM_BASE_DIR', plugin_basename(__FILE__));

        /**
         * URLs
         */
        define( 'CSM_URL', trailingslashit ( plugins_url ( '', __FILE__ ) ) );
        define( 'CSM_IMAGES_URL', trailingslashit ( CSM_URL . 'images/' ) );
        define( 'CSM_CSS_URL', trailingslashit ( CSM_URL . 'css/' ) );
        define( 'CSM_JS_URL', trailingslashit ( CSM_URL . 'js/' ) );
        /**
         * plugin Version
         */
        define( 'CSM_VERSION', self::VERSION );
        
        define( 'ADMIN_USER', 'administrator' );
        define( 'GENERAL_USER', 'pmpro_role_1' );
        define( 'SHIPLINE_USER', 'pmpro_role_2' );
        define( 'PORT_OPERATOR_USER', 'pmpro_role_3' );
        define( 'REGULATOR_USER', 'pmpro_role_4' );


    }

    /**
     * Plugin requiered files
     */
    public function includes() {

        /**
         * Required all files 
         */

        if( file_exists( CSM_INCLUDES_DIR.'helper.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'helper.php' );
        }

        if( is_admin() ) {
            if( file_exists( CSM_INCLUDES_DIR.'settings.php' ) ) {
                require_once( CSM_INCLUDES_DIR . 'settings.php' );
            }

            if( file_exists( CSM_INCLUDES_DIR.'admin.php' ) ) {
                require_once( CSM_INCLUDES_DIR . 'admin.php' );
            }

            if( file_exists( CSM_INCLUDES_DIR . 'admin/darkships/class-menu.php' ) ) {
                require_once CSM_INCLUDES_DIR . 'admin/darkships/class-menu.php';
            }

            if( file_exists( CSM_INCLUDES_DIR . 'admin/darkships/class-darkships.php' ) ) {
                require_once CSM_INCLUDES_DIR . 'admin/darkships/class-darkships.php';
            }
        }
        if( file_exists( CSM_INCLUDES_DIR.'front.php' ) ) {
            require_once CSM_INCLUDES_DIR . 'front.php';
        }

        if( file_exists( CSM_INCLUDES_DIR.'shortcodes/levy_calculator.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'shortcodes/levy_calculator.php' );
        }

        // if( file_exists( CSM_INCLUDES_DIR.'menu-shortcode.php' ) ) {
        //     require_once( CSM_INCLUDES_DIR . 'menu-shortcode.php' );
        // }

        if( file_exists( CSM_INCLUDES_DIR.'shortcodes/sts-shortcode.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'shortcodes/sts-shortcode.php' );
        }

        if( file_exists( CSM_INCLUDES_DIR.'shortcodes/sbm-shortcode.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'shortcodes/sbm-shortcode.php' );
        }

        if( file_exists( CSM_INCLUDES_DIR.'shortcodes/port-congestion-shortcode.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'shortcodes/port-congestion-shortcode.php' );
        }

        if( file_exists( CSM_INCLUDES_DIR.'shortcodes/vessels-shortcode.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'shortcodes/vessels-shortcode.php' );
        }

        if( file_exists( CSM_INCLUDES_DIR.'shortcodes/port-congestion-home-shortcode.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'shortcodes/port-congestion-home-shortcode.php' );
        }
    }
}

/**
 * @return bool
 */
function Coastalynk_Map_Load() {
    
    return Coastalynk_Sea_Vessel_Map::instance();
}
add_action( 'plugins_loaded', 'Coastalynk_Map_Load' );