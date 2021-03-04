"use strict";

import $ from "jquery";
import ParamEnum from "~/enum/ParamEnum";
import dataForJs from "~/function/dataForJs";
import Map from "~/module/leaflet/map";

let listingEditMap = {};

listingEditMap = {
    map: null,
    leafletMap: null,
    latLng: null,
    currentLocationMarker: null,
};

listingEditMap.init = function () {
    let displayMapOnInit = document.querySelector(".js__listingEditMapEnabled");
    if (displayMapOnInit) {
        listingEditMap.displayMap();
    }

    $(".js__addLocationToTheMapButton").on("click", function () {
        listingEditMap.displayMap();
    });
};

listingEditMap.displayMap = function () {
    $(".js__listingEditLocationMap").show();
    $(".js__addLocationToTheMapButton").hide();

    let mapElement = document.querySelector(".js__listingEditLocationMap");
    listingEditMap.map = new Map();
    listingEditMap.leafletMap = listingEditMap.map.createLeafletMap(mapElement);

    listingEditMap.leafletMap.on("click", listingEditMap.onMapClick);
    listingEditMap.addMarkerWithCurrentLocation();
};

listingEditMap.addMarkerWithCurrentLocation = function () {
    if (!listingEditMap.haveLocationCoordinates()) {
        return;
    }

    let latitude = dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LATITUDE];
    let longitude = dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LONGITUDE];
    listingEditMap.latLng = new L.LatLng(latitude, longitude);
    listingEditMap.addMarker(listingEditMap.latLng);
};

listingEditMap.onMapClick = function (event) {
    listingEditMap.addMarker(event.latlng);
    listingEditMap.updateFormWithLocationCoordinates(listingEditMap.latLng);
};

listingEditMap.addMarker = function (latLng) {
    if (listingEditMap.currentLocationMarker !== null) {
        listingEditMap.leafletMap.removeLayer(listingEditMap.currentLocationMarker);
        listingEditMap.currentLocationMarker = null;
    }

    // noinspection JSPotentiallyInvalidConstructorUsage
    listingEditMap.currentLocationMarker = listingEditMap.map.addMarker(latLng);
    listingEditMap.latLng = latLng;
    listingEditMap.onMarkerClickRemoveMarker(listingEditMap.currentLocationMarker);
};

listingEditMap.onMarkerClickRemoveMarker = function (marker) {
    marker.on("click", function () {
        listingEditMap.leafletMap.removeLayer(marker);
        listingEditMap.updateFormWithLocationCoordinates(null);
    });
};

listingEditMap.getMapDefaultLatLng = function () {
    if (!listingEditMap.haveLocationCoordinates()) {
        return new L.LatLng(dataForJs[ParamEnum.MAP_DEFAULT_LATITUDE], dataForJs[ParamEnum.MAP_DEFAULT_LONGITUDE]);
    }

    let latitude = dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LATITUDE];
    let longitude = dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LONGITUDE];

    return new L.LatLng(latitude, longitude);
};

listingEditMap.haveLocationCoordinates = function () {
    return (
        dataForJs[ParamEnum.MAP_LOCATION_COORDINATES] &&
        dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LONGITUDE] !== null &&
        dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LATITUDE] !== null
    );
};

listingEditMap.updateFormWithLocationCoordinates = function (latLng) {
    let $formLatitude = $(".js__locationLatitude");
    let $formLongitude = $(".js__locationLongitude");

    if (latLng === null) {
        $formLatitude.val(null);
        $formLongitude.val(null);

        return;
    }

    $formLatitude.val(latLng.lat);
    $formLongitude.val(latLng.lng);
};

listingEditMap.init();
