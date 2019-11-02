"use strict";

function toggleCheckbox(selector) {
    $(selector).click();
}

function selectAll(selector) {
    $(selector).prop('checked', $('.listingSelectionCheckbox').filter(':checked').length < 1);
}

function getCheckboxesValues(selector) {
    let values = [];
    $(selector + ':checked').each(function (i, el) {
        values.push(el.value);
    });

    return values;
}

function enableSelection(button) {
    $('.listingSelectionHidden').show();
    $('.listingSelectionCheckbox').prop('checked', false);
    $(button).remove();
}

let $listingSelectionForm = $('.listingSelectionForm');
$listingSelectionForm.one('submit', function listingSelectionSubmitCallback(event) {
    event.preventDefault();

    let $checked = $('.listingSelectionCheckbox').filter(':checked');
    if ($checked.length < 1) {
        alert(Translator.trans('trans.No listings selected'));

        $listingSelectionForm.one('submit', listingSelectionSubmitCallback);
        return;
    }

    $('.listingSelectionSelectedHiddenInput').val(getCheckboxesValues('.listingSelectionCheckbox').join());

    $listingSelectionForm.submit();
});