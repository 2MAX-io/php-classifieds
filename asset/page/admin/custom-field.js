"use strict";

$.ajaxSetup({
    headers: {
        'X-CSRF-Token': app.getJsonDataCached()['adminCustomFieldsSaveSortCsrfToken']
    }
});

$('.sortable').sortable({
    handle: '.sortableHandle'
});

$('.saveOrder').click(function () {
    var orderedIdList = $('.sortable').sortable('widget').toArray();

    $.ajax(Routing.generate('app_admin_custom_field_save_order', []), {
        type: 'POST',
        dataType: 'json',
        data: JSON.stringify({
            orderedIdList: orderedIdList
        })
    });
});