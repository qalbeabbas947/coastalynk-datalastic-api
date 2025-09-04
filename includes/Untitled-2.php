<?php
/**
 * Pay Per Lesson shortcode page
 *
 * Do not allow directly accessing this file.
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Coastalynk_STS_Shortcode
 */
class Coastalynk_STS_Shortcode {

    private static $instance = null;

    /**
     * @since 1.0
     * @return $this
     */
    public static function instance() {

        if ( is_null( self::$instance ) && ! ( self::$instance instanceof Coastalynk_STS_Shortcode ) ) {

            self::$instance = new self;

            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Coastalynk_STS_Shortcode hooks
     */
    private function hooks() {
        add_shortcode( 'Coastalynk_STS_MAP', [ $this, 'coastalynk_sts_shortcode' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'coastalynk_enqueue_scripts' ] );
    }
    
    /**
     * Create shortcode for slideshow
     */
    public function coastalynk_sts_shortcode( $atts ) {
        global $wpdb;
ini_set( "display_errors", "On" );
error_reporting(E_ALL);
        ob_start();
        
            // Your Datalastic API Key
        $apiKey = '15df4420-d28b-4b26-9f01-13cca621d55e';
        $total_port_vessels = [];
        // 1. Define the center points (latitude, longitude) of our ports
        $table_name = $wpdb->prefix . 'coastalynk_ports';
        $port_data = $wpdb->get_results("SELECT * FROM $table_name where port_type='Port'");
        $ports = [];  
        $ports['Apapa'] = [6.45, 3.36];
        $ports['TinCanIsland'] = [6.44, 3.34];
        $ports['Lomé'] = [6.1375, 1.2870];
        $ports['Tema'] = [5.6167, 0.0167];
        foreach( $port_data as $port ) {
            $ports[$port->title] = [$port->lat, $port->lon];
        }

        $table_name_sts = $wpdb->prefix . 'coastalynk_sts';
        $vessel_data = $wpdb->get_results("SELECT * FROM $table_name_sts");

        ?>
            <div class="datalastic-container">
                <header>
                    <div class="section-title d-flex whitetitle direction-column mb-0 mt-2">
                        <h2 class="mb-2"><?php _e( "Nigerian Ports Traffic Heatmap", "castalynkmap" );?></h2>
                        <p class="subtitle"><?php _e( "Real-time vessel traffic analysis for major Nigerian ports", "castalynkmap" );?></p>
                    </div>
                </header>
                
                <div class="controls">
                    <div class="port-selector coastalynk-port-selector">
                        <button class="port-button active" data-port="all"><?php _e( "All Ports", "castalynkmap" );?></button>
                        <?php
                            foreach( $ports as $port => $coords ) {
                                echo '<button class="port-button" data-port="'.$port.'">'.$port.'</button>';
                            }
                        ?>
                        
                    </div>
                    
                    <div class="view-options mt-2">
                        <button class="view-button active" data-view="heatmap"><?php _e( "Heatmap", "castalynkmap" );?></button>
                        <button class="view-button" data-view="vessels"><?php _e( "Vessels", "castalynkmap" );?></button>
                        <button class="view-button" data-view="ports"><?php _e( "Ports", "castalynkmap" );?></button>
                    </div>
                </div>
                
                <div class="dashboard">
                    <div class="stats-panel">
                        <div class="section-title d-flex justify-content-between mb-0 leftalign">
                            <h2><?php _e( "Port Statistics", "castalynkmap" );?></h2>               
                        </div>
                        <div class="stat-item">
                            <div class="stat-label"><?php _e( "Total Vessels Tracked", "castalynkmap" );?></div>
                            <div class="stat-value" id="total-vessels"><?php echo 1;//$total_vessels;?></div>
                        </div>
                        
                    </div>
                    
                    <div class="map-container">
                        <div id="map"></div>
                    </div>
                </div>
                
                <div class="info-panel">
                    <div class="section-title d-flex justify-content-between mb-0 leftalign">
                        <h2><?php _e( "Port Information", "castalynkmap" );?></h2>               
                    </div>
                    <div class="port-info">
                        <div class="port-card coastlynk-port-card" data-port="apapa">
                            <h3>Apapa Port</h3>
                            <div class="port-stat">
                                <span>UN/LOCODE:</span>
                                <span>NGAPP</span>
                            </div>
                            <div class="port-stat">
                                <span>Vessels:</span>
                                <span id="apapa-vessels"><?php echo $total_port_vessels["Apapa"];?></span> 
                            </div>
                            
                        </div>
                        <div class="port-card coastlynk-port-card" data-port="TinCanIsland">
                            <h3>Tin Can Island Port</h3>
                            <div class="port-stat">
                                <span>UN/LOCODE:</span>
                                <span>NGTIN</span>
                            </div>
                            <div class="port-stat">
                                <span>Vessels:</span>
                                <span id="tincan-vessels"><?php echo $total_port_vessels["TinCanIsland"];?></span>
                            </div>
                        </div>
                        
                        <div class="port-card coastlynk-port-card" data-port="onne">
                            <h3>Onne Port</h3>
                            <div class="port-stat">
                                <span>UN/LOCODE:</span>
                                <span>NGONN</span>
                            </div>
                            <div class="port-stat">
                                <span>Vessels:</span>
                                <span id="onne-vessels"><?php echo $total_port_vessels["Onne"];?></span>
                            </div>
                        </div>
                        
                        <div class="port-card coastlynk-port-card" data-port="calabar">
                            <h3>Calabar Port</h3>
                            <div class="port-stat">
                                <span>UN/LOCODE:</span>
                                <span>NGCBQ</span>
                            </div>
                            <div class="port-stat">
                                <span>Vessels:</span>
                                <span id="calabar-vessels"><?php echo $total_port_vessels["Calabar"];?></span>
                            </div>
                        </div>

                        <div class="port-card coastlynk-port-card" data-port="lomé">
                            <h3>Lomé Port</h3>
                            <div class="port-stat">
                                <span>UN/LOCODE:</span>
                                <span>TGLFW</span>
                            </div>
                            <div class="port-stat">
                                <span>Vessels:</span>
                                <span id="calabar-vessels"><?php echo $total_port_vessels["Lomé"];?></span>
                            </div>
                        </div>

                        <div class="port-card coastlynk-port-card" data-port="tema">
                            <h3>Tema Port</h3>
                            <div class="port-stat">
                                <span>UN/LOCODE:</span>
                                <span>GHTEM</span>
                            </div>
                            <div class="port-stat">
                                <span>Vessels:</span>
                                <span id="calabar-vessels"><?php echo $total_port_vessels["Tema"];?></span>
                            </div>
                        </div>

                    </div>
                </div>
                
            </div>

            <!-- Leaflet JS -->
            <script>
                // Initialize the map
                var map = L.map('map').setView([37.55, 122.08], 10);

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Create custom vessel icons
                function createVesselIcon() {
                    var iconUrl;
                    var iconSize = [30, 30];
                    
                    iconUrl = "http://localhost:8089/wp-content/plugins/coastlynkmap/images/ship.png";
                    // Add status indicator
                    var html = '<div style="position:relative">';
                    html += '<img src="' + iconUrl + '" width="30" height="30">';
                    html += '<div style="position:absolute; bottom:-5px; right:-5px; width:12px; height:12px; border-radius:50%; background:';
                    
                    html += 'green';
                    
                    html += '"></div></div>';
                    
                    return L.divIcon({
                        html: html,
                        className: 'vessel-marker',
                        iconSize: [30, 30],
                        iconAnchor: [15, 15]
                    });
                }

                // Vessel data structure
                var vessels = {
                    <?php foreach( $vessel_data as $feature ) { ?>
                        '<?php echo $feature->vessel1_uuid;?>': {
                            id: '<?php echo $feature->vessel1_uuid;?>',
                            name: '<?php echo $feature->vessel1_name;?>',
                            type: '<?php echo $feature->vessel1_type;?>',
                            position: [<?php echo $feature->vessel1_lat;?>, <?php echo $feature->vessel1_lon;?>],
                            speed: <?php echo $feature->vessel1_speed;?>,
                        },
                        '<?php echo $feature->vessel2_uuid;?>': {
                            id: '<?php echo $feature->vessel2_uuid;?>',
                            name: '<?php echo $feature->vessel2_name;?>',
                            type: '<?php echo $feature->vessel2_type;?>',
                            position: [<?php echo $feature->vessel2_lat;?>, <?php echo $feature->vessel2_lon;?>],
                            speed: <?php echo $feature->vessel2_speed;?>,
                        },
                    <?php } ?>
                    
                };

                // Transfer operations data
                var transferOperations = [
                    <?php foreach( $vessel_data as $feature ) { ?>
                    {
                        id: 'transfer<?php echo $feature->vessel1_uuid;?>',
                        vessel1: '<?php echo $feature->vessel1_uuid;?>',
                        vessel2: '<?php echo $feature->vessel2_uuid;?>',
                        startTime: '<?php echo $feature->last_updated;?>',
                        color: '#00ff00'
                    },
                    <?php } ?>
                    
                ];

                // Store markers and connections
                var vesselMarkers = {};
                var transferConnections = {};
                var activeLines = [];

                // Initialize vessel markers
                for (var vesselId in vessels) {
                    if (vessels.hasOwnProperty(vesselId)) {
                        var vessel = vessels[vesselId];
                        vesselMarkers[vesselId] = L.marker(
                            vessel.position,
                            {icon: createVesselIcon()}
                        ).addTo(map);
                        
                        // Add popup to vessel
                        vesselMarkers[vesselId].bindPopup(`
                            <div class="vessel-info">
                                <h4>${vessel.name}</h4>
                                <p>Type: ${vessel.type}</p>
                                <p>Speed: ${vessel.speed} knots</p>
                            </div>
                        `);
                    }
                }

                // Function to create transfer connection
                function createTransferConnection(operation) {
                    var vessel1 = vesselMarkers[operation.vessel1];
                    var vessel2 = vesselMarkers[operation.vessel2];
                    
                    if (!vessel1 || !vessel2) return null;
                    
                    var latlngs = [
                        vessel1.getLatLng(),
                        vessel2.getLatLng()
                    ];
                    
                    var style = {
                            color: '#00ff00',
                            weight: 4,
                            dashArray: null,
                            opacity: 0.9
                        };
                    
                    var line = L.polyline(latlngs, style).addTo(map);
                    
                    // Add hover effects
                    line.on('mouseover', function(e) {
                        this.setStyle({weight: style.weight + 2});
                    });
                    
                    line.on('mouseout', function(e) {
                        this.setStyle({weight: style.weight});
                    });
                    
                    // Add popup to transfer line
                    line.bindPopup(`
                        <div class="transfer-info">
                            <h4>Ship-to-Ship Transfer</h4>
                            <p>Vessels: ${vessels[operation.vessel1].name} ↔ ${vessels[operation.vessel2].name}</p>
                            <p>Started: ${operation.startTime}</p>
                        </div>
                    `);
                    
                    // Store reference
                    transferConnections[operation.id] = {
                        line: line,
                        operation: operation
                    };
                    
                    activeLines.push(line);
                    
                    return operation.id;
                }

                // Create all transfer connections
                for (var i = 0; i < transferOperations.length; i++) {
                    createTransferConnection(transferOperations[i]);
                }

                // Fit map to show all vessels and connections
                var group = new L.featureGroup(activeLines.concat(Object.values(vesselMarkers)));
                map.fitBounds(group.getBounds().pad(0.1));
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
       wp_enqueue_style( 'coastalynk-slick-front-style', CSSN_ASSETS_URL.'css/sts-shortcode.css' );
       
        // Enqueue my scripts.
        wp_enqueue_script( 'coastalynk-slick-carousel-front', CSSN_ASSETS_URL.'js/sts-shortcode.js', array("jquery"), time(), true );    
        
    }
}

/**
 * Class instance.
 */
Coastalynk_STS_Shortcode::instance();


<script>
                // Initialize the map
                var map = L.map('map').setView([37.55, 122.08], 10);

                // Add OpenStreetMap tiles
                // Add base layers
                const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: '© Esri'
                });
                // Create custom vessel icons
                function createVesselIcon() {
                    var iconUrl;
                    var iconSize = [30, 30];
                    
                    iconUrl = "http://localhost:8089/wp-content/plugins/coastlynkmap/images/ship.png";
                    // Add status indicator
                    var html = '<div style="position:relative">';
                    html += '<img src="' + iconUrl + '" width="30" height="30">';
                    html += '<div style="position:absolute; bottom:-5px; right:-5px; width:12px; height:12px; border-radius:50%; background:';
                    
                    html += 'green';
                    
                    html += '"></div></div>';
                    
                    return L.divIcon({
                        html: html,
                        className: 'vessel-marker',
                        iconSize: [30, 30],
                        iconAnchor: [15, 15]
                    });
                }

                // Vessel data structure
                var vessels = {
                    <?php foreach( $vessel_data as $feature ) { ?>
                        '<?php echo $feature->vessel1_uuid;?>': {
                            id: '<?php echo $feature->vessel1_uuid;?>',
                            name: '<?php echo $feature->vessel1_name;?>',
                            type: '<?php echo $feature->vessel1_type;?>',
                            position: [<?php echo $feature->vessel1_lat;?>, <?php echo $feature->vessel1_lon;?>],
                            speed: <?php echo $feature->vessel1_speed;?>,
                        },
                        '<?php echo $feature->vessel2_uuid;?>': {
                            id: '<?php echo $feature->vessel2_uuid;?>',
                            name: '<?php echo $feature->vessel2_name;?>',
                            type: '<?php echo $feature->vessel2_type;?>',
                            position: [<?php echo $feature->vessel2_lat;?>, <?php echo $feature->vessel2_lon;?>],
                            speed: <?php echo $feature->vessel2_speed;?>,
                        },
                    <?php } ?>
                    
                };

                // Transfer operations data
                var transferOperations = [
                    <?php foreach( $vessel_data as $feature ) { ?>
                    {
                        id: 'transfer<?php echo $feature->vessel1_uuid;?>',
                        vessel1: '<?php echo $feature->vessel1_uuid;?>',
                        vessel2: '<?php echo $feature->vessel2_uuid;?>',
                        startTime: '<?php echo $feature->last_updated;?>',
                        color: '#00ff00'
                    },
                    <?php } ?>
                    
                ];

                // Store markers and connections
                var vesselMarkers = {};
                var transferConnections = {};
                var activeLines = [];

                // Initialize vessel markers
                for (var vesselId in vessels) {
                    if (vessels.hasOwnProperty(vesselId)) {
                        var vessel = vessels[vesselId];
                        vesselMarkers[vesselId] = L.marker(
                            vessel.position,
                            {icon: createVesselIcon()}
                        ).addTo(map);
                        
                        // Add popup to vessel
                        vesselMarkers[vesselId].bindPopup(`
                            <div class="vessel-info">
                                <h4>${vessel.name}</h4>
                                <p>Type: ${vessel.type}</p>
                                <p>Speed: ${vessel.speed} knots</p>
                            </div>
                        `);
                    }
                }

                // Function to create transfer connection
                function createTransferConnection(operation) {
                    var vessel1 = vesselMarkers[operation.vessel1];
                    var vessel2 = vesselMarkers[operation.vessel2];
                    
                    if (!vessel1 || !vessel2) return null;
                    
                    var latlngs = [
                        vessel1.getLatLng(),
                        vessel2.getLatLng()
                    ];
                    
                    var style = {
                            color: '#00ff00',
                            weight: 4,
                            dashArray: null,
                            opacity: 0.9
                        };
                    
                    var line = L.polyline(latlngs, style).addTo(map);
                    
                    // Add hover effects
                    line.on('mouseover', function(e) {
                        this.setStyle({weight: style.weight + 2});
                    });
                    
                    line.on('mouseout', function(e) {
                        this.setStyle({weight: style.weight});
                    });
                    
                    // Add popup to transfer line
                    line.bindPopup(`
                        <div class="transfer-info">
                            <h4>Ship-to-Ship Transfer</h4>
                            <p>Vessels: ${vessels[operation.vessel1].name} ↔ ${vessels[operation.vessel2].name}</p>
                            <p>Started: ${operation.startTime}</p>
                        </div>
                    `);
                    
                    // Store reference
                    transferConnections[operation.id] = {
                        line: line,
                        operation: operation
                    };
                    
                    activeLines.push(line);
                    
                    return operation.id;
                }

                // Create all transfer connections
                for (var i = 0; i < transferOperations.length; i++) {
                    createTransferConnection(transferOperations[i]);
                }

                // Fit map to show all vessels and connections
                var group = new L.featureGroup(activeLines.concat(Object.values(vesselMarkers)));
                map.fitBounds(group.getBounds().pad(0.1));
            </script>