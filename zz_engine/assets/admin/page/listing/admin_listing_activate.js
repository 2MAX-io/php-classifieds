"use strict";

import $ from "jquery";
import Translator from "~/module/Translator";
import "~/module/confirm";

$(".js__selectAll").on("click", function () {
    let $listingSelectionCheckbox = $(".js__selectListingCheckbox");
    $listingSelectionCheckbox.prop("checked", $listingSelectionCheckbox.filter(":checked").length < 1);
});

$(".js__enableSelection").on("click", function () {
    let $button = $(this);

    $(".js__listingSelectionHidden").show();
    $(".js__selectListingCheckbox").prop("checked", false);
    $button.remove();
});

function getCheckboxesValues(selector) {
    let values = [];
    $(selector + ":checked").each(function (i, el) {
        values.push($(el).val());
    });

    return values;
}

let $listingSelectionForm = $(".js__listingSelectionForm");
$listingSelectionForm.one("submit", function listingSelectionSubmitCallback(event) {
    event.preventDefault();

    let $checkedCheckboxes = $(".js__selectListingCheckbox").filter(":checked");
    if ($checkedCheckboxes.length < 1) {
        alert(Translator.trans("trans.No listings selected"));

        $listingSelectionForm.one("submit", listingSelectionSubmitCallback);
        return;
    }

    $(".js__selectedListingsInput").val(getCheckboxesValues(".js__selectListingCheckbox").join());

    $listingSelectionForm.trigger("submit");
});

$(".js__activateSelectedActionButton").on("click", function () {
    let $button = $(this);
    let translate = function (text) {
        if (text.startsWith("trans.")) {
            return Translator.trans(text);
        }

        return text;
    };

    if (!confirm(translate($button.data("confirm-message")))) {
        return false;
    }

    $(".js__actionToExecuteOnSelectedListings").val($button.val());

    return true;
});
