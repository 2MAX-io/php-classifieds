"use strict";

app.listingEditMap = {
    map: null,
    currentLocationMarker: null,
};

app.listingEditMap.init = function () {
    var displayMapOnInit = document.querySelector('.js__listing-edit-map-enabled');
    if (displayMapOnInit) {
        app.listingEditMap.displayMap();
    }

    $('.js__add-location-to-the-map-button').on('click', function () {
        app.listingEditMap.displayMap();
    });
};

app.listingEditMap.displayMap = function () {
    $('.js__listing-edit-location-map').show();
    $('.js__add-location-to-the-map-button').hide();

    var mapElement = document.querySelector('.js__listing-edit-location-map');
    app.listingEditMap.map = L.map(mapElement)
        .setView(app.listingEditMap.getDefaultLatLng(), app.getDataForJs()[app.ParamEnum.MAP_DEFAULT_ZOOM]);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(app.listingEditMap.map);

    L.control.locate({
        position: 'topright',
        drawMarker: false,
        drawCircle: false,
        returnToPrevBounds: true,
        onLocationError: () => {
            alert(Translator.trans('trans.Your web browser denied access to geolocation'));
        }
    }).addTo(app.listingEditMap.map);

    app.listingEditMap.map.on('click', app.listingEditMap.onMapClick);
    app.listingEditMap.addMarkerWithCurrentLocation();
};

app.listingEditMap.onMapClick = function (event) {
    app.listingEditMap.addMarker(event.latlng);
};

app.listingEditMap.addMarker = function (latLng) {
    app.listingEditMap.allowChangeOfMarkerLocation();

    var marker = new L.marker(latLng);
    app.listingEditMap.onMarkerClickRemoveMarker(marker);
    marker.addTo(app.listingEditMap.map);
    app.listingEditMap.currentLocationMarker = marker;
    app.listingEditMap.updateFormWithLocationCoordinates(latLng);
};

app.listingEditMap.allowChangeOfMarkerLocation = function () {
    if (app.listingEditMap.currentLocationMarker !== null) {
        app.listingEditMap.map.removeLayer(app.listingEditMap.currentLocationMarker);
        app.listingEditMap.currentLocationMarker = null;
    }
};

app.listingEditMap.onMarkerClickRemoveMarker = function (marker) {
    marker.on('click', function () {
        app.listingEditMap.map.removeLayer(marker);
        app.listingEditMap.updateFormWithLocationCoordinates(null);
    });
};

app.listingEditMap.addMarkerWithCurrentLocation = function () {
    if (!app.listingEditMap.haveLocationCoordinates()) {
        return;
    }

    var latitude = app.getDataForJs()[app.ParamEnum.MAP_LOCATION_COORDINATES][app.ParamEnum.LATITUDE];
    var longitude = app.getDataForJs()[app.ParamEnum.MAP_LOCATION_COORDINATES][app.ParamEnum.LONGITUDE];
    app.listingEditMap.currentLocationMarker = new L.marker(new L.LatLng(latitude, longitude)).addTo(app.listingEditMap.map);
};

app.listingEditMap.getDefaultLatLng = function () {
    if (!app.listingEditMap.haveLocationCoordinates()) {
        return new L.LatLng(app.getDataForJs()[app.ParamEnum.MAP_DEFAULT_LATITUDE], app.getDataForJs()[app.ParamEnum.MAP_DEFAULT_LONGITUDE]);
    }

    var latitude = app.getDataForJs()[app.ParamEnum.MAP_LOCATION_COORDINATES][app.ParamEnum.LATITUDE];
    var longitude = app.getDataForJs()[app.ParamEnum.MAP_LOCATION_COORDINATES][app.ParamEnum.LONGITUDE];

    return new L.LatLng(latitude, longitude);
};

app.listingEditMap.haveLocationCoordinates = function () {
    return app.getDataForJs()[app.ParamEnum.MAP_LOCATION_COORDINATES]
    && app.getDataForJs()[app.ParamEnum.MAP_LOCATION_COORDINATES][app.ParamEnum.LONGITUDE] !== null
    && app.getDataForJs()[app.ParamEnum.MAP_LOCATION_COORDINATES][app.ParamEnum.LATITUDE] !== null;
};

app.listingEditMap.updateFormWithLocationCoordinates = function (latLng) {
    var $formLatitude = $('.js__location-latitude');
    var $formLongitude = $('.js__location-longitude');

    if (latLng === null) {
        $formLatitude.val(null);
        $formLongitude.val(null);

        return;
    }

    $formLatitude.val(latLng.lat);
    $formLongitude.val(latLng.lng);
};

app.listingEditMap.init();
