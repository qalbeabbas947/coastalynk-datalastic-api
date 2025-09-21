(function ($) {
  "use strict";
  $(document).ready(function () {
    var Shortcode = {
      init: function () {
        mytable: null,
        this.load_data_table(); 
        this.load_popup();
        this.close_popup();
        this.reload_popup_hooks();
        this.show_hide_vessel_filters();
      },

      show_hide_vessel_filters: function() {
         document.addEventListener('vessel_search_change', (e) => {
            if( e.detail.field_id == 'coastalynk-vessel-search-ddl' ) {
              switch( e.detail.value ) {
                case 'uuid':
                case 'mmsi':
                case 'imo':
                  document.querySelector(".coastalynk-vessel-country-field").style.display = 'none';
                  document.querySelector(".coastalynk-vessel-type-field").style.display = 'none';
                  break; 

                default:
                  // document.querySelector(".coastalynk-vessel-country-field").style.display = 'block';
                  // document.querySelector(".coastalynk-vessel-type-field").style.display = 'block';
                  break;
              }
              console.log('Event field_id:', e.detail.field_id);
              console.log('Event  field_name:', e.detail.field_name);
              console.log('Custom event value:', e.detail.value);
              console.log('Event label:', e.detail.label);
            }
          });
      },
      reload_popup_hooks: function() {
        //  document.querySelector('#coastalynk-vessel-popup-close').addEventListener( 'click', (event) => {
          
        //   document.querySelector(".coastalynk-vessel-popup-overlay").style.display = 'none';
        //   document.querySelector(".coastalynk-vessel-popup-content").style.display = 'none';
        // });
      },
      close_popup: function() {
         document.querySelector('#coastalynk-vessel-popup-close').addEventListener( 'click', (event) => {
          
          document.querySelector(".coastalynk-vessel-popup-overlay").style.display = 'none';
          document.querySelector(".coastalynk-vessel-popup-content").style.display = 'none';
        });

        document.querySelector('.coastalynk-vessel-popup-overlay').addEventListener( 'click', () => {
            document.querySelector(".coastalynk-vessel-popup-overlay").style.display = 'none';
            document.querySelector(".coastalynk-vessel-popup-content").style.display = 'none';
        });
      },
      load_popup: function() {

        $('.coastalynk-vessel-table-wrapper').on('click', '.coastalynk-retrieve-popup-btn', function(event) {
            event.preventDefault();
            let button = event.target;
            let loader = event.target.nextElementSibling;
            loader.style.display = "block";
            button.style.display = "none";

            const data = new URLSearchParams();
            data.append('nonce', COSTALYNK_VESSEL_VARS.nonce);
            data.append('action', 'coastalynk_load_popup_data');
            data.append('uuid', event.target.getAttribute("data-uuid"));
            
            fetch(COSTALYNK_VESSEL_VARS.ajaxURL, {
                method: 'POST',
                body: data // Or new URLSearchParams(data) for form data
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json(); // Or response.text() for plain text
            })
            .then( data => {

                document.querySelector(".coastalynk-vessel-popup-overlay").style.display = 'block';
                document.querySelector(".coastalynk-vessel-popup-content").style.display = 'block';
                document.querySelector(".coastalynk-vessel-popup-content h2").innerHTML = data.name;
                document.querySelector(".coastalynk-vessel-popup-top-bar-country-flag").src = data.flag;
                document.querySelector(".coastalynk-vessel-popup-top-bar-country-name").innerHTML = data.country_iso;
                document.querySelector(".coastalynk-vessel-popup-top-bar-type-content").innerHTML = data.type;
                document.querySelector(".coastalynk-vessel-popup-top-bar-homeport-content").innerHTML = data.home_port;
                document.querySelector(".coastalynk-vessel-popup-top-bar-yearbuilt-content").innerHTML = data.year_built;
                document.querySelector(".coastalynk-vessel-popup-content-type_specific").innerHTML = data.type_specific;
                document.querySelector(".coastalynk-vessel-popup-content-course").innerHTML = data.course;
                document.querySelector(".coastalynk-vessel-popup-content-draught").innerHTML = data.draught;
                document.querySelector(".coastalynk-vessel-popup-content-avg-speed").innerHTML = data.speed_avg;
                document.querySelector(".coastalynk-vessel-popup-content-max-speed").innerHTML = data.speed_max;
                document.querySelector(".coastalynk-vessel-popup-content-speed").innerHTML = data.speed;
                document.querySelector(".coastalynk-vessel-popup-content-heading").innerHTML = data.heading;

                document.querySelector(".coastalynk-vessel-popup-content-last-position").innerHTML = parseFloat(data.lat).toFixed(2)+'/'+parseFloat(data.lon).toFixed(2);
                document.querySelector(".coastalynk-vessel-popup-content-destination").innerHTML = data.destination;
                document.querySelector(".coastalynk-vessel-popup-content-eta").innerHTML = data.eta_UTC;

                document.querySelector(".coastalynk-vessel-popup-content-dept-port").innerHTML = data.dep_port;
                document.querySelector(".coastalynk-vessel-popup-content-dest-port").innerHTML = data.dest_port;
                document.querySelector(".coastalynk-vessel-popup-content-nav-status").innerHTML = data.navigation_status;


                document.querySelector(".coastalynk-vessel-popup-content-bredth").innerHTML = data.breadth;
                document.querySelector(".coastalynk-vessel-popup-content-gross-tonnage").innerHTML = data.gross_tonnage;
                document.querySelector(".coastalynk-vessel-popup-content-dead-weight").innerHTML = data.deadweight;
                document.querySelector(".coastalynk-vessel-popup-content-length").innerHTML = data.length;

                document.querySelector(".coastalynk-vessel-popup-content-imo").innerHTML = data.imo;
                document.querySelector(".coastalynk-vessel-popup-content-mmsi").innerHTML = data.mmsi;
                document.querySelector(".coastalynk-vessel-popup-content-callsign").innerHTML = data.callsign;
                console.log(data);
                
                button.style.display = "block";
                loader.style.display = "none";
                // Process the response data here
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
                button.style.display = "block";
                loader.style.display = "none";
            });
        });
      },
      load_data_table: function() {
          let mytable = new DataTable('#coastalynk-vessel-table', {
              layout: {
                  bottomEnd: { 
                      paging: {
                          firstLast: false
                      }
                  }
              }
          });
          
        //   mytable.on('page.dt', function() {
        //     // This function will be executed whenever the page changes
        //     console.log('Pagination button clicked or page changed!');
        //     // this.load_popup();
        //     // this.close_popup();
        //     // this.reload_popup_hooks();
        // });
      },
      
    };
    Shortcode.init();
  }); 

  
})(jQuery);