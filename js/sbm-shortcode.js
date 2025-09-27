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

                

                $('.coastlynk-menu-dashboard-open-close-burger').on('click', function() {
                    $('#coastlynk-menu-toggle-close').css( 'display', 'none' );
                    $('.coastlynk-menu-toggle-open').css( 'display', 'none' );

                    if( $('#coastlynk-vessel-dashboard-menu').hasClass('coastalynk-expanded') ) {
                        $('#coastlynk-vessel-dashboard-menu').removeClass('coastalynk-expanded').css( 'display', 'none' );
                    } else {
                        $('#coastlynk-vessel-dashboard-menu').addClass('coastalynk-expanded').css( 'display', 'block' );
                        
                    }
                    
                });

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

               if( isMobile.any() ) {
                    $('#coastlynk-vessel-dashboard-menu').removeClass('coastalynk-expanded').css( 'display', 'none' );
                    $('.coastlynk-menu-toggle').css( 'display', 'none' );
                    $('.coastlynk-menu-dashboard-open-close-burger').css( 'display', 'block' );
               } else if( isTablet() ) {
                    $('#coastlynk-vessel-dashboard-menu').removeClass('coastalynk-expanded').css( 'display', 'none' );
                    $('.coastlynk-menu-toggle').css( 'display', 'none' );
                    $('.coastlynk-menu-dashboard-open-close-burger').css( 'display', 'block' );alert('')
               } else {
                    $('.coastlynk-menu-toggle').css( 'display', 'block' );
                    $('.coastlynk-menu-toggle-open').css( 'display', 'none' );
                    $('.coastlynk-menu-dashboard-open-close-burger').css( 'display', 'none' );
               }
                    
            }
        }

        function  isTablet(){
            const userAgent = navigator.userAgent.toLowerCase();
            const isiPad = /ipad/.test(userAgent);
            const isAndroidTablet = /android/.test(userAgent) && !/mobile/.test(userAgent);
            const isWindowsTablet = /windows/.test(userAgent) && /touch/.test(userAgent);
            
            return isiPad || isAndroidTablet || isWindowsTablet;
        }

        const isMobile = {
            Android: function() {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function() {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function() {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function() {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function() {
                return navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/WPDesktop/i);
            },
            any: function() {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        };
        coastalynk_Sbm.init();
    });
})( jQuery, window );