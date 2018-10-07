/*
 * Handles geocoding and displays google maps with given locations
 *
 * @package				class.geocoding
 * @version				2.0: geocoding,  03-2018
 * @copyright 			(c)2018 Christian Brinkert
 * @author 				Christian Brinkert <christian.brinkert@googlemail.com>
 */



function Geocoding() {

    var self = this;
    var gmap = null;
    var osmmap = null;
    var osmmapPreview = null;
    var mapboxmap = null;
    var mapboxAccessToken = null;
    var mapboxMarker = null;
    var mapboxPopup = null;


    /**
     * Try to find coordinates by given address
     * @param uid int
     * @param mapProvider string
     * @param mapboxAccesstoken string
     * @return void
     */
    this.doGeocoding = function (uid, mapProvider, mapboxAccesstoken) {
        // verify existing gmap instance
        if (!this.gmap) {
            this.gmap = new Gmaps();
        }
        // set uid to the current gmap instance
        this.gmap.setUid(uid);

        // set address to gmap object
        this.getAddressFromBackend(this.gmap);

        // try to fetch coordinates by address, set callback method
        this.gmap.fetchCoordinatesByAddress(this.resultsHandling, mapProvider, mapboxAccesstoken);

    };



    /**
     * Result handle to check if gecoding was succesfull and display results in backend form
     * @param gmap Gmaps
     * @return void
     */
    this.resultsHandling = function (gmap) {
        // work with given results
        if (gmap && typeof(gmap.getLatitude()) === 'number' && typeof(gmap.getLongitude()) === 'number') {
            // set new coordinates to the backend fields
            self.setCoordinatesToBackend(gmap);

        } else {
            alert("Die angegebene Adresse konnte nicht lokalisiert werden.");
        }
    };



    /*
     * Display results in a new windows with coordinates and google maps preview
     * @param uid int
     * @param mapProvider string
     * @param mapboxAccesstoken string
     * @return void
     */
    this.displayLocation = function (uid, mapProvider, mapboxAccesstoken) {

        // verify existing gmap instance
        if (!this.gmap) {
            this.gmap = new Gmaps();
        }
        // set uid to the current gmap instance
        this.gmap.setUid(uid);


        // set address to gmap object
        this.getAddressFromBackend(this.gmap);
        this.getCoordinatesFromBackend(this.gmap);

        // check if coordinates are given
        if (typeof this.gmap.getLatitude() === 'number' && typeof this.gmap.getLongitude() === 'number') {

            // set attributes to the map
            document.getElementById('cbgm_previewLocation').setAttribute("style", "width:530px; height:300px; border:1px solid #8E8E8E; ", false);

            if ('Google' === mapProvider) {

                // create google maps LatLng object
                var latlng = new google.maps.LatLng(this.gmap.getLatitude(), this.gmap.getLongitude());
                // set options object
                var myOptions = {
                    zoom: 15,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                // create map
                var map = new google.maps.Map(document.getElementById("cbgm_previewLocation"), myOptions);
                // create map marker
                var marker = new google.maps.Marker({
                    position: latlng,
                    draggable: true,
                    title: ""
                });
                // add listener
                google.maps.event.addListener(marker, 'dragend', function () {

                    // set new coordinates from marker to gmap instance
                    self.gmap.setLatitude( marker.getPosition().lat() );
                    self.gmap.setLongitude( marker.getPosition().lng() );

                    // update coordinates to the backend fields
                    self.setCoordinatesToBackend( self.gmap );

                });

                // assign marker to the map
                marker.setMap(map);


            } else if ('MapBox' === mapProvider) {
                // display map by mapbox gl
                mapboxgl.accessToken = mapboxAccesstoken;

                this.mapboxmap = new mapboxgl.Map({
                    container: 'cbgm_previewLocation',
                    center: [this.gmap.getLongitude(),this.gmap.getLatitude()],    // longitude and latitude switched!
                    minZoomnumber: 0,
                    maxZoomnumber: 24,
                    zoom:15,
                    style: 'mapbox://styles/mapbox/streets-v10'
                }).addControl(new mapboxgl.NavigationControl());

                this.mapboxMarker = new mapboxgl.Marker()
                    .setLngLat([this.gmap.getLongitude(),this.gmap.getLatitude()]) // longitude and latitude switched!
                    .addTo(this.mapboxmap)
                    .setDraggable(true)
                    .on('dragend', function(e) {

                        // set new coordinates from marker to gmap instance
                        self.gmap.setLatitude( e.target._lngLat.lat );
                        self.gmap.setLongitude( e.target._lngLat.lng );

                        // update coordinates to the backend fields
                        self.setCoordinatesToBackend( self.gmap );
                    });

            } else {
                // display map by openstreetmap
                if (this.osmmap) {
                    // if map already exists, clear it
                    this.osmmap.off();
                    this.osmmap.remove();
                }

                // show openstreetmap map
                this.osmmap = L.map('cbgm_previewLocation', {
                    center: [this.gmap.getLatitude(), this.gmap.getLongitude()],
                    zoom: 15,
                    scrollWheelZoom: true
                });

                // add osm layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors, '
                    + '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'
                }).addTo(this.osmmap);

                // add marker
                L.marker([this.gmap.getLatitude(), this.gmap.getLongitude()],{draggable:true})
                    .addTo(this.osmmap)
                    .on('dragend', function(e) {

                        // set new coordinates from marker to gmap instance
                        self.gmap.setLatitude( e.target._latlng.lat );
                        self.gmap.setLongitude( e.target._latlng.lng );

                        // update coordinates to the backend fields
                        self.setCoordinatesToBackend( self.gmap );
                    });
            }
        } else {
            alert("No coordinates or location given.");
        }
    };



    /*
     * Display results in a new windows with coordinates and google maps preview
     * @param uid int
     * @param defaultZoom int
     * @param defaultMapType string
     * @param mapProvider string
     * @param mapboxAccesstoken string
     */
    this.displayPreview = function(uid, defaultZoom, defaultMapType, defaultMapControl, mapProvider, mapboxAccesstoken) {

        // verify existing gmap instance
        if (!this.gmap) {
            this.gmap = new Gmaps();
        }
        // set uid to the current gmap instance
        this.gmap.setUid(uid);
        // get coordinates
        this.getCoordinatesFromBackend(this.gmap);

        var infoText = '';
        var mapZoom = 10;
        var mapType = '';
        var mapControl = '';

        // fetch current inputs
        var fieldPrefix1 = "data[tt_content][" + this.gmap.getUid() + "][pi_flexform][data][sDEF][lDEF]";
        var fieldPrefix2 = "data[tt_content][" + this.gmap.getUid() + "][pi_flexform][data][s_displayproperties][lDEF]";

        // TYPO3 <= 6.2
        if (document.getElementsByName(fieldPrefix1 + "[settings.cbgmZip][vDEF]_hr")[0]) {

            infoText = document.getElementsByName(fieldPrefix2 + "[settings.cbgmDescription][vDEF]")[0].value;
            mapZoom = parseInt(document.getElementsByName(fieldPrefix2 + "[settings.cbgmScaleLevel][vDEF]_hr")[0].value, 10);
            mapType = document.getElementsByName(fieldPrefix2 + "[settings.cbgmMapType][vDEF]")[0].value;
            mapControl = document.getElementsByName(fieldPrefix2 + "[settings.cbgmNavigationControl][vDEF]")[0].value;
        }
        // TYPO3 >= 7.x
        else if (TYPO3.jQuery
            && TYPO3.jQuery("input[data-formengine-input-name*=\'" + fieldPrefix1 + "[settings.cbgmStreet][vDEF]\']")) {

            infoText = TYPO3.jQuery("textarea[name*=\'" + fieldPrefix2 + "[settings.cbgmDescription][vDEF]\']").val();
            mapZoom = parseInt(TYPO3.jQuery("input[data-formengine-input-name*=\'" + fieldPrefix2 + "[settings.cbgmScaleLevel][vDEF]\']").val(), 10);
            mapType = TYPO3.jQuery("select[name*=\'" + fieldPrefix2 + "[settings.cbgmMapType][vDEF]\']").val();
            mapControl = TYPO3.jQuery("select[name*=\'" + fieldPrefix2 + "[settings.cbgmNavigationControl][vDEF]\']").val();
        }


        if (isNaN(mapZoom)) mapZoom = parseInt(defaultZoom);
        if ('' === mapType) mapType = defaultMapType;
        if ('' === mapControl) mapControl = defaultMapControl;

        // split infoText into rows
        var rowsInfoText = infoText.split("\n");

        // check if coordinates are given
        if (typeof(this.gmap.getLatitude()) === 'number' && typeof(this.gmap.getLongitude()) === 'number') {

            // set attributes to the map
            document.getElementById('cbgm_previewMap').setAttribute("style", "width:530px; height:300px; border:1px solid #8E8E8E; ", false);

            if ('Google' === mapProvider){
                // create map by google maps

                // create google maps LatLng object
                var latlng = new google.maps.LatLng(this.gmap.getLatitude(), this.gmap.getLongitude());
                // set options object
                var myOptions = {
                    "zoom": mapZoom,
                    "center": latlng,
                    "navigationControl": true
                };

                // specifiy map type
                switch (mapType) {
                    case "ROADMAP":
                        myOptions.mapTypeId = google.maps.MapTypeId.ROADMAP;
                        break;
                    case "TERRAIN":
                        myOptions.mapTypeId = google.maps.MapTypeId.TERRAIN;
                        break;
                    case "SATELLITE":
                        myOptions.mapTypeId = google.maps.MapTypeId.SATELLITE;
                        break;
                    default:
                        myOptions.mapTypeId = google.maps.MapTypeId.HYBRID;
                }

                // specifiy navigation control
                switch (mapControl) {
                    case "SMALL":
                        myOptions.navigationControlOptions = {style: google.maps.NavigationControlStyle.SMALL};
                        break;
                    case "ZOOM_PAN":
                        myOptions.navigationControlOptions = {style: google.maps.NavigationControlStyle.ZOOM_PAN};
                        break;
                    case "ANDROID":
                        myOptions.navigationControlOptions = {style: google.maps.NavigationControlStyle.ANDROID};
                        break;
                    default:
                        myOptions.navigationControlOptions = {style: google.maps.NavigationControlStyle.DEFAULT};
                }

                // create map
                var map = new google.maps.Map(document.getElementById("cbgm_previewMap"), myOptions);
                // create map marker
                var marker = new google.maps.Marker({
                    position: latlng,
                    title: rowsInfoText[0]
                });

                // set info window
                if ('' !== infoText) {
                    infoText = infoText.replace(/\n/g, "<br>");

                    var infowindow = new google.maps.InfoWindow({content: infoText});
                    // add listener
                    google.maps.event.addListener(marker, 'click', function () {
                        infowindow.open(map, marker);
                    });
                }

                // assign marker to the map
                marker.setMap(map);


            } else if ('MapBox' === mapProvider) {
                // get map styling
                switch (mapType) {
                    case "MapBox-BASIC":
                        mapType = 'mapbox://styles/mapbox/basic-v9';
                        break;
                    case "MapBox-BRIGHT":
                        mapType = 'mapbox://styles/mapbox/bright-v9';
                        break;
                    case "MapBox-LIGHT":
                        mapType = 'mapbox://styles/mapbox/light-v9';
                        break;
                    case "MapBox-DARK":
                        mapType = 'mapbox://styles/mapbox/dark-v9';
                        break;
                    case "MapBox-SATELLITE":
                        mapType = 'mapbox://styles/mapbox/satellite-v9';
                        break;
                    default:
                        mapType = 'mapbox://styles/mapbox/streets-v9';
                }

                // create map by mapboy
                mapboxgl.accessToken = mapboxAccesstoken;

                this.mapboxmap = new mapboxgl.Map({
                    container: 'cbgm_previewMap',
                    center: [this.gmap.getLongitude(), this.gmap.getLatitude()],       // longitude and latitude switched!
                    minZoomnumber: 0,
                    maxZoomnumber: 24,
                    zoom:mapZoom,
                    style: mapType
                }).addControl(new mapboxgl.NavigationControl());

                // add infotext popup if given
                if ('' !== infoText) {
                    this.mapboxPopup = new mapboxgl.Popup({closeOnClick: true, closeButton: false})
                        .setLngLat([this.gmap.getLongitude(), this.gmap.getLatitude()])
                        .setHTML(infoText.replace(/\+/g, ' ').replace(/\n/g, '<br/>'));
                }

                // add marker to the position
                this.mapboxMarker = new mapboxgl.Marker()
                    .setLngLat([this.gmap.getLongitude(), this.gmap.getLatitude()])    // longitude and latitude switched!
                    .addTo(this.mapboxmap)
                    .setPopup(this.mapboxPopup);


            } else {
                // create map by openstreetmap
                if (this.osmmapPreview) {
                    this.osmmapPreview.off();
                    this.osmmapPreview.remove();
                }

                // show openstreetmap map
                this.osmmapPreview = L.map('cbgm_previewMap', {
                    center: [this.gmap.getLatitude(), this.gmap.getLongitude()],
                    zoom: mapZoom,
                    scrollWheelZoom: true
                });

                // add osm layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors, '
                    + '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'
                }).addTo(this.osmmapPreview);

                // add marker
                osmMarker = L.marker([this.gmap.getLatitude(), this.gmap.getLongitude()]).addTo(this.osmmapPreview);

                // add infotext
                if (infoText){
                    osmMarker.bindPopup( infoText.replace(/\+/g, ' ').replace(/\n/g, '<br/>') );
                }

            }

        } else {
            alert("No coordinates or location given.");
        }
    };



    /**
     * Get coordinates from TYPO3 backend fields and store them to local properties
     * @param  gmap Gmaps
     * @return void
     */
    this.getAddressFromBackend = function (gmap){

        // fetch current inputs
        var fieldPrefix = "data[tt_content][" + gmap.getUid() + "][pi_flexform][data][sDEF][lDEF]";

        // TYPO3 <= 6.2
        if (document.getElementsByName(fieldPrefix + "[settings.cbgmZip][vDEF]_hr")[0]) {

            // set address to gmap instance, street, zip, city and country
            gmap.setAddress(
                document.getElementsByName(fieldPrefix + "[settings.cbgmStreet][vDEF]_hr")[0].value,
                document.getElementsByName(fieldPrefix + "[settings.cbgmZip][vDEF]_hr")[0].value,
                document.getElementsByName(fieldPrefix + "[settings.cbgmCity][vDEF]_hr")[0].value,
                document.getElementsByName(fieldPrefix + "[settings.cbgmCountry][vDEF]_hr")[0].value
            );
        }
        // TYPO3 >= 7.x
        else if (TYPO3.jQuery
            && TYPO3.jQuery("input[data-formengine-input-name*=\'" + fieldPrefix + "[settings.cbgmStreet][vDEF]\']")) {

            // set address to gmap instance, street, zip, city and country
            gmap.setAddress(
                TYPO3.jQuery("input[data-formengine-input-name*=\'" + fieldPrefix + "[settings.cbgmStreet][vDEF]\']").val(),
                TYPO3.jQuery("input[data-formengine-input-name*=\'" + fieldPrefix + "[settings.cbgmZip][vDEF]\']").val(),
                TYPO3.jQuery("input[data-formengine-input-name*=\'" + fieldPrefix + "[settings.cbgmCity][vDEF]\']").val(),
                TYPO3.jQuery("input[data-formengine-input-name*=\'" + fieldPrefix + "[settings.cbgmCountry][vDEF]\']").val()
            );
        }

    };



    /**
     * Set given coordinates to the backend fields
     * @param gmap     Gmaps
     * @return void
     */
    this.setCoordinatesToBackend = function (gmap){

        // write results to backend form
        var fieldPrefix = "data[tt_content][" + gmap.getUid() + "][pi_flexform][data][sDEF][lDEF]";

        // TYPO3 <= 6.2
        if (document.getElementsByName(fieldPrefix + "[settings.cbgmLatitude][vDEF]_hr")[0]) {

            document.getElementsByName(fieldPrefix + "[settings.cbgmLatitude][vDEF]_hr")[0].value = self.gmap.getLatitude();
            document.getElementsByName(fieldPrefix + "[settings.cbgmLatitude][vDEF]")[0].value = self.gmap.getLatitude();
            document.getElementsByName(fieldPrefix + "[settings.cbgmLongitude][vDEF]_hr")[0].value = self.gmap.getLongitude();
            document.getElementsByName(fieldPrefix + "[settings.cbgmLongitude][vDEF]")[0].value = self.gmap.getLongitude();
        }
        // TYPO3 >= 7.x
        else if (TYPO3.jQuery
            && TYPO3.jQuery("input[data-formengine-input-name*=\'" + fieldPrefix + "[settings.cbgmLatitude][vDEF]\']")) {

            TYPO3.jQuery("input[data-formengine-input-name*=\'" + fieldPrefix + "[settings.cbgmLatitude][vDEF]\']").val(self.gmap.getLatitude());
            TYPO3.jQuery("input[name*=\'" + fieldPrefix + "[settings.cbgmLatitude][vDEF]\']").val(self.gmap.getLatitude());
            TYPO3.jQuery("input[data-formengine-input-name*=\'" + fieldPrefix + "[settings.cbgmLongitude][vDEF]\']").val(self.gmap.getLongitude());
            TYPO3.jQuery("input[name*=\'" + fieldPrefix + "[settings.cbgmLongitude][vDEF]\']").val(self.gmap.getLongitude());
        }
    };



    /**
     * Set coordinates from backend to the gmap instance
     * @param gmap Gmaps
     * @return void
     */
    this.getCoordinatesFromBackend = function(gmap){

        var fieldPrefix = "data[tt_content][" + gmap.getUid() + "][pi_flexform][data][sDEF][lDEF]";

        // TYPO3 <= 6.2
        if (document.getElementsByName(fieldPrefix + "[settings.cbgmLatitude][vDEF]_hr")[0]) {
            gmap.setLatitude( parseFloat(document.getElementsByName(fieldPrefix + "[settings.cbgmLatitude][vDEF]_hr")[0].value) );
            gmap.setLongitude( parseFloat(document.getElementsByName(fieldPrefix + "[settings.cbgmLongitude][vDEF]_hr")[0].value) );
        }
        // TYPO3 >= 7.x
        else if (TYPO3.jQuery
            && TYPO3.jQuery("input[data-formengine-input-name*=\'" + fieldPrefix + "[settings.cbgmLatitude][vDEF]\']")) {

            gmap.setLatitude( parseFloat(
                TYPO3.jQuery("input[data-formengine-input-name*=\'" + fieldPrefix + "[settings.cbgmLatitude][vDEF]\']").val()
            ));
            gmap.setLongitude( parseFloat(
                TYPO3.jQuery("input[data-formengine-input-name*=\'" + fieldPrefix + "[settings.cbgmLongitude][vDEF]\']").val()
            ));
        }
    };

}


var cbGooglemaps = new Geocoding();

