<?php
/**
 * STS shortcode page
 *
 * Do not allow directly accessing this file.
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Coastalynk_Vessel_Shortcode
 */
class Coastalynk_Vessel_Shortcode {

    private static $instance = null;

    /**
     * @since 1.0
     * @return $this
     */
    public static function instance() {

        if ( is_null( self::$instance ) && ! ( self::$instance instanceof Coastalynk_Vessel_Shortcode ) ) {

            self::$instance = new self;

            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Coastalynk_Vessel_Shortcode hooks
     */
    private function hooks() {
        add_shortcode( 'Coastalynk_Show_Vessels', [ $this, 'coastalynk_shortcode' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'coastalynk_enqueue_scripts' ] );
    }
    
    /**
     * Create shortcode for slideshow
     */
    public function coastalynk_shortcode( $atts ) {
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
                    <input type="image" class="coastlynk-menu-dashboard-open-close-burger" src="<?php echo CSM_IMAGES_URL;?>burger-port-page.png" />
                    <div class="controls">
                        <div class="coastalynk_vessel_search_options">
                            <div class="coastalynk-vessel-search-parent">
                                <i class="fa fa-search"></i>
                                <input type="text" name="coastalynk-vessel-search-field" placeholder="<?php _e( "Search...", "castalynkmap" );?>" id="coastalynk-vessel-search-field" class="coastalynk-vessel-search-field" />
                                <!-- <select name="coastalynk-vessel-search-ddl" id="coastalynk-vessel-search-ddl" class="coastalynk-vessel-search-ddl">
                                    <option value="name"><?php _e( "Name", "castalynkmap" );?></option>
                                    <option value="name"><?php _e( "UUID", "castalynkmap" );?></option>
                                    <option value="name"><?php _e( "IMO", "castalynkmap" );?></option>
                                </select> -->
                                <div class="coastalynk-vessel-filter-types">
                                    <?php
                                        $options = [];
                                        $options['Name'] = 'Name';
                                        $options['Cargo'] = 'UUID';
                                        $options['Cargo'] = 'Cargo';
                                        coastalynk_display_dropdown( 'coastalynk-vessel-search-ddl', __( "Name", "castalynkmap" ), $options );
                                    ?>
                                </div>
                            </div>
                            <div class="coastalynk-vessel-type-field">
                                <?php
                                    coastalynk_display_dropdown( 'coastalynk-vessel-type-ddl', __( "Type", "castalynkmap" ), coastalynk_vessel_types( ) );
                                ?>
                            </div>
                            <div class="coastalynk-vessel-country-field">
                                <?php
                                    coastalynk_display_dropdown( 'coastalynk-vessel-country-ddl', __( "Country", "castalynkmap" ), coastalynk_countries_list( ) );
                                ?>
                            </div>
                            <div class="coastalynk-vessel-search-submit">
                                <input type="button" id="coastalynk-vessel-search-submit-btn" value="<?php _e( "Search", "castalynkmap" );?>" class="coastalynk-vessel-search-submit-btn">
                            </div>
                        </div>
                    </div>
                </header>
                
                
                
                <div class="dashboard-vessels">
                    
                    <div id="map" style="height: 80vh; width: 100%; min-width:100%;"></div>
                </div>
                <div class="coastalynk-vessel-table-wrapper">
                    <table id="coastalynk-vessel-table" class="display" class="cell-border hover stripe">
                        <thead>
                            <tr>
                                <th></th>
                                <th><?php _e( "Name", "castalynkmap" );?></th>
                                <th><?php _e( "Port", "castalynkmap" );?></th>
                                <th><?php _e( "MMSI", "castalynkmap" );?></th>
                                <th><?php _e( "IMO", "castalynkmap" );?></th>
                                <th><?php _e( "Destination", "castalynkmap" );?></th>
                                <th><?php _e( "Speed", "castalynkmap" );?></th>
                                <th><?php _e( "UTC", "castalynkmap" );?></th>
                                <th><?php _e( "Tonnage", "castalynkmap" );?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $features = [
                                [
                                    'uuid' => '12312312312312',
                                    'country_flag' => 'http://localhost:8089/wp-content/plugins/coastlynkmap/images/flags/lr.jpg',
                                    'name' => 'PRINCESS OGE 1',
                                    'port' => 'APAPA',
                                    'mmsi' => '000100',
                                    'imo' => '1231123',
                                    'destination' => 'aSAsa',
                                    'speed' => 'aSAsa',
                                    'timestamp' => 'aSAsa',
                                ],
                                [
                                    'uuid' => '34334232423423',
                                    'country_flag' => 'http://localhost:8089/wp-content/plugins/coastlynkmap/images/flags/lr.jpg',
                                    'name' => 'PRINCESS OGE 2',
                                    'port' => 'APAPA',
                                    'mmsi' => '000100',
                                    'imo' => '1231123',
                                    'destination' => 'aSAsa',
                                    'speed' => 'aSAsa',
                                    'timestamp' => 'aSAsa',
                                ],
                            ];
                            foreach( $features as $feature ) { ?>
                                <tr>
                                    <td>
                                        <?php 
                                            if( !empty( $feature['country_flag'] ) ) {
                                                echo '<img src="'.$feature['country_flag'].'" class="coastalyn-flag-port-listing" alt="'.$feature['name'].'">';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo $feature['name']; ?></td>
                                    <td><?php echo $feature['port']; ?></td>
                                    <td><?php echo $feature['mmsi']; ?></td>
                                    <td><?php echo $feature['imo']; ?></td>
                                    <td><?php echo $feature['destination']; ?></td>
                                    <td><?php echo $feature['speed']; ?></td>
                                    <td><?php echo 'N/A'; ?></td>
                                    <td>
                                        <input type="button" class="coastalynk-retrieve-popup-btn" data-name="<?php echo $feature['name']; ?>" data-uuid="<?php echo $feature['uuid']; ?>" value="<?php _e( "More", "castalynkmap" );?>">
                                        <div id="coastalynk-column-loader" style="display:none;">
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
            <div class="coastalynk-vessel-popup-overlay"></div>
            <div class="coastalynk-vessel-popup-content">
                <h2>My Popup Title</h2>
                <div class="coastalynk-vessel-popup-top-bar">
                    <div class="coastalynk-vessel-popup-top-bar-country coastalynk-vessel-popup-top-bar-items">
                        <label><?php _e( "Country:", "castalynkmap" );?></label>
                        <div class="coastalynk-vessel-popup-top-bar-country-content">
                            <img class="coastalynk-vessel-popup-top-bar-country-flag"  src="http://localhost:8089/wp-content/plugins/coastlynkmap/images/flags/lr.jpg">  
                            <span class="coastalynk-vessel-popup-top-bar-country-name">NG</span>                      
                        </div>
                    </div>
                    <div class="coastalynk-vessel-popup-top-bar-homeport coastalynk-vessel-popup-top-bar-items">
                        <label><?php _e( "Home Port:", "castalynkmap" );?></label>
                        <div class="coastalynk-vessel-popup-top-bar-homeport-content">APAPA</div>
                    </div>
                    <div class="coastalynk-vessel-popup-top-bar-type coastalynk-vessel-popup-top-bar-items">
                        <label><?php _e( "Type:", "castalynkmap" );?></label>
                        <div class="coastalynk-vessel-popup-top-bar-type-content">Tanker</div>
                    </div>
                    <div class="coastalynk-vessel-popup-top-bar-yearbuilt coastalynk-vessel-popup-top-bar-items">
                        <label><?php _e( "Year Built:", "castalynkmap" );?></label>
                        <div class="coastalynk-vessel-popup-top-bar-yearbuilt-content">2012</div>
                    </div>
                </div>
                <div class="coastalynk-vessel-popup-content-boxes">
                    <div class="coastalynk-vessel-popup-content-box">
                        <h3><?php _e( "Identifiers", "castalynkmap" );?></h3>
                        <ul class="coastalynk-vessel-popup-content-box-list">
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "IMO:", "castalynkmap" );?> 9286592</li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "MMSI:", "castalynkmap" );?> 229609000</li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Callsign:", "castalynkmap" );?> 9HA3445</li>
                        </ul>
                    </div>
                    <div class="coastalynk-vessel-popup-content-box">
                        <h3><?php _e( "Dimensions", "castalynkmap" );?></h3>
                        <ul class="coastalynk-vessel-popup-content-box-list">
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "IMO:", "castalynkmap" );?> 9286592</li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "MMSI:", "castalynkmap" );?> 229609000</li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Callsign:", "castalynkmap" );?> 9HA3445</li>
                        </ul>
                    </div>
                    <div class="coastalynk-vessel-popup-content-box">
                        <h3><?php _e( "Speed & Performance", "castalynkmap" );?></h3>
                        <ul class="coastalynk-vessel-popup-content-box-list">
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "IMO:", "castalynkmap" );?> 9286592</li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "MMSI:", "castalynkmap" );?> 229609000</li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Callsign:", "castalynkmap" );?> 9HA3445</li>
                        </ul>
                    </div>
                    <div class="coastalynk-vessel-popup-content-box">
                        <h3><?php _e( "Current Status", "castalynkmap" );?></h3>
                        <ul class="coastalynk-vessel-popup-content-box-list">
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "IMO:", "castalynkmap" );?> 9286592</li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "MMSI:", "castalynkmap" );?> 229609000</li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Callsign:", "castalynkmap" );?> 9HA3445</li>
                        </ul>
                    </div>
                </div>
                <button id="coastalynk-vessel-popup-close"><?php _e( "Back to Results", "castalynkmap" );?></button>
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
                            type_specific: '<?php echo $feature->vessel1_type_specific;?>',
                            position: [<?php echo $feature->vessel1_lat;?>, <?php echo $feature->vessel1_lon;?>],
                            speed: <?php echo $feature->vessel1_speed;?>,
                            draught: <?php echo $feature->vessel1_draught;?>,
                            navigation_status: "<?php echo $feature->vessel1_navigation_status;?>",
                            country_iso: "<?php echo $feature->vessel1_country_iso;?>",
                            flag: "<?php echo CSM_IMAGES_URL."flags/".strtolower( $feature->vessel1_country_iso ).".jpg"; ?>",
                            lat: <?php echo $feature->vessel1_lat;?>,
                            lng: <?php echo $feature->vessel1_lon;?>,
                            port: "<?php echo $feature->port;?>",
                            distance: "<?php echo $feature->distance;?>",
                            last_updated: "<?php echo $feature->last_updated;?>",
                        },
                        '<?php echo $feature->vessel2_uuid;?>': {
                            id: '<?php echo $feature->vessel2_uuid;?>',
                            name: '<?php echo $feature->vessel2_name;?>',
                            type: '<?php echo $feature->vessel2_type;?>',
                            type_specific: '<?php echo $feature->vessel2_type_specific;?>',
                            position: [<?php echo $feature->vessel2_lat;?>, <?php echo $feature->vessel2_lon;?>],
                            lat: <?php echo $feature->vessel2_lat;?>,
                            lng: <?php echo $feature->vessel2_lon;?>,
                            speed: <?php echo $feature->vessel2_speed;?>,
                            country_iso: "<?php echo $feature->vessel2_country_iso;?>",
                            draught: <?php echo $feature->vessel2_draught;?>,
                            navigation_status: "<?php echo $feature->vessel2_navigation_status;?>",
                            flag: "<?php echo CSM_IMAGES_URL."flags/".strtolower( $feature->vessel2_country_iso ).".jpg"; ?>",
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
                        distance: "<?php echo number_format($feature->distance);?>",
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
                                <td colspan="2" class="coastalynk-sbm-marker-middle-part">
                                    <table>
                                        <tr class="coastalynk-sbm-marker-first-row">
                                            <td width="33%">
                                                <b><?php _e( "Speed", "castalynkmap" );?></b><br>
                                                ${vessel.speed}
                                            </td>
                                            <td width="33%">
                                                <b><?php _e( "Draught", "castalynkmap" );?></b><br>
                                                ${vessel.draught}m
                                            </td>
                                            <td width="33%">
                                                <b><?php _e( "Distance", "castalynkmap" );?></b><br>
                                                ${vessel.distance}m
                                            </td>
                                        </tr>
                                        <tr class="coastalynk-sbm-marker-second-row">
                                            <td colspan="3">
                                                <b><?php _e( "Type Specific", "castalynkmap" );?></b><br>
                                                ${vessel.type_specific}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="coastalynk-sbm-marker-bottom-part">
                                <td>
                                    <b><?php _e( "Nav. Status:", "castalynkmap" );?></b><br>
                                    ${vessel.navigation_status}
                                </td>
                                <td>
                                    <b><?php _e( "Lat:", "castalynkmap" );?></b>
                                    ${vessel.lat}<br>
                                    <b><?php _e( "Lon:", "castalynkmap" );?></b>
                                    ${vessel.lng}
                                </td>
                            </tr>
                        </table>
                        
                            
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

       wp_enqueue_style( 'coastalynk-vessels-shortcode-style', CSM_CSS_URL.'vessel-shortcode.css?'.time() );
       
        // Enqueue my scripts.
        wp_enqueue_script( 'coastalynk-vessels-shortcode-front', CSM_JS_URL.'vessel-shortcode.js', array("jquery"), time(), true ); 
        wp_enqueue_script( 'coastalynk-vessels-dropdown-front', CSM_JS_URL.'dropdown.js', array("jquery"), time(), true ); 
    }
}

/**
 * Class instance.
 */
Coastalynk_Vessel_Shortcode::instance();