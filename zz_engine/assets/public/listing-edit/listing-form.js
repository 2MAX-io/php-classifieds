"use strict";

import $ from "jquery";
import dataForJs from "~/function/dataForJs";
import Cleave from "cleave.js";
import "~/function/fancy-confirm";
import { preventDoubleClick } from "~/function/preventDoubleClick";
import Translator from "~/module/Translator";
import ParamEnum from "~/enum/ParamEnum";

new Cleave(".js__inputMoney", {
    numeral: true,
    numeralThousandsGroupStyle: "thousand",
    numeralPositiveOnly: true,
    delimiter: dataForJs[ParamEnum.THOUSAND_SEPARATOR],
    numeralDecimalMark: dataForJs[ParamEnum.NUMERAL_DECIMAL_MARK],
});

let countryIso = dataForJs[ParamEnum.COUNTRY_ISO].toString().toLowerCase();
import("../../../node_modules/cleave.js/dist/addons/cleave-phone." + countryIso)
    .then(() => {
        new Cleave(".js__inputPhone", {
            phone: true,
            phoneRegionCode: dataForJs[ParamEnum.COUNTRY_ISO],
        });
    })
    .catch((error) => {
        new Cleave(".js__inputPhone", {
            numeral: true,
            numeralPositiveOnly: true,
            numeralDecimalMark: "",
            delimiter: "",
        });

        throw error;
    });

let isUploadFinished = function () {
    let chosenFiles = $.fileuploader.getInstance("#js__listingFileUpload").getChoosedFiles();
    let noFilesUploaded = chosenFiles[0] === undefined;
    if (noFilesUploaded) {
        return true;
    }

    return chosenFiles[0].uploaded;
};

$(".js__listingFormSaveButton").on("click", function (event) {
    let button = this;

    if (!preventDoubleClick(button, event)) {
        return;
    }
    if ($(button).data("force-submit")) {
        return;
    }

    if (isUploadFinished()) {
        return;
    }

    event.preventDefault();

    window.setTimeout(function () {
        if (isUploadFinished()) {
            $(button).trigger("click");
        } else {
            $.fancyConfirm({
                message: Translator.trans(
                    "trans.Some files are still uploading, are you sure you want to submit form now?"
                ),
                okButton: Translator.trans("trans.Yes"),
                noButton: Translator.trans("trans.Cancel"),
                callback: function (confirmed) {
                    if (confirmed) {
                        $(button).data("force-submit", true);
                        $(button).trigger("click");
                    }
                },
            });
        }
    }, 1000);
});
