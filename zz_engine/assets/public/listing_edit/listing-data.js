"use strict";

import $ from "jquery";
import ParamEnum from "~/enum/ParamEnum";
import dataForJs from "~/function/dataForJs";
import Routing from "~/module/Routing";

let listingData = {};
listingData.category = null;
listingData.formValues = [];
listingData.$customFieldListWrapper = $(".js__customFieldList");
listingData.$packageListWrapper = $(".js__packageList");

listingData.loadFromBackend = function () {
    let listingId = dataForJs[ParamEnum.LISTING_ID];
    let categoryId = listingData.getCategoryFromSelect();
    if (!categoryId) {
        return;
    }
    listingData.category = categoryId;
    let request = {};
    request[ParamEnum.LISTING_ID] = listingId;
    request[ParamEnum.CATEGORY_ID] = categoryId;
    $.post(Routing.generate("app_listing_data", request), function (response) {
        listingData.$customFieldListWrapper.html(response[ParamEnum.CUSTOM_FIELD]);
        listingData.$packageListWrapper.html(response[ParamEnum.PACKAGE_LIST]);
        listingData.restoreUserEnteredValues();
    });
};

/**
 * prevent loss of user entered custom field values when changing categories
 */
listingData.restoreUserEnteredValues = function () {
    let $customFieldInputs = listingData.$customFieldListWrapper.find($('[name^="listing[customFieldList]"]'));
    $customFieldInputs.each(function () {
        let $customFieldInput = $(this);
        let previousValue = listingData.formValues[$customFieldInput.attr("name")];
        if (previousValue && previousValue.value.trim() !== "") {
            $customFieldInput.val(previousValue.value);
        }
    });
};

listingData.getFormValues = function () {
    let formValues = [];
    $(".js__formCategory")
        .parents("form")
        .serializeArray()
        .forEach(function (el) {
            formValues[el.name] = el;
        });

    return formValues;
};

$(".js__formCategory").on("change", function () {
    if (listingData.category === listingData.getCategoryFromSelect()) {
        return;
    }

    listingData.formValues = $.extend({}, listingData.formValues, listingData.getFormValues());
    listingData.loadFromBackend();
});

listingData.getCategoryFromSelect = function () {
    return parseInt($(".js__formCategory").val()) || null;
};

let customFieldsCount = listingData.$customFieldListWrapper.find($(".form-group")).length;
if (customFieldsCount < 1) {
    listingData.loadFromBackend();
}
