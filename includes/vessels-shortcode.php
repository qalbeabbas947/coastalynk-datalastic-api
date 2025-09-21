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
        
        add_action('wp_ajax_coastalynk_load_popup_data', [ $this, 'coastalynk_load_popup_data']);
        add_action('wp_ajax_nopriv_coastalynk_load_popup_data', [ $this, 'coastalynk_load_popup_data']);        
    }

    /**
     * load popup data
     */
    public function coastalynk_load_popup_data( ) {

        global $wpdb;
        
        if ( ! check_ajax_referer( 'coastalynk_front_vessel_shortcode', 'nonce', false ) ) {
            wp_send_json_error( __( 'Security nonce check failed. Please refresh the page.', "castalynkmap" ) );
            wp_die();
        }

        $uuid = sanitize_text_field( $_REQUEST['uuid'] );
        $table_name = $wpdb->prefix . 'coastalynk_vessels';
        $vessel = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name where uuid=%s", $uuid ), ARRAY_A );
        if( isset( $vessel['country_iso'] ) && !empty( $vessel['country_iso'] )) {
            $vessel['flag'] = CSM_IMAGES_URL."flags/".strtolower( $vessel['country_iso'] ).".jpg";
        }

        $vessel['breadth'] = '';
        $vessel['callsign'] = '';
        $vessel['country_iso'] = '';
        $vessel['country_name'] = '';
        $vessel['deadweight'] = '';
        $vessel['draught_avg'] = '';
        $vessel['draught_max'] = '';
        $vessel['gross_tonnage'] = '';
        $vessel['home_port'] = '';
        $vessel['is_navaid'] = '';
        $vessel['length'] = '';
        $vessel['liquid_gas'] = '';
        $vessel['name_ais'] = '';
        $vessel['speed_avg'] = '';
        $vessel['speed_max'] = '';
        $vessel['teu'] = '';
        $vessel['year_built'] = '';
        $api_key = get_option('coatalynk_datalastic_apikey');
        if( !empty( $api_key ) ) {
            $url = sprintf(
                "https://api.datalastic.com/api/v0/vessel_info?api-key=%s&uuid=%s",
                urlencode($api_key),
                $uuid
            );

            // Fetch vessels in area
            $response = file_get_contents($url);
            $data= json_decode($response, true);
            if( !empty( $data['data'] ) ) {
                $data= $data['data'];
                if( !empty( $data ) ) {
                    $vessel['breadth'] = $data['breadth']??'N/A';
                    $vessel['callsign'] = $data['callsign']??'N/A';
                    $vessel['country_iso'] = $data['country_iso']??'N/A';
                    $vessel['country_name'] = $data['country_name']??'N/A';
                    $vessel['deadweight'] = $data['deadweight']??'N/A';
                    $vessel['draught_avg'] = $data['draught_avg']??'N/A';
                    $vessel['draught_max'] = $data['draught_max']??'N/A';
                    $vessel['gross_tonnage'] = $data['gross_tonnage']??'N/A';
                    $vessel['home_port'] = $data['home_port']??'N/A';
                    $vessel['is_navaid'] = $data['is_navaid']??'N/A';
                    $vessel['length'] = $data['length']??'N/A';
                    $vessel['liquid_gas'] = $data['liquid_gas']??'N/A';
                    $vessel['name_ais'] = $data['name_ais']??'N/A';
                    $vessel['speed_avg'] = $data['speed_avg']??'N/A';
                    $vessel['speed_max'] = $data['speed_max']??'N/A';
                    $vessel['teu'] = $data['teu']??'N/A';
                    $vessel['year_built'] = $data['year_built']??'N/A';
                }
            }
        }
        

        echo json_encode($vessel);
        exit;
    }
    
    /**
     * Create shortcode for slideshow
     */
    public function coastalynk_shortcode( $atts ) {
        global $wpdb;

        $vessel_data = [];

        $search_field      = sanitize_text_field( $_REQUEST['coastalynk_vessel_search_text'] );
        $field_type        = sanitize_text_field( $_REQUEST['coastalynk-vessel-search-ddl'] );
        // $type_ddl          = sanitize_text_field( $_REQUEST['coastalynk_vessel_type_ddl'] );
        // $country_ddl       = sanitize_text_field( $_REQUEST['coastalynk_vessel_country_ddl'] );

        $api_key = get_option('coatalynk_datalastic_apikey');
        if ( ! isset( $_POST['coastalynk_vessel_search_field'] ) || ! wp_verify_nonce( $_POST['coastalynk_vessel_search_field'], 'coastalynk_vessel_search' ) ) {
            $table_name = $wpdb->prefix . 'coastalynk_vessels';
            $vessel_data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
            
        } elseif ( empty( $search_field ) ) {
            $table_name = $wpdb->prefix . 'coastalynk_vessels';
            $vessel_data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
        } else {

            switch( $field_type ) {
                case "imo":
                case "uuid":
                case "mmsi":
                    $url = sprintf(
                        "https://api.datalastic.com/api/v0/vessel_pro?api-key=%s&%s=%s",
                        urlencode($api_key),
                        $field_type,
                        $search_field
                    );echo $url;
                    // Fetch vessels in area
                    $response = file_get_contents($url);
                    $pro = json_decode($response, true);
                    if( isset( $pro['data'] ) )
                        $vessel_data[] = $pro['data'];
                    break;
                default:
                    
                    if( empty( $field_type ) ) {
                        $field_type = 'name';
                    }

                    $url = sprintf(
                        "https://api.datalastic.com/api/v0/vessel_find?api-key=%s",
                        urlencode($api_key)
                    );

                    $is_filter_criteria = false;
                    if( !empty( $field_type ) && !empty( $search_field ) ) {
                        $url = sprintf(
                            $url.'&%s=%s',
                            $field_type,
                            $search_field
                        ); 

                        $is_filter_criteria = true;
                    }

                    // if( !empty( $country_ddl ) ) {
                    //     $url = sprintf(
                    //         $url.'&country_iso=%s',
                    //         $country_ddl
                    //     ); 

                    //     $is_filter_criteria = true;
                    // }

                    // if( !empty( $type_ddl ) ) {
                    //     $url = sprintf(
                    //         $url.'&type=%s',
                    //         $type_ddl
                    //     ); 

                    //     $is_filter_criteria = true;
                    // }

                    // if( $is_filter_criteria == false ) {
                    //      $url = $url.'&country_iso=ng';
                    // }
                    // echo $url;
                    // Fetch vessels in area
                    $response = file_get_contents($url);
                    $pro = json_decode($response, true);
                    if( isset( $pro['data'] ) ){
                        $new_data = [];
                        $vessel_data = $pro['data'];
                        foreach(  $vessel_data as $vessel ) {
                            $url = sprintf(
                                "https://api.datalastic.com/api/v0/vessel_pro?api-key=%s&uuid=%s",
                                urlencode($api_key),
                                $vessel['uuid']
                            );
                            
                            // Fetch vessels in area
                            $response = file_get_contents($url);
                            $vessel_pro = json_decode($response, true);
                            if( isset( $vessel_pro['data'] ) ){
                                $vessel_pro = $vessel_pro['data'];
                                $new_data[] = array_merge($vessel_pro, $vessel);
                            }
                                
                        }

                        $vessel_data = $new_data;
                        
                    }
                        
                   break; 
            }
        }

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
        ?>
        
        <div class="vessel-dashboard-container">
            <?php coastalynk_side_bar_menu();?>
            <div class="datalastic-container">
                <header>
                    <input type="image" class="coastlynk-menu-dashboard-open-close-burger" src="<?php echo CSM_IMAGES_URL;?>burger-port-page.png" />
                    <div class="controls">
                        <form method="post" id="coastalynk_vessel_search_form">
                            <div class="coastalynk_vessel_search_options">
                                <div class="coastalynk-vessel-search-parent">
                                    <i class="fa fa-search"></i>
                                    <input type="text" name="coastalynk_vessel_search_text" value="<?php echo $search_field;?>" placeholder="<?php _e( "Search...", "castalynkmap" );?>" id="coastalynk_vessel_search_text" class="coastalynk_vessel_search_text" />
                                    <div class="coastalynk-vessel-filter-types">
                                        <?php
                                            $options = [];
                                            $options['name'] = 'Name';
                                            $options['uuid'] = 'UUID';
                                            $options['mmsi'] = 'MMSI';
                                            $options['imo'] = 'IMO';
                                            // $options['gross_tonnage_min'] = 'Min Gross Tonnage';
                                            // $options['gross_tonnage_max'] = 'Max Gross Tonnage';
                                            // $options['deadweight_min'] = 'Min Dead Weight';
                                            // $options['deadweight_max'] = 'Max Dead Weight';
                                            // $options['length_min'] = 'Min Length';
                                            // $options['length_max'] = 'Max Length';                                        
                                            // $options['breadth_min'] = 'Min Breadth';
                                            // $options['breadth_max'] = 'Max Breadth';
                                            // $options['year_built_min'] = 'Min Year Built';
                                            // $options['year_built_max'] = 'Max Year Built';

                                            coastalynk_display_dropdown( 'coastalynk-vessel-search-ddl', __( "Name", "castalynkmap" ), $field_type, $options );
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="coastalynk-vessel-search-submit">
                                    <input type="submit" id="coastalynk-vessel-search-submit-btn" value="<?php _e( "Search", "castalynkmap" );?>" class="coastalynk-vessel-search-submit-btn">
                                </div>
                            </div>

                            <?php wp_nonce_field( 'coastalynk_vessel_search', 'coastalynk_vessel_search_field' ); ?>
                        </form>
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
                                <th><?php _e( "ATA", "castalynkmap" );?></th>
                                <th><?php _e( "More", "castalynkmap" );?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach( $vessel_data as $data ) { 
                                                                
                                ?>
                                <tr>
                                    <td>
                                        <?php 
                                            if( isset( $data['country_iso'] ) && !empty( $data['country_iso'] )) {
                                                echo '<img src="'.CSM_IMAGES_URL."flags/".strtolower( $data['country_iso'] ).".jpg".'" class="coastalyn-flag-port-listing" alt="'.$data['name'].'">';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo $data['name']; ?></td>
                                    <td><?php echo $data['dest_port']; ?></td>
                                    <td><?php echo $data['mmsi']; ?></td>
                                    <td><?php echo $data['imo']; ?></td>
                                    <td><?php echo $data['destination']; ?></td>
                                    <td><?php echo $data['speed']; ?></td>
                                    <td><?php echo $data['atd_UTC']; ?></td>
                                    <td>
                                        <input type="button" class="coastalynk-retrieve-popup-btn" data-name='<?php echo $data['name']; ?>' data-uuid="<?php echo $data['uuid']; ?>" value="<?php _e( "More", "castalynkmap" );?>">
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
                        <span class="coastalynk-vessel-popup-top-bar-homeport-content">APAPA</span>
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
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "IMO:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-imo"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "MMSI:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-mmsi"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Callsign:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-callsign"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Spec. Type:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-type_specific"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Course:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-course"></span></li>
                            
                        </ul>
                    </div>
                    <div class="coastalynk-vessel-popup-content-box">
                        <h3><?php _e( "Dimensions", "castalynkmap" );?></h3>
                        <ul class="coastalynk-vessel-popup-content-box-list">
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Length:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-length"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Bredth:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-bredth"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Gross Tonnage:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-gross-tonnage"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Dead Weight:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-dead-weight"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Draught:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-draught"></span></li>
                        </ul>
                    </div>
                    <div class="coastalynk-vessel-popup-content-box">
                        <h3><?php _e( "Speed & Destination", "castalynkmap" );?></h3>
                        <ul class="coastalynk-vessel-popup-content-box-list">
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Avg. Speed:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-avg-speed"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Max Speed:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-max-speed"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Speed:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-speed"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Departure Port:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-dept-port"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Destintion Port:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-dest-port"></span></li>
                        </ul>
                    </div>
                    <div class="coastalynk-vessel-popup-content-box">
                        <h3><?php _e( "Current Status", "castalynkmap" );?></h3>
                        <ul class="coastalynk-vessel-popup-content-box-list">
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Last Known Position:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-last-position"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Destination:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-destination"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "ETA:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-eta"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Nav.Status:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-nav-status"></span></li>
                            <li><span class="fa-li"><i class="fas fa-angle-right"></i></span><?php _e( "Heading:", "castalynkmap" );?> <span class="coastalynk-vessel-popup-content-heading"></span></li>
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

                <?php 
                    if( is_array( $vessel_data ) && count( $vessel_data ) > 0 ) {
                        foreach( $vessel_data as $feature ) { ?>
                            vesselData.features.push({
                                type: 'Feature',
                                geometry: {
                                    type: 'Point',
                                    coordinates:[<?php echo $feature['lon'];?>, <?php echo $feature['lat'];?>]
                                },
                                properties: {
                                    uuid: "<?php echo $feature['uuid'];?>",
                                    name: "<?php echo $feature['name'];?>",
                                    mmsi: "<?php echo $feature['mmsi'];?>",
                                    imo: "<?php echo $feature['imo'];?>",
                                    country_iso: "<?php echo $feature['country_iso'];?>",
                                    type: "<?php echo $feature['type'];?>",
                                    type_specific: "<?php echo $feature['type_specific'];?>",
                                    speed: "<?php echo $feature['speed'];?>",
                                    navigation_status: "<?php echo $feature['navigation_status'];?>",
                                    last_position_UTC: "<?php echo $feature['last_position_UTC'];?>",
                                    port: "<?php echo $feature['dest_port'];?>",
                                }
                            });

                <?php } } ?>
                <?php  if( is_array( $vessel_data ) && count( $vessel_data ) > 0 ) { ?>
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
                <?php } ?>
                // Create vessel markers layer
                const vesselsLayer = L.layerGroup();
                
                // Create custom vessel icons
                function createVesselIcon() {
                    var iconUrl;
                    var iconSize = [16, 16];
                    
                    iconUrl = "<?php echo CSM_URL;?>images/ship.png";
                    // Add status indicator
                    var html = '<div style="position:relative"><img src="' + iconUrl + '" width="30" height="30"></div>';
                    
                    return L.divIcon({
                        html: html,
                        className: 'vessel-marker',
                        iconSize: [30, 30],
                        iconAnchor: [15, 15]
                    });
                }

                // Vessel data structure
                var vessels = {
                    <?php 
                        if( is_array( $vessel_data ) && count( $vessel_data ) > 0 ) {
                            foreach( $vessel_data as $feature ) { 
                                if( !empty( $feature['lat'] ) && !empty( $feature['lon'] ) && !empty( $feature['country_iso'] ) ) { 
                                ?>
                            '<?php echo $feature['uuid'];?>': {
                                id: '<?php echo $feature['uuid'];?>',
                                name: "<?php echo $feature['name'];?>",
                                type: "<?php echo $feature['type'];?>",
                                type_specific: "<?php echo $feature['type_specific'];?>",
                                position: [<?php echo $feature['lat'];?>, <?php echo $feature['lon'];?>],
                                speed: "<?php echo $feature['speed'];?>",
                                current_draught: '<?php echo $feature['current_draught'];?>',
                                navigation_status: "<?php echo $feature['navigation_status'];?>",
                                country_iso: "<?php echo $feature['country_iso'];?>",
                                flag: "<?php echo CSM_IMAGES_URL."flags/".strtolower( $feature['country_iso'] ).".jpg"; ?>",
                                lat: "<?php echo number_format($feature['lat'],4);?>",
                                lng: "<?php echo number_format($feature['lon'],4);?>",
                                port: "<?php echo $feature['dest_port'];?>",
                            },
                    <?php } } } ?>
                    
                };

                
                // Initialize vessel markers
                for (var vesselId in vessels) {
                    if (vessels.hasOwnProperty(vesselId)) {
                        var vessel = vessels[vesselId];
                        marker = L.marker(
                            vessel.position,
                            {icon: createVesselIcon()}
                        ).addTo(map);
                        
                        // Add popup to vessel
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
                                <td colspan="2" class="coastalynk-sbm-marker-middle-part">
                                    <table>
                                        <tr class="coastalynk-sbm-marker-first-row">
                                            <td width="50%">
                                                <b><?php _e( "Speed", "castalynkmap" );?></b><br>
                                                ${vessel.speed}
                                            </td>
                                            <td width="50%">
                                                <b><?php _e( "Draught", "castalynkmap" );?></b><br>
                                                ${vessel.current_draught}m
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
                        vesselsLayer.addLayer(marker);
                    }
                }

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
                <?php  if( is_array( $vessel_data ) && count( $vessel_data ) > 0 ) { ?>"Heatmap": heatLayer, <?php } ?>
                "Vessels": vesselsLayer,
                "Ports": portsLayer
            };

                L.control.layers(baseMaps, overlayMaps).addTo(map);

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
                            <?php  if( is_array( $vessel_data ) && count( $vessel_data ) > 0 ) { ?>map.addLayer(heatLayer);<?php } ?>
                            map.removeLayer(vesselsLayer);
                            map.addLayer(portsLayer);
                        } else if (view === 'vessels') {
                            <?php  if( is_array( $vessel_data ) && count( $vessel_data ) > 0 ) { ?>map.removeLayer(heatLayer);<?php } ?>
                            map.addLayer(vesselsLayer);
                            map.addLayer(portsLayer);
                        } else if (view === 'ports') {
                            <?php  if( is_array( $vessel_data ) && count( $vessel_data ) > 0 ) { ?>map.removeLayer(heatLayer);<?php } ?>
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
        wp_localize_script( 'coastalynk-vessels-shortcode-front', 'COSTALYNK_VESSEL_VARS', [          
                'ajaxURL' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce('coastalynk_front_vessel_shortcode') // Create nonce
            ] );
    }
}

/**
 * Class instance.
 */
Coastalynk_Vessel_Shortcode::instance();