var Geocoder = {
	// Container for the marker
	marker: null,
	// UID of the current record
	uid: 0,
	// Open the map in new window
	mapNewWindow: false,
	// Container for google's geocoder
	geocoder: null,
	// Container for the map-object
	map: null,
	// Map options
	mapOptions: {
		// Initial zoom
		zoom: 17,
		// Deactivate scrollwheel
		scrollwheel: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	},
	// The element for the map
	$mapCanvas: null,
	// The element for messages
	$messageField: null,
	/**
	 * Initialize the geocoder-script
	 *
	 * @param {number} uid Uid of the current record
	 */
	initialize: function (uid) {
		this.uid = uid;
		// Initialize geocoder
		this.geocoder = new google.maps.Geocoder();
		// Set map canvas
		this.$mapCanvas = TYPO3.jQuery('#map_canvas');
		// Set message area
		this.$messageField = TYPO3.jQuery('#geocode-status-' + uid);
		// Set new position of the map canvas
		var formSection = this.$mapCanvas.closest('.form-section');
		formSection.append(this.$mapCanvas.detach());
	},
	/**
	 * Shows the map
	 *
	 * @returns {undefined}
	 */
	showMap: function () {
		Geocoder.resetMessage();
		var latitude = Geocoder.getFieldValue('latitude').toFloat();
		var longitude = Geocoder.getFieldValue('longitude').toFloat();
		if (!isNaN(latitude) && !isNaN(longitude)) {
			if (this.mapNewWindow) {
				Geocoder.linkToMap(latitude, longitude);
			} else {
				Geocoder.updateMap(latitude, longitude);
			}
		} else {
			Geocoder.showMessage(TYPO3.lang['geocoder.missingGeometry'], 2);
		}
	},
	/**
	 * Geocode an address to latitude/longitude
	 *
	 * @returns {undefined}
	 */
	geocodeAddress: function () {
		Geocoder.resetMessage();
		var addressToGeocode = Geocoder.getAddress(this.uid);
		if (addressToGeocode !== '') {
			this.geocoder.geocode({address: addressToGeocode}, function (results, status) {
				if (status === google.maps.GeocoderStatus.OK) {
					var latlng = results[0].geometry.location;
					Geocoder.setFieldValue('latitude', latlng.lat());
					Geocoder.setFieldValue('longitude', latlng.lng());
					Geocoder.setFieldValue('place_id', results[0].place_id);
					// Reset map position
					Geocoder.updateMap(latlng.lat(), latlng.lng());
				} else {
					Geocoder.showMessage(TYPO3.lang['geocoder.error'] + status, 1);
				}
			});
		} else {
			Geocoder.showMessage(TYPO3.lang['geocoder.missingAddress'], 2);
		}
	},
	/**
	 * Reverse geocode latitude/longitude to place-ID
	 *
	 * @returns {undefined}
	 */
	reverseGeocodeFromLatLng: function (latitude, longitude) {
		var latlng = {lat: parseFloat(latitude), lng: parseFloat(longitude)};
		this.geocoder.geocode({location: latlng}, function (results, status) {
			if (status === google.maps.GeocoderStatus.OK) {
				if (results[1]) {
					Geocoder.setFieldValue('place_id', results[0].place_id);
				}
			} else {
				Geocoder.showMessage(TYPO3.lang['geocoder.error'] + status, 1);
			}
		});
	},
	/**
	 * Opens the map in a new window
	 *
	 * @param {number} latitude
	 * @param {number} longitude
	 * @returns {undefined}
	 */
	linkToMap: function (latitude, longitude) {
		var url = 'https://maps.google.de/maps?q=' + latitude + ',' + longitude;
		var win = window.open(url, '_blank');
		win.focus();
	},
	/**
	 * Unveil the map inline and add dragable marker for repositioning
	 *
	 * @param {number} latitude
	 * @param {number} longitude
	 * @returns {undefined}
	 */
	updateMap: function (latitude, longitude) {
		if (this.$mapCanvas.css('display') === 'none') {
			this.$mapCanvas.show();
		}
		// Create map if not already created
		if (!this.map) {
			this.map = new google.maps.Map(this.$mapCanvas[0], this.mapOptions);
		}
		// Reset marker
		if (this.marker) {
			this.marker.setMap(null);
		}
		// Set new marker
		this.marker = new google.maps.Marker({
			map: this.map,
			position: {lat: latitude, lng: longitude},
			draggable: true
		});
		// Set the center of the map
		this.map.setCenter({lat: latitude, lng: longitude});
		// Add event listener for marker-repositioning
		google.maps.event.addListener(this.marker, "dragend", function () {
			var latitude = Geocoder.marker.getPosition().lat();
			var longitude = Geocoder.marker.getPosition().lng();
			// Reverse geocode (get place_id from new coordinates)
			var placeId = Geocoder.reverseGeocodeFromLatLng(latitude, longitude);
			Geocoder.setFieldValue('latitude', latitude);
			Geocoder.setFieldValue('longitude', longitude);
		});
	},
	/**
	 * Get the whole address-string (address, zip city)
	 *
	 * @returns {String|TYPO3@call;jQuery.value|addressToGeocode}
	 */
	getAddress: function () {
		var zip = Geocoder.getFieldValue('zip');
		var city = Geocoder.getFieldValue('city');
		addressToGeocode = Geocoder.getFieldValue('address');
		if (zip !== '' || city !== '') {
			addressToGeocode += ',';
			if (zip !== '') {
				addressToGeocode += ' ' + zip;
			}
			if (city !== '') {
				addressToGeocode += ' ' + city;
			}
		}
		return addressToGeocode;
	},
	/**
	 * Get the value of a specific field
	 *
	 * @param {string} field
	 * @returns {TYPO3@call;jQuery.value|String}
	 */
	getFieldValue: function (field) {
		var theField = 'data[tx_geolocations_domain_model_location][' + this.uid + '][' + field + ']';
		var $humanReadableField = TYPO3.jQuery('[data-formengine-input-name="' + theField + '"]');
		if ($humanReadableField.length) {
			return $humanReadableField[0].value;
		} else {
			return '';
		}
	},
	/**
	 * Set the value of a specific field
	 *
	 * @param {string} field
	 * @param {string} newValue
	 * @returns {undefined}
	 */
	setFieldValue: function (field, newValue) {
		var theField = 'data[tx_geolocations_domain_model_location][' + this.uid + '][' + field + ']';
		TBE_EDITOR.isChanged = 1;
		// Modify the "field has changed" info by adding a class to the container element (based on palette or main field)
		var $formField = TYPO3.jQuery('[name="' + theField + '"]');
		var $humanReadableField = TYPO3.jQuery('[data-formengine-input-name="' + theField + '"]');
		if (!$formField.is($humanReadableField)) {
			$humanReadableField[0].value = newValue;
			$humanReadableField.triggerHandler('change');
		}
		$formField[0].value = newValue;
		var $paletteField = $formField.closest('.t3js-formengine-palette-field');
		$paletteField.addClass('has-change');
	},
	/**
	 * Show a notification below the field
	 *
	 * @param {string} message
	 * @param {number} status
	 * @returns {undefined}
	 */
	showMessage: function (message, status) {
		switch (status) {
			case 2:
				var cssClass = 'label-info';
				break;
			case 1:
				var cssClass = 'label-danger';
				break;
			default:
				var cssClass = 'label-success';
		}
		this.$messageField.html(TYPO3.jQuery('<span />', {'class': 'label ' + cssClass}).text(message)).show();
	},
	/**
	 * Resets the notification
	 *
	 * @returns {undefined}
	 */
	resetMessage: function () {
		this.$messageField.hide();
	}
};
/**
 * Parse string to float
 *
 * @returns {number}
 */
String.prototype.toFloat = function () {
	return parseFloat(this);
};
