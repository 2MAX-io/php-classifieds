"use strict";

import $ from "jquery";
import ParamEnum from "~/enum/ParamEnum";
import dataForJs from "~/function/dataForJs";
import Routing from "~/module/Routing";

let customFields = {};
customFields.category = null;
customFields.formValues = [];
customFields.$customFieldListWrapper = $(".js__customFieldList");

customFields.loadFromBackend = function () {
    let listingId = dataForJs[ParamEnum.LISTING_ID];
    let categoryId = customFields.getCategoryFromSelect();
    if (!categoryId) {
        return;
    }
    customFields.category = categoryId;
    let request = {};
    request[ParamEnum.LISTING_ID] = listingId;
    request[ParamEnum.CATEGORY_ID] = categoryId;
    $.post(Routing.generate("app_listing_get_custom_fields", request), function (html) {
        customFields.$customFieldListWrapper.html(html);
        customFields.restoreUserEnteredValues();
    });
};

/**
 * prevent loss of user entered custom field values when changing categories
 */
customFields.restoreUserEnteredValues = function () {
    let $customFieldInputs = customFields.$customFieldListWrapper.find($('[name^="listing[customFieldList]"]'));
    $customFieldInputs.each(function () {
        let $customFieldInput = $(this);
        let previousValue = customFields.formValues[$customFieldInput.attr("name")];
        if (previousValue && previousValue.value.trim() !== "") {
            $customFieldInput.val(previousValue.value);
        }
    });
};

customFields.getFormValues = function () {
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
    if (customFields.category === customFields.getCategoryFromSelect()) {
        return;
    }

    customFields.formValues = $.extend({}, customFields.formValues, customFields.getFormValues());
    customFields.loadFromBackend();
});

customFields.getCategoryFromSelect = function () {
    return parseInt($(".js__formCategory").val()) || null;
};

let customFieldsCount = customFields.$customFieldListWrapper.find($(".form-group")).length;
if (customFieldsCount < 1) {
    customFields.loadFromBackend();
}
