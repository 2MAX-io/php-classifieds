"use strict";

import $ from "jquery";

$.fn.textareaAutosize = function () {
    let autosize = function ($textarea) {
        let maxHeight = $textarea.css("max-height");
        let scrollHeight = $textarea[0].scrollHeight;
        if (scrollHeight >= maxHeight) {
            return;
        }
        let additionalHeight = -(
            parseFloat($textarea.css("padding-top")) + parseFloat($textarea.css("padding-bottom"))
        );
        $textarea.height(scrollHeight + additionalHeight);
    };

    return this.each(function () {
        let $textarea = $(this);
        $textarea.height($textarea.height());

        autosize($textarea);

        $textarea.on("input", function () {
            autosize($textarea);
        });
    });
};

$(".js__textareaAutosize").textareaAutosize();
