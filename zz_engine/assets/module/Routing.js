"use strict";

import FosJsRouting from "../../../zz_engine/vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js";
import dataForJs from "~/function/dataForJs";
import ParamEnum from "~/enum/ParamEnum";

// noinspection JSUnresolvedFunction
FosJsRouting.setRoutingData(require("../../../asset/backendGenerated/fosjsrouting/routes.json"));

class Routing {
    static generate(routeName, routeParams) {
        // noinspection JSUnresolvedFunction
        return dataForJs[ParamEnum.BASE_URL] + FosJsRouting.generate(routeName, routeParams).replace(/^\/+/, "");
    }
}

export default Routing;
