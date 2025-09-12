(function( $, window ) { 'use strict';
    
    $( document ).ready( function() {

        let coastalynk_Sbm = {
            init: function() {
                this.menu_toggle_click();
                this.mark_active();
                this.checkScreenSize();
                this.on_resize();
            },
            on_resize: function(){
                $(window).on('resize', function() {
                    coastalynk_Sbm.checkScreenSize();
                });
            },
            mark_active: function(){
                $('.coastlynk-menu-toggle-open').on('click', function() {
                    $('#coastlynk-vessel-dashboard-menu').addClass('coastalynk-expanded');
                    $('#coastlynk-menu-toggle-close').css( 'display', 'block' );
                    $('.coastlynk-menu-toggle-open').css( 'display', 'none' );
                });

                $('#coastlynk-menu-toggle-close').on('click', function() {
                    $('#coastlynk-vessel-dashboard-menu').removeClass('coastalynk-expanded');
                    $('#coastlynk-menu-toggle-close').css( 'display', 'none' );
                    $('.coastlynk-menu-toggle-open').css( 'display', 'block' );
                });
            },
            menu_toggle_click: function(){
                $('.coastlynk-vessel-menu-item').on('click', function(  ) {
                    // Remove active class from all items
                    $('.coastlynk-vessel-menu-item').removeClass('active')
                    $(this).addClass('active')
                });
            },
            checkScreenSize: function () {
                $('.coastlynk-menu-toggle').css( 'display', 'none' );
                $('.coastalynk-expanded #coastlynk-menu-toggle-close').css( 'display', 'block' );
                if (window.innerWidth > 768) {
                    $('#coastlynk-vessel-dashboard-menu').addClass('coastalynk-expanded');
                    $('.coastalynk-expanded #coastlynk-menu-toggle-close').css( 'display', 'block' );
                } else {
                    $('#coastlynk-vessel-dashboard-menu').removeClass('coastalynk-expanded');
                    $('.coastlynk-menu-toggle-open').css( 'display', 'block' );
                }
            }
        }
        coastalynk_Sbm.init();
    });
})( jQuery, window );