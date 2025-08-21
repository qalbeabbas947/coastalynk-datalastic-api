(function( $ ) { 'use strict';
    
    $( document ).ready( function() {

        let coastalynk = {
            init: function() {
                new DataTable('#coastalynk-table', {
                    layout: {
                        bottomEnd: {
                            paging: {
                                firstLast: false
                            }
                        }
                    }
                });
            }
        };
        coastalynk.init();
    });
})( jQuery );
