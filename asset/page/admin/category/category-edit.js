"use strict";

$.ajaxSetup({
    headers: {
        'X-CSRF-Token': app.getDataForJs()['adminCustomFieldsInCategorySaveSort'],
    }
});

$('.sortable').sortable({
    handle: '.sortableHandle'
});

$('.saveOrder').click(function () {
    var orderedIdList = $('.sortable').sortable('widget').toArray();

    $.ajax(Routing.generate('app_admin_category_custom_fields_save_order', []), {
            type: 'POST',
            dataType: 'json',
            data: JSON.stringify({
                orderedIdList: orderedIdList
            })
        }
    );
});
