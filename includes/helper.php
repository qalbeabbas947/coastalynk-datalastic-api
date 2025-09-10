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
    ?>
        <div class="coastlynk-vessel-dashboard-menu" id="coastlynk-vessel-dashboard-menu">
            <ul class="coastlynk-vessel-menu-items">
                <li class="coastlynk-vessel-menu-item active">
                    <div class="coastlynk-vessel-menu-icon">
                        <i class="fa-solid fa-anchor"></i>                        
                    </div>
                    <div class="coastlynk-vessel-menu-text"><?php _e( "Ports", "castalynkmap" );?></div>
                    <div class="coastlynk-menu-toggle" id="coastlynk-menu-toggle-close">
                        <i class="fa-regular fa-circle-xmark"></i>                        
                    </div>
                    <div class="coastlynk-menu-toggle" id="coastlynk-menu-toggle-open">
                        <i class="fa-solid fa-angle-right"></i>                       
                    </div>
                </li>
                <li class="coastlynk-vessel-menu-item">
                    <div class="coastlynk-vessel-menu-icon">
                        <i class="fa-solid fa-ship"></i>                        
                    </div>
                    <div class="coastlynk-vessel-menu-text"><?php _e( "Vessels", "castalynkmap" );?></div>
                </li>
                <li class="coastlynk-vessel-menu-item">
                    <div class="coastlynk-vessel-menu-icon">
                        <i class="fa-solid fa-map"></i>
                    </div>
                    <div class="coastlynk-vessel-menu-text"><?php _e( "STS Map", "castalynkmap" );?></div>
                </li>
                <li class="coastlynk-vessel-menu-item">
                    <div class="coastlynk-vessel-menu-icon">
                        <i class="fa-solid fa-map-location"></i>
                    </div>
                    <div class="coastlynk-vessel-menu-text"><?php _e( "SBM MAP", "castalynkmap" );?></div>
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
