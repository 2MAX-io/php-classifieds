"use strict";

new Cleave('.input-phone', {
    phone: true,
    phoneRegionCode: app.getDataForJs()['cleaveConfig']['languageTwoLetters']
});
new Cleave('.input-money', {
    numeral: true,
    numeralThousandsGroupStyle: 'thousand',
    numeralPositiveOnly: true,
    delimiter: app.getDataForJs()['cleaveConfig']['delimiter'],
    numeralDecimalMark: app.getDataForJs()['cleaveConfig']['numeralDecimalMark']
});

var isUploadFinished = function () {
    var chosenFiles = $.fileuploader.getInstance('#listing_file').getChoosedFiles();
    var noFilesUploaded = chosenFiles[0] === undefined;
    if (noFilesUploaded) {
        return true;
    }

    return chosenFiles[0].uploaded;
};

$('.js__listingFormSaveButton').on('click', function (event) {
    var button = this;

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
});
