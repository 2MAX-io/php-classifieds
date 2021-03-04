"use strict";

import $ from "jquery";
import Routing from "~/module/Routing";
import Sortable from "sortablejs";
import "~/function/jqueryAjaxaddCsrfHeader";
import "~/module/confirm";

let sortable = new Sortable(document.querySelector(".js__sortable"), {
    handle: ".js__sortableHandle",
});

$(".js__saveOrder").on("click", function () {
    let orderedIdList = sortable.toArray();

    $.ajax(Routing.generate("app_admin_category_custom_fields_save_order", []), {
        type: "POST",
        dataType: "json",
        data: JSON.stringify({
            orderedIdList: orderedIdList,
        }),
    });
});
