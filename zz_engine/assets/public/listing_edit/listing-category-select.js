"use strict";

import $ from "jquery";
import "./cascader.css";

class Cascader {
    $mainElement = $("#js__cascaderCategorySelect");
    currentLevel = 0;
    clickedLevel = 0;

    select(clicked) {
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
    }

    hideLowerLevels() {
        let level = cascader.currentLevel;
        while (cascader.$mainElement.find(".cascader-level.cascader-level-" + level).length) {
            cascader.$mainElement.find(".cascader-level.cascader-level-" + level + " .cascader-branch-list").hide();
            level += 1;
        }
    }

    removeSelection() {
        let level = cascader.clickedLevel;
        while (cascader.$mainElement.find(".cascader-level.cascader-level-" + level).length) {
            cascader.$mainElement
                .find(".cascader-level.cascader-level-" + level + " .cascader-selected")
                .removeClass("cascader-selected");
            level += 1;
        }
    }

    setValue(leafId) {
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
    }

    selectValueFromForm() {
        cascader.setValue($(".js__formCategory").val());
    }

    onLeafSelection(leafId) {
        let $formCategory = $(".js__formCategory");
        $formCategory.val(leafId);
        $formCategory.trigger("change", leafId);
    }

    onFormChangeUpdateCascader() {
        $(".js__formCategory").on("change", function () {
            cascader.setValue(this.value);
        });
    }
}

let cascader = new Cascader();

if ($(window).width() >= 768 && cascader.$mainElement.length) {
    cascader.$mainElement.show();
    cascader.$mainElement.find($(".cascader-branch-list")).first().show();
    cascader.selectValueFromForm();
    cascader.onFormChangeUpdateCascader();
}

$(".js__cascaderSelectCategory").on("click", function () {
    cascader.select(this);
});
