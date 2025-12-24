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
            'lat',
            'lon',
            'speed',
            'ais_signal',
            'deadweight',
            'gross_tonnage',
            'port',
            'port_id',
            'distance',
            'event_ref_id',
            'zone_type',
            'zone_terminal_name',
            'status',
            'is_email_sent',
            'is_complete',
            'is_disappeared',
            'last_updated',
        ];

        add_action( 'admin_menu',                                   [ $this, 'admin_menu_page' ] );
		add_action( 'wp_ajax_csm_sts_display', 		                [ $this, 'csm_sts_display' ], 100 );
		add_action( 'wp_ajax_csm_sts_delete', 		                [ $this, 'csm_sts_delete_callback' ], 100 );
        add_action( 'wp_ajax_coastalynk_update_sts', 		        [ $this, 'coastalynk_update_sts' ], 100 );
        add_action( 'admin_enqueue_scripts',                        [ $this, 'admin_enqueue_scripts_callback' ] );
        add_action('admin_post_coastalynk_admin_export_csv',        [ $this, 'coastalynk_admin_export_csv_callback' ]);
        add_action('admin_post_nopriv_coastalynk_admin_export_csv', [ $this, 'coastalynk_admin_export_csv_callback' ]);

	}

    /**
     * Action wp_ajax for fetching the first time table structure
     */
    public function coastalynk_admin_export_csv_callback() {
        
        global $wpdb;
        
        if (!isset($_REQUEST['coastalynk_sts_history_load_nonce']) || ! wp_verify_nonce($_REQUEST['coastalynk_sts_history_load_nonce'], 'coastalynk_sts_history_load')) {
            $error_url = add_query_arg('form_error', 'Security verification failed', wp_get_referer());
            wp_redirect($error_url);
            exit;
        }

        $event_table_mother = $wpdb->prefix . 'coastalynk_sts_events';
        $event_table_daughter = $wpdb->prefix . 'coastalynk_sts_event_detail';
        $csm_ports_filter             = isset( $_REQUEST['csm_ports_filter'] ) ? sanitize_text_field( $_REQUEST['csm_ports_filter'] ) : '' ;
        $selected_search              = ( isset( $_REQUEST['search'] )  ) ? sanitize_text_field( $_REQUEST['search'] ) : ''; 
        $csm_vessel1_search1          = ( isset( $_REQUEST['csm_vessel1_search1'] )  ) ? sanitize_text_field( $_REQUEST['csm_vessel1_search1'] ) : ''; 
        $csm_vessel2_search2          = ( isset( $_REQUEST['csm_vessel2_search2'] )  ) ? sanitize_text_field( $_REQUEST['csm_vessel2_search2'] ) : ''; 
        $csm_history_range            = ( isset( $_REQUEST['csm_history_range'] )  ) ? sanitize_text_field( $_REQUEST['csm_history_range'] ) : ''; 
        $csm_risk_level               = ( isset( $_REQUEST['csm_risk_level'] )  ) ? sanitize_text_field( $_REQUEST['csm_risk_level'] ) : ''; 
        $csm_status                   = ( isset( $_REQUEST['csm_status'] )  ) ? sanitize_text_field( $_REQUEST['csm_status'] ) : ''; 
        
        $table_name = $wpdb->prefix.'coastalynk_sts_events'; 
        $where = " where 1 = 1";
       
        if( ! empty( $csm_ports_filter ) ) {
            $where .= " and e.port_id='".$csm_ports_filter."'";
        }
        
        if( ! empty( $csm_status ) ) {
            $where .= " and e.status='".$csm_status."'";
        }

        if( ! empty( $csm_vessel1_search1 ) ) {
            $where .= " and ( e.uuid like '%".$csm_vessel1_search1."%' or lower(e.name) like '%".strtolower($csm_vessel1_search1)."%' or lower(e.imo) like '%".strtolower($csm_vessel1_search1)."%' or lower(e.mmsi) like '%".strtolower($csm_vessel1_search1)."%' or lower(e.type_specific) like '%".strtolower($csm_vessel1_search1)."%' or lower(e.event_ref_id) like '%".strtolower($csm_vessel1_search1)."%' )";
        }

        if( !empty( $csm_history_range ) ) {
            $explode = explode( '-', $csm_history_range );
            $start_date = date( 'Y-m-d H:i:s', strtotime( trim( $explode[0] ) ) );
            $end_date = date( 'Y-m-d H:i:s', strtotime( trim( $explode[1] ) ) );
            
            $where .= " and e.start_date >= '".$start_date."' and ( e.end_date  <= '".$end_date."' or e.end_date is NULL ) ";
        }

        if( !empty( $csm_risk_level ) ) {

            switch( $csm_risk_level ) {
                case "0-30":
                    $where .= " and d.event_percentage >= '0' and d.event_percentage < '30' ";
                    break;
                case "30-70":
                    $where .= " and d.event_percentage >= '30' and d.event_percentage < '70' ";
                    break;
                case "70-100":
                    $where .= " and d.event_percentage >= '70'";
                    break;
            }
        }

        $vessle_recs     = $wpdb->get_results( "SELECT e.`id`,e.`uuid` as vessel1_uuid, e.`name` as vessel1_name, e.`mmsi` as vessel1_mmsi, e.`imo` as vessel1_imo, e.`country_iso` as vessel1_country_iso, e.`type` as vessel1_type, e.`type_specific` as vessel1_type_specific, e.`lat` as vessel1_lat, e.`lon` as vessel1_lon, e.`speed` as vessel1_speed, e.`navigation_status` as vessel1_navigation_status, e.`draught` as vessel1_draught, e.`completed_draught` as vessel1_completed_draught, e.`last_position_UTC` as vessel1_last_position_UTC, e.`ais_signal` as vessel1_signal,e.`deadweight` as vessel1_deadweight,e.`gross_tonnage` as vessel1_gross_tonnage,e.`port`,e.`port_id`, e.`distance`,e.`event_ref_id`, e.`zone_type`,e.`zone_ship`, e.`zone_terminal_name`,e.`start_date`,e.`end_date`,e.`status`,e.`is_email_sent`,e.`is_complete`,e.`is_disappeared`, e.`last_updated`, d.`event_id`,d.`uuid` as vessel2_uuid,d.`name` as vessel2_name,d.`mmsi` as vessel2_mmsi,d.`imo` as vessel2_imo,d.`country_iso` as vessel2_country_iso,d.`type` as vessel2_type,d.`type_specific` as vessel2_type_specific,d.`lat` as vessel2_lat,d.`lon` as vessel2_lon,d.`speed` as vessel2_speed,d.`navigation_status` as vessel2_navigation_status,d.`draught` as vessel2_draught,d.`completed_draught` as vessel2_completed_draught,d.`last_position_UTC` as vessel2_last_position_UTC,d.`deadweight` as vessel2_deadweight,d.`gross_tonnage` as vessel2_gross_tonnage,d.`draught_change`,d.`ais_signal` as vessel2_signal,d.`end_date` as vessel2_end_date, d.`distance`,d.`event_percentage`,d.`cargo_category_type`,d.`risk_level`,d.`stationary_duration_hours`,d.`proximity_consistency`,d.`data_points_analyzed`,d.`is_disappeared`,d.`operationmode`,d.`is_complete` as vessel2_is_complete,d.`last_updated` as vessel2_last_updated,d.`status` as vessel2_status from ".$event_table_mother." as e inner join ".$event_table_daughter." as d on(e.id=d.event_id) $where ORDER BY e.last_updated desc", ARRAY_A );
        $fp = fopen('php://output', 'w'); 
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sts.csv"');
        header('Pragma: no-cache');    
        header('Expires: 0');
        
        $headers = ['id','vessel1_uuid', 'vessel1_name', 'vessel1_mmsi', 'vessel1_imo', 'vessel1_country_iso', 'vessel1_type', 'vessel1_type_specific', 'vessel1_lat', 'vessel1_lon', 'vessel1_speed', 'vessel1_navigation_status', 'vessel1_draught', 'vessel1_completed_draught', 'vessel1_last_position_UTC', 'vessel1_signal','vessel1_deadweight','vessel1_gross_tonnage','port','port_id', 'distance','event_ref_id', 'zone_type','zone_ship', 'zone_terminal_name','start_date','end_date','status','is_email_sent','is_complete','is_disappeared', 'last_updated', 'event_id','vessel2_uuid','vessel2_name','vessel2_mmsi','vessel2_imo','vessel2_country_iso','vessel2_type','vessel2_type_specific','vessel2_lat','vessel2_lon','vessel2_speed','vessel2_navigation_status','vessel2_draught','vessel2_completed_draught','vessel2_last_position_UTC','vessel2_deadweight','vessel2_gross_tonnage','draught_change','vessel2_signal','vessel2_end_date', 'distance','event_percentage','cargo_category_type','risk_level','stationary_duration_hours', 'proximity_consistency','data_points_analyzed','is_disappeared','operationmode','vessel2_is_complete','vessel2_last_updated','vessel2_status'];
        if( ! empty( $vessle_recs ) && is_array( $vessle_recs ) ) {
            $headers = array_keys( $vessle_recs[0] );
        }
        fputcsv($fp, $headers); 
        
        if ($fp && $vessle_recs){     
            foreach( $vessle_recs as $vessle_row ) {
                fputcsv($fp, array_values($vessle_row)); 
            }
        }
        exit;
    }
    /**
     * Action wp_ajax for fetching the first time table structure
     */
    public function coastalynk_update_sts() {
        global $wpdb;

        if( ! wp_verify_nonce( $_REQUEST['security'], 'csm_sts_load' ) ) {
            exit;
        }

        $id     = sanitize_text_field( $_REQUEST[ 'id' ] );
        $start_date    = sanitize_text_field( $_REQUEST[ 'start_date' ] );
        $end_date    = sanitize_text_field( $_REQUEST[ 'end_date' ] );
        $port_zone = sanitize_text_field( $_REQUEST[ 'port_zone' ] );
        $vessel1_before_draught    = sanitize_text_field( $_REQUEST[ 'vessel1_before_draught' ] );
        $vessel1_after_draught    = sanitize_text_field( $_REQUEST[ 'vessel1_after_draught' ] );
        $vessel2_before_draught    = sanitize_text_field( $_REQUEST[ 'vessel2_before_draught' ] );
        $vessel2_after_draught    = sanitize_text_field( $_REQUEST[ 'vessel2_after_draught' ] );
        $status    = sanitize_text_field( $_REQUEST[ 'status' ] );
        $comments    = sanitize_text_field( $_REQUEST[ 'comments' ] );
        
        $table_name = $wpdb->prefix . 'coastalynk_sts';
        
        $data_to_update = array(
            'start_date' => date('Y-m-d H:i:s', strtotime($start_date)),
            'end_date' => date('Y-m-d H:i:s', strtotime($end_date)),
            'zone_terminal_name' => $port_zone,
            'vessel1_draught' => $vessel1_before_draught,
            'vessel1_completed_draught' => $vessel1_after_draught,
            'vessel2_draught' => $vessel2_before_draught,
            'vessel2_completed_draught' => $vessel2_after_draught,
            'status' => $status
        );
        
        $where_condition = array(
            'id' => $id,
        );

        $result = $wpdb->update( $table_name, $data_to_update, $where_condition );
        

        if ( false === $result ) {
            echo json_encode( [ 'type' => 'failed', 'message' => sprintf( __( 'Error updating row with ID: %s', 'castalynkmap' ), $wpdb->last_error ) ] );
        } else if ( 0 === $result ) {
            echo json_encode( [ 'type' => 'failed', 'message' => sprintf( __( 'No row found with ID: %s', 'castalynkmap' ), $id ) ] );
        } else {
            echo json_encode( [ 'type' => 'success', 'message' => sprintf( __( 'ID#%s is updated successfully.', 'castalynkmap' ), $id ) ] );
        }

        exit;
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

        $event_table_mother = $wpdb->prefix . 'coastalynk_sts_events';
        $event_table_daughter = $wpdb->prefix . 'coastalynk_sts_event_detail';
        
        $result = $wpdb->delete(
            $event_table_daughter,
            array( 'event_id' => $id ),
            array( '%s' )
        );

        $result = $wpdb->delete(
            $event_table_mother,
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
            wp_enqueue_style( 'coastlynk-daterangepicker-css', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css' );
            wp_enqueue_style( 'jquery-Intimidatetime-style', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css' );
            wp_enqueue_style( 'csm-backend-css', CSM_CSS_URL . 'backend.css', [], time(), null );
            wp_enqueue_style( 'font-awesome-4.7', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), time() );            
            
            /**
             * enqueue admin js
             */
            wp_enqueue_script( 'coastlynk-moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array( 'jquery' ) );
            wp_enqueue_script( 'csm-backendcookie-js', CSM_JS_URL . 'backend/jquery.cookie.js', [ 'jquery' ], time(), true );
            wp_enqueue_script( 'coastlynk-daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array( 'jquery' ) );
            wp_enqueue_script( 'csm-intimidatetime-backend-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js', [ 'jquery' ], time(), true );
            wp_enqueue_script( 'csm-sts-backend-js', CSM_JS_URL . 'backend/sts.js', [ 'jquery' ], time(), true ); 
            wp_localize_script( 'csm-sts-backend-js', 'CSM_ADMIN', [  
                'ajaxURL'                       => admin_url( 'admin-ajax.php' ),
                'loader'                        => CSM_IMAGES_URL .'spinner-2x.gif',
                'preloader_gif_img'             => Coastalynk_Admin::get_bar_preloader(),
                'security'                      => wp_create_nonce( 'csm_sts_load' ),
                'nonce'                         => wp_create_nonce('coastalynk_secure_ajax_nonce')
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
        
        $table_name = $wpdb->prefix.'coastalynk_sts_events';
        if( is_null( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) ) ) {
            ?> 
                <div class="wrap">
                    <h2><?php _e( 'STS', 'castalynkmap' ); ?></h2>
                    <p id="csm-dat-not-imported-message"><?php _e( 'STS data is not imported yet. Please, run the cron for the first time.', 'castalynkmap' ); ?></p>
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
		
        $table_name = $wpdb->prefix.'coastalynk_ports';
        $sql = "select title from ".$table_name." where country_iso='NG' and port_type in( 'Port', 'Coastal Zone', 'Territorial Zone', 'EEZ' ) order by title";
        $results = $wpdb->get_results( $sql );
        ?>
            <div class="wrap">
                <h2><?php _e( 'STS', 'castalynkmap' ); ?></h2>
                <div class="csm_filters_top">
                    <form id="csm-sts-filter" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                        <div class="csm-filter-handler alignleft actions bulkactions">
                            <span class="csm_filter_labels"><?php _e( 'Filters:', 'castalynkmap' ); ?></span>
                            <input type="text" value="" name="coastalynk-general-vessel1-search" class="form-control coastalynk-general-vessel1-search" placeholder="<?php _e( 'Search Vessel', 'castalynkmap' );?>">
                            <select name="csm_ports_filter" class="csm-ports-filter csm_ports_filter" >
                                <option value=""><?php echo __( 'All Ports', 'castalynkmap' );?></option>
                                <?php
                                    $sql = "select port_id, title from ".$table_name." where country_iso='NG'";
                                    $ports = $wpdb->get_results($sql);
                                    foreach ( $ports as $port ) {
                                        ?>
                                            <option value="<?php echo $port->port_id; ?>"><?php echo $port->title; ?></option>
                                        <?php 
                                    }
                                ?>
                            </select>
                            <input id="caostalynk_sts_history_range" type="text" class="coastalynk-date-range-js caostalynk_sts_history_range" name="caostalynk_sts_history_range">
                            <select name="coastalynk-field-status" id="coastalynk-field-status">
                                <option value=""><?php _e( "Status", "castalynkmap" );?></option>
                                <option value="Detected"><?php _e( "Detected", "castalynkmap" );?></option>
                                <option value="Ongoing"><?php _e( "Ongoing", "castalynkmap" );?></option>
                                <option value="Increase"><?php _e( "Increase", "castalynkmap" );?></option>
                                <option value="Decrease"><?php _e( "Decrease", "castalynkmap" );?></option>
                                <option value="No Change"><?php _e( "No Change", "castalynkmap" );?></option>
                                <option value="Awaiting Draught Update"><?php _e( "Awaiting Draught Update", "castalynkmap" );?></option>
                                <option value="Inconclusive"><?php _e( "Inconclusive", "castalynkmap" );?></option>
                                <option value="Error"><?php _e( "Error", "castalynkmap" );?></option>
                                <option value="Completed"><?php _e( "Completed", "castalynkmap" );?></option>
                                <option value="Pending Manual Review"><?php _e( "Pending Review", "castalynkmap" );?></option>
                                <option value="Verified"><?php _e( "Verified", "castalynkmap" );?></option>
                            </select>
                            <select name="coastalynk-field-risk-level" id="coastalynk-field-risk-level">
                                <option value=""><?php _e( "Risk Level", "castalynkmap" );?></option>
                                <option value="0-30"><?php _e( "0%-30%", "castalynkmap" );?></option>
                                <option value="30-70"><?php _e( "30%-70%", "castalynkmap" );?></option>
                                <option value="70-100"><?php _e( "70%-100%", "castalynkmap" );?></option>
                            </select>
                            <input type="button" name="coastalynk-search-search-button" value="<?php _e( 'Filter', 'castalynkmap' );?>" class="btn button coastalynk-search-search-button" />
                            <input type="submit" name="coastalynk-export-csv-button" value="<?php _e( 'Export', 'castalynkmap' );?>" class="btn button coastalynk-export-csv-button" />
                            <input type="hidden" name="action" value="coastalynk_admin_export_csv" />
                            <?php wp_nonce_field( 'coastalynk_sts_history_load', 'coastalynk_sts_history_load_nonce' ); ?>
                            <input type="hidden" class="csm-coastlynk-page" name="page" value="1" />
                            <input type="hidden" class="csm-coastlynk-order" name="order" value="id" />
                            <input type="hidden" class="csm-coastlynk-orderby" name="orderby" value="asc" />
                        </div>
                    </form>
                    <form id="coastalynk-sts-filter-text" method="post" style="display: none;">
                        <input type="text" value="" name="coastalynk-general-search" class="form-control coastalynk-general-search" placeholder="<?php _e( 'Search', 'castalynkmap' );?>">
                        <input type="submit" name="coastalynk-search-button-txt" value="<?php _e( 'Search', 'castalynkmap' );?>" class="btn button coastalynk-search-button-txt" />
                    </form>
                    <div id="csm_sts_data">
                        <!-- Now we can render the completed list table -->
                        <?php $testListTable->display() ?>
                    </div>
                </div>
            </div>
            <div class="coastalynk-popup-overlay"></div>
            <div class="coastalynk-popup-content">
                <h2>
                    <div>
                        <?php _e( "STS Activity", "castalynkmap" );?>(<span class="coastalynk-sts-popup-content-operationmode"></span>)
                        <span class="coastalynk-popup-approved-zone"><i class="fa fa-check-square" aria-hidden="true"></i></span>
                        <span class="coastalynk-popup-unapproved-zone"><i class="fa fa-exclamation" aria-hidden="true"></i></span>
                        <span class="coastalynk-popup-risk-level-top"></span>
                    </div>
                    <div id="coastalynk-popup-close"><span class="dashicons dashicons-no"></span></div>
                </h2>
                <div class="coastalynk-popup-top-message" style="display:none;"></div>
                <div class="coastalynk-popup-top-progress-bar" data-percentage="10">
                    <div class="coastalynk-progress-container-wrapper">
                        <label class="coastalynk-percentage-label"><?php _e( "Start", "castalynkmap" );?></label>
                        <div class="coastalynk-progress-container">
                            <div class="coastalynk-progress-bar">
                                <div class="coastalynk-progress-fill">
                                    <span class="coastalynk-progress-percentage">0%</span>
                                </div>
                            </div>
                        </div>
                        <label class="coastalynk-percentage-label"><?php _e( "End", "castalynkmap" );?></label>
                    </div>
                </div>
                
                <div class="coastalynk-popup-content-boxes coastalynk-popup-sts-content-boxes">
                    <div class="coastalynk-popup-content-box">
                        <h3><?php _e( "Vessel 1", "castalynkmap" );?><span title = "<?php _e( "Mother", "castalynkmap" );?>" class="coastalynk-popup-vessel1-parent" style="display:none;"><?php _e( "(Mother)", "castalynkmap" );?></span><span title = "<?php _e( "Daughter", "castalynkmap" );?>" class="coastalynk-popup-vessel1-daughter" style="display:none;"><?php _e( "(Daughter)", "castalynkmap" );?></span></h3>
                        <ul class="coastalynk-popup-content-box-list">
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Name:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_name"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "MMSI:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_mmsi"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "IMO:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_imo"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Tonnage:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_tonnage"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Type:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_type"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Sub Type:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_type_specific"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Before Draught:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_draught"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "After Draught:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_completed_draught"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Country:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_country_iso"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Speed:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_speed"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Nav. Status:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_navigation_status"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Signal:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1-ais-signal"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Last Position:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_last_position_UTC"></span></li>
                        </ul>
                    </div>
                    <div class="coastalynk-popup-content-box">
                        <h3><?php _e( "Vessel 2", "castalynkmap" );?><span title = "<?php _e( "Mother", "castalynkmap" );?>" class="coastalynk-popup-vessel2-parent" style="display:none;"><?php _e( "(Mother)", "castalynkmap" );?></span><span title = "<?php _e( "Daughter", "castalynkmap" );?>" class="coastalynk-popup-vessel2-daughter" style="display:none;"><?php _e( "(Daughter)", "castalynkmap" );?></span></h3>
                        <ul class="coastalynk-popup-content-box-list">
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Name:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_name"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "MMSI:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_mmsi"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "IMO:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_imo"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Tonnage:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_tonnage"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Type:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_type"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Sub Type:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_type_specific"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Before Draught:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_draught"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "After Draught:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_completed_draught"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Country:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_country_iso"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Speed:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_speed"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Nav. Status:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_navigation_status"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Signal:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2-ais-signal"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Last Position:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_last_position_UTC"></span></li>
                        </ul>
                    </div>
                    <div class="coastalynk-popup-content-box">
                        <h3><?php _e( "STS Event Details", "castalynkmap" );?></h3>
                        <ul class="coastalynk-popup-content-box-list">
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Port:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-port"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Ref#:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-event_ref_id"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Zone Type:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-zone_type"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Mother Ship:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-zone_ship"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Zone:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-zone_terminal_name"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Start Date:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-start_date"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "End Date:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-end_date"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Cargo Type:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-cargo_category_type"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Draught Change:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-draught_change"></span></li>
                        </ul>
                    </div>
                    <div class="coastalynk-popup-content-box">
                        <h3><?php _e( "STS Risk & Activity Status", "castalynkmap" );?></h3>
                        <ul class="coastalynk-popup-content-box-list">
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Risk Status:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-risk_level"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Distance(NM):", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-current_distance_nm"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Stationary(hours):", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-stationary_duration_hours"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Proximity Consistency:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-proximity_consistency"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Data Points Analyzed:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-data_points_analyzed"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Status:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-status"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Percentage:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-event_percentage"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Last Updated:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-last_updated"></span></li>
                        </ul>
                    </div>
                </div>
                <div class="coastalynk-popup-sts-content-form" style="display:none;">
                    <div class="coastalynk-sts-popup-field">
                        <label><?php _e( "Start Time", "castalynkmap" );?></label>
                        <input type="text" name="coastalynk-field-start-date" class="coastalynk-field-sts-date" value="" id="coastalynk-field-start-date" />
                    </div>
                    <div class="coastalynk-sts-popup-field">
                        <label><?php _e( "End Time", "castalynkmap" );?></label>
                        <input type="text" name="coastalynk-field-end-date" class="coastalynk-field-sts-date" value="" id="coastalynk-field-end-date" />
                    </div> 
                    <div class="coastalynk-sts-popup-field">
                        <label><?php _e( "Port / STS Zone", "castalynkmap" );?></label>
                        <select name="coastalynk-field-end-date" id="coastalynk-field-port-zone">
                            <?php
                                if ( count( $results ) > 0 ) {
                                    foreach ( $results as $res ) { 
                                        echo '<option value="'.$res->title.'">'.$res->title.'</option>';
                                    }
                                } 
                            ?>
                        </select>
                    </div>
                    <div class="coastalynk-sts-popup-field">
                        <label><?php _e( "Vessel 1 Before Draught", "castalynkmap" );?></label>
                        <input type="text" name="coastalynk-field-vessel1-before-draught" value="0" id="coastalynk-field-vessel1-before-draught" />
                    </div>
                    <div class="coastalynk-sts-popup-field">
                        <label><?php _e( "Vessel 1 After Draught", "castalynkmap" );?></label>
                        <input type="text" name="coastalynk-field-vessel1-after-draught" value="0" id="coastalynk-field-vessel1-after-draught" />
                    </div>
                    <div class="coastalynk-sts-popup-field">
                        <label><?php _e( "Vessel 2 Before Draught", "castalynkmap" );?></label>
                        <input type="text" name="coastalynk-field-vessel2-before-draught" value="0" id="coastalynk-field-vessel2-before-draught" />
                    </div>
                    <div class="coastalynk-sts-popup-field">
                        <label><?php _e( "Vessel 2 After Draught", "castalynkmap" );?></label>
                        <input type="text" name="coastalynk-field-vessel2-after-draught" value="0" id="coastalynk-field-vessel2-after-draught" />
                    </div>
                    <div class="coastalynk-sts-popup-field">
                        <label><?php _e( "Event Status", "castalynkmap" );?></label>
                        <select name="coastalynk-field-status" id="coastalynk-field-status">
                            <option value="Detected"><?php _e( "Detected", "castalynkmap" );?></option>
                            <option value="Ongoing"><?php _e( "Ongoing", "castalynkmap" );?></option>
                            <option value="Increase"><?php _e( "Increase", "castalynkmap" );?></option>
                            <option value="Decrease"><?php _e( "Decrease", "castalynkmap" );?></option>
                            <option value="No Change"><?php _e( "No Change", "castalynkmap" );?></option>
                            <option value="Awaiting Draught Update"><?php _e( "Awaiting Draught Update", "castalynkmap" );?></option>
                            <option value="Inconclusive"><?php _e( "Inconclusive", "castalynkmap" );?></option>
                            <option value="Error"><?php _e( "Error", "castalynkmap" );?></option>
                            <option value="Completed"><?php _e( "Completed", "castalynkmap" );?></option>
                            <option value="Pending Manual Review"><?php _e( "Pending Review", "castalynkmap" );?></option>
                            <option value="Verified"><?php _e( "Verified", "castalynkmap" );?></option>
                        </select>
                    </div>
                    <div class="coastalynk-sts-popup-field">
                        <label><?php _e( "Admin Notes / Comments", "castalynkmap" );?></label>
                        <textarea name="coastalynk-field-comments" id="coastalynk-field-comments" rows="4" cols="50"></textarea>
                    </div>
                </div>
                <div class="coastalynk-popup-update-button">
                    <input type="hidden" id="coastalynk-field-id" name="coastalynk-field-id" value="0" />
                    <input type="submit" class="btn button coastalynk-edit-sts-button" value="<?php _e( "Edit Information", "castalynkmap" );?>" />
                    <input type="submit" style="display:none;" class="btn button coastalynk-cancel-sts-button" value="<?php _e( "Back", "castalynkmap" );?>" />
                    <input type="submit" style="display:none;" class="btn button coastalynk-update-sts-button" onlick="return confirm('<?php _e( "Are you sure?", "castalynkmap" );?>')" value="<?php _e( "Update Information", "castalynkmap" );?>"/>
                    <div id="coastalynk-column-loader coastalynk-column-sts-popup-loader" style="display:none;">
                        Updating....
                    </div>
                </div>
            </div>
        <?php
    }
}

new CSM_STS_Admin_Menu();