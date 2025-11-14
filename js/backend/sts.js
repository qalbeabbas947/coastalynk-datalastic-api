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
            },

            /**
             * Load data from the cookies
             */
            load_data_from_cookies: function() { 

                $('.csm_ports_filter').val(jQuery.cookie( 'sts_csm_ports_filter' ) ).change();
                $('.coastalynk-general-search').val(jQuery.cookie( 'sts_coastalynk-general-search') ).change();
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

                let display_type = $( '.csm-display-coastlynk-type' ).val();
                if( display_type == 'text' ) {
                    var csm_ports_filter     = '';
                    var general_search     = $( '.coastalynk-general-search' ).val();
                    jQuery.cookie( 'sts_coastalynk-general-search', general_search, { expires: 30, path: '/' } );
                    jQuery.cookie( 'sts_csm_ports_filter', '', { expires: 30, path: '/' } );
                
                } else {
                    var csm_ports_filter     = $( '.csm_ports_filter' ).val();
                    var general_search     = '';
                    jQuery.cookie( 'sts_coastalynk-general-search', '', { expires: 30, path: '/' } );
                    jQuery.cookie( 'sts_csm_ports_filter', csm_ports_filter, { expires: 30, path: '/' } );
                }

                $.ajax({
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        action: 'csm_sts_display', 
                        security: CSM_ADMIN.security,
                        paged:  csmpage,
                        csm_ports_filter: csm_ports_filter,
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