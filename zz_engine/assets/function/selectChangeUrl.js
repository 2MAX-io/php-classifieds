"use strict";

document.querySelectorAll(".js__selectChangeUrl").forEach((select) => {
    select.addEventListener("change", function () {
        window.location = this.options[select.selectedIndex].value;
    });
});
