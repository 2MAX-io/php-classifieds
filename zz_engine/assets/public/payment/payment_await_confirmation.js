"use strict";

import dataForJs from "~/function/dataForJs";
import ParamEnum from "~/enum/ParamEnum";
import Routing from "~/module/Routing";
import Translator from "~/module/Translator";

class PaymentAwaitConfirmation {
    refreshCount = 0;

    init() {
        paymentAwaitConfirmation.refresh();
    }

    refresh() {
        paymentAwaitConfirmation.refreshCount++;
        let paymentAppToken = dataForJs[ParamEnum.PAYMENT_APP_TOKEN];
        $.ajax(Routing.generate("app_payment_status_refresh", []), {
            type: "POST",
            dataType: "json",
            data: {
                paymentAppToken: paymentAppToken,
            },
        })
            .done(function (data) {
                $("#js__lastRefreshTime").text(data["lastRefreshTime"]);

                if (data["paymentComplete"] === true) {
                    let $jsPaymentStatus = $("#js__paymentStatus");
                    $jsPaymentStatus.addClass("text-success");
                    $jsPaymentStatus.removeClass("text-danger");
                    $jsPaymentStatus.text(Translator.trans("trans.Success, redirecting..."));

                    window.location.href = Routing.generate("app_payment_await_confirmation_redirect", {
                        paymentAppToken: paymentAppToken,
                    });
                } else {
                    let refreshTimeout = 800;
                    if (paymentAwaitConfirmation.refreshCount > 300) {
                        refreshTimeout = 5000;
                    }
                    if (paymentAwaitConfirmation.refreshCount > 1800) {
                        refreshTimeout = 60 * 1000;
                    }
                    setTimeout(function () {
                        paymentAwaitConfirmation.refresh();
                    }, refreshTimeout);
                }
            })
            .fail(function () {
                setTimeout(function () {
                    paymentAwaitConfirmation.refresh();
                }, 5000);
            });
    }
}

let paymentAwaitConfirmation = new PaymentAwaitConfirmation();
paymentAwaitConfirmation.init();
