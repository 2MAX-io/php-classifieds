"use strict";

var customFields = {};
customFields.loadedCategory = null;
customFields.formArray = [];

customFields.loadFromBackend = function() {
    var listingId = app.getDataForJs()['listingId'];
    var categoryId = parseInt($('.formCategory').val());
    customFields.loadedCategory = categoryId;
    $.get(Routing.generate('app_listing_get_custom_fields', {listingId: listingId, categoryId: categoryId}), function (html) {
        var $formCustomFieldList = $('.formCustomFieldList');
        $formCustomFieldList.html(html);
        $formCustomFieldList.find('[name^="listing[customFieldList]"]').each(function(){
            var $input = $(this);
            var formSerializedElement = customFields.formArray[$input.attr('name')];
            if (formSerializedElement && formSerializedElement.value.trim() !== '')  {
                $input.val(formSerializedElement.value);
            }
        });
    });
};

customFields.getFormArray = function () {
    var result = [];
    $('.formCategory').parents('form').serializeArray().forEach(function (el) {
        result[el.name] = el;
    });

    return result;
};

$('.formCategory').on('change', function () {
    if (customFields.loadedCategory === parseInt($('.formCategory').val())){
        return;
    }

    customFields.formArray = $.extend({}, customFields.formArray, customFields.getFormArray());
    customFields.loadFromBackend();
});

if ($('.formCustomFieldList').find('.form-group').length < 1) {
    customFields.loadFromBackend();
}
