(function( $ ) { 'use strict';
    
    $( document ).ready( function() {

        let CSM_Dark_Ships = {
            init: function() {

                CSM_Dark_Ships.load_data_from_cookies();
                CSM_Dark_Ships.display_new_page_darkships();
                CSM_Dark_Ships.display_dark_ships_onchange();
                CSM_Dark_Ships.display_dark_ships_search_submit();
                CSM_Dark_Ships.display_dark_ships();
                CSM_Dark_Ships.remove_dark_ship();
            },

            /**
             * Load data from the cookies
             */
            load_data_from_cookies: function() { 

                $('.csm_ports_filter').val(jQuery.cookie( 'darkship_csm_ports_filter' ) ).change();
                $('.coastalynk-general-search').val(jQuery.cookie( 'darkship_coastalynk-general-search') ).change();
            },

            /**
             * displays dark ships on pagination clicks
             */
			display_new_page_darkships: function() { 

                $( '#csm_darkships_data' ).on( 'click', '.tablenav-pages a, th a', function( e ) {
                    $( '.csm-coastlynk-order'   ).val(CSM_Dark_Ships.getParameterByName( 'order', $( this ).attr('href'))).change();
                    $( '.csm-coastlynk-orderby' ).val(CSM_Dark_Ships.getParameterByName( 'orderby', $( this ).attr('href') )).change();
                    
                    e.preventDefault();
                    var page = $( '.csm-coastlynk-page' ).val( $( this ).data( 'paged' ) ).change();
                    CSM_Dark_Ships.display_dark_ships();
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
             * Show dark_ships based on filters
             */
			display_dark_ships_onchange: function() {
                
                $('.coastalynk-search-search-button').on('click', function() {
                    $('.csm-display-coastlynk-type').val('filter').change();
                    $('.csm-coastlynk-page').val(1).change();
                    CSM_Dark_Ships.display_dark_ships();
                });
            },

            /**
             * Show dark_ships based on filters
             */
            display_dark_ships_search_submit: function() {

                $('#coastalynk-dark-ships-filter-text').on('submit', function(e) {
                    e.preventDefault();

                    $('.csm-display-coastlynk-type').val('text').change();
                    $('.csm-coastlynk-page').val(1).change();
                    CSM_Dark_Ships.display_dark_ships();
                });
                
            },
            /**
             * Display subscription details via popup
             */
            remove_dark_ship: function() {

                $( '#csm_darkships_data' ).on( 'click', '.csm_delete_dark_ship', function( e ) {

                    e.preventDefault();
                    if( confirm('Are you sure?')) {
                        var lnk = $( this );
                        let uuid = lnk.data('uuid');
                        let imo = lnk.data('imo');

                        $.ajax({
                            url: ajaxurl,
                            dataType: 'json',
                            data: {
                                action: 'csm_dark_ships_delete', 
                                security: CSM_ADMIN.security,
                                id:  uuid,
                                imo: imo
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
             * Display the Dark ships data based on ajax calls
             */
            display_dark_ships: function() {
				
                var columns_count = $('#csm_darkships_data table thead tr:eq(0)').find('th:not(.hidden)').length; 
                var placeholder = '<tr>';
                for( var i = 0; i < columns_count; i++ ) {
                    placeholder += '<td align="center">' + CSM_ADMIN.preloader_gif_img + '</td>';
                }

                placeholder += '</tr>';
                $( '#csm_darkships_data table tbody' ).html( placeholder ).change();
               
                var csmpage       = $( '.csm-coastlynk-page' ).val();
                var order_str       = $( '.csm-coastlynk-order' ).val();
                var orderby_str     = $( '.csm-coastlynk-orderby' ).val();

                let display_type = $( '.csm-display-coastlynk-type' ).val();
                if( display_type == 'text' ) {
                    var csm_ports_filter     = '';
                    var general_search     = $( '.coastalynk-general-search' ).val();
                    jQuery.cookie( 'darkship_coastalynk-general-search', general_search, { expires: 30, path: '/' } );
                    jQuery.cookie( 'darkship_csm_ports_filter', '', { expires: 30, path: '/' } );
                
                } else {
                    var csm_ports_filter     = $( '.csm_ports_filter' ).val();
                    var general_search     = '';
                    jQuery.cookie( 'darkship_coastalynk-general-search', '', { expires: 30, path: '/' } );
                    jQuery.cookie( 'darkship_csm_ports_filter', csm_ports_filter, { expires: 30, path: '/' } );
                }

                $.ajax({
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        action: 'csm_dark_ships_display', 
                        security: CSM_ADMIN.security,
                        paged:  csmpage,
                        csm_ports_filter: csm_ports_filter,
                        order: order_str,
                        search: general_search,
                        orderby: orderby_str,
                    },
                    success: function ( response ) {
                        
                        $("#csm_darkships_data").html( response.display ).change();
                        $( "tbody" ).on( "click", ".toggle-row", function( e ) {
                            $( this ).closest( "tr" ).toggleClass( "is-expanded" )
                        });
                    }
                });
            },
        };

        CSM_Dark_Ships.init();
    });   
})( jQuery );