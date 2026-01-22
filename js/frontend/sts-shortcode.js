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
        this.load_daughtership_data();
        this.close_daughtership_data();

        this.load_popup();
        this.close_popup();
        this.goto_map();
        this.submit_export();
        this.submit_export_popup_event();
        this.display_tonnage();
        
        },
        display_tonnage: function() { 
            $('.dashboard-sts').on('click', '.coastlynk-display-sts-tonnage', function(event) {
                event.preventDefault();
                
                let vessel1_uuid = $(this).data('id');
                let vessel1_name = $(this).data('name');
                let link = $(this);
                let link_parent = link.parent();
                

                link_parent.html('<div id="coastalynk-field-column-loader"><div id="coastalynk-field-column-blockG_1" class="coastalynk-field-column-blockG"></div><div id="coastalynk-field-column-blockG_2" class="coastalynk-field-column-blockG"></div><div id="coastalynk-field-column-blockG_3" class="coastalynk-field-column-blockG"></div></div>');
                $.ajax({
                    type: 'POST',
                    aSync: false,
                    dataType: "html",
                    url: COSTALYNKVARS.ajaxURL, // URL from our localized variable
                    data: {
                        action: 'coastalynk_retrieve_tonnage', // The WordPress hook to trigger
                        nonce: COSTALYNKVARS.nonce,     // The nonce value
                        selected_uuid: vessel1_uuid,
                        selected_name: vessel1_name
                    },
                    success: function(response) {
                        link_parent.html(response);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                    }
                });
            });
        },
        submit_export_popup_event: function(){
            $('#coastalynk-port-sts-popup-history-form').on('click', '.coastalynk-sts-history-buttons-popup-export-pdf', function(event) {
                event.preventDefault();
                let btn = $(this).data('id');
                $("#coastalynk-port-sts-popup-history-form").submit();
            });
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
      close_daughtership_data: function() {

        $('.coastalynk-sts-popup-content-box-close-daughtship-detail').on('click', function(event) {
            event.preventDefault();
            $('.coastalynk-sts-popup-content-box-daughtship-detail').hide();
            $('.coastalynk-sts-popup-content-box-close-daughtship-detail').hide();
            $('.coastalynk-sts-popup-content-box-wide').show();
        });
      },  
      load_daughtership_data : function() {
        $('.coastalynk-sts-popup-content-box-daughterships').on('click', '.coastalynk-sts-retrieve-popup-daughtership-btn', function(event) {
            event.preventDefault();
            console.log($(this).data());
            var data = $(this).data();
            
            $(".coastalynk-sts-popup-content-vessel2_name").html(data.name);
            $(".coastalynk-sts-popup-content-vessel2_mmsi").html(data.mmsi);
            $(".coastalynk-sts-popup-content-vessel2_imo").html(data.imo);
            $(".coastalynk-sts-popup-content-coordinate").html('['+ data.lat +', '+data.lon+']');
            $(".coastalynk-sts-popup-content-vessel2_tonnage").html(data.gross_tonnage);
            $(".coastalynk-sts-popup-content-vessel2_type").html(data.type);
            $(".coastalynk-sts-popup-content-vessel2_type_specific").html(data.type_specific);
            $(".coastalynk-sts-popup-content-vessel2_draught").html(data.draught);
            $(".coastalynk-sts-popup-content-vessel2_completed_draught").html(data.completed_draught);
            $(".coastalynk-sts-popup-content-vessel2_country_iso").html(data.country_iso);
            $(".coastalynk-sts-popup-content-vessel2_speed").html(data.speed);
            $(".coastalynk-sts-popup-content-vessel2_deadweight").html(data.deadweight);
            $(".coastalynk-sts-popup-content-vessel2_ais_signal").html(data.ais_signal);
            $(".coastalynk-sts-popup-content-vessel2_operationmode").html(data.operationmode);
            $(".coastalynk-sts-popup-content-vessel2_proximity_consistency").html(data.proximity_consistency);
            $(".coastalynk-popup-sts-remarks").html(data.remarks);
            $(".coastalynk-sts-popup-content-vessel2_risk_level").html(data.risk_level);
            $(".coastalynk-sts-popup-content-vessel2_stationary_duration_hours").html(data.stationary_duration_hours);
            $(".coastalynk-sts-popup-content-vessel2_distance").html(data.distance.toFixed(2));
            $(".coastalynk-sts-popup-content-vessel2_end_date").html(data.end_date);
            $(".coastalynk-sts-popup-content-vessel2_joining_date").html(data.joining_date);
            $(".coastalynk-sts-popup-content-vessel2_lock_time").html(data.lock_time);
            $(".coastalynk-sts-popup-content-vessel2_navigation_status").html(data.navigation_status);
            $(".coastalynk-sts-popup-content-vessel2_cargo_category_type").html(data.cargo_category_type);
            $(".coastalynk-sts-popup-content-vessel2_data_points_analyzed").html(data.data_points_analyzed);
            $(".coastalynk-sts-popup-content-vessel2_draught_change").html(data.draught_change);
            $(".coastalynk-sts-popup-content-vessel2_event_percentage").html(data.event_percentage);

            if( data.status == 'ended' ) {
                $(".coastalynk-sts-popup-content-vessel2_status").html(data.end_date);
                $(".coastalynk-sts-popup-content-vessel2_status-parent").show();
            } else {
                $(".coastalynk-sts-popup-content-vessel2_status").html('');
                $(".coastalynk-sts-popup-content-vessel2_status-parent").hide();
            }
            
            $(".coastalynk-sts-popup-content-vessel2_last_position_utc").html(data.last_position_utc);
            $(".coastalynk-sts-popup-content-vessel2_last_updated").html(data.last_updated);
            $(".coastalynk-sts-popup-content-vessel2_outcome_status").html(data.outcome_status);console.log(data.outcome_status);
            $(".coastalynk-sts-popup-content-vessel2_flag_status").html(data.flag_status);
            $('.coastalynk-sts-popup-content-box-daughtship-detail').show();
            $('.coastalynk-sts-popup-content-box-close-daughtship-detail').show();
            $('.coastalynk-sts-popup-content-box-wide').hide();
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
            var data = $(this).data();
            console.log(data);
            button.style.display = "none";
            $(".coastalynk-popup-sts-remarks").html('');
            $(".coastalynk-sts-popup-overlay").show(); 
            $(".coastalynk-sts-popup-content").show();;
            $(".coastalynk-sts-popup-content-box-daughterships-loader").show();
            $(".coastalynk-sts-popup-content-box-daughterships").html('');
            $.ajax({
                type: 'POST',
                aSync: false,
                dataType: "html",
                url: COSTALYNKVARS.ajaxURL, // URL from our localized variable
                data: {
                    action: 'coastalynk_retrieve_daughterships', // The WordPress hook to trigger
                    nonce: COSTALYNKVARS.nonce,     // The nonce value
                    event_id: data.id,
                    uuid: data.uuid,
                    name: data.name,
                    port: data.zone_terminal_name
                },
                success: function(response) {
                    $(".coastalynk-sts-popup-content-box-daughterships").html(response);
                    $(".coastalynk-sts-popup-content-box-daughterships-loader").hide(); 
                    $(".coastalynk-sts-popup-content-vessel1_draught_change").html($("#coastalynk-sts-popup-calculated-draught-change").val());
                },
                error: function(jqXHR, textStatus, errorThrown) {
                }
            });

            
            $("#coastalynk_sts_popup_history_load_vessel_id_ctrl").val(data.id);
            $(".coastalynk-sts-popup-content-vessel1_tonnage").html(data.gross_tonnage);;
            $(".coastalynk-sts-popup-content-vessel1_name").html(data.name);
            $(".coastalynk-sts-popup-content-vessel1_mmsi").html(data.mmsi);
            $(".coastalynk-sts-popup-content-vessel1_imo").html(data.imo);
            $(".coastalynk-sts-popup-content-vessel1_country_iso").html(data.country_iso);
            $(".coastalynk-sts-popup-content-vessel1_type").html(data.type);
            $(".coastalynk-sts-popup-content-vessel1_type_specific").html(data.type_specific);
            $(".coastalynk-sts-popup-content-vessel1_speed").html(data.speed);
            $(".coastalynk-sts-popup-content-vessel1_navigation_status").html(data.navigation_status);
            $(".coastalynk-sts-popup-content-vessel1_draught").html(data.draught);
            $(".coastalynk-sts-popup-content-vessel1_completed_draught").html(data.completed_draught);
            $(".coastalynk-sts-popup-content-vessel1_last_position_UTC").html(data.last_position_utc);
            $(".coastalynk-sts-popup-content-draught_change").html(data.draught_change);
            $(".coastalynk-sts-popup-content-vessel1_last_position_UTC").html(data.last_position_utc);
            $(".coastalynk-sts-popup-content-coordinates").html("["+data.lat+","+data.lon+"]");
            
            if( data.zone_terminal_name != '' ) {
                $(".coastalynk-popup-approved-zone").css('display', 'inline-block');
                $(".coastalynk-popup-unapproved-zone").css('display', 'none');
            } else {
                $(".coastalynk-popup-unapproved-zone").css('display', 'inline-block');
                $(".coastalynk-popup-approved-zone").css('display', 'none');
            }

            $(".coastalynk-sts-popup-content-port").html(data.zone_terminal_name);
            
            if( data.ais_signal == 'AIS  Consistent Signal Detected' ) {
                $(".coastalynk-sts-popup-content-vessel1-ais-signal").html(data.ais_signal + ' <i class="fa fa-check-square" aria-hidden="true"></i>');
            } else if( data.ais_signal == 'AIS Signal Gap Detected' ) {
                $(".coastalynk-sts-popup-content-vessel1-ais-signal").html(data.ais_signal + ' <i class="fa fa-exclamation" aria-hidden="true"></i>');
            }

            $(".coastalynk-sts-popup-content-vessel1-ais-signal").html(data.ais_signal);

            $(".coastalynk-sts-popup-content-event_ref_id").html(data.event_ref_id);
            $(".coastalynk-sts-popup-content-zone_type").html(data.zone_type);
            $(".coastalynk-sts-popup-content-zone_ship").html(data.zone_ship);
            //$(".coastalynk-sts-popup-content-zone_terminal_name").html(data.zone_terminal_name);
            $(".coastalynk-sts-popup-content-start_date").html(data.start_date);
            $(".coastalynk-sts-popup-content-end_date").html(data.end_date);
            $(".coastalynk-sts-popup-content-deadweight").html(data.deadweight);
            $(".coastalynk-sts-popup-content-event_percentage").html(data.event_percentage);
            $(".coastalynk-sts-popup-content-cargo_category_type").html(data.cargo_category_type);
            $(".coastalynk-sts-popup-content-risk_level").html(data.risk_level);
            // $(".coastalynk-sts-popup-content-distance").html(data.distance);
            $(".coastalynk-sts-popup-content-stationary_duration_hours").html(data.stationary_duration_hours);
            $(".coastalynk-sts-popup-content-proximity_consistency").html(data.proximity_consistency);
            $(".coastalynk-sts-popup-content-data_points_analyzed").html(data.data_points_analyzed);
            $(".coastalynk-sts-popup-content-operationmode").html(data.operationmode);
            $(".coastalynk-sts-popup-content-status").html(data.status);
            $(".coastalynk-sts-popup-content-top-heading-status").html(data.status);
            $(".coastalynk-sts-popup-content-last_updated").html(data.last_updated);            
            $('.coastalynk-sts-popup-content-box-daughtship-detail').hide();
            $('.coastalynk-sts-popup-content-box-close-daughtship-detail').hide();
            $('.coastalynk-sts-popup-content-box-wide').show();
            button.style.display = "block";
            //loader.style.display = "none";
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