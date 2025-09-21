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

        if( file_exists( CSM_INCLUDES_DIR.'settings.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'settings.php' );
        }

        if( file_exists( CSM_INCLUDES_DIR.'front.php' ) ) {
            require_once CSM_INCLUDES_DIR . 'front.php';
        }

        if( file_exists( CSM_INCLUDES_DIR.'menu-shortcode.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'menu-shortcode.php' );
        }

        if( file_exists( CSM_INCLUDES_DIR.'sts-shortcode.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'sts-shortcode.php' );
        }

        if( file_exists( CSM_INCLUDES_DIR.'sbm-shortcode.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'sbm-shortcode.php' );
        }

        if( file_exists( CSM_INCLUDES_DIR.'port-congestion-shortcode.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'port-congestion-shortcode.php' );
        }

        if( file_exists( CSM_INCLUDES_DIR.'vessels-shortcode.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'vessels-shortcode.php' );
        }

        if( file_exists( CSM_INCLUDES_DIR.'port-congestion-home-shortcode.php' ) ) {
            require_once( CSM_INCLUDES_DIR . 'port-congestion-home-shortcode.php' );
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