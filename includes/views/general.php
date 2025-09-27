<?php
/**
 * Abort if this file is accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$coatalynk_datalastic_apikey 	    = get_option( 'coatalynk_datalastic_apikey' );
$coatalynk_ports_page 	            = get_option( 'coatalynk_ports_page' );
$coatalynk_vessels_page             = get_option( 'coatalynk_vessels_page' );
$coatalynk_sbm_page 	            = get_option( 'coatalynk_sbm_page' );
$coatalynk_sts_page 	            = get_option( 'coatalynk_sts_page' );
$coatalynk_levy_calculator_page 	= get_option( 'coatalynk_levy_calculator_page' );
$coatalynk_levy_calculator_page_rate1 	= get_option( 'coatalynk_levy_calculator_page_rate1' );
$coatalynk_levy_calculator_page_rate2 	= get_option( 'coatalynk_levy_calculator_page_rate2' );

if( floatval( $coatalynk_levy_calculator_page_rate1 ) <= 0 ) {
    $coatalynk_levy_calculator_page_rate1 	= 0.5;
}

if( floatval( $coatalynk_levy_calculator_page_rate2 ) <= 0 ) {
    $coatalynk_levy_calculator_page_rate2 	= 0.3;
}
?>
<div id="general_settings" class="cs_ld_tabs"> 
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <table class="setting-table-wrapper">
            <tbody>
                <tr> 
                    <td width="15%" align="left" valign="top">
						<strong><label align="left" for="coatalynk_datalastic_apikey"><?php _e( 'Datalastic API Key', 'castalynkmap' ); ?></label></strong>
					</td>
                    <td width="85%">
                        <input type="text" id="coatalynk_datalastic_apikey" size="40" name="coatalynk_datalastic_apikey" value="<?php echo get_option( 'coatalynk_datalastic_apikey' ); ?>">
                        <p class="description" style="font-weight: normal;">
                            <?php echo __('Enter the datalastic api key.', "castalynkmap" ); ?>
                        </p>
                    </td>    
                </tr>   
                <tr> 
                    <td align="left" valign="top">
						<strong><label align="left" for="coatalynk_ports_page"><?php _e( 'Ports Page', 'castalynkmap' ); ?></label></strong>
					</td>
                    <td>
                        <?php wp_dropdown_pages( ['selected' => $coatalynk_ports_page, 'name' => 'coatalynk_ports_page', 'show_option_none' => __('Select Ports Page', 'castalynkmap'), 'sort_column' => 'post_title'] );?>
                        <p class="description">
                            <?php echo __('Select the ports page.', "castalynkmap" ); ?>
                        </p>
                    </td>    
                </tr>  
				<tr> 
                    <td align="left" valign="top">
						<strong><label align="left" for="coatalynk_vessels_page"><?php _e( 'Vessels Page', 'castalynkmap' ); ?></label></strong>
					</td>
                    <td>
                        <?php wp_dropdown_pages( ['selected' => $coatalynk_vessels_page, 'name' => 'coatalynk_vessels_page', 'show_option_none' => __('Select Vessels Page', 'castalynkmap'), 'sort_column' => 'post_title'] );?>
                        <p class="description">
                            <?php echo __('Select the vessels page.', "castalynkmap" ); ?>
                        </p>
                    </td>    
                </tr> 
                <tr> 
                    <td align="left" valign="top">
						<strong><label align="left" for="coatalynk_sbm_page"><?php _e( 'SBM Page', 'castalynkmap' ); ?></label></strong>
					</td>
                    <td>
                        <?php wp_dropdown_pages( ['selected' => $coatalynk_sbm_page, 'name' => 'coatalynk_sbm_page', 'show_option_none' => __('Select SBM Page', 'castalynkmap'), 'sort_column' => 'post_title'] );?>
                        <p class="description">
                            <?php echo __('Select the SBM page.', "castalynkmap" ); ?>
                        </p>
                    </td>    
                </tr> 
                <tr> 
                    <td align="left" valign="top">
						<strong><label align="left" for="coatalynk_sts_page"><?php _e( 'STS Page', 'castalynkmap' ); ?></label></strong>
					</td>
                    <td>
                        <?php wp_dropdown_pages( ['selected' => $coatalynk_sts_page, 'name' => 'coatalynk_sts_page', 'show_option_none' => __('Select STS Page', 'castalynkmap'), 'sort_column' => 'post_title'] );?>
                        <p class="description">
                            <?php echo __('Select the STS page.', "castalynkmap" ); ?>
                        </p>
                    </td>    
                </tr>   
                <tr> 
                    <td align="left" valign="top">
						<strong><label align="left" for="coatalynk_levy_calculator_page"><?php _e( 'Levy Calculator Page', 'castalynkmap' ); ?></label></strong>
					</td>
                    <td>
                        <?php wp_dropdown_pages( ['selected' => $coatalynk_levy_calculator_page, 'name' => 'coatalynk_levy_calculator_page', 'show_option_none' => __('Select Levy Calculator Page', 'castalynkmap'), 'sort_column' => 'post_title'] );?>
                        <p class="description">
                            <?php echo __('Select the Levy Calculator page.', "castalynkmap" ); ?>
                        </p>
                    </td>    
                </tr>  
                <tr> 
                    <td align="left" valign="top">
						<strong><label align="left" for="coatalynk_levy_calculator_page_rate1"><?php _e( 'Levy Calculator Rate 1', 'castalynkmap' ); ?></label></strong>
					</td>
                    <td>
                        <input type="number" step="0.1" id="coatalynk_levy_calculator_page_rate1" size="40" name="coatalynk_levy_calculator_page_rate1" value="<?php echo $coatalynk_levy_calculator_page_rate1; ?>">
                           
                        <p class="description">
                            <?php echo __('Select the Levy Calculator Rate 1.', "castalynkmap" ); ?>
                        </p>
                    </td>    
                </tr>
                <tr> 
                    <td align="left" valign="top">
						<strong><label align="left" for="coatalynk_levy_calculator_page_rate2"><?php _e( 'Levy Calculator Rate 1', 'castalynkmap' ); ?></label></strong>
					</td>
                    <td>
                        <input type="number" step="0.1" id="coatalynk_levy_calculator_page_rate2" size="40" name="coatalynk_levy_calculator_page_rate2" value="<?php echo $coatalynk_levy_calculator_page_rate2; ?>">
                           
                        <p class="description">
                            <?php echo __('Select the Levy Calculator Rate 2.', "castalynkmap" ); ?>
                        </p>
                    </td>    
                </tr>
            </tbody>
        </table>
        
        <div class="submit-button" style="padding-top:10px">
            <input type="hidden" value="general" name="current_tab">
            <input type="hidden" name="action" value="save_coastalynkmap_settings">
            <input type="submit" name="save_settings" class="button-primary" value="<?php _e('Update Settings', 'castalynkmap' ); ?>">
        </div>
        <?php wp_nonce_field( 'save_settings_nonce' ); ?>
    </form>
</div>