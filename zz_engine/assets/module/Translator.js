"use strict";

import ParamEnum from "~/enum/ParamEnum";
import TranslatorBundle from "../../../zz_engine/vendor/willdurand/js-translation-bundle/Resources/js/translator.js";
import dataForJs from "~/function/dataForJs";

class Translator {
    static translationsLoaded = false;

    static trans(translationKey, translationParams) {
        /** @type {string} */
        let lang = dataForJs[ParamEnum.LANGUAGE_ISO].toString().toLowerCase();
        if (!Translator.translationsLoaded) {
            // noinspection JSUnresolvedFunction
            TranslatorBundle.fromJSON(
                require("../../../asset/backendGenerated/bazingajstranslation/translations/" + lang + ".json")
            );
        }

        // noinspection JSUnresolvedFunction
        return TranslatorBundle.trans(translationKey, translationParams, null, lang);
    }
}

export default Translator;
