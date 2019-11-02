"use strict";

var showActionFields = function () {
    var $select = $('.selectActonInput');
    var action = $select.val();

    var $actionField = $('.actionField');
    $actionField.hide();
    $actionField.filter('[data-for-action="'+action+'"]').show();
};

$('.selectActonInput').on('change', function () {
    showActionFields();
});

showActionFields();