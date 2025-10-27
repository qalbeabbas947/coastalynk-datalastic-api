(function( $, window ) { 'use strict';
    
    $( document ).ready( function() {

        let coastalynk_Sbm = {
            init: function() {
                this.load_select_js();
                this.load_date_range_js(); 
                this.load_date_table(); 
                this.ajax_retrieve_draught(); 
            },
            load_date_table: function() {
                new DataTable('#coastalynk-sbm-table', {
                    layout: {
                        bottomEnd: { 
                            paging: {
                                firstLast: false
                            }
                        }
                    },
                    drawCallback: function(settings) {
                        $('.coastalynk-sbm-retrieve-draught-btn').on('click', function(e) {
                            let button = $(this);
                            
                            button.siblings('div').css('display', 'block');
                            button.css('display', 'none');
                            let uuid = $(this).data('uuid');
                            let name = $(this).data('name');
                            $.ajax({
                                type: 'POST',
                                aSync: false,
                                dataType: "html",
                                url: COSTALYNKVARS.ajaxURL, // URL from our localized variable
                                data: {
                                    action: 'coastalynk_retrieve_draught', // The WordPress hook to trigger
                                    nonce: COSTALYNKVARS.nonce,     // The nonce value
                                    selected_uuid: uuid,
                                    selected_name: name
                                },
                                success: function(response) {
                                    button.parent().html(response);
                                    
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    button.parent.find('img').css('display', 'none');
                                    button.parent.css('display', 'block');

                                }
                            });
                        });
                    }
                });
            },
            ajax_retrieve_draught: function() {
                // Example: Trigger the AJAX call on a button click
                $('.coastalynk-sbm-retrieve-draught-btn').on('click', function(e) {
                    let button = $(this);
                    
                    button.siblings('div').css('display', 'block');
                    button.css('display', 'none');
                    let uuid = $(this).data('uuid');
                    let name = $(this).data('name');
                    $.ajax({
                        type: 'POST',
                        aSync: false,
                        dataType: "html",
                        url: COSTALYNKVARS.ajaxURL, // URL from our localized variable
                        data: {
                            action: 'coastalynk_retrieve_draught', // The WordPress hook to trigger
                            nonce: COSTALYNKVARS.nonce,     // The nonce value
                            selected_uuid: uuid,
                            selected_name: name
                        },
                        success: function(response) {
                            button.parent().html(response);
                            
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            button.parent.find('img').css('display', 'none');
                            button.parent.css('display', 'block');

                        }
                    });
                });
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