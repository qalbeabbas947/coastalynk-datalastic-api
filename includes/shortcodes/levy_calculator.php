<?php
/**
 * Levy Calculation shortcode page
 *
 * Do not allow directly accessing this file.
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Coastalynk_Levy_Calculator_Shortcode
 */
class Coastalynk_Levy_Calculator_Shortcode {

    private static $instance = null;

    /**
     * @since 1.0
     * @return $this
     */
    public static function instance() {

        if ( is_null( self::$instance ) && ! ( self::$instance instanceof Coastalynk_Levy_Calculator_Shortcode ) ) {

            self::$instance = new self;

            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Coastalynk_Levy_Calculator_Shortcode hooks
     */
    private function hooks() {
        add_shortcode( 'Coastalynk_Levy_Calculator', [ $this, 'shortcode_body' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'coastalynk_enqueue_scripts' ] );
    }
    
    /**
     * Create shortcode
     */
    public function shortcode_body( $atts ) {
        global $wpdb;
        
        $coatalynk_levy_calculator_page_rate1 	= get_option( 'coatalynk_levy_calculator_page_rate1' );
        if( floatval( $coatalynk_levy_calculator_page_rate1 ) <= 0 ) {
             $coatalynk_levy_calculator_page_rate1 	= 0.5;
        }

        $coatalynk_levy_calculator_page_rate2 	= get_option( 'coatalynk_levy_calculator_page_rate2' );
        if( floatval( $coatalynk_levy_calculator_page_rate2 ) <= 0 ) {
             $coatalynk_levy_calculator_page_rate2 	= 0.3;
        }

        ob_start();
        $apiKey 	= get_option( 'coatalynk_datalastic_apikey' );
        ?>
        
            <div class="vessel-dashboard-container">
                <?php coastalynk_side_bar_menu(); ?>

                <div class="datalastic-container">
                    <input type="image" class="coastlynk-menu-dashboard-open-close-burger" src="<?php echo CSM_IMAGES_URL;?>burger-port-page.png" />
                    <div class="coastalynk-calculator-container">
                        <h1 class="coastalynk_blog_heading"><?php _e( "Levy Calculator", "castalynkmap" );?></h1>
                        <p class="coastalynk-calculator-subtitle"><?php _e( "Enter GT/NT values to calculate the levy amount", "castalynkmap" );?></p>
                        
                        <div class="coastalynk-calculator-form-group">
                            <label for="coastalynk-calculator-gt"><?php _e( "Gross Tonnage (GT)", "castalynkmap" );?></label>
                            <input type="number" id="coastalynk-calculator-gt" placeholder="<?php _e( "Enter GT value", "castalynkmap" );?>" min="0" step="0.01">
                            <div class="coastalynk-calculator-error-message" id="coastalynk-calculator-gt-error"><?php _e( "Please enter a valid GT value", "castalynkmap" );?></div>
                        </div>
                        
                        <div class="coastalynk-calculator-form-group">
                            <label for="coastalynk-calculator-nt"><?php _e( "Net Tonnage (NT)", "castalynkmap" );?></label>
                            <input type="number" id="coastalynk-calculator-nt" placeholder="<?php _e( "Enter NT value", "castalynkmap" );?>" min="0" step="0.01">
                            <div class="coastalynk-calculator-error-message" id="coastalynk-calculator-nt-error"><?php _e( "Please enter a valid NT value", "castalynkmap" );?></div>
                        </div>
                        <div class="coastalynk-calculator-result-container">
                            <div class="coastalynk-calculator-result-label"><?php _e( "Total Levy Amount", "castalynkmap" );?></div>
                            <div class="coastalynk-calculator-result-value" id="coastalynk-calculator-result-value">-</div>
                        </div>
                        
                        
                        <button class="coastalynk-calculator-calculate-btn" id="coastalynk-calculator-calculate"><?php _e( "Calculate Levy", "castalynkmap" );?></button>
                        <button class="coastalynk-calculator-reset-btn" id="coastalynk-calculator-reset-btn"><?php _e( "Reset", "castalynkmap" );?></button>
                        <div class="coastalynk-calculator-formula-info">
                            <strong><?php _e( "Formula:", "castalynkmap" );?></strong> <?php _e( "Levy = (GT X Rate1) + (NT X Rate2)", "castalynkmap" );?><br>
                            <small><?php _e( "Current rates: Rate1 =", "castalynkmap" );?> <?php echo $coatalynk_levy_calculator_page_rate1;?>, <?php _e( "Rate2 =", "castalynkmap" );?> <?php echo $coatalynk_levy_calculator_page_rate2;?></small>
                        </div>
                        
                    </div>
                </div>
            </div>
            
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
        wp_enqueue_style( 'coastalynk-levy-calculator-shortcode-style', CSM_CSS_URL.'frontend/levy-calculator-shortcode.css?'.time() );

        // Enqueue my scripts.
        wp_enqueue_script( 'coastalynk-levy-calculator-shortcode-script', CSM_JS_URL.'frontend/levy-calculator-shortcode.js', array("jquery"), time(), true ); 
        
        $coatalynk_levy_calculator_page_rate1 	= get_option( 'coatalynk_levy_calculator_page_rate1' )??"0.5";
        $coatalynk_levy_calculator_page_rate2 	= get_option( 'coatalynk_levy_calculator_page_rate2' )??"0.3";
        wp_localize_script( 'coastalynk-levy-calculator-shortcode-script', 'COSTALYNK_CALC_VARS', [          
                'rate1' => floatval($coatalynk_levy_calculator_page_rate1), 
                'rate2' => floatval($coatalynk_levy_calculator_page_rate2),
            ] );
        
    }
}

/**
 * Class instance.
 */
Coastalynk_Levy_Calculator_Shortcode::instance();