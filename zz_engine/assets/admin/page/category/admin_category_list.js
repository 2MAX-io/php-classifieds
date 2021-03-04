"use strict";

import $ from "jquery";
import Routing from "~/module/Routing";
import Sortable from "sortablejs";
import "~/function/jqueryAjaxaddCsrfHeader";

let sortableInstances = [];
$(".js__nestedSortable").each(function (i, el) {
    let sortableInstance = new Sortable(el, {
        group: $(el).data("sortable-group"),
        handle: ".js__sortableHandle",
    });
    sortableInstances.push(sortableInstance);
});

$(".js__saveOrder").on("click", function () {
    let orderedIdList = [];
    for (let sortableInstance of sortableInstances) {
        orderedIdList = orderedIdList.concat(sortableInstance.toArray());
    }

    $.ajax(Routing.generate("app_admin_category_save_order", []), {
        type: "POST",
        dataType: "json",
        data: JSON.stringify({
            orderedIdList: orderedIdList,
        }),
    });
});
