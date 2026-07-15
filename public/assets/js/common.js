function initMap()
	{
		var mapCanvas = document.getElementById('map');
		var input = document.getElementById('pac-input');
		var mapOptions = {
			zoom: 2,
			minZoom: 1,
			zoomControl: true,
			fullscreenControl: false,
			center:{lat: 0, lng: 0},
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		var drawingControlOptions = {
			position: google.maps.ControlPosition.TOP_CENTER,
			drawingModes: ['polygon']
		};
		var polygonOptions = {
			strokeWeight: 0,
			fillOpacity: 0.45,
			editable: true,
			draggable: true,
			fillColor: '#fe2c2c'
		};
		var polyLineOptions = {
			strokeWeight: 0,
			fillOpacity: 0.45,
			editable: true,
			fillColor: '#fe2c2c'
		};
		var markers = [];

		if(!mapCanvas) {
			return false;
		}

		var map = new google.maps.Map(mapCanvas,mapOptions);

	    // Create the DIV to hold the control to remove selected polygon
	    var removeControlDiv = document.createElement('div');
	    var removeControl = $scope.RemoveShapeControl(removeControlDiv, map);
	    removeControlDiv.index = 1;
	    map.controls[google.maps.ControlPosition.TOP_CENTER].push(removeControlDiv);

	    var drawingManager = new google.maps.drawing.DrawingManager({
	    	drawingMode: null,
	    	drawingControl: true,
	    	drawingControlOptions: drawingControlOptions,
	    	markerOptions: {
	    		draggable: true
	    	},
	    	polygonOptions: polygonOptions,
	    	polyLineOptions: polyLineOptions
	    });
	    drawingManager.setMap(map);

	    // Create the search box and link it to the UI element.
	    var searchBox = new google.maps.places.SearchBox(input);
	    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);

	    // Bias the SearchBox results towards current map's viewport.
	    map.addListener('bounds_changed', function() {
	    	searchBox.setBounds(map.getBounds());
	    });

	    // Listen for the event fired when the user selects a prediction and retrieve more details for that place.
	    searchBox.addListener('places_changed', function() {
	    	var places = searchBox.getPlaces();

	    	if (places.length == 0) {
	    		return;
	    	}

			// Clear out the old markers.
			markers.forEach(function(marker) {
				marker.setMap(null);
			});

			// For each place, get the icon, name and location.
			var bounds = new google.maps.LatLngBounds();
			places.forEach(function(place) {
				if (!place.geometry) {
					console.log("Returned place contains no geometry");
					return;
				}

				if (place.geometry.viewport) {
			      // Only geocodes have viewport.
			      bounds.union(place.geometry.viewport);
			  }
			  else {
			  	bounds.extend(place.geometry.location);
			  }
			});
	      	map.fitBounds(bounds);
	  	});

	    // Load already drawed polygons to map
	    google.maps.event.addListenerOnce(map, 'tilesloaded', function(event) {
	    	$('#pac-input').removeClass('hide');
	    	if($scope.formatted_coords.length > 0 ) {
	    		setTimeout(function(){
	    			$('.remove_location').removeClass('hide');
	    		},1000)
	    		$scope.addPolygons(map);
	    	}
	    });
	    
	    // Remove Polygon Selection while click outside
	    google.maps.event.addListener(map, 'click', function(event) {
	    	$scope.clearSelection();
	    });

	    google.maps.event.addListener(drawingManager, 'drawingmode_changed', function(event) {
	    	if($scope.coordinates.length > 0 && drawingManager.drawingMode != null) {
	    		drawingManager.setDrawingMode(null);
	    		return;
	    	}
	    });

	    google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {

	    	var coordinates = $scope.getCoordinates(event.overlay);
	    	$scope.coordinates.push(coordinates);
	    	$('.coordinates').val($scope.coordinates);

			// Add an event listener that selects the newly-drawn shape when the user click on it.
			var newShape = event.overlay;
			$scope.addEventListeners(newShape);
			$scope.setSelection(newShape);

			// Disable Drawing mode after Complete any overlay
			drawingManager.setDrawingMode(null);
		});
	}
	google.maps.event.addDomListener(window, "load", initMap);



	function initHomeLocationService()
	{
  		let autocomplete_results = document.querySelector('.home-autocomplete-results');
		let home_location = document.getElementById('input_home_location');

		var service = new google.maps.places.AutocompleteService();
		var placeService = new google.maps.places.PlacesService(home_location);
		let sessionToken = new google.maps.places.AutocompleteSessionToken();

  		var addInputListener = function(element) {
			element.addEventListener('click', function() {
				const selected_text = this.querySelector('.autocomplete-text').textContent;
				const place_id = this.getAttribute('data-place-id');
				let request = {
					placeId: place_id,
					fields: ['name', 'geometry','formatted_address','utc_offset_minutes']
				};
				placeService.getDetails(request, function(place, status) {
					if (status == google.maps.places.PlacesServiceStatus.OK) {
						if (!place.geometry) {
							return;
						}
						document.getElementById("input_home_location").value = place.formatted_address;
						document.getElementById("home_latitude").value = place.geometry.location.lat();
						document.getElementById("home_longitude").value = place.geometry.location.lng();
					}
					autocomplete_results.style.display = 'none';
				});
			});
		};

		var displaySuggestions = function(predictions, status) {
			autocomplete_results.innerHTML = '';
			autocomplete_results.style.display = 'none';
			if (status != google.maps.places.PlacesServiceStatus.OK) {
				return;
			}
			let results_html = [];
			predictions.forEach(function(prediction) {
				results_html.push(`<li class="autocomplete-item" data-type="place" data-place-id=${prediction.place_id}><span class="autocomplete-icon icon-localities"></span><span class="autocomplete-text">${prediction.description}</span></li>`);
			});

			setTimeout(() => {
				let autocomplete_items = autocomplete_results.querySelectorAll('.autocomplete-item');
				for (let autocomplete_item of autocomplete_items) {
					addInputListener(autocomplete_item);
				}
			},100);

			autocomplete_results.innerHTML = results_html.join("");
			autocomplete_results.style.display = 'block';
		};

		var showAutocompleteItems = function() {
			let value = this.value;
			if (value.length > 1) {
				value.replace('"', '\\"').replace(/^\s+|\s+$/g, '');
				if (value !== "") {
					service.getPlacePredictions({ input: value,sessionToken:sessionToken }, displaySuggestions);
				}
				else {
					autocomplete_results.innerHTML = '';
					autocomplete_results.style.display = 'none';
				}
			}
			else {
				autocomplete_results.innerHTML = '';
				autocomplete_results.style.display = 'none';
			}
		};

		home_location.addEventListener('input', debounce(showAutocompleteItems, 500));
	}


		function initWorkLocationService()
	{
  		let autocomplete_results = document.querySelector('.work-autocomplete-results');
		let work_location = document.getElementById('input_work_location');

		var service = new google.maps.places.AutocompleteService();
		var placeService = new google.maps.places.PlacesService(work_location);
		let sessionToken = new google.maps.places.AutocompleteSessionToken();

  		var addInputListener = function(element) {
			element.addEventListener('click', function() {
				const selected_text = this.querySelector('.autocomplete-text').textContent;
				const place_id = this.getAttribute('data-place-id');
				let request = {
					placeId: place_id,
					fields: ['name', 'geometry','formatted_address','utc_offset_minutes']
				};
				placeService.getDetails(request, function(place, status) {
					if (status == google.maps.places.PlacesServiceStatus.OK) {
						if (!place.geometry) {
							return;
						}
						document.getElementById("input_work_location").value = place.formatted_address;
						document.getElementById("work_latitude").value = place.geometry.location.lat();
						document.getElementById("work_longitude").value = place.geometry.location.lng();
					}
					autocomplete_results.style.display = 'none';
				});
			});
		};

		var displaySuggestions = function(predictions, status) {
			autocomplete_results.innerHTML = '';
			autocomplete_results.style.display = 'none';
			if (status != google.maps.places.PlacesServiceStatus.OK) {
				return;
			}
			let results_html = [];
			predictions.forEach(function(prediction) {
				results_html.push(`<li class="autocomplete-item" data-type="place" data-place-id=${prediction.place_id}><span class="autocomplete-icon icon-localities"></span><span class="autocomplete-text">${prediction.description}</span></li>`);
			});

			setTimeout(() => {
				let autocomplete_items = autocomplete_results.querySelectorAll('.autocomplete-item');
				for (let autocomplete_item of autocomplete_items) {
					addInputListener(autocomplete_item);
				}
			},100);

			autocomplete_results.innerHTML = results_html.join("");
			autocomplete_results.style.display = 'block';
		};

		var showAutocompleteItems = function() {
			let value = this.value;
			if (value.length > 1) {
				value.replace('"', '\\"').replace(/^\s+|\s+$/g, '');
				if (value !== "") {
					service.getPlacePredictions({ input: value,sessionToken:sessionToken }, displaySuggestions);
				}
				else {
					autocomplete_results.innerHTML = '';
					autocomplete_results.style.display = 'none';
				}
			}
			else {
				autocomplete_results.innerHTML = '';
				autocomplete_results.style.display = 'none';
			}
		};

		work_location.addEventListener('input', debounce(showAutocompleteItems, 500));
	}