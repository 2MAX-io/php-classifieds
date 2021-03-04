"use strict";

import L from "leaflet";
import mapMarkerIcon from "~/img/map/map-marker-icon.svg";

let markerIcon = L.icon({
    iconUrl: mapMarkerIcon,
    iconSize: [22, 22],
    iconAnchor: [11, 11],
    popupAnchor: [0, -11],
    shadowSize: false,
    shadowAnchor: false,
});

export default markerIcon;
