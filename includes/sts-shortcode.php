<?php
/**
 * STS shortcode page
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

        ob_start();
        
            // Your Datalastic API Key
        $apiKey 	= get_option( 'coatalynk_datalastic_apikey' );
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
        <div class="vessel-dashboard-container">
            <?php coastalynk_side_bar_menu();?>
            <div class="datalastic-container">
                <header>
                    
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
                
                <div class="dashboard-sts">

                     <div id="map" style="height: 100vh; width: 100%; min-width:100%;"></div>
                </div>
                
                
            </div>

            <!-- Leaflet JS -->
            <script>
                // Initialize the map centered on Nigeria
                const map = L.map('map').setView([6.5, 5.0], 7);

                // Add base layers
                const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: '© Esri'
                });

                // Define port locations
                const ports = {
                    <?php foreach( $ports as $key=>$port ) { ?>
                        '<?php echo $key;?>': { coords: [<?php echo $port[0];?>, <?php echo $port[1];?>] },
                    <?php } ?>
                };

                // Simulated vessel data (in a real scenario, this would come from the Datalastic API)
                const vesselData = {
                    type: 'FeatureCollection',
                    features: []
                };

                <?php foreach( $vessel_data as $feature ) { ?>
                    vesselData.features.push({
                        type: 'Feature',
                        geometry: {
                            type: 'Point',
                            coordinates:[<?php echo $feature->vessel1_lon;?>, <?php echo $feature->vessel1_lat;?>]
                        },
                        properties: {
                            uuid: "<?php echo $feature->vessel1_uuid;?>",
                            name: "<?php echo $feature->vessel1_name;?>",
                            mmsi: "<?php echo $feature->vessel1_mmsi;?>",
                            imo: "<?php echo $feature->vessel1_imo;?>",
                            country_iso: "<?php echo $feature->vessel1_country_iso;?>",
                            type: "<?php echo $feature->vessel1_type;?>",
                            type_specific: "<?php echo $feature->vessel1_type_specific;?>",
                            speed: "<?php echo $feature->vessel1_speed;?>",
                            navigation_status: "<?php echo $feature->vessel1_navigation_status;?>",
                            last_position_UTC: "<?php echo $feature->vessel1_last_position_UTC;?>",
                            name: "<?php echo $feature->vessel1_name;?>",
                            port: "<?php echo $feature->port;?>",
                            distance: "<?php echo $feature->distance;?>",
                            last_updated: "<?php echo $feature->last_updated;?>"
                        }
                    });

                    vesselData.features.push({
                        type: 'Feature',
                        geometry: {
                            type: 'Point',
                            coordinates : [<?php echo $feature->vessel2_lon;?>, <?php echo $feature->vessel2_lat;?>]
                        },
                        properties: {
                            uuid: "<?php echo $feature->vessel2_uuid;?>",
                            name: "<?php echo $feature->vessel2_name;?>",
                            mmsi: "<?php echo $feature->vessel2_mmsi;?>",
                            imo: "<?php echo $feature->vessel2_imo;?>",
                            country_iso: "<?php echo $feature->vessel2_country_iso;?>",
                            type: "<?php echo $feature->vessel2_type;?>",
                            type_specific: "<?php echo $feature->vessel2_type_specific;?>",
                            speed: "<?php echo $feature->vessel2_speed;?>",
                            navigation_status: "<?php echo $feature->vessel2_navigation_status;?>",
                            last_position_UTC: "<?php echo $feature->vessel2_last_position_UTC;?>",
                            port: "<?php echo $feature->port;?>",
                            distance: "<?php echo $feature->distance;?>",
                            last_updated: "<?php echo $feature->last_updated;?>",
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
                
                // Create custom vessel icons
                function createVesselIcon() {
                    var iconUrl;
                    var iconSize = [30, 30];
                    
                    iconUrl = "<?php echo CSM_URL;?>images/ship.png";
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
                            draught: <?php echo $feature->vessel1_draught;?>,
                            navigation_status: "<?php echo $feature->vessel1_navigation_status;?>",
                            port: "<?php echo $feature->port;?>",
                            distance: "<?php echo $feature->distance;?>",
                            last_updated: "<?php echo $feature->last_updated;?>",
                        },
                        '<?php echo $feature->vessel2_uuid;?>': {
                            id: '<?php echo $feature->vessel2_uuid;?>',
                            name: '<?php echo $feature->vessel2_name;?>',
                            type: '<?php echo $feature->vessel2_type;?>',
                            position: [<?php echo $feature->vessel2_lat;?>, <?php echo $feature->vessel2_lon;?>],
                            speed: <?php echo $feature->vessel2_speed;?>,
                            draught: <?php echo $feature->vessel2_draught;?>,
                            navigation_status: "<?php echo $feature->vessel2_navigation_status;?>",
                            port: "<?php echo $feature->port;?>",
                            distance: "<?php echo $feature->distance;?>",
                            last_updated: "<?php echo $feature->last_updated;?>",
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
                        speed: <?php echo $feature->vessel1_speed;?>,
                        port: "<?php echo $feature->port;?>",
                        distance: "<?php echo $feature->distance;?>",
                        last_updated: "<?php echo $feature->last_updated;?>",
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
                                <p>Port: ${vessel.port}</p>
                                <p>Speed: ${vessel.speed} knots</p>
                                <p>Navigation Status: ${vessel.navigation_status}</p>
                                <p>Draught: ${vessel.draught} meters</p>
                                <p>Distance: ${vessel.distance} meters</p>
                                <p>Last Updated: ${vessel.last_updated}</p>
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
                            <p>Speed: ${operation.speed}</p>
                            <p>Port: ${operation.port}</p>
                            <p>Distance: ${operation.distance} meters</p>
                            <p>Last Updated: ${operation.last_updated}</p>
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

                var group = new L.featureGroup(activeLines.concat(Object.values(vesselMarkers)));
                
                vesselsLayer.addLayer(group);
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
        ob_get_clean();
        return $content; 
    }
    
    /**
    * Load custom CSS and JavaScript.
    */
    function coastalynk_enqueue_scripts() : void {
        // Enqueue my styles.
       wp_enqueue_style( 'coastalynk-sts-shortcode-style', CSM_CSS_URL.'sts-shortcode.css' );
       
        // Enqueue my scripts.
        wp_enqueue_script( 'coastalynk-sts-shortcode-front', CSM_JS_URL.'sts-shortcode.js', array("jquery"), time(), true );    
        
    }
}

/**
 * Class instance.
 */
Coastalynk_STS_Shortcode::instance();