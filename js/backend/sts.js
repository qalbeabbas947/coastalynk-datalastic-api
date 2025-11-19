(function( $ ) { 'use strict';
    
    $( document ).ready( function() {

        let CSM_STS = {
            init: function() {

                CSM_STS.load_data_from_cookies();
                CSM_STS.display_new_page_sts();
                CSM_STS.display_sts_onchange();
                CSM_STS.display_sts_search_submit();
                CSM_STS.display_sts();
                CSM_STS.remove_sts();
                CSM_STS.load_popup();
                CSM_STS.close_popup();
                CSM_STS.edit_information();
                CSM_STS.update_information();
                CSM_STS.back_to_information();
                CSM_STS.load_datetime_pickers();
                CSM_STS.load_date_range_js();
            },
            load_datetime_pickers: function() {
                $(".coastalynk-field-sts-date").each(function () {
                    $(this).datetimepicker();
                });
            },
            load_date_range_js: function() {
                $('.coastalynk-date-range-js').daterangepicker({
                    timePicker: true,
                    startDate: moment().subtract(6, 'days'),
                    // "maxSpan": {
                    //     "months": 1
                    // },  
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
            back_to_information: function() {
                $('.coastalynk-cancel-sts-button').on('click', function(event) { 
                    $('.coastalynk-popup-sts-content-boxes').css( 'display', 'flex' );
                    $('.coastalynk-popup-top-bar').css( 'display', 'block' );
                    $('.coastalynk-edit-sts-button').css( 'display', 'inline-block' );
                    $('.coastalynk-popup-sts-content-form').css( 'display', 'none' );
                    $('.coastalynk-update-sts-button').css( 'display', 'none' );
                    $('.coastalynk-cancel-sts-button').css( 'display', 'none' );
                    $('.coastalynk-popup-top-message').css( 'display', 'none' );
                });
             },
            update_information: function() {
                $('.coastalynk-update-sts-button').on('click', function(event) { 
                    $('.coastalynk-column-sts-popup-loader').css( 'display', 'inline-block' );
                    $.ajax({
                        type: 'POST',
                        aSync: false,
                        dataType: "json",
                        url: ajaxurl, // URL from our localized variable
                        data: {
                            action: 'coastalynk_update_sts', // The WordPress hook to trigger
                            security: CSM_ADMIN.security,     // The nonce value
                            start_date: $('#coastalynk-field-start-date').val(),
                            end_date: $('#coastalynk-field-end-date').val(),
                            port_zone: $('#coastalynk-field-port-zone').val(), 
                            vessel1_before_draught: $('#coastalynk-field-vessel1-before-draught').val(),
                            vessel1_after_draught: $('#coastalynk-field-vessel1-after-draught').val(),
                            vessel2_before_draught: $('#coastalynk-field-vessel2-before-draught').val(),
                            vessel2_after_draught: $('#coastalynk-field-vessel2-after-draught').val(),
                            status: $('#coastalynk-field-status').val(),
                            comments: $('#coastalynk-field-comments').val(),
                            id: $('#coastalynk-field-id').val(),
                        },
                        success: function(response) {
                            console.log(response);
                            
                            $('.coastalynk-popup-top-message').css( 'display', 'block' ).addClass('coastalynk-popup-top-message-'+response.type).html(response.message);
                            $('.coastalynk-column-sts-popup-loader').css( 'display', 'none' );
                            
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $('.coastalynk-column-sts-popup-loader').css( 'display', 'none' );
                        }
                    });
                });
            },
            edit_information: function() {
                $('.coastalynk-edit-sts-button').on('click', function(event) { 
                   
                    $('.coastalynk-popup-sts-content-boxes').css( 'display', 'none' );
                    $('.coastalynk-popup-top-bar').css( 'display', 'none' );
                    $('.coastalynk-edit-sts-button').css( 'display', 'none' );
                    $('.coastalynk-popup-sts-content-form').css( 'display', 'flex' );
                    $('.coastalynk-update-sts-button').css( 'display', 'inline-block' );
                    $('.coastalynk-cancel-sts-button').css( 'display', 'inline-block' );
                    
                });
            },
            close_popup: function() {
                document.querySelector('#coastalynk-popup-close').addEventListener( 'click', (event) => {
                
                    document.querySelector(".coastalynk-popup-overlay").style.display = 'none';
                    document.querySelector(".coastalynk-popup-content").style.display = 'none';
                });

                document.querySelector('.coastalynk-popup-overlay').addEventListener( 'click', () => {
                    document.querySelector(".coastalynk-popup-overlay").style.display = 'none';
                    document.querySelector(".coastalynk-popup-content").style.display = 'none';
                });
            },
            load_popup: function() {

                $('#csm_sts_data').on('click', '.csm_view_sts', function(event) {
                    event.preventDefault();
                    
                    var data = $(this).data();
                    console.log(data);
                    $(".coastalynk-popup-overlay").show();
                    $(".coastalynk-popup-content").show();;

                    $.ajax({
                        type: 'POST',
                        aSync: false,
                        dataType: "html",
                        url: ajaxurl, // URL from our localized variable
                        data: {
                            action: 'coastalynk_retrieve_tonnage', // The WordPress hook to trigger
                            nonce: CSM_ADMIN.nonce,     // The nonce value
                            selected_uuid: data.vessel1_uuid,
                            selected_name: data.vessel1_name
                        },
                        success: function(response) {
                            $(".coastalynk-sts-popup-content-vessel1_tonnage").html(response);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        aSync: false,
                        dataType: "html",
                        url: ajaxurl, // URL from our localized variable
                        data: {
                            action: 'coastalynk_retrieve_tonnage', // The WordPress hook to trigger
                            nonce: CSM_ADMIN.nonce,     // The nonce value
                            selected_uuid: data.vessel2_uuid,
                            selected_name: data.vessel2_name
                        },
                        success: function(response) {
                            $(".coastalynk-sts-popup-content-vessel2_tonnage").html(response);

                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                        }
                    });
                    $(".coastalynk-sts-popup-content-vessel1_tonnage").html('<div id="coastalynk-field-column-loader"><div id="coastalynk-field-column-blockG_1" class="coastalynk-field-column-blockG"></div><div id="coastalynk-field-column-blockG_2" class="coastalynk-field-column-blockG"></div><div id="coastalynk-field-column-blockG_3" class="coastalynk-field-column-blockG"></div></div>')
                    $(".coastalynk-sts-popup-content-vessel2_tonnage").html('<div id="coastalynk-field-column-loader"><div id="coastalynk-field-column-blockG_1" class="coastalynk-field-column-blockG"></div><div id="coastalynk-field-column-blockG_2" class="coastalynk-field-column-blockG"></div><div id="coastalynk-field-column-blockG_3" class="coastalynk-field-column-blockG"></div></div>')        
                    $(".coastalynk-sts-popup-content-vessel1_name").html(data.vessel1_name)

                    $(".coastalynk-popup-vessel1-parent").css('display', 'none');  
                    $(".coastalynk-popup-vessel2-parent").css('display', 'none');  
                    if( parseInt(data.mother_vessel_number ) == 1 ) {
                        $(".coastalynk-popup-vessel1-parent").css('display', 'inline-block');    
                    } else if( parseInt(data.mother_vessel_number ) == 2 ) {
                        $(".coastalynk-popup-vessel2-parent").css('display', 'inline-block');
                    }
                    
                    $('#coastalynk-field-start-date').val(data.start_date);
                    $('#coastalynk-field-end-date').val(data.end_date);
                    $('#coastalynk-field-port-zone').val(data.zone_terminal_name);
                    $('#coastalynk-field-vessel1-before-draught').val(data.vessel1_draught);
                    $('#coastalynk-field-vessel1-after-draught').val(data.vessel1_completed_draught);
                    $('#coastalynk-field-vessel2-before-draught').val(data.vessel2_draught);
                    $('#coastalynk-field-vessel2-after-draught').val(data.vessel2_completed_draught);
                    $('#coastalynk-field-status').val(data.status);
                    $('#coastalynk-field-comments').val(data.remarks);
                    $('#coastalynk-field-id').val(data.id);

                    $(".coastalynk-sts-popup-content-vessel1_mmsi").html(data.vessel1_mmsi)
                    $(".coastalynk-sts-popup-content-vessel1_imo").html(data.vessel1_imo)
                    $(".coastalynk-sts-popup-content-vessel1_country_iso").html(data.vessel1_country_iso)
                    $(".coastalynk-sts-popup-content-vessel1_type").html(data.vessel1_type)
                    $(".coastalynk-sts-popup-content-vessel1_type_specific").html(data.vessel1_type_specific)
                    $(".coastalynk-sts-popup-content-vessel1_speed").html(data.vessel1_speed)
                    $(".coastalynk-sts-popup-content-vessel1_navigation_status").html(data.vessel1_navigation_status)
                    $(".coastalynk-sts-popup-content-vessel1_draught").html(data.vessel1_draught)
                    $(".coastalynk-sts-popup-content-vessel1_completed_draught").html(data.vessel1_completed_draught)
                    $(".coastalynk-sts-popup-content-vessel1_last_position_UTC").html(data.vessel1_last_position_utc)
                    $(".coastalynk-sts-popup-content-vessel2_name").html(data.vessel2_name)
                    $(".coastalynk-sts-popup-content-vessel2_mmsi").html(data.vessel2_mmsi)
                    $(".coastalynk-sts-popup-content-vessel2_imo").html(data.vessel2_imo)
                    $(".coastalynk-sts-popup-content-vessel2_country_iso").html(data.vessel2_country_iso)
                    $(".coastalynk-sts-popup-content-vessel2_type").html(data.vessel2_type)
                    $(".coastalynk-sts-popup-content-vessel2_type_specific").html(data.vessel2_type_specific)
                    $(".coastalynk-sts-popup-content-vessel2_speed").html(data.vessel2_speed)
                    $(".coastalynk-sts-popup-content-vessel2_navigation_status").html(data.vessel2_navigation_status)
                    $(".coastalynk-sts-popup-content-vessel2_draught").html(data.vessel2_draught)
                    $(".coastalynk-sts-popup-content-vessel2_completed_draught").html(data.vessel2_completed_draught)
                    $(".coastalynk-sts-popup-content-vessel2_last_position_UTC").html(data.vessel1_last_position_utc)
                    if( data.zone_terminal_name != '' ) {
                        $(".coastalynk-popup-approved-zone").css('display', 'inline-block');
                        $(".coastalynk-popup-unapproved-zone").css('display', 'none');
                    } else {
                        $(".coastalynk-popup-unapproved-zone").css('display', 'inline-block');
                        $(".coastalynk-popup-approved-zone").css('display', 'none');
                    }
                    $(".coastalynk-sts-popup-content-port").html(data.port);
                    $(".coastalynk-sts-popup-content-estimated_cargo").html(data.estimated_cargo);

                    if( data.vessel1_signal == 'AIS Consistent' ) {
                        $(".coastalynk-sts-popup-content-vessel1-ais-signal").html(data.vessel1_signal + ' <i class="fa fa-check-square" aria-hidden="true"></i>');
                    } else if( data.vessel1_signal == 'AIS Gap' ) {
                        $(".coastalynk-sts-popup-content-vessel1-ais-signal").html(data.vessel1_signal + ' <i class="fa fa-exclamation" aria-hidden="true"></i>');
                    }
                    
                    if( data.vessel2_signal == 'AIS Consistent' ) {
                        $(".coastalynk-sts-popup-content-vessel2-ais-signal").html(data.vessel2_signal + ' <i class="fa fa-check-square" aria-hidden="true"></i>');
                    } else if( data.vessel1_signal == 'AIS Gap' ) {
                        $(".coastalynk-sts-popup-content-vessel2-ais-signal").html(data.vessel2_signal + ' <i class="fa fa-exclamation" aria-hidden="true"></i>');
                    }

                    $(".coastalynk-sts-popup-content-event_ref_id").html(data.event_ref_id);
                    $(".coastalynk-sts-popup-content-is_sts_zone").html(data.is_sts_zone);
                    $(".coastalynk-sts-popup-content-zone_terminal_name").html(data.zone_terminal_name);
                    $(".coastalynk-sts-popup-content-start_date").html(data.start_date);
                    $(".coastalynk-sts-popup-content-end_date").html(data.end_date);
                    $(".coastalynk-sts-popup-content-remarks").html(data.remarks);
                    $(".coastalynk-sts-popup-content-event_percentage").html(data.event_percentage);
                    $(".coastalynk-sts-popup-content-cargo_category_type").html(data.cargo_category_type);
                    $(".coastalynk-sts-popup-content-risk_level").html(data.risk_level);
                    $(".coastalynk-sts-popup-content-current_distance_nm").html(data.current_distance_nm);
                    $(".coastalynk-sts-popup-content-stationary_duration_hours").html(data.stationary_duration_hours);
                    $(".coastalynk-sts-popup-content-proximity_consistency").html(data.proximity_consistency);
                    $(".coastalynk-sts-popup-content-data_points_analyzed").html(data.data_points_analyzed);
                    $(".coastalynk-sts-popup-content-operationmode").html(data.operationmode);
                    $(".coastalynk-sts-popup-content-status").html(data.status);
                    $(".coastalynk-sts-popup-content-last_updated").html(data.last_updated);
                });
            },
            /**
             * Load data from the cookies
             */
            load_data_from_cookies: function() { 

                $('.csm_ports_filter').val(jQuery.cookie( 'sts_csm_ports_filter' ) ).change();
                $('.coastalynk-general-search').val(jQuery.cookie( 'sts_coastalynk-general-search') ).change();
                $('.coastalynk-general-vessel1-search').val(jQuery.cookie( 'sts_csm_vessel1_search1') ).change();
                $('.coastalynk-general-vessel2-search').val(jQuery.cookie( 'sts_csm_vessel2_search2') ).change();
                $('.caostalynk_sts_history_range').val(jQuery.cookie( 'sts_csm_history_range') ).change();
                $('#coastalynk-field-risk-level').val(jQuery.cookie( 'sts_csm_risk_level') ).change();
            },
            /**
             * displays dark ships on pagination clicks
             */
			display_new_page_sts: function() { 

                $( '#csm_sts_data' ).on( 'click', '.tablenav-pages a, th a', function( e ) {
                    $( '.csm-coastlynk-order'   ).val(CSM_STS.getParameterByName( 'order', $( this ).attr('href'))).change();
                    $( '.csm-coastlynk-orderby' ).val(CSM_STS.getParameterByName( 'orderby', $( this ).attr('href') )).change();
                    
                    e.preventDefault();
                    var page = $( '.csm-coastlynk-page' ).val( $( this ).data( 'paged' ) ).change();
                    CSM_STS.display_sts();
                });
            },
 
            /**
             * get Parameter By Name
             */
            getParameterByName: function( name, url ) {
                
                name = name.replace(/[\[\]]/g, '\\$&');
                
                var regex = new RegExp( '[?&]' + name + '(=([^&#]*)|&|#|$)' ), 
                            results = regex.exec( url );
                if ( ! results ) return null;
                if ( ! results[2] ) return '';
                return decodeURIComponent( results[2].replace(/\+/g, ' ') );
            },

            /**
             * Show sts based on filters
             */
			display_sts_onchange: function() {
                
                $('.coastalynk-search-search-button').on('click', function() {
                    $('.csm-display-coastlynk-type').val('filter').change();
                    $('.csm-coastlynk-page').val(1).change();
                    CSM_STS.display_sts();
                });
            },

            /**
             * Show sts based on filters
             */
            display_sts_search_submit: function() {

                $('#coastalynk-dark-ships-filter-text').on('submit', function(e) {
                    e.preventDefault();

                    $('.csm-display-coastlynk-type').val('text').change();
                    $('.csm-coastlynk-page').val(1).change();
                    CSM_STS.display_sts();
                });
                
            },
            /**
             * Display subscription details via popup
             */
            remove_sts: function() {

                $( '#csm_sts_data' ).on( 'click', '.csm_delete_sts', function( e ) {

                    e.preventDefault();
                    if( confirm('Are you sure?')) {
                        var lnk = $( this );
                        let uuid = lnk.data('id');
                        let event_ref_id = lnk.data('event_ref_id');

                        $.ajax({
                            url: ajaxurl,
                            dataType: 'json',
                            data: {
                                action: 'csm_sts_delete', 
                                security: CSM_ADMIN.security,
                                id:  uuid,
                                event_ref_id: event_ref_id
                            },
                            success: function ( response ) {
                                if( response.type == 'success' ) {
                                    alert(response.message);
                                    location.reload();
                                } else {
                                    alert(response.message);
                                }
                            }
                        });
                    }
                });
            },
            /**
             * Display the sts data based on ajax calls
             */
            display_sts: function() {
				
                var columns_count = $('#csm_sts_data table thead tr:eq(0)').find('th:not(.hidden)').length; 
                var placeholder = '<tr>';
                for( var i = 0; i < columns_count; i++ ) {
                    placeholder += '<td align="center">' + CSM_ADMIN.preloader_gif_img + '</td>';
                }

                placeholder += '</tr>';
                $( '#csm_sts_data table tbody' ).html( placeholder ).change();
               
                var csmpage       = $( '.csm-coastlynk-page' ).val();
                var order_str       = $( '.csm-coastlynk-order' ).val();
                var orderby_str     = $( '.csm-coastlynk-orderby' ).val();

                var vessel1_search1      = '';
                var vessel2_search2      = '';
                var history_range        = '';
                var risk_level           = '';
                var status               = '';

                let display_type = $( '.csm-display-coastlynk-type' ).val();
                if( display_type == 'text' ) {
                    var csm_ports_filter     = '';
                    var general_search     = $( '.coastalynk-general-search' ).val();
                    jQuery.cookie( 'sts_coastalynk-general-search', general_search, { expires: 30, path: '/' } );
                    jQuery.cookie( 'sts_csm_ports_filter', '', { expires: 30, path: '/' } );
                
                } else {
                    csm_ports_filter     = $( '.csm_ports_filter' ).val();
                    vessel1_search1      = $( '.coastalynk-general-vessel1-search' ).val();
                    vessel2_search2      = $( '.coastalynk-general-vessel2-search' ).val();
                    history_range        = $( '.caostalynk_sts_history_range' ).val();
                    risk_level           = $( '#coastalynk-field-risk-level' ).val();
                    status               = $( '#coastalynk-field-status' ).val();
                    var general_search     = '';
                    jQuery.cookie( 'sts_coastalynk-general-search', '', { expires: 30, path: '/' } );
                    jQuery.cookie( 'sts_csm_ports_filter', csm_ports_filter, { expires: 30, path: '/' } );
                    jQuery.cookie( 'sts_csm_vessel1_search1', vessel1_search1, { expires: 30, path: '/' } );
                    jQuery.cookie( 'sts_csm_vessel2_search2', vessel2_search2, { expires: 30, path: '/' } );
                    jQuery.cookie( 'sts_csm_status', status, { expires: 30, path: '/' } );
                    jQuery.cookie( 'sts_csm_history_range', history_range, { expires: 30, path: '/' } );
                    jQuery.cookie( 'sts_csm_risk_level', risk_level, { expires: 30, path: '/' } );
                }

                $.ajax({
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        action: 'csm_sts_display', 
                        security: CSM_ADMIN.security,
                        paged:  csmpage,
                        csm_ports_filter: csm_ports_filter,
                        csm_vessel1_search1: vessel1_search1,
                        csm_vessel2_search2: vessel2_search2,
                        csm_history_range: history_range,
                        csm_risk_level: risk_level,
                        csm_status: status,
                        order: order_str,
                        search: general_search,
                        orderby: orderby_str,
                    },
                    success: function ( response ) {
                        
                        $("#csm_sts_data").html( response.display ).change();
                        $( "tbody" ).on( "click", ".toggle-row", function( e ) {
                            $( this ).closest( "tr" ).toggleClass( "is-expanded" )
                        });
                    }
                });
            },
        };

        CSM_STS.init();
    });   
})( jQuery );