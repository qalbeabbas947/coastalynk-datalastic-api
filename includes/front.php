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
            $port_name = 'Apapa';
        }  

        ob_start();
        $types = [ "Cargo", "Tanker", "Passenger", "Tug", "Pilot", "Dredger", "Fishing", "Law Enforcement", "Other" ];
        ?>
            <div class="section-title d-flex justify-content-between mb-0 leftalign">
                <h3><?php echo  __( ucwords( $port_name ) . ' Port Congestion', "castalynkmap" );?></h3>               
            </div>
        <?php
        $updated_at = $wpdb->get_var( "select updated_at from ".$wpdb->prefix."port_congestion limit 1" );
        ?>
            <div class="coastalynk-stat-item-wrapper">
        <?php
        foreach( $types as $type  ) {
            $total = $wpdb->get_var( "select sum(total) as total from ".$wpdb->prefix."port_congestion where port like '%" . $wpdb->esc_like( $port_name ) . "%'  and vessel_type like '%" . $wpdb->esc_like( $type ) . "%'" );
            if( intval($total) > 0 ) {
                ?>
                    <div class="stat-item">
                        <div class="stat-label"><?php _e( ucwords( $type ), "castalynkmap" );?></div>
                        <div class="stat-value" id="total-vessels"><?php echo $total;?> <?php _e( "vessel(s)", "castalynkmap" );?></div>
                    </div>
                <?php
            }
        }
        ?>
            </div>
            <div class="coastalynk-date-updated">
                <div class="stat-label"><?php _e( "Updated Congestion Data", "castalynkmap" );?></div>
                <div class="stat-value" id="total-vessels"><?php echo $updated_at;?></div>
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
    function coastalynk_retrieve_tonnage() {
        $selected_uuid = isset( $_REQUEST['selected_uuid']  ) ? sanitize_text_field( $_REQUEST['selected_uuid'] ): "";
        $selected_name = isset( $_REQUEST['selected_name']  ) ? sanitize_text_field( $_REQUEST['selected_name'] ): "";

        if ( ! check_ajax_referer( 'coastalynk_secure_ajax_nonce', 'nonce', false ) ) {
            wp_send_json_error( __( 'Security nonce check failed. Please refresh the page.', "castalynkmap" ) );
            wp_die();
        }

        $apiKey = '15df4420-d28b-4b26-9f01-13cca621d55e'; // Replace with your actual API key.
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