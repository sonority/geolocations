/**
 * Geolocations functions
 *
 * TODO:
 *	- Set initial position per click if autoposition is deactivated
 *
 * @params {jQuery} $
 * @class Geolocations
 */

var TYPO3 = TYPO3 || {};
var google = google || {};
var Gl = Gl || {};

Gl = {
	/***************************************************************************
	 * COMMON Variables
	 **************************************************************************/
	/**
	 * Ajax request url
	 *
	 * @type String
	 */
	ajaxRequestUrl: 'index.php?eID=geolocations',
	/**
	 * The element which receives messages
	 *
	 * @type String|$|_$|$.length|_$.length
	 */
	elMessageField: '#geolocations-message',
	/**
	 * First run indicator
	 *
	 * @type Boolean
	 */
	firstRun: false,
	/**
	 *
	 * @type Boolean
	 */
	addressFieldChangedManually: false,
	/**
	 * This object hold the currently selected position
	 *
	 * @type Object
	 */
	latLng: null,
	/***************************************************************************
	 * SEARCH Variables
	 **************************************************************************/
	/**
	 * The search container element
	 *
	 * @type String|$.length|_$.length|$|_$
	 */
	elSearchContainer: '#geolocations-search',
	/**
	 * The address field
	 *
	 * @type String|$|_$|$.length|_$.length
	 */
	elFieldAddress: '#search-address',
	/**
	 * The radius select field
	 *
	 * @type String|$|_$|$.length|_$.length
	 */
	elFieldRadius: '#search-radius',
	/**
	 * The input field for the search keyword
	 *
	 * @type String|$|_$|$.length|_$.length
	 */
	elFieldKeyword: '#search-keyword',
	/**
	 * The fieldset containing the radius search elements
	 *
	 * @type String|$.length|_$.length|$|_$
	 */
	elFieldsetRadial: '#search-radial',
	/**
	 * The fieldset containing the fulltext search elements
	 *
	 * @type String|$|_$|$.length|_$.length
	 */
	elFieldsetFulltext: '#search-fulltext',
	/**
	 * The button for the radial search
	 *
	 * @type String|$|_$|$.length|_$.length
	 */
	elButtonRadial: '#search-radial-button',
	/**
	 * The button for the fulltext search
	 *
	 * @type String|$|_$|$.length|_$.length
	 */
	elButtonFulltext: '#search-fulltext-button',
	/**
	 * The search type toggle (switches between radial and fulltext)
	 *
	 * @type String|$.length|_$.length|$|_$
	 */
	elButtonSearchType: '#search-type',
	/**
	 * The name of the button for clearing the input field
	 *
	 * @type String
	 */
	elClearFieldName: '.search-clear',
	/**
	 * The object wich contains the search options
	 *
	 * @type Object
	 */
	sO: null,
	/***************************************************************************
	 * MAP Variables
	 **************************************************************************/
	/**
	 * The element wich contains the map
	 *
	 * @type String|$.length|_$.length|$|_$
	 */
	elMapContainer: '#geolocations-map',
	/**
	 * The element for the map
	 *
	 * @type String|$.length|_$.length|$|_$
	 */
	elMapCanvas: '#map-canvas',
	/**
	 * The element for the toggle button
	 *
	 * @type String|$.length|_$.length|$|_$
	 */
	elMapToggleLock: '#toggle-lock',
	/*
	 * Loading overlay
	 *
	 * @type String|$.length|_$.length|$|_$
	 */
	elMapLoadingOverlay: '#loading-overlay',
	/**
	 * Object for the map options
	 *
	 * @type Object
	 */
	mO: null,
	/**
	 * Object for the map
	 *
	 * @type google.maps.Map
	 */
	map: null,
	/**
	 * Marker array
	 *
	 * @type Array
	 */
	markers: null,
	/**
	 * Google maps marker object
	 *
	 * @type google.maps.Marker
	 */
	positionMarker: null,
	/**
	 * Google maps circle object
	 *
	 * @type google.maps.Circle
	 */
	perimeterCircle: null,
	/***************************************************************************
	 * LIST Variables
	 **************************************************************************/
	/**
	 * The element wich contains the location list
	 *
	 * @type String|$.length|_$.length|$|_$
	 */
	elListContainer: '#geolocations-list',
	/**
	 * The container name for location elements
	 *
	 * @type String
	 */
	locationElementsName: '.geolocations-list-item',
	/**
	 * The name of the location info elements
	 *
	 * @type String
	 */
	locationInfoName: '.geolocation-list-info',
	/**
	 * The name of the bodytext elements
	 *
	 * @type String
	 */
	locationBodyName: '.location-body',
	/*
	 * List-options
	 *
	 * @type Object
	 */
	lO: null,
	/***************************************************************************
	 * INITIALIZE
	 **************************************************************************/
	/**
	 * Initialize
	 *
	 * @returns {void}
	 */
	initialize: function () {
		//console.info('Gl::initialize()');

		if (Gl.initializeGlobalElements()) {
			Gl.initializeListOptions();
			Gl.initializeList();
			Gl.initializeMapOptions();
			Gl.initializeMap();
			Gl.initializeSearchOptions();
			Gl.initializeSearch();

			Gl.mapToggleLoadingOverlay(false);
			Gl.listSetLocationMarkers();
		}
	},
	/**
	 * Initialize global elements
	 *
	 * @returns {Boolean}
	 */
	initializeGlobalElements: function () {
		//console.info('Gl::initializeGlobalElements()');

		/**
		 * Common elements
		 */
		Gl.elMessageField = $(Gl.elMessageField).length && $(Gl.elMessageField) || null;

		/**
		 * List elements
		 */
		// Define list-container
		Gl.elListContainer = $(Gl.elListContainer).length && $(Gl.elListContainer) || null;

		/**
		 *  Map elements
		 */
		// Define map-container
		Gl.elMapContainer = $(Gl.elMapContainer).length && $(Gl.elMapContainer) || null;
		// Set map canvas
		Gl.elMapCanvas = $(Gl.elMapCanvas).length && $(Gl.elMapCanvas) || null;
		// Define toggle-lock
		Gl.elMapToggleLock = $(Gl.elMapToggleLock).length && $(Gl.elMapToggleLock) || null;
		// Define loading-overlay
		Gl.elMapLoadingOverlay = $(Gl.elMapLoadingOverlay).length && $(Gl.elMapLoadingOverlay) || null;

		/**
		 * Search elements
		 */
		// Define search-container
		Gl.elSearchContainer = $(Gl.elSearchContainer).length && $(Gl.elSearchContainer) || null;
		// Get the input-element for the search-location (radial-search)
		Gl.elFieldAddress = $(Gl.elFieldAddress).length && $(Gl.elFieldAddress) || null;
		// Get the input-element for the search-radius (radial-search)
		Gl.elFieldRadius = $(Gl.elFieldRadius).length && $(Gl.elFieldRadius) || null;
		// Get the input-element for the search-keyword (fulltext-search)
		Gl.elFieldKeyword = $(Gl.elFieldKeyword).length && $(Gl.elFieldKeyword) || null;
		// Get the element which switches the search type
		Gl.elButtonSearchType = $(Gl.elButtonSearchType).length && $(Gl.elButtonSearchType) || null;
		// Get the set of fields for radial-search
		Gl.elFieldsetRadial = $(Gl.elFieldsetRadial).length && $(Gl.elFieldsetRadial) || null;
		// Get the button for radial-search
		Gl.elButtonRadial = $(Gl.elButtonRadial).length && $(Gl.elButtonRadial) || null;
		// Get the set of fields for fulltext-search
		Gl.elFieldsetFulltext = $(Gl.elFieldsetFulltext).length && $(Gl.elFieldsetFulltext) || null;
		// Get the button for fulltext-search
		Gl.elButtonFulltext = $(Gl.elButtonFulltext).length && $(Gl.elButtonFulltext) || null;

		// Geolocations-elements could not be found, nothing todo, return false
		if (!Gl.elMapContainer && !Gl.elSearchContainer && !Gl.elListContainer) {
			return false;
		} else {
			return true;
		}
	},
	/***************************************************************************
	 * INITIALIZE LIST
	 **************************************************************************/
	/**
	 * Initialize the list options
	 *
	 * @returns {undefined}
	 */
	initializeListOptions: function () {
		//console.info('Gl::initializeListOptions()');

		// Get configuration values from list-container
		Gl.lO = {
			clickMode: Gl.elListContainer.data('clickmode')
		};
	},
	/**
	 * Initialize the list view
	 *
	 * @returns {undefined}
	 */
	initializeList: function () {
		//console.info('Gl::initializeList()');

		// Set links to all listed location records
		if (Gl.lO.clickMode) {
			switch (Gl.lO.clickMode) {
				case 'unveilRecord':
					$(Gl.locationElementsName).click(Gl.listUnveilRecord);
					break;
				case 'showInMap':
					$(Gl.locationElementsName).click(Gl.listShowInMap);
					break;
			}
		}
	},
	/***************************************************************************
	 * INITIALIZE MAP
	 **************************************************************************/
	/**
	 * Get default configuration values from data-attributes of the elMapContainer and set mapOptions
	 *
	 * @returns {undefined}
	 */
	initializeMapOptions: function () {
		//console.info('Gl::initializeMapOptions()');

		if (Gl.elMapContainer) {
			Gl.mO = {};

			var defaultOptions = {};
			var preparedUserStyles = [];
			var mapTypeIds = [];

			// Get configuration values from map-container
			var center = Gl.elMapContainer.data('center');
			var zoom = Gl.elMapContainer.data('zoom');
			var minZoom = Gl.elMapContainer.data('minzoom');
			var maxZoom = Gl.elMapContainer.data('maxzoom');
			var zoomControl = Gl.elMapContainer.data('zoomcontrol');
			var draggable = Gl.elMapContainer.data('draggable');
			var scrollwheel = Gl.elMapContainer.data('scrollwheel');
			var enableDragScroll = Gl.elMapContainer.data('enabledragscroll');
			var fullscreenControl = Gl.elMapContainer.data('fullscreencontrol');
			var backgroundColor = Gl.elMapContainer.data('background');
			var circleColor = Gl.elMapContainer.data('circle');
			var mapTypes = Gl.elMapContainer.data('maptypes');
			var userStyles = Gl.elMapContainer.data('userstyles');

			// Set initial center
			if (center) {
				center = center.split(',');
				defaultOptions.center = Gl.getLatLng(center[0], center[1]);
			} else {
				defaultOptions.center = Gl.getLatLng(48.2081743, 16.3738189);
			}
			// Set initial zoom
			if (zoom) {
				defaultOptions.zoom = parseInt(zoom);
			} else {
				defaultOptions.zoom = 14;
			}
			// Set minimum zoom
			if (minZoom) {
				defaultOptions.minZoom = parseInt(minZoom);
			}
			// Set maximum zoom
			if (maxZoom) {
				defaultOptions.maxZoom = parseInt(maxZoom);
			}
			// Set zoomControl
			if (zoomControl) {
				defaultOptions.zoomControl = Gl.parseBoolean(zoomControl);
			}
			// Set draggable
			Gl.mO.draggable = Gl.parseBoolean(draggable);
			defaultOptions.draggable = false;
			// Set scrollwheel
			Gl.mO.scrollwheel = Gl.parseBoolean(scrollwheel);
			defaultOptions.scrollwheel = false;
			// Set enableDragScroll
			Gl.mO.enableDragScroll = Gl.parseBoolean(enableDragScroll);
			if (Gl.mO.enableDragScroll) {
				if (Gl.mO.draggable) {
					defaultOptions.draggable = true;
				}
				if (Gl.mO.scrollwheel) {
					defaultOptions.scrollwheel = true;
				}
			}
			// Set fullscreenControl
			if (fullscreenControl) {
				defaultOptions.fullscreenControl = Gl.parseBoolean(fullscreenControl);
			}
			// Set backgroundColor
			if (backgroundColor) {
				defaultOptions.backgroundColor = backgroundColor;
			}
			// Set backgroundColor
			if (circleColor) {
				Gl.mO.circleColor = circleColor;
			}
			// Set mapTypeIds
			if (mapTypes && mapTypes !== '') {
				mapTypeIds = mapTypes.split(',');
			}
			// Set user styles
			if (userStyles && userStyles !== '') {
				try {
					var i = 1;
					// Double decode from json (map style is a json-string already)
					userStyles = $.parseJSON($.parseJSON(userStyles));
					$.each(userStyles, function (key, value) {
						mapTypeIds.push('user_style' + i);
						preparedUserStyles.push({
							mapTypeId: 'user_style' + i,
							name: key,
							config: value
						});
						i++;
					});
				} catch (err) {
					console.warn('Style could not be preprocessed.');
				}
			}
			if (mapTypeIds.length > 0) {
				defaultOptions.mapTypeControlOptions = {mapTypeIds: mapTypeIds};
			}
			if (preparedUserStyles.length > 0) {
				Gl.mO.styledMapTypes = preparedUserStyles;
			}
			defaultOptions.scaleControl = true;
			defaultOptions.streetViewControl = false;
			defaultOptions.scaleControlOptions = {position: google.maps.ControlPosition.BOTTOM_LEFT};

			Gl.mO.defaults = defaultOptions;
		}
	},
	/**
	 * Create a map object, and include the MapTypeId to add to the map type control.
	 *
	 * @returns {undefined}
	 */
	initializeMap: function () {
		//console.info('Gl::initializeMap()');

		// Initialize map if canvas is available
		if (Gl.mO && Gl.elMapContainer && Gl.elMapCanvas && Gl.mO.defaults) {
			// Create map if not already created
			if (!Gl.map) {
				Gl.map = new google.maps.Map(Gl.elMapCanvas[0], Gl.mO.defaults);
			}
			// Iterate through preprocessed styles and apply them
			if (Gl.mO.styledMapTypes) {
				$.each(Gl.mO.styledMapTypes, function (key, style) {
					var styledMap = null;
					// Try to create a new StyledMapType object
					try {
						styledMap = new google.maps.StyledMapType(style.config, {name: style.name});
					} catch (err) {
						console.warn('Style could not be applied.');
					}
					// Associate the styled map with the MapTypeId and set it to display
					if (styledMap) {
						Gl.map.mapTypes.set(style.mapTypeId, styledMap);
						Gl.map.setMapTypeId(style.mapTypeId);
					}
				});
			}
			if (Gl.elFieldRadius) {
				var radius = Gl.elFieldRadius.val();
			}
			// Create toggle for 'draggable' and 'scrollwheel' after tiles are fully loaded
			if (Gl.mO.draggable || Gl.mO.scrollwheel) {
				google.maps.event.addListenerOnce(this, 'tilesloaded', Gl.mapCustomControls());
			}
		}
	},
	/***************************************************************************
	 * INITIALIZE SEARCH
	 **************************************************************************/
	/**
	 * Initialize options for autocompleter/autolocation
	 *
	 * @returns {undefined}
	 */
	initializeSearchOptions: function () {
		//console.info('Gl::initializeSearchOptions()');

		if (Gl.elSearchContainer) {
			// Get configuration values from search-container
			var searchObject = Gl.elSearchContainer.data('search');
			var enabled = Gl.elSearchContainer.data('autocompleter');
			var country = Gl.elSearchContainer.data('autocompleter-country');
			var types = Gl.elSearchContainer.data('autocompleter-types');
			var language = Gl.elSearchContainer.data('autocompleter-language');
			var autoposition = Gl.elSearchContainer.data('autoposition');
			var perimeter = Gl.elSearchContainer.data('perimeter');

			// Set searchOptions
			Gl.sO = {
				autocompleter: {
					enabled: Gl.parseBoolean(enabled)
				},
				autoposition: Gl.parseBoolean(autoposition)
			};
			// Set autocompleter.country
			if (country && country !== '') {
				Gl.sO.autocompleter.country = country;
			}
			// Set autocompleter.types
			if (types && types !== '') {
				Gl.sO.autocompleter.types = types;
			}
			// Set autocompleter.language
			if (language && language !== '') {
				Gl.sO.autocompleter.language = language;
			}
			// Set perimeter
			if (perimeter) {
				Gl.sO.perimeter = Gl.parseBoolean(perimeter);
			}
			Gl.sO.searchObject = searchObject;
		}
	},
	/**
	 * Initialize search
	 *
	 * @returns {undefined}
	 */
	initializeSearch: function () {
		//console.info('Gl::initializeSearch()');

		if (Gl.sO && Gl.sO.searchObject.length && Gl.elSearchContainer) {
			if (Gl.elFieldAddress) {
				// Activate autocompleter
				if (Gl.sO.autocompleter && Gl.sO.autocompleter.enabled) {
					Gl.searchSetAutocompleter();
				}
				// Activate autodetection of the current position
				if (Gl.sO.autoposition) {
					Gl.searchSetGeolocation();
				}
			}
			if (Gl.elFieldRadius) {
				Gl.elFieldRadius.change(Gl.searchRadiusChanged);
			}
			Gl.searchSetTypeToggle();
			Gl.searchSetButtonsAndElements();
		}
	},
	/***************************************************************************
	 * LIST
	 **************************************************************************/
	/**
	 * Set markers of the listed locations on the map
	 *
	 * @returns {undefined}
	 */
	listSetLocationMarkers: function () {
		//console.info('Gl::listSetLocationMarkers()');

		// Reset all previously set markers
		Gl.mapClearMarkers();
		Gl.markers = [];
		if ($(Gl.locationInfoName).length > 0) {
			// Create a new boundary object
			var bounds = new google.maps.LatLngBounds();
			// Create a new infowindow
			var infowindow = new google.maps.InfoWindow({
				content: '',
				maxWidth: 203
			});
			$(Gl.locationInfoName).each(function () {
				var location = $(this);
				// Get the coordinates from the location record
				var latLng = Gl.getLatLng(location.data('latitude'), location.data('longitude'));
				// Extend the current boundary
				bounds.extend(latLng);
				// Create new marker
				var marker = new google.maps.Marker({
					map: Gl.map,
					position: latLng,
					bounds: true,
					title: location.data('title'),
					content: location.html(),
					zIndex: location.data('index'),
					id: 'mk_' + location.data('uid'),
					letter: location.data('uid')
				});
				// Set marker icon
				var icon = location.data('icon');
				if (icon && icon !== '') {
					marker.setIcon(icon);
				}
				// Push the marker to the 'markers' array (this is used to focus a specific point if a link was clicked)
				Gl.markers.push(marker);
				// Add listener to view a location on the map
				google.maps.event.addListener(marker, 'click', function () {
					infowindow.close();
					infowindow.setContent(this.content);
					infowindow.open(Gl.map, this);
				});
			});
			// Apply fitBounds
			Gl.map.fitBounds(bounds);
		}
	},
	/**
	 * Unveil/open a clicked location item
	 *
	 * @returns {undefined}
	 */
	listUnveilRecord: function () {
		//console.info('Gl::listUnveilRecord()');

		$(this).find(Gl.locationBodyName).fadeToggle();
	},
	/**
	 * Zoom to specific marker if a list-element gets clicked
	 *
	 * @returns {undefined}
	 */
	listShowInMap: function () {
		//console.info('Gl::listShowInMap()');

		var latLng = Gl.getLatLng($(this).data('latitude'), $(this).data('longitude'));
		// Center to the given coordinates
		Gl.map.panTo(latLng);
		// Zoom closer
		Gl.map.setZoom(15);
		google.maps.event.trigger(Gl.markers[$(this).data('index')], 'click');
		// Scroll to the map
		$('html, body').animate({
			scrollTop: Gl.elMapCanvas.position().top + 60
		}, 200);
	},
	/***************************************************************************
	 * SEARCH
	 **************************************************************************/
	/**
	 * Start google's autocompleter
	 *
	 * @returns {undefined}
	 * @private
	 */
	searchSetAutocompleter: function () {
		//console.info('Gl::searchSetAutocompleter()');

		var options = {};
		// Set the region type
		if (Gl.sO.autocompleter.types) {
			options.types = [Gl.sO.autocompleter.types];
		}
		// Restrict the search to a specific country
		if (Gl.sO.autocompleter.country) {
			options.componentRestrictions = {country: Gl.sO.autocompleter.country};
		}
		// Create the autocomplete object
		var autocomplete = new google.maps.places.Autocomplete(
				Gl.elFieldAddress[0],
				options
				);
		autocomplete.addListener('place_changed', function () {
			Gl.addressFieldChangedManually = false;
			var place = autocomplete.getPlace();
			if (!place.geometry) {
				var firstResult = $('.pac-container .pac-item:first').text();
				Gl.reverseGeocode({address: firstResult}, 'formatted_address', Gl.reverseGeocodeReturned);
			} else {
				Gl.latLng = place.geometry.location;
				Gl.mapUpdate(Gl.latLng);
				Gl.searchStartRadial();
			}
		});
	},
	/*
	 * Get the Location by the browser and write the address to the input-field
	 *
	 * @returns {undefined}
	 * @private
	 */
	searchSetGeolocation: function () {
		//console.info('Gl::searchSetGeolocation()');

		if (!!navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function (position) {
				var latLng = Gl.getLatLng(position.coords.latitude, position.coords.longitude);
				Gl.reverseGeocode({location: latLng}, 'formatted_address', Gl.reverseGeocodeReturned);
				Gl.msg('autocomplete.success', 'Position could be automatically resolved.', 2);
			}, function () {
				Gl.msg('autocomplete.error', 'Geolocation service failed.', 4);
			});
		}
		// Browser doesn't support Geolocation
		else {
			Gl.msg('autocomplete.notavailable', 'Position could not be resolved.', 5);
		}
	},
	/**
	 * Set the behaviour of the search type button
	 *
	 * @returns {undefined}
	 */
	searchSetTypeToggle: function () {
		//console.info('Gl::searchSetTypeToggle()');

		// Disable buttons per default
		if (Gl.elButtonRadial) {
			Gl.elButtonRadial.prop('disabled', true);
		}
		if (Gl.elButtonFulltext) {
			Gl.elButtonFulltext.prop('disabled', true);
		}
		if (Gl.elButtonSearchType) {
			var searchType = null;
			$('#search-type input[type="radio"]:checked').each(function () {
				searchType = $(this).val();
			});
			$('#search-' + searchType).show();
			Gl.elButtonSearchType.change(function () {
				Gl.elFieldsetFulltext.toggle();
				Gl.elFieldsetRadial.toggle();
			});
		} else {
			if (Gl.elFieldsetFulltext) {
				Gl.elFieldsetFulltext.show();
			}
			if (Gl.elFieldsetRadial) {
				Gl.elFieldsetRadial.show();
			}
		}
	},
	/**
	 * Set eventhandlers for search-buttons and -fields
	 *
	 * @returns {undefined}
	 */
	searchSetButtonsAndElements: function () {
		//console.info('Gl::searchSetButtonsAndElements()');

		if (Gl.elFieldsetRadial) {
			if (Gl.elFieldKeyword) {
				Gl.elFieldAddress.on('input', function () {
					Gl.addressFieldChangedManually = true;
					if ($(this).val().length < 3) {
						Gl.elButtonRadial.prop('disabled', true);
					} else {
						Gl.elButtonRadial.prop('disabled', false);
					}
				});
			}
			// Start radial-search if radial-search-button is clicked
			if (Gl.elButtonRadial) {
				Gl.elButtonRadial.on('click', function () {
					Gl.reverseGeocode({address: Gl.elFieldAddress.val()}, 'formatted_address', Gl.reverseGeocodeReturned);
				});
			}
		}
		if (Gl.elFieldsetFulltext) {
			// Disable/enable fulltext-search-button
			if (Gl.elFieldKeyword) {
				Gl.elFieldKeyword.on('input', function () {
					if ($(this).val().length < 3) {
						Gl.elButtonFulltext.prop('disabled', true);
					} else {
						Gl.elButtonFulltext.prop('disabled', false);
					}
				});
				// Start fulltext-search on keypress (enter)
				Gl.elFieldKeyword.focusin(function () {
					$(this).keypress(function (e) {
						if (!Gl.elButtonFulltext.prop('disabled')) {
							if (e.which === 13) {
								Gl.searchStartFulltext();
							}
						}
					});
				});
			}
			// Start fulltext-search if fulltext-search-button is clicked
			if (Gl.elButtonFulltext) {
				Gl.elButtonFulltext.on('click', Gl.searchStartFulltext);
			}
		}
		// Add click handler to the reset button
		$(Gl.elClearFieldName).on('click', Gl.searchClearField);
	},
	/**
	 * This is called if the clear input field button is clicked
	 *
	 * @returns {undefined}
	 */
	searchClearField: function () {
		//console.info('Gl::searchClearField()');

		$(this).siblings('input').val('').focus();
		$(this).parent().parent().find('button').attr('disabled', true);
	},
	/**
	 * This is called if the radius field changes
	 *
	 * @returns {undefined}
	 */
	searchRadiusChanged: function () {
		//console.info('Gl::searchRadiusChanged()');

		if (Gl.sO.perimeter) {
			if (Gl.perimeterCircle) {
				Gl.perimeterCircle.setRadius(parseInt(Gl.elFieldRadius.val()) * 1000);
				if (Gl.addressFieldChangedManually) {
					Gl.reverseGeocode({address: Gl.elFieldAddress.val()}, 'formatted_address', Gl.reverseGeocodeReturned);
				} else {
					Gl.searchStartRadial();
				}
			}
		}
	},
	/**
	 * Disable/enable input field if input is lower/greater than 3 chars
	 *
	 * @returns {undefined}
	 */
	searchCheckInput: function () {
		//console.info('Gl::searchCheckInput()');

		if ($(this).val().length < 3) {
			Gl.elButtonFulltext.prop('disabled', true);
		} else {
			Gl.elButtonFulltext.prop('disabled', false);
		}
	},
	/**
	 * Disable/enable fieldsets if search is running
	 *
	 * @param {type} fieldSet
	 * @param {type} enable
	 * @returns {undefined}
	 */
	searchToggleFieldset: function (fieldSet, enable) {
		//console.info('Gl::searchToggleFieldset()');

		if (fieldSet.length) {
			if (enable) {
				fieldSet.find('select, input, button').attr('disabled', false);
			} else {
				fieldSet.find('select, input, button').attr('disabled', true);
			}
		}
	},
	/**
	 * Start the radial search
	 *
	 * @returns {undefined}
	 */
	searchStartRadial: function () {
		//console.info('Gl::searchStartRadial()');

		if (Gl.latLng) {
			var addParams = {
				searchType: 'radial',
				latitude: Gl.latLng.lat(), longitude: Gl.latLng.lng(),
				radius: Gl.elFieldRadius.val()
			};
			Gl.searchAjax(Gl.searchGetRequest(addParams), Gl.searchAjaxReturned, Gl.elFieldsetRadial);
		}
	},
	/**
	 * Start the fulltext search
	 *
	 * @returns {undefined}
	 */
	searchStartFulltext: function () {
		//console.info('Gl::searchStartFulltext()');

		// Reset positionmarker and perimeter
		if (Gl.positionMarker) {
			Gl.positionMarker.setMap(null);
			Gl.positionMarker = false;
		}
		if (Gl.perimeterCircle) {
			Gl.perimeterCircle.setMap(null);
			Gl.perimeterCircle = false;
		}
		var keyword = Gl.elFieldKeyword.val();
		if (keyword !== '') {
			var addParams = {
				searchType: 'fulltext',
				keyword: keyword
			};
			Gl.searchAjax(Gl.searchGetRequest(addParams), Gl.searchAjaxReturned, Gl.elFieldsetFulltext);
		}
	},
	/**
	 * Set and extend the query parameters for ajax-search
	 *
	 * @param {type} addParams
	 * @returns {Gl.searchGetRequest.requestParameter}
	 */
	searchGetRequest: function (addParams) {
		//console.info('Gl::searchGetRequest()');

		var requestParameter = {
			tx_geolocations_pi1: {
				controller: 'Ajax', action: 'search', searchObject: Gl.sO.searchObject, categories: Gl.getCheckedCategories()
			}
		};
		$.extend(requestParameter.tx_geolocations_pi1, addParams);
		return requestParameter;
	},
	/**
	 * Start the ajax search
	 *
	 * @param {type} requestParameter
	 * @param {type} callback
	 * @param {type} fieldSet
	 * @returns {undefined}
	 */
	searchAjax: function (requestParameter, callback, fieldSet) {
		//console.info('Gl::searchAjax()');

		// Disable fieldset
		Gl.mapToggleLoadingOverlay(true);
		Gl.searchToggleFieldset(fieldSet, false);
		$.ajax({
			url: Gl.getBaseUrl() + Gl.ajaxRequestUrl,
			data: requestParameter,
			type: 'get',
			dataType: 'json',
			cache: false,
			async: true,
			success: function (data) {
				callback(data, fieldSet);
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				console.error(errorThrown);
				callback(null, fieldSet);
			}
		});
	},
	/**
	 * This is called if the ajax search successfully returned
	 *
	 * @param {type} data
	 * @param {type} fieldSet
	 * @returns {undefined}
	 */
	searchAjaxReturned: function (data, fieldSet) {
		//console.info('Gl::searchAjaxReturned()');

		if (data) {
			Gl.elListContainer.html(data);
			Gl.initializeList();
			Gl.listSetLocationMarkers();
		}
		Gl.mapToggleLoadingOverlay(false);
		Gl.searchToggleFieldset(fieldSet, true);
	},
	/**
	 * Get checked categories
	 *
	 * @returns {Gl.getCheckedCategories.categories|Array}
	 */
	getCheckedCategories: function () {
		//console.info('Gl::getCheckedCategories()');

		var categories = [];
		$("#search-categories input[type='checkbox']:checked").each(function () {
			categories.push($(this).val());
		});
		return categories;
	},
	/***************************************************************************
	 * MAP
	 **************************************************************************/
	/**
	 * Add custom controls for dragging and zooming on mobile devices
	 *
	 * @returns {undefined}
	 */
	mapCustomControls: function () {
		//console.info('Gl::mapCustomControls()');

		// Create the DIV to hold the control
		var controlDiv = document.createElement('div');
		// Detach the target element from current position
		var elMapToggleLock = Gl.elMapToggleLock.detach();
		if (!Gl.mO.enableDragScroll) {
			elMapToggleLock.addClass('disabled');
		}
		elMapToggleLock.show();
		elMapToggleLock[0].addEventListener('click', function () {
			Gl.mapToggleCustomControls();
		});
		// Append the toggle to the new container
		controlDiv.appendChild(elMapToggleLock[0]);
		// Put the toggle into the map
		Gl.map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv);
	},
	/**
	 * Enable/disable draggable and scrollwheel
	 *
	 * @param {type} enable
	 * @returns {undefined}
	 */
	mapToggleCustomControls: function (enable) {
		//console.info('Gl::mapToggleCustomControls()');

		if (enable === true) {
			// Enable
			if (Gl.mO.draggable) {
				Gl.map.set('draggable', true);
			}
			if (Gl.mO.scrollwheel) {
				Gl.map.set('scrollwheel', true);
			}
			Gl.elMapToggleLock.removeClass('disabled');
		} else if (enable === false) {
			// Disable
			Gl.map.set('draggable', false);
			Gl.map.set('scrollwheel', false);
			Gl.elMapToggleLock.addClass('disabled');
		} else {
			// Toggle
			if (Gl.mO.draggable) {
				Gl.mapToggleOptions('draggable');
			}
			if (Gl.mO.scrollwheel) {
				Gl.mapToggleOptions('scrollwheel');
			}
			Gl.elMapToggleLock.toggleClass('disabled');
		}
	},
	/**
	 * Toggle draggable and scrollwheel
	 *
	 * @param {type} element
	 * @returns {undefined}
	 */
	mapToggleOptions: function (element) {
		//console.info('Gl::mapToggleOptions()');

		if (Gl.map.get(element)) {
			Gl.map.set(element, false);
		} else {
			Gl.map.set(element, true);
		}
	},
	/**
	 * Show/hide the loading overlay
	 *
	 * @param {type} enable
	 * @returns {undefined}
	 */
	mapToggleLoadingOverlay: function (enable) {
		//console.info('Gl::mapToggleLoadingOverlay()');

		if (Gl.elMapLoadingOverlay) {
			if (enable) {
				Gl.elMapLoadingOverlay.fadeIn(50);
			} else {
				Gl.elMapLoadingOverlay.fadeOut(300);
			}
		}
	},
	/**
	 * Update the map and add dragable marker for repositioning if needed
	 *
	 * @param {object} latLng
	 * @returns {undefined}
	 */
	mapUpdate: function (latLng) {
		//console.info('Gl::mapUpdate()');

		// Set current value to global latLng
		Gl.latLng = latLng;
		if (Gl.map) {
			// Add the perimeter cirle to the map
			if (Gl.elMapCanvas) {
				if (Gl.sO.perimeter && Gl.elFieldRadius) {
					if (!Gl.perimeterCircle) {
						Gl.perimeterCircle = new google.maps.Circle({
							strokeColor: Gl.mO.circleColor,
							strokeOpacity: 0.8,
							strokeWeight: 2,
							fillColor: '#ffffff',
							fillOpacity: 0.05,
							map: Gl.map,
							center: latLng,
							radius: Gl.elFieldRadius.val() * 1000,
							cursor: null
						});
					}
					Gl.perimeterCircle.setCenter(latLng);
					if (!Gl.firstRun) {
						Gl.map.fitBounds(Gl.perimeterCircle.getBounds());
					}
				}
				if (!Gl.positionMarker) {
					Gl.positionMarker = new google.maps.Marker({
						position: latLng,
						icon: {
							path: google.maps.SymbolPath.CIRCLE,
							strokeColor: Gl.mO.circleColor,
							fillColor: '#ffffff',
							fillOpacity: 0.9,
							strokeWeight: 3,
							scale: 8
						},
						draggable: true,
						map: Gl.map
					});
					// Add event listener for marker-repositioning
					google.maps.event.addListener(Gl.positionMarker, 'dragend', function () {
						var latLng = Gl.getLatLng(this.getPosition().lat(), this.getPosition().lng());
						Gl.latLng = latLng;
						var location = Gl.reverseGeocode({location: latLng}, 'formatted_address', Gl.reverseGeocodeReturned);
						// Set perimeter to new position
						if (Gl.elMapCanvas) {
							if (Gl.sO.perimeter) {
								Gl.perimeterCircle.setCenter(latLng);
							}
							Gl.map.panTo(latLng);
						}
					});
				}
				if (!Gl.firstRun) {
					Gl.positionMarker.setPosition(latLng);
					// Set the center of the map
					Gl.map.setCenter(latLng);
					//Gl.map.panTo(latLng);
				}
			}
			Gl.firstRun = false;
		}
	},
	/**
	 * Clear all markers from map
	 *
	 * @returns {undefined}
	 */
	mapClearMarkers: function () {
		//console.info('Gl::mapClearMarkers()');

		if (Gl.markers && Gl.markers.length > 0) {
			for (var i = 0; i < Gl.markers.length; i++) {
				Gl.markers[i].setMap(null);
			}
			Gl.markers.length = 0;
		}
	},
	/***************************************************************************
	 * HELPERS
	 **************************************************************************/
	/**
	 * Reverse geocode latitude/longitude to place-ID
	 *
	 * @param {type} search
	 * @param {type} fieldName The content-field
	 * @param {type} callback The callback on success
	 * @returns {undefined}
	 */
	reverseGeocode: function (search, fieldName, callback) {
		//console.info('Gl::reverseGeocode()');

		var geocoder = new google.maps.Geocoder();
		if (geocoder) {
			geocoder.geocode(search, function (results, status) {
				if (status === google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						var fieldValue = '';
						switch (fieldName) {
							case 'formatted_address':
								fieldValue = results[0].formatted_address;
								break;
							case 'place_id':
								fieldValue = results[0].place_id;
								break;
							default:
								break;
						}
						callback(fieldValue, results);
					}
				} else {
					callback();
				}
			});
		}
	},
	reverseGeocodeReturned: function (fieldValue, results) {
		//console.info('Gl::reverseGeocodeReturned()');

		if (results) {
			Gl.latLng = results[0].geometry.location;
			Gl.mapUpdate(Gl.latLng);
			Gl.searchStartRadial();
		} else {
			Gl.msg('autocomplete.notavailable', 'Position could not be resolved.', 5);
		}
		if (Gl.elFieldAddress) {
			Gl.elFieldAddress.val(fieldValue);
		}
		Gl.elButtonRadial.prop('disabled', false);
	},
	/**
	 * Get google.maps.LatLng from latitude, longitude
	 *
	 * @param {type} latitude
	 * @param {type} longitude
	 * @returns {google.maps.LatLng}
	 */
	getLatLng: function (latitude, longitude) {
		//console.info('Gl::getLatLng()');

		if (!latitude || latitude === '') {
			latitude = 0;
		}
		if (!longitude || longitude === '') {
			longitude = 0;
		}
		return new google.maps.LatLng(
				parseFloat(latitude),
				parseFloat(longitude)
				);
	},
	/**
	 * Return baseUrl as prefix
	 *
	 * @return {string} Base url
	 * @private
	 */
	getBaseUrl: function () {
		//console.info('Gl::getBaseUrl()');

		var baseUrl = $('base');
		if (baseUrl.length > 0 && baseUrl !== '/') {
			baseUrl = jQuery('base').prop('href');
			if (baseUrl.substr(0, 2) === '//') {
				baseUrl = window.location.protocol + baseUrl;
			}
		} else {
			if (window.location.protocol !== 'https:') {
				baseUrl = 'http://' + window.location.hostname + '/';
			} else {
				baseUrl = 'https://' + window.location.hostname + '/';
			}
		}
		return baseUrl;
	},
	/**
	 * Convert string to boolean value
	 *
	 * @param {type} value
	 * @returns {Boolean}
	 */
	parseBoolean: function (value) {
		if (value === 1 || value === '1' || value === 'true') {
			return true;
		} else {
			return false;
		}
	},
	/**
	 * Debug messages
	 *
	 * @param {type} langId
	 * @param {type} msg
	 * @param {type} severity
	 * @returns {undefined}
	 */
	msg: function (langId, msg, severity) {
		if (langId !== '' && TYPO3.lang[langId][0]['target']) {
			msg = TYPO3.lang[langId][0]['target'];
		}
		if (Gl.elMessageField) {
			var cssClass = '';
			switch (severity) {
				case 5:
					console.error(msg);
					cssClass = 'danger';
					break;
				case 4:
					console.warn(msg);
					cssClass = 'warning';
					break;
				case 3:
					console.info(msg);
					cssClass = 'info';
					break;
				case 2:
					console.log(msg);
					cssClass = 'success';
					break;
				case 1:
					console.debug(msg);
					cssClass = 'primary';
					break;
				case 0:
				default:
					console.log(msg);
					cssClass = 'default';
					break;
			}
			Gl.elMessageField.removeClass();
			Gl.elMessageField.addClass('message-' + cssClass);
			Gl.elMessageField.text(msg).show();
			Gl.elMessageField.delay(1000).fadeOut(400);

		}
	}
};

jQuery(window).load(function ($) {
	'use strict';
	//console.info('Start Geolocations');

	// Fix bug where dragging does not work on some touch-devices
	// http://stackoverflow.com/questions/28661844/touch-events-not-working-with-google-maps-in-angular/31023464
	//function fixTouchBug() {
	//	return true === ('ontouchstart' in window || window.DocumentTouch && document instanceof DocumentTouch);
	//}
	// check if google-maps-api is available
	if (typeof google === 'object' && typeof google.maps === 'object') {
		//if (fixTouchBug() === true) {
		//	navigator = navigator || {};
		//	navigator.msMaxTouchPoints = navigator.msMaxTouchPoints || 2;
		//}
		// Start Geolocations
		Gl.initialize();
	}
});
