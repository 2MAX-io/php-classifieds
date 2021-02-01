"use strict";

$.ajaxSetup({
    headers: {
        'X-CSRF-Token': app.getDataForJs()['adminCustomFieldOptionsOrderCsrfToken']
    }
});

$('.sortable').sortable({
    handle: '.sortableHandle'
});

$('.saveOrder').click(function () {
    var orderedIdList = $('.sortable').sortable('widget').toArray();

    $.ajax(Routing.generate('app_admin_custom_field_options_save_order', []), {
            type: 'POST',
            dataType: 'json',
            data: JSON.stringify({
                orderedIdList: orderedIdList
            })
        }
    );
});

$('.sortAlphabetically').click(function () {
    var sortElementList = [];
    $('.sortAlphabeticallyElement').each(function (k, el) {
        sortElementList.push({
            id: $(el).data('id'),
            name: $(el).data('name'),
        });
    });
    sortElementList.sort((a, b) => (a.name > b.name) ? 1 : -1);

    var sort = sortElementList.map(function (el) {
        return el.id;
    });
    $('.sortable').sortable('widget').sort(sort);
});
