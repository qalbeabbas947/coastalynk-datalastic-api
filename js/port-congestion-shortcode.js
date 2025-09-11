(function( $ ) { 'use strict';
    
    $( document ).ready( function() {

        let coastalynk = {
            init: function() {
                this.load_data_table();
                this.load_port_data();
                this.ajax_retrieve_tonnage();
                this.load_ajax_port_congestion();
            },
            ajax_retrieve_tonnage: function() {
                // Example: Trigger the AJAX call on a button click
                $('.coastalynk-retrieve-tonnage-btn').on('click', function(e) {
                    let button = $(this);

                    button.siblings('div').css('display', 'block');
                    button.css('display', 'none');
                    let uuid = $(this).data('uuid');
                    let name = $(this).data('name');
                    $.ajax({
                        type: 'POST',
                        aSync: false,
                        dataType: "html",
                        url: COSTALUNKVARS.ajaxURL, // URL from our localized variable
                        data: {
                            action: 'coastalynk_retrieve_tonnage', // The WordPress hook to trigger
                            nonce: COSTALUNKVARS.nonce,     // The nonce value
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
            
            load_ajax_port_congestion: function() {
                // Example: Trigger the AJAX call on a button click
                $('.coastalynk-port-selector button').on('click', function(e) {
                    e.preventDefault();
                    let port  = $(this).data( "port" );
                    if( port == 'all' ) {
                        port == '';
                    }
                    // Show a loading indicator for better UX
                    $('.coastalynk-congestion-loader').css("display", "block");
                    $( '.coastalynk-congestion-data' ).html("").css("display", "none");
                    // Perform the AJAX POST request
                    $.ajax({
                        type: 'POST',
                        aSync: false,
                        dataType: "html",
                        url: COSTALUNKVARS.ajaxURL, // URL from our localized variable
                        data: {
                            action: 'coastalynk_load_port_congestion', // The WordPress hook to trigger
                            nonce: COSTALUNKVARS.nonce,     // The nonce value
                            selected_port: port
                        },
                        success: function(response) {
                            $( '.coastalynk-congestion-data' ).html(response).css("display", "block");
                            $('.coastalynk-congestion-loader').css("display", "none");
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $('.coastalynk-congestion-loader').css("display", "none");
                            $( '.coastalynk-congestion-data' ).html(errorThrown);

                        }
                    });
                }).trigger('click');
            },
            load_data_table: function() {
                new DataTable('#coastalynk-table', {
                    layout: {
                        bottomEnd: { 
                            paging: {
                                firstLast: false
                            }
                        }
                    }
                });
            },
            load_port_data: function() {
                $('.coastlynk-port-card').on('click', function(e) {
                    e.stopPropagation();
                    let port = $(this).data('port');
                    var table = $('#coastalynk-table').DataTable();
                    table.column(1).search(port).draw();
                    
                });
            }
        };
        coastalynk.init();
    });
})( jQuery );
