"use strict";

document.querySelectorAll(".js__goBackOnClick").forEach((button) => {
    button.addEventListener("click", function () {
        history.back();

        return false;
    });
});
