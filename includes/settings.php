<?php
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Coastalynk_Maps_Settings
 */
class Coastalynk_Maps_Settings {

	private $page_tab;
    
    /**
     * Constructor function
     */
    public function __construct() {

        $this->page_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
        add_action( 'admin_menu', [ $this, 'setting_menu' ], 1001 );
        add_action( 'admin_post_save_coastalynkmap_settings', [ $this, 'save_settings' ] );
       // add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_scripts' ] );
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
            __( 'Coastalynk Settings', "castalynkmap" ),
            __( 'Coastalynk Settings', "castalynkmap" ),
            'manage_options',
            'coastalynk-map-settngs',
            [ $this, 'load_setting_menu' ]
        );
    }
	

	/**
     * Save custom settings
     */
    public function save_settings() {

        $url = admin_url('admin.php');
        $url = add_query_arg( 'page', 'coastalynk-map-settngs', $url );

        $current_tab = isset( $_POST['current_tab'] ) ? $_POST['current_tab'] : '';
        if( $current_tab === 'general' ) {

            $coatalynk_datalastic_apikey = isset( $_POST['coatalynk_datalastic_apikey'] ) ? sanitize_textarea_field( stripslashes_deep( $_POST['coatalynk_datalastic_apikey'] ) ) : '';
            
            update_option( 'coatalynk_datalastic_apikey', $coatalynk_datalastic_apikey );
            $url = add_query_arg( 'tab', 'general', $url );
        }

        $url = add_query_arg( 'updated', 1, $url );
        wp_redirect( $url );
        exit;
    }

    /**
     * Load settings page content
     */
    public function load_setting_menu() {
        
		$settings_sections = array(
            'general' => array(
                'title' => __( 'General Settings', "castalynkmap" ),
                'icon' => 'fa-cog',
            ),
        );
		$settings_sections = apply_filters( 'coastalynk_settings_sections', $settings_sections );
        ?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2><?php _e( 'Coastalynk Settings', "castalynkmap" ); ?></h2>
		
			<div class="nav-tab-wrapper">
				<?php
					foreach( $settings_sections as $key => $section ) {
				?>
						<a href="?page=coastalynk-map-settngs&tab=<?php echo $key; ?>"
							class="nav-tab <?php echo $this->page_tab == $key ? 'nav-tab-active' : ''; ?>">
							<i class="fa <?php echo $section['icon']; ?>" aria-hidden="true"></i>
							<?php _e( $section['title'], 'castalynkmap' ); ?>
						</a>
                <?php
                    }
                ?>
			</div>
		
			<?php
					foreach( $settings_sections as $key => $section ) {
						if( $this->page_tab == $key ) {
							$key = str_replace( '_', '-', $key );
							include( 'views/' . $key . '.php' );
						}
					}
					?>
		</div>
        <?php
    }
}

new Coastalynk_Maps_Settings();