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
        
        wp_enqueue_style( 'coastalynk-sts-shortcode-style' );
        wp_enqueue_script( 'coastalynk-sts-shortcode-front' );

        ob_start();
          
            // Your Datalastic API Key
        $apiKey 	= get_option( 'coatalynk_datalastic_apikey' );
        $total_port_vessels = [];
        // 1. Define the center points (latitude, longitude) of our ports
        $table_name = $wpdb->prefix . 'coastalynk_ports';
        $port_data = $wpdb->get_results("SELECT * FROM $table_name where country_iso='NG' and port_type in( 'Port', 'Coastal Zone', 'Territorial Zone', 'EEZ' ) order by title");
        $ports = []; 
        $port_exports = [];  
        $table_name_sts = $wpdb->prefix . 'coastalynk_sts';
        foreach( $port_data as $port ) {
            if( $port->lat && $port->lon ) {
                $total = $wpdb->get_var( "SELECT count(id) as total FROM $table_name_sts where is_disappeared='No' and port='".$port->title."'" );
                if( intval( $total ) > 0 ) {
                    $port_exports[$port->title] = [$port->lat, $port->lon, $total, $port->port_id];
                }

                if( $port->port_type == 'Port' ) {
                    $ports[$port->title] = [$port->lat, $port->lon];
                }
            }
        }

        $vessel_data = $wpdb->get_results( "SELECT * FROM $table_name_sts where is_disappeared='No'" );
        ?>
        
        <div class="vessel-dashboard-container">
            <?php coastalynk_side_bar_menu();?>
            <div class="datalastic-container">
                <header>
                    <input type="image" class="coastlynk-menu-dashboard-open-close-burger" src="<?php echo CSM_IMAGES_URL;?>burger-port-page.png" />
                            
                    <div class="controls">
                        <div class="coastalynk-port-menu-container">
                            <button id="coastalynk-port-prev-btn">&lt;</button>
                            <div class="port-selector coastalynk-port-selector coastalynk-port-scroll-menu">
                                <button class="port-button active" data-port="all"><?php _e( "All Ports", "castalynkmap" );?></button>
                                <?php
                                    foreach( $ports as $port => $coords ) {
                                        echo '<button class="port-button" data-port="'.$port.'">'.$port.'</button>';
                                    }
                                ?>
                                
                            </div>
                            <button id="coastalynk-port-next-btn">&gt;</button>
                        </div>
                        <div class="view-options">
                            <button class="view-button coastalynk-map-view-button-first active" data-view="heatmap"><?php _e( "Heatmap", "castalynkmap" );?></button>
                            <button class="view-button" data-view="vessels"><?php _e( "Vessels", "castalynkmap" );?></button>
                            <button class="view-button coastalynk-map-view-button-last" data-view="ports"><?php _e( "Ports", "castalynkmap" );?></button>
                        </div>
                    </div>
                </header>
                <div class="dashboard-sts">
                     <div id="map" style="height: 80vh; width: 100%; min-width:100%;"></div>
                </div>
                
                <form method="post" id="coastalynk-port-sts-history-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" name="coastalynk-port-sts-history-form">
                    <div class="section-title d-flex justify-content-between mb-0 leftalign">
                        <h2><?php _e( "STS History", "castalynkmap" );?></h2>               
                    </div>
                    <div class="caostalynk-sts-history-header-buttons">
                        <div class="coastalynk-sts-history-ports">
                            <select id="caostalynk_history_ddl_ports" class="coastalynk-sts-select2-js" name="caostalynk_history_ddl_ports">
                                <option value=""><?php _e( "All Ports", "castalynkmap" );?></option>
                                <?php foreach( $port_exports as $key=>$port ) { ?>
                                    <option value="<?php echo $key;?>"><?php echo $key.'('.$port[2].')';?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="coastalynk-sts-history-date-range">
                            <input id="caostalynk_sts_history_range" type="text" class="coastalynk-date-range-js" name="caostalynk_sts_history_range">
                        </div>
                        <div class="coastalynk-sts-history-buttons">
                            <button class="coastalynk-sts-history-buttons-export-csv" data-id="export-csv">
                                <?php _e( "Export CSV", "castalynkmap" );?>
                                <div id="coastalynk-column-loader" class="coastalynk-column-loader" style="display:none;">
                                    <div id="coastalynk-column-blockG_1" class="coastalynk-column-blockG"></div>
                                    <div id="coastalynk-column-blockG_2" class="coastalynk-column-blockG"></div>
                                    <div id="coastalynk-column-blockG_3" class="coastalynk-column-blockG"></div>
                                </div>
                            </button>
                            <button class="coastalynk-sts-history-buttons-export-pdf" data-id="export-pdf">
                                <?php _e( "Export PDF", "castalynkmap" );?>
                                <div id="coastalynk-column-loader" class="coastalynk-column-loader" style="display:none;">
                                    <div id="coastalynk-column-blockG_1" class="coastalynk-column-blockG"></div>
                                    <div id="coastalynk-column-blockG_2" class="coastalynk-column-blockG"></div>
                                    <div id="coastalynk-column-blockG_3" class="coastalynk-column-blockG"></div>
                                </div>
                            </button>
                        </div>
                        <?php wp_nonce_field( 'coastalynk_sts_history_load', 'coastalynk_sts_history_load_nonce' ); ?>
                        <input type="hidden" id="coastalynk_sts_history_load_action_ctrl" name="action" value="coastalynk_sts_history_load_action_ctrl_csv" />
                    </div>
                </form>
                <div class="coastalynk-sts-table_wrapper">
                    <table id="coastalynk-sts-table" class="display" class="cell-border hover stripe"> 
                        <thead>
                            <tr>
                                <th colspan="5" align="center"><?php _e( "Vessel 1", "castalynkmap" );?></th>
                                <th colspan="5" align="center"><?php _e( "Vessel 2", "castalynkmap" );?></th>
                                <th><?php _e( "Port", "castalynkmap" );?></th>
                                <th colspan="2"></th>
                            </tr>
                            
                            <tr>
                                <th></th>
                                <th><?php _e( "Name", "castalynkmap" );?></th>
                                <!-- <th><?php _e( "MMSI", "castalynkmap" );?></th>
                                <th><?php _e( "IMO", "castalynkmap" );?></th> -->
                                <th><?php _e( "Type", "castalynkmap" );?></th>
                                <!-- <th><?php _e( "Speed", "castalynkmap" );?></th> -->
                                <th><?php _e( "Status", "castalynkmap" );?></th>
                                <th><?php _e( "Draught", "castalynkmap" );?></th>
                                <th></th>
                                <th><?php _e( "Name", "castalynkmap" );?></th>
                                <!-- <th><?php _e( "MMSI", "castalynkmap" );?></th>
                                <th><?php _e( "IMO", "castalynkmap" );?></th> -->
                                <th><?php _e( "Type", "castalynkmap" );?></th>
                                <!-- <th><?php _e( "Speed", "castalynkmap" );?></th> -->
                                <th><?php _e( "Status", "castalynkmap" );?></th>
                                <th><?php _e( "Draught", "castalynkmap" );?></th>
                                <th><?php _e( "Port", "castalynkmap" );?></th>
                                <th><?php _e( "Detail", "castalynkmap" );?></th>
                                <th><?php _e( "Action", "castalynkmap" );?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach( $vessel_data as $vessel ) { 
                                    $attributes = '';
                                    foreach( $vessel as $key=>$val ) {

                                        
                                        if( in_array( $key, ['vessel1_eta', 'vessel1_atd', 'vessel2_eta', 'vessel2_atd', 'last_updated', 'start_date', 'end_date'] ) ) {
                                            $attributes .= ' data-'.$key.' = "'.get_date_from_gmt( $val, CSM_DATE_FORMAT.' '.CSM_TIME_FORMAT ).'"';
                                        } else if( in_array( $key, ['vessel1_draught', 'vessel1_completed_draught', 'vessel2_draught', 'vessel2_completed_draught' ] ) ) {
                                            $attributes .= ' data-'.$key.' = "'.( floatval( $val ) > 0?$val.'m':__( "Pending", "castalynkmap" )).'"';
                                        } else {
                                            $attributes .= ' data-'.$key.' = "'.$val.'"';
                                        }
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <?php 
                                            if( !empty( $vessel->vessel1_country_iso ) ) {
                                                echo '<img src="'.CSM_IMAGES_URL."flags/".strtolower( $vessel->vessel1_country_iso ).".jpg".'" class="coastalyn-flag-port-listing" alt="'.$vessel->vessel1_name.'">';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo $vessel->vessel1_name; ?></td>
                                    <!-- <td><?php echo $vessel->vessel1_mmsi; ?></td>
                                    <td><?php echo $vessel->vessel1_imo; ?></td> -->
                                    <td><?php echo coastalynk_get_vessel_short_types( $vessel->vessel1_type); ?></td>
                                    <!-- <td><?php echo $vessel->vessel1_speed; ?></td> -->
                                    <td class="<?php echo coastalynk_get_vessel_navigation_status_class( $vessel->vessel1_navigation_status); ?>"><?php echo $vessel->vessel1_navigation_status; ?></td>
                                    <td>
                                        <input type="button" class="coastalynk-retrieve-draught-btn" data-name="<?php echo $vessel->vessel1_name; ?>" data-uuid="<?php echo $vessel->vessel1_uuid; ?>" value="<?php _e( "Draught", "castalynkmap" );?>">
                                        <div id="coastalynk-column-loader" class="coastalynk-column-loader" style="display:none;">
                                            <div id="coastalynk-column-blockG_1" class="coastalynk-column-blockG"></div>
                                            <div id="coastalynk-column-blockG_2" class="coastalynk-column-blockG"></div>
                                            <div id="coastalynk-column-blockG_3" class="coastalynk-column-blockG"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                            if( !empty( $vessel->vessel2_country_iso ) ) {
                                                echo '<img src="'.CSM_IMAGES_URL."flags/".strtolower( $vessel->vessel2_country_iso ).".jpg".'" class="coastalyn-flag-port-listing" alt="'.$vessel->vessel2_name.'">';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo $vessel->vessel2_name; ?></td>
                                    <!-- <td><?php echo $vessel->vessel2_mmsi; ?></td>
                                    <td><?php echo $vessel->vessel2_imo; ?></td> -->
                                    <td>
                                        <?php echo coastalynk_get_vessel_short_types( $vessel->vessel2_type); ?>
                                    </td>
                                    <!-- <td><?php echo $vessel->vessel2_speed; ?></td> -->
                                    <td class="<?php echo coastalynk_get_vessel_navigation_status_class( $vessel->vessel2_navigation_status); ?>"><?php echo $vessel->vessel2_navigation_status; ?></td>
                                    <td>
                                        <input type="button" class="coastalynk-retrieve-draught-btn" data-name="<?php echo $vessel->vessel2_name; ?>" data-uuid="<?php echo $vessel->vessel2_uuid; ?>" value="<?php _e( "Draught", "castalynkmap" );?>">
                                        <div id="coastalynk-column-loader" class="coastalynk-column-loader" style="display:none;">
                                            <div id="coastalynk-column-blockG_1" class="coastalynk-column-blockG"></div>
                                            <div id="coastalynk-column-blockG_2" class="coastalynk-column-blockG"></div>
                                            <div id="coastalynk-column-blockG_3" class="coastalynk-column-blockG"></div>
                                        </div>
                                    </td>
                                    <td><?php echo $vessel->port; ?></td>
                                    <td>
                                        <input type="button" class="coastalynk-sts-retrieve-popup-btn" <?php echo $attributes;?> value="<?php _e( "Details", "castalynkmap" );?>">
                                        <div id="coastalynk-column-loader" style="display:none;">
                                            <div id="coastalynk-column-blockG_1" class="coastalynk-column-blockG"></div>
                                            <div id="coastalynk-column-blockG_2" class="coastalynk-column-blockG"></div>
                                            <div id="coastalynk-column-blockG_3" class="coastalynk-column-blockG"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="button" class="coastalynk-sts-focus-marker-btn" data-lon="<?php echo $vessel->vessel1_lon; ?>" data-lat="<?php echo $vessel->vessel1_lat; ?>" value="<?php _e( "View on Map", "castalynkmap" );?>">
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="coastalynk-sts-popup-overlay"></div>
            <div class="coastalynk-sts-popup-content">
                <h2>
                    <div>
                        <?php _e( "STS Activity", "castalynkmap" );?>
                        <span class="coastalynk-popup-approved-zone"><i class="fa fa-check-square" aria-hidden="true"></i></span>
                        <span class="coastalynk-popup-unapproved-zone"><i class="fa fa-exclamation" aria-hidden="true"></i></span>

                    </div>
                    <div id="coastalynk-sts-popup-close"><i class="fa fa-times" aria-hidden="true"></i></div>
                </h2>
                <div class="coastalynk-sts-popup-top-bar">
                    <div class="coastalynk-sts-popup-top-bar-remarks">
                        <?php _e( "Remarks:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-remarks"></span>
                    </div>
                </div>
                <div class="coastalynk-sts-popup-content-boxes">
                    <div class="coastalynk-sts-popup-content-box">
                        <h3><?php _e( "Vessel 1", "castalynkmap" );?><span title = "<?php _e( "Mother Ship", "castalynkmap" );?>" class="coastalynk-popup-vessel1-parent" style="display:none;"><i class="fa fa-cogs" aria-hidden="true"></i></span></h3>
                        <ul class="coastalynk-sts-popup-content-box-list">
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Name:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_name"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "MMSI:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_mmsi"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "IMO:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_imo"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Tonnage:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_tonnage"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Type:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_type"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Sub Type:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_type_specific"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Before Draught:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_draught"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "After Draught:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_completed_draught"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Country:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_country_iso"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Speed:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_speed"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Nav. Status:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_navigation_status"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Signal:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1-ais-signal"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Last Position:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel1_last_position_UTC"></span></li>
                            
                            <!-- <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Condition:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel_condition1"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Cargo ETA:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-cargo_eta1"></span></li> -->
                        </ul>
                    </div>
                    <div class="coastalynk-sts-popup-content-box">
                        <h3><?php _e( "Vessel 2", "castalynkmap" );?><span title = "<?php _e( "Mother Ship", "castalynkmap" );?>" class="coastalynk-popup-vessel2-parent" style="display:none;"><i class="fa fa-cogs" aria-hidden="true"></i></span></h3>
                        <ul class="coastalynk-sts-popup-content-box-list">
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Name:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_name"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "MMSI:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_mmsi"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "IMO:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_imo"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Tonnage:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_tonnage"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Type:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_type"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Sub Type:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_type_specific"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Before Draught:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_draught"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "After Draught:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_completed_draught"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Country:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_country_iso"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Speed:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_speed"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Nav. Status:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_navigation_status"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Signal:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2-ais-signal"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Last Position:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel2_last_position_UTC"></span></li>
                            <!-- <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Condition:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-vessel_condition2"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Cargo ETA:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-cargo_eta2"></span></li> -->
                        </ul>
                    </div>
                    <div class="coastalynk-sts-popup-content-box">
                        <h3><?php _e( "Destination", "castalynkmap" );?></h3>
                        <ul class="coastalynk-sts-popup-content-box-list">
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Port:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-port"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Ref#:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-event_ref_id"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "STS Zone:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-zone_type"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "STS Ship:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-zone_ship"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Zone:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-zone_terminal_name"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Start Date:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-start_date"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "End Date:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-end_date"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Cargo Type:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-cargo_category_type"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Estimated Cargo:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-estimated_cargo"></span></li>
                        </ul>
                    </div>
                    <div class="coastalynk-sts-popup-content-box">
                        <h3><?php _e( "Current Status", "castalynkmap" );?></h3>
                        <ul class="coastalynk-sts-popup-content-box-list">
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Risk Status:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-risk_level"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Distance(NM):", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-current_distance_nm"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Stationary(hours):", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-stationary_duration_hours"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Proximity Consistency:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-proximity_consistency"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Data Points Analyzed:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-data_points_analyzed"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Operation Mode:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-operationmode"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Percentage:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-event_percentage"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Status:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-status"></span></li>
                            <li><span class="fa-li"><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php _e( "Last Updated:", "castalynkmap" );?> <span class="coastalynk-sts-popup-content-last_updated"></span></li>
                        </ul>
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
                            last_position_UTC: "<?php echo get_date_from_gmt( $feature->vessel1_last_position_UTC, CSM_DATE_FORMAT.' '.CSM_TIME_FORMAT );?>",
                            name: "<?php echo $feature->vessel1_name;?>",
                            port: "<?php echo $feature->port;?>",
                            distance: "<?php echo $feature->distance;?>",
                            last_updated: "<?php echo get_date_from_gmt( $feature->last_updated, CSM_DATE_FORMAT.' '.CSM_TIME_FORMAT );?>"
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
                            last_position_UTC: "<?php echo get_date_from_gmt( $feature->vessel2_last_position_UTC, CSM_DATE_FORMAT.' '.CSM_TIME_FORMAT );?>",
                            port: "<?php echo $feature->port;?>",
                            distance: "<?php echo $feature->distance;?>",
                            last_updated: "<?php echo get_date_from_gmt( $feature->last_updated, CSM_DATE_FORMAT.' '.CSM_TIME_FORMAT );?>",
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
                            imo: "<?php echo $feature->vessel1_imo;?>",
                            name: '<?php echo $feature->vessel1_name;?>',
                            type: '<?php echo $feature->vessel1_type;?>',
                            type_specific: '<?php echo $feature->vessel1_type_specific;?>',
                            position: ['<?php echo $feature->vessel1_lat;?>', '<?php echo $feature->vessel1_lon;?>'],
                            speed: '<?php echo $feature->vessel1_speed;?>',
                            draught: '<?php echo ( floatval( $feature->vessel1_draught ) > 0?$feature->vessel1_draught:__( "Pending", "castalynkmap" ));?>',
                            navigation_status: "<?php echo $feature->vessel1_navigation_status;?>",
                            country_iso: "<?php echo $feature->vessel1_country_iso;?>",
                            flag: "<?php echo CSM_IMAGES_URL."flags/".strtolower( $feature->vessel1_country_iso ).".jpg"; ?>",
                            lat: '<?php echo $feature->vessel1_lat;?>',
                            lng: '<?php echo $feature->vessel1_lon;?>',
                            port: "<?php echo $feature->port;?>",
                            distance: "<?php echo $feature->distance;?>",
                            last_updated: "<?php echo get_date_from_gmt( $feature->last_updated, CSM_DATE_FORMAT.' '.CSM_TIME_FORMAT );?>",
                        },
                        '<?php echo $feature->vessel2_uuid;?>': {
                            id: '<?php echo $feature->vessel2_uuid;?>',
                            imo: "<?php echo $feature->vessel2_imo;?>",
                            name: '<?php echo $feature->vessel2_name;?>',
                            type: '<?php echo $feature->vessel2_type;?>',
                            type_specific: '<?php echo $feature->vessel2_type_specific;?>',
                            position: ['<?php echo $feature->vessel2_lat;?>', '<?php echo $feature->vessel2_lon;?>'],
                            lat: '<?php echo $feature->vessel2_lat;?>',
                            lng: '<?php echo $feature->vessel2_lon;?>',
                            speed: '<?php echo $feature->vessel2_speed;?>',
                            country_iso: "<?php echo $feature->vessel2_country_iso;?>",
                            draught: '<?php echo ( floatval( $feature->vessel2_draught ) > 0?$feature->vessel2_draught:__( "Pending", "castalynkmap" ));?>',
                            navigation_status: "<?php echo $feature->vessel2_navigation_status;?>",
                            flag: "<?php echo CSM_IMAGES_URL."flags/".strtolower( $feature->vessel2_country_iso ).".jpg"; ?>",
                            port: "<?php echo $feature->port;?>",
                            distance: "<?php echo $feature->distance;?>",
                            last_updated: "<?php echo get_date_from_gmt( $feature->last_updated, CSM_DATE_FORMAT.' '.CSM_TIME_FORMAT );?>",
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
                        speed: '<?php echo $feature->vessel1_speed;?>',
                        port: "<?php echo $feature->port;?>",
                        distance: "<?php echo number_format($feature->distance);?>",
                        last_updated: "<?php echo get_date_from_gmt( $feature->last_updated, CSM_DATE_FORMAT.' '.CSM_TIME_FORMAT );?>",
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
                                                <div>IMO: ${vessel.imo}</div>
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
                                        <tr class="coastalynk-sbm-marker-second-row">
                                            <td colspan="3">
                                                <b><?php _e( "Tonnage", "castalynkmap" );?></b><br>
                                                <div><a href="javascript:;" class="coastlynk-display-sts-tonnage" data-id="${vessel.id}" data-name="${vessel.name}">Show Tonnage</a></div>
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
                    
                    marker.bindPopup(`<strong>${name}</strong>`);
                    portsLayer.addLayer(marker);
                });
                
                // // Add ports layer by default
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
        wp_register_style( 'coastalynk-sts-shortcode-style', CSM_CSS_URL.'/frontend/sts-shortcode.css?'.time() );

        // Enqueue my scripts.
        wp_register_script( 'coastalynk-sts-shortcode-front', CSM_JS_URL.'/frontend/sts-shortcode.js', array("jquery"), time(), true );
        wp_localize_script( 'coastlynk-map-js', 'COSTALYNKVARS', [          
                'ajaxURL' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce('coastalynk_secure_ajax_nonce') // Create nonce
            ] ); 
    }
}

/**
 * Class instance.
 */
Coastalynk_STS_Shortcode::instance();