"use strict";

import $ from "jquery";
import ParamEnum from "~/enum/ParamEnum";
import dataForJs from "~/function/dataForJs";
import Routing from "~/module/Routing";
import { onClickCopyToClipboard } from "~/function/copyTextToClipboard";
import { preventDoubleClick } from "~/function/preventDoubleClick";

import "@fancyapps/fancybox/dist/jquery.fancybox";
import "@fancyapps/fancybox/dist/jquery.fancybox.css";
import Map from "~/module/leaflet/map";

$(".js__listingShowContactDetails").on("click", function (event) {
    if (!preventDoubleClick(this, event)) {
        return;
    }

    let $button = $(this);
    let listingId = $(this).data("listing-id");
    let params = {};
    let showListingPreviewForOwner = dataForJs[ParamEnum.SHOW_LISTING_PREVIEW_FOR_OWNER];
    if (showListingPreviewForOwner !== undefined) {
        params[ParamEnum.SHOW_LISTING_PREVIEW_FOR_OWNER] = dataForJs[ParamEnum.SHOW_LISTING_PREVIEW_FOR_OWNER];
    }

    let contactDataRequest = {};
    contactDataRequest[ParamEnum.LISTING_ID] = listingId;
    contactDataRequest[ParamEnum.SHOW_LISTING_PREVIEW_FOR_OWNER] = dataForJs[ParamEnum.SHOW_LISTING_PREVIEW_FOR_OWNER];
    $.post(Routing.generate("app_listing_contact_data", params), contactDataRequest)
        .done(function (response) {
            if (true !== response.success) {
                return;
            }

            $(".js__listingContactData").remove();

            $button.before(response[ParamEnum.SHOW_CONTACT_HTML]);
            $button.hide();
        })
        .fail(function () {
            $(".js__listingContactData").remove();
            $button.show();
        });
});

onClickCopyToClipboard();

$('[data-fancybox="file"]').fancybox({
    buttons: ["zoom", "thumbs", "close"],
    hash: false,
});

$(".js__showListingOnMap").on("click", function () {
    let $button = $(this);
    $(".js__listingOnMap").show();
    $(".js__mapButtonWrapper").hide();

    let map = new Map();
    map.mapCenterLatLng = new L.LatLng(
        dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LATITUDE],
        dataForJs[ParamEnum.MAP_LOCATION_COORDINATES][ParamEnum.LONGITUDE]
    );
    map.mapZoom = 12;
    map.createLeafletMap(document.querySelector(".js__listingOnMap"));
    map.addMarker(map.mapCenterLatLng);

    $button.parents(".card.d-print-none").removeClass("d-print-none");
});
