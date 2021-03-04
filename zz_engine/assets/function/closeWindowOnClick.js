"use strict";

document.querySelectorAll(".js__closeWindowOnClick").forEach((button) => {
    button.addEventListener("click", function () {
        window.close();

        return false;
    });
});
