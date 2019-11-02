"use strict";

var showPreviewForOwner = app.getJsonDataCached()['showPreviewForOwner'];

$('[data-fancybox="file"]').fancybox({
    buttons: [
        "zoom",
        "thumbs",
        "close"
    ],
    hash: false
});

$('.onClickShowContactInformation').on('click', function (event) {
    if (!preventDoubleClick(this, event)) {
        return;
    }

    var $button = $(this);
    var listingId = $(this).data('listing-id');
    var params = {};
    if (showPreviewForOwner !== '') {
        params.showPreviewForOwner = 1;
    }

    $.post(
        Routing.generate('app_listing_contact_data', params),
        {
            listingId: listingId
        }
    ).done(function (response) {
        $('.listing-contact-data').remove();

        $button.before(response.html);
        $button.remove();
    });
});

var $topImage = $('.listing-show-top-image');
if ($topImage.prop('naturalWidth') < 150 || $topImage.prop('naturalHeight') < 120) {
    $topImage.parents('.zoom-wrapper').find('.zoom').remove();
}