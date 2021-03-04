"use strict";

import $ from "jquery";

let $messageTextarea = $(".js__messageTextarea");
$messageTextarea.on("keypress", function (event) {
    let enterKey = event["which"] === 13;
    if (enterKey && !event.shiftKey && $messageTextarea.val().trim() !== "") {
        $(event.target.form).trigger("submit");
        event.preventDefault();
    }
});

$(function () {
    $(".js__messageList").scrollTop($(".js__messageListMessagesBox").height());

    if ($messageTextarea.length) {
        $(".js__messageTextarea").trigger("focus");
    }
});
