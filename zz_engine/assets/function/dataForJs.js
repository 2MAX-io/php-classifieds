"use strict";

let dataForJs = null;

let getDataForJsNoCache = function () {
    let dataForJsElement = document.getElementById("js__dataForJs");
    if (null === dataForJsElement) {
        return {};
    }

    return JSON.parse(atob(dataForJsElement.value));
};

let getDataForJs = function () {
    if (dataForJs === null) {
        dataForJs = getDataForJsNoCache();
    }

    return dataForJs;
};

dataForJs = getDataForJs();

export { dataForJs as default, getDataForJs, getDataForJsNoCache };
