"use strict";

$('.js__message-textarea').on('keypress', function (event) {
    if(event["which"] === 13 && !event.shiftKey){
        $(event.target.form).submit();
        event.preventDefault();
    }
});


$(document).ready(function () {
    $('.js__message-list').scrollTop($('.js__message-list-all').height());
});
