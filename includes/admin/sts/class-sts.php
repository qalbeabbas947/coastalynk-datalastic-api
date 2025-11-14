<?php
/**
 * CSM_STS_Admin_listing admin template
 */

if( ! defined( 'ABSPATH' ) ) exit;

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
ini_set('display_errors', 'On');
error_reporting(E_ALL);
/**
 * Class CSM_STS_Admin_listing
 */
class CSM_STS_Admin_listing extends WP_List_Table {

    /**
     * Current select Plugin
     */
    public $csm_ports_filter;

    /**
     * STS Search
     */
    public $selected_search;


    /**
     * Plugins list
     */
    public $plugins;
    
    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    public function __construct(){
        
        global $status, $page;

		$this->csm_ports_filter       = isset( $_GET['csm_ports_filter'] ) ? sanitize_text_field( $_GET['csm_ports_filter'] ) : '' ;
        $this->selected_search          = ( isset( $_GET['search'] )  ) ? sanitize_text_field( $_GET['search'] ) : ''; 
       
        /**
         * Set parent defaults
         */
        parent::__construct( [
            'singular'      => 'csm_sts_admin_listing',
            'plural'        => 'csm_sts_admin_listing',
            'ajax'          => true
        ] );
        
    }


    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title() 
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as 
     * possible. 
     * 
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     * 
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    public function column_default($item, $column_name){
        
        switch($column_name){
            case 'id':
            case 'vessel1_uuid':
            case 'vessel1_name':
            case 'vessel1_mmsi':
            case 'vessel1_imo':
            case 'vessel1_country_iso':
            case 'vessel1_type':
            case 'vessel1_type_specific':
            case 'vessel1_lat':
            case 'vessel1_lon':
            case 'vessel1_speed':
            case 'vessel1_navigation_status':
            case 'vessel1_draught':
            case 'vessel1_completed_draught':
            case 'vessel1_last_position_UTC':
            case 'vessel1_signal':
            case 'vessel2_uuid':
            case 'vessel2_name':
            case 'vessel2_mmsi':
            case 'vessel2_imo':
            case 'vessel2_country_iso':
            case 'vessel2_type':
            case 'vessel2_type_specific':
            case 'vessel2_lat':
            case 'vessel2_lon':
            case 'vessel2_speed':
            case 'vessel2_navigation_status':
            case 'vessel2_draught':
            case 'vessel2_completed_draught':
            case 'vessel2_last_position_UTC':
            case 'vessel2_signal':
            case 'port':
            case 'port_id':
            case 'distance':
            case 'event_ref_id':
            case 'is_sts_zone':
            case 'zone_terminal_name':
            case 'start_date':
            case 'end_date':
            case 'remarks':
            case 'event_percentage':
            case 'vessel_condition1':
            case 'vessel_condition2':
            case 'cargo_category_type':
            case 'risk_level':
            case 'current_distance_nm':
            case 'stationary_duration_hours':
            case 'proximity_consistency':
            case 'data_points_analyzed':
            case 'estimated_cargo':
            case 'operationmode':
            case 'status':
            case 'is_email_sent':
            case 'is_complete':
            case 'is_disappeared':
            case 'last_updated':
                return $item[$column_name];
            default:
                return print_r($item,true);
        }
    }


    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    public function get_columns(){
        
        $columns = [
            'id'                                    => __( 'ID', 'castalynkmap' ),
            'vessel1_uuid'                          => __( 'Vessel 1 UUID', 'castalynkmap' ),
            'vessel1_name'                          => __( 'Vessel 1 Name', 'castalynkmap' ),
            'vessel1_mmsi'                          => __( 'Vessel 1 MMSI', 'castalynkmap' ),
            'vessel1_imo'                           => __( 'Vessel 1 IMO', 'castalynkmap' ),
            'vessel1_country_iso'                   => __( 'Vessel 1 Country', 'castalynkmap' ),
            'vessel1_type'                          => __( 'Vessel 1 Type', 'castalynkmap' ),
            'vessel1_type_specific'                 => __( 'Vessel 1 Sub. Type', 'castalynkmap' ),
            'vessel1_lat'                           => __( 'Vessel 1 Latitude', 'castalynkmap' ),
            'vessel1_lon'                           => __( 'Vessel 1 Longitude', 'castalynkmap' ),
            'vessel1_speed'                         => __( 'Vessel 1 Speed', 'castalynkmap' ),
            'vessel1_navigation_status'             => __( 'Vessel 1 Nav. Status', 'castalynkmap' ),
            'vessel1_draught'                       => __( 'Vessel 1 Before Draught', 'castalynkmap' ),
            'vessel1_completed_draught'             => __( 'Vessel 1 After Draught', 'castalynkmap' ),
            'vessel1_last_position_UTC'             => __( 'Vessel 1 Last Position', 'castalynkmap' ),
            'vessel1_signal'                        => __( 'Vessel 1 Signal', 'castalynkmap' ),
            'vessel2_uuid'                          => __( 'Vessel 2 UUID', 'castalynkmap' ),
            'vessel2_name'                          => __( 'Vessel 2 Name', 'castalynkmap' ),
            'vessel2_mmsi'                          => __( 'Vessel 2 MMSI', 'castalynkmap' ),
            'vessel2_imo'                           => __( 'Vessel 2 IMO', 'castalynkmap' ),
            'vessel2_country_iso'                   => __( 'Vessel 2 Country', 'castalynkmap' ),
            'vessel2_type'                          => __( 'Vessel 2 Type', 'castalynkmap' ),
            'vessel2_type_specific'                 => __( 'Vessel 2 Sub. Type', 'castalynkmap' ),
            'vessel2_lat'                           => __( 'Vessel 2 Latitude', 'castalynkmap' ),
            'vessel2_lon'                           => __( 'Vessel 2 Longitude', 'castalynkmap' ),
            'vessel2_speed'                         => __( 'Vessel 2 Speed', 'castalynkmap' ),
            'vessel2_navigation_status'             => __( 'Vessel 2 Nav. Status', 'castalynkmap' ),
            'vessel2_draught'                       => __( 'Vessel 2 Before Draught', 'castalynkmap' ),
            'vessel2_completed_draught'             => __( 'Vessel 2 After Draught', 'castalynkmap' ),
            'vessel2_last_position_UTC'             => __( 'Vessel 2 Last Position', 'castalynkmap' ),
            'vessel2_signal'                        => __( 'Vessel 2 Signal', 'castalynkmap' ),
            'port'                                  => __( 'Port', 'castalynkmap' ),
            'port_id'                               => __( 'Port ID', 'castalynkmap' ),
            'distance'                              => __( 'distance', 'castalynkmap' ),
            'event_ref_id'                          => __( 'Ref ID', 'castalynkmap' ),
            'is_sts_zone'                           => __( 'IS STS Zone?', 'castalynkmap' ),
            'zone_terminal_name'                    => __( 'Zone Name', 'castalynkmap' ),
            'start_date'                            => __( 'Start Date', 'castalynkmap' ),
            'end_date'                              => __( 'End Date', 'castalynkmap' ),
            'remarks'                               => __( 'Remarks', 'castalynkmap' ),
            'event_percentage'                      => __( 'Event Percentage', 'castalynkmap' ),
            'vessel_condition1'                     => __( 'Vessel 1 Condition', 'castalynkmap' ),
            'vessel_condition2'                     => __( 'Vessel 2 Condition', 'castalynkmap' ),
            'cargo_category_type'                   => __( 'Cargo Type', 'castalynkmap' ),
            'risk_level'                            => __( 'Risk Level', 'castalynkmap' ),
            'current_distance_nm'                   => __( 'Distance(NM)', 'castalynkmap' ),
            'stationary_duration_hours'             => __( 'Stationary hours', 'castalynkmap' ),
            'proximity_consistency'                 => __( 'Proximity Consistency', 'castalynkmap' ),
            'data_points_analyzed'                  => __( 'Data Points Analyzed', 'castalynkmap' ),
            'estimated_cargo'                       => __( 'Estimated Cargo', 'castalynkmap' ),
            'operationmode'                         => __( 'Operation Mode', 'castalynkmap' ),
            'status'                                => __( 'Status', 'castalynkmap' ),
            'is_email_sent'                         => __( 'Email Sent?', 'castalynkmap' ),
            'is_complete'                           => __( 'Left Area?', 'castalynkmap' ),
            'is_disappeared'                        => __( 'Is Disappeared?', 'castalynkmap' ),
            'last_updated'                          => __( 'Last Updated', 'castalynkmap' ),
            'view'                                  => __( 'View', 'castalynkmap' )
        ];

        return $columns;
    }

    /**
     * Will display a link to show popup for the subscription detail.
     */
    public function column_view( $item ){
        
        if( !empty( strip_tags( $item['id'] ) ) ) {
 
            return '<a data-action="csm_delete_sts" data-id="'.$item['id'].'"  data-event_ref_id="'.$item['event_ref_id'].'" class="csm_delete_sts" href="javascript:;">'.__( 'Delete', 'castalynkmap' ).'</a>';
        } else {
            return Coastalynk_Admin::get_bar_preloader();
        }    
    }

    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    public function get_sortable_columns() {
        
        $sortable_columns = array(
            'id'                            => array( 'id', false ),
            'vessel1_uuid'                  => array( 'vessel1_uuid', false ),
            'vessel1_name'                  => array( 'vessel1_name', false ),
            'vessel1_mmsi'                  => array( 'vessel1_mmsi', false ),
            'vessel1_imo'                   => array( 'vessel1_imo', false ),
            'vessel1_country_iso'           => array( 'vessel1_country_iso', false ),
            'vessel1_type'                  => array( 'vessel1_type', false ),
            'vessel1_type_specific'         => array( 'vessel1_type_specific', false ),
            'reason_type'                   => array( 'reason_type', false ),
            'vessel1_lat'                   => array( 'vessel1_lat', false ),
            'vessel1_lon'                   => array( 'vessel1_lon', false ),
            'vessel1_speed'                 => array( 'vessel1_speed', false ),
            'vessel1_navigation_status'     => array( 'vessel1_navigation_status', false ),
            'vessel1_draught'               => array( 'vessel1_draught', false ),
            'vessel1_completed_draught'     => array( 'vessel1_completed_draught', false ),
            'vessel1_last_position_UTC'     => array( 'vessel1_last_position_UTC', false ),
            'vessel1_signal'                => array( 'vessel1_signal', false ),
            'vessel2_uuid'                  => array( 'vessel2_uuid', false ),
            'vessel2_name'                  => array( 'vessel2_name', false ),
            'vessel2_mmsi'                  => array( 'vessel2_mmsi', false ),
            'vessel2_imo'                   => array( 'vessel2_imo', false ),
            'vessel2_country_iso'           => array( 'vessel2_country_iso', false ),
            'vessel2_type'                  => array( 'vessel2_type', false ),
            'vessel2_type_specific'         => array( 'vessel2_type_specific', false ),
            'vessel2_lat'                  => array( 'vessel2_lat', false ),
            'vessel2_lon'                  => array( 'vessel2_lon', false ),
            'vessel2_speed'                => array( 'vessel2_speed', false ),
            'vessel2_navigation_status'    => array( 'vessel2_navigation_status', false ),
            'vessel2_draught'              => array( 'vessel2_draught', false ),
            'vessel2_completed_draught'    => array( 'vessel2_completed_draught', false ),
            'vessel2_last_position_UTC'    => array( 'vessel2_last_position_UTC', false ),
            'vessel2_signal'               => array( 'vessel2_signal', false ),
            'port'                         => array( 'port', false ),
            'port_id'                       => array( 'port_id', false ),
            'distance'                      => array( 'distance', false ),
            'event_ref_id'                  => array( 'event_ref_id', false ),
            'is_sts_zone'                   => array( 'is_sts_zone', false ),
            'zone_terminal_name'            => array( 'zone_terminal_name', false ),
            'start_date'                    => array( 'start_date', false ),
            'end_date'                      => array( 'end_date', false ),
            'remarks'                       => array( 'remarks', false ),
            'event_percentage'              => array( 'event_percentage', false ),
            'vessel_condition1'             => array( 'vessel_condition1', false ),
            'vessel_condition2'             => array( 'vessel_condition2', false ),
            'cargo_category_type'           => array( 'cargo_category_type', false ),
            'risk_level'                    => array( 'risk_level', false ),
            'current_distance_nm'           => array( 'current_distance_nm', false ),
            'stationary_duration_hours'     => array( 'stationary_duration_hours', false ),
            'proximity_consistency'         => array( 'proximity_consistency', false ),
            'data_points_analyzed'          => array( 'data_points_analyzed', false ),
            'estimated_cargo'               => array( 'estimated_cargo', false ),
            'operationmode'                 => array( 'operationmode', false ),
            'status'                        => array( 'status', false ),
            'is_email_sent'                 => array( 'is_email_sent', false ),
            'is_complete'                   => array( 'is_complete', false ),
            'is_disappeared'                => array( 'is_disappeared', false ),
            'last_updated'                  => array( 'last_updated', false )
      );
         
        return $sortable_columns;
    }


    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    public function get_bulk_actions() {
       
        $actions = [];
        return $actions;
    }


    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    public function process_bulk_action() {
        
        /**
         * Detect when a bulk action is being triggered...
         */
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }


    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    public function prepare_items() {
        
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = get_user_option(
            'sts_per_page',
            get_current_user_id()
        );
        
        if( empty( $per_page ) ) {
            $per_page = 10;
        }
		if( ! wp_doing_ajax() ) {
            
			$this->items = [
                [
                    'id'                            => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_uuid'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_name'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_mmsi'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_imo'                   => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_country_iso'           => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_type'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_type_specific'         => Coastalynk_Admin::get_bar_preloader(), 
                    'reason_type'                   => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_lat'                   => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_lon'                   => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_speed'                 => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_navigation_status'     => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_draught'               => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_completed_draught'     => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_last_position_UTC'     => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel1_signal'                => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_uuid'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_name'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_mmsi'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_imo'                   => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_country_iso'           => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_type'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_type_specific'         => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_lat'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_lon'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_speed'                => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_navigation_status'    => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_draught'              => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_completed_draught'    => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_last_position_UTC'    => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel2_signal'               => Coastalynk_Admin::get_bar_preloader(), 
                    'port'                         => Coastalynk_Admin::get_bar_preloader(), 
                    'port_id'                       => Coastalynk_Admin::get_bar_preloader(), 
                    'distance'                      => Coastalynk_Admin::get_bar_preloader(), 
                    'event_ref_id'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'is_sts_zone'                   => Coastalynk_Admin::get_bar_preloader(), 
                    'zone_terminal_name'            => Coastalynk_Admin::get_bar_preloader(), 
                    'start_date'                    => Coastalynk_Admin::get_bar_preloader(), 
                    'end_date'                      => Coastalynk_Admin::get_bar_preloader(), 
                    'remarks'                       => Coastalynk_Admin::get_bar_preloader(), 
                    'event_percentage'              => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel_condition1'             => Coastalynk_Admin::get_bar_preloader(), 
                    'vessel_condition2'             => Coastalynk_Admin::get_bar_preloader(), 
                    'cargo_category_type'           => Coastalynk_Admin::get_bar_preloader(), 
                    'risk_level'                    => Coastalynk_Admin::get_bar_preloader(), 
                    'current_distance_nm'           => Coastalynk_Admin::get_bar_preloader(), 
                    'stationary_duration_hours'     => Coastalynk_Admin::get_bar_preloader(), 
                    'proximity_consistency'         => Coastalynk_Admin::get_bar_preloader(), 
                    'data_points_analyzed'          => Coastalynk_Admin::get_bar_preloader(), 
                    'estimated_cargo'               => Coastalynk_Admin::get_bar_preloader(), 
                    'operationmode'                 => Coastalynk_Admin::get_bar_preloader(), 
                    'status'                        => Coastalynk_Admin::get_bar_preloader(), 
                    'is_email_sent'                 => Coastalynk_Admin::get_bar_preloader(), 
                    'is_complete'                   => Coastalynk_Admin::get_bar_preloader(), 
                    'is_disappeared'                => Coastalynk_Admin::get_bar_preloader(), 
                    'last_updated'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'view'                          => Coastalynk_Admin::get_bar_preloader()
                ]
            ];

            $this->set_pagination_args( [
                'total_items'   => 1,
                'per_page'      => 1,
                'paged'        => 1,
                'current_recs'  => 1,
                'total_pages'   => 1
            ] );

			return;
		}
        
        $paged = isset( $_REQUEST['paged'] ) && intval( $_REQUEST['paged'] ) > 0 ? intval( $_REQUEST['paged'] ) : 1;
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        
        $columns = $this->get_columns();
        $screen = WP_Screen::get( 'coastalynk_page_coastalynk-sts' );
        $hidden   = get_hidden_columns( $screen );
        if( empty( $hidden ) ) {
            $hidden = get_user_meta( get_current_user_id(), 'manage' . $screen->id . 'columnshidden', true );
            if( empty( $hidden ) ) {
                $hidden = get_user_meta( get_current_user_id(), $wpdb->prefix.'manage' . $screen->id . 'columnshidden', true );
            }
        }

        if( empty( $hidden ) ) {
            $hidden = [];
        }
        
        $sortable = $this->get_sortable_columns();
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = [ $columns, $hidden, $sortable ];
        
        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */

        $table_name = $wpdb->prefix.'coastalynk_sts'; 
        $where = " where 1 = 1";
       
        if( ! empty( $this->csm_ports_filter ) ) {
            $where .= " and port_id='".$this->csm_ports_filter."'";
        }
        
        $where .= ! empty( $this->selected_search ) ? " and ( vessel1_uuid like '%".$this->selected_search."%' or  vessel2_uuid like '%".$this->selected_search."%' or lower(vessel1_name) like '%".strtolower($this->selected_search)."%' or lower(vessel2_name) like '%".strtolower($this->selected_search)."%' or lower(vessel1_imo) like '%".strtolower($this->selected_search)."%' or lower(vessel2_imo) like '%".strtolower($this->selected_search)."%' or lower(vessel1_mmsi) like '%".strtolower($this->selected_search)."%' or lower(vessel2_mmsi) like '%".strtolower($this->selected_search)."%' or lower(vessel1_type_specific) like '%".strtolower($this->selected_search)."%' or lower(vessel2_type_specific) like '%".strtolower($this->selected_search)."%'  or lower(event_ref_id) like '%".strtolower($this->selected_search)."%' )" : '';
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name".$where);

        // prepare query params, as usual current page, order by and order direction
        $offset     = isset($paged) ? intval(($paged-1) * $per_page) : 0;
        $orderby    = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'last_updated';
        $order      = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? sanitize_text_field( $_REQUEST['order'] ) : 'desc';
        $result     = $wpdb->get_results( "SELECT * FROM $table_name $where ORDER BY $orderby $order LIMIT $per_page OFFSET $offset", ARRAY_A );
            
        $data = [];
        $count = 0;
        if( isset($result) && is_array($result) && count($result) > 0 ) {
            foreach( $result as $res ) {
                $user_id = 0;
                foreach( $res as $key => $value ) {
                    
                    if( empty( $value ) ) {
                        $value = '-';
                    }    
                    $data[$count][$key] = $value;
                } 
                 
                $count++;   
            }
        }
        
        
        $this->items = $data;

        $this->set_pagination_args( [
            'total_items'   => $total_items,
            'per_page'      => $per_page,
            'paged'         => $paged,
            'current_recs'  => count( $this->items ),
            'total_pages'   => ceil( $total_items / $per_page )
        ] );
    }
	
	/**
	 * @Override of display method
	 */

	public function display() {

		parent::display();
	}

	/**
	 * @Override ajax_response method
	 */
	public function ajax_response() {

		$this->prepare_items();

		extract( $this->_args );
		extract( $this->_pagination_args, EXTR_SKIP );

		ob_start();

		if ( ! empty( $_REQUEST['no_placeholder'] ) ) {
            $this->display_rows();
        } else {
            $this->display_rows_or_placeholder();
        }
		$rows = ob_get_clean();

		ob_start();
		$this->print_column_headers();
		$headers = ob_get_clean();

		ob_start();
		$this->pagination('top');
		$pagination_top = ob_get_clean();

		ob_start();
		$this->pagination('bottom');
		$pagination_bottom = ob_get_clean();

		$response = [ 'rows' => $rows ];
		$response['pagination']['top'] = $pagination_top;
		$response['pagination']['bottom'] = $pagination_bottom;
		$response['column_headers'] = $headers;

		if ( isset( $total_items ) )
			$response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );

		if ( isset( $total_pages ) ) {
			$response['total_pages'] = $total_pages;
			$response['total_pages_i18n'] = number_format_i18n( $total_pages );
		}

		die( json_encode( $response ) );
	}
	
    /**
	 * Displays the pagination.
	 *
	 * @param string $which
	 */
    public function display_tablenav( $which ) {
        
        if ( 'top' === $which ) {
            wp_nonce_field( 'bulk-' . $this->_args['plural'] );
        }
        ?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>">

                <?php if ( $this->has_items() ) : ?>
                    <div class="alignleft actions bulkactions">
                        <?php $this->bulk_actions( $which ); ?>
                    </div>
                <?php
                endif;
                
                $this->extra_tablenav( $which );
                $this->pagination_new( $which );
                ?>

                <br class="clear" />
            </div>
        <?php
    }
	
    /**
	 * Displays the pagination.
	 *
	 * @since 3.1.0
	 *
	 * @param string $which
	 */
	protected function pagination_new( $which ) {
        
		if ( empty( $this->_pagination_args ) ) {
            return;
        }

        $total_items     = $this->_pagination_args['total_items'];
        $total_pages     = $this->_pagination_args['total_pages'];
        $per_page       = $this->_pagination_args['per_page'];
		$paged          = $this->_pagination_args['paged'];
        $current_recs   = $this->_pagination_args['current_recs'];

        $infinite_scroll = false;
        if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
            $infinite_scroll = $this->_pagination_args['infinite_scroll'];
        }
    
        if ( 'top' === $which && $total_pages > 1 ) {
            $this->screen->render_screen_reader_content( 'heading_pagination' );
        }
    
        $output = '<span class="displaying-num">' . sprintf(
            /* translators: %s: Number of items. */
            _n( '%s item', '%s items', $total_items ),
            number_format_i18n( $total_items )
        ) . '</span>';
    
        $current              = $this->get_pagenum();
        $removable_query_args = wp_removable_query_args();
    
        $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
    
        $current_url = remove_query_arg( $removable_query_args, $current_url );
    
        $page_links = array();
    
        $total_pages_before = '<span class="paging-input">';
        $total_pages_after  = '</span></span>';
    
        $disable_first = false;
        $disable_last  = false;
        $disable_prev  = false;
        $disable_next  = false;
    
        if ( 1 == $current ) {
            $disable_first = true;
            $disable_prev  = true;
        }
        if ( $total_pages == $current ) {
            $disable_last = true;
            $disable_next = true;
        }
    
        if ( $disable_first ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
        } else {
            $page_links[] = sprintf( 
                "<a data-csm_ports_filter='%d' data-search='%s' data-per_page='%d' class='first-page button csm_check_load_next' data-paged='1' href='javascript:;'>" .
                    "<span class='screen-reader-text'>%s</span>" .
                    "<span aria-hidden='true'>%s</span>" .
                '</a>',
                $this->csm_ports_filter,
                $this->selected_search,
                $per_page,
                /* translators: Hidden accessibility text. */
                __( 'First page' ),
                '&laquo;'
            );
        }
        
        if ( $disable_prev ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<a data-csm_ports_filter='%d' data-search='%s' data-per_page='%d' class='prev-page button csm_check_load_next' data-paged='%d' href='javascript:;'>" .
                    "<span class='screen-reader-text'>%s</span>" .
                    "<span aria-hidden='true'>%s</span>" .
                '</a>',
                $this->csm_ports_filter,
                $this->selected_search,
                $per_page,
                intval($paged)>1?intval($paged)-1:1,
                /* translators: Hidden accessibility text. */
                __( 'Previous page' ),
                '&lsaquo;'
            );

        }
    
        $html_current_page  = $current;
        $total_pages_before = sprintf(
            '<span class="screen-reader-text">%s</span>' .
            '<span id="table-paging" class="paging-input">' .
            '<span class="tablenav-paging-text">',
            /* translators: Hidden accessibility text. */
            __( 'Current Page' )
        );
    
        $html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
    
        $page_links[] = $total_pages_before . sprintf(
            /* translators: 1: Current page, 2: Total pages. */
            _x( '%1$s of %2$s', 'paging' ),
            $html_current_page,
            $html_total_pages
        ) . $total_pages_after;
    
        if ( $disable_next ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<a data-csm_ports_filter='%d' data-search='%s' data-per_page='%d' data-paged='%d' data-current_recs='%d' class='next-page button csm_check_load_next' href='javascript:;'>" .
                    "<span class='screen-reader-text'>%s</span>" .
                    "<span aria-hidden='true'>%s</span>" .
                '</a>',
                $this->csm_ports_filter,
                $this->selected_search,
                $per_page,
                $paged+1,
                $current_recs,
                /* translators: Hidden accessibility text. */
                __( 'Next page' ),
                '&rsaquo;'
            );
        }
    
        if ( $disable_last ) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<a data-csm_ports_filter='%d' data-search='%s' data-per_page='%d' data-paged='%d' data-current_recs='%d' class='last-page button csm_check_load_next' href='javascript:;'>" .
                    "<span class='screen-reader-text'>%s</span>" .
                    "<span aria-hidden='true'>%s</span>" .
                '</a>',
                $this->csm_ports_filter,
                $this->selected_search,
                $per_page,
                $total_pages,
                $current_recs,
                /* translators: Hidden accessibility text. */
                __( 'Last page' ),
                '&raquo;'
            );
        }
    
        $pagination_links_class = 'pagination-links';
        if ( ! empty( $infinite_scroll ) ) {
            $pagination_links_class .= ' hide-if-js';
        }
        $output .= "\n<span class='$pagination_links_class'>" . implode( "\n", $page_links ) . '</span>';
    
        if ( $total_pages ) {
            $page_class = $total_pages < 2 ? ' one-page' : '';
        } else {
            $page_class = ' no-pages';
        }
        $this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";
    
        echo $this->_pagination;
	}
}