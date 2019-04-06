"use strict";

import $ from "jquery";
import Translator from "~/module/Translator";

$(".js__confirm").on("click", function () {
    let $button = $(this);
    let translate = function (text) {
        if (text.startsWith("trans.")) {
            return Translator.trans(text);
        }

        return text;
    };

    return confirm(translate($button.data("confirm-message")));
});
