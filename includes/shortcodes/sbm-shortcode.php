<?php
/**
 * STS shortcode page
 *
 * Do not allow directly accessing this file.
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Coastalynk_SBM_Shortcode
 */
class Coastalynk_SBM_Shortcode {

    private static $instance = null;

    /**
     * @since 1.0
     * @return $this
     */
    public static function instance() {

        if ( is_null( self::$instance ) && ! ( self::$instance instanceof Coastalynk_SBM_Shortcode ) ) {

            self::$instance = new self;

            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Coastalynk_SBM_Shortcode hooks
     */
    private function hooks() {
        add_shortcode( 'Coastalynk_SBM_MAP', [ $this, 'shortcode_body' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'coastalynk_enqueue_scripts' ] );
    }
    
    /**
     * Create shortcode for slideshow
     */
    public function shortcode_body( $atts ) {
        global $wpdb;

        ob_start();
        
        // Your Datalastic API Key
        $apiKey 	= get_option( 'coatalynk_datalastic_apikey' );
        $total_port_vessels = [];
        $type_of_port = __( "Offshore Terminal", "castalynkmap" );

        $table_name = $wpdb->prefix . 'coastalynk_ports';
        $port_data = $wpdb->get_results("SELECT * FROM $table_name where country_iso='NG'"); // where port_type='Offshore Terminal'

        $ports = [];  
        foreach( $port_data as $port ) {
            $ports[$port->title] = [$port->lat, $port->lon];
        }

        $table_name_sbm = $wpdb->prefix . 'coastalynk_sbm';
        $vessel_data = $wpdb->get_results("SELECT * FROM $table_name_sbm");

        ?>
        <script>
        
    </script>
        <div class="vessel-dashboard-container">
            <?php coastalynk_side_bar_menu(); ?>
            <!-- Sidebar -->

            <div class="datalastic-container">
                <div class="sbm-main-content">
                    <div class="sbm-sidebar">
                        <div class="sbm-card">
                            <input type="image" class="coastlynk-menu-dashboard-open-close-burger" src="<?php echo CSM_IMAGES_URL;?>burger-port-page.png" />
                            <h2 class="sbm-card-title"><?php _e( "SBM Locations", "castalynkmap" );?></h2>
                            <ul class="sbm-list">
                                
                                <?php foreach( $port_data as $port ) { ?>
        
                                    <li class="sbm-item">
                                        <span class="sbm-name"><?php echo $port->title; ?></span>
                                        <span class="sbm-status sbm-status-active"><?php _e( "Active", "castalynkmap" );?></span>
                                    </li>
                                <?php } ?>
                                
                            </ul>
                        </div>
                        
                        <div class="sbm-card">
                            <h2 class="sbm-card-title"><?php _e( "Vessels at SBM", "castalynkmap" );?></h2>
                            <div class="sbm-vessel-list">
                                <?php foreach( $vessel_data as $vessel ) { ?>
                                    <div class="sbm-vessel-item">
                                        <div class="sbm-vessel-name"><?php echo $vessel->name; ?></div>
                                        <div class="sbm-vessel-details">
                                            <span class="sbm-vessel-location"><?php echo $vessel->port; ?></span>  
                                            <span class="sbm-vessel-status sbm-status-at-sbm"><?php _e( "At SBM", "castalynkmap" );?></span>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="sbm-map-container">
                        <div id="sbm-map" style="height: 100vh; width: 100%; min-width:100%;"></div>
                        
                        <div class="sbm-legend">
                            <h3 class="sbm-legend-title"><?php _e( "Map Legend", "castalynkmap" );?></h3>
                            <div class="sbm-legend-item">
                                <div class="sbm-legend-color sbm-marker"></div>
                                <span><?php _e( "SBM Location", "castalynkmap" );?></span>
                            </div>
                            <div class="sbm-legend-item">
                                <div class="sbm-legend-color sbm-vessel-marker"></div>
                                <span><?php _e( "Vessel", "castalynkmap" );?></span>
                            </div>
                            <div class="sbm-legend-item">
                                <div class="sbm-legend-color" style="background-color: #2e7d32;"></div>
                                <span><?php _e( "Vessel at SBM", "castalynkmap" );?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <!-- Leaflet JS -->
            <script>
                // Initialize the map
                const map = L.map('sbm-map').setView([4.5, 6.0], 7);
                
                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                
                // SBM locations with coordinates (approximate)
                const sbmLocations = [
                    <?php 
                        $index = 0;
                        foreach( $port_data as $port ) { ?>
                        <?php echo $index == 0 ? '' : ','; ?>{ name: '<?php echo $port->title; ?>', port_type: '<?php echo $port->port_type; ?>', lat: '<?php echo $port->lat; ?>', lng: '<?php echo $port->lon; ?>', status: 'active' }
                    <?php 
                            $index++;
                        } 
                    ?>
                ];
                
                // Vessel data
                const vessels = [
                    <?php foreach( $vessel_data as $vessel ) { 
                        $status = '';
                        if($vessel->is_offloaded == 'No') {
                            $status = 'at-sbm';
                        } else {
                            $status = 'complete';
                        }

                        $draught = abs( floatval( $vessel->draught ) - floatval( $vessel->completed_draught ) );
                        $leavy_data = '';
                        if($vessel->is_offloaded == 'Yes') {
                            $leavy_data = '';
                            $total = 0;
                            $cargo_dues = ( $draught * 6.79 );

                            $total += $cargo_dues;
                            $leavy_data .= '<b>NPA cargo dues (liquid bulk):</b> $'.$cargo_dues.'/ton<br>';

                            $sbm_spm_harbour = ( $draught * 1.39 );
                            $total += $sbm_spm_harbour;

                            $leavy_data .= '<b>SBM/SPM harbour dues:</b> $'.$sbm_spm_harbour.'/ton<br>';

                            $env_leavy = ( $draught * 0.12 );
                            $total += $env_leavy;
                            $leavy_data .= '<b>Environmental levy:</b> $'. $env_leavy.'/ton<br>';

                            $polution_leavy = ( $draught * 0.10 );
                            $total += $polution_leavy;
                            $leavy_data .= '<b>NIMASA pollution levy:</b> $'.$polution_leavy.'/ton<br>';
                            $leavy_data .= '<b>NIMASA wet cargo levy:</b> 3% of freight value<br>';
                            $leavy_data .= '<b>Total levy:</b> $'.$total.'/ton<br>';
                        }
                        ?>
                        { name: '<?php echo $vessel->name; ?>',completed_draught: '<?php echo $vessel->completed_draught; ?>',navigation_status: '<?php echo $vessel->navigation_status; ?>', type_specific: '<?php echo $vessel->type_specific; ?>', distance: '<?php echo number_format($vessel->distance); ?>', draught: '<?php echo $vessel->draught; ?>', speed: '<?php echo $vessel->speed; ?>', type: '<?php echo $vessel->type; ?>',flag: '<?php echo CSM_IMAGES_URL."flags/".strtolower( $vessel->country_iso ).".jpg"; ?>', mmsi: '<?php echo $vessel->mmsi; ?>', lat: '<?php echo $vessel->lat; ?>', lng: '<?php echo $vessel->lon; ?>', status: '<?php echo $status; ?>', sbm: '<?php echo $vessel->port; ?>', draught: '<?php echo $vessel->draught.(!empty($vessel->draught)?__( "meters", "castalynkmap" ):''); ?>', completed_draught: '<?php echo $vessel->completed_draught.(!empty($vessel->completed_draught)?__( "meters", "castalynkmap" ):''); ?>',leavy_data: '<?php echo $leavy_data; ?>' },
                    <?php } ?>
                ];
                
                // Create SBM markers
                const sbmMarkers = [];
                sbmLocations.forEach(location => {
                    const marker = L.circleMarker([location.lat, location.lng], {
                        color: '#4da6ff',
                        weight: 2,
                        fillColor: '#4da6ff',
                        fillOpacity: 0.7,
                        radius: 10
                    }).addTo(map);
                    
                    marker.bindPopup(`
                        <div style="font-weight: bold; margin-bottom: 8px;">${location.name} <?php _e( "SBM", "castalynkmap" );?></div>
                        <div>Status: <span style="color: ${location.status === 'active' ? '#2e7d32' : '#d32f2f'}">${location.status === 'active' ? '<?php _e( "Active", "castalynkmap" );?>' : '<?php _e( "Maintenance", "castalynkmap" );?>'}</span></div>
                        <div>Type: ${location.port_type}</div>
                    `);
                    
                    sbmMarkers.push(marker);
                    
                });
                
                // Create vessel markers
                const vesselMarkers = [];
                vessels.forEach(vessel => {
                    const isAtSbm = vessel.status === 'at-sbm';
                    const marker = L.circleMarker([vessel.lat, vessel.lng], {
                        color: isAtSbm ? '#2e7d32' : '#ff9800',
                        weight: 2,
                        fillColor: isAtSbm ? '#2e7d32' : '#ff9800',
                        fillOpacity: 0.9,
                        radius: 8
                    }).addTo(map);
                    
                    if (isAtSbm) {
                        marker.setStyle({
                            className: 'pulse-effect'
                        });
                    }
                    
                    marker.bindPopup(`
                        <table class="coastalynk-sbm-marker">
                            <tr>
                                <td colspan="2" valign="top" class="coastalynk-sbm-marker-name-part">
                                    <table>
                                        <tr>
                                            <td valign="top" width="20%"><img src="${vessel.flag}" alt="${vessel.country_iso}" style="width: 50px; height: 50px;"></td>
                                            <td valign="top" width="80%">
                                                <h3>${vessel.name}</h3>
                                                <div>${vessel.type}</div>
                                            </td>
                                        </tr>
                                        
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td class="coastalynk-sbm-marker-labels-part"><b><?php _e( "Before Draught", "castalynkmap" );?>:</b></td>
                                <td class="coastalynk-sbm-marker-labels-part">${vessel.draught}</td>
                            </tr>
                            <tr>
                                <td class="coastalynk-sbm-marker-labels-part"><b><?php _e( "Completed Draught", "castalynkmap" );?>:</b></td>
                                <td class="coastalynk-sbm-marker-labels-part">${vessel.completed_draught}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="coastalynk-sbm-marker-middle-part">
                                    <table>
                                        <tr class="coastalynk-sbm-marker-first-row">
                                            <td width="50%">
                                                <b><?php _e( "Speed:", "castalynkmap" );?></b><br>
                                                ${vessel.speed}
                                            </td>
                                            <td width="50%">
                                                <b><?php _e( "Distance:", "castalynkmap" );?></b><br>
                                                ${vessel.distance}m
                                            </td>
                                        </tr>
                                        <tr class="coastalynk-sbm-marker-second-row">
                                            <td colspan="2">
                                                <b><?php _e( "Type Specific", "castalynkmap" );?></b><br>
                                                ${vessel.type_specific}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <h3><?php _e( "Leavy Data", "castalynkmap" );?></h3>
                                </td>
                            </tr>    
                            <tr>
                                <td colspan="2" class="coastalynk-sbm-marker-middle-part">
                                    ${vessel.leavy_data}
                                </td>
                            </tr>
                            <tr class="coastalynk-sbm-marker-bottom-part">
                                <td>
                                    <b><?php _e( "Lat:", "castalynkmap" );?></b>
                                    ${vessel.lat}
                                </td>
                                <td>
                                    <b><?php _e( "Lon:", "castalynkmap" );?></b>
                                    ${vessel.lng}
                                </td>
                            </tr>
                            <tr class="coastalynk-sbm-marker-bottom-part">
                                <td colspan="2">
                                    <b><?php _e( "Nav. Status:", "castalynkmap" );?></b> ${vessel.navigation_status}
                                </td>
                            </tr>
                        </table>
                    `);
                    
                    vesselMarkers.push(marker);
                });
                
                // Add a scale control
                L.control.scale({metric: true, imperial: false}).addTo(map);
                
            </script>
        <?php
        $content = ob_get_contents();
        ob_get_clean();
        return $content; 
    }
    
    /**
    * Load custom CSS and JavaScript.
    */
    function coastalynk_enqueue_scripts() : void {
        
        // Enqueue my styles.
        wp_enqueue_style( 'coastalynk-sbm-shortcode-style', CSM_CSS_URL.'sbm-shortcode.css?'.time() );

        // Enqueue my scripts.
        wp_enqueue_script( 'coastalynk-sbm-shortcode-front', CSM_JS_URL.'sbm-shortcode.js', array("jquery"), time(), true );    
    }
}

/**
 * Class instance.
 */
Coastalynk_SBM_Shortcode::instance();