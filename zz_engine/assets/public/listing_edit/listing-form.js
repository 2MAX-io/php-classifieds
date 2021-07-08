"use strict";

import $ from "jquery";
import dataForJs from "~/function/dataForJs";
import Cleave from "cleave.js";
import "~/function/fancyConfirm";
import { preventDoubleClick } from "~/function/preventDoubleClick";
import Translator from "~/module/Translator";
import ParamEnum from "~/enum/ParamEnum";
import Routing from "~/module/Routing";
import defaultFileUpload from "~/module/fileuploader/fileuploader";
import "~/function/jqueryAjaxAddCsrfHeader";

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

$("#js__listingFileUpload").fileuploader(
    $.extend(defaultFileUpload, {
        limit: 10,
        files: dataForJs[ParamEnum.LISTING_FILES],
        onRemove: function (item) {
            if ("listingFileId" in item.data) {
                $.post(Routing.generate("app_user_listing_file_remove"), {
                    listingFileId: item.data.listingFileId,
                });
            }

            return true;
        },
    })
);

let fileUploadInstance = $.fileuploader.getInstance("#js__listingFileUpload");

let isUploadFinished = function () {
    let chosenFiles = fileUploadInstance.getChoosedFiles();
    for (let chosenFile of chosenFiles) {
        if (!chosenFile.uploaded) {
            return false;
        }
    }

    return true;
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

    let confirmWindowDisplayed = false;
    let checkUploadFinished = () => {
        if (isUploadFinished()) {
            $(button).trigger("click");
        } else {
            if (!confirmWindowDisplayed) {
                confirmWindowDisplayed = true;
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

            window.setTimeout(function () {
                checkUploadFinished();
            }, 360);
        }
    };

    checkUploadFinished();
});
