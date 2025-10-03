(function ($) {
  "use strict";
  $(document).ready(function () {
    var Shortcode = {
      init: function () {
        this.prebutton(); 
        this.nextButton(); 
        this.load_select_js();
        this.load_date_range_js(); 
      },
      prebutton: function(){
        $('#coastalynk-port-prev-btn').on( 'click', function(){
          document.querySelector('.coastalynk-port-scroll-menu').scrollBy({ left: -200, behavior: 'smooth' }); // Adjust -200 for desired scroll distance
        });
      },
      nextButton: function(){
        $('#coastalynk-port-next-btn').on( 'click', function(){
          document.querySelector('.coastalynk-port-scroll-menu').scrollBy({ left: 200, behavior: 'smooth' }); // Adjust -200 for desired scroll distance
        });
      },
      load_select_js: function() {
          $('.coastalynk-sts-select2-js').select2({ width: '100%' });
      },
      load_date_range_js: function() {
          $('#caostalynk_sts_history_range').daterangepicker({
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

      
    };
    Shortcode.init();
  });
})(jQuery);