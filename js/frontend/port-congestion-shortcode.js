(function( $ ) { 'use strict';
    
    $( document ).ready( function() {

        let coastalynk = {
            date_obj: null,
            init: function() {
                this.load_select_js();
                this.load_date_range_js();
                this.load_data_table();
                this.load_port_data();
                this.ajax_retrieve_tonnage();
                this.load_ajax_port_congestion();
                this.load_popup();
                this.close_popup();
                this.btn_filter_port_congestion();
                this.btn_export_port_congestion_history();
            },
            btn_export_port_congestion_history: function() {
                $('.coastalynk-history-button-export-csv').on('click', function(event) {
                    event.preventDefault();
                    $(this).find('.coastalynk-column-loader').css('display', 'inline-block');
                    $('#coastalynk_congestion_history_load_action_ctrl').val("coastalynk_congestion_history_export_action");
                    $('#coastalynk-port-congestion-history-form').submit();
                    $(this).find('.coastalynk-column-loader').css('display', 'none');
                });
            },
            load_select_js: function() {
                $('.coastalynk-select2-js').select2({ width: '100%' });
                $('.caostalynk_history_ddl_dates').select2({ width: '100%' });
                $('.caostalynk_history_ddl_times').select2({ width: '100%' });
            },
            load_date_range_js: function() {
               $('#caostalynk_congestion_history_range').daterangepicker({
                    timePicker: true,
                    startDate: moment().startOf('hour'),
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
                    endDate: moment().startOf('hour').add(32, 'hour'),
                    locale: {
                    format: 'M/DD/YYYY hh:mm A'
                    }
                });
                $('#caostalynk_congestion_history_range').on('apply.daterangepicker', function(ev, picker) {
                    console.log(picker.startDate.format('YYYY-MM-DD'));
                    console.log(picker.endDate.format('YYYY-MM-DD'));
                    console.log($('#caostalynk_congestion_history_range').val());
                });
            },
            close_popup: function() {
                document.querySelector('#coastalynk-history-popup-close').addEventListener( 'click', (event) => {
                    document.querySelector(".coastalynk-history-popup-overlay").style.display = 'none';
                    document.querySelector(".coastalynk-history-popup-content").style.display = 'none';
                });

                document.querySelector('.coastalynk-history-popup-overlay').addEventListener( 'click', () => {
                    document.querySelector(".coastalynk-history-popup-overlay").style.display = 'none';
                    document.querySelector(".coastalynk-history-popup-content").style.display = 'none';
                });
            },
            load_date_time_ddls: function() {
                if( this.date_obj != null ) {
                    $('.caostalynk_history_ddl_dates').empty();
                    $('.caostalynk_history_ddl_dates').val(null).trigger('change');
                    var date_all_opt = new Option(this.date_obj.date_all, '', false, false); 
                    $('.caostalynk_history_ddl_dates').append(date_all_opt).trigger('change');

                    for( let date in this.date_obj.options ) {
                        console.log(date);
                        let date_opt = new Option(date, date, false, false); 
                        $('.caostalynk_history_ddl_dates').append(date_opt).trigger('change');
                        
                        $('.caostalynk_history_ddl_times').val(null).trigger('change');
                        var time_all_opt = new Option(this.date_obj.time_all, '', false, false); 
                        $('.caostalynk_history_ddl_times').append(time_all_opt).trigger('change');
                        for( let time in this.date_obj.options[date] ) {
                            let time_opt = new Option(this.date_obj.options[date][time], this.date_obj.options[date][time], false, false); 
                            $('.caostalynk_history_ddl_times').append(time_opt).trigger('change');
                        }
                    }
                }
                
            },
            btn_filter_port_congestion: function(){

                $('.coastalynk-history-button').on('click', function(event) {
                    event.preventDefault();
                    var preloader = $(this).find('.coastalynk-column-loader');
                    preloader.css('display', 'inline-block');
                    const data = new URLSearchParams();
                    data.append('nonce', COSTALYNKVARS.nonce);
                    data.append('action', 'coastalynk_congestion_history_ports_data');
                    data.append('port', $('.coastalynk-select2-js').val());
                    data.append('dates', $('#caostalynk_congestion_history_range').val());
                    fetch(COSTALYNKVARS.ajaxURL, {
                        method: 'POST',
                        body: data // Or new URLSearchParams(data) for form data
                    }) 
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json(); // Or response.text() for plain text
                    })
                    .then( data => {
                        if( data.success == true ) {
                            coastalynk.date_obj = data.data;
                            coastalynk.load_date_time_ddls();
                        } else {
                            alert(data.data.message);
                        }

                        $('.coastalynk-congestion-history-dates').css('display', 'block');
                        $('.coastalynk-congestion-history-times').css('display', 'block');
                        preloader.hide();
                        
                    })
                    .catch(error => {
                        preloader.hide();
                        console.error('There was a problem with the fetch operation:', error);
                    });
                });
                
            },
            load_popup: function() { 

                $('.coastalynk-port-history').on('click', function(event) {
                    event.preventDefault();

                    document.querySelector(".coastalynk-history-popup-overlay").style.display = 'block';
                    document.querySelector(".coastalynk-history-popup-content").style.display = 'block';
                    $('.coastalynk-history-button1').trigger('click');
                });
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
                        url: COSTALYNKVARS.ajaxURL, // URL from our localized variable
                        data: {
                            action: 'coastalynk_retrieve_tonnage', // The WordPress hook to trigger
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
            
            load_ajax_port_congestion: function() {
                // Example: Trigger the AJAX call on a button click
                $('.coastalynk-port-selector button.port-button').on('click', function(e) {
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
                        url: COSTALYNKVARS.ajaxURL, // URL from our localized variable
                        data: {
                            action: 'coastalynk_load_port_congestion', // The WordPress hook to trigger
                            nonce: COSTALYNKVARS.nonce,     // The nonce value
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
                });

                $('.coastalynk-port-selector button.port-button').eq(1).trigger('click');
            },
            load_data_table: function() {
                new DataTable('#coastalynk-table', {
                    layout: {
                        bottomEnd: { 
                            paging: {
                                firstLast: false
                            }
                        }
                    },
                    drawCallback: function(settings) {
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
                                url: COSTALYNKVARS.ajaxURL, // URL from our localized variable
                                data: {
                                    action: 'coastalynk_retrieve_tonnage', // The WordPress hook to trigger
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
            load_port_data: function() {
                $('.coastlynk-port-card').on('click', function(e) {
                    e.stopPropagation();
                    let port = $(this).data('port');
                    
                    var table = $('#coastalynk-table').DataTable();
                    table.column(2).search(port).draw();
                    
                });
            }
        };
        coastalynk.init();
    });
})( jQuery );
