"use strict";

import $ from "jquery";
import ParamEnum from "~/enum/ParamEnum";
import dataForJs from "~/function/dataForJs";
import { onClickCopyToClipboard } from "~/function/copyTextToClipboard";

import "@fancyapps/fancybox/dist/jquery.fancybox";
import "@fancyapps/fancybox/dist/jquery.fancybox.css";
import Map from "~/module/leaflet/map";

onClickCopyToClipboard();

$('[data-fancybox="file"]').fancybox({
    buttons: ["zoom", "thumbs", "close"],
    hash: false,
});

document.querySelector(".js__showListingOnMap")?.addEventListener("click", function () {
    let $button = this;
    document.querySelector(".js__listingOnMap").style.display = "block";
    document.querySelector(".js__mapButtonWrapper").style.display = "none";

    let map = new Map();
    map.mapCenterLatLng = new L.LatLng(
        dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LATITUDE],
        dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LONGITUDE]
    );
    map.mapZoom = 12;
    map.createLeafletMap(document.querySelector(".js__listingOnMap"));
    map.addMarker(map.mapCenterLatLng);

    $button.closest(".card.d-print-none").classList.remove("d-print-none");
});
