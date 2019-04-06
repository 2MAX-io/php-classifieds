"use strict";

import $ from "jquery";
import "~/module/confirm";

let showValueSelectForAction = function () {
    let $actionSelect = $(".js__selectAction");
    let action = $actionSelect.val();

    let $valueSelect = $(".js__executeActionValueSelect");
    $valueSelect.hide();
    $valueSelect.filter('[data-value-for-action="' + action + '"]').show();
};

$(".js__selectAction").on("change", function () {
    showValueSelectForAction();
});

showValueSelectForAction();
