<?php

/**
 * Dashboard Menu shortcode page
 *
 * Do not allow directly accessing this file.
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Coastalynk_Dashboard_Port_Congestion_Shortcode
 */
class Coastalynk_Dashboard_Port_Congestion_Shortcode {

    private static $instance = null;

    /**
     * @since 1.0
     * @return $this
     */
    public static function instance() {

        if ( is_null( self::$instance ) && ! ( self::$instance instanceof Coastalynk_Dashboard_Port_Congestion_Shortcode ) ) {

            self::$instance = new self;

            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Coastalynk_Dashboard_Port_Congestion_Shortcode hooks
     */
    private function hooks() {
        add_shortcode( 'show_vessels', [ $this, 'shortcode_body' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'coastalynk_enqueue_scripts' ] );
    }
    
    /**
     * Create shortcode for slideshow
     */
    public function shortcode_body( $atts ) {
        
        global $wpdb;
        $table_name = $wpdb->prefix.'coastalynk_dark_ships'; 
        $dark_ships = $wpdb->get_results( "SELECT uuid, name, last_position_UTC, reason FROM $table_name", ARRAY_A );

        $apiKey 	= get_option( 'coatalynk_datalastic_apikey' );

        $searchfield = isset( $_REQUEST['searchfield']  ) ? sanitize_text_field( $_REQUEST['searchfield'] ): "";
        ob_start();

        // Your Datalastic API Key
        
        $total_port_vessels = [];
        $table_name = $wpdb->prefix . 'coastalynk_ports';
        $port_data = $wpdb->get_results("SELECT * FROM $table_name where country_iso='NG' and port_type='Port'");

        $ports = [];  
        foreach( $port_data as $port ) {
            $ports[$port->title] = [$port->lat, $port->lon];
        }

        // 2. Define the search radius (in kilometers) around each port
        $searchRadiusKm = 10; // Search 10 km around each port center

        // 3. Define the dimensions and resolution of our final heatmap grid
        $gridWidth = 500;  // Width of the final image in pixels
        $gridHeight = 300; // Height of the final image in pixels

        // 4. Define the bounding box for our entire heatmap area
        // This will contain all points we find. We'll calculate this dynamically.
        $minLat = 90;
        $maxLat = -90;
        $minLon = 180;
        $maxLon = -180;


        // Initialize an array to hold all vessel positions
        $allVesselPositions = [];

        $total_vessels = 0;
        // Loop through each port and fetch vessels in its radius
        foreach ($ports as $portName => $portCoords) {
            list($portLat, $portLon) = $portCoords;

            // Build the API URL for the vessels_in_radius endpoint
            $url = sprintf(
                "https://api.datalastic.com/api/v0/vessel_inradius?api-key=%s&lat=%f&lon=%f&radius=%d",
                urlencode($apiKey),
                $portLat,
                $portLon,
                $searchRadiusKm
            );

            // Make the API request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Enable in production

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                echo "cURL Error for $portName: " . curl_error($ch) . "\n";
                curl_close($ch);
                continue;
            }
            curl_close($ch);

            // Decode the JSON response
            $data = json_decode($response, true);

            // Check if we got data
            if (isset($data['data']['vessels'])) {
                $vessels = $data['data']['vessels'];
                $total_port_vessels[ $portName ] = 0;
                
                // Add each vessel's position to our master list and update the overall bounding box
                foreach ($vessels as $vessel) {
                    if (isset($vessel['lat']) && isset($vessel['lon'])) {
                        $lat = (float)$vessel['lat'];
                        $lon = (float)$vessel['lon'];
                        $name = isset( $vessel['name'] ) ? $vessel['name']: '';
                        $mmsi = isset( $vessel['mmsi'] ) ? $vessel['mmsi'] : '';
                        $imo = isset( $vessel['imo'] ) ? $vessel['imo'] : '';
                        if( !empty( $searchfield ) ) {
                            
                            if( ! str_contains( $name, $searchfield ) && ! str_contains( $mmsi, $searchfield ) &&  ! str_contains( $imo, $searchfield ) ) {
                                continue;
                            }
                        }
                        $allVesselPositions[] = ['lat' => $lat, 'lon' => $lon];

                        // Expand the overall bounding box to include this point
                        $minLat = min($minLat, $lat);
                        $maxLat = max($maxLat, $lat);
                        $minLon = min($minLon, $lon);
                        $maxLon = max($maxLon, $lon);
                        $total_vessels++;
                        $total_port_vessels[$portName] += 1;

                    }
                }
            } 
        }
        
        // Initialize arrays
        $allVesselPositions = [];
        $features = []; // For GeoJSON
        $last_position_epoch = 0;
        $last_position_utc = '';
        // Fetch data for each port
        foreach ($ports as $portName => $portCoords) {
            list($portLat, $portLon) = $portCoords;

            $url = sprintf(
                "https://api.datalastic.com/api/v0/vessel_inradius?api-key=%s&lat=%f&lon=%f&radius=%d",
                urlencode($apiKey),
                $portLat,
                $portLon,
                $searchRadiusKm
            );

            $response = file_get_contents( $url );
            $data = json_decode( $response, true );

            if (isset($data['data']['vessels'])) {

                foreach ( $data['data']['vessels'] as $vessel ) {

                    if ( isset( $vessel['lat'] ) && isset( $vessel['lon'] ) ) {
                        $lat = (float) $vessel['lat'];
                        $lon = (float) $vessel['lon'];
                        
                        // Add to all positions array
                        $allVesselPositions[] = ['lat' => $lat, 'lon' => $lon];
                        if( $last_position_epoch < intval($vessel['last_position_epoch']) ) {
                            $last_position_epoch = intval($vessel['last_position_epoch']);
                            $last_position_utc = $vessel['last_position_UTC'];

                        }

                        $name = isset( $vessel['name'] ) ? $vessel['name']: '';
                        $mmsi = isset( $vessel['mmsi'] ) ? $vessel['mmsi'] : '';
                        $imo = isset( $vessel['imo'] ) ? $vessel['imo'] : '';
                        if( !empty( $searchfield ) ) {
                            
                            if( ! str_contains( $name, $searchfield ) && ! str_contains( $mmsi, $searchfield ) &&  ! str_contains( $imo, $searchfield ) ) {
                                continue;
                            }
                        }

                        $flag = 'no-flag';
                        if( !empty( $vessel['country_iso'] ) ) {
                            $flag = $vessel['country_iso'];
                        }
                        // Create a GeoJSON feature for each vessel
                        $is_dark_ship = false;
                        $color = "#008000";
                        $reason = '';
                        foreach( $dark_ships as $dship ) {
                            if( $vessel['uuid'] == $dship['uuid'] ) {
                                $is_dark_ship = true;
                                $reason = $dship['reason'];
                                $color = "#ff0000";
                            }
                        }

                        $features[] = [
                            'type' => 'Feature',
                            'geometry' => [
                                'type' => 'Point',
                                'coordinates' => [$lon, $lat]
                            ],
                            'properties' => [
                                'uuid' => $vessel['uuid'],
                                'name' => $vessel['name'] ?? __( "Unknown", "castalynkmap" ),
                                'mmsi' => $vessel['mmsi'] ?? __( 'N/A', "castalynkmap" ),
                                'imo' => $vessel['imo'] ?? '',
                                'port' => $portName,
                                'country_flag' => CSM_IMAGES_URL."flags/".strtolower( $flag ).".jpg" ?? '',
                                'destination' => $vessel['destination'] ?? '',
                                'speed' => $vessel['speed'] ?? '',
                                'timestamp' => $vessel['last_position_epoch'] ?? '',
                                'type' => $vessel['type'] ?? '',
                                'type_specific' => $vessel['type_specific'] ?? '',
                                'reason' => $reason ?? '',
                                'color' => $color ?? '',

                            ]
                        ];
                    }
                }
            }
        }
        ?>
        <div class="vessel-dashboard-container">
            <?php coastalynk_side_bar_menu(); ?>
            <!-- Sidebar -->
            <div class="datalastic-container">
                <header>
                    <div class="controls">
                        
                        <div class="port-selector coastalynk-port-selector">
                            <input type="image" class="coastlynk-menu-dashboard-open-close-burger" src="<?php echo CSM_IMAGES_URL;?>burger-port-page.png" />
                            <!-- <button class="coastalynk-port-history"><?php _e( "History", "castalynkmap" );?></button> -->
                            <div class="coastalynk-port-menu-container">
                                <button id="coastalynk-port-prev-btn">&lt;</button>
                                <div class="port-selector coastalynk-port-selector coastalynk-port-scroll-menu">
                                    <button class="port-button active" data-port="all"><?php _e( "All Ports", "castalynkmap" );?></button>
                                    <?php foreach( $ports as $port => $coords ) { ?>
                                        <button class="port-button" data-port="<?php echo $port;?>"><?php _e( $port, "castalynkmap" );?></button>
                                    <?php } ?>
                                </div>
                                <button id="coastalynk-port-next-btn">&gt;</button>
                            </div>
                        </div>
                        <div class="view-options">
                            <button class="view-button coastalynk-map-view-button-first active" data-view="heatmap"><?php _e( "Heatmap", "castalynkmap" );?></button>
                            <button class="view-button" data-view="vessels"><?php _e( "Vessels", "castalynkmap" );?></button>
                            <button class="view-button coastalynk-map-view-button-last" data-view="ports"><?php _e( "Ports", "castalynkmap" );?></button>
                        </div>
                    </div>
                </header>
                <div class="dashboard">
                    <div class="stats-panel">
                        <div class="section-title d-flex justify-content-between mb-0 leftalign">
                            <h2><?php _e( "Port Statistics", "castalynkmap" );?></h2>               
                        </div>
                        <div class="coastalynk-port-congestion-sidebar-wrapper"> 
                            <div class="stat-item coastalynk-totoal-vessels">
                                <div class="stat-label"><?php _e( "Total Vessels Tracked", "castalynkmap" );?></div>
                                <div class="stat-value" id="total-vessels"><?php echo $total_vessels;?></div>
                            </div>
                            
                            <div class="coastalynk-congestion-data" style="display: none;">
                            </div>
                            <div class="stat-item coastalynk-congestion-loader" style="display: none;">
                                <div class="stat-label"><?php _e( "Loading, please wait...", "castalynkmap" );?></div>
                            
                            </div>
                        </div>
                    </div>
                    
                    <div class="map-container">
                        <div id="map"></div>
                    </div>
                </div>

                <div class="coastlynk-darkship-ticker-container">
                    <div class="coastlynk-darkship-ticker-wrapper">
                        <div class="coastallynk-darkship-ticker" id="coastallynk-darkship-ticker">
                            <?php
                            foreach( $dark_ships as $dship ) {
                                ?>
                                    <div class="coastalynk-darkship-ticker-item">
                                        <span class="coastalynk-darkship-news-time"><?php echo date('m/d/Y H:i', strtotime( $dship['last_position_UTC']));?></span>
                                        <?php echo $dship['name'];?>: <?php echo $dship['reason'];?>
                                    </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="coastalynk-darkship-controls">
                        <button id="coastalynk-darkship-pause-btn"><?php _e( "Pause", "castalynkmap" );?></button>
                        <button id="coastalynk-darkship-resume-btn"><?php _e( "Resume", "castalynkmap" );?></button>
                    </div>
                </div>

                <form method="post" id="coastalynk-port-congestion-history-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" name="coastalynk-port-congestion-history-form">
                    <div class="section-title d-flex justify-content-between mb-0 leftalign">
                        <h2><?php _e( "Congestion History", "castalynkmap" );?></h2>               
                    </div>
                    <div class="caostalynk-history-header-buttons">
                        <div class="coastalynk-congestion-history-ports">
                            <select id="caostalynk_history_ddl_ports" class="coastalynk-select2-js" name="caostalynk_history_ddl_ports">
                                <option value=""><?php _e( "All Ports", "castalynkmap" );?></option>
                                <?php foreach( $port_data as $port ) { ?>
                                    <option value="<?php echo $port->port_id;?>"><?php echo $port->title;?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="coastalynk-congestion-history-date-range">
                            <input id="caostalynk_congestion_history_range" type="text" class="coastalynk-date-range-js" name="caostalynk_congestion_history_range">
                        </div>
                        <div class="coastalynk-congestion-history-dates" style="display: none;">
                            <select id="caostalynk_history_ddl_dates" class="caostalynk_history_ddl_dates" name="caostalynk_history_ddl_dates">
                                <option value=""><?php _e( "All Dates", "castalynkmap" );?></option>
                            </select>
                        </div>
                        <div class="coastalynk-congestion-history-times" style="display: none;">
                            <select id="caostalynk-history-ddl-times" class="caostalynk_history_ddl_times" name="caostalynk_history_ddl_times">
                                <option value=""><?php _e( "All Times", "castalynkmap" );?></option>
                            </select>
                        </div>
                        <div class="coastalynk-congestion-history-buttons">
                            <button class="coastalynk-history-button">
                                <?php _e( "Sub Dates/Times", "castalynkmap" );?>
                                <div id="coastalynk-column-loader" class="coastalynk-column-loader" style="display:none;">
                                    <div id="coastalynk-column-blockG_1" class="coastalynk-column-blockG"></div>
                                    <div id="coastalynk-column-blockG_2" class="coastalynk-column-blockG"></div>
                                    <div id="coastalynk-column-blockG_3" class="coastalynk-column-blockG"></div>
                                </div>
                            </button>
                            <button class="coastalynk-history-button-export-csv">
                                <?php _e( "Export", "castalynkmap" );?>
                                <div id="coastalynk-column-loader" class="coastalynk-column-loader" style="display:none;">
                                    <div id="coastalynk-column-blockG_1" class="coastalynk-column-blockG"></div>
                                    <div id="coastalynk-column-blockG_2" class="coastalynk-column-blockG"></div>
                                    <div id="coastalynk-column-blockG_3" class="coastalynk-column-blockG"></div>
                                </div>
                            </button>
                        </div>
                        <?php wp_nonce_field( 'coastalynk_congestion_history_load', 'coastalynk_congestion_history_load_nonce' ); ?>
                        <input type="hidden" id="coastalynk_congestion_history_load_action_ctrl" name="action" value="coastalynk_congestion_history_load_action" />
                    </div>
                </form>
                
                <div class="info-panel">
                    <div class="section-title d-flex justify-content-between mb-0 leftalign">
                        <h2><?php _e( "Port Information", "castalynkmap" );?></h2>               
                    </div>
                    <div class="port-info">
                        
                        <?php foreach( $port_data as $port ) { ?>
                            <div class="port-card coastlynk-port-card" data-port="<?php echo $port->title;?>">
                                <h3><?php echo $port->title;?> <?php _e( "Port", "castalynkmap" );?></h3>
                                <div class="port-stat coastlynk-port-stat-code"> 
                                    <div><?php _e( "Lat/Lon", "castalynkmap" );?>:</div>
                                    <span>[<?php echo number_format( $port->lat, 2);?>, <?php echo number_format( $port->lon, 2);?>]</span>
                                </div>
                                <div class="port-stat coastlynk-port-stat-vessel">
                                    <span><?php _e( "Vessels", "castalynkmap" );?>:</span>
                                    <span id="apapa-vessels"><?php echo $total_port_vessels[$port->title];?></span> 
                                </div>
                                
                            </div>
                        <?php } ?>
                    </div>
                </div>
                
                <table id="coastalynk-table" class="display" class="cell-border hover stripe">
                    <thead>
                        <tr>
                            <th></th>
                            <th><?php _e( "Name", "castalynkmap" );?></th>
                            <th><?php _e( "Port", "castalynkmap" );?></th>
                            <th><?php _e( "MMSI", "castalynkmap" );?></th>
                            <th><?php _e( "IMO", "castalynkmap" );?></th>
                            <th><?php _e( "Darkship?", "castalynkmap" );?></th>
                            <th><?php _e( "Destination", "castalynkmap" );?></th>
                            <th><?php _e( "Speed", "castalynkmap" );?></th>
                            <th><?php _e( "UTC", "castalynkmap" );?></th>
                            <th><?php _e( "Tonnage", "castalynkmap" );?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $features as $feature ) { ?>
                            <tr>
                                <td>
                                    <?php 
                                        if( !empty( $feature['properties']['country_flag'] ) ) {
                                            echo '<img src="'.$feature['properties']['country_flag'].'" class="coastalyn-flag-port-listing" alt="'.$feature['properties']['name'].'">';
                                        }
                                    ?>
                                </td>
                                <td><?php echo $feature['properties']['name']; ?></td>
                                <td><?php echo $feature['properties']['port']; ?></td>
                                <td><?php echo $feature['properties']['mmsi']; ?></td>
                                <td><?php echo $feature['properties']['imo']; ?></td>
                                <td><?php echo $feature['properties']['reason']; ?></td>
                                <td><?php echo $feature['properties']['destination']; ?></td>
                                <td><?php echo $feature['properties']['speed']; ?></td>
                                <td><?php echo $feature['properties']['timestamp'] ? date('Y-m-d H:i:s', $feature['properties']['timestamp']) : 'N/A'; ?></td>
                                <td>
                                    <input type="button" class="coastalynk-retrieve-tonnage-btn" data-name="<?php echo $feature['properties']['name']; ?>" data-uuid="<?php echo $feature['properties']['uuid']; ?>" value="<?php _e( "Retrieve Tonnage", "castalynkmap" );?>">
                                    <div id="coastalynk-column-loader" class="coastalynk-column-loader" style="display:none;">
                                        <div id="coastalynk-column-blockG_1" class="coastalynk-column-blockG"></div>
                                        <div id="coastalynk-column-blockG_2" class="coastalynk-column-blockG"></div>
                                        <div id="coastalynk-column-blockG_3" class="coastalynk-column-blockG"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="coastalynk-history-popup-overlay"></div>
        <div class="coastalynk-history-popup-content">
            <h2>History</h2>
            <div class="coastalynk-history-popup-content-boxes">
                <div class="caostalynk-history-header-buttons">
                    <!-- <div class="coastalynk-congestion-history-ports">
                        <?php
                            $results = $wpdb->get_results( "select distinct(port) as port from ".$wpdb->prefix."coastalynk_port_congestion" );
                        ?>
                        <select id="caostalynk-history-ddl-ports" class="coastalynk-select2-js" name="caostalynk-history-ddl-ports">
                            <option value=""><?php _e( "All Ports", "castalynkmap" );?></option>
                            <?php foreach( $results as $result ) { ?>
                                <option value="<?php echo $result->port;?>"><?php echo $result->port;?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="coastalynk-congestion-history-date-range">
                        <input id="caostalynk_congestion_history_range" type="text" class="coastalynk-date-range-js" name="caostalynk_congestion_history_range">
                    </div> 
                    
                    <button class="coastalynk-history-button"><?php _e( "Filter", "castalynkmap" );?></button>-->
                </div>
                <div class="caostalynk-history-header-images">

                    <div id="caostalynk-history-congestion-data-container"></div>
                    <div id="caostalynk-history-congestion-pagination-links"></div>

                </div>
            </div>
            <button id="coastalynk-history-popup-close"><?php _e( "Back to Results", "castalynkmap" );?></button>
        </div>
        <!-- Leaflet JS -->
        <script>
            // Initialize the map centered on Nigeria
            const map = L.map('map').setView([6.5, 5.0], 7);

            // Add base layers
            const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            });

            const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: '© Esri'
            }).addTo(map);

            // Define port locations
            const ports = {
                <?php foreach ($ports as $portName => $portCoords) {
                    list($portLat, $portLon) = $portCoords;
                    echo "'".$portName."': { coords: [$portLat, $portLon] },";
                } ?>
            };

            // Simulated vessel data (in a real scenario, this would come from the Datalastic API)
            const vesselData = {
                type: 'FeatureCollection',
                features: []
            };
            
            <?php foreach( $features as $feature ) { ?>
                    vesselData.features.push({
                        type: '<?php echo $feature['type'];?>',
                        geometry: {
                            type: '<?php echo $feature['geometry']['type'];?>',
                            coordinates: [<?php echo $feature['geometry']['coordinates'][0];?>, <?php echo $feature['geometry']['coordinates'][1];?>]
                        },
                        properties: {
                            name: "<?php echo $feature['properties']['name'];?>",
                            country_flag: "<?php echo $feature['properties']['country_flag'];?>",
                            mmsi: "<?php echo $feature['properties']['mmsi'];?>",
                            type: "<?php echo $feature['properties']['type'];?>",
                            type_specific: "<?php echo $feature['properties']['type_specific'];?>",
                            reason: "<?php echo $feature['properties']['reason'];?>",
                            color: "<?php echo $feature['properties']['color'];?>",
                            port: "<?php echo $feature['properties']['port'];?>",
                            timestamp: "<?php echo $feature['properties']['timestamp'];?>",
                            lat: "<?php echo $feature['geometry']['coordinates'][0];?>",
                            lng: "<?php echo $feature['geometry']['coordinates'][1];?>",
                            imo: "<?php echo $feature['properties']['imo'];?>",
                            destination: "<?php echo $feature['properties']['destination'];?>",
                            speed: "<?php echo $feature['properties']['speed'];?>",
                        }
                    });
            <?php } ?>
            // Convert to heatmap points
            const heatPoints = vesselData.features.map(feature => [
                feature.geometry.coordinates[1],
                feature.geometry.coordinates[0],
                0.5
            ]);

            // Create heatmap layer
            const heatLayer = L.heatLayer(heatPoints, {
                radius: 25,
                blur: 15,
                maxZoom: 17,
                gradient: {0.4: 'blue', 0.65: 'lime', 1: 'red'}
            }).addTo(map);

            // Create vessel markers layer
            const vesselsLayer = L.layerGroup();
            
            vesselData.features.forEach(feature => {
                const marker = L.circleMarker([
                    feature.geometry.coordinates[1],
                    feature.geometry.coordinates[0]
                ], {
                    radius: 4,
                    fillColor: feature.properties.color,
                    color: "#000",
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.8
                });
                
                const props = feature.properties;
                marker.bindPopup(`
                    <table class="coastalynk-sbm-marker" cellpadding="5" width="100%">
                        <tr>
                            <td colspan="2" valign="top" class="coastalynk-sbm-marker-name-part">
                                <table width="100%">
                                    <tr>
                                        <td valign="top" width="20%"><img src="${props.country_flag}" alt="${props.name}" style="width: 50px; height: 50px;"></td>
                                        <td valign="top" width="80%">
                                            <h3>${props.name}</h3>
                                            <div>${props.type}</div>
                                        </td>
                                    </tr>
                                    
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="coastalynk-sbm-marker-labels-part"><b><?php _e( "Last Updated:", "castalynkmap" );?></b> ${new Date(props.timestamp * 1000).toLocaleString()}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="coastalynk-sbm-marker-middle-part">
                                <table width="100%">
                                    <tr class="coastalynk-sbm-marker-first-row">
                                        <td width="50%">
                                            <b><?php _e( "Speed:", "castalynkmap" );?></b><br>
                                            ${props.speed}
                                        </td>
                                        <td width="50%">
                                            <b><?php _e( "Near:", "castalynkmap" );?></b><br>
                                            ${props.port}
                                        </td>
                                    </tr>
                                    <tr class="coastalynk-sbm-marker-second-row">
                                        <td colspan="3">
                                            <b><?php _e( "Specific Type", "castalynkmap" );?></b><br>
                                            ${props.type_specific}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr class="coastalynk-sbm-marker-bottom-part">
                            <td>
                                <b><?php _e( "Lat:", "castalynkmap" );?></b>
                                ${props.lat}<br>
                                
                            </td>
                            <td>
                                
                                <b><?php _e( "Lon:", "castalynkmap" );?></b>
                                ${props.lng}
                            </td>
                        </tr>
                        
                        <tr class="coastalynk-sbm-marker-bottom-part">
                            <td colspan="2">
                                <b><?php _e( "Reason(If Darkship):", "castalynkmap" );?></b> ${props.reason}
                            </td>
                        </tr>
                        <tr class="coastalynk-sbm-marker-bottom-part">
                            <td colspan="2">
                                <b><?php _e( "Destination:", "castalynkmap" );?></b> ${props.destination}
                            </td>
                        </tr>
                    </table>
                `);
                
                vesselsLayer.addLayer(marker);
            });
            vesselsLayer.addTo(map);
            // Create port markers layer
            const portsLayer = L.layerGroup();
            
            
            Object.entries(ports).forEach(([name, data]) => {
                const marker = L.marker(data.coords, {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                });
                
                marker.bindPopup(`<strong>${name} Port</strong>`);
                portsLayer.addLayer(marker);
            });
            
            // Add ports layer by default
            portsLayer.addTo(map);

            // Layer control
            const baseMaps = {
                "OpenStreetMap": osmLayer,
                "Satellite": satelliteLayer
            };

            const overlayMaps = {
                "Heatmap": heatLayer,
                "Vessels": vesselsLayer,
                "Ports": portsLayer
            };

            L.control.layers(baseMaps, overlayMaps).addTo(map);

            // Add legend
            const legend = L.control({position: 'bottomright'});
            legend.onAdd = function(map) {
                const div = L.DomUtil.create('div', 'info legend');
                div.innerHTML = `
                    <h4>Traffic Density</h4>
                    <div class="legend">
                        <i style="background: blue;"></i> Low<br>
                        <i style="background: lime;"></i> Medium<br>
                        <i style="background: red;"></i> High
                    </div>
                    <h4>Map Features</h4>
                    <div class="legend">
                        <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png" style="width: 15px; height: 25px;"> Port<br>
                        <span style="color: #ff7800;">●</span> Individual Vessel
                    </div>
                `;
                return div;
            };

            legend.addTo(map);

            // Set up button handlers
            document.querySelectorAll('.port-button').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.port-button').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    const port = this.dataset.port;
                    if (port === 'all') {
                        map.setView([6.5, 5.0], 7);
                    } else {
                        const portName = port.replace(/(^\w|\s\w)/g, l => l.toUpperCase()).replace('-', ' ');
                        const portCoords = ports[portName];
                        if (portCoords) {
                            map.setView(portCoords.coords, 12);
                        }
                    }
                });
            });
            
            document.querySelectorAll('.view-button').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.view-button').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    const view = this.dataset.view;
                    
                    // Toggle layers based on view
                    if (view === 'heatmap') {
                        map.addLayer(heatLayer);
                        map.removeLayer(vesselsLayer);
                        map.addLayer(portsLayer);
                    } else if (view === 'vessels') {
                        map.removeLayer(heatLayer);
                        map.addLayer(vesselsLayer);
                        map.addLayer(portsLayer);
                    } else if (view === 'ports') {
                        map.removeLayer(heatLayer);
                        map.removeLayer(vesselsLayer);
                        map.addLayer(portsLayer);
                    }
                });
            });
        </script>
    <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    /**
    * Load custom CSS and JavaScript.
    */
    function coastalynk_enqueue_scripts() : void {

        wp_enqueue_style( 'coastlynk-daterangepicker-css', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css' );
        wp_enqueue_style( 'coastlynk-map-dataTables-css', '//cdn.datatables.net/2.3.3/css/dataTables.dataTables.min.css' );
        wp_enqueue_style( 'coastlynk-select2.min.css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' );
        wp_enqueue_style( 'coastlynk-map-leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), time() );
        wp_enqueue_style( 'coastlynk-map-css', CSM_CSS_URL.'/frontend/port-congestion-shortcode.css', array(), time() );

        wp_enqueue_script( 'coastlynk-map-dataTables-js', '//cdn.datatables.net/2.3.3/js/dataTables.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'coastlynk-moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'coastlynk-daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array( 'jquery' ) );
        wp_enqueue_script( 'coastlynk-select2.min.js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'markercluster', 'https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js', array( 'jquery' ) );
        wp_enqueue_script( 'coastlynk-ticker-js', CSM_JS_URL.'ticker.js', array( 'jquery' ), time(), true );
        wp_enqueue_script( 'coastlynk-map-js', CSM_JS_URL.'/frontend/port-congestion-shortcode.js', array( 'jquery' ), time(), true );
        wp_localize_script( 'coastlynk-map-js', 'COSTALYNKVARS', [          
                'ajaxURL' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce('coastalynk_secure_ajax_nonce') // Create nonce
            ] );
    }
}

/**
 * Class instance.
 */
Coastalynk_Dashboard_Port_Congestion_Shortcode::instance();