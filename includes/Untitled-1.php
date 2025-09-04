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
                        <p class="subtitle"><?php _e( "Last updated:", "castalynkmap" );?> <?php echo $last_position_utc;?></p>
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
                
                vesselData.features.forEach(feature => {
                    
                    const marker = L.circleMarker([
                        feature.geometry.coordinates[1],
                        feature.geometry.coordinates[0]
                    ], {
                        radius: 4,
                        fillColor: "#ff7800",
                        color: "#000",
                        weight: 1,
                        opacity: 1,
                        fillOpacity: 0.8
                    });
                    
                    const props = feature.properties;console.log(props);
                    marker.bindPopup(`
                        <table cellspacing="4"><tr><td><img src="${props.country_iso}.jpg" alt="${props.name}" style="width: 80px; height: auto;"></td>
                        <td><strong>${props.name}</strong><br>
                        MMSI: ${props.mmsi}<br>
                        IMO: ${props.imo}<br></td></tr></table>
                        Type: ${props.type}<br>
                        Type Specific: ${props.type_specific}<br>
                        Speed: ${props.speed}<br>
                        Navigation Status: ${props.navigation_status}<br>
                        Near: ${props.port} Port<br>
                        Last Update: ${props.last_updated}
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