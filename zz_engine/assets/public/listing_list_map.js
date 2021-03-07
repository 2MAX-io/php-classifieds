"use strict";

import dataForJs from "~/function/dataForJs";
import ParamEnum from "~/enum/ParamEnum";
import Routing from "~/module/Routing";
import Map from "~/module/leaflet/map";
import "~/module/leaflet/leaflet";
// noinspection ES6CheckImport
import {
    PruneCluster,
    PruneClusterForLeaflet,
} from "exports-loader?exports=PruneCluster,PruneClusterForLeaflet!prunecluster/dist/PruneCluster.js";
import "prunecluster/dist/LeafletStyleSheet.css";
import markerIcon from "~/module/leaflet/leaflet_marker_icon";

let map = new Map();
let mapElement = document.querySelector(".js__mapWithListings");
if (mapElement) {
    let leafletMap = map.createLeafletMap(mapElement);
    leafletMap.on("moveend", function () {
        let url = new URL(window.location);
        let lat = leafletMap.getCenter().lat;
        let lng = leafletMap.getCenter().lng;
        let zoom = leafletMap.getZoom();

        url.searchParams.set(ParamEnum.LATITUDE, lat);
        url.searchParams.set(ParamEnum.LONGITUDE, lng);
        url.searchParams.set(ParamEnum.ZOOM, zoom);
        window.history.pushState(null, null, url.toString());

        document.querySelectorAll(".js__linkToMap").forEach((linkElement) => {
            let linkUrl = new URL(linkElement.getAttribute("href"), window.location);
            linkUrl.searchParams.set(ParamEnum.LATITUDE, lat);
            linkUrl.searchParams.set(ParamEnum.LONGITUDE, lng);
            linkUrl.searchParams.set(ParamEnum.ZOOM, zoom);
            linkElement.setAttribute("href", linkUrl.toString());
        });

        document.querySelector(".js__filterLatitude").value = lat;
        document.querySelector(".js__filterLongitude").value = lng;
        document.querySelector(".js__filterZoom").value = zoom;
    });

    var pruneCluster = new PruneClusterForLeaflet();
    pruneCluster.PrepareLeafletMarker = function (leafletMarker, data) {
        leafletMarker.setIcon(markerIcon);
        let listingOnMap = data.listingOnMap;
        let url = Routing.generate("app_listing_show", {
            id: listingOnMap.listingId,
            slug: listingOnMap.listingSlug,
        });
        let popupContent = `<a href='${url}'>${listingOnMap.listingTitle}</a>`;
        if (leafletMarker.getPopup()) {
            leafletMarker.setPopupContent(popupContent);
        } else {
            leafletMarker.bindPopup(popupContent);
        }
    };

    dataForJs[ParamEnum.LISTING_LIST].forEach((listingOnMap) => {
        var marker = new PruneCluster.Marker(listingOnMap.latitude, listingOnMap.longitude);
        /**
         * @typedef {Object} listingOnMap
         * @property {string} listingSlug
         * @property {string} listingTitle
         */
        marker.data.listingOnMap = listingOnMap;
        pruneCluster.RegisterMarker(marker);
    });

    leafletMap.addLayer(pruneCluster);
}
