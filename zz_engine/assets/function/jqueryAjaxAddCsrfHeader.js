"use strict";

import $ from "jquery";
import dataForJs from "~/function/dataForJs";
import ParamEnum from "~/enum/ParamEnum";

$.ajaxSetup({
    headers: {
        "X-CSRF-Token": dataForJs[ParamEnum.CSRF_TOKEN],
    },
});
