"use strict";

$.fn.textareaAutosize = function() {
    var autosize = function ($textarea) {
        var maxHeight = $textarea.css('max-height');
        var scrollHeight = $textarea[0].scrollHeight;
        if (scrollHeight >= maxHeight) {
            return;
        }
        var heightOffset = -(parseFloat($textarea.css('padding-top')) + parseFloat($textarea.css('padding-bottom')));
        $textarea.height(scrollHeight + heightOffset);
    };

    return this.each(function() {
        var $textarea = $(this);
        $textarea.height($textarea.height());

        autosize($textarea);

        $textarea.on('input', function () {
            autosize($textarea);
        });
    });
};

$('.textarea-autosize').textareaAutosize();
