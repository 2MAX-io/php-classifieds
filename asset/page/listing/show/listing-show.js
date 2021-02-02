"use strict";

$('[data-fancybox="file"]').fancybox({
    buttons: [
        "zoom",
        "thumbs",
        "close"
    ],
    hash: false
});

$('.js__onClickShowContactInformation').on('click', function (event) {
    if (!preventDoubleClick(this, event)) {
        return;
    }

    var $button = $(this);
    var listingId = $(this).data('listing-id');
    var params = {};
    var showPreviewForOwner = app.getDataForJs()['showPreviewForOwner'];
    if (showPreviewForOwner !== undefined) {
        params.showPreviewForOwner = app.getDataForJs()['showPreviewForOwner'];
    }

    $.post(
        Routing.generate('app_listing_contact_data', params),
        {
            listingId: listingId
        }
    ).done(function (response) {
        if (true !== response.success) {
            return;
        }

        $('.js__listing-contact-data').remove();

        $button.before(response.html);
        $button.hide();
    }).fail(function () {
        $('.js__listing-contact-data').remove();
        $button.show();
    });
});

var $topImage = $('.listing-show-top-image');
if ($topImage.prop('naturalWidth') < 150 || $topImage.prop('naturalHeight') < 120) {
    $topImage.parents('.zoom-wrapper').find('.zoom').remove();
}

$('.js__show-on-map-button').on('click', function () {
    $('.js__listing-show-single-map').show();
    $('.js__show-on-map-button-wrapper').hide();

    var mapLatLng = new L.LatLng(
        app.getDataForJs()[app.ParamEnum.MAP_LOCATION_COORDINATES][app.ParamEnum.LATITUDE],
        app.getDataForJs()[app.ParamEnum.MAP_LOCATION_COORDINATES][app.ParamEnum.LONGITUDE]
    );
    var map = L.map(document.querySelector('.js__listing-show-single-map')).setView(mapLatLng, 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    new L.marker(mapLatLng).addTo(map);
    map.panTo(mapLatLng);
});
