<?php
/**
 * Abort if this file is accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$coatalynk_datalastic_apikey 	= get_option( 'coatalynk_datalastic_apikey' );

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