"use strict";

import $ from "jquery";
import Translator from "./../module/Translator";

$.fancyConfirm = function (opts) {
    opts = $.extend(
        true,
        {
            title: Translator.trans("trans.Are you sure?"),
            message: "",
            okButton: Translator.trans("trans.OK"),
            noButton: Translator.trans("trans.Cancel"),
            callback: $.noop,
        },
        opts || {}
    );

    $.fancybox.open({
        type: "html",
        src:
            '<div class="fc-content p-5 rounded">' +
            '<h2 class="mb-3">' +
            opts.title +
            "</h2>" +
            "<p>" +
            opts.message +
            "</p>" +
            '<p class="text-right">' +
            '<a data-value="0" data-fancybox-close href="javascript:;" class="mr-2">' +
            opts.noButton +
            "</a>" +
            '<button data-value="1" data-fancybox-close class="btn btn-primary">' +
            opts.okButton +
            "</button>" +
            "</p>" +
            "</div>",
        opts: {
            animationEffect: false,
            modal: true,
            baseTpl:
                '<div class="fancybox-container fc-container" role="dialog" tabindex="-1">' +
                '<div class="fancybox-bg"></div>' +
                '<div class="fancybox-inner">' +
                '<div class="fancybox-stage"></div>' +
                "</div>" +
                "</div>",
            afterClose: function (instance, current, e) {
                let button = e ? e.target || e.currentTarget : null;
                let value = button ? $(button).data("value") : 0;

                opts.callback(value);
            },
        },
    });
};
