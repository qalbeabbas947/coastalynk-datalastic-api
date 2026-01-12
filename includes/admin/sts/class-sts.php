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
    public $csm_vessel1_search1;
    public $csm_vessel2_search2;
    public $csm_history_range;
    public $csm_risk_level;
    public $csm_status;

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

		$this->csm_ports_filter             = isset( $_GET['csm_ports_filter'] ) ? sanitize_text_field( $_GET['csm_ports_filter'] ) : '' ;
        $this->selected_search              = ( isset( $_GET['search'] )  ) ? sanitize_text_field( $_GET['search'] ) : ''; 
        $this->csm_vessel1_search1          = ( isset( $_GET['csm_vessel1_search1'] )  ) ? sanitize_text_field( $_GET['csm_vessel1_search1'] ) : ''; 
        $this->csm_vessel2_search2          = ( isset( $_GET['csm_vessel2_search2'] )  ) ? sanitize_text_field( $_GET['csm_vessel2_search2'] ) : ''; 
        $this->csm_history_range            = ( isset( $_GET['csm_history_range'] )  ) ? sanitize_text_field( $_GET['csm_history_range'] ) : ''; 
        $this->csm_risk_level               = ( isset( $_GET['csm_risk_level'] )  ) ? sanitize_text_field( $_GET['csm_risk_level'] ) : ''; 
        $this->csm_status                   = ( isset( $_GET['csm_status'] )  ) ? sanitize_text_field( $_GET['csm_status'] ) : ''; 
        
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
            case  'id':
            case  'uuid':
            case  'name':
            case  'mmsi':
            case  'imo':
            case  'country_iso':
            case  'type':
            case  'type_specific':
            case  'lat':
            case  'lon':
            case  'speed':
            case  'draught':
            case  'completed_draught':
            case  'ais_signal':
            case  'deadweight':
            case  'gross_tonnage':
            case  'port':
            case  'port_id':
            case  'distance':
            case  'event_ref_id':
            case  'zone_type':
            case  'zone_terminal_name':
            case  'status':
            case  'is_email_sent':
            case  'is_complete':
            case  'is_disappeared':
                return $item[$column_name];
            case 'last_updated':
            case 'start_date':
            case 'end_date':
            case 'last_position_UTC':
                return get_date_from_gmt( $item[$column_name], CSM_DATE_FORMAT.' '.CSM_TIME_FORMAT );
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
           'name'                  => __( 'name', 'castalynkmap'  ), 
            'id'                    => __( 'id', 'castalynkmap' ), 
            'uuid'                  => __( 'uuid', 'castalynkmap'  ), 
            'mmsi'                  => __( 'mmsi', 'castalynkmap'  ), 
            'imo'                   => __( 'imo', 'castalynkmap'  ), 
            'country_iso'           => __( 'country_iso', 'castalynkmap'  ), 
            'type'                  => __( 'type', 'castalynkmap'  ), 
            'type_specific'         => __( 'type_specific', 'castalynkmap'  ), 
            'lat'                   => __( 'lat', 'castalynkmap'  ), 
            'lon'                   => __( 'lon', 'castalynkmap'  ), 
            'speed'                 => __( 'speed', 'castalynkmap'  ), 
            'draught'               => __( 'draught', 'castalynkmap'  ), 
            'completed_draught'     => __( 'completed_draught', 'castalynkmap'  ), 
            'last_position_UTC'     => __( 'last_position_UTC', 'castalynkmap'  ), 
            'ais_signal'            => __( 'ais_signal', 'castalynkmap'  ), 
            'deadweight'            => __( 'deadweight', 'castalynkmap'  ), 
            'gross_tonnage'         => __( 'gross_tonnage', 'castalynkmap'  ), 
            'port'                  => __( 'port', 'castalynkmap'  ), 
            'port_id'               => __( 'port_id', 'castalynkmap'  ), 
            'distance'              => __( 'distance', 'castalynkmap'  ), 
            'event_ref_id'          => __( 'event_ref_id', 'castalynkmap'  ), 
            'zone_type'             => __( 'zone_type', 'castalynkmap'  ), 
            'zone_terminal_name'    => __( 'zone_terminal_name', 'castalynkmap'  ), 
            'start_date'            => __( 'start_date', 'castalynkmap'  ), 
            'end_date'              => __( 'end_date', 'castalynkmap'  ), 
            'status'                => __( 'status', 'castalynkmap'  ), 
            'is_email_sent'         => __( 'is_email_sent', 'castalynkmap'  ), 
            'is_complete'           => __( 'is_complete', 'castalynkmap'  ), 
            'is_disappeared'        => __( 'is_disappeared', 'castalynkmap'  ), 
            'last_updated'          => __( 'last_updated', 'castalynkmap'  ), 
            'childern'              => __( 'Childern', 'castalynkmap'  ),
            'view'                  => __( 'Delete', 'castalynkmap' )
        ];

        return $columns;
    }

    /**
     * Will display a link to show popup for the subscription detail.
     */
    public function column_completed_draught( $item ){
        
        if( !empty( strip_tags( $item['id'] ) ) ) { 
            if( floatval( $item['completed_draught'] ) > 0 ) { 
                return $item['completed_draught'];
            } else {
                return __( "Cargo Inference: Not Eligible", "castalynkmap" );
            } 
        } else {
            return Coastalynk_Admin::get_bar_preloader();
        }    
    }

    /**
     * Will display a link to show popup for the subscription detail.
     */
    public function column_view( $item ){
        
        if( !empty( strip_tags( $item['id'] ) ) ) { 
            $attributes = '';
            foreach( $item as $key=>$val ) {
                if( in_array( $key, ['last_updated', 'start_date', 'end_date', 'last_position_UTC'] ) ) {
                    $attributes .= ' data-'.$key.' = "'.get_date_from_gmt( $val, CSM_DATE_FORMAT.' '.CSM_TIME_FORMAT ).'"';
                } else if( in_array( $key, ['draught' ] ) ) {
                    $attributes .= ' data-'.$key.' = "'.( floatval( $val ) > 0?$val.'m':__( "Pending", "castalynkmap" )).'"';
                } else if( in_array( $key, [ 'completed_draught', 'vessel1_completed_draught', 'vessel2_completed_draught' ] ) ) {
                    $attributes .= ' data-'.$key.' = "'.( floatval( $val ) > 0?$val.'m':__( "Cargo Inference: Not Eligible", "castalynkmap" )).'"';
                } else if( in_array( $key, [ 'draught_change' ] ) ) {
                    $attributes .= ' data-'.$key.' = "'.( floatval( $val ) > 0?$val.'m':__( "Pending / AIS-limited", "castalynkmap" )).'"';
                } else {
                    $attributes .= ' data-'.$key.' = "'.$val.'"';
                }
            }

            return '<a data-action="csm_view_sts" '.$attributes.' data-id="'.$item['id'].'"  data-event_ref_id="'.$item['event_ref_id'].'" class="csm_view_sts" href="javascript:;"><span class="dashicons dashicons-search"></span></a>&nbsp;<a data-action="csm_delete_sts" data-id="'.$item['id'].'"  data-event_ref_id="'.$item['event_ref_id'].'" class="csm_delete_sts" href="javascript:;"><span class="dashicons dashicons-no-alt"></span></a>';
        } else {
            return Coastalynk_Admin::get_bar_preloader();
        }    
    }

    /**
     * Will display a link to show popup for the subscription detail.
     */
    public function column_childern( $item ){
        
        if( !empty( strip_tags( $item['id'] ) ) ) { 
            global $wpdb;
            
            $event_table_mother = $wpdb->prefix . 'coastalynk_sts_events';
            $event_table_daughter = $wpdb->prefix . 'coastalynk_sts_event_detail';
            $vessel_data = $wpdb->get_results( "SELECT * from ".$event_table_daughter." where event_id = '".$item['id']."'");
            $childern = '<ul>';
            foreach( $vessel_data as $item_data ) {
                $attributes = '';
                foreach( $item_data as $key=>$val ) {
                    if( in_array( $key, ['last_updated', 'lock_time', 'joining_date', 'end_date', 'last_position_UTC'] ) ) {
                        $attributes .= ' data-'.$key.' = "'.get_date_from_gmt( $val, CSM_DATE_FORMAT.' '.CSM_TIME_FORMAT ).'"';
                    } else if( in_array( $key, ['draught' ] ) ) {
                        $attributes .= ' data-'.$key.' = "'.( floatval( $val ) > 0?$val.'m':__( "Pending", "castalynkmap" )).'"';
                    } else if( in_array( $key, [ 'completed_draught' ] ) ) {
                        $attributes .= ' data-'.$key.' = "'.( floatval( $val ) > 0?$val.'m':__( "Cargo Inference: Not Eligible", "castalynkmap" )).'"';
                    } else if( in_array( $key, [ 'draught_change' ] ) ) {
                        $attributes .= ' data-'.$key.' = "'.( floatval( $val ) > 0?$val.'m':__( "Pending / AIS-limited", "castalynkmap" )).'"';
                    } else {
                        $attributes .= ' data-'.$key.' = "'.$val.'"';
                    }
                }

                $childern .= '<li><a data-action="csm_view_sts" '.$attributes .' class="csm_view_sts" href="javascript:;">'.$item_data->name.'</a></li>';
            }
            $childern .= '</ul>';

            return $childern;
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
            'name'                  => array( 'name', false ), 
            'id'                    => array( 'id', false ), 
            'uuid'                  => array( 'uuid', false ), 
            'mmsi'                  => array( 'mmsi', false ), 
            'imo'                   => array( 'imo', false ), 
            'country_iso'           => array( 'country_iso', false ), 
            'type'                  => array( 'type', false ), 
            'type_specific'         => array( 'type_specific', false ), 
            'lat'                   => array( 'lat', false ), 
            'lon'                   => array( 'lon', false ), 
            'speed'                 => array( 'speed', false ), 
            'draught'               => array( 'draught', false ), 
            'completed_draught'     => array( 'completed_draught', false ), 
            'last_position_UTC'     => array( 'last_position_UTC', false ), 
            'ais_signal'            => array( 'ais_signal', false ), 
            'deadweight'            => array( 'deadweight', false ), 
            'gross_tonnage'         => array( 'gross_tonnage', false ), 
            'port'                  => array( 'port', false ), 
            'port_id'               => array( 'port_id', false ), 
            'distance'              => array( 'distance', false ), 
            'event_ref_id'          => array( 'event_ref_id', false ), 
            'zone_type'             => array( 'zone_type', false ), 
            'zone_terminal_name'    => array( 'zone_terminal_name', false ), 
            'start_date'            => array( 'start_date', false ), 
            'end_date'              => array( 'end_date', false ), 
            'status'                => array( 'status', false ), 
            'is_email_sent'         => array( 'is_email_sent', false ), 
            'is_complete'           => array( 'is_complete', false ), 
            'is_disappeared'        => array( 'is_disappeared', false ), 
            'last_updated'          => array( 'last_updated', false )
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
        
        $event_table_mother = $wpdb->prefix . 'coastalynk_sts_events';
        $event_table_daughter = $wpdb->prefix . 'coastalynk_sts_event_detail';
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
                    'id'                    => Coastalynk_Admin::get_bar_preloader(), 
                    'uuid'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'name'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'mmsi'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'imo'                   => Coastalynk_Admin::get_bar_preloader(), 
                    'country_iso'           => Coastalynk_Admin::get_bar_preloader(), 
                    'type'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'type_specific'         => Coastalynk_Admin::get_bar_preloader(), 
                    'lat'                   => Coastalynk_Admin::get_bar_preloader(), 
                    'lon'                   => Coastalynk_Admin::get_bar_preloader(), 
                    'speed'                 => Coastalynk_Admin::get_bar_preloader(), 
                    'draught'               => Coastalynk_Admin::get_bar_preloader(), 
                    'completed_draught'     => Coastalynk_Admin::get_bar_preloader(), 
                    'last_position_UTC'     => Coastalynk_Admin::get_bar_preloader(), 
                    'ais_signal'            => Coastalynk_Admin::get_bar_preloader(), 
                    'deadweight'            => Coastalynk_Admin::get_bar_preloader(), 
                    'gross_tonnage'         => Coastalynk_Admin::get_bar_preloader(), 
                    'port'                  => Coastalynk_Admin::get_bar_preloader(), 
                    'port_id'               => Coastalynk_Admin::get_bar_preloader(), 
                    'distance'              => Coastalynk_Admin::get_bar_preloader(), 
                    'event_ref_id'          => Coastalynk_Admin::get_bar_preloader(), 
                    'zone_type'             => Coastalynk_Admin::get_bar_preloader(), 
                    'zone_terminal_name'    => Coastalynk_Admin::get_bar_preloader(), 
                    'start_date'            => Coastalynk_Admin::get_bar_preloader(), 
                    'end_date'              => Coastalynk_Admin::get_bar_preloader(), 
                    'status'                => Coastalynk_Admin::get_bar_preloader(), 
                    'is_email_sent'         => Coastalynk_Admin::get_bar_preloader(), 
                    'is_complete'           => Coastalynk_Admin::get_bar_preloader(), 
                    'is_disappeared'        => Coastalynk_Admin::get_bar_preloader(), 
                    'last_updated'          => Coastalynk_Admin::get_bar_preloader()
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
        
        if( ! empty( $this->csm_status ) ) {
            $where .= " and status='".$this->csm_status."'";
        }

        if( ! empty( $this->csm_vessel1_search1 ) ) {
            $where .= " and ( uuid like '%".$this->csm_vessel1_search1."%' or lower(name) like '%".strtolower($this->csm_vessel1_search1)."%' or lower(imo) like '%".strtolower($this->csm_vessel1_search1)."%' or lower(mmsi) like '%".strtolower($this->csm_vessel1_search1)."%' or lower(type_specific) like '%".strtolower($this->csm_vessel1_search1)."%' or lower(event_ref_id) like '%".strtolower($this->csm_vessel1_search1)."%' )";
        }

        if( !empty( $this->csm_history_range ) ) {
            $explode = explode( '-', $this->csm_history_range );
            $start_date = date( 'Y-m-d H:i:s', strtotime( trim( $explode[0] ) ) );
            $end_date = date( 'Y-m-d H:i:s', strtotime( trim( $explode[1] ) ) );
            
            $where .= " and start_date >= '".$start_date."' and ( end_date  <= '".$end_date."' or end_date is NULL ) ";
        }

        if( !empty( $this->csm_risk_level ) ) {

            switch( $this->csm_risk_level ) {
                case "0-30":
                    $where .= " and event_percentage >= '0' and event_percentage < '30' ";
                    break;
                case "30-70":
                    $where .= " and event_percentage >= '30' and event_percentage < '70' ";
                    break;
                case "70-100":
                    $where .= " and event_percentage >= '70'";
                    break;
            }
        }
        
        $where .= ! empty( $this->selected_search ) ? " and ( vessel1_uuid like '%".$this->selected_search."%' or  vessel2_uuid like '%".$this->selected_search."%' or lower(vessel1_name) like '%".strtolower($this->selected_search)."%' or lower(vessel2_name) like '%".strtolower($this->selected_search)."%' or lower(vessel1_imo) like '%".strtolower($this->selected_search)."%' or lower(vessel2_imo) like '%".strtolower($this->selected_search)."%' or lower(vessel1_mmsi) like '%".strtolower($this->selected_search)."%' or lower(vessel2_mmsi) like '%".strtolower($this->selected_search)."%' or lower(vessel1_type_specific) like '%".strtolower($this->selected_search)."%' or lower(vessel2_type_specific) like '%".strtolower($this->selected_search)."%'  or lower(event_ref_id) like '%".strtolower($this->selected_search)."%' )" : '';
        $total_items = $wpdb->get_var("SELECT count(id) from ".$event_table_mother."".$where);
        
        // prepare query params, as usual current page, order by and order direction
        $offset     = isset($paged) ? intval(($paged-1) * $per_page) : 0;
        $orderby    = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'last_updated';
        $order      = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? sanitize_text_field( $_REQUEST['order'] ) : 'desc';
        $result     = $wpdb->get_results( "SELECT * from ".$event_table_mother." $where ORDER BY $orderby $order LIMIT $per_page OFFSET $offset", ARRAY_A );
 
//        echo "SELECT vessel1_name,id, vessel1_uuid, vessel1_mmsi,vessel1_imo,vessel1_country_iso,vessel1_type,vessel1_type_specific, vessel1_lat,vessel1_lon,vessel1_speed,vessel1_navigation_status,vessel1_draught,vessel1_completed_draught,vessel1_last_position_UTC,vessel1_signal,vessel2_uuid,vessel2_name,vessel2_mmsi,vessel2_imo,vessel2_country_iso, vessel2_type,vessel2_type_specific,vessel2_lat,vessel2_lon,vessel2_speed,vessel2_navigation_status,vessel2_draught,vessel2_completed_draught,vessel2_last_position_UTC,vessel2_signal,port,port_id,distance, event_ref_id, zone_type, zone_ship, zone_terminal_name,start_date,end_date,event_percentage,draught_change,cargo_category_type,risk_level,current_distance_nm,stationary_duration_hours,proximity_consistency,data_points_analyzed,mother_vessel_number,operationmode,status,is_email_sent,is_complete,is_disappeared,last_updated FROM $table_name $where ORDER BY $orderby $order LIMIT $per_page OFFSET $offset";   
        $data = []; 
        $count = 0;
        if( isset($result) && is_array($result) && count($result) > 0 ) {
            foreach( $result as $res ) {
                $user_id = 0;
                foreach( $res as $key => $value ) {
                    
                    if( in_array( $key, ['draught' ] ) ) {
                        $data[$count][$key] = ( floatval( $value ) > 0?$value:__( "Pending", "castalynkmap" )).'"';
                    } else if( in_array( $key, [ 'completed_draught' ] ) ) {
                        $data[$count][$key] = ( floatval( $value ) > 0?$value:__( "Cargo Inference: Not Eligible", "castalynkmap" )).'"';
                    } else if( in_array( $key, [ 'draught_change' ] ) ) {
                        $data[$count][$key] = ( floatval( $value ) > 0?$value:__( "Pending / AIS-limited", "castalynkmap" )).'"';
                    } else if( empty( $value ) ) {
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
        echo '<div class="fixed-column-table-container">';
		parent::display();
        echo '</div>';
        
        $this->add_table_styles();
	}
private function add_table_styles() {
        ?>
        <style>
        .fixed-column-table-container {
            position: relative;
            overflow-x: auto;
            background: white;
            border: 1px solid #ccd0d4;
            margin-bottom: 20px;
        }

        .fixed-column-table-container table.widefat {
            min-width: 800px;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        /* Fixed first column */
        .fixed-column-table-container table.widefat thead th:first-child,
        .fixed-column-table-container table.widefat tfoot th:first-child,
        .fixed-column-table-container table.widefat tbody td:first-child {
            position: sticky;
            left: 0;
            background: white;
            z-index: 2;
            border-right: 2px solid #e2e4e7;
            width: 200px;
        }

        /* Fixed header first column */
        .fixed-column-table-container table.widefat thead th:first-child,
        .fixed-column-table-container table.widefat tfoot th:first-child {
            background: #f1f1f1;
            z-index: 3;
            top: 0;
        }
        #csm_sts_data{
            clear: both;
        }
        /* Checkbox column styling */
        .fixed-column-table-container table.widefat thead th.column-cb,
        .fixed-column-table-container table.widefat tfoot th.column-cb,
        .fixed-column-table-container table.widefat tbody td.check-column {
            position: sticky;
            left: 0;
            background: #f1f1f1;
            z-index: 4;
            width: 40px;
            border-right: 2px solid #e2e4e7;
        }

        .fixed-column-table-container table.widefat tbody td.check-column {
            background: white;
            z-index: 3;
        }

        /* Regular cells */
        .fixed-column-table-container table.widefat th,
        .fixed-column-table-container table.widefat td {
            width: 70px;
        }
        </style>
        <?php
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
                "<a data-csm_ports_filter='%d' data-search='%s' data-csm_vessel1_search1='%s' data-csm_vessel2_search2='%s' data-csm_history_range='%s' data-csm_risk_level='%s' data-csm_status='%s' data-per_page='%d' class='first-page button csm_check_load_next' data-paged='1' href='javascript:;'>" .
                    "<span class='screen-reader-text'>%s</span>" .
                    "<span aria-hidden='true'>%s</span>" .
                '</a>',
                $this->csm_ports_filter,
                $this->selected_search,
                $this->csm_vessel1_search1,
                $this->csm_vessel2_search2,
                $this->csm_history_range,
                $this->csm_risk_level,
                $this->csm_status,
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
                "<a data-csm_ports_filter='%d' data-search='%s' data-csm_vessel1_search1='%s' data-csm_vessel2_search2='%s' data-csm_history_range='%s' data-csm_risk_level='%s' data-csm_status='%s' data-per_page='%d' class='prev-page button csm_check_load_next' data-paged='%d' href='javascript:;'>" .
                    "<span class='screen-reader-text'>%s</span>" .
                    "<span aria-hidden='true'>%s</span>" .
                '</a>',
                $this->csm_ports_filter,
                $this->selected_search,
                $this->csm_vessel1_search1,
                $this->csm_vessel2_search2,
                $this->csm_history_range,
                $this->csm_risk_level,
                $this->csm_status,
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
                "<a data-csm_ports_filter='%d' data-search='%s' data-csm_vessel1_search1='%s' data-csm_vessel2_search2='%s' data-csm_history_range='%s' data-csm_risk_level='%s' data-csm_status='%s' data-per_page='%d' data-paged='%d' data-current_recs='%d' class='next-page button csm_check_load_next' href='javascript:;'>" .
                    "<span class='screen-reader-text'>%s</span>" .
                    "<span aria-hidden='true'>%s</span>" .
                '</a>',
                $this->csm_ports_filter,
                $this->selected_search,
                $this->csm_vessel1_search1,
                $this->csm_vessel2_search2,
                $this->csm_history_range,
                $this->csm_risk_level,
                $this->csm_status,
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
                "<a data-csm_ports_filter='%d' data-search='%s' data-csm_vessel1_search1='%s' data-csm_vessel2_search2='%s' data-csm_history_range='%s' data-csm_risk_level='%s' data-csm_status='%s' data-per_page='%d' data-paged='%d' data-current_recs='%d' class='last-page button csm_check_load_next' href='javascript:;'>" .
                    "<span class='screen-reader-text'>%s</span>" .
                    "<span aria-hidden='true'>%s</span>" .
                '</a>',
                $this->csm_ports_filter,
                $this->selected_search,
                $this->csm_vessel1_search1,
                $this->csm_vessel2_search2,
                $this->csm_history_range,
                $this->csm_risk_level,
                $this->csm_status,
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