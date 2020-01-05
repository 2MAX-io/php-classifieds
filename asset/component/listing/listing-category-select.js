"use strict";

var cascader = {};
cascader.$mainElement = $('#js__cascader-category-select');
cascader.currentLevel = 0;
cascader.clickedLevel = 0;

cascader.select = function (selectionId, clicked) {
    var $clicked = $(clicked);
    var $branchList = cascader.$mainElement.find('.cascader-branch-list-' + selectionId);
    var hasChildren = $branchList.length > 0;

    cascader.clickedLevel = $clicked.parents('.cascader-level').data('cascader-level');

    var newLevel = null;
    if (hasChildren) {
        newLevel = $branchList.parents('.cascader-level').data('cascader-level');
        $branchList.parents('.cascader-level').find('.cascader-branch-list').hide();
        $branchList.show();
    } else {
        newLevel = $clicked.parents('.cascader-level').data('cascader-level');
        cascader.onLeafSelection(selectionId);
    }

    if (newLevel < cascader.currentLevel) {
        cascader.hideLowerLevels();
    }

    cascader.currentLevel = newLevel;
    cascader.removeSelection();
    $clicked.addClass('cascader-selected');

};

cascader.hideLowerLevels = function() {
    var level = cascader.currentLevel;
    while (cascader.$mainElement.find('.cascader-level.cascader-level-'+level).length) {
        cascader.$mainElement.find('.cascader-level.cascader-level-'+level+' .cascader-branch-list').hide();
        level += 1;
    }
};

cascader.removeSelection = function() {
    var level = cascader.clickedLevel;
    while (cascader.$mainElement.find('.cascader-level.cascader-level-'+level).length) {
        cascader.$mainElement.find('.cascader-level.cascader-level-'+level+' .cascader-selected').removeClass('cascader-selected');
        level += 1;
    }
};

cascader.setValue = function (leafId) {
    if (leafId.length < 1) {
        return;
    }

    cascader.$mainElement.find('.cascader-selected').removeClass('cascader-selected');
    cascader.$mainElement.find('.cascader-branch-list').hide();
    var $leaf = cascader.$mainElement.find('.cascader-leaf-' + leafId);
    var parenLeafId = 0;
    var $parentLeaf = null;
    do {
        var $branchList = $leaf.parents('.cascader-branch-list');

        $leaf.addClass('cascader-selected');
        $branchList.show();

        parenLeafId = $branchList.data('cascader-leaf');
        $parentLeaf = cascader.$mainElement.find('.cascader-leaf-' + parenLeafId);
        $leaf = $parentLeaf;
    } while ($parentLeaf.length);
};

cascader.selectValueFromForm = function() {
    cascader.setValue($('.formCategory').val());
};

cascader.onLeafSelection = function(leafId) {
    var $formCategory = $('.formCategory');
    $formCategory.val(leafId);
    $formCategory.trigger('change', leafId)
};

cascader.onFormChangeUpdateCascader = function() {
    $('.formCategory').on('change', function() {
        cascader.setValue(this.value);
    });
};

if ($(window).width() >= 768 && cascader.$mainElement.length) {
    cascader.$mainElement.show();
    cascader.$mainElement.find('.cascader-branch-list').first().show();
    cascader.selectValueFromForm();
    cascader.onFormChangeUpdateCascader();
}
