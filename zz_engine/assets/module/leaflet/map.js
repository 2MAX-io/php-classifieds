"use strict";

import "~/module/leaflet/leaflet";
import dataForJs from "~/function/dataForJs";
import ParamEnum from "~/enum/ParamEnum";
import Translator from "~/module/Translator";
import markerIcon from "~/module/leaflet/leaflet_marker_icon";

export default class Map {
    mapElement;
    leafletMap;
    mapCenterLatLng;
    mapZoom;

    constructor() {
        this.mapCenterLatLng = this.getDefaultMapCenterLatLng();
        this.mapZoom = dataForJs[ParamEnum.MAP_DEFAULT_ZOOM];
    }

    createLeafletMap(mapElement) {
        this.mapElement = mapElement;
        this.leafletMap = L.map(this.mapElement, {
            tap: false, // ref https://github.com/Leaflet/Leaflet/issues/7255
            zoomControl: false,
            attributionControl: false,
        });
        this.leafletMap.setView(this.mapCenterLatLng, this.mapZoom);

        L.control
            .zoom({
                zoomInTitle: Translator.trans("trans.Zoom in"),
                zoomOutTitle: Translator.trans("trans.Zoom out"),
            })
            .addTo(this.leafletMap);

        L.control
            .locate({
                position: "topright",
                drawMarker: false,
                drawCircle: false,
                returnToPrevBounds: true,
                locateOptions: {
                    maxZoom: 15,
                },
                icon: "svg svg-location",
                iconLoading: "svg svg-spinner svg-pulse",
                strings: {
                    title: Translator.trans("trans.Show me where I am"),
                    metersUnit: Translator.trans("trans.meters"),
                    feetUnit: Translator.trans("trans.feet"),
                    popup: Translator.trans("trans.You are within {distance} {unit} from this point"),
                    outsideMapBoundsMsg: Translator.trans("trans.You seem located outside the boundaries of the map"),
                },
                onLocationError: () => {
                    alert(Translator.trans("trans.Your web browser denied access to geolocation"));
                },
            })
            .addTo(this.leafletMap);

        this.leafletMap.addControl(
            new L.Control.Fullscreen({
                title: {
                    false: Translator.trans("trans.View Fullscreen"),
                    true: Translator.trans("trans.Exit Fullscreen"),
                },
            })
        );

        L.control.attribution({ prefix: false }).addTo(this.leafletMap);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(this.leafletMap);

        return this.leafletMap;
    }

    addMarker(latLng) {
        let marker = new L.marker(latLng, { icon: markerIcon });
        marker.addTo(this.leafletMap);

        return marker;
    }

    getDefaultMapCenterLatLng() {
        if (!this.haveLocationCoordinates()) {
            return new L.LatLng(dataForJs[ParamEnum.MAP_DEFAULT_LATITUDE], dataForJs[ParamEnum.MAP_DEFAULT_LONGITUDE]);
        }

        let latitude = dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LATITUDE];
        let longitude = dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LONGITUDE];

        return new L.LatLng(latitude, longitude);
    }

    haveLocationCoordinates() {
        return (
            dataForJs[ParamEnum.MAP_LOCATION_COORDINATES] &&
            dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LONGITUDE] !== null &&
            dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LATITUDE] !== null
        );
    }
}
