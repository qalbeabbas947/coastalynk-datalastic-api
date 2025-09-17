(function ($) {
  "use strict";
  $(document).ready(function () {
    var Shortcode = {
      init: function () {
        this.load_data_table(); 
        this.load_popup();
        this.close_popup();
      },
      close_popup: function() {
        $('.coastalynk-retrieve-popup-btn').on( 'click', function(){
          $(".coastalynk-vessel-popup-overlay").show();
          $(".coastalynk-vessel-popup-content").show();
        });
      },
      load_popup: function() {
        $('#coastalynk-vessel-popup-close, .coastalynk-vessel-popup-overlay').on( 'click', function(){
          $(".coastalynk-vessel-popup-overlay").hide();
          $(".coastalynk-vessel-popup-content").hide();
        });
      },
      load_data_table: function() {
          new DataTable('#coastalynk-vessel-table', {
              layout: {
                  bottomEnd: { 
                      paging: {
                          firstLast: false
                      }
                  }
              }
          });
      },
      
    };
    Shortcode.init();
  }); 

  $(".coastalynk-retrieve-popup-btn").click(function() {
    $(".popup-overlay").show();
    $(".popup-content").show();
  });

  $("#closePopup, .popup-overlay").click(function() {
    
  });
})(jQuery);