app.paymentWait = {};

app.paymentWait.init = function () {
    app.paymentWait.refresh();
};

app.paymentWait.refresh = function () {
    $.ajax(Routing.generate('app_payment_wait_refresh_ajax', []), {
        type: 'POST',
        dataType: 'json',
        data: {
            paymentAppToken: app.getJsonDataCached()['paymentAppToken']
        }
    }).done(function (data) {
        app.debugLog(data);

        $('#js__lastRefreshTime').text(data['lastRefreshTime']);

        if (data['paymentComplete'] === true) {
            let $jsPaymentStatus = $('#js__paymentStatus');
            $jsPaymentStatus.addClass('text-success');
            $jsPaymentStatus.removeClass('text-danger');
            $jsPaymentStatus.text(Translator.trans('trans.Success, redirecting...'));
        } else {
            setTimeout(function () {
                app.paymentWait.refresh();
            }, 2000);
        }

    }).fail(function () {
        setTimeout(function () {
            app.paymentWait.refresh();
        }, 5000);
    });
};

app.paymentWait.init();