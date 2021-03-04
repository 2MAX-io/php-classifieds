"use strict";

document.getElementById("js__gdprAcceptButton")?.addEventListener("click", function () {
    document.getElementById("js__gdpr").remove();
    document.cookie = "gdpr=1; expires=" + new Date(new Date().getTime() + 315360000000).toUTCString() + "; path=/";
});
