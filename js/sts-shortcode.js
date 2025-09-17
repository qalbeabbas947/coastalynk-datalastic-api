(function ($) {
  "use strict";
  $(document).ready(function () {
    var Shortcode = {
      init: function () {
        this.prebutton(); 
        this.nextButton();  
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
      }
      
    };
    Shortcode.init();
  });
})(jQuery);