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
    var showPreviewForOwner = app.getJsonDataCached()['showPreviewForOwner'];
    if (showPreviewForOwner !== undefined) {
        params.showPreviewForOwner = app.getJsonDataCached()['showPreviewForOwner'];
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
