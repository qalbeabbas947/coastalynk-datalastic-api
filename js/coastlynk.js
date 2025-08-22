(function( $ ) { 'use strict';
    
    $( document ).ready( function() {

        let coastalynk = {
            init: function() {
                this.load_data_table();
                this.load_port_data();
                
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
//
                
            }
        };
        coastalynk.init();
    });
})( jQuery );
