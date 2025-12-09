<?php
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Coastalynk_Admin
 */
class Coastalynk_Admin {

	private $page_tab;
    
    /**
     * Constructor function
     */
    public function __construct() {

        add_action( 'admin_menu', [ $this, 'setting_menu' ], 1 );

        /**
         * Save screen options for dark_ships per page
         */
        add_filter( 'set-screen-option', function( $status, $option, $value ){
            return ( $option == 'dark_ships_per_page' ) ? (int) $value : $status;
        }, 10, 3 );

       // add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_scripts' ] );
    }
    
    /**
     * Return loader image
     * 
     * @since 1.0
     * @return $this
     */
    public static function get_bar_preloader( $class = 'csm-subssummary-loader' ) {
        
        Ob_start();
        ?>
            <img width="30px" class="<?php echo $class; ?>" src="<?php echo CSM_IMAGES_URL  . 'bar-preloader.gif'; ?>" />
        <?php
		
        $return  = ob_get_contents();
        ob_end_clean();
		
        return $return;
    }

    /**
     * Adds frontend scripts
     */
    public function add_admin_scripts() {
        
        wp_enqueue_style( 'cem-admin-css', CEM_ASSETS_URL . 'css/admin.css', [], time(), null );
        wp_enqueue_script( 'cem-admin-js', CEM_ASSETS_URL . 'js/admin.js', [ 'jquery' ], time(), true );

        wp_localize_script( 'cem-admin-js', 'CEM_Email', [
            'ajaxURL'       => admin_url( 'admin-ajax.php' )
           ] );
    }

    /**
     * Add new setting menu under WooCommerce menu
     */
    public function setting_menu() {
        
        /**
         * Add Setting Page
         */
        add_menu_page(
            __( 'Coastalynk', "castalynkmap" ),
            __( 'Coastalynk', "castalynkmap" ),
            'manage_options',
            'coastalynk',
            [ $this, 'dark_ships' ]
        );
        
    }

    
}

new Coastalynk_Admin();