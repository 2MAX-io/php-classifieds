"use strict";

document.getElementById("js__gdprAcceptButton")?.addEventListener("click", function () {
    let el = document.getElementById("js__gdpr");
    el.parentElement.removeChild(el);
    document.cookie = "gdpr=1; expires=" + new Date(new Date().getTime() + 315360000000).toUTCString() + "; path=/";
});
