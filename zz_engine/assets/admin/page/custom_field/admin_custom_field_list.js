"use strict";

import $ from "jquery";
import Routing from "~/module/Routing";
import Sortable from "sortablejs";
import "~/function/jqueryAjaxaddCsrfHeader";

let sortable = new Sortable(document.querySelector(".js__sortable"), {
    handle: ".js__sortableHandle",
});

$(".js__saveOrder").on("click", function () {
    let orderedIdList = sortable.toArray();

    $.ajax(Routing.generate("app_admin_custom_field_save_order", []), {
        type: "POST",
        dataType: "json",
        data: JSON.stringify({
            orderedIdList: orderedIdList,
        }),
    });
});
