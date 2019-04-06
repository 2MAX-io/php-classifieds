"use strict";

import $ from "jquery";
import Cleave from "cleave.js";
import "~/function/textareaAutosize";
import "~/module/confirm";
import "~/module/confirm";

$(".js__inputDatetime")
    .toArray()
    .forEach(function (field) {
        new Cleave(field, {
            delimiters: ["-", "-", " ", ":", ":"],
            blocks: [4, 2, 2, 2, 2, 2],
        });
    });
