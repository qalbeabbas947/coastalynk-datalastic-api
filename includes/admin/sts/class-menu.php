<?php
/**
 * CSM_STS_Admin_Menu class manages the admin side darkships.
 */

 if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
ini_set('display_errors', 'On');
error_reporting(E_ALL);
/**
 * CSM_STS_Admin_Menu class
 */
class CSM_STS_Admin_Menu {

    /**
     * Default hidden columns
     */
    private $default_hidden_columns;

    /** ************************************************************************
     * REQUIRED. Set up a constructor.
     ***************************************************************************/
	function __construct() {

        $this->default_hidden_columns = [
            'id',
            'vessel1_uuid',
            'vessel1_lat',
            'vessel1_lon',
            'vessel1_speed',
            'vessel1_navigation_status',
            'vessel1_draught',
            'vessel1_completed_draught',
            'vessel1_last_position_UTC',
            'vessel1_signal',
            'vessel2_uuid',
            'vessel2_lat',
            'vessel2_lon',
            'vessel2_speed',
            'vessel2_navigation_status',
            'vessel2_draught',
            'vessel2_completed_draught',
            'vessel2_last_position_UTC',
            'vessel2_signal',
            'port_id',
            'port',
            'distance',
            'is_sts_zone',
            'remarks',
            'event_percentage',
            'vessel_condition1',
            'vessel_condition2',
            'cargo_category_type',
            'risk_level',
            'current_distance_nm',
            'stationary_duration_hours',
            'proximity_consistency',
            'data_points_analyzed',
            'estimated_cargo',
            'operationmode',
            'is_email_sent',
            'is_complete',
            'is_disappeared'
        ];

        add_action( 'admin_menu',                           [ $this, 'admin_menu_page' ] );
		add_action( 'wp_ajax_csm_sts_display', 		        [ $this, 'csm_sts_display' ], 100 );
		add_action( 'wp_ajax_csm_sts_delete', 		        [ $this, 'csm_sts_delete_callback' ], 100 );
        add_action( 'admin_enqueue_scripts',                [ $this, 'admin_enqueue_scripts_callback' ] );
	}
	
    /**
     * Action wp_ajax for fetching the first time table structure
     */
    public function csm_sts_delete_callback() {
        
        global $wpdb;

        if( ! wp_verify_nonce( $_GET['security'], 'csm_sts_load' ) ) {
            exit;
        }

        $id     = sanitize_text_field( $_REQUEST[ 'id' ] );
        $event_ref_id    = sanitize_text_field( $_REQUEST[ 'event_ref_id' ] );

        $table_name = $wpdb->prefix . 'coastalynk_sts';
        
        $result = $wpdb->delete(
            $table_name,
            array( 'id' => $id ),
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
        if( $screen->id == 'coastalynk_page_coastalynk-sts' ) {

            /**
             * enqueue admin css
             */
            wp_enqueue_style( 'csm-backend-css', CSM_CSS_URL . 'backend.css', [], time(), null );
            
            /**
             * enqueue admin js
             */
            wp_enqueue_script( 'csm-backendcookie-js', CSM_JS_URL . 'backend/jquery.cookie.js', [ 'jquery' ], time(), true ); 
            wp_enqueue_script( 'csm-sts-backend-js', CSM_JS_URL . 'backend/sts.js', [ 'jquery' ], time(), true ); 
            wp_localize_script( 'csm-sts-backend-js', 'CSM_ADMIN', [  
                'ajaxURL'                       => admin_url( 'admin-ajax.php' ),
                'loader'                        => CSM_IMAGES_URL .'spinner-2x.gif',
                'preloader_gif_img'             => Coastalynk_Admin::get_bar_preloader(),
                'security'                      => wp_create_nonce( 'csm_sts_load' )
            ] );
        }
    }  
    

	/**
     * Action wp_ajax for fetching the first time table structure
     */
    public function csm_sts_display() {

        if( ! wp_verify_nonce( $_GET['security'], 'csm_sts_load' ) ) {
            exit;
        }
        
        $wp_list_table = new CSM_STS_Admin_listing();
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
            __( 'STS', 'castalynkmap' ),
            __( 'STS', 'castalynkmap' ),
            'manage_options',
            'coastalynk-sts',
            [ $this,'listing_page'],
            2 
        );

        if( get_user_option( 'sts_hidden_columns_set', $user_id ) != 'Yes' ) {
            update_user_option( $user_id, 'managecoastalynk_page_coastalynk-stscolumnshidden', $this->default_hidden_columns );
            update_user_option( $user_id, 'sts_hidden_columns_set', 'Yes' );
        }

        add_action( "load-$hook", function () {
            
            global $csmSTSListTable;
            
            $option = 'per_page';
            $args = [
                    'label' => 'STS Per Page',
                    'default' => 20,
                    'option' => 'sts_per_page'
                ];
            add_screen_option( $option, $args );
            $csmSTSListTable = new CSM_STS_Admin_listing();
        } );
    }

    /**
     * Add setting page Tabs
     *
     * @param $current
     */
    public static function listing_page( ) {

        global $wpdb;
        
        $table_name = $wpdb->prefix.'coastalynk_sts';
        if( is_null( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) ) ) {
            ?> 
                <div class="wrap">
                    <h2><?php _e( 'STS', 'castalynkmap' ); ?></h2>
                    <p id="csm-dat-not-imported-message"><?php _e( 'Darkships data is not imported yet. Please, run the cron for the first time.', 'castalynkmap' ); ?></p>
                </div>
            <?php

            return;
        }
		
		

        /**
         * Create an instance of our package class... 
         */
        $testListTable = new CSM_STS_Admin_listing();

        /**
         * Fetch, prepare, sort, and filter our data... 
         */
        $testListTable->prepare_items();
		
        ?>
            <div class="wrap">
                <h2><?php _e( 'STS', 'castalynkmap' ); ?></h2>
                
                <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                
                    <div class="csm_filters_top">
                        <form id="csm-sts-filter" method="post">
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
                        <form id="coastalynk-sts-filter-text" method="post">
                            <input type="text" value="" name="coastalynk-general-search" class="form-control coastalynk-general-search" placeholder="<?php _e( 'Search', 'castalynkmap' );?>">
                            <input type="submit" name="coastalynk-search-button-txt" value="<?php _e( 'Search', 'castalynkmap' );?>" class="btn button coastalynk-search-button-txt" />
                        </form>
                		<div id="csm_sts_data">
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

new CSM_STS_Admin_Menu();