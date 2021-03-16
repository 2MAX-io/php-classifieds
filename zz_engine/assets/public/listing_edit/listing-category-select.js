"use strict";

import $ from "jquery";
import "./cascader.css";

let cascader = {};
cascader.$mainElement = $("#js__cascaderCategorySelect");
cascader.currentLevel = 0;
cascader.clickedLevel = 0;

cascader.select = function (clicked) {
    let $clicked = $(clicked);
    let selectionId = $clicked.data("category-id");
    let $branchList = cascader.$mainElement.find(".cascader-branch-list-" + selectionId);
    let hasChildren = $branchList.length > 0;

    cascader.clickedLevel = $clicked.parents(".cascader-level").data("cascader-level");

    let newLevel;
    if (hasChildren) {
        newLevel = $branchList.parents(".cascader-level").data("cascader-level");
        $branchList.parents(".cascader-level").find($(".cascader-branch-list")).hide();
        $branchList.show();
    } else {
        newLevel = $clicked.parents(".cascader-level").data("cascader-level");
        cascader.onLeafSelection(selectionId);
    }

    if (newLevel < cascader.currentLevel) {
        cascader.hideLowerLevels();
    }

    cascader.currentLevel = newLevel;
    cascader.removeSelection();
    $clicked.addClass("cascader-selected");
};

cascader.hideLowerLevels = function () {
    let level = cascader.currentLevel;
    while (cascader.$mainElement.find(".cascader-level.cascader-level-" + level).length) {
        cascader.$mainElement.find(".cascader-level.cascader-level-" + level + " .cascader-branch-list").hide();
        level += 1;
    }
};

cascader.removeSelection = function () {
    let level = cascader.clickedLevel;
    while (cascader.$mainElement.find(".cascader-level.cascader-level-" + level).length) {
        cascader.$mainElement
            .find(".cascader-level.cascader-level-" + level + " .cascader-selected")
            .removeClass("cascader-selected");
        level += 1;
    }
};

cascader.setValue = function (leafId) {
    if (!leafId && leafId.length < 1) {
        return;
    }

    cascader.$mainElement.find($(".cascader-selected")).removeClass("cascader-selected");
    cascader.$mainElement.find($(".cascader-branch-list")).hide();
    let $leaf = cascader.$mainElement.find(".cascader-leaf-" + leafId);
    let parenLeafId = 0;
    let $parentLeaf = null;
    do {
        let $branchList = $leaf.parents(".cascader-branch-list");

        $leaf.addClass("cascader-selected");
        $branchList.show();

        parenLeafId = $branchList.data("cascader-leaf");
        $parentLeaf = cascader.$mainElement.find(".cascader-leaf-" + parenLeafId);
        $leaf = $parentLeaf;
    } while ($parentLeaf.length);
};

cascader.selectValueFromForm = function () {
    cascader.setValue($(".js__formCategory").val());
};

cascader.onLeafSelection = function (leafId) {
    let $formCategory = $(".js__formCategory");
    $formCategory.val(leafId);
    $formCategory.trigger("change", leafId);
};

cascader.onFormChangeUpdateCascader = function () {
    $(".js__formCategory").on("change", function () {
        cascader.setValue(this.value);
    });
};

if ($(window).width() >= 768 && cascader.$mainElement.length) {
    cascader.$mainElement.show();
    cascader.$mainElement.find($(".cascader-branch-list")).first().show();
    cascader.selectValueFromForm();
    cascader.onFormChangeUpdateCascader();
}

$(".js__cascaderSelectCategory").on("click", function () {
    cascader.select(this);
});
