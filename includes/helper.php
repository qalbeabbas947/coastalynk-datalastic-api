<?php
/**
 * Helper functions
 *
 * Do not allow directly accessing this file.
 */

 
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Renders the sidebar expandable menu
 */
function coastalynk_side_bar_menu() {

    if( ! is_page() ) {
        return;
    }

    global $post;
    
    $coatalynk_ports_page 	= get_option( 'coatalynk_ports_page' );
    $coatalynk_vessels_page = get_option( 'coatalynk_vessels_page' );
    $coatalynk_sbm_page 	= get_option( 'coatalynk_sbm_page' );
    $coatalynk_sts_page 	= get_option( 'coatalynk_sts_page' );

    ?>
        <div class="coastlynk-vessel-dashboard-menu" id="coastlynk-vessel-dashboard-menu">
            <ul class="coastlynk-vessel-menu-items">
                <li class="coastlynk-vessel-menu-item  <?php echo intval($post->ID)==intval($coatalynk_ports_page)?'active':'';?>">
                    <div class="coastlynk-vessel-menu-icon">
                        <i class="fa-solid fa-anchor"></i>                        
                    </div>
                    <div class="coastlynk-vessel-menu-text"><a href="<?php echo get_permalink($coatalynk_ports_page);?>"><?php _e( "Ports", "castalynkmap" );?></a></div>
                    <div class="coastlynk-menu-toggle" id="coastlynk-menu-toggle-close">
                        <a href="<?php echo get_permalink($coatalynk_ports_page);?>"><i class="fa-regular fa-circle-xmark"></i></a>                    
                    </div>
                    <div class="coastlynk-menu-toggle coastlynk-menu-toggle-open">
                        <i class="fa-solid fa-angle-right"></i>                       
                    </div>
                </li>
                <li class="coastlynk-vessel-menu-item <?php echo intval($post->ID)==intval($coatalynk_vessels_page)?'active':'';?>">
                    <div class="coastlynk-vessel-menu-icon">
                        <a href="<?php echo get_permalink($coatalynk_vessels_page);?>"><i class="fa-solid fa-ship"></i></a>                       
                    </div>
                    <div class="coastlynk-vessel-menu-text"><a href="<?php echo get_permalink($coatalynk_vessels_page);?>"><?php _e( "Vessels", "castalynkmap" );?></a></div>
                </li>
                <li class="coastlynk-vessel-menu-item <?php echo $post->ID==$coatalynk_sbm_page?'active':'';?>">
                    <div class="coastlynk-vessel-menu-icon">
                        <a href="<?php echo get_permalink($coatalynk_sbm_page);?>"><i class="fa-solid fa-map"></i></a>
                    </div>
                    <div class="coastlynk-vessel-menu-text"><a href="<?php echo get_permalink($coatalynk_sbm_page);?>"><?php _e( "SBM Map", "castalynkmap" );?></a></div>
                </li>
                <li class="coastlynk-vessel-menu-item <?php echo $post->ID==$coatalynk_sts_page?'active':'';?>">
                    <div class="coastlynk-vessel-menu-icon">
                        <a href="<?php echo get_permalink($coatalynk_sts_page);?>"><i class="fa-solid fa-map-location"></i></a>
                    </div>
                    <div class="coastlynk-vessel-menu-text"><a href="<?php echo get_permalink($coatalynk_sts_page);?>"><?php _e( "STS MAP", "castalynkmap" );?></a></div>
                </li>
                <li class="coastlynk-vessel-menu-item">
                    <div class="coastlynk-vessel-menu-icon">
                        <i class="fa-solid fa-database"></i>                       
                    </div>
                    <div class="coastlynk-vessel-menu-text"><?php _e( "Historical Data", "castalynkmap" );?></div>
                </li>
                <li class="coastlynk-vessel-menu-item">
                    <div class="coastlynk-vessel-menu-icon">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <div class="coastlynk-vessel-menu-text"><?php _e( "Sea Routes", "castalynkmap" );?></div>
                </li>
                <li class="coastlynk-vessel-menu-item">
                    <div class="coastlynk-vessel-menu-icon">
                        <i class="fa-solid fa-book"></i>                    
                    </div>
                    <div class="coastlynk-vessel-menu-text"><?php _e( "Reports", "castalynkmap" );?></div>
                </li>
            </ul>
        </div>
    <?php
}
