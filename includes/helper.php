<?php
/**
 * Helper functions
 *
 * Do not allow directly accessing this file.
 */

 
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Renders the list of country names and codes
 */
function coastalynk_countries_list( ) { 
    
    return $country_list = [
        'AF' => 'Afghanistan',
        'AX' => 'Åland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua and Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia (Plurinational State of)',
        'BA' => 'Bosnia and Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'CV' => 'Cabo Verde',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'BQ' => 'Caribbean Netherlands',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo',
        'CD' => 'Congo, Democratic Republic of the',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CW' => 'Curaçao',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'CI' => 'Côte d\'Ivoire',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'SZ' => 'Eswatini (Swaziland)',
        'ET' => 'Ethiopia',
        'FK' => 'Falkland Islands (Malvinas)',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island and Mcdonald Islands',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KP' => 'Korea, North',
        'KR' => 'Korea, South',
        'XK' => 'Kosovo',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Lao People\'s Democratic Republic',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia North',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar (Burma)',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestine',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn Islands',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'ROU' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barthelemy',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts and Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin',
        'PM' => 'Saint Pierre and Miquelon',
        'VC' => 'Saint Vincent and the Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome and Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SX' => 'Sint Maarten',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia and the South Sandwich Islands',
        'SS' => 'South Sudan',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard and Jan Mayen',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syria',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad and Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey (Türkiye)',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks and Caicos Islands',
        'TV' => 'Tuvalu',
        'UM' => 'U.S. Outlying Islands',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VA' => 'Vatican City Holy See',
        'VE' => 'Venezuela',
        'VN' => 'Vietnam',
        'VG' => 'Virgin Islands, British',
        'VI' => 'Virgin Islands, U.S',
        'WF' => 'Wallis and Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe'
    ];
}

/**
 * Returns the user type
 */
function coastalynk_user_type() { 
    $user_type = '';
    $current_user = wp_get_current_user();
    if ( $current_user && $current_user->ID ) {
        $user_roles = $current_user->roles;
        if( in_array( GENERAL_USER, $user_roles ) ) {
           $user_type = GENERAL_USER; 
        }

        if( in_array( SHIPLINE_USER, $user_roles ) ) {
           $user_type = SHIPLINE_USER; 
        }

        if( in_array( PORT_OPERATOR_USER, $user_roles ) ) {
           $user_type = PORT_OPERATOR_USER; 
        }

        if( in_array( REGULATOR_USER, $user_roles ) ) {
           $user_type = REGULATOR_USER; 
        }

        if( in_array( ADMIN_USER, $user_roles ) ) {
           $user_type = ADMIN_USER; 
        }
    }

    if ( $user_type != ADMIN_USER && ! pmpro_hasMembershipLevel() ) {
        $user_type = 'expired'; 
    }

    return $user_type;
}

/**
 * Renders the sidebar expandable menu
 */
function coastalynk_vessel_types( ) { 
    $options = [];
    $options['Anti-Pollution'] = __( "Anti-Pollution", "castalynkmap" );
    $options['Anti-Pollution Vessel'] = __( "Anti-Pollution Vessel", "castalynkmap" );
    $options['Beacon, Cardinal E'] = __( "Beacon, Cardinal E", "castalynkmap" );
    $options['Beacon, Cardinal N'] = __( "Beacon, Cardinal N", "castalynkmap" );
    $options['Beacon, Cardinal S'] = __( "Beacon, Cardinal S", "castalynkmap" );
    $options['Beacon, Cardinal W'] = __( "Beacon, Cardinal W", "castalynkmap" );
    $options['Beacon, Isolated danger'] = __( "Beacon, Isolated danger", "castalynkmap" );
    $options['Beacon, Port Hand'] = __( "Beacon, Port Hand", "castalynkmap" );
    $options['Beacon, Preferred Channel Port hand'] = __( "Beacon, Preferred Channel Port hand", "castalynkmap" );
    $options['Beacon, Preferred Channel Starboard hand'] = __( "Beacon, Preferred Channel Starboard hand", "castalynkmap" );
    $options['Beacon, Safe Water'] = __( "Beacon, Safe Water", "castalynkmap" );
    $options['Beacon, Special Mark'] = __( "Beacon, Special Mark", "castalynkmap" );
    $options['Beacon, Starboard Hand'] = __( "Beacon, Starboard Hand", "castalynkmap" );
    $options['Cardinal Mark E'] = __( "Cardinal Mark E", "castalynkmap" );
    $options['Cardinal Mark N'] = __( "Cardinal Mark N", "castalynkmap" );
    $options['Cardinal Mark S'] = __( "Cardinal Mark S", "castalynkmap" );
    $options['Cardinal Mark W'] = __( "Cardinal Mark W", "castalynkmap" );
    $options['Cargo'] = __( "Cargo", "castalynkmap" );
    $options['Cargo - Hazard A (Major)'] = __( "Cargo - Hazard A (Major)", "castalynkmap" );
    $options['Cargo - Hazard B'] = __( "Cargo - Hazard B", "castalynkmap" );
    $options['Cargo - Hazard C (Minor)'] = __( "Cargo - Hazard C (Minor)", "castalynkmap" );
    $options['Cargo - Hazard D (Recognizable)'] = __( "Cargo - Hazard D (Recognizable)", "castalynkmap" );
    $options['Cargo: Hazardous category A'] = __( "Cargo: Hazardous category A", "castalynkmap" );
    $options['Cargo: Hazardous category B'] = __( "Cargo: Hazardous category B", "castalynkmap" );
    $options['Cargo: Hazardous category C'] = __( "Cargo: Hazardous category C", "castalynkmap" );
    $options['Cargo: Hazardous category D'] = __( "Cargo: Hazardous category D", "castalynkmap" );
    $options['Dive Ops'] = __( "Dive Ops", "castalynkmap" );
    $options['Dive Vessel'] = __( "Dive Vessel", "castalynkmap" );
    $options['Dredger'] = __( "Dredger", "castalynkmap" );
    $options['Fishing'] = __( "Fishing", "castalynkmap" );
    $options['High Speed Craft'] = __( "High Speed Craft", "castalynkmap" );
    $options['HSC'] = __( "HSC", "castalynkmap" );
    $options['Isolated Danger'] = __( "Isolated Danger", "castalynkmap" );
    $options['Law Enforce'] = __( "Law Enforce", "castalynkmap" );
    $options['Law Enforcement'] = __( "Law Enforcement", "castalynkmap" );
    $options['Leading Light Front'] = __( "Leading Light Front", "castalynkmap" );
    $options['Leading Light Rear'] = __( "Leading Light Rear", "castalynkmap" );
    $options['Light Vessel'] = __( "Light Vessel", "castalynkmap" );
    $options['Light, without Sectors'] = __( "Light, without Sectors", "castalynkmap" );
    $options['Light, with Sectors'] = __( "Light, with Sectors", "castalynkmap" );
    $options['Local Vessel'] = __( "Local Vessel", "castalynkmap" );
    $options['Manned VTS'] = __( "Manned VTS", "castalynkmap" );
    $options['Medical Trans'] = __( "Medical Trans", "castalynkmap" );
    $options['Military Ops'] = __( "Military Ops", "castalynkmap" );
    $options['Navigation Aid'] = __( "Navigation Aid", "castalynkmap" );
    $options['OffShore Structure'] = __( "OffShore Structure", "castalynkmap" );
    $options['Other'] = __( "Other", "castalynkmap" );
    $options['Passenger'] = __( "Passenger", "castalynkmap" );
    $options['Pilot Vessel'] = __( "Pilot Vessel", "castalynkmap" );
    $options['Pleasure Craft'] = __( "Pleasure Craft", "castalynkmap" );
    $options['Port Hand Mark'] = __( "Port Hand Mark", "castalynkmap" );
    $options['Port Tender'] = __( "Port Tender", "castalynkmap" );
    $options['Preferred Channel Port Hand'] = __( "Preferred Channel Port Hand", "castalynkmap" );
    $options['Preferred Channel Starboard Hand'] = __( "Preferred Channel Starboard Hand", "castalynkmap" );
    $options['RACON'] = __( "RACON", "castalynkmap" );
    $options['Reference Point'] = __( "Reference Point", "castalynkmap" );
    $options['Reserved'] = __( "Reserved", "castalynkmap" );
    $options['Safe Water'] = __( "Safe Water", "castalynkmap" );
    $options['Sailing Vessel'] = __( "Sailing Vessel", "castalynkmap" );
    $options['SAR'] = __( "SAR", "castalynkmap" );
    $options['SAR Aircraft'] = __( "SAR Aircraft", "castalynkmap" );
    $options['Search and Rescue Aircraft'] = __( "Search and Rescue Aircraft", "castalynkmap" );
    $options['Search and Rescue Vessel'] = __( "Search and Rescue Vessel", "castalynkmap" );
    $options['Spare'] = __( "Spare", "castalynkmap" );
    $options['Special Craft'] = __( "Special Craft", "castalynkmap" );
    $options['Starboard Hand Mark'] = __( "Starboard Hand Mark", "castalynkmap" );
    $options['Tanker'] = __( "Tanker", "castalynkmap" );
    $options['Tanker - Hazard A (Major)'] = __( "Tanker - Hazard A (Major)", "castalynkmap" );
    $options['Tanker - Hazard B'] = __( "Tanker - Hazard B", "castalynkmap" );
    $options['Tanker - Hazard C (Minor)'] = __( "Tanker - Hazard C (Minor)", "castalynkmap" );
    $options['Tanker - Hazard D (Recognizable)'] = __( "Tanker - Hazard D (Recognizable)", "castalynkmap" );
    $options['Tanker: Hazardous category A'] = __( "Tanker: Hazardous category A", "castalynkmap" );
    $options['Tanker: Hazardous category B'] = __( "Tanker: Hazardous category B", "castalynkmap" );
    $options['Tanker: Hazardous category C'] = __( "Tanker: Hazardous category C", "castalynkmap" );
    $options['Tanker: Hazardous category D'] = __( "Tanker: Hazardous category D", "castalynkmap" );
    $options['Tug'] = __( "Tug", "castalynkmap" );
    $options['Unspecified'] = __( "Unspecified", "castalynkmap" );
    $options['WIG'] = __( "WIG", "castalynkmap" );
    $options['Wing In Grnd'] = __( "Wing In Grnd", "castalynkmap" );
    $options['none'] = __( "none", "castalynkmap" );

    return $options;
}

/**
 * Renders the sidebar expandable menu
 */
function coastalynk_display_dropdown( $field_id, $default = '', $selected = '', $options = [] ) {
    ?>
        <div class="coastalynk-ddl-select" id="coastalynk-ddl-select">
            <button type="button" 
                id="coastalynk-ddl-dropdown-button"
                class="coastalynk-ddl-button"
                role="combobox" 
                aria-label="select button"
                aria-haspopup="listbox"
                aria-expanded="false"
                aria-controls="coastalynk-ddl-dropdown">
                <span data-default="<?php echo $default;?>" data-selected="<?php echo $selected;?>" class="coastalynk-ddl-selected-value"><?php echo $default;?></span>
                <span class="coastalynk-ddl-arrow"></span>
            </button>
            
            <div class="coastalynk-ddl-dropdown-wrapper coastalynk-ddl-dropdown-wrapper-hidden">
                <ul class="coastalynk-ddl-dropdown hidden" role="listbox" id="coastalynk-ddl-dropdown" aria-labelledby="coastalynk-ddl-dropdown-button">
                    <?php 
                    $first = '';
                    foreach( $options as $key => $option ) { 
                        if( empty( $first ) ) {
                            $first = $key;
                        }
                        
                        ?>
                        <li role="option" data-value="<?php echo $key;?>" class="<?php echo $selected == $key ? 'coastalynk-ddl-dropdown-selected-li' : '';?>" ><?php echo $option;?></li>
                    <?php } ?>
                    <!-- Clear option to reset selection -->
                    <li role="option" data-value="clear">
                        <span><?php _e( "Clear selection", "castalynkmap" );?></span>
                    </li>
                </ul>
            </div>
            <input type="hidden" name="<?php echo $field_id;?>" id="<?php echo $field_id;?>" value="" class="coastalynk-ddl-dropdown-button-field" />
        </div>
    <?php
}

/**
 * Renders the sidebar expandable menu
 */
function coastalynk_side_bar_menu() {

    if( ! is_page() ) {
        return;
    }

    $user_type = coastalynk_user_type();
    
    global $post;
    
    $coatalynk_ports_page 	            = get_option( 'coatalynk_ports_page' );
    $coatalynk_vessels_page             = get_option( 'coatalynk_vessels_page' );
    $coatalynk_sbm_page 	            = get_option( 'coatalynk_sbm_page' );
    $coatalynk_sts_page 	            = get_option( 'coatalynk_sts_page' );
    $coatalynk_levy_calculator_page 	= get_option( 'coatalynk_levy_calculator_page' );
    ?>
        <div class="coastlynk-vessel-dashboard-menu" id="coastlynk-vessel-dashboard-menu">
            <ul class="coastlynk-vessel-menu-items">
                <?php if( in_array( $user_type, [REGULATOR_USER, SHIPLINE_USER, PORT_OPERATOR_USER, ADMIN_USER]) ) { ?>
                    <li class="coastlynk-vessel-menu-item <?php echo intval($post->ID)==intval($coatalynk_ports_page)?'active':'';?>">
                        <div class="coastlynk-vessel-menu-icon">
                            <i class="fa-solid fa-anchor"></i>                        
                        </div>
                        <div class="coastlynk-vessel-menu-text"><a href="<?php echo get_permalink($coatalynk_ports_page);?>"><?php _e( "Ports", "castalynkmap" );?></a></div>
                        <div class="coastlynk-menu-toggle" id="coastlynk-menu-toggle-close">
                            <a href="javascript:;"><i class="fa fa-window-close"></i> </a>                    
                        </div>
                        <div class="coastlynk-menu-toggle coastlynk-menu-toggle-open" style="display: none;">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                        </div>
                        <div class="coastlynk-menu-dashboard-open-close-burger coastlynk-menu-dashboard-burger">
                            <i class="fa fa-window-close"></i>                 
                        </div>
                    </li>
                <?php } ?>
                <?php if( in_array( $user_type, [GENERAL_USER]) ) { ?>
                    <li class="coastlynk-vessel-menu-item <?php echo intval($post->ID)==intval($coatalynk_ports_page)?'active':'';?>">
                        <div class="coastlynk-vessel-menu-icon">
                            <i class="fa-solid fa-anchor"></i>                        
                        </div>
                        <div class="coastlynk-vessel-menu-text"><a href="<?php echo get_permalink($coatalynk_ports_page);?>"><?php _e( "Vessels", "castalynkmap" );?></a></div>
                        <div class="coastlynk-menu-toggle" id="coastlynk-menu-toggle-close">
                            <a href="<?php echo get_permalink($coatalynk_vessels_page);?>"><i class="fa fa-window-close"></i></a>                    
                        </div>
                        <div class="coastlynk-menu-toggle coastlynk-menu-toggle-open" style="display: none;">
                            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                         </div>
                        <div class="coastlynk-menu-dashboard-open-close-burger coastlynk-menu-dashboard-burger">
                            <i class="fa fa-window-close"></i>                 
                        </div>
                    </li>
                <?php } ?>
                <?php if( in_array( $user_type, [REGULATOR_USER, SHIPLINE_USER, PORT_OPERATOR_USER, ADMIN_USER]) ) { ?>
                    <li class="coastlynk-vessel-menu-item <?php echo intval($post->ID)==intval($coatalynk_vessels_page)?'active':'';?>">
                        <div class="coastlynk-vessel-menu-icon">
                            <a href="<?php echo get_permalink($coatalynk_vessels_page);?>"><i class="fa-solid fa-ship"></i></a>                       
                        </div>
                        <div class="coastlynk-vessel-menu-text"><a href="<?php echo get_permalink($coatalynk_vessels_page);?>"><?php _e( "Vessels", "castalynkmap" );?></a></div>
                    </li>
                <?php } ?>
                <?php if( in_array( $user_type, [REGULATOR_USER, ADMIN_USER]) ) { ?>
                    <li class="coastlynk-vessel-menu-item <?php echo $post->ID==$coatalynk_sbm_page?'active':'';?>">
                        <div class="coastlynk-vessel-menu-icon">
                            <a href="<?php echo get_permalink($coatalynk_sbm_page);?>"><i class="fa-solid fa-map"></i></a>
                        </div>
                        <div class="coastlynk-vessel-menu-text"><a href="<?php echo get_permalink($coatalynk_sbm_page);?>"><?php _e( "SBM Map", "castalynkmap" );?></a></div>
                    </li>
                <?php } ?>
                <?php if( in_array( $user_type, [REGULATOR_USER, ADMIN_USER]) ) { ?>
                    <li class="coastlynk-vessel-menu-item <?php echo $post->ID==$coatalynk_sts_page?'active':'';?>">
                        <div class="coastlynk-vessel-menu-icon">
                            <a href="<?php echo get_permalink($coatalynk_sts_page);?>"><i class="fa fa-arrows-h"></i></a>
                        </div>
                        <div class="coastlynk-vessel-menu-text"><a href="<?php echo get_permalink($coatalynk_sts_page);?>"><?php _e( "STS MAP", "castalynkmap" );?></a></div>
                    </li>
                <?php } ?>
                <?php if( in_array( $user_type, [REGULATOR_USER, ADMIN_USER]) ) { ?>
                    <li class="coastlynk-vessel-menu-item <?php echo $post->ID==$coatalynk_levy_calculator_page?'active':'';?>">
                        <div class="coastlynk-vessel-menu-icon">
                            <a href="<?php echo get_permalink($coatalynk_levy_calculator_page);?>"><i class="fa-solid fa-calculator"></i></a>
                        </div>
                        <div class="coastlynk-vessel-menu-text"><a href="<?php echo get_permalink($coatalynk_levy_calculator_page);?>"><?php _e( "Levy Calculator", "castalynkmap" );?></a></div>
                    </li>
                <?php } ?>
<!--                 
                <?php if( in_array( $user_type, [REGULATOR_USER, ADMIN_USER]) ) { ?>
                    <li class="coastlynk-vessel-menu-item">
                        <div class="coastlynk-vessel-menu-icon">
                            <i class="fa-solid fa-database"></i>                       
                        </div>
                        <div class="coastlynk-vessel-menu-text"><?php _e( "Historical Data", "castalynkmap" );?></div>
                    </li>
                <?php } ?>
                <?php if( in_array( $user_type, [REGULATOR_USER, ADMIN_USER]) ) { ?>
                    <li class="coastlynk-vessel-menu-item">
                        <div class="coastlynk-vessel-menu-icon">
                            <i class="fa fa-compress"></i>
                        </div>
                        <div class="coastlynk-vessel-menu-text"><?php _e( "Sea Routes", "castalynkmap" );?></div>
                    </li>
                <?php } ?>
                <?php if( in_array( $user_type, [REGULATOR_USER, ADMIN_USER]) ) { ?>
                    <li class="coastlynk-vessel-menu-item">
                        <div class="coastlynk-vessel-menu-icon">
                            <i class="fa-solid fa-book"></i>                    
                        </div>
                        <div class="coastlynk-vessel-menu-text"><?php _e( "Reports", "castalynkmap" );?></div>
                    </li>
                <?php } ?> -->
            </ul>
        </div>
    <?php
}
