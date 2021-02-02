"use strict";

let mapLatLng = new L.LatLng(
    app.getDataForJs()[app.ParamEnum.MAP_DEFAULT_LATITUDE],
    app.getDataForJs()[app.ParamEnum.MAP_DEFAULT_LONGITUDE]
);
let map = L.map(document.querySelector('.js__map-with-listings'))
    .setView(mapLatLng, app.getDataForJs()[app.ParamEnum.MAP_DEFAULT_ZOOM])
;
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

L.control.locate({
    position: 'topright',
    drawMarker: false,
    drawCircle: false,
    returnToPrevBounds: true,
    locateOptions: {
        maxZoom: 15
    },
    onLocationError: () => {
        alert(Translator.trans('trans.Your web browser denied access to geolocation'));
    }
}).addTo(map);

map.on('moveend', function () {
    var url = new URL(window.location);
    url.searchParams.set(app.ParamEnum.LATITUDE, map.getCenter().lat);
    url.searchParams.set(app.ParamEnum.LONGITUDE, map.getCenter().lng);
    url.searchParams.set(app.ParamEnum.ZOOM, map.getZoom());
    window.history.pushState(null, null, url.toString());
});

app.getDataForJs()[app.ParamEnum.LISTING_LIST].forEach(listingOnMap => {
    let url = Routing.generate('app_listing_show', {id: listingOnMap.listingId, slug: listingOnMap.listingSlug});

    new L.marker(new L.LatLng(listingOnMap.latitude, listingOnMap.longitude))
        .bindPopup(`
            <a href='${url}'>${listingOnMap.listingTitle}</a>
        `)
        .addTo(map)
    ;
});
