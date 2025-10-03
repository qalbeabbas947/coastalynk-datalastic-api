(function ($) {
  "use strict";
  $(document).ready(function () {
    // var Shortcode = {
    //   init: function () {
    //     this.prebutton(); 
    //     this.nextButton();  
    //   },
    //   prebutton: function(){
    //     $('#coastalynk-port-prev-btn').on( 'click', function(){
    //       document.querySelector('.coastalynk-port-scroll-menu').scrollBy({ left: -200, behavior: 'smooth' }); // Adjust -200 for desired scroll distance
    //     });
    //   },
    //   nextButton: function(){
    //     $('#coastalynk-port-next-btn').on( 'click', function(){
    //       document.querySelector('.coastalynk-port-scroll-menu').scrollBy({ left: 200, behavior: 'smooth' }); // Adjust -200 for desired scroll distance
    //     });
    //   }
      
    // };
    // Shortcode.init();

    // Ticker variables
    let currentPosition = 0;
    let isPaused = false;
    let scrollSpeed = 30; // pixels per update
    let updateInterval = 100; // milliseconds between updates
    let tickerInterval;
    
    // Initialize the ticker
    initializeTicker();
    startTicker();
    
    // Control event handlers
    $('#coastalynk-darkship-pause-btn').click(function() {
        pauseTicker();
    });
    
    $('#coastalynk-darkship-resume-btn').click(function() {
        resumeTicker();
    });
    
    // Pause on hover
    $('.coastlynk-darkship-ticker-wrapper').hover(
        function() {
            pauseTicker();
        },
        function() {
            if (!isPaused) {
                resumeTicker();
            }
        }
    );
    
    // Functions
    function initializeTicker() {
        const ticker = $('#coastallynk-darkship-ticker');
        // Clone items for seamless looping
        ticker.html(ticker.html() + ticker.html());
    }
    
    function startTicker() {
        tickerInterval = setInterval(function() {
            if (!isPaused) {
                // Move the ticker to the left
                currentPosition -= scrollSpeed;
                
                // Get the total width of the original content (without the duplicate)
                const tickerWidth = $('#coastallynk-darkship-ticker').width() / 2;
                
                // If we've scrolled past the original content, reset to start
                if (Math.abs(currentPosition) >= tickerWidth) {
                    currentPosition = 0;
                }
                
                // Apply the transformation
                $('#coastallynk-darkship-ticker').css('transform', `translateX(${currentPosition}px)`);
                
                // Update which item is "current" for display
            }
        }, updateInterval);
    }
    
    function pauseTicker() {
        isPaused = true;
        $('#coastalynk-darkship-pause-btn').prop('disabled', true);
        $('#coastalynk-darkship-resume-btn').prop('disabled', false);
    }
    
    function resumeTicker() {
        isPaused = false;
        $('#coastalynk-darkship-pause-btn').prop('disabled', false);
        $('#coastalynk-darkship-resume-btn').prop('disabled', true);
    }
  });
})(jQuery);