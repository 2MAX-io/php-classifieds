"use strict";

import dataForJs from "~/function/dataForJs";

let onClick = function (event) {
    event.preventDefault();
    let ParamEnum = {};
    ParamEnum.LISTING_ID = "listingId";
    ParamEnum.SHOW_LISTING_PREVIEW_FOR_OWNER = "showListingPreviewForOwner";
    ParamEnum.SHOW_CONTACT_HTML = "showContactHtml";

    let $button = this;
    var formData = new FormData();
    formData.append(ParamEnum.LISTING_ID, $button.getAttribute("data-listing-id"));
    formData.append(ParamEnum.SHOW_LISTING_PREVIEW_FOR_OWNER, dataForJs[ParamEnum.SHOW_LISTING_PREVIEW_FOR_OWNER]);
    fetch($button.getAttribute("data-route"), {
        method: "post",
        body: formData,
        headers: {
            "X-CSRF-Token": dataForJs[ParamEnum.CSRF_TOKEN],
        },
    })
        .then((response) => response.json())
        .then((jsonData) => {
            if (true !== jsonData.success) {
                return;
            }

            document.querySelector(".js__listingContactData")?.remove();

            $button.insertAdjacentHTML("beforebegin", jsonData[ParamEnum.SHOW_CONTACT_HTML]);
            $button.style.display = "none";
        })
        .catch((err) => {
            document.querySelectorAll(".js__listingContactData").forEach((element) => element.remove());
            $button.style.display = "block";
            throw err;
        });
};

document.querySelector(".js__listingShowContactDetails")?.addEventListener("click", onClick);
document.querySelector(".js__listingShowContactDetails")?.addEventListener("touchstart", onClick);
