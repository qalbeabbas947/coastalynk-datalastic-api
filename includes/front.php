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

    function sts_save_blob_screenshots() {
        $uploadDir = CSM_INCLUDES_DIR.'uploads/';
        $filePath = $uploadDir . basename($_FILES['file']['name']);

        // Check if the file was uploaded without errors
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $tempName = $_FILES['file']['tmp_name'];

            // Move the temporary file to the desired location on the server
            if (move_uploaded_file($tempName, $filePath)) {
                echo "The file ". basename($_FILES['file']['name']) . " has been uploaded successfully.";
            } else {
                echo "There was an error moving the uploaded file.";
            }
        } else {
            echo "No file uploaded or an upload error occurred.";
            // You can check $_FILES['file']['error'] for specific error codes
        }
    }
    /**
     * Plugin requiered files
     */
    public function hooks() {

        add_action('wp_ajax_coastalynk_load_port_congestion', [ $this, 'coastalynk_show_port_congestion']);
        add_action('wp_ajax_nopriv_coastalynk_load_port_congestion', [ $this, 'coastalynk_show_port_congestion']);

        add_action('wp_ajax_sts_save_blob_screenshots', [ $this, 'sts_save_blob_screenshots']);
        add_action('wp_ajax_nopriv_sts_save_blob_screenshots', [ $this, 'sts_save_blob_screenshots']);

        

        add_action('wp_ajax_coastalynk_retrieve_tonnage', [ $this, 'coastalynk_retrieve_tonnage' ]);
        add_action('wp_ajax_nopriv_coastalynk_retrieve_tonnage', [ $this, 'coastalynk_retrieve_tonnage' ]);

        add_action('wp_ajax_coastalynk_retrieve_draught', [ $this, 'coastalynk_retrieve_draught' ]);
        add_action('wp_ajax_nopriv_coastalynk_retrieve_draught', [ $this, 'coastalynk_retrieve_draught' ]);

        add_action('wp_ajax_coastalynk_congestion_history_ports_data', [ $this, 'congestion_history_ports_data' ]);

        
        add_action('admin_post_coastalynk_leavy_form_download_pdf_submit', [ $this, 'download_pdf_submit' ]);
        add_action('admin_post_nopriv_coastalynk_leavy_form_download_pdf_submit', [ $this, 'download_pdf_submit' ]);

        
        add_action('admin_post_coastalynk_congestion_history_export_action', [ $this, 'coastalynk_congestion_history_export_action_callback' ]);
        add_action('admin_post_nopriv_coastalynk_congestion_history_export_action', [ $this, 'coastalynk_congestion_history_export_action_callback' ]);

        add_action('admin_post_coastalynk_sts_history_load_action_ctrl_csv', [ $this, 'coastalynk_sts_history_load_action_ctrl_csv_callback' ]);
        add_action('admin_post_nopriv_coastalynk_sts_history_load_action_ctrl_csv', [ $this, 'coastalynk_sts_history_load_action_ctrl_csv_callback' ]);


        add_action('admin_post_coastalynk_sts_popup_history_export_single_event_id', [ $this, 'coastalynk_sts_popup_history_export_single_event_id_callback' ]);
        add_action('admin_post_nopriv_coastalynk_sts_popup_history_export_single_event_id', [ $this, 'coastalynk_sts_popup_history_export_single_event_id_callback' ]);

        add_action('admin_post_coastalynk_sts_history_load_action_ctrl_pdf', [ $this, 'coastalynk_sts_history_load_action_ctrl_pdf_callback' ]);
        add_action('admin_post_nopriv_coastalynk_sts_history_load_action_ctrl_pdf', [ $this, 'coastalynk_sts_history_load_action_ctrl_pdf_callback' ]);


        add_action('admin_post_coastalynk_sbm_history_load_action_ctrl', [ $this, 'coastalynk_sbm_history_load_action_ctrl_callback' ]);
        add_action('admin_post_nopriv_coastalynk_sbm_history_load_action_ctrl', [ $this, 'coastalynk_sbm_history_load_action_ctrl_callback' ]);

        add_action('wp_ajax_coastalynk_retrieve_daughterships', [ $this, 'coastalynk_retrieve_daughterships_callback' ]);
        add_action('wp_ajax_nopriv_coastalynk_retrieve_daughterships', [ $this, 'coastalynk_retrieve_daughterships_callback' ]);
        
        
        add_action( 'wp_enqueue_scripts', [ $this, 'coastalynk_enqueue_scripts' ] );
    }

    /**
     * enque dashboard.
     */
    function coastalynk_retrieve_daughterships_callback() { 
        
        global $wpdb;
        if ( ! check_ajax_referer( 'coastalynk_secure_ajax_nonce', 'nonce', false ) ) {
            wp_send_json_error( __( 'Security nonce check failed. Please refresh the page.', "castalynkmap" ) );
            wp_die();
        }

        $event_id = sanitize_text_field( $_POST['event_id'] );
        $port = isset( $_POST['port'] ) ? sanitize_text_field( $_POST['port'] ) : '';
        $event_table_daughter = $wpdb->prefix . 'coastalynk_sts_event_detail';
        $table_listing = $wpdb->get_results( $wpdb->prepare( "SELECT * from ".$event_table_daughter." where event_id = %d", $event_id ),ARRAY_A );   
        $draught_change = 0;
        ?>
            <table id="coastalynk-sts-popup-table" class="display" class="cell-border hover stripe"> 
                <thead>
                    <tr>
                        <th></th>
                        <th><?php _e( "Name", "castalynkmap" );?></th>
                        <th><?php _e( "MMSI", "castalynkmap" );?></th>
                        <th><?php _e( "IMO", "castalynkmap" );?></th>
                        <th><?php _e( "Type", "castalynkmap" );?></th>
                        <th><?php _e( "Tonnage", "castalynkmap" );?></th>
                        <th><?php _e( "Detail", "castalynkmap" );?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $table_listing as $vessel ) { 
                        $draught_change += floatval( $vessel['draught_change']);
                        ?>
                        <tr>
                            <td>
                                <?php 
                                    if( !empty( $vessel['country_iso'] ) ) {
                                        echo '<img src="'.CSM_IMAGES_URL."flags/".strtolower( $vessel['country_iso'] ).".jpg".'" class="coastalyn-flag-port-listing" alt="'.$vessel['country_iso'].'">';
                                    }
                                ?>
                            </td>
                            <td><?php echo $vessel['name']; ?></td>
                            <td><?php echo $vessel['mmsi']; ?></td>
                            <td><?php echo $vessel['imo']; ?></td>
                            <td><?php echo coastalynk_get_vessel_short_types( $vessel['type']); ?></td>
                            <td><?php echo $vessel['gross_tonnage']; ?></td>
                            
                            <td>
                                <?php
                                    $attributes = '';
                                    foreach( $vessel as $key=>$val ) {

                                        
                                        if( in_array( $key, ['last_updated', 'start_date', 'lock_time', 'joining_date', 'end_date'] ) ) {
                                            if( ! empty( $val ) && $val != '-' ) {
                                                $attributes .= ' data-'.$key.' = "'.get_date_from_gmt( $val, CSM_DATE_FORMAT.' '.CSM_TIME_FORMAT ).'"';
                                            } else {
                                                $attributes .= ' data-'.$key.' = ""';
                                            }
                                        } else if( in_array( $key, ['remarks' ] ) ) {
                                            $end_date = date( 'Y-m-d H:i:s' );
                                            if( !empty( $vessel['end_date'] ) ) {
                                                $end_date = $vessel['end_date'];
                                            } else if( !empty( $vessel['last_updated'] ) ) {
                                                $end_date = $vessel['last_updated'];
                                            }

                                            $dateTime1 = new DateTime($end_date);
                                            $dateTime2 = new DateTime($vessel['joining_date']);
                                            $interval = $dateTime2->diff($dateTime1);
                                            $totalMinutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

                                            $total_hours = ( $interval->days * 24 + $interval->h ).'h '. $interval->i.'m';

                                            $stationary_duration_mins   = ( floatval( $vessel['stationary_duration_hours'] ) * 60 );
                                            $confidence_level = 'Low';
                                            if( $stationary_duration_mins < 30 ) {
                                                $confidence_level = 'Low';
                                            } else if( $stationary_duration_mins >= 30 && $stationary_duration_mins <= 90 ) {
                                                $confidence_level = 'Medium';
                                            } else if( $stationary_duration_mins > 90 && ( $vessel['distance'] < 10 || $vessel['speed'] < 2 ) ) {
                                                $confidence_level = 'High';
                                            }
                                            $confidence_level = $vessel['confidence_string'];
                                            
                                            $attributes .= ' data-'.$key.' = "'.$this->generateSTSRemark($vessel['proximity_consistency'], number_format($vessel['distance'], 1), number_format($vessel['speed'], 1), $total_hours, strtoupper($port), $confidence_level, $vessel['status'],  $vessel['stationary_duration_hours']).'"';
                                        } else if( in_array( $key, ['draught' ] ) ) {
                                            $attributes .= ' data-'.$key.' = "'.( floatval( $val ) > 0?$val.'m':__( "Pending", "castalynkmap" )).'"';
                                        } else if( in_array( $key, [ 'completed_draught' ] ) ) {
                                            $attributes .= ' data-'.$key.' = "'.( floatval( $val ) > 0?$val.'m':__( "Not Eligible", "castalynkmap" )).'"';
                                        } else if( in_array( $key, ['draught_change' ] ) ) {
                                            if( floatval( $val ) > 0 || floatval( $val ) > 0 ) {
                                                $attributes .= ' data-'.$key.' = "'.floatval( $val ).'m"';
                                            } else {
                                                $attributes .= ' data-'.$key.' = "'.__( "No Change", "castalynkmap" ).'"';
                                            }
                                        } else if( in_array( $key, [ 'status' ] ) ) {
                                            $attributes .= ' data-'.$key.' = "'.( $val == 'Completed'?__( "Concluded", "castalynkmap" ):($val == 'Detected'?__( "Ongoing", "castalynkmap" ):$val)).'"';
                                        } else {
                                            $attributes .= ' data-'.$key.' = "'.$val.'"';
                                        }
                                    }
                                ?>
                                <input type="button" <?php echo $attributes;?> class="coastalynk-sts-retrieve-popup-daughtership-btn" value="<?php _e( "Detail", "castalynkmap" );?>">
                            </td>
                       </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    
                </tfoot>
            </table>
            <input type="hidden" id="coastalynk-sts-popup-calculated-draught-change" value="<?php echo $draught_change==0?__( "Pending / AIS-limited", "castalynkmap" ):$draught_change;?>" />  
        <?php
        exit;
    }

    function generateSTSRemark($proximity, $distance, $speed, $duration, $location, $confidence, $status, $stationary_duration_hours) {
        // Format proximity with nm
        
        return $string = sprintf( __( 'Distance: %sm | Speed: %skn | Duration: %s | Confidence: %s | Zone: %s', "castalynkmap" ), $distance, $speed, $duration, $confidence, $location );

        // $formattedProximity = is_numeric($proximity) ? number_format($proximity, 2) . 'nm' : $proximity;
        // $formattedProximitystr = '';
        // if( floatval($proximity) > 70 ) {
        //     $formattedProximitystr = sprintf( __( 'maintained close contact (~%s proximity)', "castalynkmap" ), $formattedProximity );
        // } else {
        //     $formattedProximitystr = sprintf( __( 'about to maintain a contact (~%s proximity)', "castalynkmap" ), $formattedProximity );
        // }
       
        // // Format speed with kn and determine description
        // $speedDesc = '';
        // if (is_numeric($speed)) {
        //     $formattedSpeed = number_format($speed, 1) . 'kn';
        //     $speedDesc = $speed <= 1.0 ? 'low' : ($speed <= 3.0 ? 'moderate' : 'high');
        // } else {
        //     $formattedSpeed = $speed;
        // }
        
        // // Format duration
        // $formattedDuration = '';
        // if (is_numeric($duration)) {
        //     $hours = floor($duration / 60);
        //     $minutes = $duration % 60;
        //     $formattedDuration = $hours > 0 ? $hours . 'h' : '';
        //     $formattedDuration .= $minutes > 0 ? ($hours > 0 ? ' ' : '') . $minutes . 'm' : '';
        // } else {
        //     $formattedDuration = $duration;
        // }
        
        // // Format confidence with proper case
        // $formattedConfidence = ucfirst(strtolower($confidence));
        
        // $statusDesc = '';
        // switch ($status) {
        //     case 'tentative':
        //         $statusDesc = __( 'Uncertain STS progress — ', "castalynkmap" );
        //         break;
        //     case 'ended':
        //         $statusDesc = __( 'STS progress is complete — ', "castalynkmap" );
        //         break;
        //     case 'active':
        //         $statusDesc = __( 'STS likely ongoing — ', "castalynkmap" );
        //         break;
        //     default:
        //         $statusDesc = '';
        //         break;
        // }
        // // Build the remark
        // return $statusDesc.$formattedProximitystr." at $speedDesc speed (~$formattedSpeed) for ~$formattedDuration within $location. Confidence: $formattedConfidence.";
    }

    /**
     * Generate narrative line for STS operation
     * Format:
     * "An STS operation was observed between [Vessel A] and [Vessel B] within [Zone] from [Start Time] to [End Time or 'present'].
     * The vessels maintained [Proximity Signal] proximity during this period.
     * AIS data continuity was [AIS Continuity], and draught data was [Draught Evidence]"
     */
    private function generateNarrativeLine($analysis, $vessel1, $vessel2) {
        // Get vessel names
        $vesselAName = $vessel1['name'] ?? 'Unknown Vessel';
        $vesselBName = $vessel2['name'] ?? 'Unknown Vessel';
        
        // Get zone/terminal name (you need to implement or call getZoneTerminalName)
        $lat = $vessel1['lat'] ?? $vessel1['position']['lat'] ?? 0;
        $lon = $vessel1['lon'] ?? $vessel1['position']['lon'] ?? 0;
        $zone = $vessel1['zone_terminal_name'];
        
        if (empty($zone)) {
            $zone = "the area at coordinates " . round($lat, 4) . ", " . round($lon, 4);
        }
        
        // Format start time
        $startTime = !empty($analysis['start_date']) ? 
            date('Y-m-d H:i', strtotime($analysis['start_date'])) : 
            date('Y-m-d H:i');
        
        // Determine end time
        $endTime = !empty($analysis['end_date']) ? 
            date('Y-m-d H:i', strtotime($analysis['end_date'])) : 
            'present';
        
        // Get proximity signal
        $proximitySignal = $analysis['proximity_signal'] ?? 'unknown';
        
        // Get AIS continuity (use the worse of the two vessels)
        $aisContinuityV1 = $analysis['ais_continuity_v1'] ?? 'Limited';
        $aisContinuityV2 = $analysis['ais_continuity_v2'] ?? 'Limited';
        
        // Determine overall AIS continuity
        $aisContinuity = 'Good';
        if ($aisContinuityV1 === 'Limited' || $aisContinuityV2 === 'Limited') {
            $aisContinuity = 'Limited';
        }
        
        if ($aisContinuityV1 === 'Intermittent' || $aisContinuityV2 === 'Intermittent') {
            $aisContinuity = 'Intermittent';
        }
        
        // Get draught evidence
        $draughtEvidence = $analysis['draught_evidence'] ?? 'AIS-Limited';
        
        // Build the narrative
        $narrative = sprintf(
            "An STS operation was observed between %s and %s within %s from %s to %s. " .
            "The vessels maintained %s proximity during this period. " .
            "AIS data continuity was %s, and draught data was %s.",
            $vesselAName,
            $vesselBName,
            $zone,
            $startTime,
            $endTime,
            strtolower($proximitySignal),
            strtolower($aisContinuity),
            $draughtEvidence
        );
        
        return $narrative;
    }
    /**
     * Create a speed message.
     */
    function getFleetSpeedMessage($mother_vessel, $daughter_vessels) {
        $speed_threshold = 2; // knots
        
        // Check if we have any vessels at all
        if (empty($mother_vessel) && empty($daughter_vessels)) {
            return "Speed behaviour cannot be determined due to incomplete AIS data.";
        }
        
        // Check for missing speed data in mother vessels
        if (!isset($mother_vessel['speed']) || $mother_vessel['speed'] === '') {
            return "Speed behaviour cannot be determined due to incomplete AIS data.";
        }
        
        // Check for missing speed data in daughter vessels
        foreach ($daughter_vessels as $vessel) {
            if (!isset($vessel['speed']) || $vessel['speed'] === '') {
                return "Speed behaviour cannot be determined due to incomplete AIS data.";
            }
        }
        
        // Determine if any mother vessel is moving (>= 2 knots)
        $any_mother_moving = false;
        $all_mothers_slow = true;
        
        $speed = floatval($mother_vessel['speed']);
        if ($speed >= $speed_threshold) {
            $any_mother_moving = true;
            $all_mothers_slow = false;
        }
        
        // Determine if any daughter vessel is moving (>= 2 knots)
        $any_daughter_moving = false;
        $all_daughters_slow = true;
        
        foreach ($daughter_vessels as $daughter) {
            $speed = floatval($daughter['speed']);
            if ($speed >= $speed_threshold) {
                $any_daughter_moving = true;
                $all_daughters_slow = false;
            }
        }
        
        // Handle cases with no mother vessels
        if (empty($mother_vessel)) {
            if (!$any_daughter_moving) {
                return "All vessels are moving slowly or stationary.";
            } else {
                return "All vessels are maneuvering.";
            }
        }
        
        // Handle cases with no daughter vessels
        if (empty($daughter_vessels)) {
            if (!$any_mother_moving) {
                return "All vessels are moving slowly or stationary.";
            } else {
                return "The main vessel is moving while one or more daughter vessels remain stationary.";
            }
        }
        
        // Determine the message based on vessel states
        if (!$any_mother_moving && !$any_daughter_moving) {
            return "All vessels are moving slowly or stationary.";
        } elseif (!$any_mother_moving && $any_daughter_moving) {
            return "The main vessel is stationary while one or more daughter vessels are maneuvering nearby.";
        } elseif ($any_mother_moving && !$any_daughter_moving) {
            return "The main vessel is moving while one or more daughter vessels remain stationary.";
        } elseif ($any_mother_moving && $any_daughter_moving) {
            return "All vessels are maneuvering.";
        }
        
        // Fallback
        return "Speed behaviour cannot be determined due to incomplete AIS data.";
    }

    /**
     * Export Single Event PDF
     */
    function coastalynk_sts_popup_history_export_single_event_id_callback() {

        if (!isset($_POST['coastalynk_sts_popup_history_load_nonce']) || !wp_verify_nonce($_POST['coastalynk_sts_popup_history_load_nonce'], 'coastalynk_sts_popup_history_load')) {
            echo $error_url = add_query_arg('form_error', 'Security verification failed', wp_get_referer());exit;
            wp_redirect($error_url);
            exit;
        }

        $event_id = intval($_POST['event_id']);
        if ($event_id <= 0) {
            $error_url = add_query_arg('form_error', 'Event ID is required', wp_get_referer());
            wp_redirect($error_url);
            exit;
        }

        $coastalynk_sts_popup_map_image = $_POST['coastalynk_sts_popup_map_image'];
        global $wpdb;

        $event_table_mother = $wpdb->prefix . 'coastalynk_sts_events';
        $event_table_daughter = $wpdb->prefix . 'coastalynk_sts_event_detail';

        // Fetch mother vessel data
        $mother_vessel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM " . $event_table_mother . " WHERE id = %d",
            $event_id
        ), ARRAY_A);

        if (!$mother_vessel) {
            $error_url = add_query_arg('form_error', 'Event not found', wp_get_referer());
            wp_redirect($error_url);
            exit;
        }

        // Fetch daughter vessel(s) data
        $daughter_vessels = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM " . $event_table_daughter . " WHERE event_id = %d ORDER BY id ASC",
            $event_id
        ), ARRAY_A);

        require(CSM_LIB_DIR . 'vendor/autoload.php');
        $options = new Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('chroot', __DIR__);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf\Dompdf();

        // Prepare image data
        $imageData = base64_encode(file_get_contents('https://coastalynk.com/staging/wp-content/themes/coastalynk/assets/images/pdf_logo.png'));
        $base64Image = 'data:image/jpeg;base64,' . $imageData;

        // Generate Event Narrative (Section 3)
        $event_narrative = "The system observed ";
        if ($mother_vessel['name'] && !empty($daughter_vessels)) {
            $event_narrative .= $mother_vessel['name'] . " (MMSI: " . $mother_vessel['mmsi'] . ") ";
            
            if (count($daughter_vessels) == 1) {
                $event_narrative .= "and " . $daughter_vessels[0]['name'] . " (MMSI: " . $daughter_vessels[0]['mmsi'] . ") ";
            } else {
                $event_narrative .= "and " . count($daughter_vessels) . " other vessel(s) ";
            }
            
            $event_narrative .= "in proximity at coordinates " . $mother_vessel['lat'] . ", " . $mother_vessel['lon'] . ". ";
        }

        if ($mother_vessel['start_date'] && $mother_vessel['end_date']) {
            $event_narrative .= "The observed proximity period was from " . $mother_vessel['start_date'] . " to " . $mother_vessel['end_date'] . ". ";
        }

        if (!empty($daughter_vessels)) {
            $first_daughter = $daughter_vessels[0];
            if ($first_daughter['stationary_duration_hours']) {
                $event_narrative .= "Vessels maintained stationary or low-speed operations for approximately " . $first_daughter['stationary_duration_hours'] . " hours. ";
            }
            
            if ($first_daughter['proximity_consistency']) {
                $event_narrative .= "Proximity consistency was measured at " . $first_daughter['proximity_consistency'] . "%. ";
            }
            
            if ($first_daughter['data_points_analyzed']) {
                $event_narrative .= $first_daughter['data_points_analyzed'] . " AIS data points were analyzed during this observation. ";
            }
        }

        $event_narrative .= "The event concluded with vessels moving apart or one vessel departing the area.";

        // Generate System Notes (Section 5)
        $system_notes = [];

        // Check for AIS gaps
        $ais_signal_mother = $mother_vessel['ais_signal'] ?? null;
        foreach ($daughter_vessels as $daughter) {
            $ais_signal_daughter = $daughter['ais_signal'] ?? null;
            if ($ais_signal_mother == 'weak' || $ais_signal_daughter == 'weak') {
                $system_notes[] = "AIS signal gaps were detected for one or more vessels during the observation period.";
                break;
            }
        }

        // Draught data limitations
        if (empty($mother_vessel['draught']) || empty($mother_vessel['completed_draught'])) {
            $system_notes[] = "Draught change data is incomplete or unavailable for the mother vessel.";
        }

        // Zone assumptions
        if ($mother_vessel['zone_type']) {
            $system_notes[] = "Zone classification is based on system-defined geographical boundaries (" . $mother_vessel['zone_type'] . ").";
        }

        // Default notes
        $system_notes[] = "This report is generated from automated AIS data analysis only.";
        $system_notes[] = "No visual confirmation or additional intelligence sources were used.";
        $system_notes[] = "Event classification is based on proximity, duration, and AIS behavior patterns.";
        $generated = wp_date( 'Y-m-d H:i', null, new DateTimeZone( 'UTC' ) );
        // HTML content with new structure
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.4; }
                .header-section { background-color: #317ec6; color: white; padding: 10px; }
                .section-title { background-color: #f0f0f0; padding: 8px; font-weight: bold; border-left: 4px solid #317ec6; margin-top: 20px; }
                .content-box { border: 1px solid #ddd; padding: 12px; margin-bottom: 15px; }
                .vessel-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                .vessel-table th { background-color: #e9e9e9; padding: 8px; text-align: left; border: 1px solid #ddd; font-size: 9pt; }
                .vessel-table td { padding: 8px; border: 1px solid #ddd; font-size: 9pt; }
                .indicator-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; }
                .indicator-item { background-color: #f8f9fa; padding: 10px; border-left: 3px solid #317ec6; }
                .indicator-label { font-weight: bold; color: #555; font-size: 9pt; }
                .indicator-value { font-size: 10pt; margin-top: 3px; }
                .note-item { margin-bottom: 5px; padding-left: 10px; border-left: 2px solid #ccc; font-size: 9pt; }
                .footer { background-color: #317ec6; color: white; padding: 8px; text-align: left; font-size: 9pt; margin-top: 20px; }
                .narrative-text { font-size: 10pt; line-height: 1.5; padding: 10px; background-color: #fafafa; border-radius: 4px; }
                .sub-section { margin-top: 15px; }
                .tickbox {font-family: "DejaVu Sans", sans-serif;}
            </style>
        </head>
        <body>
            <div class="header-section">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="20%">
                            <img width="90px" src="' . $base64Image . '" />
                        </td>
                        <td width="80%" valign="center">
                            <h1>' . __("STS Operational Intelligence Report", "castalynkmap") . '</h1>
                            <p style="font-size: 11px; color: #e0e0e0;">' . __("Report ID:", "castalynkmap") . ' ' . ($mother_vessel['event_ref_id'] ?? 'Not Available') . '<br>
                            ' . __("Generated:", "castalynkmap") . ' ' . $generated . ' UTC<br>
                            ' . __("Location: Nigerian Offshore Waters", "castalynkmap") . '</p>
                        </td>
                    </tr>
                </table> 
            </div>
            <div style="padding: 20px;">
                <!-- Event Summary -->
            <div class="section-title">1. Case Reference</div>
            <div class="content-box">
                <table width="100%" cellpadding="5" cellspacing="0">
                    <tr>
                        <td width="18%"><strong>Report ID:</strong></td>
                        <td width="35%">' . ($mother_vessel['event_ref_id'] ?? 'Not Available') . '</td>
                        <td width="12%"><strong>System:</strong></td>
                        <td width="35%">Coastalynk AIS Intelligence</td>
                    </tr>
                    <tr>
                        <td><strong>Observation Window:</strong></td>
                        <td>' . ($mother_vessel['start_date'] ?$mother_vessel['start_date'].' UTC': 'Not Available') .' - '. ($mother_vessel['end_date'] ?$mother_vessel['end_date'].' UTC': 'Ongoing') . '</td>
                        <td><strong>Generated:</strong></td>
                        <td>'.$generated.'</td>
                    </tr>
                </table>
            </div>
            <div class="section-title">2. Executive Summary</div>
            <div class="content-box">
                <div class="narrative-text">
                    Derived from AIS ingestion statistics.<br>
                    AIS messages analyzed: ' . ($first_daughter['data_points_analyzed'] ?? 'Not Available') . '<br>
                    Observation Window: ' . ($mother_vessel['start_date'] ?$mother_vessel['start_date'].' UTC': 'Not Available') .' - '. ($mother_vessel['end_date'] ?$mother_vessel['end_date'].' UTC': 'Ongoing') . '<br>
                    Signal Continuity example: ' . ($first_daughter['proximity_consistency'] ?? 'Not Available') . ' of expected AIS transmissions received during the observation window.
                </div> 
            </div>
            <!-- Vessel Details -->
            <div class="section-title">3. Vessel Identification</div>
            <div class="content-box">
                <!-- Mother Vessel -->
                <div class="sub-section">
                    <strong>Mother Vessel:</strong>
                    <table class="vessel-table">
                        <tr>
                            <th>Name</th>
                            <th>IMO</th>
                            <th>MMSI</th>
                            <th>Flag</th>
                            <th>Type</th>
                            <th>Gross Tonnage</th>
                        </tr>
                        <tr>
                            <td>' . ($mother_vessel['name'] ?? 'Not Available') . '</td>
                            <td>' . ($mother_vessel['imo'] ?? 'Not Available') . '</td>
                            <td>' . ($mother_vessel['mmsi'] ?? 'Not Available') . '</td>
                            <td>' . ($mother_vessel['country_iso'] ?? 'Not Available') . '</td>
                            <td>' . ($mother_vessel['type'] ?? 'Not Available') . ' / ' . ($mother_vessel['type_specific'] ?? 'Not Available') . '</td>
                            <td>' . ($mother_vessel['gross_tonnage'] ?? 'Not Available') . '</td>
                        </tr>
                    </table>
                </div>';

        // Daughter Vessels
        $transfer_status = '';
        $detection_confidence = '';
        if (!empty($daughter_vessels)) {
            $html .= '
                    <div class="sub-section">
                        <strong>Daughter Vessel(s):</strong>
                        <table class="vessel-table">
                            <tr>
                                <th>Name</th>
                                <th>IMO</th>
                                <th>MMSI</th>
                                <th>Flag</th>
                                <th>Type</th>
                                <th>Gross Tonnage</th>
                            </tr>
                        ';
            
            foreach ($daughter_vessels as $index => $daughter) {
                $html .= '
                        
                            <tr>
                                <td>' . ($daughter['name'] ?? 'Not Available') . '</td>
                                <td>' . ($daughter['imo'] ?? 'Not Available') . '</td>
                                <td>' . ($daughter['mmsi'] ?? 'Not Available') . '</td>
                                <td>' . ($daughter['country_iso'] ?? 'Not Available') . '</td>
                                <td>' . ($daughter['type'] ?? 'Not Available') . ' / ' . ($daughter['type_specific'] ?? 'Not Available') . '</td>
                                <td>' . ($daughter['gross_tonnage'] ?? 'Not Available') . '</td>
                            </tr>';
                if( !empty( $daughter['transfer_confidence'] ) ) {
                    $transfer_status = $daughter['transfer_confidence'];
                }

                if( !empty( $daughter['confidence_string'] ) ) {
                    $detection_confidence = $daughter['confidence_string'];
                }
                    
            }
            $html .= '</table></div>';
        }

        $html .= '
                    <p style="font-size: 9pt; color: #666; margin-top: 10px;">
                        <em>Note: Vessel roles (mother/daughter) are assigned based on system observation context only.</em>
                    </p>
                </div>
            <div class="section-title">4. Interaction Location + Map</div>
            <div class="content-box">
                <p>'.__("Primary Interaction Location", "castalynkmap").'</p>
                <table width="100%" cellpadding="5" cellspacing="0">
                    <tr>
                        <td width="25%"><strong>Latitude:</strong></td>
                        <td width="25%">' . ($mother_vessel['lat'] ?? 'Not Available') . '</td>
                        <td width="15%"><strong>Longitude:</strong></td>
                        <td width="35%">' . ( $mother_vessel['lon'] ?? 'Not Available' ) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Operational Zone:</strong></td>
                        <td colspan="2">' . ($mother_vessel['port'] ?? 'Not Available'). '</td>
                    </tr>
                </table>
                <p>'.__("Location represents the midpoint of the maximum proximity window between vessels.", "castalynkmap").'</p>';
        
                if( !empty($coastalynk_sts_popup_map_image) ) {
                    $html .= '<p><img width="640px" src="'.$coastalynk_sts_popup_map_image.'" /></p>';
                }
            $speed_profile = $this->getFleetSpeedMessage($mother_vessel, $daughter_vessels);
            
            $html .= '</div>
            <div class="section-title">5. Detection & Risk Profile</div>
            <div class="content-box">
                <p><strong>'.__("Indicators:", "castalynkmap").'</strong></p>
                <table width="100%" cellpadding="5" cellspacing="0">
                    <tr>
                        <td width="25%">'.__("STS Likelihood:", "castalynkmap").'</td>
                        <td width="25%">' . ($transfer_status ?? 'Not Available') . '</td>
                        <td width="25%">'.__("Detection Confidence:", "castalynkmap").'</td>
                        <td width="25%">' . ( $detection_confidence ?? 'Not Available' ) . '</td>
                    </tr>
                    <tr>
                        <td>'.__("Signal Continuity:", "castalynkmap").'</td>
                        <td>' . ($mother_vessel['ais_continuity'] ?? 'Not Available'). '</td>
                        <td>'.__("Proximity Signal:", "castalynkmap").'</td>
                        <td>' . ($first_daughter['proximity_signal'] ?? 'Not Available') . '</td>
                    </tr>
                    <tr>
                        <td>'.__("Proximity Consistency:", "castalynkmap").'</td>
                        <td>' . ($first_daughter['proximity_consistency'] ?? 'Not Available') . '</td>
                        <td>'.__("AIS Data Points:", "castalynkmap").'</td>
                        <td>' . ($first_daughter['data_points_analyzed'] ?? 'Not Available') . '</td>
                    </tr>
                    
                    <tr>
                        <td>'.__("Outcome Status:", "castalynkmap").'</td>
                        <td>' . ($mother_vessel['outcome_status'] ?? 'Not Available'). '</td>
                        <td>'.__("Transfer Status:", "castalynkmap").'</td>
                        <td>' . ($mother_vessel['transfer_status'] ?? 'Not Available') . '</td>
                    </tr>
                    <tr>
                        <td>'.__("Transfer Confidence:", "castalynkmap").'</td>
                        <td>' . ($mother_vessel['transfer_confidence'] ?? 'Not Available'). '</td>
                        <td>'.__("Transfer Status:", "castalynkmap").'</td>
                        <td>' . ($mother_vessel['transfer_status'] ?? 'Not Available') . '</td>
                    </tr>
                    <tr>
                        <td>'.__("Mother Vessel AIS:", "castalynkmap").'</td>
                        <td>' . ($mother_vessel['ais_continuity'] ?? 'Not Available') . ' signal</td>
                        <td>'.__("Event Percentage", "castalynkmap").'</td>
                        <td>' . (!empty($daughter_vessels) && $first_daughter['event_percentage'] ?? 'Not Available') . '%</td>
                    </tr>
                    <tr>
                        <td>'.__("Speed Profile:", "castalynkmap").'</td>
                        <td colspan="3">' . ($speed_profile ?? 'Not Available'). '</td>
                    </tr>
                    <tr>
                        <td>'.__("Operational Context:", "castalynkmap").'</td>
                        <td>' . ($mother_vessel['port'] ?? 'Not Available') . '</td>
                        <td>'.__("AIS Data Points:", "castalynkmap").'</td>
                        <td>' . ($first_daughter['data_points_analyzed'] ?? 'Not Available') . '</td>
                    </tr>
                    </table>
                <p><strong>'.__("Data Sources:", "castalynkmap").'</strong></p>
                <table width="100%" cellpadding="5" cellspacing="0">
                    <tr>
                        <td>STS Likelihood - derived from behavioural STS detection rules.</td>
                    </tr>
                    <tr>
                        <td>Detection Confidence - derived from proximity stability, duration overlap, and AIS signal reliability.</td>
                    </tr>
                    <tr>
                        <td>Signal Continuity - derived from behavioural STS detection rules.</td>
                    </tr>
                    
                </table>
            </div>

            <div class="section-title">6. Operational Interpretation</div>
            <div class="content-box">
                <div class="narrative-text">
                    ' . $event_narrative . '
                </div> 
                <p style="font-size: 9pt; color: #666; margin-top: 10px;">
                    <em>This narrative reflects system-observed AIS signals only and does not constitute confirmation of an STS operation or any compliance assessment.</em>
                </p>
            </div>
            <div class="section-title">7. Data Limitations</div>
            <div class="content-box">
                <div class="narrative-text">
                    ' . $event_narrative . '
                </div> 
                <p style="font-size: 9pt; color: #666; margin-top: 10px;">
                    <em>This narrative reflects system-observed AIS signals only and does not constitute confirmation of an STS operation or any compliance assessment.</em>
                </p>
            </div>
            <div class="section-title">8. Methodology & Metadata</div>
            <div class="content-box">
                <p>
                    This report is generated automatically from AIS vessel tracking data processed by the Coastalynk operational intelligence system.
                </p>';
                foreach ($system_notes as $note) {
                    $html .= '<div class="note-item">• ' . $note . '</div>';
                }

                $html .= '
                    <div style="margin-top: 15px; padding: 10px; background-color: #f0f7ff; border-radius: 4px; font-size: 9pt;">
                        <strong>Report Metadata:</strong><br>
                        Generated: ' . date('Y-m-d H:i:s') . '<br>
                        Event ID: ' . $event_id . '<br>
                        Report Version: v1.0.
                    </div>
            </div>
            <div class="section-title">6. Operational Risk Indicator</div>
            <div class="content-box">
                    <div class="indicator-grid">';
                    
        if (!empty($daughter_vessels)) {
            $first_daughter = $daughter_vessels[0];

            $html .= '
                        <div class="indicator-item">
                            <div class="indicator-label">Proximity Signal:</div>
                            <div class="indicator-value">' . ($first_daughter['proximity_signal'] ?? 'Not Available') . '</div>
                        </div>
                        <div class="indicator-item">
                            <div class="indicator-label">Proximity Consistency:</div>
                            <div class="indicator-value">' . ($first_daughter['proximity_consistency'] ?? 'Not Available') . '</div>
                        </div>
                        <div class="indicator-item">
                            <div class="indicator-label">Stationary Duration:</div>
                            <div class="indicator-value">' . ($first_daughter['stationary_duration_hours'] ?? 'Not Available') . ' hours</div>
                        </div>
                        <div class="indicator-item">
                            <div class="indicator-label">AIS Data Points:</div>
                            <div class="indicator-value">' . ($first_daughter['data_points_analyzed'] ?? 'Not Available') . ' analyzed</div>
                        </div>
                        <div class="indicator-item">
                            <div class="indicator-label">Operational Context:</div>
                            <div class="indicator-value">' . ($mother_vessel['port'] ?? 'Not Available') . '</div>
                        </div>
                         <div class="indicator-item">
                            <div class="indicator-label">Outcome Status:</div>
                            <div class="indicator-value">' . ($mother_vessel['outcome_status'] ?? 'Not Available') . '</div>
                        </div>
                         <div class="indicator-item">
                            <div class="indicator-label">Transfer Status:</div>
                            <div class="indicator-value">' . ($mother_vessel['transfer_status'] ?? 'Not Available') . '</div>
                        </div>
                        <div class="indicator-item">
                            <div class="indicator-label">Transfer Confidence:</div>
                            <div class="indicator-value">' . ($mother_vessel['transfer_confidence'] ?? 'Not Available') . '</div>
                        </div>
                        ';
        }

        $html .= '
                        <div class="indicator-item">
                            <div class="indicator-label">Mother Vessel AIS:</div>
                            <div class="indicator-value">' . ($mother_vessel['ais_continuity'] ?? 'Not Available') . ' signal</div>
                        </div>';

        if (!empty($daughter_vessels)) {
            $html .= '
                        <div class="indicator-item">
                            <div class="indicator-label">Event Percentage</div>
                            <div class="indicator-value">' . ($first_daughter['event_percentage'] ?? 'Not Available') . '%</div>
                        </div>
                        ';
        }

        

        $html .= '
                    </div>
                    <p style="font-size: 9pt; color: #666; margin-top: 10px;">
                        <em>Note: Indicators represent system-observed signals only. No compliance or intent conclusions are drawn.</em>
                    </p>
                </div>

            <div class="section-title">6. Interaction Behavior Pattern</div>
            <div class="content-box">
                    <div class="indicator-grid">
                    <div class="indicator-item">
                            <div class="indicator-label">Speed Profile:</div>
                            <div class="indicator-value">' . $speed_profile . '</div>
                        </div>
                    </div>
                </div><!-- Operational Timeline -->
            <div class="section-title">8. Operational Timeline</div>
            <div class="content-box">
                <div class="indicator-grid">';
                
    if (!empty($daughter_vessels)) {
        $first_daughter = $daughter_vessels[0];
        $html .= '
                    <div class="indicator-item">
                        <div class="indicator-label">Proximity Consistency</div>
                        <div class="indicator-value">' . ($first_daughter['proximity_consistency'] ?? 'Not Available') . '</div>
                    </div>
                    <div class="indicator-item">
                        <div class="indicator-label">Stationary Duration</div>
                        <div class="indicator-value">' . ($first_daughter['stationary_duration_hours'] ?? 'Not Available') . ' hours</div>
                    </div>
                    <div class="indicator-item">
                        <div class="indicator-label">AIS Data Points</div>
                        <div class="indicator-value">' . ($first_daughter['data_points_analyzed'] ?? 'Not Available') . ' analyzed</div>
                    </div>';
    }

    $html .= '
                    <div class="indicator-item">
                        <div class="indicator-label">Mother Vessel AIS</div>
                        <div class="indicator-value">' . ($mother_vessel['ais_continuity'] ?? 'Not Available') . ' signal</div>
                    </div>';

    if (!empty($daughter_vessels)) {
        $html .= '
                    <div class="indicator-item">
                        <div class="indicator-label">Event Percentage</div>
                        <div class="indicator-value">' . ($first_daughter['event_percentage'] ?? 'Not Available') . '%</div>
                    </div>
                    ';
    }

    $html .= '
                </div>
                <p style="font-size: 9pt; color: #666; margin-top: 10px;">
                    <em>Note: Indicators represent system-observed signals only. No compliance or intent conclusions are drawn.</em>
                </p>
            </div>
            <!-- Event Narrative -->
            <div class="section-title">9. Operational Assessment</div>
            <div class="content-box">
                <div class="narrative-text">
                    ' . $event_narrative . '
                </div> 
                <p style="font-size: 9pt; color: #666; margin-top: 10px;">
                    <em>This narrative reflects system-observed AIS signals only and does not constitute confirmation of an STS operation or any compliance assessment.</em>
                </p>
            </div>
            
            <div class="section-title">11. Regulatory Relevance</div>
            <div class="content-box">
                <div class="narrative-text">
                    Monitoring offshore STS operations<br>
                    Verification of cargo or bunkering activity<br>
                    Safety and environmental oversight<br>
                    Operational pattern analysis<br>
                    Coordination with maritime enforcement agencies<br>
                </div> 
            </div>
            <div class="section-title">12. Evidence Included</div>
            <div class="content-box">
                <div class="narrative-text tickbox">
                    ✓ AIS track visualization<br>
                    ✓ Proximity analysis<br>
                    ✓ Timeline reconstruction<br>
                    ✓ Vessel identity verification<br>
                </div> 
            </div>
            <div class="section-title">13. Methodology & Data Basis</div>
            <div class="content-box">
                <div class="narrative-text tickbox">
                    <p>
                        This report is generated automatically from AIS vessel tracking data processed by the Coastalynk operational intelligence system.
                    </p>
                    <p>
                        The system analyzes vessel proximity, movement behavior, AIS signal continuity, and duration of interaction to identify patterns consistent with offshore ship-to-ship activity.
                    </p>
                    <p>
                        All timestamps are expressed in Coordinated Universal Time (UTC).
                    </p>
                    <p>
                        All geographic coordinates use the WGS‑84 standard.
                    </p>
                    <p>
                        AIS signal availability and transmission quality may influence detection confidence and continuity metrics.
                    </p>
                </div> 
            </div>';
     

        $html .= '
                    <p style="font-size: 9pt; color: #666; margin-top: 10px;">
                        <em>Note: Vessel roles (mother/daughter) are assigned based on system observation context only.</em>
                    </p>
                </div>

                <!-- System Notes -->
                <div class="section-title">14. SYSTEM NOTES (AUTO-GENERATED)</div>
                <div class="content-box">';
                
        foreach ($system_notes as $note) {
            $html .= '<div class="note-item">• ' . $note . '</div>';
        }

        $html .= '
                    <div style="margin-top: 15px; padding: 10px; background-color: #f0f7ff; border-radius: 4px; font-size: 9pt;">
                        <strong>Report Metadata:</strong><br>
                        Generated: ' . date('Y-m-d H:i:s') . '<br>
                        Event ID: ' . $event_id . '<br>
                        Report Version: v1.0.
                    </div>
                </div>
            

            <div class="footer">
                <strong>Rules Version: v1.0</strong><br>
                <strong>STS Likelihood Rules v1.0</strong><br>
                <strong>STS Data Quality Rules v1.0</strong><br>
                Generated by Coastalynk AIS Intelligence System<br>
                This report provides system-observed operational intelligence derived from AIS analysis.<br>
                Compliance determination remains the responsibility of the relevant regulatory authority.
            </div>
        </body>
        </html>';

        // Load HTML content
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Output the PDF
        $dompdf->stream("coastalynk_sts_report_" . $event_id . ".pdf", array("Attachment" => false));
        // if (!isset($_POST['coastalynk_sts_popup_history_load_nonce']) || !wp_verify_nonce($_POST['coastalynk_sts_popup_history_load_nonce'], 'coastalynk_sts_popup_history_load')) {
            
        //     echo $error_url = add_query_arg('form_error', 'Security verification failed', wp_get_referer());exit;
        //     wp_redirect($error_url);
        //     exit;
        // }

        // $event_id = intval( $_POST['event_id'] );
        // if( $event_id <= 0 ) {
        //     $error_url = add_query_arg('form_error', 'Event ID is required', wp_get_referer());
        //     wp_redirect($error_url);
        //     exit;
        // }

        // global $wpdb;

            

        // $event_table_mother = $wpdb->prefix . 'coastalynk_sts_events';
        // $event_table_daughter = $wpdb->prefix . 'coastalynk_sts_event_detail';
        // //$vessle_recs = $wpdb->get_results( $wpdb->prepare( "SELECT e.`id`,e.`uuid` as vessel1_uuid, e.`name` as vessel1_name, e.`mmsi` as vessel1_mmsi, e.`imo` as vessel1_imo, e.`country_iso` as vessel1_country_iso, e.`type` as vessel1_type, e.`type_specific` as vessel1_type_specific, e.`lat` as vessel1_lat, e.`lon` as vessel1_lon, e.`speed` as vessel1_speed, e.`navigation_status` as vessel1_navigation_status, e.`draught` as vessel1_draught, e.`completed_draught` as vessel1_completed_draught, e.`last_position_UTC` as vessel1_last_position_UTC, e.`ais_signal` as vessel1_signal,e.`deadweight` as vessel1_deadweight,e.`gross_tonnage` as vessel1_gross_tonnage,e.`port`,e.`port_id`, e.`distance`,e.`event_ref_id`, e.`zone_type`,e.`zone_ship`, e.`zone_terminal_name`,e.`start_date`,e.`end_date`,e.`status`,e.`is_email_sent`,e.`is_complete`,e.`is_disappeared`, e.`last_updated`, d.`event_id`,d.`uuid` as vessel2_uuid,d.`name` as vessel2_name,d.`mmsi` as vessel2_mmsi,d.`imo` as vessel2_imo,d.`country_iso` as vessel2_country_iso,d.`type` as vessel2_type,d.`type_specific` as vessel2_type_specific,d.`lat` as vessel2_lat,d.`lon` as vessel2_lon,d.`speed` as vessel2_speed,d.`navigation_status` as vessel2_navigation_status,d.`draught` as vessel2_draught,d.`completed_draught` as vessel2_completed_draught,d.`last_position_UTC` as vessel2_last_position_UTC,d.`deadweight` as vessel2_deadweight,d.`gross_tonnage` as vessel2_gross_tonnage,d.`draught_change`,d.`ais_signal` as vessel2_signal,d.`end_date` as vessel2_end_date, d.`distance`,d.`event_percentage`,d.`cargo_category_type`,d.`risk_level`,d.`stationary_duration_hours`,d.`proximity_consistency`,d.`data_points_analyzed`,d.`is_disappeared`,d.`operationmode`,d.`is_complete` as vessel2_is_complete,d.`last_updated` as vessel2_last_updated,d.`status` as vessel2_status,d.`outcome_status`,d.`flag_status` from ".$event_table_mother." as e inner join ".$event_table_daughter." as d on(e.id=d.event_id) where e.id = %d", $event_id), ARRAY_A );
        
        // $vessle_recs = $wpdb->get_results( $wpdb->prepare( "SELECT * from ".$event_table_mother." where id = %d", $event_id), ARRAY_A );
        
        // require( CSM_LIB_DIR.'vendor/autoload.php' );
        // $options = new Dompdf\Options();
        // $options->set('isRemoteEnabled', true);
        // $options->set('isHtml5ParserEnabled', true);
        // $options->set('chroot', __DIR__); // Set base directory
        // $options->set('defaultFont', 'Helvetica');

        // // Create PDF instance
        // $dompdf = new Dompdf\Dompdf();

        // //$imageData = base64_encode(file_get_contents(get_template_directory_uri().'/assets/images/main_logo-footer.png'));
        // $imageData = base64_encode(file_get_contents('https://coastalynk.com/staging/wp-content/themes/coastalynk/assets/images/main_logo-footer.png'));
        // $base64Image = 'data:image/jpeg;base64,' . $imageData;

        // // HTML content
        // $html = '
        // <!DOCTYPE html>
        // <html>
        // <head>
        //     <style>
        //         body { font-family: Arial, sans-serif; }
        //         .currency{font-family: "DejaVu Sans Mono", monospace;}
        //         h1 { color: #333; }
        //         .header-td { background-color: #ddd;border: 1px solid #ddd;padding: 8px; }
        //         .data-td { background-color: #eee;border: 1px solid #ddd;padding: 8px; }
        //         .content { margin: 0px; }
        //         footer{ background-color:#317ec6; color:white; padding:5px;}
        //     </style>
        // </head>
        // <body>
        //     <div class="content">
        //         <table width="100%" cellpadding="5px" cellspacing="0">
                    
                
        //             <tr style="background-color:#317ec6;">
        //                 <td width="35%" style="background-color:#317ec6;"><img width=""171px src="'.$base64Image.'" /></td>
        //                 <td width="65%" valign="" style="background-color:#317ec6;color:white;"><h2>'.__( "Coastalynk STS Report", "castalynkmap" ).'</h2><span style="font-size:13px; color: white;">'.__( "Digital Maritime Intelligence Platform", "castalynkmap" ).'</span></td>
        //             </tr>
        //             <tr>
        //                 <td>&nbsp;</td>
        //                 <td>&nbsp;</td>
        //             </tr>
                    
        //             ';
                    
        //             foreach( $vessle_recs as $record ) {
        //                 $html .= '<tr><td colspan="2"><table width="100%" cellpadding="5px" cellspacing="0">
        //                 <tr><td class="data-td" colspan="6">'.__( "Mother Vessel:", "castalynkmap" ).'</td></tr>
        //                 <tr>
        //                     <td class="header-td">'.__( "Event ID", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['event_ref_id'].'</td>
        //                     <td class="header-td">'.__( "Start Date", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['start_date'].'</td>
        //                     <td class="header-td">'.__( "End Date", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['end_date'].'</td>
        //                 </tr>
        //                 <tr>
        //                     <td class="header-td">'.__( "Name", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['name'].'</td>
        //                     <td class="header-td">'.__( "MMSI", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['mmsi'].'</td>
        //                     <td class="header-td">'.__( "IMO", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['imo'].'</td>
        //                 </tr>
        //                 <tr>
        //                     <td class="header-td">'.__( "Country", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['country_iso'].'</td>
        //                     <td class="header-td">'.__( "Type", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['type'].'</td>
        //                     <td class="header-td">'.__( "Sub. Type", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['type_specific'].'</td>
        //                 </tr>
        //                 <tr>
        //                     <td class="header-td">'.__( "Lattitude", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['lat'].'</td>
        //                     <td class="header-td">'.__( "Longitude", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['lon'].'</td>
        //                     <td class="header-td">'.__( "Speed", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['speed'].'</td>
        //                 </tr>
        //                 <tr>
        //                     <td class="header-td">'.__( "Nav. Status", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['navigation_status'].'</td>
        //                     <td class="header-td">'.__( "Before Draught", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['draught'].'</td>
        //                     <td class="header-td"></td>
        //                     <td class="data-td"></td>
        //                 </tr>
        //                 <tr>
        //                     <td class="header-td">'.__( "Signal", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['ais_signal'].'</td>
        //                     <td class="header-td">'.__( "Dead Weight", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['deadweight'].'</td>
        //                     <td class="header-td">'.__( "Tonnage", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['gross_tonnage'].'</td>
        //                 </tr>
        //                 <tr>
        //                     <td class="header-td">'.__( "Port", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['zone_terminal_name'].'</td>
        //                     <td class="header-td">'.__( "Distance", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['distance'].'</td>
        //                     <td class="header-td">'.__( "Zone Type", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['zone_type'].'</td>
        //                 </tr>
        //                 <tr>
        //                     <td class="header-td">'.__( "Status", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['status'].'</td>
        //                     <td class="header-td">'.__( "Last Updated", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['last_updated'].'</td>
        //                     <td class="header-td">'.__( "Last Position", "castalynkmap" ).'</td>
        //                     <td class="data-td">'.$record['last_position_UTC'].'</td>
                            
        //                 </tr>';
        //                 $html .= '</table></td> </tr>';
        //                 $daughter_recs = $wpdb->get_results( $wpdb->prepare( "SELECT * from ".$event_table_daughter." where event_id = %d", $record['id']), ARRAY_A );
        //                 $index = 1;
        //                 foreach( $daughter_recs as $daughter ) {

        //                     $html .= '<tr><td><table width="100%" cellpadding="5px" cellspacing="0"><tr><td class="data-td" colspan="6">'.__( "Daughter Vessel ", "castalynkmap" ).($index++).':</td></tr>
        //                     <tr>
        //                         <td class="header-td">'.__( "Name", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['name'].'</td>
        //                         <td class="header-td">'.__( "MMSI", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['mmsi'].'</td>
        //                         <td class="header-td">'.__( "IMP", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['imo'].'</td>
        //                     </tr>
        //                     <tr>
        //                         <td class="header-td">'.__( "Country", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['country_iso'].'</td>
        //                         <td class="header-td">'.__( "Type", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['type'].'</td>
        //                         <td class="header-td">'.__( "Sub. Type", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['type_specific'].'</td>
        //                     </tr>
        //                     <tr>
        //                         <td class="header-td">'.__( "Lattitude", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['lat'].'</td>
        //                         <td class="header-td">'.__( "Longitude", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['lon'].'</td>
        //                         <td class="header-td">'.__( "Speed", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['speed'].'</td>
        //                     </tr>
        //                     <tr>
        //                         <td class="header-td">'.__( "Nav. Status", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['navigation_status'].'</td>
        //                         <td class="header-td">'.__( "Before Draught", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['draught'].'</td>
        //                         <td class="header-td"></td>
        //                         <td class="data-td"></td>
        //                     </tr>
        //                     <tr>
        //                         <td class="header-td">'.__( "Joining Date", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['joining_date'].'</td>
        //                         <td class="header-td">'.__( "Locking Time", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['lock_time'].'</td>
        //                         <td class="header-td">'.__( "End Date", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['end_date'].'</td>
        //                     </tr>
        //                     <tr>
        //                         <td class="header-td">'.__( "Signal", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['ais_signal'].'</td>
        //                         <td class="header-td">'.__( "Dead Weight", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['deadweight'].'</td>
        //                         <td class="header-td">'.__( "Tonnage", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['gross_tonnage'].'</td>
        //                     </tr>
        //                     <tr>
        //                         <td class="header-td">'.__( "Distance", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['distance'].'</td>
        //                         <td class="header-td">'.__( "Stationary(hrs)", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['stationary_duration_hours'].'</td>
        //                         <td class="header-td">'.__( "Proximity", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['proximity_consistency'].'</td>
        //                     </tr>
        //                     <tr>    
        //                         <td class="header-td">'.__( "Percentage", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['event_percentage'].'</td>
        //                         <td class="header-td">'.__( "Cargo Type", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['cargo_category_type'].'</td>
        //                         <td class="header-td">'.__( "Risk Status", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['risk_level'].'</td>
        //                     </tr>
        //                     <tr>
        //                         <td class="header-td">'.__( "Data Points", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['data_points_analyzed'].'</td>
        //                         <td class="header-td">'.__( "Operation Mode", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['operationmode'].'</td>
        //                         <td class="header-td">'.__( "Last Updated", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['last_updated'].'</td>
        //                     </tr> 
        //                     <tr>
        //                         <td class="header-td">'.__( "Last Position", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['last_position_UTC'].'</td>
        //                         <td class="header-td">'.__( "Outcome Classification", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['outcome_status'].'</td>
        //                         <td class="header-td">'.__( "Review / Workflow Flags", "castalynkmap" ).'</td>
        //                         <td class="data-td">'.$daughter['flag_status'].'</td>
        //                     </tr>                                            
        //                     <tr>
        //                         <td class="header-td">'.__( "Last Updated", "castalynkmap" ).'</td>
        //                         <td class="data-td" colspan="5">'.$daughter['last_updated'].'</td>
        //                     </tr>
        //                     <tr>
        //                         <td class="header-td">'.__( "Remarks", "castalynkmap" ).'</td>
        //                         <td class="data-td" colspan="5">'.$daughter['remarks'].'</td>
        //                     </tr>
        //                     <tr>
        //                         <td colspan="6">&nbsp;</td>
        //                     </tr>';
        //                     $html .= '</table></td> </tr>';
        //                 }
        //             }    
                        
        //     $html .= '</table>
        //     </div>
        //     <footer>Generated by Coastalynk STS - Pilot Version.</footer>
        // </body>
        // </html>';

        // // Load HTML content
        // $dompdf->loadHtml($html);

        // // Set paper size and orientation
        // $dompdf->setPaper('A4', 'portrait');

        // // Render PDF
        // $dompdf->render();

        // // Output the PDF
        // $dompdf->stream("document.pdf", array("Attachment" => false));

        

        exit;
    }
    /**
     * enque dashboard.
     */
    function coastalynk_sts_history_load_action_ctrl_pdf_callback( ) {
        ini_set('memory_limit', '-1');
        global $wpdb;
        // print_r($_POST);
        // ;
        // if (!isset($_POST['coastalynk_sbm_history_load_nonce']) || !wp_verify_nonce($_POST['coastalynk_sbm_history_load_nonce'], 'coastalynk_sbm_history_load')) {
            
        //     echo $error_url = add_query_arg('form_error', 'Security verification failed', wp_get_referer());exit;
        //     wp_redirect($error_url);
        //     exit;
        // }
        $port       = sanitize_text_field( $_REQUEST['caostalynk_history_ddl_ports'] );
        $date_range = sanitize_text_field( $_REQUEST['caostalynk_sts_history_range'] );
        if( ! empty( $date_range ) ) {
            $where = '';
            if( ! empty( $port ) ) {
                $where = " and e.port = '".$port."' ";
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

            $event_table_mother = $wpdb->prefix . 'coastalynk_sts_events';
            $event_table_daughter = $wpdb->prefix . 'coastalynk_sts_event_detail';
            $vessle_recs = $wpdb->get_results( $wpdb->prepare( "SELECT e.`id`,e.`uuid` as vessel1_uuid, e.`name` as vessel1_name, e.`mmsi` as vessel1_mmsi, e.`imo` as vessel1_imo, e.`country_iso` as vessel1_country_iso, e.`type` as vessel1_type, e.`type_specific` as vessel1_type_specific, e.`lat` as vessel1_lat, e.`lon` as vessel1_lon, e.`speed` as vessel1_speed, e.`navigation_status` as vessel1_navigation_status, e.`draught` as vessel1_draught, e.`completed_draught` as vessel1_completed_draught, e.`last_position_UTC` as vessel1_last_position_UTC, e.`ais_signal` as vessel1_signal,e.`deadweight` as vessel1_deadweight,e.`gross_tonnage` as vessel1_gross_tonnage,e.`port`,e.`port_id`, e.`distance`,e.`event_ref_id`, e.`zone_type`,e.`zone_ship`, e.`zone_terminal_name`,e.`start_date`,e.`end_date`,e.`status`,e.`is_email_sent`,e.`is_complete`,e.`is_disappeared`, e.`last_updated`, d.`event_id`,d.`uuid` as vessel2_uuid,d.`name` as vessel2_name,d.`mmsi` as vessel2_mmsi,d.`imo` as vessel2_imo,d.`country_iso` as vessel2_country_iso,d.`type` as vessel2_type,d.`type_specific` as vessel2_type_specific,d.`lat` as vessel2_lat,d.`lon` as vessel2_lon,d.`speed` as vessel2_speed,d.`navigation_status` as vessel2_navigation_status,d.`draught` as vessel2_draught,d.`completed_draught` as vessel2_completed_draught,d.`last_position_UTC` as vessel2_last_position_UTC,d.`deadweight` as vessel2_deadweight,d.`gross_tonnage` as vessel2_gross_tonnage,d.`draught_change`,d.`ais_signal` as vessel2_signal,d.`end_date` as vessel2_end_date, d.`distance`,d.`event_percentage`,d.`cargo_category_type`,d.`risk_level`,d.`stationary_duration_hours`,d.`proximity_consistency`,d.`data_points_analyzed`,d.`is_disappeared`,d.`operationmode`,d.`is_complete` as vessel2_is_complete,d.`last_updated` as vessel2_last_updated,d.`status` as vessel2_status,d.`outcome_status`,d.`flag_status` from ".$event_table_mother." as e inner join ".$event_table_daughter." as d on(e.id=d.event_id) where e.last_updated BETWEEN %s AND %s", $start_date, $end_date).$where, ARRAY_A );

            require( CSM_LIB_DIR.'vendor/autoload.php' );
            $options = new Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('chroot', __DIR__); // Set base directory
            $options->set('defaultFont', 'Helvetica');

            // Create PDF instance
            $dompdf = new Dompdf\Dompdf();

            //$imageData = base64_encode(file_get_contents(get_template_directory_uri().'/assets/images/main_logo-footer.png'));
            $imageData = base64_encode(file_get_contents('https://coastalynk.com/staging/wp-content/themes/coastalynk/assets/images/main_logo-footer.png'));
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
                    .content { margin: 0px; }
                    footer{ background-color:#317ec6; color:white; padding:5px;}
                </style>
            </head>
            <body>
                <div class="content">
                    <table width="100%" cellpadding="5px" cellspacing="0">
                        
                    
                        <tr style="background-color:#317ec6;">
                            <td width="35%" style="background-color:#317ec6;"><img width=""171px src="'.$base64Image.'" /></td>
                            <td width="65%" valign="" style="background-color:#317ec6;color:white;"><h2>'.__( "Coastalynk STS Report", "castalynkmap" ).'</h2><span style="font-size:13px; color: white;">'.__( "Digital Maritime Intelligence Platform", "castalynkmap" ).'</span></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        
                        ';
                        
                        foreach( $vessle_recs as $record ) {
                            $html .= '<tr><td colspan="2"><table width="100%" cellpadding="5px" cellspacing="0">
                            <tr><td class="data-td" colspan="6">'.__( "Vessel 1:", "castalynkmap" ).'</td></tr>
                            <tr>
                                <td class="header-td">'.__( "Name", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel1_name'].'</td>
                                <td class="header-td">'.__( "MMSI", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel1_mmsi'].'</td>
                                <td class="header-td">'.__( "IMP", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel1_imo'].'</td>
                            </tr>
                            <tr>
                                <td class="header-td">'.__( "Country", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel1_country_iso'].'</td>
                                <td class="header-td">'.__( "Type", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel1_type'].'</td>
                                <td class="header-td">'.__( "Sub. Type", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel1_type_specific'].'</td>
                            </tr>
                            <tr>
                                <td class="header-td">'.__( "Lattitude", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel1_lat'].'</td>
                                <td class="header-td">'.__( "Longitude", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel1_lon'].'</td>
                                <td class="header-td">'.__( "Speed", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel1_speed'].'</td>
                            </tr>
                            <tr>
                                <td class="header-td">'.__( "Nav. Status", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel1_navigation_status'].'</td>
                                <td class="header-td">'.__( "Before Draught", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel1_draught'].'</td>
                                <td class="header-td">'.__( "Last Position", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel1_last_position_UTC'].'</td>
                            </tr>
                            <tr><td class="data-td" colspan="6">'.__( "Vessel 2:", "castalynkmap" ).'</td></tr>
                            <tr>
                                <td class="header-td">'.__( "Name", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel2_name'].'</td>
                                <td class="header-td">'.__( "MMSI", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel2_mmsi'].'</td>
                                <td class="header-td">'.__( "IMP", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel2_imo'].'</td>
                            </tr>
                            <tr>
                                <td class="header-td">'.__( "Country", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel2_country_iso'].'</td>
                                <td class="header-td">'.__( "Type", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel2_type'].'</td>
                                <td class="header-td">'.__( "Sub. Type", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel2_type_specific'].'</td>
                            </tr>
                            <tr>
                                <td class="header-td">'.__( "Lattitude", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel2_lat'].'</td>
                                <td class="header-td">'.__( "Longitude", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel2_lon'].'</td>
                                <td class="header-td">'.__( "Speed", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel2_speed'].'</td>
                            </tr>
                            <tr>
                                <td class="header-td">'.__( "Nav. Status", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel2_navigation_status'].'</td>
                                <td class="header-td">'.__( "Before Draught", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel2_draught'].'</td>
                                <td class="header-td">'.__( "Last Position", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['vessel2_last_position_UTC'].'</td>
                            </tr>
                            
                            <tr><td class="data-td" colspan="6">'.__( "General detail:", "castalynkmap" ).'</td></tr>
                            <tr>
                                <td class="header-td">'.__( "Reference ID", "castalynkmap" ).'</td>
                                <td class="data-td" colspan="3">'.$record['event_ref_id'].'</td>
                                <td class="header-td">'.__( "Last Updated", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['last_updated'].'</td>
                            </tr>
                            <tr>
                                <td class="header-td">'.__( "Zone Name", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['zone_terminal_name'].'</td>
                                <td class="header-td">'.__( "Start Date", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['start_date'].'</td>
                                <td class="header-td">'.__( "End Date", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['end_date'].'</td>
                            </tr>
                            <tr>    
                                <td class="header-td">'.__( "Percentage", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['event_percentage'].'</td>
                                <td class="header-td">'.__( "Cargo Type", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['cargo_category_type'].'</td>
                                <td class="header-td">'.__( "Risk Status", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['risk_level'].'</td>
                            </tr>
                            <tr>
                                <td class="header-td">'.__( "Distance", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['distance'].'</td>
                                <td class="header-td">'.__( "Stationary(hrs)", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['stationary_duration_hours'].'</td>
                                <td class="header-td">'.__( "Proximity", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['proximity_consistency'].'</td>
                            </tr>
                            <tr>
                                <td class="header-td">'.__( "Data Points", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['data_points_analyzed'].'</td>
                                <td class="header-td">'.__( "Operation Mode", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['operationmode'].'</td>
                                <td class="header-td">'.__( "Status", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['status'].'</td>
                            </tr>
                            <tr>
                                <td class="header-td">'.__( "Distance", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['distance'].'</td>
                                <td class="header-td">'.__( "Outcome Classification", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['outcome_status'].'</td>
                                <td class="header-td">'.__( "Review / Workflow Flags", "castalynkmap" ).'</td>
                                <td class="data-td">'.$record['flag_status'].'</td>
                            
                            </tr>
                            <tr>
                                <td colspan="6">&nbsp;</td>
                            </tr>
                            </table></td> </tr>
                            ';
                        }
                   
            $html .= '</table>
                </div>
                <footer>Generated by Coastalynk STS - Pilot Version.</footer>
            </body>
            </html>';
// echo $html;
// exit;
            // Load HTML content
            $dompdf->loadHtml($html);

            // Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render PDF
            $dompdf->render();

            // Output the PDF
            $dompdf->stream("document.pdf", array("Attachment" => false));
        }
        

        exit;
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
    function coastalynk_sts_history_load_action_ctrl_csv_callback( ) {
        
        global $wpdb;
        if (!isset($_POST['coastalynk_sts_history_load_nonce']) || ! wp_verify_nonce($_POST['coastalynk_sts_history_load_nonce'], 'coastalynk_sts_history_load')) {
            $error_url = add_query_arg('form_error', 'Security verification failed', wp_get_referer());
            wp_redirect($error_url);
            exit;
        }
        
        $event_table_mother = $wpdb->prefix . 'coastalynk_sts_events';
        $event_table_daughter = $wpdb->prefix . 'coastalynk_sts_event_detail';

        $port       = sanitize_text_field( $_REQUEST['caostalynk_history_ddl_ports'] );
        $date_range = sanitize_text_field( $_REQUEST['caostalynk_sts_history_range'] );
        if( ! empty( $date_range ) ) {
            $where = '';
            if( ! empty( $port ) ) {
                $where = " and e.port = '".$port."' ";
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

            $vessle_recs = $wpdb->get_results( $wpdb->prepare( "SELECT e.`id`,e.`uuid` as vessel1_uuid, e.`name` as vessel1_name, e.`mmsi` as vessel1_mmsi, e.`imo` as vessel1_imo, e.`country_iso` as vessel1_country_iso, e.`type` as vessel1_type, e.`type_specific` as vessel1_type_specific, e.`lat` as vessel1_lat, e.`lon` as vessel1_lon, e.`speed` as vessel1_speed, e.`navigation_status` as vessel1_navigation_status, e.`draught` as vessel1_draught, e.`completed_draught` as vessel1_completed_draught, e.`last_position_UTC` as vessel1_last_position_UTC, e.`ais_signal` as vessel1_signal,e.`deadweight` as vessel1_deadweight,e.`gross_tonnage` as vessel1_gross_tonnage,e.`port`,e.`port_id`, e.`distance`,e.`event_ref_id`, e.`zone_type`,e.`zone_ship`, e.`zone_terminal_name`,e.`start_date`,e.`end_date`,e.`status`,e.`is_email_sent`,e.`is_complete`,e.`is_disappeared`, e.`last_updated`, d.`event_id`,d.`uuid` as vessel2_uuid,d.`name` as vessel2_name,d.`mmsi` as vessel2_mmsi,d.`imo` as vessel2_imo,d.`country_iso` as vessel2_country_iso,d.`type` as vessel2_type,d.`type_specific` as vessel2_type_specific,d.`lat` as vessel2_lat,d.`lon` as vessel2_lon,d.`speed` as vessel2_speed,d.`navigation_status` as vessel2_navigation_status,d.`draught` as vessel2_draught,d.`completed_draught` as vessel2_completed_draught,d.`last_position_UTC` as vessel2_last_position_UTC,d.`deadweight` as vessel2_deadweight,d.`gross_tonnage` as vessel2_gross_tonnage,d.`draught_change`,d.`ais_signal` as vessel2_signal,d.`end_date` as vessel2_end_date, d.`distance`,d.`event_percentage`,d.`cargo_category_type`,d.`risk_level`,d.`stationary_duration_hours`,d.`proximity_consistency`,d.`data_points_analyzed`,d.`is_disappeared`,d.`operationmode`,d.`is_complete` as vessel2_is_complete,d.`last_updated` as vessel2_last_updated,d.`status` as vessel2_status,d.`outcome_status`,d.`flag_status` from ".$event_table_mother." as e inner join ".$event_table_daughter." as d on(e.id=d.event_id) where e.last_updated BETWEEN %s AND %s", $start_date, $end_date).$where, ARRAY_A );
            $fp = fopen('php://output', 'w'); 
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="sts.csv"');
            header('Pragma: no-cache');    
            header('Expires: 0');
            $headers = ['id','vessel1_uuid', 'vessel1_name', 'vessel1_mmsi', 'vessel1_imo', 'vessel1_country_iso', 'vessel1_type', 'vessel1_type_specific', 'vessel1_lat', 'vessel1_lon', 'vessel1_speed', 'vessel1_navigation_status', 'vessel1_draught', 'vessel1_completed_draught', 'vessel1_last_position_UTC', 'vessel1_signal','vessel1_deadweight','vessel1_gross_tonnage','port','port_id', 'distance','event_ref_id', 'zone_type','zone_ship', 'zone_terminal_name','start_date','end_date','status','is_email_sent','is_complete','is_disappeared', 'last_updated', 'event_id','vessel2_uuid','vessel2_name','vessel2_mmsi','vessel2_imo','vessel2_country_iso','vessel2_type','vessel2_type_specific','vessel2_lat','vessel2_lon','vessel2_speed','vessel2_navigation_status','vessel2_draught','vessel2_completed_draught','vessel2_last_position_UTC','vessel2_deadweight','vessel2_gross_tonnage','draught_change','vessel2_signal','vessel2_end_date', 'distance','event_percentage','cargo_category_type','risk_level','stationary_duration_hours', 'proximity_consistency','data_points_analyzed','is_disappeared','operationmode','vessel2_is_complete','vessel2_last_updated','vessel2_status','outcome_status','flag_status'];
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
        //echo get_template_directory_uri().'/assets/images/main_logo-footer.png';
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
        if (!empty($data) && isset( $data->data )) {
            foreach( $data->data as $dat ) {
                if( $selected_uuid == $dat->uuid ) {
                    if( intval( $dat->gross_tonnage ) > 0 ) {
                        echo number_format($dat->gross_tonnage, 0).' '.__( "GT", "castalynkmap" );
                    } else {
                        echo __( "N/A", "castalynkmap" );
                    }
                    
                    exit;
                } 
            }
        }
        echo __( "N/A", "castalynkmap" );
        exit;
    }
    
}

/**
 * @return bool
 */
return Coastalynk_Sea_Vessel_Map_Front::instance();