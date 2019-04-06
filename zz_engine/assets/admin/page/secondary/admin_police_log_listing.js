"use strict";

import $ from "jquery";
import dataForJs from "~/function/dataForJs";
import { copyTextToClipboard } from "~/function/copyTextToClipboard";
import ParamEnum from "~/enum/ParamEnum";

$(".js__policeLogCopyToClipboard").on("click", function () {
    copyTextToClipboard(dataForJs[ParamEnum.POLICE_LOG_TEXT]);
});
