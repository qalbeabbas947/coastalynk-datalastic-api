<?php
/**
 * CSM_Dark_Ships_Menu class manages the admin side darkships.
 */

 if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * CSM_Dark_Ships_Menu class
 */
class CSM_Dark_Ships_Menu {

    /**
     * Default hidden columns
     */
    private $default_hidden_columns;

    /** ************************************************************************
     * REQUIRED. Set up a constructor.
     ***************************************************************************/
	function __construct() {

        $this->default_hidden_columns = [
            'uuid',
            'reason_type',
            'port_id',
            'last_updated'
        ];

        add_action( 'admin_menu',                           [ $this, 'admin_menu_page' ] );
		add_action( 'wp_ajax_csm_dark_ships_display', 		[ $this, 'csm_dark_ships_display' ], 100 );
		add_action( 'wp_ajax_csm_dark_ships_delete', 		[ $this, 'csm_dark_ships_delete_callback' ], 100 );
        add_action( 'admin_enqueue_scripts',                [ $this, 'admin_enqueue_scripts_callback' ] );
	}
	
    /**
     * Action wp_ajax for fetching the first time table structure
     */
    public function csm_dark_ships_delete_callback() {
        
        global $wpdb;

        if( ! wp_verify_nonce( $_GET['security'], 'csm_dark_ships_load' ) ) {
            exit;
        }

        $id     = sanitize_text_field( $_REQUEST[ 'id' ] );
        $imo    = sanitize_text_field( $_REQUEST[ 'imo' ] );

        $table_name = $wpdb->prefix . 'coastalynk_dark_ships';
        
        $result = $wpdb->delete(
            $table_name,
            array( 'uuid' => $id ),
            array( '%s' )
        );

        if ( false === $result ) {
            echo json_encode( [ 'type' => 'failed', 'message' => sprintf( __( 'Error deleting row: %s', 'castalynkmap' ), $wpdb->last_error ) ] );
        } else if ( 0 === $result ) {
            echo json_encode( [ 'type' => 'failed', 'message' => sprintf( __( 'No row found with ID: %s', 'castalynkmap' ), $id ) ] );
        } else {
            echo json_encode( [ 'type' => 'success', 'message' => sprintf( __( '%s deleted successfully.', 'castalynkmap' ), $id ) ] );
        }

        exit;
    }

    /**
     * Action wp_ajax for fetching the first time table structure
     */
    public function admin_enqueue_scripts_callback() {
        $screen = get_current_screen();
        
        if( $screen->id == 'coastalynk_page_coastalynk-dark-ships' ) {

            /**
             * enqueue admin css
             */
            wp_enqueue_style( 'csm-backend-css', CSM_CSS_URL . 'backend.css', [], time(), null );
            
            /**
             * enqueue admin js
             */
            wp_enqueue_script( 'csm-backendcookie-js', CSM_JS_URL . 'backend/jquery.cookie.js', [ 'jquery' ], time(), true ); 
            wp_enqueue_script( 'csm-dark-ships-backend-js', CSM_JS_URL . 'backend/dark-ships.js', [ 'jquery' ], time(), true ); 
            wp_localize_script( 'csm-dark-ships-backend-js', 'CSM_ADMIN', [  
                'ajaxURL'                       => admin_url( 'admin-ajax.php' ),
                'loader'                        => CSM_IMAGES_URL .'spinner-2x.gif',
                'preloader_gif_img'             => Coastalynk_Admin::get_bar_preloader(),
                'security'                      => wp_create_nonce( 'csm_dark_ships_load' )
            ] );
        }
    }  
    

	/**
     * Action wp_ajax for fetching the first time table structure
     */
    public function csm_dark_ships_display() {

        if( ! wp_verify_nonce( $_GET['security'], 'csm_dark_ships_load' ) ) {
            exit;
        }
        
        $wp_list_table = new CSM_Dark_Ships();
        $wp_list_table->prepare_items();

        ob_start();
        $wp_list_table->display();
        $display = ob_get_clean();

        die(
            json_encode([
                "display" => $display
            ])
        );
    }

    /**
     * Add Reset Course Progress submenu page under learndash menus
     */
    public function admin_menu_page() { 
        
        $user_id = get_current_user_id();
        
        $hook = add_submenu_page( 
            'coastalynk',
            __( 'Dark Ships', 'castalynkmap' ),
            __( 'Dark Ships', 'castalynkmap' ),
            'manage_options',
            'coastalynk-dark-ships',
            [ $this,'listing_page'],
            2 
        );

        if( get_user_option( 'darkship_hidden_columns_set', $user_id ) != 'Yes' ) {
            update_user_option( $user_id, 'managecoastalynk_page_coastalynk-dark-shipscolumnshidden', $this->default_hidden_columns );
            update_user_option( $user_id, 'darkship_hidden_columns_set', 'Yes' );
        }

        add_action( "load-$hook", function () {
            
            global $csmDarkshipListTable;
            
            $option = 'per_page';
            $args = [
                    'label' => 'Dark Ships Per Page',
                    'default' => 20,
                    'option' => 'dark_ships_per_page'
                ];
            add_screen_option( $option, $args );
            $csmDarkshipListTable = new CSM_Dark_Ships();
        } );
    }

    /**
     * Add setting page Tabs
     *
     * @param $current
     */
    public static function listing_page( ) {

        global $wpdb;
        
        $table_name = $wpdb->prefix.'coastalynk_dark_ships';
        if( is_null( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) ) ) {
            ?> 
                <div class="wrap">
                    <h2><?php _e( 'Dark Ships', 'castalynkmap' ); ?></h2>
                    <p id="csm-dat-not-imported-message"><?php _e( 'Darkships data is not imported yet. Please, run the cron for the first time.', 'castalynkmap' ); ?></p>
                </div>
            <?php

            return;
        }
		
		

        /**
         * Create an instance of our package class... 
         */
        $testListTable = new CSM_Dark_Ships();

        /**
         * Fetch, prepare, sort, and filter our data... 
         */
        $testListTable->prepare_items();
		
        ?>
            <div class="wrap">
                <h2><?php _e( 'Dark Ships', 'castalynkmap' ); ?></h2>
                
                <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                
                    <div class="csm_filters_top">
                        <form id="csm-dark-ships-filter" method="post">
                            <div class="csm-filter-handler alignleft actions bulkactions">
                                <span class="csm_filter_labels"><?php _e( 'Filters:', 'castalynkmap' ); ?></span>
                                <select name="csm_ports_filter" class="csm-ports-filter csm_ports_filter" >
                                    <option value=""><?php echo __( 'All Ports', 'castalynkmap' );?></option>
                                    <?php
                                        $table_name = $wpdb->prefix. 'coastalynk_ports';
                                        $sql = "select port_id, title from ".$table_name." where country_iso='NG'";
                                        $ports = $wpdb->get_results($sql);
                                        foreach ( $ports as $port ) {
                                            ?>
                                                <option value="<?php echo $port->port_id; ?>"><?php echo $port->title; ?></option>
                                            <?php 
                                        }
                                    ?>
                                </select>
                                
                                <input type="button" name="coastalynk-search-search-button" value="<?php _e( 'Filter', 'castalynkmap' );?>" class="btn button coastalynk-search-search-button" />
                            </div>
                        </form>
                        <form id="coastalynk-dark-ships-filter-text" method="post">
                            <input type="text" value="" name="coastalynk-general-search" class="form-control coastalynk-general-search" placeholder="<?php _e( 'Search', 'castalynkmap' );?>">
                            <input type="submit" name="coastalynk-search-button-txt" value="<?php _e( 'Search', 'castalynkmap' );?>" class="btn button coastalynk-search-button-txt" />
                        </form>
                		<div id="csm_darkships_data">
							<!-- Now we can render the completed list table -->
							<?php $testListTable->display() ?>
						</div>
					</div>
					<input type="hidden" class="csm-coastlynk-page" name="page" value="1" />
                    <input type="hidden" class="csm-coastlynk-order" name="order" value="id" />
                    <input type="hidden" class="csm-coastlynk-orderby" name="orderby" value="asc" />
					<input type="hidden" class="csm-script-coastlynk-type" name="csm-script-coastlynk-type" value="dark_ships" />
                    <input type="hidden" class="csm-display-coastlynk-type" name="coastlynk-type" value="filter" />
                </form>
            </div>
        <?php
    }
}

new CSM_Dark_Ships_Menu();