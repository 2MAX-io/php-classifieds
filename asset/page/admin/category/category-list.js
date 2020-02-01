"use strict";

$.ajaxSetup({
    headers: {
        'X-CSRF-Token': app.getJsonDataCached()['adminCategorySaveSort'],
    }
});

$('.nested-sortable').each(function (i, el) {
    $(el).sortable({
        group: $(el).data('sortable-group'),
        handle: '.sortableHandle'
    });
});

$('.saveOrder').click(function () {
    var orderedIdList = [];
    $('.nested-sortable').each(function (i, el) {
        orderedIdList = orderedIdList.concat($(el).sortable('widget').toArray());
    });

    $.ajax(Routing.generate('app_admin_category_save_order', []), {
            type: 'POST',
            dataType: 'json',
            data: JSON.stringify({
                orderedIdList: orderedIdList
            })
        }
    );
});