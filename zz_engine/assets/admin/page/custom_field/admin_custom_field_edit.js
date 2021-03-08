"use strict";

import $ from "jquery";
import Routing from "~/module/Routing";
import Sortable from "sortablejs";
import "~/function/jqueryAjaxaddCsrfHeader";
import "~/module/confirm";

let sortableElement = $(".js__sortable");
if (sortableElement) {
    let sortable = new Sortable(sortableElement, {
        handle: ".js__sortableHandle",
    });

    $(".js__saveOrder").on("click", function () {
        let orderedIdList = sortable.toArray();
        $.ajax(Routing.generate("app_admin_custom_field_options_save_order", []), {
            type: "POST",
            dataType: "json",
            data: JSON.stringify({
                orderedIdList: orderedIdList,
            }),
        });
    });

    $(".js__sortAlphabetically").on("click", function () {
        let listToSort = [];
        $(".js__sortAlphabeticallyElement").each(function (k, el) {
            listToSort.push({
                id: $(el).data("id"),
                name: $(el).data("name"),
            });
        });
        listToSort.sort((a, b) => (a.name > b.name ? 1 : -1));

        let newSort = listToSort.map(function (el) {
            return el.id;
        });
        sortable.sort(newSort);
    });
}
