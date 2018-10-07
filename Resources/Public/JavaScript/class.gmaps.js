/*
 * Provides functionality to geocode, localize positions by using
 * google maps api v3 and display given geocoded postions in
 * google maps views.
 *
 * @package				class.gmaps
 * @version				1.0: gmaps,  03-2011
 * @copyright 			(c)2011 Christian Brinkert
 * @author 				Christian Brinkert <christian.brinkert@googlemail.com>
 */

function Gmaps(){
	// set self
	var self = this;

	// set private properties
	var street = null;
	var zip = null;
	var city = null;
	var country = null;

	// initialize defauls
	var lat = null;
	var lng = null;
	
	// current uid
	var uid = null;

	// current map provider
	var mapProvider = null;



	// helper methods
	function trim(givenString){
		return givenString.replace (/^\s+/, '').replace (/\s+$/, '');
	}



	// getter & setter
	this.setStreet = function(streetString){
		if (typeof(streetString) === 'string' && '' !== streetString)
			self.street = streetString;
	};
	this.getStreet = function(){
		return self.street;
	};


	this.setZip = function(zipCode){
		if (typeof(zipCode) === 'string' && '' !== zipCode)
			self.zip = zipCode;
	};
	this.getZip = function(){
		return self.zip;
	};


	this.setCity = function(cityString){
		if (typeof(cityString) === 'string' && '' !== cityString)
			self.city = cityString;
	};
	this.getCity = function(){
		return self.city;
	};


	this.setCountry = function(countryString){
		if (typeof(countryString) === 'string' && '' !== countryString)
			self.country = countryString;
	};
	this.getCountry = function(){
		return self.country;
	};


	this.setLatitude = function(latitude){
		if (typeof(latitude) === 'number')
			self.lat = latitude;
	};
	this.getLatitude = function(){
		return self.lat;
	};


	this.setLongitude = function(longitude){
		if (typeof(longitude) === 'number')
			self.lng = longitude;
	};
	this.getLongitude = function(){
		return self.lng;
	};


	this.setUid = function(uid){
		self.uid = uid;
	};
	this.getUid = function(){
		return self.uid;
	};


	this.setMapProvider = function(mapProvider){
		self.mapProvider = mapProvider;
	};
	this.getMapProvider = function(){
		return self.mapProvider;
	};


    /**
	 * Fetch coordinates from mapProvider
	 * @param callback object
	 * @param uid int
	 * @param mapProvider string
	 * @param mapboxAccesstoken string
     */
	this.fetchCoordinatesByAddress = function(callback, mapProvider, mapboxAccesstoken){
        // set mapProvider
		self.setMapProvider(mapProvider);

		// if google is current map provider, ask google for localization
		if ('Google' === self.getMapProvider()) {
            // create google geocoder object
            var geocoder = new google.maps.Geocoder();

            if (geocoder) {
                // try to fetch coordinates and save them to local property
                geocoder.geocode({'address': this.getAddressAsString()}, function (results, status) {
                    // check if geocoding was successfull
                    if (status === google.maps.GeocoderStatus.OK) {
                        self.setLatitude(results[0].geometry.location.lat());
                        self.setLongitude(results[0].geometry.location.lng());
                        // return values to callback method
                        callback(self);
                    }
                });
            }

        } else if ('MapBox' === self.getMapProvider()) {
            //https://api.mapbox.com/geocoding/v5/mapbox.places/Los%20Angeles.json?access_token=your-access-token
			// if mapbox is current map provider
			var xhr = new XMLHttpRequest();
			var searchUri = 'https://api.mapbox.com/geocoding/v5/mapbox.places/'
						  +	encodeURI(this.getAddressAsString()) +'.json?access_token='+ mapboxAccesstoken;

            xhr.open('GET', searchUri );
            xhr.onload = function () {
                // parse result string to json object
                results = JSON.parse(xhr.response);

                if (0 < results.features.length){
                    self.setLatitude(parseFloat(results.features[0].geometry.coordinates[1]));
                    self.setLongitude(parseFloat(results.features[0].geometry.coordinates[0]));
                    // return values to callback method
                    callback(self);
                } else {
                    alert('Given address can\'t be localized.');
                }
            };
            xhr.onerror = function () {
                alert('Localization service not available, check internet connection.');
            };
            xhr.send();

		} else {
			// if openstreetmap is current map provider
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'https://nominatim.openstreetmap.org/search?format=json&q='+ encodeURI(this.getAddressAsString()) );
            xhr.onload = function () {
            	// parse result string to json object
                results = JSON.parse(xhr.response);

            	if (0 < results.length){
                    self.setLatitude(parseFloat(results[0].lat));
                    self.setLongitude(parseFloat(results[0].lon));
                    // return values to callback method
                    callback(self);
				} else {
            		alert('Given address can\'t be localized.');
				}
            };
            xhr.onerror = function () {
                alert('Localization service not available, check internet connection.');
            };
            xhr.send();
		}
	};


	// set location by one method
	this.setAddress = function(street, zip, city, country){
		self.setStreet(street);
		self.setZip(zip);
		self.setCity(city);
		self.setCountry(country);
	};

	// return complete location as comma separated string
	this.getAddressAsString = function(){
		var address = [];
		if(null != self.getStreet())
			address.push(self.getStreet());
		if(null != self.getZip() && null != self.getCity()){
			address.push(trim(self.getZip() +" "+ self.getCity()));
		}else if(null != self.getZip()){
			address.push(self.getZip());
		}else if(null != self.getCity()){
			address.push(self.getCity());
		}
		if(null != self.getCountry())
			address.push(self.getCountry());

		// return array as string
		return address.join(", ");
	};

	// set coordinates by one call
	this.setCoordinates = function(lat, lng){
		self.lat = latitude;
		self.lng = longitude;
	};

}