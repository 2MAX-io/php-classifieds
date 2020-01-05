"use strict";

new Cleave('.input-phone', {
    phone: true,
    phoneRegionCode: app.getJsonDataCached()['cleaveConfig']['languageTwoLetters']
});
new Cleave('.input-money', {
    numeral: true,
    numeralThousandsGroupStyle: 'thousand',
    numeralPositiveOnly: true,
    delimiter: app.getJsonDataCached()['cleaveConfig']['delimiter'],
    numeralDecimalMark: app.getJsonDataCached()['cleaveConfig']['numeralDecimalMark']
});
// new Cleave('.input-number', {
//     numeral: true,
//     numeralThousandsGroupStyle: 'thousand',
//     numeralPositiveOnly: true,
//     delimiter: ' ',
//     numeralDecimalMark: app.getJsonDataCached()['cleaveConfig']['numeralDecimalMark']
// });

autosize($('.textarea-autosize'));

var isUploadFinished = function () {
    var chosenFiles = $.fileuploader.getInstance('#listing_file').getChoosedFiles();
    var noFilesUploaded = chosenFiles[0] === undefined;
    if (noFilesUploaded) {
        return true;
    }

    return chosenFiles[0].uploaded;
};

var onListingSaveFormSubmit = function (button, event) {
    if (!preventDoubleClick(button, event)) {
        return;
    }
    if ($(button).data('force-submit')) {
        return;
    }

    if (isUploadFinished()) {
        return;
    }

    event.preventDefault();

    window.setTimeout(function () {
        if (isUploadFinished()) {
            $(button).click();
        } else {
            $.fancyConfirm({
                message: Translator.trans('trans.Some files are still uploading, are you sure you want to submit form now?'),
                okButton: Translator.trans('trans.Yes'),
                noButton: Translator.trans('trans.Cancel'),
                callback: function(confirmed) {
                    if (confirmed) {
                        $(button).data('force-submit', true);
                        $(button).click();
                    }
                }
            });
        }
    }, 1000);
};
