"use strict";

var app = {
    jsonDataCache: null,
};

app.getJsonDataCached = function () {
    if (app.jsonDataCache === null) {
        app.jsonDataCache = app.getJsonData();
    }

    return app.jsonDataCache;
};

app.getJsonData = function () {
    var $jsonData = $('#js__jsonData');

    if ($jsonData.length < 1) {
        return [];
    }

    return JSON.parse($jsonData[0].textContent);
};

app.debugLog = function (message) {
    console.log(message);
};

function copyTextToClipboard(text) {
    var textArea = document.createElement("textarea");

    //
    // *** This styling is an extra step which is likely not required. ***
    //
    // Why is it here? To ensure:
    // 1. the element is able to have focus and selection.
    // 2. if element was to flash render it has minimal visual impact.
    // 3. less flakyness with selection and copying which **might** occur if
    //    the textarea element is not visible.
    //
    // The likelihood is the element won't even render, not even a flash,
    // so some of these are just precautions. However in IE the element
    // is visible whilst the popup box asking the user for permission for
    // the web page to copy to the clipboard.
    //

    // Place in top-left corner of screen regardless of scroll position.
    textArea.style.position = 'fixed';
    textArea.style.top = '0';
    textArea.style.left = '0';

    // Ensure it has a small width and height. Setting to 1px / 1em
    // doesn't work as this gives a negative w/h on some browsers.
    textArea.style.width = '2em';
    textArea.style.height = '2em';

    // We don't need padding, reducing the size if it does flash render.
    textArea.style.padding = '0';

    // Clean up any borders.
    textArea.style.border = 'none';
    textArea.style.outline = 'none';
    textArea.style.boxShadow = 'none';

    // Avoid flash of white box if rendered for any reason.
    textArea.style.background = 'transparent';


    textArea.value = text;

    document.body.appendChild(textArea);

    textArea.select();

    try {
        document.execCommand('copy');
    } catch (err) {
        console.log('unable to copy to clipboard');
    }

    document.body.removeChild(textArea);
}

$.fancyConfirm = function(opts) {
    opts = $.extend(
        true,
        {
            title: Translator.trans('trans.Are you sure?'),
            message: "",
            okButton: Translator.trans('trans.OK'),
            noButton: Translator.trans('trans.Cancel'),
            callback: $.noop
        },
        opts || {}
    );

    $.fancybox.open({
        type: "html",
        src:
            '<div class="fc-content p-5 rounded">' +
            '<h2 class="mb-3">' +
            opts.title +
            "</h2>" +
            "<p>" +
            opts.message +
            "</p>" +
            '<p class="text-right">' +
            '<a data-value="0" data-fancybox-close href="javascript:;" class="mr-2">' +
            opts.noButton +
            "</a>" +
            '<button data-value="1" data-fancybox-close class="btn btn-primary">' +
            opts.okButton +
            "</button>" +
            "</p>" +
            "</div>",
        opts: {
            animationEffect: false,
            modal: true,
            baseTpl:
                '<div class="fancybox-container fc-container" role="dialog" tabindex="-1">' +
                '<div class="fancybox-bg"></div>' +
                '<div class="fancybox-inner">' +
                '<div class="fancybox-stage"></div>' +
                "</div>" +
                "</div>",
            afterClose: function(instance, current, e) {
                var button = e ? e.target || e.currentTarget : null;
                var value = button ? $(button).data("value") : 0;

                opts.callback(value);
            }
        }
    });
};

$('.listingListItemClick').on('click', function () {
    var $listingListItem = $(this);
    window.location.href = $listingListItem.find('.listingListLink').attr('href');
});

function preventDoubleClick(element, event) {
    var $element = $(element);
    var previousClickTimestamp = $element.data('last-click-timestamp') || null;
    $element.data('last-click-timestamp', event.timeStamp);

    if (previousClickTimestamp !== null && event.timeStamp - 1000 < previousClickTimestamp) {
        event.preventDefault();
        return false;
    }

    return true;
}

$('.preventDoubleClick').on('click', function (event) {
    preventDoubleClick(this, event);
});
