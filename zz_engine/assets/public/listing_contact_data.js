"use strict";

import dataForJs from "~/function/dataForJs";

let loadContactData = ($contactDataButton) => {
    let ParamEnum = {};
    ParamEnum.LISTING_ID = "listingId";
    ParamEnum.SHOW_LISTING_PREVIEW_FOR_OWNER = "showListingPreviewForOwner";
    ParamEnum.SHOW_CONTACT_HTML = "showContactHtml";
    ParamEnum.CSRF_TOKEN = "csrfToken";

    var formData = new FormData();
    formData.append(ParamEnum.LISTING_ID, $contactDataButton.getAttribute("data-listing-id"));
    formData.append(ParamEnum.SHOW_LISTING_PREVIEW_FOR_OWNER, dataForJs[ParamEnum.SHOW_LISTING_PREVIEW_FOR_OWNER]);

    fetch($contactDataButton.getAttribute("data-route"), {
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

            $contactDataButton.insertAdjacentHTML("beforebegin", jsonData[ParamEnum.SHOW_CONTACT_HTML]);
            $contactDataButton.style.display = "none";
        })
        .catch((err) => {
            document.querySelectorAll(".js__listingContactData").forEach((element) => element.remove());
            $contactDataButton.style.display = "block";
            throw err;
        });
};

let onClick = function (event) {
    event.preventDefault();
    let $contactDataButton = this;
    if (typeof window.fetch !== "function") {
        import("whatwg-fetch").then((module) => {
            window.fetch = module.fetch;
            loadContactData($contactDataButton);
        });
        return;
    }
    loadContactData($contactDataButton);
};

document.querySelector(".js__listingShowContactDetails")?.addEventListener("click", onClick);
document.querySelector(".js__listingShowContactDetails")?.addEventListener("touchstart", onClick);
document.addEventListener("click", (e) => {
    if (e.target.closest(".js__reloadPage")) {
        location.reload();
    }
});
