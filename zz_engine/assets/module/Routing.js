"use strict";

import FosJsRouting from "../../../zz_engine/vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js";

// noinspection JSUnresolvedFunction
FosJsRouting.setRoutingData(require("../../../asset/backendGenerated/fosjsrouting/routes.json"));

class Routing {
    static generate(routeName, routeParams) {
        // noinspection JSUnresolvedFunction
        return FosJsRouting.generate(routeName, routeParams);
    }
}

export default Routing;
