"use strict";

import $ from "jquery";

let preventDoubleClick = function (element, event) {
    let $element = $(element);
    let previousClickTimestamp = $element.data("last-click-timestamp") || null;
    $element.data("last-click-timestamp", event.timeStamp);

    if (previousClickTimestamp !== null && event.timeStamp - 1000 < previousClickTimestamp) {
        event.preventDefault();
        return false;
    }

    return true;
};

export { preventDoubleClick };
