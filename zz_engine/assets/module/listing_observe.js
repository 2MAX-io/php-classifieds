"use strict";

import $ from "jquery";
import ParamEnum from "~/enum/ParamEnum";
import dataForJs from "~/function/dataForJs";
import Routing from "~/module/Routing";

$(".js__observe").on("click", function () {
    let $button = $(this);
    let listingId = $button.attr("data-listing-id");
    let currentObservedValue = $button.attr("data-observed") | 0;
    let newObservedValue = !currentObservedValue;

    var formData = new FormData();
    formData.append(ParamEnum.LISTING_ID, listingId);
    formData.append(ParamEnum.OBSERVED, newObservedValue ? '1' : '0');
    fetch(Routing.generate("app_user_observed_listing_set"), {
        method: "post",
        body: formData,
        headers: {
            "X-CSRF-Token": dataForJs[ParamEnum.CSRF_TOKEN],
        },
    })
        .then((response) => response.json())
        .then((jsonData) => {
            if (true !== jsonData[ParamEnum.SUCCESS]) {
                return;
            }

            $button.attr("data-observed", jsonData[ParamEnum.OBSERVED] | 0);
            let $icon = $button;
            if (!$icon.is(".svg")) {
                $icon = $icon.find($(".svg"));
            }

            if (jsonData[ParamEnum.OBSERVED]) {
                $icon.removeClass("svg-heart-outline").addClass("svg-heart");
            } else {
                $icon.removeClass("svg-heart").addClass("svg-heart-outline");
            }
        });
});
