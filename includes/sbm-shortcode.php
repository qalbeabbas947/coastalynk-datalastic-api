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
        // 1. Define the center points (latitude, longitude) of our ports
        $table_name = $wpdb->prefix . 'coastalynk_ports';
        $port_data = $wpdb->get_results("SELECT * FROM $table_name where port_type='Offshore Terminal'");
        //echo '<pre>';print_r($port_data);echo '</pre>';
        $ports = [];  
        foreach( $port_data as $port ) {
            $ports[$port->title] = [$port->lat, $port->lon];
        }

        $table_name_sbm = $wpdb->prefix . 'coastalynk_sbm';
        $vessel_data = $wpdb->get_results("SELECT * FROM $table_name_sbm");

        ?>
            <div class="datalastic-container">
                <header>
                    <div class="section-title d-flex whitetitle direction-column mb-0 mt-2">
                        <h2 class="mb-2"><?php _e( "Nigerian Single Buoy Mooring (SBM)", "castalynkmap" );?></h2>
                        <p class="subtitle"><?php _e( "SBM vessel traffic for major Nigerian ports", "castalynkmap" );?></p>
                    </div>
                </header>
                
                <div class="sbm-main-content">
                    <div class="sbm-sidebar">
                        <div class="sbm-card">
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
                        <div id="sbm-map"></div>
                        
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
                        <?php echo $index == 0 ? '' : ','; ?>{ name: '<?php echo $port->title; ?>', lat: '<?php echo $port->lat; ?>', lng: '<?php echo $port->lon; ?>', status: 'active' }
                    <?php 
                            $index++;
                        } 
                    ?>
                ];
                
                // Vessel data
                const vessels = [
                    <?php foreach( $vessel_data as $vessel ) { ?>
                        { name: '<?php echo $vessel->name; ?>', mmsi: '<?php echo $vessel->mmsi; ?>', lat: '<?php echo $vessel->lat; ?>', lng: '<?php echo $vessel->lon; ?>', status: 'at-sbm', sbm: '<?php echo $vessel->port; ?>' },
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
                        <div>Type: <?php echo $type_of_port;?></div>
                    `);
                    
                    sbmMarkers.push(marker);
                    
                    // Add SBM label
                    L.marker([location.lat, location.lng], {
                        icon: L.divIcon({
                            html: `<div style="background: rgba(26, 54, 93, 0.8); padding: 4px 8px; border-radius: 4px; color: white; font-weight: bold; border: 1px solid #2a4b7c;">${location.name}</div>`,
                            className: 'sbm-label',
                            iconSize: [60, 20],
                            iconAnchor: [30, 0]
                        })
                    }).addTo(map);
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
                        <div style="font-weight: bold; margin-bottom: 8px;">${vessel.name}</div>
                        <div>MMSI: ${vessel.mmsi}</div>
                        <div>Status: <span style="color: ${vessel.status === 'at-sbm' ? '#2e7d32' : '#ff9800'}">${vessel.status === 'at-sbm' ? 'At SBM' : 'In Transit'}</span></div>
                        ${vessel.sbm ? `<div>Current SBM: ${vessel.sbm}</div>` : ''}
                    `);
                    
                    vesselMarkers.push(marker);
                });
                
                // Add a scale control
                L.control.scale({metric: true, imperial: false}).addTo(map);
                
                // Button functionality for demo purposes
                document.getElementById('sbm-refresh-btn').addEventListener('click', function() {
                    alert('Data refresh simulated. In a real application, this would update vessel positions from the API.');
                });
                
                document.getElementById('sbm-simulate-btn').addEventListener('click', function() {
                    alert('Vessel movement simulation triggered. In a real application, this would animate vessel movements.');
                });
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