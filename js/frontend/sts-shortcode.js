(function ($, map) {
  "use strict";
  $(document).ready(function () {
    var Shortcode = {
      init: function () {
        this.prebutton(); 
        this.nextButton(); 
        this.load_select_js();
        this.load_date_range_js(); 
        this.load_date_table(); 
        this.load_popup();
        this.close_popup();
        this.goto_map();
        this.submit_export();
        },
        submit_export: function() {
            $('#coastalynk-port-sts-history-form').on('click', '.coastalynk-sts-history-buttons-export-csv, .coastalynk-sts-history-buttons-export-pdf', function(event) {
                event.preventDefault();
                let btn = $(this).data('id');
                switch( btn ) {
                    case "export-csv":
                        $("#coastalynk_sts_history_load_action_ctrl").val('coastalynk_sts_history_load_action_ctrl_csv');
                        break;
                    case "export-pdf":
                        $("#coastalynk_sts_history_load_action_ctrl").val('coastalynk_sts_history_load_action_ctrl_pdf');
                        break;
                }
                
                $("#coastalynk-port-sts-history-form").submit();
            });
        },
      
      goto_map: function() {
        $('.coastalynk-sts-table_wrapper').on('click', '.coastalynk-sts-focus-marker-btn', function(event) {
                event.preventDefault();
                var lat = $(this).data('lat');
                var lon = $(this).data('lon');
                const portCoords = [lat, lon];
                var mapContainer = $('#map');

                // Scroll to the map container
                $('html, body').animate({
                    scrollTop: mapContainer.offset().top 
                }, 800, function() { // Callback after scroll animation completes
                    // Focus the map container after scrolling
                    mapContainer.focus();
                });
                if (portCoords) {
                    map.setView(portCoords, 14);
                }
            });

      },
      close_popup: function() {
         document.querySelector('#coastalynk-sts-popup-close').addEventListener( 'click', (event) => {
          
          document.querySelector(".coastalynk-sts-popup-overlay").style.display = 'none';
          document.querySelector(".coastalynk-sts-popup-content").style.display = 'none';
        });

        document.querySelector('.coastalynk-sts-popup-overlay').addEventListener( 'click', () => {
            document.querySelector(".coastalynk-sts-popup-overlay").style.display = 'none';
            document.querySelector(".coastalynk-sts-popup-content").style.display = 'none';
        });
      },
      load_popup: function() {

        $('.coastalynk-sts-table_wrapper').on('click', '.coastalynk-sts-retrieve-popup-btn', function(event) {
            event.preventDefault();
            
            let button = event.target;
            let loader = event.target.nextElementSibling;
            var data = $(this).data();
            console.log(data);console.log(data.id);
            loader.style.display = "block";
            button.style.display = "none";
            
            $(".coastalynk-sts-popup-overlay").show();
            $(".coastalynk-sts-popup-content").show();;

            // $(".coastalynk-vessel-popup-content h2").html(data());
            // $(".coastalynk-vessel-popup-top-bar-country-flag").src = data.flag;
            // $(".coastalynk-vessel-popup-top-bar-country-name").innerHTML = data.country_iso;
            // $(".coastalynk-vessel-popup-top-bar-type-content").innerHTML = data.type;
            console.log(data.vessel1_name)
            console.log(data.vessel1_mmsi)
            $(".coastalynk-sts-popup-content-vessel1_name").html(data.vessel1_name)
            $(".coastalynk-sts-popup-content-vessel1_mmsi").html(data.vessel1_mmsi)
            $(".coastalynk-sts-popup-content-vessel1_imo").html(data.vessel1_imo)
            $(".coastalynk-sts-popup-content-vessel1_country_iso").html(data.vessel1_country_iso)
            $(".coastalynk-sts-popup-content-vessel1_type").html(data.vessel1_type)
            $(".coastalynk-sts-popup-content-vessel1_type_specific").html(data.vessel1_type_specific)
            $(".coastalynk-sts-popup-content-vessel1_speed").html(data.vessel1_speed)
            $(".coastalynk-sts-popup-content-vessel1_navigation_status").html(data.vessel1_navigation_status)
            $(".coastalynk-sts-popup-content-vessel1_draught").html(data.vessel1_draught)
            $(".coastalynk-sts-popup-content-vessel1_completed_draught").html(data.vessel1_completed_draught)
            $(".coastalynk-sts-popup-content-vessel1_last_position_UTC").html(data.vessel1_last_position_UTC)
            $(".coastalynk-sts-popup-content-vessel_condition1").html(data.vessel_condition1)
            $(".coastalynk-sts-popup-content-vessel_owner1").html(data.vessel_owner1)
            $(".coastalynk-sts-popup-content-cargo_eta1").html(data.cargo_eta1)
            $(".coastalynk-sts-popup-content-vessel1_eta").html(data.vessel1_eta)
            $(".coastalynk-sts-popup-content-vessel1_atd").html(data.vessel1_atd)

            $(".coastalynk-sts-popup-content-vessel2_name").html(data.vessel2_name)
            $(".coastalynk-sts-popup-content-vessel2_mmsi").html(data.vessel2_mmsi)
            $(".coastalynk-sts-popup-content-vessel2_imo").html(data.vessel2_imo)
            $(".coastalynk-sts-popup-content-vessel2_country_iso").html(data.vessel2_country_iso)
            $(".coastalynk-sts-popup-content-vessel2_type").html(data.vessel2_type)
            $(".coastalynk-sts-popup-content-vessel2_type_specific").html(data.vessel2_type_specific)
            $(".coastalynk-sts-popup-content-vessel2_speed").html(data.vessel2_speed)
            $(".coastalynk-sts-popup-content-vessel2_navigation_status").html(data.vessel2_navigation_status)
            $(".coastalynk-sts-popup-content-vessel2_draught").html(data.vessel2_draught)
            $(".coastalynk-sts-popup-content-vessel2_completed_draught").html(data.vessel2_completed_draught)
            $(".coastalynk-sts-popup-content-vessel2_last_position_UTC").html(data.vessel2_last_position_UTC)
            $(".coastalynk-sts-popup-content-vessel2_eta").html(data.vessel2_eta)
            $(".coastalynk-sts-popup-content-vessel2_atd").html(data.vessel2_atd)
            $(".coastalynk-sts-popup-content-vessel_condition2").html(data.vessel_condition2)
            $(".coastalynk-sts-popup-content-vessel_owner2").html(data.vessel_owner2)
            $(".coastalynk-sts-popup-content-cargo_eta2").html(data.cargo_eta2)

            $(".coastalynk-sts-popup-content-port").html(data.port);
            $(".coastalynk-sts-popup-content-distance").html(data.distance);
            $(".coastalynk-sts-popup-content-event_ref_id").html(data.event_ref_id);
            $(".coastalynk-sts-popup-content-zone_terminal_name").html(data.zone_terminal_name);
            $(".coastalynk-sts-popup-content-start_date").html(data.start_date);
            $(".coastalynk-sts-popup-content-end_date").html(data.end_date);
            $(".coastalynk-sts-popup-content-remarks").html(data.remarks);
            $(".coastalynk-sts-popup-content-event_percentage").html(data.event_percentage);
            $(".coastalynk-sts-popup-content-cargo_category_type").html(data.cargo_category_type);
            $(".coastalynk-sts-popup-content-risk_level").html(data.risk_level);
            $(".coastalynk-sts-popup-content-current_distance_nm").html(data.current_distance_nm);
            $(".coastalynk-sts-popup-content-stationary_duration_hours").html(data.stationary_duration_hours);
            $(".coastalynk-sts-popup-content-proximity_consistency").html(data.proximity_consistency);
            $(".coastalynk-sts-popup-content-data_points_analyzed").html(data.data_points_analyzed);
            $(".coastalynk-sts-popup-content-operationmode").html(data.operationmode);
            $(".coastalynk-sts-popup-content-status").html(data.status);
            $(".coastalynk-sts-popup-content-last_updated").html(data.last_updated);
            

            // $(".coastalynk-vessel-popup-content-last-position").innerHTML = parseFloat(data.lat).toFixed(2)+'/'+parseFloat(data.lon).toFixed(2);
            // $(".coastalynk-vessel-popup-content-destination").innerHTML = data.destination;
            // $(".coastalynk-vessel-popup-content-eta").innerHTML = data.eta_UTC;

            // $(".coastalynk-vessel-popup-content-dept-port").innerHTML = data.dep_port;
            // $(".coastalynk-vessel-popup-content-dest-port").innerHTML = data.dest_port;
            // $(".coastalynk-vessel-popup-content-nav-status").innerHTML = data.navigation_status;


            // $(".coastalynk-vessel-popup-content-bredth").innerHTML = data.breadth;
            // $(".coastalynk-vessel-popup-content-gross-tonnage").innerHTML = data.gross_tonnage;
            // $(".coastalynk-vessel-popup-content-dead-weight").innerHTML = data.deadweight;
            // $(".coastalynk-vessel-popup-content-length").innerHTML = data.length;

            // $(".coastalynk-vessel-popup-content-imo").innerHTML = data.imo;
            // $(".coastalynk-vessel-popup-content-mmsi").innerHTML = data.mmsi;
            // $(".coastalynk-vessel-popup-content-callsign").innerHTML = data.callsign;
            console.log(data);
            
            button.style.display = "block";
            loader.style.display = "none";
        });
      },
      ajax_retrieve_draught: function() {
          // Example: Trigger the AJAX call on a button click
          $('.coastalynk-retrieve-draught-btn').on('click', function(e) {
              let button = $(this);
              
              button.siblings('div').css('display', 'block');
              button.css('display', 'none');
              let uuid = $(this).data('uuid');
              let name = $(this).data('name');
              $.ajax({
                  type: 'POST',
                  aSync: false,
                  dataType: "html",
                  url: COSTALYNKVARS.ajaxURL, // URL from our localized variable
                  data: {
                      action: 'coastalynk_retrieve_draught', // The WordPress hook to trigger
                      nonce: COSTALYNKVARS.nonce,     // The nonce value
                      selected_uuid: uuid,
                      selected_name: name
                  },
                  success: function(response) {
                      button.parent().html(response);
                      
                  },
                  error: function(jqXHR, textStatus, errorThrown) {
                      button.parent.find('img').css('display', 'none');
                      button.parent.css('display', 'block');

                  }
              });
          });
      },
      load_date_table: function(){
        new DataTable('#coastalynk-sts-table', {
                    layout: {
                        bottomEnd: { 
                            paging: {
                                firstLast: false
                            }
                        }
                    },
                    drawCallback: function(settings) {
                        $('.coastalynk-retrieve-draught-btn').on('click', function(e) {
                            let button = $(this);
                            
                            button.siblings('div').css('display', 'block');
                            button.css('display', 'none');
                            let uuid = $(this).data('uuid');
                            let name = $(this).data('name');
                            $.ajax({
                                type: 'POST',
                                aSync: false,
                                dataType: "html",
                                url: COSTALYNKVARS.ajaxURL, // URL from our localized variable
                                data: {
                                    action: 'coastalynk_retrieve_draught', // The WordPress hook to trigger
                                    nonce: COSTALYNKVARS.nonce,     // The nonce value
                                    selected_uuid: uuid,
                                    selected_name: name
                                },
                                success: function(response) {
                                    button.parent().html(response);
                                    
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    button.parent.find('img').css('display', 'none');
                                    button.parent.css('display', 'block');

                                }
                            });
                        });
                    }
                });
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
})(jQuery, map);