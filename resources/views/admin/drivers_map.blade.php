@extends('admin.includes.main')
@section('content')

<main id="main" class="main">
<section class="section">
<div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                  <a href="<?php echo secure_url('admin/dashboard'); ?>" class="pull-right btn btn-secondary btn-sm">Back</a>
                <h3><small>Driver Locations Map</small></h3>
              </div>
              <div class="title_right">
                <span class="badge badge-success" id="online-drivers">Loading...</span>
                <span class="badge badge-info" id="last-update">Last updated: Never</span>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row" style="display: block;">
              <div class="clearfix"></div>
              <div class="col-md-12 col-sm-12  ">
                <div class="x_panel">
                  <div class="x_content">
                    
                    <div class="row">
                        <div class="form-group">
                           <div id="map" style="height: 850px;width: 100%;"></div>
                        </div>
                    </div>
            
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
  </section>
  </main>      
      <!--<script src="{{asset('public/assets/vendors/jquery/dist/jquery.min.js')}}"></script>  -->
      <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js" defer></script>
      <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
      
      <!------Map Code Start------>
      <script src="https://maps.googleapis.com/maps/api/js?key={{ get_option_data('google_map_api_key') }}&libraries=drawing,places,geometry"></script>
      <script src="{{asset('public/assets/js/common.js')}}"></script>
      <!------Map Code End------>
      
     <script src="{{asset('assets/js/jquery1-3.4.1.min.js')}}"></script>
     
     <script>
        let map;
        let markers = {};
        let infoWindows = {};
        let updateInterval;
        let currentOpenInfoWindow = null; // Track currently open info window

        function initMap() {
            // Initialize the map with a default center
            const defaultCenter = { lat: 20.5937, lng: 78.9629 }; // India center as default

            map = new google.maps.Map(document.getElementById('map'), {
                center: defaultCenter,
                zoom: 5,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            // Load initial driver locations
            loadDriverLocations();

            // Set interval to update locations every 10 seconds
            updateInterval = setInterval(loadDriverLocations, 10000);
        }

        function loadDriverLocations() {
            $.ajax({
                url: '{{ route("driverLocations") }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        updateMapMarkers(response.data);
                        updateLastUpdateTime();
                        $('#online-drivers').text(response.data.length + ' Online Drivers');
                    } else {
                        $('#online-drivers').text('0 Online Drivers');
                        clearAllMarkers();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading driver locations:', error);
                    $('#last-update').text('Error loading data').addClass('badge-danger').removeClass('badge-info');
                }
            });
        }

        function updateMapMarkers(drivers) {
            const bounds = new google.maps.LatLngBounds();
            const currentDriverIds = [];

            // Update or create markers for each driver
            drivers.forEach(function(driver, index) {
                if (driver.latitude && driver.longitude) {
                    const driverId = driver.id || driver.name; // Use ID if available, else name
                    currentDriverIds.push(driverId);

                    const userPosition = {
                        lat: parseFloat(driver.latitude),
                        lng: parseFloat(driver.longitude)
                    };

                    // Check if marker already exists for this driver
                    if (markers[driverId]) {
                        // Update existing marker position
                        markers[driverId].setPosition(userPosition);
                        
                        // Update info window content
                        infoWindows[driverId].setContent(createInfoWindowContent(driver));
                    } else {
                        // Create new marker
                        const markerIcon = {
                            url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
                            scaledSize: new google.maps.Size(40, 40)
                        };

                        const marker = new google.maps.Marker({
                            map: map,
                            position: userPosition,
                            title: driver.name,
                            animation: google.maps.Animation.DROP,
                            icon: markerIcon,
                            label: {
                                text: (index + 1).toString(),
                                color: 'white',
                                fontSize: '12px',
                                fontWeight: 'bold'
                            }
                        });

                        // Create info window
                        const infoWindow = new google.maps.InfoWindow({
                            content: createInfoWindowContent(driver)
                        });

                        // Add click listener
                        marker.addListener('click', function() {
                            // Close currently open info window if any
                            if (currentOpenInfoWindow) {
                                currentOpenInfoWindow.close();
                            }
                            infoWindow.open(map, marker);
                            currentOpenInfoWindow = infoWindow;
                        });

                        // Add hover listener
                        marker.addListener('mouseover', function() {
                            // Close currently open info window if any
                            if (currentOpenInfoWindow) {
                                currentOpenInfoWindow.close();
                            }
                            infoWindow.open(map, marker);
                            currentOpenInfoWindow = infoWindow;
                        });

                        // Store marker and info window
                        markers[driverId] = marker;
                        infoWindows[driverId] = infoWindow;
                    }

                    // Extend bounds
                    bounds.extend(userPosition);
                }
            });

            // Remove markers for drivers that are no longer online
            Object.keys(markers).forEach(function(driverId) {
                if (!currentDriverIds.includes(driverId)) {
                    markers[driverId].setMap(null);
                    delete markers[driverId];
                    delete infoWindows[driverId];
                }
            });

            // Fit map to show all markers (only on first load)
            if (Object.keys(markers).length > 0 && !map.getBounds()) {
                map.fitBounds(bounds);
                
                google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
                    if (Object.keys(markers).length === 1) {
                        map.setZoom(12);
                    } else if (map.getZoom() > 15) {
                        map.setZoom(15);
                    }
                });
            }
        }

        function createInfoWindowContent(driver) {
            const lastUpdated = driver.location_change_time || 'N/A';
            return `<div style="padding: 10px; min-width: 200px;">
                        <h4 style="margin: 0 0 10px 0; color: #333;">
                            <strong>${driver.name}</strong>
                        </h4>
                        <p style="margin: 5px 0; font-size: 13px;">
                            <strong>Latitude:</strong> ${driver.latitude}<br>
                            <strong>Longitude:</strong> ${driver.longitude}<br>
                            <strong>Last Updated:</strong> ${lastUpdated}
                        </p>
                    </div>`;
        }

        function clearAllMarkers() {
            Object.values(markers).forEach(function(marker) {
                marker.setMap(null);
            });
            markers = {};
            infoWindows = {};
        }

        function updateLastUpdateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            $('#last-update').text('Last updated: ' + timeString).removeClass('badge-danger').addClass('badge-info');
        }

        // Initialize the map when the window has finished loading
        google.maps.event.addDomListener(window, 'load', initMap);

        // Clear interval when leaving the page
        $(window).on('beforeunload', function() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
        });
    </script>
     
@endsection