"use strict";

var $messageTextarea = $('.js__message-textarea');
$messageTextarea.on('keypress', function (event) {
    if(event["which"] === 13 && !event.shiftKey && $messageTextarea.val().trim() !== ''){
        $(event.target.form).submit();
        event.preventDefault();
    }
});

$(document).ready(function () {
    $('.js__message-list').scrollTop($('.js__message-list-messages-box').height());

    if ($messageTextarea.length) {
        $('.js__message-textarea').focus();
    }
});
