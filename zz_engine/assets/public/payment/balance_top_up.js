"use strict";

import Cleave from "cleave.js";
import dataForJs from "~/function/dataForJs";
import ParamEnum from "~/enum/ParamEnum";

new Cleave(".js__inputMoney", {
    numeral: true,
    numeralThousandsGroupStyle: "thousand",
    numeralPositiveOnly: true,
    delimiter: dataForJs[ParamEnum.THOUSAND_SEPARATOR],
    numeralDecimalMark: dataForJs[ParamEnum.NUMERAL_DECIMAL_MARK],
});
