(function( $, window ) { 'use strict';
    
    $( document ).ready( function() {

        let coastalynk_Sbm = {
            init: function() {
                this.load_select_js();
                this.load_date_range_js(); 
            },
            load_select_js: function() {
                $('.coastalynk-sbm-select2-js').select2({ width: '100%' });
            },
            load_date_range_js: function() {
                $('#caostalynk_sbm_history_range').daterangepicker({
                        timePicker: true,
                        startDate: moment().subtract(6, 'days'),
                        "maxSpan": {
                            "months": 1
                        },  
                        ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                        },
                        endDate: moment(),
                        locale: {
                        format: 'M/DD/YYYY hh:mm A'
                    }
                });
                
            },
        }
        
        coastalynk_Sbm.init();
    });
})( jQuery, window );