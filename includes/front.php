<?php

/**
 * Class Coastalynk_Sea_Vessel_Map_Front
 */
class Coastalynk_Sea_Vessel_Map_Front {

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

        if ( is_null( self::$instance ) && ! ( self::$instance instanceof Coastalynk_Sea_Vessel_Map_Front ) ) {
            self::$instance = new self;

            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Plugin requiered files
     */
    public function hooks() {

        add_action('wp_ajax_coastalynk_load_port_congestion', [ $this, 'coastalynk_show_port_congestion']);
        add_action('wp_ajax_nopriv_coastalynk_load_port_congestion', [ $this, 'coastalynk_show_port_congestion']);

        add_action('wp_ajax_coastalynk_retrieve_tonnage', [ $this, 'coastalynk_retrieve_tonnage' ]);
        add_action('wp_ajax_nopriv_coastalynk_retrieve_tonnage', [ $this, 'coastalynk_retrieve_tonnage' ]);

        add_action('wp_ajax_coastalynk_retrieve_draught', [ $this, 'coastalynk_retrieve_draught' ]);
        add_action('wp_ajax_nopriv_coastalynk_retrieve_draught', [ $this, 'coastalynk_retrieve_draught' ]);

        add_action('wp_ajax_coastalynk_congestion_history_ports_data', [ $this, 'congestion_history_ports_data' ]);

        
        add_action('admin_post_coastalynk_leavy_form_download_pdf_submit', [ $this, 'download_pdf_submit' ]);
        add_action('admin_post_nopriv_coastalynk_leavy_form_download_pdf_submit', [ $this, 'download_pdf_submit' ]);

        
        add_action('admin_post_coastalynk_congestion_history_export_action', [ $this, 'coastalynk_congestion_history_export_action_callback' ]);
        add_action('admin_post_nopriv_coastalynk_congestion_history_export_action', [ $this, 'coastalynk_congestion_history_export_action_callback' ]);

        add_action('admin_post_coastalynk_sts_history_load_action_ctrl', [ $this, 'coastalynk_sts_history_load_action_ctrl_callback' ]);
        add_action('admin_post_nopriv_coastalynk_sts_history_load_action_ctrl', [ $this, 'coastalynk_sts_history_load_action_ctrl_callback' ]);
        
        add_action('admin_post_coastalynk_sbm_history_load_action_ctrl', [ $this, 'coastalynk_sbm_history_load_action_ctrl_callback' ]);
        add_action('admin_post_nopriv_coastalynk_sbm_history_load_action_ctrl', [ $this, 'coastalynk_sbm_history_load_action_ctrl_callback' ]);
        
        add_action( 'wp_enqueue_scripts', [ $this, 'coastalynk_enqueue_scripts' ] );
    }

    /**
     * enque dashboard.
     */
    function coastalynk_sbm_history_load_action_ctrl_callback( ) {
        global $wpdb;
        if (!isset($_POST['coastalynk_sbm_history_load_nonce']) || !wp_verify_nonce($_POST['coastalynk_sbm_history_load_nonce'], 'coastalynk_sbm_history_load')) {
            $error_url = add_query_arg('form_error', 'Security verification failed', wp_get_referer());
            wp_redirect($error_url);
            exit;
        }

        $port       = sanitize_text_field( $_REQUEST['caostalynk_history_ddl_ports'] );
        $date_range = sanitize_text_field( $_REQUEST['caostalynk_sbm_history_range'] );
        if( ! empty( $date_range ) ) {
            $where = '';
            if( ! empty( $port ) ) {
                $where = " and port = '".$port."' ";
            }

            $start_date = '';
            $end_date = '';
            if( !empty( $date_range ) ) {
                $explode = explode( '-', $date_range );
                $start_date = date( 'Y-m-d H:i:s', strtotime( trim( $explode[0] ) ) );
                $end_date = date( 'Y-m-d H:i:s', strtotime( trim( $explode[1] ) ) );
            } else {
                $start_date = date( 'Y-m-d' );
                $end_date = date( 'Y-m-d', strtotime('-6 Days'));
            }

            

            $vessle_recs = $wpdb->get_results( $wpdb->prepare( "select uuid, name, mmsi, imo, country_iso, type, type_specific, lat, lon, speed, navigation_status, draught, completed_draught, last_position_UTC, port, port_id, port_type, distance, is_offloaded, is_start_email_sent, is_complete_email_sent, last_updated from ".$wpdb->prefix."coastalynk_sbm where last_updated BETWEEN %s AND %s", $start_date, $end_date).$where, ARRAY_A );
            
            
            $fp = fopen('php://output', 'w'); 
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="sts.csv"');
            header('Pragma: no-cache');    
            header('Expires: 0');
            $headers = ['uuid', 'name', 'mmsi', 'imo', 'country_iso', 'type', 'type_specific', 'lat', 'lon', 'speed', 'navigation_status', 'draught', 'completed_draught', 'last_position_UTC', 'port', 'port_id', 'port_type', 'distance', 'is_offloaded', 'is_start_email_sent', 'is_complete_email_sent', 'last_updated'];
            if( ! empty( $vessle_recs ) && is_array( $vessle_recs ) ) {
                $headers = array_keys( $vessle_recs[0] );
            }
            fputcsv($fp, $headers); 
                
            
            if ($fp && $vessle_recs){     
                foreach( $vessle_recs as $vessle_row ) {
                    fputcsv($fp, array_values($vessle_row)); 
                }
            }
        }

        exit;
    }

    /**
     * enque dashboard.
     */
    function coastalynk_enqueue_scripts( ) {
        wp_enqueue_style( 'coastlynk-daterangepicker-css', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css' );
        wp_enqueue_style( 'coastlynk-map-dataTables-css', '//cdn.datatables.net/2.3.3/css/dataTables.dataTables.min.css' );
        wp_enqueue_style( 'coastlynk-select2.min.css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' );
        wp_enqueue_style( 'coastlynk-map-leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), time() ); 
        wp_enqueue_style( 'coastalynk-frontend-dashboard-style', CSM_CSS_URL.'/frontend/dashboard.css?'.time() );  
        
        wp_enqueue_script( 'coastlynk-map-dataTables-js', '//cdn.datatables.net/2.3.3/js/dataTables.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'coastlynk-moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'coastlynk-daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array( 'jquery' ) );
        wp_enqueue_script( 'coastlynk-select2.min.js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'markercluster', 'https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js', array( 'jquery' ) );
        wp_enqueue_script( 'coastalynk-frontend-dashboard-js', CSM_JS_URL.'/frontend/dashboard.js', array( 'jquery' ), time(), true );
    }

    /**
     * Process the STS export.
     */
    function coastalynk_sts_history_load_action_ctrl_callback( ) {
        
        global $wpdb;
        if (!isset($_POST['coastalynk_sts_history_load_nonce']) || !wp_verify_nonce($_POST['coastalynk_sts_history_load_nonce'], 'coastalynk_sts_history_load')) {
            $error_url = add_query_arg('form_error', 'Security verification failed', wp_get_referer());
            wp_redirect($error_url);
            exit;
        }

        $port       = sanitize_text_field( $_REQUEST['caostalynk_history_ddl_ports'] );
        $date_range = sanitize_text_field( $_REQUEST['caostalynk_sts_history_range'] );
        if( ! empty( $date_range ) ) {
            $where = '';
            if( ! empty( $port ) ) {
                $where = " and port = '".$port."' ";
            }

            $start_date = '';
            $end_date = '';
            if( !empty( $date_range ) ) {
                $explode = explode( '-', $date_range );
                $start_date = date( 'Y-m-d H:i:s', strtotime( trim( $explode[0] ) ) );
                $end_date = date( 'Y-m-d H:i:s', strtotime( trim( $explode[1] ) ) );
            } else {
                $start_date = date( 'Y-m-d' );
                $end_date = date( 'Y-m-d', strtotime('-6 Days'));
            }

            $vessle_recs = $wpdb->get_results( $wpdb->prepare( "select `vessel1_uuid`,`vessel1_name`, `vessel1_mmsi`, `vessel1_imo`, `vessel1_country_iso`, `vessel1_type`, `vessel1_type_specific`,`vessel1_lat`', `vessel1_lon`', `vessel1_speed`, `vessel1_navigation_status`, `vessel1_draught`, `vessel1_last_position_UTC`,`vessel2_uuid`, `vessel2_name`, `vessel2_mmsi`, `vessel2_imo`, `vessel2_country_iso`,`vessel2_type`, `vessel2_type_specific`, `vessel2_lat`', `vessel2_lon`', `vessel2_speed`, `vessel2_navigation_status`, `vessel2_draught`, `vessel2_last_position_UTC`, `port`, `port_id`, `distance`, `is_email_sent`, `is_complete`, `last_updated` from ".$wpdb->prefix."coastalynk_sts where last_updated BETWEEN %s AND %s", $start_date, $end_date).$where, ARRAY_A );
            
            $fp = fopen('php://output', 'w'); 
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="sts.csv"');
            header('Pragma: no-cache');    
            header('Expires: 0');
            $headers = ['vessel1_uuid','vessel1_name', 'vessel1_mmsi', 'vessel1_imo', 'vessel1_country_iso', 'vessel1_type', 'vessel1_type_specific','vessel1_lat', 'vessel1_lon', 'vessel1_speed', 'vessel1_navigation_status', 'vessel1_draught', 'vessel1_last_position_UTC','vessel2_uuid', 'vessel2_name', 'vessel2_mmsi', 'vessel2_imo', 'vessel2_country_iso','vessel2_type', 'vessel2_type_specific', 'vessel2_lat', 'vessel2_lon', 'vessel2_speed', 'vessel2_navigation_status', 'vessel2_draught', 'vessel2_last_position_UTC', 'port', 'port_id', 'distance', 'is_email_sent', 'is_complete', 'last_updated'];
            if( ! empty( $vessle_recs ) && is_array( $vessle_recs ) ) {
                $headers = array_keys( $vessle_recs[0] );
            }
            fputcsv($fp, $headers); 
                
            
            if ($fp && $vessle_recs){     
                foreach( $vessle_recs as $vessle_row ) {
                    fputcsv($fp, array_values($vessle_row)); 
                }
            }
        }

        exit;
    }
    /**
     * Process the congestion export.
     */
    function coastalynk_congestion_history_export_action_callback( ) {
        
        global $wpdb;
        if (!isset($_POST['coastalynk_congestion_history_load_nonce']) || !wp_verify_nonce($_POST['coastalynk_congestion_history_load_nonce'], 'coastalynk_congestion_history_load')) {
            $error_url = add_query_arg('form_error', 'Security verification failed', wp_get_referer());
            wp_redirect($error_url);
            exit;
        }

        $port       = sanitize_text_field( $_REQUEST['caostalynk_history_ddl_ports'] );
        $date_range = sanitize_text_field( $_REQUEST['caostalynk_congestion_history_range'] );
        $date       = sanitize_text_field( $_REQUEST['caostalynk_history_ddl_dates'] );
        if( ! empty( $date )  || ! empty( $date_range ) ) {

            $where = '';
            if( ! empty( $port ) ) {
                $where = " and port = '".$port."' ";
            }

            if( ! empty( $date ) ) {
                $date_time = strtotime( date('Y-m-d H:i:s', strtotime( $date ) ));
                $results = $wpdb->get_results( $wpdb->prepare( "select id, port from ".$wpdb->prefix."coastalynk_port_congestion where Hour(updated_at) = %s and Minute(updated_at) = %s and Date(updated_at) = %s", date('H', $date_time ), date('i', $date_time ), date('Y-m-d', $date_time ) ).$where );
            } else {
                $start_date = '';
                $end_date = '';
                if( !empty( $date_range ) ) {
                    $explode = explode( '-', $date_range );
                    $start_date = date( 'Y-m-d H:i:s', strtotime( trim( $explode[0] ) ) );
                    $end_date = date( 'Y-m-d H:i:s', strtotime( trim( $explode[1] ) ) );
                } else {
                    $start_date = date( 'Y-m-d' );
                    $end_date = date( 'Y-m-d', strtotime('-6 Days'));
                }

                $results = $wpdb->get_results( $wpdb->prepare( "select id, port from ".$wpdb->prefix."coastalynk_port_congestion where updated_at BETWEEN %s AND %s", $start_date, $end_date).$where );
            }
            $array = [];
            $vessle_recs = [];
            foreach( $results as $result ) {
                $sql = "SELECT uuid,`name`,`mmsi`,`eni`,`imo`, '".$result->port."' as port,`type`,`type_specific`,`country_iso`, `navigation_status`, `lat`, `lon`, `speed`, `course`, `heading`, `current_draught`, `dest_port_uuid`, `dest_port`, `dest_port_unlocode`, `dep_port`, `dep_port_uuid`, `dep_port_unlocode`, `last_position_epoch`, `last_position_UTC`, `atd_epoch`, `atd_UTC`, `eta_epoch`, `eta_UTC`, `destination` FROM `staging_coastalynk_port_congestion_vessels` WHERE `congestion_id`=%d;"; 
                $array = $wpdb->get_results( $wpdb->prepare( $sql, $result->id ), ARRAY_A  );
                $vessle_recs = array_merge( $vessle_recs, $array );
            }
            
            $fp = fopen('php://output', 'w'); 
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="port-congestions.csv"');
            header('Pragma: no-cache');    
            header('Expires: 0');
            $headers = ['uuid','name','mmsi','eni','imo', 'port','type','type_specific','country_iso', 'navigation_status', 'lat', 'lon', 'speed', 'course', 'heading', 'current_draught', 'dest_port_uuid', 'dest_port', 'dest_port_unlocode', 'dep_port', 'dep_port_uuid', 'dep_port_unlocode', 'last_position_epoch', 'last_position_UTC', 'atd_epoch', 'atd_UTC', 'eta_epoch', 'eta_UTC', 'destination'];
            if( ! empty( $vessle_recs ) && is_array( $vessle_recs ) ) {
                $headers = array_keys( $vessle_recs[0] );
            }
            fputcsv($fp, $headers); 
                
            
            if ($fp && $vessle_recs){     
                foreach( $vessle_recs as $vessle_row ) {
                    
                    fputcsv($fp, array_values($vessle_row)); 
                }
            }
        }
        exit;
    }


    /**
     * show_vessels shortcode content.
     */
    function download_pdf_submit( ) {

        // if (!isset($_POST['coastalynk_leavy_form_download_pdf_submit_nonce']) || !wp_verify_nonce($_POST['coastalynk_leavy_form_download_pdf_submit_nonce'], 'coastalynk_leavy_form_download_pdf_submit_form')) {
        //     $error_url = add_query_arg('form_error', 'Security verification failed', wp_get_referer());
        //     wp_redirect($error_url);
        //     exit;
        // }

        require( CSM_LIB_DIR.'vendor/autoload.php' );

        $coatalynk_levy_calculator_page_rate1 	= get_option( 'coatalynk_levy_calculator_page_rate1' );
        if( floatval( $coatalynk_levy_calculator_page_rate1 ) <= 0 ) {
             $coatalynk_levy_calculator_page_rate1 	= 0.5;
        }

        $coatalynk_levy_calculator_page_rate2 	= get_option( 'coatalynk_levy_calculator_page_rate2' );
        if( floatval( $coatalynk_levy_calculator_page_rate2 ) <= 0 ) {
             $coatalynk_levy_calculator_page_rate2 	= 0.3;
        }

        $coatalynk_levy_calculator_type 	    = get_option( 'coatalynk_levy_calculator_type' );
        if( empty( $coatalynk_levy_calculator_type ) ) {
            $coatalynk_levy_calculator_type 	    = 'normal';
        }

        $coastalynk_calculator_gt = sanitize_text_field( $_REQUEST['coastalynk_calculator_gt'] );
        $coastalynk_calculator_nt = sanitize_text_field( $_REQUEST['coastalynk_calculator_nt'] );

        $levy = (floatval($coastalynk_calculator_gt) * floatval( $coatalynk_levy_calculator_page_rate1 )) + (floatval($coastalynk_calculator_nt) * $coatalynk_levy_calculator_page_rate2);
        $levystr  = "";
        $second_param_str = '';
        // Display result with formatting
        if( $coatalynk_levy_calculator_type == 'dwt' ) {
            $min_dwt = 2000000;
            if( $levy < $min_dwt ) {
                $levystr = '<span class="currency">&#x20A6;</span>'.number_format( $min_dwt, 2 );
            } else {
                $levystr = '<span class="currency">&#x20A6;</span>'.number_format( ceil($levy / 10000) * 10000 , 2);
            }
            $second_param_str = __( "Dead Weight Tonnage (DWT)", "castalynkmap" );
        } else {
            $levystr = '<span class="currency">&#x20A6;</span>'.number_format($levy, 2);
            $second_param_str = __( "Net Tonnage (NT)", "castalynkmap" );
        }

        $options = new Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('chroot', __DIR__); // Set base directory
        $options->set('defaultFont', 'Helvetica');

        // Create PDF instance
        $dompdf = new Dompdf\Dompdf();

        $imageData = base64_encode(file_get_contents(get_template_directory_uri().'/assets/images/main_logo-footer.png'));
        $base64Image = 'data:image/jpeg;base64,' . $imageData;

        // HTML content
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .currency{font-family: "DejaVu Sans Mono", monospace;}
                h1 { color: #333; }
                .header-td { background-color: #ddd;border: 1px solid #ddd;padding: 8px; }
                .data-td { background-color: #eee;border: 1px solid #ddd;padding: 8px; }
                .content { margin: 20px; }
                footer{ position: absolute; bottom: 10px;}
            </style>
        </head>
        <body>
            <div class="content">
                <table width="100%" cellpadding="5px" cellspacing="0">
                    <tr>
                        <td colspan="2" align="center"><h1>'.__( "Levy Calculation Receipt", "castalynkmap" ).'</h1></td>
                    </tr>
                
                    <tr style="background-color:#317ec6;">
                        <td width="35%" style="background-color:#317ec6;"><img width=""171px src="'.$base64Image.'" /></td>
                        <td width="65%" valign="" style="background-color:#317ec6;color:white;"><h2>'.__( "Coastalynk", "castalynkmap" ).'</h2><span style="font-size:13px; color: white;">'.__( "Digital Maritime Intelligence Platform", "castalynkmap" ).'</span></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="header-td">'.__( "Reference#", "castalynkmap" ).'</td>
                        <td class="data-td">CR-'.date('Ymd').'-'.str_pad(get_current_user_id(), 3, "0", STR_PAD_LEFT).'</td>
                    </tr>
                    <tr>
                        <td class="header-td">'.__( "Gross Tonnage (GT)", "castalynkmap" ).'</td>
                        <td class="data-td">'.number_format($coastalynk_calculator_gt,2).' '.__( "tons", "castalynkmap" ).'</td>
                    </tr>
                    
                    <tr>
                        <td class="header-td">'.$second_param_str.'</td>
                        <td class="data-td">'.number_format($coastalynk_calculator_nt,2).' '.__( "tons", "castalynkmap" ).'</td>
                    </tr>
                    
                    <tr>
                        <td class="header-td">'.__( "Levy", "castalynkmap" ).'</td>
                        <td class="data-td"><b>'.$levystr.'</b></td>
                    </tr>
                    <tr>
                        <td class="header-td">'.__( "Date", "castalynkmap" ).'</td>
                        <td class="data-td">'.wp_date('M d, Y').'</td>
                    </tr>
                </table>
            </div>
            <footer>Generated by Coastalynk Levy Calculator - Pilot Version.</footer>
        </body>
        </html>';

        // Load HTML content
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Output the PDF
        $dompdf->stream("document.pdf", array("Attachment" => false));

        exit;
    }
    /**
     * ports data.
     */
    function congestion_history_ports_data( ) {
        global $wpdb;

        if ( ! check_ajax_referer( 'coastalynk_secure_ajax_nonce', 'nonce', false ) ) {
            wp_send_json_error( __( 'Security nonce check failed. Please refresh the page.', "castalynkmap" ) );
            wp_die();
        }

        $port = sanitize_text_field( $_REQUEST['port'] );
        $dates = sanitize_text_field( $_REQUEST['dates'] );
        $start_date = '';
        $end_date = '';
        if( !empty( $dates ) ) {
            $explode = explode( '-', $dates );
            $start_date = date( 'Y-m-d H:i:s', strtotime( trim( $explode[0] ) ) );
            $end_date = date( 'Y-m-d H:i:s', strtotime( trim( $explode[1] ) ) );
        } else {
            $start_date = date( 'Y-m-d' );
            $end_date = date( 'Y-m-d', strtotime('-6 Days'));
        }

        $where = '';
        if( !empty( $port ) ) {
            $where = " and port = '".$port."' ";
        }

        $results = $wpdb->get_results( $wpdb->prepare( "select distinct(updated_at) as updated_at from ".$wpdb->prefix."coastalynk_port_congestion where updated_at BETWEEN %s AND %s", $start_date, $end_date).$where );
        $array = [];
        
        foreach( $results as $result ) {

            $array[] = $result->updated_at;
            
        }
        echo wp_send_json_success(['date_all'=>__( "All Dates", "castalynkmap" ), 'options'=>$array]);
        exit;
    }

    /**
     * show_vessels shortcode content.
     */
    function coastalynk_show_port_congestion( ) {
        
        global $wpdb;
 
        if ( ! check_ajax_referer( 'coastalynk_secure_ajax_nonce', 'nonce', false ) ) {
            wp_send_json_error( __( 'Security nonce check failed. Please refresh the page.', "castalynkmap" ) );
            wp_die();
        }

        $port_name = sanitize_text_field( $_REQUEST['selected_port'] );
        if( empty( $port_name ) || $port_name == 'all' ) {
            $port_name = 'WARRI';
        }  

        ob_start();
        ?>
            <div class="section-title d-flex justify-content-between mb-0 leftalign">
                <h3><?php echo  __( ucwords( $port_name ) . ' Port Congestion', "castalynkmap" );?></h3>               
            </div>
        <?php
        $updated_at = '';
        $congestion_id = 0;
        $congestion = $wpdb->get_row( "select id, updated_at from ".$wpdb->prefix."coastalynk_port_congestion where port like '%" . $wpdb->esc_like( $port_name ) . "%' order by id desc limit 1" );
        if( $congestion ) {
            $updated_at = $congestion->updated_at;
            $congestion_id = $congestion->id;
        }
        
        ?>
        <div class="coastalynk-stat-main-wrapper">
            <div class="coastalynk-stat-item-wrapper">
                <?php
                $columns = $wpdb->get_col( "select distinct(type) as type from ".$wpdb->prefix."coastalynk_port_congestion_vessels where congestion_id='" .$congestion_id."'" );
                $total_vessels = 0;
                foreach( $columns as $type  ) {
                    if( !empty( $type ) ) {
                        $total = $wpdb->get_var( "select count(id) as total from ".$wpdb->prefix."coastalynk_port_congestion_vessels where congestion_id='" .$congestion_id."' and type like '%" . $wpdb->esc_like( $type ) . "%'" );
                        if( intval($total) > 0 ) {
                            $total_vessels += $total;
                            ?>
                                <div class="stat-item">
                                    <div class="stat-label"><?php _e( ucwords( $type ), "castalynkmap" );?></div>
                                    <div class="stat-value" id="total-vessels">
                                        <?php echo $total;?> <?php if(intval( $total ) > 1 ) { echo __( "vessel(s)", "castalynkmap" ); } else { echo __( "vessel", "castalynkmap" ); } ?>
                                    </div>
                                </div>
                            <?php
                        }
                    }
                }
                ?>

                <div class="stat-item">
                    <div class="stat-label"><?php _e( 'Total Vessels', "castalynkmap" );?></div>
                    <div class="stat-value" id="total-vessels">
                        <?php echo $total_vessels;?> <?php if(intval( $total_vessels ) > 1 ) { echo __( "vessel(s)", "castalynkmap" ); } else { echo __( "vessel", "castalynkmap" ); } ?>
                    </div>
                </div>
            </div>
            <div class="coastalynk-date-updated">
                <div class="stat-label"><?php _e( "Updated Congestion Data", "castalynkmap" );?></div>
                <div class="stat-value" id="total-vessels"><?php echo get_date_from_gmt( $updated_at, CSM_DATE_FORMAT.' '.CSM_TIME_FORMAT);?></div>
            </div>
        </div>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        
        echo $content;
        die();

    }

    /**
     * retrieve the vessel tonnage.
     */
    function coastalynk_retrieve_draught() {
        ini_set( "display_errors", "On" );
        error_reporting(E_ALL);
        $selected_uuid = isset( $_REQUEST['selected_uuid']  ) ? sanitize_text_field( $_REQUEST['selected_uuid'] ): "";
        $selected_name = isset( $_REQUEST['selected_name']  ) ? sanitize_text_field( $_REQUEST['selected_name'] ): "";

        if ( ! check_ajax_referer( 'coastalynk_secure_ajax_nonce', 'nonce', false ) ) {
            wp_send_json_error( __( 'Security nonce check failed. Please refresh the page.', "castalynkmap" ) );
            wp_die();
        }

        $apiKey 	= get_option( 'coatalynk_datalastic_apikey' );
        $endpoint = 'https://api.datalastic.com/api/v0/vessel_find';
        $params = array(
            'api-key' => $apiKey,
            'name' => $selected_name,
            'fuzzy'    => '0'
        );
        $url = add_query_arg($params, $endpoint);

        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            error_log('API Request Failed: ' . $response->get_error_message());
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $vessel_find = json_decode($body);

        $draught_avg = 0; 
        $draught_max = 0;
           
        $vessel_data_find = [];
        if( isset( $vessel_find->data ) ){
            foreach( $vessel_find->data as $dat ) {
                if( trim( $selected_uuid ) == trim( $dat->uuid ) ) {
                    $vessel_data_find = $dat;
                }
            }
        }

        $endpoint = 'https://api.datalastic.com/api/v0/vessel_pro';
        $params = array(
            'api-key' => $apiKey,
            'uuid' => $selected_uuid
        );
        $url = add_query_arg($params, $endpoint);

        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            error_log('API Request Failed: ' . $response->get_error_message());
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $vessel_pro = json_decode($body);
        $vessel_pro_data = [];
        if( isset( $vessel_pro->data ) ){
            $vessel_pro_data = $vessel_pro->data;
        }
        
        if( !empty( $vessel_pro_data ) && !empty( $vessel_data_find ) ) {
            
            $type = $vessel_pro_data->type;
            $k_factor = 0;
            if( in_array( $type, ['Tanker', 'Tanker - Hazard A (Major)', 'Tanker - Hazard B', 'asdad', 'Tanker - Hazard C (Minor)', 'Tanker - Hazard D (Recognizable)', 'Tanker: Hazardous category A', 'Tanker: Hazardous category B', 'Tanker: Hazardous category C', 'Tanker: Hazardous category D'] ) ) {
                $k_factor = 0.26;
            } else if( in_array( $type, [ 'Cargo', 'Cargo - Hazard A (Major)', 'Cargo - Hazard B', 'Cargo - Hazard C (Minor)', 'Cargo - Hazard D (Recognizable)', 'Cargo: Hazardous category A', 'Cargo: Hazardous category B', 'Cargo: Hazardous category C', 'Cargo: Hazardous category D' ] ) ) {
                $k_factor = 0.25;
            } else if( in_array( $type, [ 'Passenger' ] ) ) {
                $k_factor = 0.23;
            } else if( in_array( $type, [ 'Fishing' ] ) ) {
                $k_factor =  0.20;
            } else if( in_array( $type, [ 'Tug', 'OffShore Structure', 'Dredger' ] ) ) {
                $k_factor =  0.21;
            } else if( in_array( $type, [ 'Other', 'Unspecified' ] ) ) {
                $k_factor =  0.21;
            }

            $current_draught = $vessel_pro_data->current_draught;
            $deadweight = $vessel_data_find->deadweight;
            $draught_max = $vessel_data_find->draught_max;
            if( floatval( $draught_max ) > 0 && floatval( $current_draught ) > 0 && floatval( $current_draught ) <= floatval( $draught_max ) && $k_factor > 0 ) {
                $percentage = ( floatval( $current_draught ) * 100 ) / $draught_max;
                if( $percentage > 80 ) {
                    echo __( "Loaded", "castalynkmap" );
                } else if( $percentage <= 80 && $percentage >= 40 ) {
                    echo __( "Partial", "castalynkmap" );
                } else {
                    echo __( "Ballast", "castalynkmap" );
                }
                
            } else if( floatval( $deadweight ) > 0 && $k_factor > 0 ) {
                
                $draught_max = pow( $deadweight, 1/3) * 0.25;
                $percentage = ( floatval( $current_draught ) * 100 ) / $draught_max;
                if( $percentage > 80 ) {
                    echo __( "Loaded", "castalynkmap" );
                } else if( $percentage <= 80 && $percentage >= 40 ) {
                    echo __( "Partial", "castalynkmap" );
                } else {
                    echo __( "Ballast", "castalynkmap" );
                }
            } else {
                    echo __( "Unknown", "castalynkmap" );
            }
        } else {
            echo __( "Unknown", "castalynkmap" );
        }

        exit;
    }

    /**
     * retrieve the vessel tonnage.
     */
    function coastalynk_retrieve_tonnage() {
        $selected_uuid = isset( $_REQUEST['selected_uuid']  ) ? sanitize_text_field( $_REQUEST['selected_uuid'] ): "";
        $selected_name = isset( $_REQUEST['selected_name']  ) ? sanitize_text_field( $_REQUEST['selected_name'] ): "";

        if ( ! check_ajax_referer( 'coastalynk_secure_ajax_nonce', 'nonce', false ) ) {
            wp_send_json_error( __( 'Security nonce check failed. Please refresh the page.', "castalynkmap" ) );
            wp_die();
        }

        $apiKey 	= get_option( 'coatalynk_datalastic_apikey' );
        $endpoint = 'https://api.datalastic.com/api/v0/vessel_find';
        $params = array(
            'api-key' => $apiKey,
            'name' => $selected_name,
            'fuzzy'    => '0'
        );
        $url = add_query_arg($params, $endpoint);

        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            error_log('API Request Failed: ' . $response->get_error_message());
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        // Process the data as needed.
        if (!empty($data)) {
            foreach( $data->data as $dat ) {
                if( $selected_uuid == $dat->uuid ) {
                    if( intval( $dat->gross_tonnage ) > 0 ) {
                        echo $dat->gross_tonnage.' '.__( "Ton(s)", "castalynkmap" );
                    } else {
                        echo __( "N/A", "castalynkmap" );
                    }
                    
                    exit;
                } 
            }
        }

        exit;
    }
    
}

/**
 * @return bool
 */
return Coastalynk_Sea_Vessel_Map_Front::instance();